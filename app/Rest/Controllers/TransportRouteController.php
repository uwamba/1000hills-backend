<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\TransportRoute;
use App\Rest\Resources\TransportRouteResource;
use Illuminate\Http\Request;

class TransportRouteController extends RestController
{
    public function index()
    {
        return TransportRouteResource::collection(TransportRoute::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'from' => 'required|string|max:255',
            'to' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'nullable|string|max:255',
            'updated_by' => 'nullable|integer|exists:users,id',
            'deleted_by' => 'nullable|integer|exists:users,id',
            'deleted_on' => 'nullable|date',
        ]);

        $route = TransportRoute::create($validated);

        return new TransportRouteResource($route);
    }

    public function show(TransportRoute $route)
    {
        return new TransportRouteResource($route);
    }

    public function update(Request $request, TransportRoute $route)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'from' => 'sometimes|required|string|max:255',
            'to' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'status' => 'sometimes|nullable|string|max:255',
            'updated_by' => 'sometimes|nullable|integer|exists:users,id',
            'deleted_by' => 'sometimes|nullable|integer|exists:users,id',
            'deleted_on' => 'sometimes|nullable|date',
        ]);

        $route->update($validated);

        return new TransportRouteResource($route);
    }

    public function destroy($route)
    {
        $route->delete();
        return response()->json(null, 204);
    }
}
