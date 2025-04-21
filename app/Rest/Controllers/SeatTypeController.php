<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\SeatType;
use App\Rest\Resources\SeatTypeResource;
use Illuminate\Http\Request;

class SeatTypeController extends RestController
{
    public function index()
    {
        return SeatTypeResource::collection(SeatType::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'row' => 'required|integer',
            'column' => 'required|integer',
            'name' => 'required|string|max:255',
            'seat_row' => 'required|integer',
            'seat_column' => 'required|integer',
            'exclude' => 'nullable|json',
        ]);

        $seatType = SeatType::create($validated);

        return new SeatTypeResource($seatType);
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
        ]);

        $seatType->update($validated);

        return new SeatTypeResource($seatType);
    }

    public function destroy( $seatType)
    {
        $seatType->delete();
        return response()->json(null, 204);
    }
}
