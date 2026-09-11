<?php

namespace App\Services\AI;

use App\Models\Workspace;

class FinancialHealthScoreService
{
    public function personal(Workspace $workspace): array
    {
        $snapshot = app(FinancialIntelligenceService::class)->snapshot($workspace);
        $income = (float) data_get($snapshot, 'income', 0);
        $expenses = (float) data_get($snapshot, 'expenses', 0);
        $savingsRate = (float) data_get($snapshot, 'savings_rate', 0);
        $goals = count((array) data_get($snapshot, 'goals', []));
        $investments = (float) data_get($snapshot, 'investment_value', 0);
        $subscriptions = (int) data_get($snapshot, 'active_subscriptions', 0);

        $score = 0;
        if ($income > 0) {
            $score += max(0, min(40, $savingsRate * 0.8));
            $score += $expenses <= $income ? 25 : 0;
            $score += $goals > 0 ? 10 : 0;
            $score += $investments > 0 ? 10 : 0;
            $score += $subscriptions <= 5 ? 10 : 0;
            $score += $savingsRate >= 20 ? 5 : 0;
        }

        return [
            'score' => (int) max(0, min(100, round($score))),
            'label' => $this->label($score),
            'savings_rate' => round($savingsRate, 1),
            'method' => 'deterministic_backend_v1',
            'inputs' => compact('income', 'expenses', 'goals', 'investments', 'subscriptions'),
        ];
    }

    public function business(Workspace $workspace): array
    {
        $snapshot = app(FinancialIntelligenceService::class)->snapshot($workspace);
        $metrics = (array) data_get($snapshot, 'metrics', []);
        $revenue = (float) data_get($metrics, 'revenue_cash', 0);
        $costs = (float) data_get($metrics, 'total_costs', 0);
        $margin = (float) data_get($metrics, 'margin', 0);
        $runway = (float) data_get($snapshot, 'runway', 0);
        $overdue = (float) data_get($metrics, 'overdue_receivables', 0);

        $score = 0;
        if ($revenue > 0) {
            $score += max(0, min(45, $margin * 1.2));
            $score += $revenue >= $costs ? 20 : 0;
            $score += $runway >= 6 ? 20 : ($runway >= 3 ? 10 : 0);
            $score += $overdue <= 0 ? 15 : 0;
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
