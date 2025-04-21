<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Agency;
use App\Rest\Resources\AgencyResource;
use Illuminate\Http\Request;

class AgencyController extends RestController
{
    public function index()
    {
        return AgencyResource::collection(Agency::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $agency = Agency::create($validated);

        return new AgencyResource($agency);
    }

    public function show(Agency $agency)
    {
        return new AgencyResource($agency);
    }

    public function update(Request $request, Agency $agency)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $agency->update($validated);

        return new AgencyResource($agency);
    }

    public function destroy( $agency)
    {
        $agency->delete();
        return response()->json(null, 204);
    }
}
