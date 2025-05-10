<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BusTicketController extends RestController
{
    public function index()
    {
        $perPage = 10;
        $buses = Bus::with('agency', 'seatType') // Load relationships
            ->whereNull('deleted_on')           // Exclude soft-deleted entries
            ->paginate($perPage);

        return response()->json($buses, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'seat_type_id' => 'required|exists:seat_types,id',
            'number_of_seat' => 'required|integer',
            'agency_id' => 'required|exists:agencies,id',
            'status' => 'sometimes|string|max:50',
        ]);

        $validated['updated_by'] = Auth::id(); // Track who created/updated

        $bus = Bus::create($validated);

        return response()->json($bus, 201);
    }

    public function show($id)
    {
        $bus = Bus::with('agency', 'seatType')
            ->findOrFail($id);

        // Optional: fetch similar buses from the same agency
        $similarBuses = Bus::where('agency_id', $bus->agency_id)
            ->where('id', '!=', $bus->id)
            ->limit(6)
            ->get();

        return response()->json([
            'bus' => $bus,
            'similarBuses' => $similarBuses
        ]);
    }

    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'seat_type_id' => 'sometimes|required|exists:seat_types,id',
            'number_of_seat' => 'sometimes|required|integer',
            'agency_id' => 'sometimes|required|exists:agencies,id',
            'status' => 'sometimes|nullable|string|max:50',
        ]);

        $validated['updated_by'] = Auth::id(); // Track who updated

        $bus->update($validated);

        return response()->json($bus);
    }

    public function delete(Bus $bus)
    {
        $bus->update([
            'deleted_by' => Auth::id(),
            'deleted_on' => Carbon::now(),
        ]);

        $bus->delete(); // Soft delete

        return response()->json(null, 204);
    }
}
