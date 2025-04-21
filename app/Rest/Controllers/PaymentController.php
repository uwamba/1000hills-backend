<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Payment;
use App\Rest\Resources\PaymentResource;
use Illuminate\Http\Request;

class PaymentController extends RestController
{
    public function index()
    {
        $payments = Payment::all();
        return PaymentResource::collection($payments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'transaction_id' => 'required|string|max:255',
            'amount_paid' => 'required|numeric',
            'account' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'status' => 'required|string|max:50',
        ]);

        $payment = Payment::create($validated);
        return new PaymentResource($payment);
    }

    public function show(Payment $payment)
    {
        return new PaymentResource($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|required|exists:clients,id',
            'transaction_id' => 'sometimes|required|string|max:255',
            'amount_paid' => 'sometimes|required|numeric',
            'account' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:50',
            'status' => 'sometimes|required|string|max:50',
        ]);

        $payment->update($validated);
        return new PaymentResource($payment);
    }

    public function destroy($payment)
    {
        $payment->delete();
        return response()->json(null, 204);
    }
}
