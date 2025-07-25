<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchisee_id', 'warehouse_id', 'total_price',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    /** @return BelongsTo<Franchisee,StockOrder> */
    public function franchisee(): BelongsTo
    {
        return $this->belongsTo(Franchisee::class);
    }

    /** @return BelongsTo<Warehouse,StockOrder> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return HasMany<StockOrderItem> */
    public function items(): HasMany
    {
        return $this->hasMany(StockOrderItem::class);
    }
}
