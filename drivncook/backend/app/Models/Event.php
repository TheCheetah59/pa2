<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
    ];

    // Un événement peut être lié à plusieurs clients
    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }
}
