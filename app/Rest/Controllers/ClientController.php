<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Client;
use App\Rest\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            'names' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',  // if needed
            'status' => 'nullable|string|in:active,inactive', // optional enum
        ]);

        $validated['updated_by'] = Auth::id();  // set updated_by on creation

        $client = Client::create($validated);
        return new ClientResource($client);
    }
    public function storeIfNotExists(Request $request)
{
    return Client::firstOrCreate(
        ['email' => $request->email],
        [
            'names' => $request->names,
            'phone' => $request->phone,
            'country' => $request->country,
        ]
    );
}


    public function show(Client $client)
    {
        return new ClientResource($client);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'names' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'sometimes|required|string|max:20',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $validated['updated_by'] = Auth::id(); // log who updated

        $client->update($validated);
        return new ClientResource($client);
    }

    public function destroy($client)
    {
        $client->update([
            'deleted_by' => Auth::id(),
            'deleted_on' => Carbon::now(),
        ]);

        $client->delete();
        return response()->json(null, 204);
    }
}
