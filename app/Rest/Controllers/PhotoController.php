<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Photo;
use App\Rest\Resources\PhotoResource;
use Illuminate\Http\Request;

class PhotoController extends RestController
{
    public function index()
    {
        $photos = Photo::all();
        return PhotoResource::collection($photos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'required|string|max:255',
            'object_type' => 'required|string|max:255',
            'object_id' => 'required|integer',
            'status' => 'nullable|string',           // New field validation
            'updated_by' => 'nullable|integer',      // New field validation
            'deleted_by' => 'nullable|integer',      // New field validation
            'deleted_on' => 'nullable|date',         // New field validation
        ]);

        $photo = Photo::create($validated);
        return new PhotoResource($photo);
    }

    public function show(Photo $photo)
    {
        return new PhotoResource($photo);
    }

    public function update(Request $request, Photo $photo)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'path' => 'sometimes|required|string|max:255',
            'object_type' => 'sometimes|required|string|max:255',
            'object_id' => 'sometimes|required|integer',
            'status' => 'nullable|string',           // New field validation
            'updated_by' => 'nullable|integer',      // New field validation
            'deleted_by' => 'nullable|integer',      // New field validation
            'deleted_on' => 'nullable|date',         // New field validation
        ]);

        $photo->update($validated);
        return new PhotoResource($photo);
    }

    public function destroy($photo)
    {
        $photo->delete();
        return response()->json(null, 204);
    }
}
