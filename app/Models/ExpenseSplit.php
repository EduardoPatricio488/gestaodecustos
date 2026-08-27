<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseSplit extends Model
{
    protected $fillable = [
        'creator_user_id', 'workspace_id', 'category_id',
        'title', 'total_amount', 'split_type', 'spent_at', 'notes',
    ];

    protected $casts = [
        'spent_at' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ExpenseSplitParticipant::class);
    }

    public function settledCount(): int
    {
        return $this->participants->where('paid', true)->count();
    }

    public function isFullySettled(): bool
    {
        return $this->participants->every(fn ($p) => $p->paid);
    }
}
