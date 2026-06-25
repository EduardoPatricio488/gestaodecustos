<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model {
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'bank_account_id',
        'description',
        'amount',
        'received_at',
        'type',
        'source',
        'frequency',
        'tax_estimate',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'date',
        'amount' => 'decimal:2'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    // RELAÇÃO ADICIONADA: Onde entrou o dinheiro?
    public function bankAccount(): BelongsTo {
        return $this->belongsTo(BankAccount::class);
    }
}
