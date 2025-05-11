<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\SeatType;
use App\Rest\Resources\SeatTypeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SeatTypeController extends RestController
{
    public function index()
    {
        return SeatTypeResource::collection(SeatType::all());
    }
    public function getAllSeatTypeNames()
    {
        $seatTypes = SeatType::select('id', 'name')->get();

        return response()->json($seatTypes);
    }


    public function store(Request $request)
    {
        // Decode seat_layout and exclude if they are JSON strings
        $seatLayout = is_string($request->seat_layout) ? json_decode($request->seat_layout, true) : $request->seat_layout;
        $exclude = is_string($request->exclude) ? json_decode($request->exclude, true) : $request->exclude;

        // Debug parsed data
        Log::debug('Decoded seat_layout:', $seatLayout);
        Log::debug('Decoded exclude:', $exclude);

        // Now validate using the decoded arrays
        $validated = validator([
            'name' => $request->name,
            'seat_layout' => $seatLayout,
            'seat_layout.rows' => $seatLayout['row'] ?? null,
            'seat_layout.columns' => $seatLayout['seats_per_row'] ?? null,
            'exclude' => $exclude,
        ])->validate();

        // Save to database
        $seatType = SeatType::create([
            'name' => $request->name,
            'row' => $seatLayout['row'],
            'column' => $seatLayout['seats_per_row'],
            'seat_row' => $seatLayout['row'],
            'seat_column' => $seatLayout['seats_per_row'],
            'exclude' => $exclude,


        ]);

        Log::debug('Seat type saved:', $seatType->toArray());

        return response()->json(['message' => 'Seat type created', 'data' => $seatType], 201);
    }


    public function show(SeatType $seatType)
    {
        return new SeatTypeResource($seatType);
    }

    public function update(Request $request, SeatType $seatType)
    {
        $validated = $request->validate([
            'row' => 'sometimes|required|integer',
            'column' => 'sometimes|required|integer',
            'name' => 'sometimes|required|string|max:255',
            'seat_row' => 'sometimes|required|integer',
            'seat_column' => 'sometimes|required|integer',
            'exclude' => 'sometimes|nullable|json',
            'status' => 'sometimes|nullable|string|max:50',
        ]);

        $validated['updated_by'] = Auth::id(); // Track who updated
        $seatType->update($validated);

        return new SeatTypeResource($seatType);
    }

    public function destroy($seatType)
    {
        $seatType->update([
            'deleted_by' => Auth::id(),
            'deleted_on' => now(),
        ]);

        $seatType->delete();

        return response()->json(null, 204);
    }
}
