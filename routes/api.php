<?php

use Illuminate\Support\Facades\Route;

// Common Controllers
use App\Rest\Controllers\UserController;
use App\Rest\Controllers\AdminController;
use App\Rest\Controllers\AdminManageController;
use App\Rest\Controllers\PhotoController;
use App\Rest\Controllers\PaymentController;
use App\Rest\Controllers\AccountController;
use App\Rest\Controllers\BookingController;
use App\Rest\Controllers\ClientController;
use App\Rest\Controllers\BusTicketController;
use App\Rest\Controllers\AuthController;

// Hotel Module Controllers
use App\Rest\Controllers\HotelController;
use App\Rest\Controllers\RoomController;

// Transport Module Controllers
use App\Rest\Controllers\AgencyController;
use App\Rest\Controllers\TransportRouteController;
use App\Rest\Controllers\SeatTypeController;
use App\Rest\Controllers\JourneyController;
use App\Rest\Controllers\OtpController;
use App\Rest\Controllers\BookingController as TransportBookingController;





// Apartment Module Controller
use App\Rest\Controllers\ApartmentController;

// Retreat Module Controller
use App\Rest\Controllers\RetreatController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// otp verification
Route::post('/send-otp', [OtpController::class, 'send']);
Route::post('/verify-otp', [OtpController::class, 'verify']);
Route::post('/bookings', [BookingController::class, 'store']);

Route::post('/flutterwave/payment', [PaymentController::class, 'makePayment']);
Route::post('/flutterwave/payment/webhook', [PaymentController::class, 'handleWebhook']);
Route::post('/payments/momo/request', [PaymentController::class, 'requestMtnMomoPayment']);




// Common


Route::apiResource('payments', PaymentController::class);

Route::get('/booked-seats/{objectId}', [BookingController::class, 'getBookedSeats']);
Route::post('/booking/ticket', [BookingController::class, 'bookingTicket']);
Route::apiResource('apartments', ApartmentController::class);
Route::get('/hotels/names', [HotelController::class, 'getAllHotelNames']);
Route::apiResource('retreats', RetreatController::class);
Route::get('/seat-types/names', [SeatTypeController::class, 'getAllSeatTypeNames']);
Route::get('/agencies/names', [AgencyController::class, 'getAllAgencyNames']);
// Authentication and Authorization Middleware example refer to this for other role protect routes

Route::middleware(['auth', 'role:admin'])->group(function () {
   Route::apiResource('accounts', AccountController::class);
   Route::apiResource('photos', PhotoController::class);
   Route::apiResource('admins', AdminController::class);
   Route::apiResource('admin-manages', AdminManageController::class);  
   Route::apiResource('bookings', BookingController::class);
   Route::apiResource('clients', ClientController::class);

   Route::apiResource('hotels', HotelController::class);

   Route::apiResource('rooms', RoomController::class);
   Route::apiResource('agencies', AgencyController::class);
   Route::apiResource('routes', TransportRouteController::class); // To avoid conflict with Laravel Route facade
   Route::apiResource('seat-types', SeatTypeController::class);
   Route::apiResource('buses', BusTicketController::class);
   Route::apiResource('journeys', JourneyController::class);
   Route::apiResource('retreats', RetreatController::class);
});

Route::middleware(['auth', 'role:admin,editor'])->group(function () {
   Route::apiResource('retreats', RetreatController::class);
});





