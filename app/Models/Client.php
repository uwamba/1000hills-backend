<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

 
class Client extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'names',
        'email',
        'password',
        'address',
        'country',
        'phone',
        'status',       // New field
        'updated_by',   // New field
        'deleted_by',   // New field
        'deleted_on',   // New field
        
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

