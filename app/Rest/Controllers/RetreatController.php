<?php

namespace App\Rest\Controllers;

use App\Models\Retreat;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Rest\Controller as RestController;
use App\Rest\Resources\RetreatResource;
use Carbon\Carbon;

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
            'type' => 'nullable|string|max:255',
            'wifi' => 'nullable|boolean',
            'projector' => 'nullable|boolean',
            'theater' => 'nullable|boolean',
            'flip_chart' => 'nullable|boolean',
            'whiteboard' => 'nullable|boolean',
            'pricing_type' => 'nullable|string',
            'price_per_person' => 'nullable|numeric',
            'package_price' => 'nullable|numeric',
            'package_size' => 'nullable|integer',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['updated_by'] = Auth::id();

        $retreat = Retreat::create($validated);

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
            'type' => 'nullable|string|max:255',
            'wifi' => 'nullable|boolean',
            'projector' => 'nullable|boolean',
            'theater' => 'nullable|boolean',
            'flip_chart' => 'nullable|boolean',
            'whiteboard' => 'nullable|boolean',
            'pricing_type' => 'nullable|string|in:per_person,per_package',
            'price_per_person' => 'nullable|numeric',
            'package_price' => 'nullable|numeric',
            'package_size' => 'nullable|integer',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['updated_by'] = Auth::id();
        $retreat->update($validated);

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

    public function destroy($id)
    {
        $retreat = Retreat::find($id);

        if (!$retreat) {
            return response()->json(['message' => 'Retreat not found'], 404);
        }

        // Soft-delete using deleted_on field
        $retreat->update([
            'deleted_on' => Carbon::now(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Retreat deleted successfully']);
    }
}
