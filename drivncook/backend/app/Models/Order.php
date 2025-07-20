<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    public function feedback()
{
    return $this->hasOne(CustomerFeedback::class);
}

}
