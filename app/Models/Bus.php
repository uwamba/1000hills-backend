<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'seat_type',
        'number_of_seat',
        'agency_id',
        'status',        // New field
        'updated_by',    // New field
        'deleted_by',    // New field
        'deleted_on',
    ];

    /**
     * Define relationship to the Agency
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Optional: Define relationship to seat type if it’s a separate model.
     * Assuming 'seat_type' is a foreign key referencing seat_types table
     */
    public function seatType()
    {
        return $this->belongsTo(SeatType::class, 'seat_type');
    }
}

