<?php

// app/Http/Controllers/Api/V1/ApartmentController.php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Apartment;
use App\Rest\Resources\ApartmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends RestController
{
    public function index()
    {
        return ApartmentResource::collection(Apartment::all());
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
            'status' => 'nullable|string|in:active,inactive', // new field
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['updated_by'] = Auth::id(); // track creator as updater

        $apartment = Apartment::create($validated);

        return new ApartmentResource($apartment);
    }

    public function show(Apartment $apartment)
    {
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
            'status' => 'nullable|string|in:active,inactive', // new field
        ]);

        $validated['updated_by'] = Auth::id(); // track who updated

        $apartment->update($validated);

        return new ApartmentResource($apartment);
    }

    
}