<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Rest\Resources\PaymentResource;

class PaymentController extends RestController
{
    public function index()
    {
        $perPage = 10; // You can change or make it dynamic via query params
        $payments = Payment::paginate($perPage);

        return PaymentResource::collection($payments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'transaction_id' => 'required|string|max:255|unique:payments,transaction_id',
            'amount_paid' => 'required|numeric',
            'account' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'status' => 'sometimes|string|max:50', // Optional
        ]);

        $payment = Payment::create($validated);

        return new PaymentResource($payment);
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

  
}
