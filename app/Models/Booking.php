<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [   'from_date_time',
    'to_date_time',
    'object_type',
    'object_id',
    'client_id',
    'seat',
    'amount_to_pay',
    'status',        // New field
    'updated_by',    // New field
    'deleted_by',    // New field
    'deleted_on',];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function object()
    {
        return $this->morphTo();
    }

    // Polymorphic relation to Room or other objects
    

    // Direct relation if object_type is 'room'
   public function room()
{
    return $this->belongsTo(Room::class, 'object_id');
}






    /**
     * Scope: room bookings accessible to the authenticated admin,
     * based on admin_manage entries of type 'hotel' or 'room'.
     *
     * - If admin manages specific rooms (object='room'), include those.
     * - If admin manages hotels (object='hotel'), include all rooms in those hotels.
     * - If not authenticated as admin, this scope does nothing (returns forRooms only),
     *   or you can adjust to return none or all.
     */
    public function scopeForAdminRooms($query)
    {
        // First apply object_type = 'room'
        $query->where('object_type', 'room');

        // Check admin guard
        if (!Auth::guard('admin')->check()) {
            // Not an admin: we leave it as plain forRooms()
            // Alternatively, you could force no results:
            // $query->whereRaw('0 = 1');
            return $query;
        }

        $admin = Auth::guard('admin')->user();

        // Fetch admin_manage entries for 'room' and 'hotel'
        $manages = $admin->manages()->whereIn('object', ['hotel'])->get()->groupBy('object');

        // If admin manages nothing relevant, force no results (optional); or skip further filtering:
        if ($manages->isEmpty()) {
            // Uncomment to force empty:
            // return $query->whereRaw('0 = 1');
            // Otherwise, return only object_type filter (all room bookings)
            return $query;
        }

        // Wrap additional conditions in a nested where to combine with object_type
        $query->where(function ($q) use ($manages) {
            
            // Hotel management: admin_manage.object='hotel'
            if (isset($manages['hotel'])) {
                $hotelIds = $manages['hotel']->pluck('object_id')->toArray();
                if (!empty($hotelIds)) {
                    // Need to include bookings where object_id refers to a Room whose hotel_id is in $hotelIds.
                    // We'll use whereHasMorph on the 'object' relation to filter Room.hoteI_id:
                    $q->orWhereHasMorph(
                        'object',
                        [Room::class],
                        function ($q2) use ($hotelIds) {
                            $q2->whereIn('hotel_id', $hotelIds);
                        }
                    );
                }
            }
        });

        return $query;
    }

    public function scopeForAdminApartments($query)
    {
        // First apply object_type = 'room'
        $query->where('object_type', 'apartment');

        // Check admin guard
        if (!Auth::guard('admin')->check()) {
            // Not an admin: we leave it as plain forRooms()
            // Alternatively, you could force no results:
            // $query->whereRaw('0 = 1');
            return $query;
        }

        $admin = Auth::guard('admin')->user();

        // Fetch admin_manage entries for 'room' and 'hotel'
        $manages = $admin->manages()->whereIn('object', ['apartment'])->get()->groupBy('object');

        // If admin manages nothing relevant, force no results (optional); or skip further filtering:
        if ($manages->isEmpty()) {
            // Uncomment to force empty:
            // return $query->whereRaw('0 = 1');
            // Otherwise, return only object_type filter (all room bookings)
            return $query;
        }

        // Wrap additional conditions in a nested where to combine with object_type
        $query->where(function ($q) use ($manages) {
            

            if (isset($manages['apartment'])) {
                $aprtIds = $manages['apartment']->pluck('object_id')->toArray();
                if (!empty($aprtIds)) {
                    $q->orWhereHasMorph(
                        'object',
                        [Apartment::class],
                        function ($q2) use ($aprtIds) {
                            $q2->whereIn('apartment_owner_id', $aprtIds);
                        }
                    );
                }
            }
        });

        return $query;
    }
}
