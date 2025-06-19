<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\Apartment;
use App\Models\Journey;

class AdminBookingScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Only apply when an admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return;
        }
        $admin = Auth::guard('admin')->user();

        // Fetch all AdminManage entries for this admin
        $manages = $admin->manages()->get()->groupBy('object');

        // If admin manages nothing, optionally return no bookings:
        if ($manages->isEmpty()) {
            // Force no results:
            $builder->whereRaw('0 = 1');
            return;
        }

        // Wrap all conditions in a single where(...) closure
        $builder->where(function (Builder $q) use ($manages) {
            // Handle 'hotel' management: manage rooms of a hotel
            if (isset($manages['hotel'])) {
                $hotelIds = $manages['hotel']->pluck('object_id')->toArray();
                // Bookings for rooms in those hotels
                $q->orWhereHasMorph(
                    'object',
                    [Room::class],
                    function (Builder $q2) use ($hotelIds) {
                        $q2->whereIn('hotel_id', $hotelIds);
                    }
                );
                // Also, in case Booking.object_type directly equals 'room',
                // whereHasMorph handles that: Booking.object_type='room' and matches Room::class
                // If your morph mapping uses custom types, ensure morphType matches model.
            }

            // Handle 'apartment' management: manage specific apartments
            if (isset($manages['apartment'])) {
                $apartmentIds = $manages['apartment']->pluck('object_id')->toArray();
                // Bookings on those apartments
                $q->orWhere(function (Builder $q2) use ($apartmentIds) {
                    $q2->where('object_type', 'apartment')
                       ->whereIn('object_id', $apartmentIds);
                });
                // Alternatively, using whereHasMorph:
                // $q->orWhereHasMorph('object', [Apartment::class], function (Builder $q2) use ($apartmentIds) {
                //     $q2->whereIn('id', $apartmentIds);
                // });
            }

            // Handle 'agency' management: manage journeys of an agency
            if (isset($manages['agency'])) {
                $agencyIds = $manages['agency']->pluck('object_id')->toArray();
                // Bookings on journeys whose bus.agency_id is in those agency IDs
                $q->orWhereHasMorph(
                    'object',
                    [Journey::class],
                    function (Builder $q2) use ($agencyIds) {
                        $q2->whereHas('bus', function (Builder $q3) use ($agencyIds) {
                            $q3->whereIn('agency_id', $agencyIds);
                        });
                    }
                );
            }

            // If you have other object types or direct booking on journeys by ID:
            // For example, if admin_manage might include 'journey' entries directly:
            if (isset($manages['journey'])) {
                $journeyIds = $manages['journey']->pluck('object_id')->toArray();
                $q->orWhere(function (Builder $q2) use ($journeyIds) {
                    $q2->where('object_type', 'journey')
                       ->whereIn('object_id', $journeyIds);
                });
            }

            // Add more cases if needed...
        });
    }
}
