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
use App\Rest\Controllers\ApartmentOwnerController;
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
use App\Rest\Controllers\DashboardController;

use App\Rest\Controllers\ContactController;
use App\Rest\Controllers\ExchangeRateController;
use App\Rest\Controllers\FlutterwaveController;



// Apartment Module Controller
use App\Rest\Controllers\ApartmentController;

// Retreat Module Controller
use App\Rest\Controllers\RetreatController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);




Route::post('/contact', [ContactController::class, 'store']);

// otp verification
Route::post('/send-otp', [OtpController::class, 'send']);
Route::post('/verify-otp', [OtpController::class, 'verify']);
Route::post('/bookings', [BookingController::class, 'store']);

Route::post('/flutterwave/payment', [PaymentController::class, 'makePayment']);
Route::post('/flutterwave/payment/webhook', [PaymentController::class, 'handleWebhook']);
Route::post('/payments/momo/request', [PaymentController::class, 'requestMtnMomoPayment']);

Route::post('/flutterwave/verify', [FlutterwaveController::class, 'verifyPayment']);


// Common
Route::get('/payments/momo/{ref}/status', [PaymentController::class, 'checkMomoStatus']);
Route::get('/payments/flutterwave/{id}/status', [PaymentController::class, 'checkFlutterwaveStatus']);
Route::get('/payments/{payment}/status', [PaymentController::class, 'checkStatus']);


Route::apiResource('payments', PaymentController::class);

Route::get('/booked-seats/{objectId}', [BookingController::class, 'getBookedSeats']);
Route::post('/booking/ticket', [BookingController::class, 'bookingTicket']);

Route::get('/hotels/names', [HotelController::class, 'getAllHotelNames']);
Route::get('/download-contract/{id}', [HotelController::class, 'download']);
Route::get('/apartment-owners/names', [ApartmentOwnerController::class, 'getAllOwnerNames']);
Route::apiResource('retreats', RetreatController::class);
Route::get('/seat-types/names', [SeatTypeController::class, 'getAllSeatTypeNames']);
Route::get('/agencies/names', [AgencyController::class, 'getAllAgencyNames']);
Route::get('/apartments/names', [ApartmentController::class, 'getAllApartmentNames']);


// example of client endpaoint

Route::get('/client/rooms', [RoomController::class, 'roomList']);
Route::get('/client/rooms/{roomId}', [RoomController::class, 'roomDetails']);

Route::get('/client/apartments', [ApartmentController::class, 'apartmentList']);
Route::get('/client/retreats', [RetreatController::class, 'retreatList']);
Route::get('/client/journeys', [JourneyController::class, 'journeyList']);

Route::get('/client/featured-rooms', [RoomController::class, 'featuredRoomList']);
Route::get('/client/featured-events', [RetreatController::class, 'featuredEventList']);
Route::get('/client/featured-apartments', [ApartmentController::class, 'featuredApartemntList']);
Route::get('/client/featured-journeys', [JourneyController::class, 'featuredJourneyList']);
Route::get('/client/exchange-rates', [ExchangeRateController::class, 'index']);


// Authentication and Authorization Middleware example refer to this for other role protect routes
Route::middleware('authAny')->group(function () {




Route::get('/exchange-rates', [ExchangeRateController::class, 'index']);
Route::post('/exchange-rates', [ExchangeRateController::class, 'storeOrUpdate']);
Route::delete('/exchange-rates/{code}', [ExchangeRateController::class, 'destroy']);


   // routes/api.php
   Route::get('/admin/dashboard/stats', [DashboardController::class, 'getStatistics']);


   Route::get('/room-bookings', [BookingController::class, 'roomBookings']);
   Route::get('/apartment-bookings', [BookingController::class, 'apartmentBookings']);
   Route::get('/ticket-bookings', [BookingController::class, 'ticketBookings']);

   Route::apiResource('apartments', ApartmentController::class);
   Route::apiResource('accounts', AccountController::class);
   Route::apiResource('photos', PhotoController::class);
   Route::apiResource('admin-manages', AdminManageController::class);

   Route::apiResource('clients', ClientController::class);
   Route::apiResource('hotels', HotelController::class);


   Route::get('/journeys-with-seats', [JourneyController::class, 'journeyListWithSeats']);



   Route::apiResource('apartment-owners', ApartmentOwnerController::class);

   Route::apiResource('rooms', RoomController::class);
   Route::apiResource('agencies', AgencyController::class);
   Route::apiResource('routes', TransportRouteController::class); // To avoid conflict with Laravel Route facade
   Route::apiResource('seat-types', SeatTypeController::class);
   Route::apiResource('buses', BusTicketController::class);
   Route::apiResource('journeys', JourneyController::class);
   Route::apiResource('retreats', RetreatController::class);
});

Route::middleware(['auth', 'role:admin,editor'])->group(function () {
   Route::apiResource('retreatstest', RetreatController::class);
});


Route::prefix('admin')->group(function () {
   Route::post('/login', [AdminController::class, 'login']);
   Route::post('/', [AdminController::class, 'store']); // Add new
   Route::get('/', [AdminController::class, 'index']); // List all
   Route::put('/{id}', [AdminController::class, 'update']);
   Route::delete('/{id}', [AdminController::class, 'destroy']);
   Route::put('/{id}/reset-password', [AdminController::class, 'resetPassword']);
   
   Route::post('/{id}/activate', [AdminController::class, 'activate']);
   Route::post('/{id}/deactivate', [AdminController::class, 'deactivate']);
});


Route::apiResource('bookings', BookingController::class);



