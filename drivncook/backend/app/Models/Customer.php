<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
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
