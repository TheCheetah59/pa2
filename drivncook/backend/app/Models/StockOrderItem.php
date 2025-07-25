<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_order_id', 'stock_item_id', 'quantity', 'price_unit',
    ];

    protected $casts = [
        'price_unit' => 'decimal:2',
    ];

    /** @return BelongsTo<StockOrder,StockOrderItem> */
    public function stockOrder(): BelongsTo
    {
        return $this->belongsTo(StockOrder::class);
    }

    /** @return BelongsTo<StockItem,StockOrderItem> */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }
}
