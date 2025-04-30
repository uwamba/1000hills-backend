<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Table name (optional if it matches the plural form of the model name)
    protected $table = 'rooms';

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'type',
        'has_wireless',
        'bed_size',
        'has_bathroom',
        'price',
        'currency',
        'number_of_people',
        'has_ac',
        'hotel_id', 'status',        // New field
        'updated_by',    // New field
        'deleted_by',    // New field
        'deleted_on',    // New field
    ];

    protected $casts = [
        'price' => 'float',
        'has_wireless' => 'boolean',
        'has_bathroom' => 'boolean',
        'has_ac' => 'boolean',
        'deleted_on' => 'datetime',  // Cast deleted_on to datetime
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    // (Optional) Relationships to users for updated_by and deleted_by
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}