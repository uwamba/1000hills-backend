<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'from',
        'to',
        'price',
        'status',        // New field
        'updated_by',    // New field
        'deleted_by',    // New field
        'deleted_on',
    ];

    /**
     * Get the journeys associated with the transport route.
     */
    public function journeys()
    {
        return $this->hasMany(Journey::class, 'route_id');
    }
}

