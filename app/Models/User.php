<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_ADMIN   = 'admin';
    public const ROLE_PETUGAS = 'petugas';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool   { return $this->role === self::ROLE_ADMIN; }
    public function isPetugas(): bool { return $this->role === self::ROLE_PETUGAS; }
}
