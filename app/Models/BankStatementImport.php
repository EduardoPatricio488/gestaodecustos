<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementImport extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'user_id', 'filename', 'bank_detected',
        'status', 'transactions_total', 'transactions_imported', 'errors',
    ];

    protected $casts = ['errors' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
