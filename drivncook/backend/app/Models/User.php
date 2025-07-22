<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'franchisee_id',
    ];

    /**
     * Les attributs cachés lors de la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs castés automatiquement.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation : un user franchisé appartient à un franchisé.
     */
    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class);
    }
}
