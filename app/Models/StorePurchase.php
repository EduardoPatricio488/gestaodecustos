<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StorePurchase extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'amount_paid', 'payment_status',
        'coupon_code', 'discount_amount', 'payment_method',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function license(): HasOne
    {
        return $this->hasOne(StoreLicense::class, 'purchase_id');
    }
}
