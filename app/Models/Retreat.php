<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retreat extends Model
{
    use HasFactory;

    // Table name (optional if it matches the plural form 'retreats')
    protected $table = 'retreats';

    // Fillable fields for mass assignment
    protected $fillable = [
        'title',
        'description',
        'address',
        'capacity',
        'status',
        'type',
        'wifi',
        'projector',
        'theater',
        'flip_chart',
        'whiteboard',
        'pricing_type',
        'price_per_person',
        'package_price',
        'package_size',
        'updated_by',
        'deleted_by',
        'deleted_on',
        'viewed',
    ];

    // Cast fields as needed
    protected $casts = [
        'wifi' => 'boolean',
        'projector' => 'boolean',
        'theater' => 'boolean',
        'flip_chart' => 'boolean',
        'whiteboard' => 'boolean',
        'capacity' => 'integer',
        'price_per_person' => 'float',
        'package_price' => 'float',
        'package_size' => 'integer',
        'deleted_on' => 'datetime',
        'viewed' => 'integer',
    ];

    // Example relationship: a retreat might have many photos
    public function photos()
    {
        return $this->hasMany(Photo::class, 'object_id')
                    ->where('object_type', 'retreat');
    }
}
