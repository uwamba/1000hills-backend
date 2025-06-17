<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'name',
        'type',
        'has_wireless',
        'bed_size',
        'view',
        'has_bathroom',
        'price',
        'currency',
        'number_of_people',
        'has_ac',
        'hotel_id',
        'status',

        // New boolean fields for features
        'has_swimming_pool',
        'has_laundry',
        'has_gym',
        'has_room_service',
        'has_sauna_massage',
        'has_kitchen',
        'has_fridge',

        // System fields
        'updated_by',
        'deleted_by',
        'deleted_on',
    ];

    protected $casts = [
        'price' => 'float',
        'has_wireless' => 'boolean',
        'has_bathroom' => 'boolean',
        'has_ac' => 'boolean',
        'has_swimming_pool' => 'boolean',
        'has_laundry' => 'boolean',
        'has_gym' => 'boolean',
        'has_room_service' => 'boolean',
        'has_sauna_massage' => 'boolean',
        'has_kitchen' => 'boolean',
        'has_fridge' => 'boolean',
        'deleted_on' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

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
            ->where('object_type', 'room');
    }

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'object')
            ->where('object_type', 'room');
    }
    

}
