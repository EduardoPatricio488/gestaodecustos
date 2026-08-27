<?php

namespace App\Models;

use App\Services\CurrencyService;
use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Income extends Model {
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'bank_account_id',
        'description',
        'amount',
        'currency',
        'amount_converted',
        'received_at',
        'type',
        'source',
        'frequency',
        'tax_estimate',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'date',
        'amount' => 'decimal:2',
        'amount_converted' => 'decimal:2'
    ];

    protected static function booted(): void
    {
        static::saving(function (Income $income): void {
            if (! $income->workspace_id || ! is_numeric($income->amount)) {
                return;
            }

            $workspaceCurrency = strtoupper((string) (Workspace::find($income->workspace_id)?->currency ?? 'EUR'));
            $transactionCurrency = strtoupper((string) ($income->currency ?: $workspaceCurrency));

            $income->currency = $transactionCurrency;
            $income->forceFill(['amount_converted' => round((float) CurrencyService::convert(
                (float) $income->amount,
                $transactionCurrency,
                $workspaceCurrency
            ), 2)]);
        });

        static::created(function (Income $income): void {
            app(\App\Services\AutoSavingsService::class)->applyForIncome($income);
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    // RELAÇÃO ADICIONADA: Onde entrou o dinheiro?
    public function bankAccount(): BelongsTo {
        return $this->belongsTo(BankAccount::class);
    }

    public function goalContributions(): HasMany {
        return $this->hasMany(GoalContribution::class);
    }
}
