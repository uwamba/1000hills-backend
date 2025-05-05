<?php

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
        'status',
        'updated_by',
        'deleted_by',
        'deleted_on',
    ];

    protected $casts = [
        'coordinate' => 'array', // assuming JSON format
        'kitchen_inside' => 'boolean',
        'kitchen_outside' => 'boolean',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class, 'object_id')
                    ->where('object_type', 'apartment');
    }
}
