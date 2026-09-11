<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = ['workspace_id', 'user_id', 'invoice_id', 'expense_id', 'bank_account_id', 'amount', 'currency', 'paid_at', 'reference', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'date'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
