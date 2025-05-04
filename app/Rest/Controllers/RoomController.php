<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Room;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RoomController extends RestController
{
   
    public function index()
    {
        $perPage = 10; // You can change this to any number or get it from query params
        $hotels = Room::with('photos')->paginate($perPage);
    
        return response()->json($hotels, 200);
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
            'hotel_id' => 'required',
            'status' => 'sometimes|string|max:50', // Optional status field
        ]);

        // Automatically set updated_by

        $room = Room::create($validated);
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos/rooms', 'public');
        
                Photo::create([
                    'name' => $photo->getClientOriginalName(),
                    'path' => $path,
                    'status' => 'active',
                    'object_type' => 'room',
                    'object_id' => $room->id,
                ]);
            }
        }
        return response()->json($room, 201);
    }

    public function show($id)
{
    $room = Room::with('photos', 'hotel', 'updatedBy', 'deletedBy') // Ensure necessary relationships are loaded
        ->findOrFail($id);

    // Fetch similar rooms based on type or hotel_id
    $similarRooms = Room::where('id', '!=', $id) // Exclude the current room
        ->limit(6) // Limit the number of similar rooms
        ->get();

    return response()->json([
        'room' => $room,
        'similarRooms' => $similarRooms
    ]);
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
