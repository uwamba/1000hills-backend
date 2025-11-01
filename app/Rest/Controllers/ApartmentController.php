<?php

namespace App\Rest\Controllers;

use App\Models\Apartment;
use App\Models\Photo;
use App\Rest\Controller as RestController;
use App\Rest\Resources\ApartmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApartmentController extends RestController
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

        $apartmentsQuery = Apartment::withoutGlobalScope()->with('photos');

        if ($user->role === 'Manager') {
            // Only apartments whose owner was created by this manager
            Log::info('Filtering apartments for Manager', [
                'manager_id' => $user->id,
            ]);
            $apartmentsQuery->ownedBy($user->id);
            Log::info('query after filter', [
                'query_sql' => $apartmentsQuery->toSql(),
                'query_bindings' => $apartmentsQuery->getBindings(),
            ]);

        }

        $apartments = $apartmentsQuery->get();

        return ApartmentResource::collection($apartments);

    }

    public function apartmentList(Request $request)
    {
        // $query = Apartment::with('photos');
        $query = Apartment::with(['photos', 'activeBookings:id,object_id,from_date_time,to_date_time']);

        // Log the entire result

        // Determine which price to filter: night or month
        $priceField = match ($request->input('price_type')) {
            'month' => 'price_per_month',
            default => 'price_per_night', // fallback to nightly
        };

        // Price filtering
        if ($request->filled('min_price')) {
            $minPrice = $request->input('min_price');
            $query->where($priceField, '>=', $minPrice);
            \Log::debug("Filtering apartments with $priceField >= $minPrice");
        }

        if ($request->filled('max_price')) {
            $maxPrice = $request->input('max_price');
            $query->where($priceField, '<=', $maxPrice);
            \Log::debug("Filtering apartments with $priceField <= $maxPrice");
        }

        // Availability filtering
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->input('from_date');
            $to = $request->input('to_date');

            $query->whereRaw("
        NOT EXISTS (
            SELECT 1 FROM bookings
            WHERE bookings.object_type = 'apartment'
              AND bookings.object_id = apartments.id
              AND (
                  (bookings.from_date_time BETWEEN ? AND ?)
                  OR (bookings.to_date_time BETWEEN ? AND ?)
                  OR (bookings.from_date_time <= ? AND bookings.to_date_time >= ?)
              )
        )
    ", [$from, $to, $from, $to, $from, $to]);
        }

        return ApartmentResource::collection($query->get());
    }

    public function featuredApartemntList()
    {
        $hotels = Apartment::with('photos')->take(3)->get();

        return response()->json($hotels, 200);
    }

    public function getAllApartmentNames()
    {
        $apart = Apartment::select('id', 'name')->get();

        return response()->json($apart);
    }

    public function store(Request $request)
    {
        // Log incoming request data for debugging (avoid logging sensitive data in production)
        Log::info('Apartment store request received', [
            'user_id' => Auth::id(),
            'input' => $request->all(),
            'files' => $request->files->keys(), // to know which file fields are present
        ]);

        // Define validation rules (corrected field name to apartment_owner_id)
        $rules = [
            'name' => 'required|string|max:255',
            'number_of_bedroom' => 'required|integer|min:1',
            'kitchen_inside' => 'required|boolean',
            'kitchen_outside' => 'required|boolean',
            'number_of_floor' => 'required|integer|min:1',
            'address' => 'required|string|max:255',
            'coordinate' => 'nullable|string', // you may further validate format if needed
            'annexes' => 'nullable|string',
            'description' => 'nullable|string',
            'view' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'swimming_pool' => 'nullable|boolean',
            'laundry' => 'nullable|boolean',
            'gym' => 'nullable|boolean',
            'room_service' => 'nullable|boolean',
            'sauna_massage' => 'nullable|boolean',
            'price_per_night' => 'nullable|numeric',
            'price_per_month' => 'nullable|numeric',
            'apartment_owner_id' => 'required|exists:apartment_owners,id',
            'contact' => 'nullable|string|max:20', // Assuming contact number is optional

        ];

        // Create the validator instance
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            // Log validation failure
            Log::warning('Apartment validation failed', [
                'user_id' => Auth::id(),
                'errors' => $errors,
                'input' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Validation Error',
                'errors' => $errors,
            ], 422);
        }

        // Validation passed
        $validated = $validator->validated();

        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'active';
        // Track who updated/created
        $validated['updated_by'] = Auth::id();
        // If you have created_by field and want to set:
        $validated['created_by'] = Auth::id();

        // If 'coordinate' needs parsing from "lat,lng" string to store as JSON or separate columns,
        // handle here before create. For example:
        // if (!empty($validated['coordinate'])) {
        //     [$lat, $lng] = explode(',', $validated['coordinate']);
        //     $validated['latitude'] = trim($lat);
        //     $validated['longitude'] = trim($lng);
        //     unset($validated['coordinate']);
        // }

        try {
            // Create the apartment record
            $apartment = Apartment::create($validated);
            Log::info('Apartment created', [
                'user_id' => Auth::id(),
                'apartment_id' => $apartment->id,
            ]);

            // Handle photos upload if present
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    try {
                        $path = $photo->store('photos/apartments', 'public');
                        Photo::create([
                            'name' => $photo->getClientOriginalName(),
                            'path' => $path,
                            'object_type' => 'apartment',
                            'object_id' => $apartment->id,
                        ]);
                        Log::info('Apartment photo saved', [
                            'apartment_id' => $apartment->id,
                            'path' => $path,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to store apartment photo', [
                            'apartment_id' => $apartment->id,
                            'error' => $e->getMessage(),
                        ]);
                        // Optionally continue or return error: here we continue to next photo
                    }
                }
            }

            // Return the created resource
            return new ApartmentResource($apartment);

        } catch (\Exception $e) {
            // Log unexpected exception
            Log::error('Unexpected error creating apartment', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to create apartment due to server error',
            ], 500);
        }
    }

    public function show(Apartment $apartment)
    {
        $apartment->load('photos');

        return new ApartmentResource($apartment);
    }

    public function update(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'number_of_bedroom' => 'sometimes|required|integer',
            'kitchen_inside' => 'sometimes|required|boolean',
            'kitchen_outside' => 'sometimes|required|boolean',
            'number_of_floor' => 'sometimes|required|integer',
            'address' => 'sometimes|required|string|max:255',
            'coordinate' => 'nullable|string',
            'annexes' => 'nullable|string',
            'description' => 'nullable|string',
            'contract' => 'nullable|string',
            'view' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'swimming_pool' => 'nullable|boolean',
            'laundry' => 'nullable|boolean',
            'gym' => 'nullable|boolean',
            'room_service' => 'nullable|boolean',
            'price_per_night' => 'required|numeric',
            'price_per_month' => 'required|numeric',
            'sauna_massage' => 'nullable|boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $apartment->update($validated);

        // Save new photos if present
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos/apartments', 'public');

                Photo::create([
                    'name' => $photo->getClientOriginalName(),
                    'path' => $path,
                    'object_type' => 'apartment',
                    'object_id' => $apartment->id,
                ]);
            }
        }

        return new ApartmentResource($apartment);
    }

    public function destroy($id)
    {
        $apartment = Apartment::find($id);

        if (! $apartment) {
            return response()->json(['message' => 'Apartment not found'], 404);
        }

        $apartment->delete();

        return response()->json(['message' => 'Apartment deleted successfully'], 200);
    }
}
