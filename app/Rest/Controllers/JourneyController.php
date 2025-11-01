<?php

namespace App\Rest\Controllers;

use App\Models\Journey;
use App\Rest\Controller as RestController;
use App\Rest\Resources\JourneyResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JourneyController extends RestController
{
    public function index()
    {
        $user = auth()->user();

        Log::info('Journey index accessed', [
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? null,
            'user_email' => $user->email ?? null,
            'user_role' => $user->role ?? 'No role assigned',
        ]);

        $query = Journey::with(['bus.agency', 'bus.seatType', 'exchangeRate'])
            ->latest();

        // If the logged-in user has role "Manager", filter by their agency
        if ($user->role === 'Manager') {
            $query->whereHas('bus.agency', function ($q) use ($user) {
                $q->where('updated_by', $user->id);
            });
        }

        return JourneyResource::collection($query->get());
    }

    public function journeyList(Request $request)
    {
        $query = Journey::with(['bus.agency', 'bus.seatType', 'exchangeRate']);
        $query->whereDate('departure', '>=', Carbon::today());

        // Filter by search (route or agency name)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('route', 'like', '%'.$search.'%')
                    ->orWhereHas('bus.agency', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%'.$search.'%');
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

        return JourneyResource::collection($query->latest()->get());
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
            'currency' => 'required|string|max:3', // ISO currency code, e.g., USD
            'departure' => 'required|date',
            'return' => 'nullable|date',
            'bus_id' => 'required|exists:buses,id',
            'time' => 'required|date_format:H:i',
        ]);
        $admin = auth()->user();

        // Add updated_by automatically
        $validated['updated_by'] = $admin->id;

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

    public function updateStatus(Request $request, $id)
    {
        $journey = Journey::findOrFail($id);
        $journey->status = $request->input('status');
        $journey->save();

        return response()->json(['message' => 'Status updated']);
    }

    public function destroy($id)
    {
        $journey = Journey::findOrFail($id);
        $journey->delete();

        return response()->json(['message' => 'Journey deleted']);
    }

    public function journeyListWithSeats(Request $request)
    {
        $query = Journey::with([
            'bus.agency',
            'bus.seatType',
            'bookings' => function ($q) {
                $q->select('id', 'seat', 'object_id', 'object_type', 'client_id')
                    ->where('object_type', 'ticket')
                    ->with('client:id,names,email,phone'); // Load client info (customize fields as needed)
            },
        ]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('from', 'like', '%'.$search.'%')
                    ->orWhere('to', 'like', '%'.$search.'%')
                    ->orWhereHas('bus.agency', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($request->filled('agency')) {
            $query->whereHas('bus.agency', function ($q) use ($request) {
                $q->where('name', $request->input('agency'));
            });
        }

        if ($request->filled('departure_date')) {
            $query->whereDate('departure', $request->input('departure_date'));
        }

        $journeys = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $journeys->map(function ($journey) {
                return [
                    'id' => $journey->id,
                    'from' => $journey->from,
                    'to' => $journey->to,
                    'departure' => $journey->departure,
                    'bus' => [
                        'id' => $journey->bus->id,
                        'number_plate' => $journey->bus->name ?? null,
                        'agency' => $journey->bus->agency->name ?? null,
                        'seatType' => [
                            'name' => $journey->bus->seatType->name ?? null,
                            'row' => $journey->bus->seatType->row ?? 0,
                            'column' => $journey->bus->seatType->column ?? 0,
                            'exclude' => $journey->bus->seatType->exclude ?? [],
                        ],
                    ],
                    'bookings' => $journey->bookings->map(function ($booking) {
                        return [
                            'id' => $booking->id,
                            'seat' => $booking->seat,
                            'client' => [
                                'id' => $booking->client->id ?? null,
                                'name' => $booking->client->names ?? null,
                                'email' => $booking->client->email ?? null,
                                'phone' => $booking->client->phone ?? null,
                            ],
                        ];
                    })->toArray(),
                ];
            }),
        ]);
    }
}
