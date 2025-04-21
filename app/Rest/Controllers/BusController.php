<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Bus;
use App\Rest\Resources\BusResource;
use Illuminate\Http\Request;

class BusController extends RestController
{
    public function index()
    {
        return BusResource::collection(Bus::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'seat_type' => 'required|exists:seat_types,id',
            'number_of_seat' => 'required|integer',
            'agency_id' => 'required|exists:agencies,id',
        ]);

        $bus = Bus::create($validated);

        return new BusResource($bus);
    }

    public function show(Bus $bus)
    {
        return new BusResource($bus);
    }

    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'seat_type' => 'sometimes|required|exists:seat_types,id',
            'number_of_seat' => 'sometimes|required|integer',
            'agency_id' => 'sometimes|required|exists:agencies,id',
        ]);

        $bus->update($validated);

        return new BusResource($bus);
    }

    public function destroy( $bus)
    {
        $bus->delete();
        return response()->json(null, 204);
    }
}
