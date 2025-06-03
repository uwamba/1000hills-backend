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

Route::apiResource('payments', PaymentController::class);
Route::get('/booked-seats/{objectId}', [BookingController::class, 'getBookedSeats']);
Route::post('/booking/ticket', [BookingController::class, 'bookingTicket']);
Route::get('/hotels/names', [HotelController::class, 'getAllHotelNames']);
Route::apiResource('retreats', RetreatController::class);
Route::get('/seat-types/names', [SeatTypeController::class, 'getAllSeatTypeNames']);
Route::get('/agencies/names', [AgencyController::class, 'getAllAgencyNames']);


// ✅ Public routes (GET only)
Route::get('/accounts', AccountController::class)->only(['index', 'show']);
Route::get('/photos', PhotoController::class)->only(['index', 'show']);
Route::get('/admins', AdminController::class)->only(['index', 'show']);
Route::get('/admin-manages', AdminManageController::class)->only(['index', 'show']);
Route::get('/bookings', BookingController::class)->only(['index', 'show']);
Route::get('/clients', ClientController::class)->only(['index', 'show']);
Route::get('/hotels', HotelController::class)->only(['index', 'show']);
Route::get('/rooms', RoomController::class)->only(['index', 'show']);
Route::get('/agencies', AgencyController::class)->only(['index', 'show']);
Route::get('/routes', TransportRouteController::class)->only(['index', 'show']);
Route::get('/seat-types', SeatTypeController::class)->only(['index', 'show']);
Route::get('/buses', BusTicketController::class)->only(['index', 'show']);
Route::get('/journeys', JourneyController::class)->only(['index', 'show']);
Route::get('/retreats', RetreatController::class)->only(['index', 'show']);
Route::get('/apartments', ApartmentController::class)->only(['index', 'show']);


// 🔒 Authenticated routes (POST, PUT, DELETE)
Route::middleware('auth:api')->group(function () {
    Route::apiResource('accounts', AccountController::class)->except(['index', 'show']);
    Route::apiResource('photos', PhotoController::class)->except(['index', 'show']);
    Route::apiResource('admins', AdminController::class)->except(['index', 'show']);
    Route::apiResource('admin-manages', AdminManageController::class)->except(['index', 'show']);
    Route::apiResource('bookings', BookingController::class)->except(['index', 'show']);
    Route::apiResource('clients', ClientController::class)->except(['index', 'show']);
    Route::apiResource('hotels', HotelController::class)->except(['index', 'show']);
    Route::apiResource('rooms', RoomController::class)->except(['index', 'show']);
    Route::apiResource('agencies', AgencyController::class)->except(['index', 'show']);
    Route::apiResource('routes', TransportRouteController::class)->except(['index', 'show']);
    Route::apiResource('seat-types', SeatTypeController::class)->except(['index', 'show']);
    Route::apiResource('buses', BusTicketController::class)->except(['index', 'show']);
    Route::apiResource('journeys', JourneyController::class)->except(['index', 'show']);
    Route::apiResource('retreats', RetreatController::class)->except(['index', 'show']);
    Route::apiResource('apartments', ApartmentController::class)->except(['index', 'show']);
});


Route::middleware(['auth', 'role:admin,editor'])->group(function () {
   Route::apiResource('retreats', RetreatController::class);
});





