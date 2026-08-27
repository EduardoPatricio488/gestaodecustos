<?php

namespace App\Services;

use App\Models\AutoSavingsRule;
use App\Models\GoalContribution;
use App\Models\Income;
use Illuminate\Support\Facades\DB;

class AutoSavingsService
{
    public function applyForIncome(Income $income): array
    {
        $workspaceId = (int) $income->workspace_id;
        $userId = (int) $income->user_id;
        $baseAmount = (float) ($income->amount_converted ?: $income->amount);

        if ($workspaceId <= 0 || $userId <= 0 || $baseAmount <= 0) {
            return [];
        }

        $rules = AutoSavingsRule::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->with('goal')
            ->get()
            ->filter(fn (AutoSavingsRule $rule): bool => $this->ruleMatchesIncome($rule, $income, $baseAmount));

        $created = [];

        foreach ($rules as $rule) {
            $goal = $rule->goal;
            if (! $goal || (int) $goal->workspace_id !== $workspaceId) {
                continue;
            }

            $remaining = max(0.0, (float) $goal->target_amount - (float) $goal->current_amount);
            if ($remaining <= 0) {
                continue;
            }

            $amount = min($remaining, round($baseAmount * ((float) $rule->percent / 100), 2));
            if ($amount <= 0) {
                continue;
            }

            $created[] = DB::transaction(function () use ($income, $rule, $goal, $workspaceId, $userId, $amount) {
                $contribution = GoalContribution::create([
                    'workspace_id' => $workspaceId,
                    'goal_id' => $goal->id,
                    'user_id' => $userId,
                    'income_id' => $income->id,
                    'amount' => $amount,
                    'source' => 'auto_savings',
                    'note' => 'Regra '.$rule->profile.' aplicada a receita.',
                    'contributed_at' => $income->received_at ?? now(),
                ]);

                $goal->increment('current_amount', $amount);

                return $contribution;
            });
        }

        return $created;
    }

    private function ruleMatchesIncome(AutoSavingsRule $rule, Income $income, float $baseAmount): bool
    {
        if ($rule->min_income_amount !== null && $baseAmount < (float) $rule->min_income_amount) {
            return false;
        }

        $appliesTo = $rule->applies_to ?: 'all';
        if ($appliesTo === 'all') {
            return true;
        }

        $frequency = strtolower((string) $income->frequency);
        $type = strtolower((string) $income->type);

        return match ($appliesTo) {
            'recurring' => in_array($frequency, ['semanal', 'mensal', 'anual'], true) || str_contains($type, 'sal'),
            'one_off' => $frequency === 'pontual' || str_contains($type, 'extra'),
            default => true,
        };
    }
}
