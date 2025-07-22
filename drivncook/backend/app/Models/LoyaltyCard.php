<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
    // Une carte de fidélité appartient à un client
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
