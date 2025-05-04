<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    // Table name (optional if it matches the plural form of the model name)
    protected $table = 'hotels';

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'address',
        'coordinate',
        'description',
        'stars',
        'working_time',
        'status',        // New field
        'updated_by',    // New field
        'deleted_by',    // New field
        'deleted_on',
    ];

    // Cast the coordinate field to an array or object (if needed)
    protected $casts = [
        'coordinate' => 'array', // Assuming it stores lat/long as JSON
    ];

    // Optionally, you can define relationships here if needed
    // Example: hotel has many rooms
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function photos()
{
    return $this->hasMany(Photo::class, 'object_id')
        ->where('object_type', 'hotel');
}

}
