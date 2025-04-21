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
        'hotel_id',
    ];

    // Cast the price to float and other necessary types
    protected $casts = [
        'price' => 'float',
        'has_wireless' => 'boolean',
        'has_bathroom' => 'boolean',
        'has_ac' => 'boolean',
    ];

    // Optionally, you can define relationships here if needed
    // Example: room belongs to a hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
