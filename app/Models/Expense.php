<?php

namespace App\Models;

use App\Services\CurrencyService;
use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\DomainException;

class Expense extends Model
{
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = ['user_id', 'category_id', 'workspace_id', 'supplier_id', 'bank_account_id', 'subcategory', 'metadata', 'amount', 'amount_paid', 'description', 'status', 'spent_at', 'receipt_path', 'is_company', 'project_id', 'task_id', 'cost_center_id', 'currency', 'amount_converted', 'vat_amount', 'title'];

    protected $casts = ['spent_at' => 'date', 'amount' => 'decimal:2', 'amount_paid' => 'decimal:2', 'amount_converted' => 'decimal:2', 'vat_amount' => 'decimal:2', 'metadata' => 'array', 'is_company' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (Expense $expense): void {
            if (! $expense->workspace_id || ! is_numeric($expense->amount)) {
                return;
            }
            $workspace = Workspace::withoutGlobalScopes()->find($expense->workspace_id);
            $workspaceCurrency = strtoupper((string) ($workspace?->currency ?? 'EUR'));
            $transactionCurrency = strtoupper((string) ($expense->currency ?: $workspaceCurrency));
            $amount = round((float) $expense->amount, 2);
            $vat = round((float) ($expense->vat_amount ?? 0), 2);
            $paid = round((float) ($expense->amount_paid ?? 0), 2);
            if ($amount <= 0 || $vat < 0 || $vat > $amount || $paid < 0 || $paid > $amount + 0.01) {
                throw new DomainException('Os valores da despesa são inválidos.');
            }
            if ($expense->category_id && (int) Category::withoutGlobalScopes()->whereKey($expense->category_id)->value('workspace_id') !== (int) $expense->workspace_id) {
                throw new DomainException('A categoria selecionada não pertence à empresa.');
            }
            if ($expense->supplier_id && (int) Supplier::withoutGlobalScopes()->whereKey($expense->supplier_id)->value('workspace_id') !== (int) $expense->workspace_id) {
                throw new DomainException('O fornecedor selecionado não pertence à empresa.');
            }
            if ($expense->cost_center_id && (int) CostCenter::withoutGlobalScopes()->whereKey($expense->cost_center_id)->value('workspace_id') !== (int) $expense->workspace_id) {
                throw new DomainException('O centro de custo não pertence à empresa.');
            }
            $expense->forceFill(['amount' => $amount, 'amount_paid' => $paid, 'vat_amount' => $vat, 'currency' => $transactionCurrency, 'amount_converted' => round((float) CurrencyService::convert($amount, $transactionCurrency, $workspaceCurrency), 2)]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function getOutstandingAmountAttribute(): float
    {
        return max(0, round((float) $this->amount - (float) $this->amount_paid, 2));
    }
}
