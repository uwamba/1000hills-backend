<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['from_date_time', 'to_date_time', 'object_type', 'object_id', 'client_id', 'amount_to_pay'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function object()
    {
        return $this->morphTo();
    }
}
