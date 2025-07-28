<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use App\Models\Booking;
use App\Models\Payment;



class FlutterwaveController extends RestController
{
    public function verifyPayment(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $txRef = $request->input('tx_ref');
        $status = $request->input('status');

        if (!$transactionId || !$txRef || !$status) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Missing payment data.'
            ], 400);
        }

        try {
            $secretKey = env('FLW_SECRET_KEY');

            $response = Http::withToken($secretKey)
                ->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Flutterwave API failed.'
                ], 500);
            }

            $data = $response->json();

            if (
                isset($data['status']) &&
                $data['status'] === 'success' &&
                $data['data']['status'] === 'successful'
            ) {


                $booking = Booking::where('transaction_ref', $txRef)->first();
                if ($booking) {
                    Log::info("Booking found for tx_ref: {$txRef}", ['booking_id' => $booking->id]);
                    $booking->status = 'approved';
                    $booking->payment_provider_id = $transactionId;
                    $saved = $booking->save();
                    Log::info("Booking save result:", ['saved' => $saved]);
                } else {
                    Log::warning("No booking found for tx_ref: {$txRef}");
                }

                $payment = Payment::where('transaction_id', $txRef)->first();
                if ($payment) {
                    Log::info("Payment found for transaction_id: {$txRef}", ['payment_id' => $payment->id]);
                    $payment->status = 'paid';
                    $payment->payment_method = 'flutterwave';
                    $saved = $payment->save();
                    Log::info("Payment save result:", ['saved' => $saved]);
                } else {
                    Log::warning("No payment found for transaction_id: {$txRef}");
                }


                return response()->json([
                    'status' => 'successful',
                    'payment_details' => $data['data'] // ✅ Include full Flutterwave payment info
                ]);
            }

            if ($status === 'cancelled') {
                return response()->json([
                    'status' => 'cancelled',
                    'message' => 'Payment was cancelled.'
                ]);
            }

            return response()->json([
                'status' => 'failed',
                'message' => 'Payment verification failed.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
