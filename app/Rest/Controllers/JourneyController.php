<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Journey;
use App\Rest\Resources\JourneyResource;
use Illuminate\Http\Request;

class JourneyController extends RestController
{
    public function index()
    {
        return JourneyResource::collection(
    Journey::with(['bus.agency', 'bus.seatType'])->get()
    );

    }

public function journeyList(Request $request)
{
    $query = Journey::with(['bus.agency', 'bus.seatType']);

    // Filter by search (route or agency name)
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('route', 'like', '%' . $search . '%')
              ->orWhereHas('bus.agency', function ($q2) use ($search) {
                  $q2->where('name', 'like', '%' . $search . '%');
              });
        });
    }

    // Filter by agency
    if ($request->filled('agency')) {
        $query->whereHas('bus.agency', function ($q) use ($request) {
            $q->where('name', $request->input('agency'));
        });
    }

    // Filter by departure date
    if ($request->filled('departure_date')) {
        $query->whereDate('departure', $request->input('departure_date'));
    }

    return JourneyResource::collection($query->get());
}

 public function featuredJourneyList()
    {
        $hotels = Journey::take(3)->get();


        return response()->json($hotels, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|string|max:255',
            'to' => 'required|string|max:255',
            'price' => 'required|numeric',
            'departure' => 'required|date',
            'return' => 'nullable|date',
            'bus_id' => 'required|exists:buses,id',
            'time' => 'required|date_format:H:i',
        ]);

        $journey = Journey::create($validated);

        return new JourneyResource($journey);
    }

    public function show(Journey $journey)
    {
        return new JourneyResource($journey);
    }

    public function update(Request $request, Journey $journey)
    {
        $validated = $request->validate([
            'route_id' => 'sometimes|required|exists:transport_routes,id',
            'from' => 'sometimes|required|string|max:255',
            'to' => 'sometimes|required|string|max:255',
            'departure' => 'sometimes|required|date',
            'return' => 'nullable|date',
            'price' => 'required|numeric',
            'bus_id' => 'sometimes|required|exists:buses,id',
            'status' => 'nullable|string',
            'updated_by' => 'nullable|integer|exists:users,id',
            'deleted_by' => 'nullable|integer|exists:users,id',
            'deleted_on' => 'nullable|date',
        ]);

        $journey->update($validated);

        return new JourneyResource($journey);
    }

   public function updateStatus(Request $request, $id) {
    $journey = Journey::findOrFail($id);
    $journey->status = $request->input('status');
    $journey->save();

    return response()->json(['message' => 'Status updated']);
}

public function destroy($id) {
    $journey = Journey::findOrFail($id);
    $journey->delete();

    return response()->json(['message' => 'Journey deleted']);
}
}
