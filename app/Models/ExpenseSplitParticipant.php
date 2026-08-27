<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseSplitParticipant extends Model
{
    protected $fillable = [
        'expense_split_id', 'user_id', 'amount', 'paid', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function split(): BelongsTo
    {
        return $this->belongsTo(ExpenseSplit::class, 'expense_split_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
