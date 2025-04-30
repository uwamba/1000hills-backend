<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'path', 'object_type', 'object_id' ,'status',        // New field
    'updated_by',    // New field
    'deleted_by',    // New field
    'deleted_on',
];

    public function object()
    {
        return $this->morphTo();
    }
}
