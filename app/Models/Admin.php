<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = ['names', 'email', 'address', 'phone', 'role'];

    public function manages()
    {
        return $this->hasMany(AdminManage::class);
    }
}

