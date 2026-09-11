<?php

namespace App\Services\AI;

use App\Models\Workspace;

class FinancialHealthScoreService
{
    public function personal(Workspace $workspace): array
    {
        $snapshot = app(FinancialIntelligenceService::class)->snapshot($workspace);
        $income = (float) data_get($snapshot, 'personal.income.current', 0);
        $expenses = (float) data_get($snapshot, 'personal.expenses.current', 0);
        $savingsRate = $income > 0 ? (($income - $expenses) / $income) * 100 : 0;

        $score = 0;
        if ($income > 0) {
            $score += max(0, min(40, $savingsRate * 0.8));
            $score += $expenses <= $income ? 25 : 0;
            $score += data_get($snapshot, 'personal.goals.active', 0) > 0 ? 10 : 0;
            $score += data_get($snapshot, 'personal.investments.total_value', 0) > 0 ? 10 : 0;
            $score += data_get($snapshot, 'personal.subscriptions.active', 0) <= 5 ? 10 : 0;
            $score += $savingsRate >= 20 ? 5 : 0;
        }

        return [
            'score' => (int) max(0, min(100, round($score))),
            'label' => $this->label($score),
            'savings_rate' => round($savingsRate, 1),
            'method' => 'deterministic_backend_v1',
            'inputs' => [
                'income' => $income,
                'expenses' => $expenses,
                'active_goals' => (int) data_get($snapshot, 'personal.goals.active', 0),
                'investments' => (float) data_get($snapshot, 'personal.investments.total_value', 0),
                'active_subscriptions' => (int) data_get($snapshot, 'personal.subscriptions.active', 0),
            ],
        ];
    }

    public function business(Workspace $workspace): array
    {
        $snapshot = app(FinancialIntelligenceService::class)->snapshot($workspace);
        $revenue = (float) data_get($snapshot, 'business.revenue.current', 0);
        $costs = (float) data_get($snapshot, 'business.costs.current', 0);
        $margin = $revenue > 0 ? (($revenue - $costs) / $revenue) * 100 : 0;
        $runway = (float) data_get($snapshot, 'business.runway_months', 0);

        $score = 0;
        if ($revenue > 0) {
            $score += max(0, min(45, $margin * 1.2));
            $score += $revenue >= $costs ? 20 : 0;
            $score += $runway >= 6 ? 20 : ($runway >= 3 ? 10 : 0);
            $score += data_get($snapshot, 'business.clients.active', 0) > 0 ? 5 : 0;
            $score += data_get($snapshot, 'business.invoices.overdue', 0) === 0 ? 10 : 0;
        }

        return [
            'score' => (int) max(0, min(100, round($score))),
            'label' => $this->label($score),
            'margin' => round($margin, 1),
            'runway_months' => round($runway, 1),
            'method' => 'deterministic_backend_v1',
        ];
    }

    private function label(float $score): string
    {
        return match (true) {
            $score >= 80 => 'Excelente',
            $score >= 65 => 'Saudável',
            $score >= 45 => 'Atenção',
            $score > 0 => 'Crítico',
            default => 'Sem dados suficientes',
        };
    }
}
