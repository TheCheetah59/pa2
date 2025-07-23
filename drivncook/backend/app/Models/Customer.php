<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property \Illuminate\Database\Eloquent\Collection $orders
 * @property \App\Models\LoyaltyCard|null $loyaltyCard
 * @property \Illuminate\Database\Eloquent\Collection $events
 */
class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Les champs remplissables en masse
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * Champs cachés dans les retours JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Un client peut passer plusieurs commandes
    public function orders()
    {
        return $this->hasMany(CustomerOrder::class);
    }

    // Un client possède une carte de fidélité
    public function loyaltyCard()
    {
        return $this->hasOne(LoyaltyCard::class);
    }

    // Un client peut participer à plusieurs événements
    public function events()
    {
        return $this->belongsToMany(Event::class);
    }
}
