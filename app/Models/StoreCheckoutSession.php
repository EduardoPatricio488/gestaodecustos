<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreCheckoutSession extends Model
{
    protected $fillable = [
        'user_id', 'items', 'discount_amount', 'coupon_code',
        'add_expense_to_education', 'status', 'stripe_session_id',
    ];

    protected $casts = [
        'items' => 'array',
        'discount_amount' => 'decimal:2',
        'add_expense_to_education' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
