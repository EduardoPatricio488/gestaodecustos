<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringIncome extends Model
{
    use BelongsToWorkspace;

    // 1. ADICIONADO 'metadata' ao fillable para permitir gravar os detalhes do salário
    protected $fillable = [
        'user_id',
        'workspace_id',
        'bank_account_id',
        'description',
        'amount',
        'day_of_month',
        'is_active',
        'source',
        'frequency',
        'tax_estimate',
        'notes',
        'metadata',
    ];

    // 2. ADICIONADO casts para o PHP saber ler os números e o JSON da base de dados
    protected $casts = [
        'metadata' => 'array',        // Crucial para carregar o Salário Bruto, SS e IRS
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
        'tax_estimate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
