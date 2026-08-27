<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoSavingsRule extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'goal_id',
        'profile',
        'percent',
        'min_income_amount',
        'applies_to',
        'is_active',
    ];

    protected $casts = [
        'percent' => 'decimal:2',
        'min_income_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
