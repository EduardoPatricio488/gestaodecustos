<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',

        // Identificação
        'name',
        'type',
        'is_business',
        'color',
        'icon',
        'status',
        'description',
        'opened_at',
        'include_in_total',
        'alert_below',

        // Dados bancários
        'bank_name',
        'country',
        'iban',
        'swift',
        'holder_name',
        'currency',

        // Financeiro
        'balance',
        'credit_limit',
        'forecast_balance',
        'risk_score',

        // Tags e notas
        'tags',
        'notes',
    ];

    protected $casts = [
        'tags'             => 'array',
        'is_business'      => 'boolean',
        'include_in_total' => 'boolean',
        'balance'          => 'float',
        'credit_limit'     => 'float',
        'forecast_balance' => 'float',
        'alert_below'      => 'float',
        'risk_score'       => 'integer',
        'opened_at'        => 'date',
    ];

    /* ============================================================
       RELAÇÕES
       ============================================================ */

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function reserves(): HasMany
    {
        return $this->hasMany(BankReserve::class);
    }

    public function transfersOut(): HasMany
    {
        return $this->hasMany(BankTransfer::class, 'from_account_id');
    }

    public function transfersIn(): HasMany
    {
        return $this->hasMany(BankTransfer::class, 'to_account_id');
    }

    /* ============================================================
       ACESSORES FINANCEIROS
       ============================================================ */

    public function getCurrentBalanceAttribute(): float
    {
        $incomes = (float) $this->incomes()->sum('amount');
        $expenses = (float) $this->expenses()->sum('amount');

        return (float) ($this->balance + $incomes - $expenses);
    }

    public function getCreditUsedAttribute(): float
    {
        if ($this->type !== 'credito' || !$this->credit_limit) {
            return 0;
        }

        return abs($this->current_balance);
    }

    public function getCreditUsagePercentAttribute(): float
    {
        if ($this->type !== 'credito' || !$this->credit_limit) {
            return 0;
        }

        return round(($this->credit_used / $this->credit_limit) * 100, 2);
    }

    public function getForecastAttribute(): float
    {
        return (float) ($this->forecast_balance ?? $this->current_balance);
    }

    /* ============================================================
       HELPERS
       ============================================================ */

    public function getIcon()
    {
        return match ($this->type) {
            'poupanca' => 'piggy-bank',
            'cash' => 'banknotes',
            'credito' => 'credit-card',
            'tesouraria' => 'building-office',
            'operacoes' => 'cog',
            'salarios' => 'users',
            'impostos' => 'document-currency-euro',
            default => 'building-library',
        };
    }
}
