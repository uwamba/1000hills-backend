<?php

namespace App\Rest\Controllers;
use App\Rest\Controller as RestController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Admin, Client, Booking, Apartment, Hotel, Room, Journey, Bus, Payment};
class DashboardController extends RestController

{
    public function getStatistics(Request $request)
    {
        return response()->json([
            'total_admins'      => Admin::count(),
            'total_clients'     => Client::count(),
            'total_bookings'    => Booking::count(),
            'active_bookings'   => Booking::where('status', 'active')->count(),
            'cancelled_bookings'=> Booking::where('status', 'cancelled')->count(),
            'apartments'        => Apartment::count(),
            'hotels'            => Hotel::count(),
            'rooms'             => Room::count(),
            'journeys'          => Journey::count(),
            'buses'             => Bus::count(),
            'total_payments'    => Payment::sum('amount_paid'),
        ]);
    }
}
