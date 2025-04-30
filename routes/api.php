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

// Hotel Module Controllers
use App\Rest\Controllers\HotelController;
use App\Rest\Controllers\RoomController;

// Transport Module Controllers
use App\Rest\Controllers\AgencyController;
use App\Rest\Controllers\TransportRouteController;
use App\Rest\Controllers\SeatTypeController;
use App\Rest\Controllers\BusController;
use App\Rest\Controllers\JourneyController;

// Apartment Module Controller
use App\Rest\Controllers\ApartmentController;

// Common
Route::apiResource('admins', AdminController::class);
Route::apiResource('admin-manages', AdminManageController::class);
Route::apiResource('photos', PhotoController::class);
Route::apiResource('payments', PaymentController::class);
Route::apiResource('accounts', AccountController::class);
Route::apiResource('bookings', BookingController::class);
Route::apiResource('clients', ClientController::class);


// Hotels
Route::apiResource('hotels', HotelController::class);
Route::apiResource('rooms', RoomController::class);

// Transport
Route::apiResource('agencies', AgencyController::class);
Route::apiResource('routes', TransportRouteController::class); // To avoid conflict with Laravel Route facade
Route::apiResource('seat-types', SeatTypeController::class);
Route::apiResource('buses', BusController::class);
Route::apiResource('journeys', JourneyController::class);

// Apartments
Route::apiResource('apartments', ApartmentController::class);
