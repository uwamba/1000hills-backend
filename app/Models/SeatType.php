<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatType extends Model
{
    use HasFactory;

    protected $fillable = [
        'row',
        'column',
        'name',
        'seat_row',
        'seat_column',
        'exclude',
        'status',        // New field
        'updated_by',    // New field
        'deleted_by',    // New field
        'deleted_on',
    ];

    protected $casts = [
        'exclude' => 'array',
    ];
}
