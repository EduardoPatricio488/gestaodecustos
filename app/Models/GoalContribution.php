<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DomainException;

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

    protected static function booted(): void
    {
        static::saving(function (GoalContribution $contribution): void {
            if (! $contribution->workspace_id) {
                return;
            }

            if ((float) $contribution->amount <= 0) {
                throw new DomainException('O valor da contribuição tem de ser superior a zero.');
            }

            if ($contribution->goal_id) {
                $goalBelongs = Goal::withoutGlobalScopes()
                    ->whereKey($contribution->goal_id)
                    ->where('workspace_id', $contribution->workspace_id)
                    ->exists();

                if (! $goalBelongs) {
                    throw new DomainException('O objetivo selecionado não pertence ao workspace atual.');
                }
            }

            if ($contribution->income_id) {
                $incomeBelongs = Income::withoutGlobalScopes()
                    ->whereKey($contribution->income_id)
                    ->where('workspace_id', $contribution->workspace_id)
                    ->exists();

                if (! $incomeBelongs) {
                    throw new DomainException('A receita selecionada não pertence ao workspace atual.');
                }
            }
        });
    }

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
