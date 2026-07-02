<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetChallenge extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'user_id', 'category_id', 'title',
        'target_amount', 'start_date', 'end_date', 'status', 'xp_awarded',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'xp_awarded' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getSpentAttribute(): float
    {
        $query = Expense::where('workspace_id', $this->workspace_id)
            ->where('is_company', false)
            ->whereBetween('spent_at', [$this->start_date, $this->end_date]);

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return (float) $query->sum('amount');
    }

    public function getProgressPctAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->spent / $this->target_amount) * 100, 1));
    }

    public function isCompleted(): bool
    {
        return $this->spent <= $this->target_amount && now()->gte($this->end_date);
    }

    public function isFailed(): bool
    {
        return $this->spent > $this->target_amount;
    }
}
