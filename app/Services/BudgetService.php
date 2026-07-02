<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Workspace;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class BudgetService
{
    public function getMonthlyOverview(Workspace $workspace, ?CarbonInterface $month = null): array
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $categories = Category::where('workspace_id', $workspace->id)->get();
        $totalBudget = (float) $categories->sum('budget_limit');

        $totalSpent = (float) Expense::where('workspace_id', $workspace->id)
            ->where('is_company', false)
            ->whereBetween('spent_at', [$start, $end])
            ->sum('amount');

        $totalIncome = (float) Income::where('workspace_id', $workspace->id)
            ->whereBetween('received_at', [$start, $end])
            ->sum('amount');

        $daysInMonth = $month->daysInMonth;
        $dayOfMonth = min($month->day, $daysInMonth);
        $daysRemaining = max(0, $daysInMonth - $dayOfMonth);

        $dailyAvg = $dayOfMonth > 0 ? $totalSpent / $dayOfMonth : 0;
        $projectedSpend = $dailyAvg * $daysInMonth;

        return [
            'month' => $month->translatedFormat('F Y'),
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'total_income' => $totalIncome,
            'remaining' => max(0, $totalBudget - $totalSpent),
            'percentage' => $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0,
            'days_remaining' => $daysRemaining,
            'daily_avg' => round($dailyAvg, 2),
            'projected_spend' => round($projectedSpend, 2),
            'safe_to_spend_daily' => $daysRemaining > 0 && $totalBudget > $totalSpent
                ? round(($totalBudget - $totalSpent) / $daysRemaining, 2)
                : 0,
        ];
    }

    public function getCategoryBreakdown(Workspace $workspace, ?CarbonInterface $month = null): Collection
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        return Category::where('workspace_id', $workspace->id)
            ->orderBy('order')
            ->get()
            ->map(function (Category $category) use ($workspace, $start, $end) {
                $spent = (float) Expense::where('workspace_id', $workspace->id)
                    ->where('category_id', $category->id)
                    ->where('is_company', false)
                    ->whereBetween('spent_at', [$start, $end])
                    ->sum('amount');

                $budget = (float) ($category->budget_limit ?? 0);
                $pct = $budget > 0 ? round(($spent / $budget) * 100, 1) : 0;

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->resolveSlug(),
                    'color' => $category->color ?? '#6366f1',
                    'icon' => $category->icon,
                    'budget' => $budget,
                    'spent' => $spent,
                    'remaining' => max(0, $budget - $spent),
                    'percentage' => $pct,
                    'alert_level' => $this->alertLevel($pct, $budget > 0),
                ];
            });
    }

    public function getAlerts(Workspace $workspace, ?CarbonInterface $month = null): Collection
    {
        $month = $month ?? now();
        $alerts = collect();

        foreach ($this->getCategoryBreakdown($workspace, $month) as $cat) {
            if ($cat['budget'] <= 0) {
                continue;
            }

            if ($cat['percentage'] >= 100) {
                $alerts->push([
                    'type' => 'danger',
                    'category' => $cat['name'],
                    'message' => "Ultrapassaste o orçamento de {$cat['name']} ({$cat['percentage']}%)",
                    'icon' => 'exclamation-circle',
                ]);
            } elseif ($cat['percentage'] >= 80) {
                $alerts->push([
                    'type' => 'warning',
                    'category' => $cat['name'],
                    'message' => "Atenção: {$cat['name']} a {$cat['percentage']}% do orçamento",
                    'icon' => 'bell',
                ]);
            }
        }

        $overview = $this->getMonthlyOverview($workspace, $month);
        if ($overview['total_budget'] > 0 && $overview['projected_spend'] > $overview['total_budget']) {
            $alerts->push([
                'type' => 'warning',
                'category' => 'Geral',
                'message' => 'Ao ritmo atual, vais ultrapassar o orçamento mensal total',
                'icon' => 'chart-bar',
            ]);
        }

        return $alerts;
    }

    private function alertLevel(float $pct, bool $hasBudget): string
    {
        if (! $hasBudget) {
            return 'none';
        }
        if ($pct >= 100) {
            return 'danger';
        }
        if ($pct >= 80) {
            return 'warning';
        }

        return 'ok';
    }
}
