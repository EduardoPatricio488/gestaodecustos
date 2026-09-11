<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\DomainException;

class BankTransfer extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'from_account_id',
        'to_account_id',
        'amount',
        'currency',
        'category',
        'description',
        'status',
        'transferred_at',
        'receipt_path',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'transferred_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (BankTransfer $transfer): void {
            if (! $transfer->workspace_id) {
                return;
            }

            if ((int) $transfer->from_account_id === (int) $transfer->to_account_id) {
                throw new DomainException('A conta de origem e a conta de destino têm de ser diferentes.');
            }

            if ((float) $transfer->amount <= 0) {
                throw new DomainException('O valor da transferência tem de ser superior a zero.');
            }

            $accountIds = array_filter([
                (int) $transfer->from_account_id,
                (int) $transfer->to_account_id,
            ]);

            $workspaceAccountIds = BankAccount::withoutGlobalScopes()
                ->where('workspace_id', $transfer->workspace_id)
                ->whereIn('id', $accountIds)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            sort($accountIds);
            sort($workspaceAccountIds);

            if ($accountIds !== $workspaceAccountIds) {
                throw new DomainException('As contas da transferência têm de pertencer ao workspace atual.');
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'to_account_id');
    }
}
