<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


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
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'coordinate' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contract' => 'nullable|string',
            'stars' => 'nullable|integer|min:1|max:5',
            'working_time' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'updated_by' => 'nullable|integer|exists:users,id',
            'deleted_by' => 'nullable|integer|exists:users,id',
            'deleted_on' => 'nullable|date',
        ]);
    } catch (ValidationException $e) {
        Log::warning('Validation failed for hotel creation', [
            'errors' => $e->errors(),
            'input' => $request->all(),
            'user_id' => auth()->id(), // optional if using auth
        ]);

        // You can re-throw the exception or return a custom response
        throw $e;
        // return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
    }

    $hotel = Hotel::create($validated);

    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('photos/hotels', 'public');

            Photo::create([
                'name' => $photo->getClientOriginalName(),
                'path' => $path,
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
            'contract' => 'nullable|string',
            'stars' => 'nullable|integer|min:1|max:5',
            'working_time' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'updated_by' => 'nullable|integer|exists:users,id',
            'deleted_by' => 'nullable|integer|exists:users,id',
            'deleted_on' => 'nullable|date',
        ]);
    
        $hotel->update($validated);

        // Handle photo replacement
        if ($request->hasFile('photos')) {
            // Delete old photos
            foreach ($hotel->photos as $photo) {
                if ($photo->path && Storage::disk('public')->exists($photo->path)) {
                    Storage::disk('public')->delete($photo->path);
                }
                $photo->delete();
            }

            // Upload new photo(s)
            foreach ($request->file('photos') as $uploadedPhoto) {
                $path = $uploadedPhoto->store('photos/hotels', 'public');

                Photo::create([
                    'name' => $uploadedPhoto->getClientOriginalName(),
                    'path' => $path,
                    'object_type' => 'hotel',
                    'object_id' => $hotel->id,
                ]);
            }
        }

        return response()->json($hotel->load('photos'));
    }
    

    public function destroy($id)
{
    $hotel = Hotel::find($id);

    if (!$hotel) {
        return response()->json(['message' => 'Hotel not found'], 404);
    }

    $hotel->delete();

    return response()->json(['message' => 'Hotel deleted successfully'], 200);
}

}
