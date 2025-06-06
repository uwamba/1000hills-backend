<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminManage extends Model
{
    use HasFactory;

    protected $table = 'admin_manage';

    protected $fillable = ['admin_id', 'object_type', 'object_id', 'role_id'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function object()
    {
        return $this->morphTo();
    }

    
}

