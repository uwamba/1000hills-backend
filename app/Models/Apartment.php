<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\AdminApartmentScope;
use App\Models\ApartmentOwner;

class Apartment extends Model
{
    protected $fillable = [
        'name',
        'number_of_bedroom',
        'kitchen_inside',
        'kitchen_outside',
        'number_of_floor',
        'address',
        'coordinate',
        'annexes',
        'description',
        'status',
        'swimming_pool',
        'laundry',
        'gym',
        'price_per_night',
        'price_per_month',
        'room_service',
        'sauna_massage',
        'contract',
        'view',
        'updated_by',
        'deleted_by',
        'deleted_on',
        'apartment_owner_id',
    ];

    protected $casts = [
        'coordinate' => 'array', // assuming JSON format
        'kitchen_inside' => 'boolean',
        'kitchen_outside' => 'boolean',
        'swimming_pool' => 'boolean',
        'laundry' => 'boolean',
        'gym' => 'boolean',
        'room_service' => 'boolean',
        'sauna_massage' => 'boolean',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class, 'object_id')
                    ->where('object_type', 'apartment');
    }
     public function bookings()
    {
        return $this->morphMany(Booking::class, 'object')
            ->where('object_type', 'apartment');
    }
    public function apartemntOwner()
    {
        return $this->belongsTo(ApartmentOwner::class, 'apartment_owner_id');
        // By default, Laravel will look for apartment_owner_id, so the second argument is optional
    }

    protected static function booted()
    {
        static::addGlobalScope(new AdminApartmentScope);
    }
    public function activeBookings()
{
    return $this->morphMany(Booking::class, 'object')
        ->where('object_type', 'apartment')
        ->whereNull('deleted_on'); // Adjust status as per your system
}

}
