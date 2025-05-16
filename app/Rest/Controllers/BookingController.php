<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Agency;
use App\Models\TransportRoute;
use App\Rest\Resources\BookingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\PaymentLinkMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Journey;

use Illuminate\Support\Facades\Log;


class BookingController extends RestController
{
    public function index()
    {
        $bookings = Booking::all();
        return BookingResource::collection($bookings);
    }



    public function store(Request $request)
    {
        Log::info('Booking.store called with payload: ' . json_encode($request->all()));

        // Validate incoming request
        $validated = $request->validate([
            'from_date_time' => 'required|date',
            'to_date_time' => 'required|date|after:from_date_time',
            'email' => 'required|email',
            'names' => 'required|string',
            'country' => 'required|string',
            'phone' => 'required|string',
            'object_type' => 'required|string|max:255',
            'object_id' => 'required',
            'amount_to_pay' => 'required',
            'status' => 'nullable|string|max:50',
        ]);
        Log::info('Validation passed', $validated);

        // Find or create client
        $client = Client::firstOrCreate(
            ['email' => $validated['email']],
            [
                'names' => $validated['names'],
                'country' => $validated['country'],
                'phone' => $validated['phone'],
                'password' => bcrypt('defaultpassword'), // Default password
                'address' => $validated['address'] ?? $validated['country']
            ],
            // Default to country if address not provided

        );
        Log::info('Client retrieved/created', ['client_id' => $client->id]);






        // ...

        // Fetch the right object based on type
        $objectType = $validated['object_type'];
        $objectId = $validated['object_id'];

        if ($objectType === 'room') {
            $object = Room::find($objectId);
            if (!$object) {
                Log::error('Room not found', ['object_id' => $objectId]);
                return response()->json(['message' => 'Room not found'], 404);
            }
            Log::info('Room found', ['room_id' => $object->id]);

        } elseif ($objectType === 'ticket') {
            $object = Journey::find($objectId);
            if (!$object) {
                Log::error('Journey not found', ['object_id' => $objectId]);
                return response()->json(['message' => 'Journey not found'], 404);
            }
            Log::info('Journey found', ['journey_id' => $object->id]);

        } else {
            Log::error('Unsupported object_type', ['object_type' => $objectType]);
            return response()->json(['message' => 'Unsupported object type'], 400);
        }

        // Create booking
        try {
            $booking = Booking::create([
                'from_date_time' => $validated['from_date_time'],
                'to_date_time' => $validated['to_date_time'],
                'client_id' => $client->id,
                'object_type' => $objectType,
                'object_id' => $object->id,
                'amount_to_pay' => $validated['amount_to_pay'],
                'status' => $validated['status'] ?? "pending",
            ]);

            Log::info('Booking created successfully', ['booking_id' => $booking->id]);

        } catch (\Exception $e) {
            Log::error('Failed to create booking', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Booking failed'], 500);
        }

        $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL')), '/');
        $params = http_build_query([
            'names' => $booking->client->names,
            'email' => $booking->client->email,
            'country' => $booking->client->country,
            'phone' => $booking->client->phone,
            'amount' => $booking->amount_to_pay,
            'booking' => $booking->id,
        ]);
        $paymentLink = "{$frontend}/payment?{$params}";
        Log::info("Generated front‑end payment link: {$paymentLink}");

        // 3️⃣ Email that link
        Mail::to($booking->client->email)
            ->send(new PaymentLinkMail($booking, $paymentLink));
        Log::info("Payment form link emailed to " . $booking->client->email);

        // 4️⃣ Return response
        return response()->json([
            'message' => 'Booking successful; payment link sent',
            'booking' => $booking,
            'payment_url' => $paymentLink,
        ], 201);

    }

    public function show(Booking $booking)
    {
        return new BookingResource($booking);
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'from_date_time' => 'sometimes|required|date',
            'to_date_time' => 'sometimes|required|date',
            'object_type' => 'sometimes|required|string|max:255',
            'object_id' => 'sometimes|required|integer',
            'client_id' => 'sometimes|required|exists:clients,id',
            'amount_to_pay' => 'sometimes|required|numeric',
            'status' => 'nullable|string|max:50',       // New field
            'updated_by' => 'nullable|integer',         // New field
        ]);

        $validated['updated_by'] = Auth::id(); // Automatically update updated_by

        $booking->update($validated);

        return new BookingResource($booking);
    }

    public function destroy($booking)
    {
        $booking->update([
            'deleted_by' => Auth::id(),
            'deleted_on' => Carbon::now(),
        ]);

        $booking->delete();

        return response()->json(null, 204);
    }
}
