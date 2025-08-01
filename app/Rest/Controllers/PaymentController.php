<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Rest\Resources\PaymentResource;
use App\Jobs\ProcessPayment;
use Illuminate\Support\Facades\Log;
use Flutterwave\Payments\Facades\Flutterwave;
use Flutterwave\Payments\Data\Currency;


use Bmatovu\MtnMomo\Products\Collection;
use Bmatovu\MtnMomo\Exceptions\CollectionRequestException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
class PaymentController extends RestController
{
    public function index()
    {
        $payments = Payment::with('client')->get();

        return PaymentResource::collection($payments);
    }


    public function makePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|max:3',
            'email' => 'required|email',
        ]);

        $payload = [
            'tx_ref' => Flutterwave::generateTransactionReference(),
            'amount' => $request->amount,
            'currency' => $request->currency,
            'customer' => [
                'email' => $request->email,
            ],
        ];

        // Render the standard modal and get the redirect link
        $paymentLink = Flutterwave::render('standard', $payload);

        return response()->json([
            'status' => 'success',
            'payment_link' => $paymentLink,
        ]);
    }

    public function handleWebhook(Request $request)
{
    $method = $request->method();
    if ($method === 'POST') {
        $body = $request->getContent();
        $webhook = Flutterwave::use('webhooks');
        $transaction = Flutterwave::use('transactions');
        $signature = $request->header($webhook::SECURE_HEADER);

        // verify signature
        $isVerified = $webhook->verifySignature($body, $signature);

        if ($isVerified) {
            ['tx_ref' => $tx_ref, 'id' => $id] = $webhook->getHook();
            ['status' => $status, 'data' => $transactionData] = $transaction->verifyTransactionReference($tx_ref);

            if ($status === 'success') {
                switch ($transactionData['status']) {
                    case \Flutterwave\Payments\Data\Status::SUCCESSFUL:
                        // Save or update payment status in DB
                        Payment::updateOrCreate(
                            ['transaction_id' => $transactionData['id']],
                            [
                                'client_id' => null, // assign if you track clients
                                'amount_paid' => $transactionData['amount'],
                                'account' => $transactionData['customer']['email'],
                                'type' => $transactionData['payment_type'] ?? 'unknown',
                                'status' => 'successful',
                                'created_by' => null,
                            ]
                        );
                        break;

                    case \Flutterwave\Payments\Data\Status::PENDING:
                        // handle pending status
                        break;

                    case \Flutterwave\Payments\Data\Status::FAILED:
                        // handle failed status
                        break;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook verified',
                'data' => [
                    'tx_ref' => $tx_ref,
                    'id' => $id,
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Access denied. Hash invalid'
        ], 401);
    }

    return abort(404);
}


 

public function store(Request $request)
{
    $validated = $request->validate([
        'client_id' => 'required|exists:clients,id',
        'transaction_id' => 'required|string|max:255|unique:payments,transaction_id',
        'amount_paid' => 'required|numeric',
        'account' => 'required|string|max:255',
        'type' => 'required|string|max:50',
        'status' => 'sometimes|string|max:50',
    ]);

    $validated['created_by'] = Auth::id();

    //ProcessPayment::dispatch($validated);

    return response()->json([
        'message' => 'Payment is being processed.',
    ], 202);
}



public function requestMtnMomoPayment(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'phone' => 'required|string',
        'currency' => 'required|string',
        'payerMessage' => 'nullable|string|max:160',
        'payeeNote' => 'nullable|string|max:160',
    ]);

    try {
        $collection = new Collection();

        $referenceId = $collection->requestToPay(
            (string) Str::uuid(),
            $request->input('phone'),
            $request->input('amount'),
            $request->input('currency', 'EUR'),
            $request->input('payerMessage', 'Payment for services'),
        );

        return response()->json([
            'status' => 'success',
            'referenceId' => $referenceId,
        ], 200);

    } catch (CollectionRequestException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to initiate payment request.',
            'errors' => [
                $e->getMessage(),
                optional($e->getPrevious())->getMessage(),
            ],
        ], 400);
    }
}




    public function show($id)
    {
        $payment = Payment::findOrFail($id);

        return new PaymentResource($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|required|exists:clients,id',
            'transaction_id' => 'sometimes|required|string|max:255|unique:payments,transaction_id,' . $payment->id,
            'amount_paid' => 'sometimes|required|numeric',
            'account' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:50',
            'status' => 'sometimes|string|max:50',
        ]);

        $validated['updated_by'] = Auth::id();

        $payment->update($validated);

        return new PaymentResource($payment);
    }




public function checkStatus($paymentId)
{
    $payment = Payment::find($paymentId);

     Log::error('Validation failed:', [
            'data'  => $payment,
        ]);


    if (!$payment) {
        return response()->json([
            'success' => false,
            'message' => 'Payment not found',
        ], 404);
    }

    if ($payment->type === 'momo') {
        Log::error('Validation failed:', [
            'type'  => "momo",
            'transaction_id' => $payment->transaction_id,
        ]);
        return $this->checkMomoStatus($payment->transaction_id, $payment);
    }

    if ($payment->type === 'flutterwave') {
         Log::error('Validation failed:', [
            'type'  => "flutterwave",
            'transaction_id' => $payment->transaction_id,
        ]);
        return $this->checkFlutterwaveStatus($payment->transaction_id, $payment);
    }

    return response()->json([
        'success' => false,
        'message' => 'Unsupported payment type',
    ], 400);
}

    protected function checkMomoStatus(string $referenceId,  $payment)
    {
        try {
            $collection = new Collection();
            $status = $collection->getTransactionStatus($referenceId);

            if (isset($status['status']) && strtolower($status['status']) === 'successful') {
            $payment->status = 'success';
            $payment->save();
        }

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'MoMo error: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function checkFlutterwaveStatus(string $transactionId,  $payment)
    {
        $response = Http::withToken(env('FLW_SECRET_KEY'))
            ->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

        if ($response->successful()) {
             $data = $response->json()['data'] ?? [];
            if (isset($data['status']) && strtolower($data['status']) === 'successful') {
            $payment->status = 'success';
            $payment->save();
            }
            return response()->json([
                'success' => true,
                'data' => $response->json()['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response->json()['message'] ?? 'Failed to verify Flutterwave transaction.',
        ], $response->status());
    }



  
}
