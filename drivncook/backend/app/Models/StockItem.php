<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id', 'name', 'unit_price', 'stock_quantity', 'category',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    /** @return BelongsTo<Warehouse,StockItem> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return HasMany<StockOrderItem> */
    public function stockOrderItems(): HasMany
    {
        return $this->hasMany(StockOrderItem::class);
    }
}
