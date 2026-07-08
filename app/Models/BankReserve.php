<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankReserve extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'bank_account_id',
        'name',
        'amount',
        'target_amount',
        'target_date',
        'status',
        'color',
        'icon',
        'description',
        'is_business',
    ];

    protected $casts = [
        'amount'        => 'float',
        'target_amount' => 'float',
        'target_date'   => 'date',
        'is_business'   => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function getProgressAttribute(): float
    {
        if (!$this->target_amount || $this->target_amount <= 0) {
            return 0;
        }
        return min(100, ($this->amount / $this->target_amount) * 100);
    }
}
