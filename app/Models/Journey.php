<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journey extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'time',
        'status',        // New field
        'updated_by',    // New field
        'deleted_by',    // New field
        'deleted_on',
    ];

    /**
     * Get the route associated with the journey.
     */
    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }
}