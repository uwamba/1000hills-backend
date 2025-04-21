<?php

// app/Models/Apartment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];
}
