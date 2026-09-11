<?php

namespace App\Services\AI;

use App\Models\Goal;
use App\Models\Investment;
use App\Models\Subscription;
use App\Models\Workspace;
use App\Services\BusinessFinancialMetrics;
use Illuminate\Support\Carbon;

class FinancialIntelligenceService
{
    public function snapshot(Workspace $workspace, ?Carbon $period = null): array
    {
        $period = $period
            ? Carbon::instance($period)->startOfMonth()
            : Carbon::now()->startOfMonth();

        return in_array($workspace->type, ['business', 'company'], true)
            ? $this->businessSnapshot($workspace, $period)
            : $this->personalSnapshot($workspace, $period);
    }

    public function personalSnapshot(Workspace $workspace, Carbon $period): array
    {
        $start = $period->copy()->startOfMonth();
        $end = $period->copy()->endOfMonth();
        $previousStart = $start->copy()->subMonth()->startOfMonth();
        $previousEnd = $start->copy()->subMonth()->endOfMonth();

        $spent = (float) $workspace->expenses()
            ->where('is_company', false)
            ->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])
            ->sum('amount_converted');

        $previousSpent = (float) $workspace->expenses()
            ->where('is_company', false)
            ->whereBetween('spent_at', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->sum('amount_converted');

        $earned = (float) $workspace->incomes()
            ->whereBetween('received_at', [$start->toDateString(), $end->toDateString()])
            ->sum('amount_converted');

        $previousEarned = (float) $workspace->incomes()
            ->whereBetween('received_at', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->sum('amount_converted');

        $recurring = (float) $workspace->recurringIncomes()
            ->where('is_active', true)
            ->sum('amount');

        $categories = $workspace->expenses()
            ->where('is_company', false)
            ->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])
            ->with('category:id,name')
            ->get()
            ->groupBy(fn ($expense) => $expense->category?->name ?: 'Sem categoria')
            ->map(fn ($items) => round((float) $items->sum('amount_converted'), 2))
            ->sortDesc()
            ->take(10)
            ->all();

        $actualSavings = $earned - $spent;
        $projectedSavings = $earned + $recurring - $spent;
        $previousActualSavings = $previousEarned - $previousSpent;
        $previousProjectedSavings = $previousEarned + $recurring - $previousSpent;

        $goals = Goal::query()
            ->where('workspace_id', $workspace->id)
            ->select(['id', 'name', 'target_amount', 'current_amount', 'deadline'])
            ->get()
            ->map(fn ($goal) => [
                'id' => $goal->id,
                'name' => $goal->name,
                'target' => (float) $goal->target_amount,
                'current' => (float) $goal->current_amount,
                'progress_percent' => (float) ($goal->target_amount > 0 ? min(100, ($goal->current_amount / $goal->target_amount) * 100) : 0),
                'deadline' => optional($goal->deadline)->toDateString(),
            ])->values()->all();

        $subscriptions = Subscription::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'amount', 'cycle', 'renewal_date']);

        $investmentValue = Investment::query()
            ->where('workspace_id', $workspace->id)
            ->get(['quantity', 'current_price'])
            ->sum(fn ($investment) => (float) $investment->quantity * (float) $investment->current_price);

        return [
            'kind' => 'personal',
            'period' => $start->format('Y-m'),
            'currency' => strtoupper((string) ($workspace->currency ?: 'EUR')),
            // Backwards-compatible aggregate, explicitly documented by the fields below.
            'income' => round($earned + $recurring, 2),
            'income_actual' => round($earned, 2),
            'income_recurring' => round($recurring, 2),
            'income_projected' => round($earned + $recurring, 2),
            'dated_income' => round($earned, 2),
            'recurring_income' => round($recurring, 2),
            'expenses' => round($spent, 2),
            'savings' => round($projectedSavings, 2),
            'savings_actual' => round($actualSavings, 2),
            'savings_projected' => round($projectedSavings, 2),
            'savings_rate' => round(($earned + $recurring) > 0 ? ($projectedSavings / ($earned + $recurring)) * 100 : 0, 2),
            'savings_rate_actual' => round($earned > 0 ? ($actualSavings / $earned) * 100 : 0, 2),
            'previous' => [
                'income' => round($previousEarned + $recurring, 2),
                'income_actual' => round($previousEarned, 2),
                'income_recurring' => round($recurring, 2),
                'expenses' => round($previousSpent, 2),
                'savings' => round($previousProjectedSavings, 2),
                'savings_actual' => round($previousActualSavings, 2),
            ],
            'changes' => [
                'income_percent' => $this->percentChange($previousEarned + $recurring, $earned + $recurring),
                'income_actual_percent' => $this->percentChange($previousEarned, $earned),
                'expenses_percent' => $this->percentChange($previousSpent, $spent),
                'savings_percent' => $this->percentChange($previousProjectedSavings, $projectedSavings),
                'savings_actual_percent' => $this->percentChange($previousActualSavings, $actualSavings),
            ],
            'top_categories' => $categories,
            'goals' => $goals,
            'active_subscriptions' => $subscriptions->count(),
            'active_subscription_cost' => round((float) $subscriptions->sum('amount'), 2),
            'investment_value' => round((float) $investmentValue, 2),
            'source' => 'Finance Pro AI database',
            'data_quality' => 'deterministic_backend_calculation',
        ];
    }

    public function businessSnapshot(Workspace $workspace, Carbon $period): array
    {
        $metrics = app(BusinessFinancialMetrics::class)->forMonth($workspace, $period);
        $previous = app(BusinessFinancialMetrics::class)->forMonth($workspace, $period->copy()->subMonth());

        return [
            'kind' => 'business',
            'period' => $period->format('Y-m'),
            'currency' => strtoupper((string) ($workspace->currency ?: 'EUR')),
            'metrics' => $metrics,
            'previous' => $previous,
            'changes' => [
                'revenue_percent' => $this->percentChange($previous['revenue_cash'], $metrics['revenue_cash']),
                'costs_percent' => $this->percentChange($previous['total_costs'], $metrics['total_costs']),
                'net_result_percent' => $this->percentChange($previous['net_result'], $metrics['net_result']),
                'margin_points' => round($metrics['margin'] - $previous['margin'], 2),
                'cash_percent' => $this->percentChange($previous['cash'], $metrics['cash']),
            ],
            'counts' => [
                'clients' => $workspace->clients()->count(),
                'suppliers' => $workspace->suppliers()->count(),
                'employees' => $workspace->employees()->where('active', true)->count(),
                'products' => $workspace->products()->count(),
                'projects' => $workspace->projects()->count(),
                'invoices' => $workspace->invoices()->count(),
            ],
            'runway' => $workspace->getRunway(),
            'source' => 'Finance Pro AI database',
            'data_quality' => 'deterministic_backend_calculation',
        ];
    }

    private function percentChange(float $previous, float $current): ?float
    {
        if (abs($previous) < 0.00001) {
            return null;
        }

        return round((($current - $previous) / abs($previous)) * 100, 2);
    }
}
