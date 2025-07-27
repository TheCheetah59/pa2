<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Franchisee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'city', 'postal_code', 
        'country', 'siret_number', 'franchise_code', 'entry_fee_paid', 
        'sales_percentage'
    ];
    protected $hidden = ['password'];

    protected $casts = [
        'joined_at' => 'datetime',
        'active' => 'boolean',
    ];

    /** @return HasMany<Truck> */
    public function trucks(): HasMany
    {
        return $this->hasMany(Truck::class);
    }

    /** @return HasMany<StockOrder> */
    public function stockOrders(): HasMany
    {
        return $this->hasMany(StockOrder::class);
    }
}
