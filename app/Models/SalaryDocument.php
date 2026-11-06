<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryDocument extends Model
{
    protected $fillable = [
        'recurring_income_id',
        'user_id',
        'workspace_id',
        'document_type',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function recurringIncome(): BelongsTo
    {
        return $this->belongsTo(RecurringIncome::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
