<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Truck extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchisee_id',
        'plate_number',      
        'model',             
        'current_location', 
        'status',
        'last_service_date', 
        'next_service_due',  
        'notes',             
    ];

    /** @return BelongsTo<Franchisee,Truck> */
    public function franchisee(): BelongsTo
    {
        return $this->belongsTo(Franchisee::class);
    }

    /** @return HasMany<TruckMaintenance> */
    public function maintenances(): HasMany
    {
        return $this->hasMany(TruckMaintenance::class);
    }
}