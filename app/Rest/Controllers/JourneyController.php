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
        $validated = $request->validate([
            'route_id' => 'required|exists:transport_routes,id',
            'time' => 'required|date_format:H:i:s',
        ]);

        $journey = Journey::create($validated);

        return new JourneyResource($journey);
    }

    public function show(Journey $journey)
    {
        return new JourneyResource($journey);
    }

    public function update(Request $request, Journey $journey)
    {
        $validated = $request->validate([
            'route_id' => 'sometimes|required|exists:transport_routes,id',
            'time' => 'sometimes|required|date_format:H:i:s',
        ]);

        $journey->update($validated);

        return new JourneyResource($journey);
    }

    public function destroy( $journey)
    {
        $journey->delete();
        return response()->json(null, 204);
    }
}
