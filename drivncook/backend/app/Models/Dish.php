<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    // Un plat appartient à un menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Un plat peut apparaître dans plusieurs lignes de commande
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
