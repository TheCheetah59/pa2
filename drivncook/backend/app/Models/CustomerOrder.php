<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    // Une commande appartient à un client
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Une commande contient plusieurs lignes de commande
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
