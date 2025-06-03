<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Role; // Ensure you have the Role model imported


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Assuming you have a role_id field in your users table
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
{
    return $this->belongsTo(Role::class);
}

    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role && $this->role->name === 'user';
    }
    public function isGuest(): bool
    {
        return !$this->role || $this->role->name === 'guest';
    }
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->name === 'super_admin';  
    }
    public function isModerator(): bool
    {
        return $this->role && $this->role->name === 'moderator';
    }
}
