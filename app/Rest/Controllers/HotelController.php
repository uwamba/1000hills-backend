<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends RestController
{
    public function index()
    {
        return Hotel::all();
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
        ]);

        $hotel = Hotel::create($validated);
        return response()->json($hotel, 201);
    }

    public function show(Hotel $hotel)
    {
        return $hotel;
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
        ]);

        $hotel->update($validated);
        return response()->json($hotel);
    }

    public function destroy( $hotel)
    {
        $hotel->delete();
        return response()->json(null, 204);
    }
}
