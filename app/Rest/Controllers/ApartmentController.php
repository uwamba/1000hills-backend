<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Apartment;
use App\Models\Photo;
use App\Rest\Resources\ApartmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends RestController
{
    public function index()
    {
        return ApartmentResource::collection(Apartment::with('photos')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number_of_bedroom' => 'required|integer',
            'kitchen_inside' => 'required|boolean',
            'kitchen_outside' => 'required|boolean',
            'number_of_floor' => 'required|integer',
            'address' => 'required|string|max:255',
            'coordinate' => 'nullable|string',
            'annexes' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'swimming_pool' => 'nullable|boolean',
            'laundry' => 'nullable|boolean',
            'gym' => 'nullable|boolean',
            'room_service' => 'nullable|boolean',
            'sauna_massage' => 'nullable|boolean',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['updated_by'] = Auth::id();

        $apartment = Apartment::create($validated);

        // Save photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos/apartments', 'public');

                Photo::create([
                    'name' => $photo->getClientOriginalName(),
                    'path' => $path,
                    'status' => 'active',
                    'object_type' => 'apartment',
                    'object_id' => $apartment->id,
                ]);
            }
        }

        return new ApartmentResource($apartment);
    }

    public function show(Apartment $apartment)
    {
        $apartment->load('photos');
        return new ApartmentResource($apartment);
    }

    public function update(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'number_of_bedroom' => 'sometimes|required|integer',
            'kitchen_inside' => 'sometimes|required|boolean',
            'kitchen_outside' => 'sometimes|required|boolean',
            'number_of_floor' => 'sometimes|required|integer',
            'address' => 'sometimes|required|string|max:255',
            'coordinate' => 'nullable|string',
            'annexes' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'swimming_pool' => 'nullable|boolean',
            'laundry' => 'nullable|boolean',
            'gym' => 'nullable|boolean',
            'room_service' => 'nullable|boolean',
            'sauna_massage' => 'nullable|boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $apartment->update($validated);

        // Save new photos if present
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos/apartments', 'public');

                Photo::create([
                    'name' => $photo->getClientOriginalName(),
                    'path' => $path,
                    'status' => 'active',
                    'object_type' => 'apartment',
                    'object_id' => $apartment->id,
                ]);
            }
        }

        return new ApartmentResource($apartment);
    }
    public function destroy($id)
{
    $apartment = Apartment::find($id);

    if (!$apartment) {
        return response()->json(['message' => 'Apartment not found'], 404);
    }

    $apartment->delete();

    return response()->json(['message' => 'Apartment deleted successfully'], 200);
}

}
