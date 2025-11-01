<?php

namespace App\Models;

use App\Scopes\AdminAgencyJourneyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journey extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'from',
        'to',
        'departure',
        'return',
        'bus_id',
        'price',
        'currency',
        'status',
        'time',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_on',
    ];

    protected $casts = [
        'departure' => 'datetime',
        'return' => 'datetime',
        'deleted_on' => 'datetime',
    ];

    /**
     * Get the route associated with the journey.
     */

    /**
     * Get the bus assigned to the journey.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function exchangeRate()
    {
        return $this->belongsTo(ExchangeRate::class, 'currency', 'currency_code');
    }

    /**
     * User who last updated this record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // In Bus.php
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'object_id')->where('object_type', 'ticket');
    }

    /**
     * User who deleted this record.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function booted()
    {
        static::addGlobalScope(new AdminAgencyJourneyScope);
    }

    public function tickets()
    {
        return $this->hasManyThrough(Ticket::class, Booking::class, 'object_id', 'booking_id')
            ->where('bookings.object_type', 'journey');
    }
}
