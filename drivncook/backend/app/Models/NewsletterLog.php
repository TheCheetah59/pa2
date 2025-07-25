<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'sent_at', 'subject', 'content',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /** @return BelongsTo<Customer,NewsletterLog> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
