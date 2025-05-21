<?php

namespace App\Rest\Controllers;

use App\Models\Retreat;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Rest\Controller as RestController;
use App\Rest\Resources\RetreatResource;

class RetreatController extends RestController
{
    public function index()
    {
        return RetreatResource::collection(
            Retreat::whereNull('deleted_on')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'status' => 'nullable|string|in:active,inactive',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['updated_by'] = Auth::id();

        $retreat = Retreat::create($validated);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $path = $photoFile->store('retreats', 'public');

                Photo::create([
                    'name' => $photoFile->getClientOriginalName(),
                    'object_id' => $retreat->id,
                    'object_type' => 'retreat',
                    'path' => $path,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return new RetreatResource($retreat);
    }

    public function show(Retreat $retreat)
    {
        return new RetreatResource($retreat);
    }

    public function update(Request $request, Retreat $retreat)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'sometimes|required|string|max:255',
            'capacity' => 'sometimes|required|integer',
            'status' => 'nullable|string|in:active,inactive',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['updated_by'] = Auth::id();
        $retreat->update($validated);

        // Handle new photo uploads on update
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $path = $photoFile->store('retreats', 'public');

                Photo::create([
                    'name' => $photoFile->getClientOriginalName(),
                    'object_id' => $retreat->id,
                    'object_type' => 'retreat',
                    'path' => $path,
                    'status' => 'active',
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return new RetreatResource($retreat);
    }
    public function destroy($id)
{
    $retreat = Retreat::find($id);

    if (!$retreat) {
        return response()->json(['message' => 'Retreat not found'], 404);
    }

    $retreat->delete();

    return response()->json(['message' => 'Retreat deleted successfully']);
}
}
