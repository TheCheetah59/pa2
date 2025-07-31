<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TruckMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'truck_id', 'date', 'description',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    /** @return BelongsTo<Truck,TruckMaintenance> */
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

}
