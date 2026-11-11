<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'subscription_id', 'workspace_id', 'user_id', 'billing_date',
        'amount', 'paid_at', 'payment_source',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'paid_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
