<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journey extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'from',
        'to',
        'departure',
        'return',
        'bus_id',
        'status',
        'time',
        'updated_by',
        'deleted_by',
        'deleted_on',
    ];

    protected $casts = [
        'departure' => 'datetime',
        'return' => 'datetime',
        'deleted_on' => 'datetime',
    ];

    /**
     * Get the route associated with the journey.
     */


    /**
     * Get the bus assigned to the journey.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    /**
     * User who last updated this record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    // In Bus.php
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }


    /**
     * User who deleted this record.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
