<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // Un menu contient plusieurs plats
    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }
}
