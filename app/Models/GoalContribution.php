<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalContribution extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'goal_id',
        'user_id',
        'income_id',
        'amount',
        'source',
        'note',
        'contributed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'contributed_at' => 'datetime',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function income(): BelongsTo
    {
        return $this->belongsTo(Income::class);
    }
}
