<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'user_id', 'name', 'type', 'is_business', 'color', 'icon', 'status', 'description', 'opened_at', 'include_in_total', 'alert_below',
        'bank_name', 'country', 'iban', 'account_number', 'swift', 'holder_name', 'currency', 'balance', 'credit_limit', 'forecast_balance', 'risk_score', 'tags', 'notes',
    ];

    protected $casts = [
        'tags' => 'array', 'is_business' => 'boolean', 'include_in_total' => 'boolean', 'balance' => 'float', 'credit_limit' => 'float', 'forecast_balance' => 'float', 'alert_below' => 'float', 'risk_score' => 'integer', 'opened_at' => 'date',
    ];

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

    public function recurringIncomes(): HasMany
    {
        return $this->hasMany(RecurringIncome::class);
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

    public function getCurrentBalanceAttribute(): float
    {
        $incomes = $this->attributes['current_balance_income_total']
            ?? (float) $this->incomes()->sum('amount');
        $expenses = $this->attributes['current_balance_expense_total']
            ?? (float) $this->expenses()->sum('amount');
        $recurringDue = $this->attributes['current_balance_recurring_due']
            ?? (float) $this->recurringIncomes()
                ->where('is_active', true)
                ->where('day_of_month', '<=', now()->day)
                ->whereNotExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('salary_payments')
                        ->whereColumn('salary_payments.recurring_income_id', 'recurring_incomes.id')
                        ->whereColumn('salary_payments.bank_account_id', 'recurring_incomes.bank_account_id')
                        ->where('salary_payments.payment_month', now()->month)
                        ->where('salary_payments.payment_year', now()->year)
                        ->where('salary_payments.status', 'paid')
                        ->where('salary_payments.bank_balance_amount', '>', 0);
                })
                ->sum('amount');

        return (float) $this->balance + (float) $incomes - (float) $expenses + (float) $recurringDue;
    }

    /**
     * Scope used by dashboard/list views that need the current balance for many
     * accounts at once. It avoids the accessor's N+1 fallback queries.
     */
    public function scopeWithCurrentBalanceTotals($query)
    {
        $today = now()->day;
        $month = now()->month;
        $year = now()->year;

        return $query
            ->withSum('incomes as current_balance_income_total', 'amount')
            ->withSum('expenses as current_balance_expense_total', 'amount')
            ->withSum([
                'recurringIncomes as current_balance_recurring_due' => function ($q) use ($today, $month, $year) {
                    $q->where('is_active', true)
                        ->where('day_of_month', '<=', $today)
                        ->whereNotExists(function ($query) use ($month, $year): void {
                            $query->selectRaw('1')
                                ->from('salary_payments')
                                ->whereColumn('salary_payments.recurring_income_id', 'recurring_incomes.id')
                                ->whereColumn('salary_payments.bank_account_id', 'recurring_incomes.bank_account_id')
                                ->where('salary_payments.payment_month', $month)
                                ->where('salary_payments.payment_year', $year)
                                ->where('salary_payments.status', 'paid')
                                ->where('salary_payments.bank_balance_amount', '>', 0);
                        });
                },
            ], 'amount');
    }

    public function getCreditUsedAttribute(): float
    {
        if ($this->type !== 'credito' || ! $this->credit_limit) {
            return 0;
        }

        return abs($this->current_balance);
    }

    public function getCreditUsagePercentAttribute(): float
    {
        if ($this->type !== 'credito' || ! $this->credit_limit) {
            return 0;
        }

        return round(($this->credit_used / $this->credit_limit) * 100, 2);
    }

    public function getForecastAttribute(): float
    {
        return (float) ($this->forecast_balance ?? $this->current_balance);
    }

    public function getIcon(): string
    {
        return match ($this->type) {
            'poupanca' => 'wallet','cash' => 'banknotes','credito' => 'credit-card','tesouraria' => 'building-office','operacoes' => 'cog','salarios' => 'users','impostos' => 'document-currency-euro',default => 'building-library',
        };
    }
}
