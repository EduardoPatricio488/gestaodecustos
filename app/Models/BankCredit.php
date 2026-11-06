<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankCredit extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'user_id', 'bank_account_id', 'name', 'amount', 'category', 'due_date',
        'status', 'received_amount', 'notes', 'is_business',
    ];

    protected $casts = [
        'amount' => 'float', 'received_amount' => 'float', 'due_date' => 'date', 'is_business' => 'boolean',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPendingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->received_amount);
    }
}
