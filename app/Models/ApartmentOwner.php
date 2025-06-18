<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Apartment; // Ensure you import the Apartment model if it's in a different namespace


class ApartmentOwner extends Model
{
    use HasFactory;

    protected $table = 'apartment_owners';

    protected $fillable = [
        'name',
        'address',
        'contract_path',
        'status',
        'created_by',
        'updated_by',
    ];

    // If you want to cast any fields:
    protected $casts = [
        // e.g., if you treat status as enum or something, but string is fine
    ];

    /**
     * The user who created this record.
     */
    public function creator()
    {
        // Adjust User::class if your user model is in a different namespace
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * The user who last updated this record.
     */
    public function editor()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'apartment_owner_id');
        // foreign key is apartment_owner_id in apartments table
    }
}
