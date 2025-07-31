<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


    /**
     * @property int $order_id
     * @property int $menu_id
     * @property int $quantity
     * @property float $price_unit
     */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'menu_id', 'quantity', 'price_unit',
    ];

    protected $casts = [
        'price_unit' => 'decimal:2',
    ];

    /** @return BelongsTo<Order,OrderItem> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function dish() {
        return $this->belongsTo(Dish::class);
    }

    /** @return BelongsTo<Menu,OrderItem> */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
