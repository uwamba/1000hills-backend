<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    // Optional: Specify the table name if it's different from the default 'agencies'
    protected $table = 'agencies';

    // Fields that are mass assignable
    protected $fillable = [
        'name',
        'address',
        'description',
    ];
}
