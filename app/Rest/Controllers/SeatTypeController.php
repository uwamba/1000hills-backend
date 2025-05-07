<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\SeatType;
use App\Rest\Resources\SeatTypeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $validated = $request->validate([
            'row' => 'required|integer',
            'column' => 'required|integer',
            'name' => 'required|string|max:255',
            'seat_row' => 'required|integer',
            'seat_column' => 'required|integer',
            'exclude' => 'nullable|json',
            'status' => 'nullable|string|max:50',
        ]);

        $validated['updated_by'] = Auth::id(); // Set current user
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
