<?php
// Dans App\Models\Customer.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens; // si vous utilisez Sanctum

class Customer extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relation avec les commandes
    public function orders()
    {
        return $this->hasMany(CustomerOrder::class, 'customer_id');
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
