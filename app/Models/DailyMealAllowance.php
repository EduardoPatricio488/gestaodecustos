<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyMealAllowance extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'recurring_income_id',
        'allowance_date',
        'expected_amount',
        'received_amount',
        'notes',
    ];

    protected $casts = [
        'allowance_date' => 'date',
        'expected_amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
    ];

    public function recurringIncome(): BelongsTo
    {
        return $this->belongsTo(RecurringIncome::class);
    }
}
