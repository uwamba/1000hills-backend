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
use Illuminate\Support\Facades\Log;


class RetreatController extends RestController
{
    public function index()
    {
        return RetreatResource::collection(
            Retreat::whereNull('deleted_on')->get()
        );
    }


public function retreatList(Request $request)
{
    Log::info('--- Retreat list request received ---');
    Log::debug('Request query:', $request->all());

    $query = Retreat::with('photos');

    // Price per person range
    if ($request->filled('min_price_per_person')) {
        $query->where('price_per_person', '>=', $request->min_price_per_person);
    }
    if ($request->filled('max_price_per_person')) {
        $query->where('price_per_person', '<=', $request->max_price_per_person);
    }

    // Package price range
    if ($request->filled('min_package_price')) {
        $query->where('package_price', '>=', $request->min_package_price);
    }
    if ($request->filled('max_package_price')) {
        $query->where('package_price', '<=', $request->max_package_price);
    }

    // Capacity range
    if ($request->filled('min_capacity')) {
        $query->where('capacity', '>=', $request->min_capacity);
    }
    if ($request->filled('max_capacity')) {
        $query->where('capacity', '<=', $request->max_capacity);
    }

    // Pricing type
    if ($request->filled('pricing_type')) {
        $query->where('pricing_type', $request->pricing_type);
    }

    // Date availability filter
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $from = $request->from_date;
        $to = $request->to_date;

        Log::debug("Filtering available retreats between: $from and $to");

        $query->whereRaw("
            NOT EXISTS (
                SELECT 1 FROM bookings
                WHERE bookings.object_type = 'event'
                  AND bookings.object_id = retreats.id
                  AND (
                      (bookings.from_date_time BETWEEN ? AND ?)
                      OR (bookings.to_date_time BETWEEN ? AND ?)
                      OR (bookings.from_date_time <= ? AND bookings.to_date_time >= ?)
                  )
            )
        ", [$from, $to, $from, $to, $from, $to]);
    } else {
        Log::debug("Filtering retreats with no bookings (no date range provided)");

        $query->whereDoesntHave('bookings', function ($q) {
            $q->where('object_type', 'event');
        });
    }

    $retreats = $query->paginate(100);

    Log::info("Returning " . count($retreats) . " retreats on page {$retreats->currentPage()}");

    return RetreatResource::collection($retreats);
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
