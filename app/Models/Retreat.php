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
        'updated_by',
        'deleted_by',
        'deleted_on',
        'viewed',
    ];

    // Cast fields as needed
    protected $casts = [
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
