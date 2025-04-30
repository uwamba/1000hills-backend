<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'transaction_id', 'amount_paid', 'account', 'type', 'status'   , 'updated_by',    // New field
    'deleted_by',    // New field
    'deleted_on',
];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

