<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RoomController extends RestController
{
    public function index()
    {
        return Room::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'has_wireless' => 'required|boolean',
            'bed_size' => 'required|string|max:255',
            'has_bathroom' => 'required|boolean',
            'price' => 'required|numeric',
            'currency' => 'required|string|max:10',
            'number_of_people' => 'required|integer',
            'has_ac' => 'required|boolean',
            'hotel_id' => 'sometimes|required|exists:hotels,id',
            'status' => 'sometimes|string|max:50', // Optional status field
        ]);

        // Automatically set updated_by
        $validated['updated_by'] = Auth::id();

        $room = Room::create($validated);
        return response()->json($room, 201);
    }

    public function show(Room $room)
    {
        return $room;
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'has_wireless' => 'sometimes|required|boolean',
            'bed_size' => 'sometimes|required|string|max:255',
            'has_bathroom' => 'sometimes|required|boolean',
            'price' => 'sometimes|required|numeric',
            'currency' => 'sometimes|required|string|max:10',
            'number_of_people' => 'sometimes|required|integer',
            'has_ac' => 'sometimes|required|boolean',
            'hotel_id' => 'sometimes|required|exists:hotels,id',
            'status' => 'sometimes|string|max:50', // Optional status update
        ]);

        // Track who updated
        $validated['updated_by'] = Auth::id();

        $room->update($validated);
        return response()->json($room);
    }

    public function destroy($room)
    {
        // Soft-delete related tracking fields
        $room->update([
            'deleted_by' => Auth::id(),
            'deleted_on' => Carbon::now(),
        ]);

        $room->delete();
        return response()->json(null, 204);
    }
}
