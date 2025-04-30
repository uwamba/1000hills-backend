<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Booking;
use App\Rest\Resources\BookingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class BookingController extends RestController
{
    public function index()
    {
        $bookings = Booking::all();
        return BookingResource::collection($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_date_time' => 'required|date',
            'to_date_time' => 'required|date',
            'object_type' => 'required|string|max:255',
            'object_id' => 'required|integer',
            'client_id' => 'required|exists:clients,id',
            'amount_to_pay' => 'required|numeric',
            'status' => 'nullable|string|max:50',      // New field
            'updated_by' => 'nullable|integer',        // New field
        ]);

        $validated['updated_by'] = Auth::id(); // Automatically set updated_by if user is authenticated

        $booking = Booking::create($validated);

        return new BookingResource($booking);
    }

    public function show(Booking $booking)
    {
        return new BookingResource($booking);
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'from_date_time' => 'sometimes|required|date',
            'to_date_time' => 'sometimes|required|date',
            'object_type' => 'sometimes|required|string|max:255',
            'object_id' => 'sometimes|required|integer',
            'client_id' => 'sometimes|required|exists:clients,id',
            'amount_to_pay' => 'sometimes|required|numeric',
            'status' => 'nullable|string|max:50',       // New field
            'updated_by' => 'nullable|integer',         // New field
        ]);

        $validated['updated_by'] = Auth::id(); // Automatically update updated_by

        $booking->update($validated);

        return new BookingResource($booking);
    }

    public function destroy( $booking)
    {
        $booking->update([
            'deleted_by' => Auth::id(),
            'deleted_on' => Carbon::now(),
        ]);

        $booking->delete();

        return response()->json(null, 204);
    }
}
