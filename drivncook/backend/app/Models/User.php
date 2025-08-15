<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CLIENT = 'client';
    public const ROLE_FRANCHISE = 'franchise';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'franchisee_id',
        'is_activated',
        'activation_token',
        'activation_token_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
        'activation_token_expires_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_activated' => 'boolean',
        'activation_token_expires_at' => 'datetime',
    ];

    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class);
    }
}