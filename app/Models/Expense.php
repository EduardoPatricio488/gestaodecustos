<?php

namespace App\Models;

use App\Services\CurrencyService;
use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = [
        'user_id', 'category_id', 'workspace_id', 'bank_account_id',
        'amount', 'description', 'status', 'spent_at', 'receipt_path',
        'is_company', 'project_id', 'task_id', 'currency', 'amount_converted', // Adicionados aqui
    ];

    protected $casts = [
        'spent_at' => 'date',
        'amount' => 'decimal:2',
        'amount_converted' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'metadata' => 'array',
        'is_company' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Expense $expense): void {
            if (! $expense->workspace_id || ! is_numeric($expense->amount)) {
                return;
            }

            $workspaceCurrency = strtoupper((string) (Workspace::find($expense->workspace_id)?->currency ?? 'EUR'));
            $transactionCurrency = strtoupper((string) ($expense->currency ?: $workspaceCurrency));

            $expense->currency = $transactionCurrency;
            $expense->forceFill(['amount_converted' => round((float) CurrencyService::convert(
                (float) $expense->amount,
                $transactionCurrency,
                $workspaceCurrency
            ), 2)]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // RELAÇÃO ADICIONADA: De onde saiu o dinheiro?
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
