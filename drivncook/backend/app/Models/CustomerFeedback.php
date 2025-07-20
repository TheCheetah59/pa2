<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    public function order()
{
    return $this->belongsTo(Order::class);
}

}
