<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PROCESSING = 'processing';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    protected $fillable = [
        'customer_id', 'total_price', 'status',
        'payment_status', 'payment_intent_id',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    /** @return BelongsTo<Customer,Order> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return HasMany<OrderItem> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return BelongsToMany<Menu> */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'order_items')
            ->withPivot(['quantity', 'price_unit'])
            ->withTimestamps();
    }
}
