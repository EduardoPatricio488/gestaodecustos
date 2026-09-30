<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringIncome extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'user_id', 'workspace_id', 'bank_account_id', 'description', 'amount', 'day_of_month',
        'is_active', 'source', 'frequency', 'tax_estimate', 'notes', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
        'tax_estimate' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (RecurringIncome $income): void {
            if (! $income->workspace_id) {
                return;
            }

            $workspace = Workspace::withoutGlobalScopes()->find($income->workspace_id);
            if (! $workspace) {
                throw new \DomainException('O workspace do rendimento não existe.');
            }

            if ($income->bank_account_id) {
                $account = BankAccount::withoutGlobalScopes()
                    ->whereKey($income->bank_account_id)
                    ->where('workspace_id', $income->workspace_id)
                    ->first();

                if (! $account) {
                    throw new \DomainException('A conta bancária selecionada não pertence ao workspace.');
                }

                if ($workspace->type === 'personal' && (int) $account->user_id !== (int) $income->user_id) {
                    throw new \DomainException('A conta bancária selecionada não pertence ao utilizador.');
                }
            }

            if ($income->user_id) {
                $isMember = $workspace->users()->whereKey($income->user_id)->exists()
                    || (int) $workspace->owner_id === (int) $income->user_id;

                if (! $isMember) {
                    throw new \DomainException('O utilizador do rendimento não pertence ao workspace.');
                }
            }

            $actor = auth()->user();
            if ($actor && $income->exists && (int) $income->user_id !== (int) $actor->id && in_array($workspace->type, ['business', 'company'], true)) {
                app(\App\Services\BusinessAccessService::class)->assert('manage_financials', $actor, $workspace);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
