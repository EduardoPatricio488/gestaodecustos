<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\DomainException;

class BankTransaction extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id','bank_account_id','user_id','transaction_date','amount','currency','description','external_reference','status','matched_type','matched_id','reconciled_at'];
    protected $casts = ['transaction_date'=>'date','amount'=>'decimal:2','reconciled_at'=>'datetime'];

    protected static function booted(): void
    {
        static::saving(function (BankTransaction $transaction): void {
            if (! $transaction->workspace_id || ! $transaction->bank_account_id) {
                return;
            }

            $belongsToWorkspace = BankAccount::withoutGlobalScopes()
                ->whereKey($transaction->bank_account_id)
                ->where('workspace_id', $transaction->workspace_id)
                ->exists();

            if (! $belongsToWorkspace) {
                throw new DomainException('A conta bancária da transação tem de pertencer ao workspace atual.');
            }
        });
    }

    public function bankAccount(): BelongsTo { return $this->belongsTo(BankAccount::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function isReconciled(): bool { return $this->status === 'reconciled'; }
}
