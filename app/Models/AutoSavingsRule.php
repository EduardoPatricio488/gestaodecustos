<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DomainException;

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

    protected static function booted(): void
    {
        static::saving(function (AutoSavingsRule $rule): void {
            if (! $rule->workspace_id) {
                return;
            }

            if ((float) $rule->percent < 0 || (float) $rule->percent > 100) {
                throw new DomainException('A percentagem de poupança automática deve estar entre 0 e 100.');
            }

            if ((float) $rule->min_income_amount < 0) {
                throw new DomainException('O rendimento mínimo não pode ser negativo.');
            }

            if ($rule->goal_id) {
                $goalBelongs = Goal::withoutGlobalScopes()
                    ->whereKey($rule->goal_id)
                    ->where('workspace_id', $rule->workspace_id)
                    ->exists();

                if (! $goalBelongs) {
                    throw new DomainException('O objetivo da regra de poupança não pertence ao workspace atual.');
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
}
