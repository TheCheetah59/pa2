<?php
// app/Models/Supply.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $fillable = [
        'franchisee_id',
        'warehouse_id',
        'product_name',   // ex: "Pain burger"
        'quantity',       // entier
        'source_type',    // 'obligatoire' | 'libre'
        'unit_price',     // décimal(10,2)
        'notes',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
    ];

    protected $appends = ['total_value'];

    // Accessor pratique: quantité * prix unitaire
    public function getTotalValueAttribute(): string
    {
        return number_format(($this->quantity ?? 0) * ($this->unit_price ?? 0), 2, '.', '');
    }

    // Relations
    public function franchisee() { return $this->belongsTo(Franchisee::class); }
    public function warehouse()  { return $this->belongsTo(Warehouse::class); }

    // Scopes lisibles
    public function scopeForFranchisee($q, int $id) { return $q->where('franchisee_id', $id); }
    public function scopeObligatory($q) { return $q->where('source_type', 'obligatoire'); }
    public function scopeFree($q) { return $q->where('source_type', 'libre'); }
}
