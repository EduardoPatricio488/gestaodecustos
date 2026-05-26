<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = [
        'workspace_id', 'user_id', 'name', 'type', 'is_business', 'balance', 'currency', 'color'
    ];

    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function incomes(): HasMany { return $this->hasMany(Income::class); }

    public function getCurrentBalanceAttribute(): float
    {
        $incomes = (float) $this->incomes()->sum('amount');
        $expenses = (float) $this->expenses()->sum('amount');
        return (float) ($this->balance + $incomes - $expenses);
    }

    public function getIcon()
    {
        return match($this->type) {
            'poupanca' => 'piggy-bank', 'cash' => 'banknotes', 'credito' => 'credit-card', default => 'building-library'
        };
    }
}
