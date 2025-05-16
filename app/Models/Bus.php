<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'seat_type_id',
        'number_of_seat',
        'agency_id',
        'status',
        'updated_by',
        'deleted_by',
        'deleted_on',
    ];

    protected $casts = [
        'deleted_on' => 'datetime',
    ];

    /**
     * Define relationship to the agency
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Define relationship to seat type
     */
    // In Bus.php
    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }
   


    /**
     * User who last updated this record
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * User who deleted this record
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
