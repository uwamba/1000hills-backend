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
        'status',
        'updated_by',
        'deleted_by',
        'deleted_on',
    ];
    // App\Models\Agency.php

public function updatedBy()
{
    return $this->belongsTo(\App\Models\User::class, 'updated_by');
}

public function deletedBy()
{
    return $this->belongsTo(\App\Models\User::class, 'deleted_by');
}

}
