<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Flutterwave\Payments\Facades\Flutterwave;


class FlutterwaveController extends RestController
{
    public function verifyPayment(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $txRef = $request->input('tx_ref');
        $status = $request->input('status');

        if (!$transactionId || !$txRef || !$status) {
            return response()->json(['status' => 'invalid', 'message' => 'Missing payment data.'], 400);
        }

        try {
            // Use Flutterwave package method to verify
            $payment = Flutterwave::transaction()->verify($transactionId);

            if (
                $payment['status'] === 'success' &&
                $payment['data']['status'] === 'successful'
            ) {
                $booking = Booking::where('transaction_ref', $txRef)->first();

                if ($booking) {
                    $booking->payment_status = 'paid';
                    $booking->payment_provider_id = $transactionId;
                    $booking->save();
                }

                return response()->json(['status' => 'successful']);
            }

            if ($status === 'cancelled') {
                return response()->json(['status' => 'cancelled']);
            }

            return response()->json(['status' => 'failed']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
