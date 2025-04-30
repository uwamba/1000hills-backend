<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [   'from_date_time',
    'to_date_time',
    'object_type',
    'object_id',
    'client_id',
    'amount_to_pay',
    'status',        // New field
    'updated_by',    // New field
    'deleted_by',    // New field
    'deleted_on',];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function object()
    {
        return $this->morphTo();
    }
}
