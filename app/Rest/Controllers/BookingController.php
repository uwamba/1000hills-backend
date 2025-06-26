<?php

namespace App\Rest\Controllers;

use App\Models\Apartment;
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
use App\Models\Retreat;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;



use Flutterwave\Payments\Facades\Flutterwave;
use Flutterwave\Payments\Data\Currency;


use Bmatovu\MtnMomo\Products\Collection;
use Bmatovu\MtnMomo\Exceptions\CollectionRequestException;
use Illuminate\Support\Str;


class BookingController extends RestController
{
    public function index()
    {
        $bookings = Booking::all();
        return BookingResource::collection($bookings);
    }



    public function roomBookings()
    {
        $roomBookings = Booking::forAdminRooms()
            ->with(['room', 'client'])
            // or ->with(['room','client']) if you prefer; 
            // with('object') will eager-load the Room model via morphTo
            ->get();

        return response()->json([
            'message' => 'Room bookings retrieved successfully',
            'data' => $roomBookings,
        ]);
    }

    public function apartmentBookings()
    {
        $apartmentBookings = Booking::forAdminApartments()
            ->with(['apartment', 'client']) // 'object' is the polymorphic relationship
            ->get();

        return response()->json([
            'message' => 'Apartment bookings retrieved successfully',
            'data' => $apartmentBookings,
        ]);
    }
    public function ticketBookings()
    {
        $ticketBookings = Booking::forAdminTickets()
            ->with(['ticket', 'client']) // 'object' is the polymorphic relationship
            ->get();

        return response()->json([
            'message' => 'Ticket bookings retrieved successfully',
            'data' => $ticketBookings,
        ]);
    }



    public function store(Request $request)
    {
        Log::info('Booking.store called with payload: ' . json_encode($request->all()));

        // Validate incoming request
        try {
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
                'payment_method' => 'required|string',
                'momo_number' => 'nullable|string|max:20',
            ]);

            // continue with storing the data...

        } catch (ValidationException $e) {
            Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }


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

        } elseif ($objectType === 'apartment') {
            $object = Apartment::find($objectId);
            if (!$object) {
                Log::error('Apartemnt not found', ['object_id' => $objectId]);
                return response()->json(['message' => 'Apartemnt not found'], 404);
            }
            Log::info('Room found', ['room_id' => $object->id]);

        } elseif ($objectType === 'ticket') {
            $object = Journey::find($objectId);
            if (!$object) {
                Log::error('Journey not found', ['object_id' => $objectId]);
                return response()->json(['message' => 'Journey not found'], 404);
            }
            Log::info('Journey found', ['journey_id' => $object->id]);

        } elseif ($objectType === 'event') {
            $object = Retreat::find($objectId);
            if (!$object) {
                Log::error('event not found', ['object_id' => $objectId]);
                return response()->json(['message' => 'event not found'], 404);
            }
            Log::info('event found', ['journey_id' => $object->id]);

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

            $paymentModel = Payment::create([
                'transaction_id' => $booking->id, // Placeholder until payment is made
                'client_id' => $client->id,
                'amount_paid' => $booking->amount_to_pay, // Initial amount
                'account' => $client->email,
                'type' => $request->payment_method,
                'status' => 'pending',
                'created_by' => Auth::id(),
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
        $paymentLink = "";
        if ($request->payment_method === 'flutterwave') {
            // 1️⃣ Generate Flutterwave payment link
            $response = $this->makeFlutterwavePaymentLink(
                $booking->amount_to_pay,
                "USD", // Currency
                $booking->client->email
            );

            // Convert JsonResponse to array
            $payment = $response->getData(true); // "true" returns an array instead of stdClass

            Log::info("Generated Flutterwave payment link: " . json_encode($payment));

            // Now you can safely access it like an array
            $paymentLink = $payment['payment_link'];

            $paymentModel->status = 'success';
            $paymentModel->save();

            Log::info("Generated front‑end payment link: {$paymentLink}");


        } elseif ($request->payment_method === 'momo_rwanda') {
            // 2️⃣ Request MTN MoMo payment
            $momo_number = $validated['momo_number'] ?? null;
            if (!$momo_number) {
                Log::error('MTN MoMo phone number is required for MoMo payments');
                return response()->json(['message' => 'MTN MoMo phone number is required'], 400);
            }
            /* $paymentLink = $this->requestMtnMomoPayment(
                $booking->amount_to_pay,
                "FRW", // Assuming EUR for simplicity
                $momo_number
            );
            */
            $paymentLink = "{$frontend}/payment?{$params}";
            Log::info("Generated front‑end payment link: {$paymentLink}");
            $paymentModel->status = 'success';
            $paymentModel->save();
            Log::info("Requested MTN MoMo payment link: {$paymentLink}");
        } else {
            Log::error('Unsupported payment method', ['method' => $request->payment_method]);
            return response()->json(['message' => 'Unsupported payment method'], 400);
        }

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

    public function bookingTicket(Request $request)
    {
        Log::info('Booking.store called with payload: ' . json_encode($request->all()));

        try {
            // Validate incoming request
            $validated = $request->validate([
                'email' => 'required|email',
                'names' => 'required|string',
                'phone' => 'required|string',
                'country' => 'required|string',
                'object_type' => 'required|string|max:255',
                'object_id' => 'required',
                'amount_to_pay' => 'required|numeric',
                'currency_code' => 'nullable|string|max:10',
                'currency_rate_to_usd' => 'nullable|numeric',
                'status' => 'nullable|string|max:50',
                'payment_method' => 'required|string|max:50',
                'momo_number' => 'nullable|string|max:20',
                'extra_note' => 'nullable|string',
                'seat' => 'nullable|string|max:20',
            ]);

        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }

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

        // Fetch the right object based on type
        $objectType = $validated['object_type'];
        $objectId = $validated['object_id'];


        $object = Journey::find($objectId);
        if (!$object) {
            Log::error('object not found', ['object_id' => $objectId]);
            return response()->json(['message' => 'object not found'], 404);
        }
        Log::info('Room found', ['room_id' => $object->id]);



        // Create booking
        try {
            $booking = Booking::create([
                'client_id' => $client->id,
                'object_type' => $objectType,
                'object_id' => $object->id,
                'from_date_time' => now(),
                'to_date_time' => now()->addHours(2),// Assuming Journey has these fields
                'amount_to_pay' => $validated['amount_to_pay'],
                'status' => $validated['status'] ?? "pending",
                'currency_code' => $validated['currency_code'] ?? 'USD',
                'currency_rate_to_usd' => $validated['currency_rate_to_usd'] ?? 1.0, // Default to 1.0 if not provided
                'payment_method' => $validated['payment_method'],
                'extra_note' => $validated['extra_note'] ?? null,
                'momo_number' => $validated['momo_number'] ?? null,
                'seat' => $validated['seat'] ?? null,
            ]);

            $payment = Payment::create([
                'transaction_id' => $booking->id, // Placeholder until payment is made
                'client_id' => $client->id,
                'amount_paid' => $booking->amount_to_pay, // Initial amount
                'account' => $client->email,
                'type' => 'booking',
                'status' => 'pending',
                'currency_code' => $validated['currency_code'] ?? 'USD',
                'currency_rate_to_usd' => $validated['currency_rate_to_usd'] ?? 1.0, // Default to 1.0 if not provided
                'payment_method' => $validated['payment_method'],
                'extra_note' => $validated['extra_note'] ?? null,
                'created_by' => Auth::id(),
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
        $paymentLink = "";
        if ($request->payment_method === 'flutterwave') {
            // 1️⃣ Generate Flutterwave payment link
            $response = $this->makeFlutterwavePaymentLink(
                $booking->amount_to_pay,
                $validated['currency_code'] ?? 'USD', // Currency
                $booking->client->email
            );

            // Convert JsonResponse to array
            $payment = $response->getData(true); // "true" returns an array instead of stdClass

            Log::info("Generated Flutterwave payment link: " . json_encode($payment));

            // Now you can safely access it like an array
            $paymentLink = $payment['payment_link'];

            Log::info("Generated front‑end payment link: {$paymentLink}");


        } elseif ($request->payment_method === 'momo_rwanda') {
            // 2️⃣ Request MTN MoMo payment
            $momo_number = $validated['momo_number'] ?? null;
            if (!$momo_number) {
                Log::error('MTN MoMo phone number is required for MoMo payments');
                return response()->json(['message' => 'MTN MoMo phone number is required'], 400);
            }
            /* $paymentLink = $this->requestMtnMomoPayment(
                $booking->amount_to_pay,
                "FRW", // Assuming EUR for simplicity
                $momo_number
            );
            */
            $paymentLink = "{$frontend}/payment?{$params}";
            Log::info("Generated front‑end payment link: {$paymentLink}");

            Log::info("Requested MTN MoMo payment link: {$paymentLink}");
        } else {
            Log::error('Unsupported payment method', ['method' => $request->payment_method]);
            return response()->json(['message' => 'Unsupported payment method'], 400);
        }

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

    private function makeFlutterwavePaymentLink($amount, $currency, $email)
    {

        $payload = [
            'tx_ref' => Flutterwave::generateTransactionReference(),
            'amount' => $amount,
            'currency' => $currency,
            'customer' => [
                'email' => $email,
            ],
        ];

        // Render the standard modal and get the redirect link
        $paymentLink = Flutterwave::render('standard', $payload);

        return response()->json([
            'status' => 'success',
            'payment_link' => $paymentLink,
        ]);
    }

    private function requestMtnMomoPayment($amount, $currency, $momo_phone)
    {


        try {
            $collection = new Collection();

            $referenceId = $collection->requestToPay(
                (string) Str::uuid(),
                $momo_phone,
                $amount,
                $currency,
                $payerMessage = 'Payment for services',
            );


            return response()->json([
                'status' => 'success',
                'referenceId' => $referenceId,
            ], 200);

        } catch (CollectionRequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate payment request.',
                'errors' => [
                    $e->getMessage(),
                    optional($e->getPrevious())->getMessage(),
                ],
            ], 400);
        }
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

    public function getBookedSeats(int $objectId, string $objectType = 'ticket', )
    {
        return Booking::where('object_type', $objectType)
            ->where('object_id', $objectId)
            ->whereNotNull('seat')
            ->pluck('seat');
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
