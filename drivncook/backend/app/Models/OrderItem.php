<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    // Une ligne de commande appartient à une commande
    public function order()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    // Une ligne de commande est liée à un plat
    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
}
