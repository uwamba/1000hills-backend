<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Client;
use App\Rest\Resources\ClientResource;
use Illuminate\Http\Request;

class ClientController extends RestController
{
    public function index()
    {
        $clients = Client::all();
        return ClientResource::collection($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone' => 'required|string|max:20',
        ]);

        $client = Client::create($validated);
        return new ClientResource($client);
    }

    public function show(Client $client)
    {
        return new ClientResource($client);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'sometimes|required|string|max:20',
        ]);

        $client->update($validated);
        return new ClientResource($client);
    }

    public function destroy( $client)
    {
        $client->delete();
        return response()->json(null, 204);
    }
}
