<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Journey;
use App\Rest\Resources\JourneyResource;
use Illuminate\Http\Request;

class JourneyController extends RestController
{
    public function index()
    {
        return JourneyResource::collection(Journey::all());
    }

    public function store(Request $request)
    {
        // Including new fields in validation
        $validated = $request->validate([
            'route_id' => 'required|exists:transport_routes,id',
            'time' => 'required|date_format:H:i:s',
            'status' => 'nullable|string',           // New field validation
            'updated_by' => 'nullable|integer',      // New field validation
            'deleted_by' => 'nullable|integer',      // New field validation
            'deleted_on' => 'nullable|date',         // New field validation
        ]);

        // Create the journey with the validated data, including the new fields
        $journey = Journey::create($validated);

        return new JourneyResource($journey);
    }

    public function show(Journey $journey)
    {
        return new JourneyResource($journey);
    }

    public function update(Request $request, Journey $journey)
    {
        // Including new fields in validation
        $validated = $request->validate([
            'route_id' => 'sometimes|required|exists:transport_routes,id',
            'time' => 'sometimes|required|date_format:H:i:s',
            'status' => 'nullable|string',           // New field validation
            'updated_by' => 'nullable|integer',      // New field validation
            'deleted_by' => 'nullable|integer',      // New field validation
            'deleted_on' => 'nullable|date',         // New field validation
        ]);

        // Update the journey with the validated data, including the new fields
        $journey->update($validated);

        return new JourneyResource($journey);
    }

    public function destroy($journey)
    {
        // Mark the journey as deleted, if applicable
        $journey->delete();
        return response()->json(null, 204);
    }
}
