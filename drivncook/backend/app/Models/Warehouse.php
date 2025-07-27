<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'postal_code',
        'city',
        'region',
        'phone',
        'manager_name',
        'email',
        'capacity',
        'kitchen_available'
    ];

    /** @return HasMany<StockItem> */
    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    /** @return HasMany<StockOrder> */
    public function stockOrders(): HasMany
    {
        return $this->hasMany(StockOrder::class);
    }
}
