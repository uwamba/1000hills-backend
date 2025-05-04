<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Models\Photo;

class HotelController extends RestController
{
    public function index()
{
    $perPage = 10; // You can change this to any number or get it from query params
    $hotels = Hotel::with('photos')->paginate($perPage);

    return response()->json($hotels, 200);
}


public function getAllHotelNames()
{
    $hotels = Hotel::select('id', 'name')->get();

    return response()->json($hotels);
}




    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'coordinate' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'stars' => 'nullable|integer|min:1|max:5',
            'working_time' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',        // New field
            'updated_by' => 'nullable|integer|exists:users,id',  // New field
            'deleted_by' => 'nullable|integer|exists:users,id',  // New field
            'deleted_on' => 'nullable|date',                // New field
        ]);

        $hotel = Hotel::create($validated);
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
               $path = $photo->store('photos/hotels', 'public');

    
               Photo::create([
                    'name' => $photo->getClientOriginalName(),
                    'path' => $path,
                    'status' => 'active',
                    'object_type' => 'hotel',
                    'object_id' => $hotel->id,
                ]);
            }
        }
        return response()->json($hotel, 201);
    }

    public function show(Hotel $hotel)
    {
        return response()->json($hotel);
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:500',
            'coordinate' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'stars' => 'nullable|integer|min:1|max:5',
            'working_time' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',        // New field
            'updated_by' => 'nullable|integer|exists:users,id',  // New field
            'deleted_by' => 'nullable|integer|exists:users,id',  // New field
            'deleted_on' => 'nullable|date',                // New field
        ]);

        $hotel->update($validated);
        return response()->json($hotel);
    }

    public function destroy($hotel)
    {
        $hotel->delete();
        return response()->json(null, 204);
    }
}
