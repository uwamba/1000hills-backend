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
                $booking->status = 'approved'; // Update booking status
                $booking->payment_provider_id = $transactionId;
                $booking->save();
            }
            $payment=Payment::where('transaction_id', $txRef)->first();
            if ($payment) {
                $payment->status = 'paid'; // Update payment status
                $payment->payment_method = 'flutterwave';
                $payment->save();
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
