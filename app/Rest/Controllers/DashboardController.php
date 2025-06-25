<?php

namespace App\Rest\Controllers;
use App\Rest\Controller as RestController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Admin, Client, Booking, Apartment, Hotel, Room, Journey, Bus, Payment,Agency};
class DashboardController extends RestController
{
    public function getStatistics(Request $request)
    {
         $now = now();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();

        return response()->json([
            'total_admins' => Admin::count(),
            'total_clients' => Client::count(),
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'apartments' => Apartment::count(),
            'hotels' => Hotel::count(),
            'rooms' => Room::count(),
            'journeys' => Journey::count(),
            'buses' => Bus::count(),
            'total_payments' => Payment::sum('amount_paid'),
            'agencies' => Agency::count(),
            'clients' => Client::count(),
            'bookings' => Booking::count(),
            'bookings_week' => Booking::where('created_at', '>=', $startOfWeek)->count(),
            'bookings_month' => Booking::where('created_at', '>=', $startOfMonth)->count(),
            'payments_total' => Payment::sum('amount_paid'),
        ]);
    }

   

}
