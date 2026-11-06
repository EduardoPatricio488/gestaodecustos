<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryPayment extends Model
{
    protected $fillable = [
        'recurring_income_id',
        'user_id',
        'workspace_id',
        'bank_account_id',
        'payment_month',
        'payment_year',
        'salary_base_gross',
        'allowances_duodecimos',
        'other_complements',
        'social_security',
        'irs',
        'other_deductions',
        'meal_allowance',
        'net_received',
        'bank_balance_amount',
        'paid_at',
        'status',
        'is_automatic',
        'notes',
    ];

    protected $casts = [
        'salary_base_gross' => 'decimal:2',
        'allowances_duodecimos' => 'decimal:2',
        'other_complements' => 'decimal:2',
        'social_security' => 'decimal:2',
        'irs' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'net_received' => 'decimal:2',
        'bank_balance_amount' => 'decimal:2',
        'paid_at' => 'date',
        'is_automatic' => 'boolean',
    ];

    public function recurringIncome(): BelongsTo
    {
        return $this->belongsTo(RecurringIncome::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
