<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TruckMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'truck_id',
        'date',
        'description',
        'type',   // NEW
        'notes',  // NEW
        'cost',   // NEW
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'float',
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
}
