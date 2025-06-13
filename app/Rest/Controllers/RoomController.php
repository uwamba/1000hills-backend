<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Room;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RoomController extends RestController
{
    public function index()
    {
        $perPage = 50;
        $hotels = Room::with('photos')->paginate($perPage);

        return response()->json($hotels, 200);
    }




    public function roomList(Request $request)
    {
        $perPage = 50;

        Log::info('--- Room list request received ---');
        Log::debug('Request query:', $request->all());

        $query = Room::with(['photos', 'hotel']);


        // Price filter
        if ($request->filled('min_price')) {
            Log::debug("Filtering rooms with price >= {$request->min_price}");
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            Log::debug("Filtering rooms with price <= {$request->max_price}");
            $query->where('price', '<=', $request->max_price);
        }

        // Availability filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date;
            $to = $request->to_date;

            Log::debug("Filtering available rooms between: $from and $to");

            $query->whereRaw("
        NOT EXISTS (
            SELECT 1 FROM bookings
            WHERE bookings.object_type = 'room'
              AND bookings.object_id = rooms.id
              AND (
                  (bookings.from_date_time BETWEEN ? AND ?)
                  OR (bookings.to_date_time BETWEEN ? AND ?)
                  OR (bookings.from_date_time <= ? AND bookings.to_date_time >= ?)
              )
        )
    ", [$from, $to, $from, $to, $from, $to]);
        } else {
            Log::debug("No date filter applied");
        }

        $rooms = $query->paginate($perPage);

        Log::info("Returning " . count($rooms) . " rooms on page {$rooms->currentPage()}");

        return response()->json($rooms, 200);
    }


    public function featuredRoomList()
    {
        $hotels = Room::with(['photos', 'hotel'])->take(3)->get();


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
            'hotel_id' => 'required|exists:hotels,id',
            'status' => 'sometimes|string|max:50',

            // New feature fields (optional boolean inputs)
            'has_swimming_pool' => 'sometimes|boolean',
            'has_laundry' => 'sometimes|boolean',
            'has_gym' => 'sometimes|boolean',
            'has_room_service' => 'sometimes|boolean',
            'has_sauna_massage' => 'sometimes|boolean',
            'has_kitchen' => 'sometimes|boolean',
            'has_fridge' => 'sometimes|boolean',
        ]);

        // Automatically set updated_by
        $validated['updated_by'] = Auth::id();

        $room = Room::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos/rooms', 'public');

                Photo::create([
                    'name' => $photo->getClientOriginalName(),
                    'path' => $path,
                    'object_type' => 'room',
                    'object_id' => $room->id,
                ]);
            }
        }

        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::with('photos', 'hotel', 'updatedBy', 'deletedBy')
            ->findOrFail($id);

        $similarRooms = Room::where('id', '!=', $id)
            ->limit(6)
            ->get();

        return response()->json([
            'room' => $room,
            'similarRooms' => $similarRooms
        ]);
    }
    public function roomDetails($id)
    {
        $room = Room::with('photos', 'hotel', 'updatedBy', 'deletedBy')
            ->findOrFail($id);

        $similarRooms = Room::where('id', '!=', $id)
            ->limit(6)
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
            'status' => 'sometimes|string|max:50',

            // New feature fields (optional boolean updates)
            'has_swimming_pool' => 'sometimes|boolean',
            'has_laundry' => 'sometimes|boolean',
            'has_gym' => 'sometimes|boolean',
            'has_room_service' => 'sometimes|boolean',
            'has_sauna_massage' => 'sometimes|boolean',
            'has_kitchen' => 'sometimes|boolean',
            'has_fridge' => 'sometimes|boolean',
        ]);

        $validated['updated_by'] = Auth::id();

        $room->update($validated);

        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $room->update([
            'deleted_by' => Auth::id(),
            'deleted_on' => Carbon::now(),
        ]);

        $room->delete();

        return response()->json(['message' => 'Room deleted successfully'], 200);
    }

}
