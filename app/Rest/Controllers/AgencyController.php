<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Agency;
use App\Rest\Resources\AgencyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyController extends RestController
{
    public function index()
    {
        // Return only agencies that are not soft-deleted
        return AgencyResource::collection(
            Agency::whereNull('deleted_on')->get()
        );
    }
    public function getAllAgencyNames()
{
    $agencies = Agency::select('id', 'name')->get();

    return response()->json($agencies);
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $agency = Agency::create([
            ...$validated,
            'updated_by' => Auth::id(),
        ]);

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
            'status' => 'nullable|in:active,inactive',
        ]);

        $agency->update([
            ...$validated,
            'updated_by' => Auth::id(),
        ]);

        return new AgencyResource($agency);
    }

   
}
