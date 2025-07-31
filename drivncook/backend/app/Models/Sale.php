<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
        protected $fillable = [
        'franchisee_id',
        'sale_date',
        'product_name',
        'quantity_sold',
        'unit_price',
        'total_price',
        'payment_method',
        'location',
        'notes',
    ];

    protected $casts = [
        'sale_date'   => 'datetime',
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /** @return BelongsTo<Franchisee,Sale> */
    public function franchisee(): BelongsTo
    {
        return $this->belongsTo(Franchisee::class);
    }
}
