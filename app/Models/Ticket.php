<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'price',
        'client_id',
        'booking_id',
        'seat',
        'bus_id',
    ];
}

