<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
// Un événement peut être lié à plusieurs clients
   public function customers()
{
    return $this->belongsToMany(Customer::class);
}

}
