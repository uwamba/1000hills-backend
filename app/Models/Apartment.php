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
        'status',           // New field
        'updated_by',       // New field
        'deleted_by',       // New field
        'deleted_on',
    ];
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
