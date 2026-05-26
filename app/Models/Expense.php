<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model {
    use BelongsToWorkspace, LogsActivity;

    protected $fillable = [
        'user_id',
        'category_id',
        'workspace_id',
        'bank_account_id', // ADICIONADO: Ligação à conta bancária
        'title',
        'amount',
        'vat_amount',
        'description',
        'spent_at',
        'subcategory',
        'metadata',
        'receipt_path',
        'is_company'
    ];

    protected $casts = [
        'spent_at' => 'date',
        'amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'metadata' => 'array',
        'is_company' => 'boolean'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }

    // RELAÇÃO ADICIONADA: De onde saiu o dinheiro?
    public function bankAccount(): BelongsTo {
        return $this->belongsTo(BankAccount::class);
    }
}
