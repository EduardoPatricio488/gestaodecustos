<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionCycleService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ExpenseForecastHub extends Component
{
    public int $lookbackMonths = 6;

    public float $recencyWeight = 0.65;

    private function weightedAverage(array $values, float $recentWeight): float
    {
        $count = count($values);
        if ($count === 0) {
            return 0.0;
        }

        $weights = [];
        for ($i = 0; $i < $count; $i++) {
            $progress = $count > 1 ? ($i / ($count - 1)) : 1;
            $weights[] = (1 - $recentWeight) + ($recentWeight * $progress);
        }

        $weightedSum = 0.0;
        $weightTotal = 0.0;
        foreach ($values as $idx => $value) {
            $weightedSum += $value * $weights[$idx];
            $weightTotal += $weights[$idx];
        }

        return $weightTotal > 0 ? $weightedSum / $weightTotal : 0.0;
    }

    private function linearTrend(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }

        $sumX = 0.0;
        $sumY = 0.0;
        $sumXY = 0.0;
        $sumX2 = 0.0;

        foreach ($values as $i => $y) {
            $x = $i + 1;
            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumX2 += ($x * $x);
        }

        $den = ($n * $sumX2) - ($sumX * $sumX);
        if ($den == 0.0) {
            return 0.0;
        }

        return (($n * $sumXY) - ($sumX * $sumY)) / $den;
    }

    private function confidence(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 40.0;
        }

        $mean = array_sum($values) / $n;
        if ($mean <= 0) {
            return 35.0;
        }

        $variance = array_sum(array_map(fn ($v) => pow($v - $mean, 2), $values)) / $n;
        $stdDev = sqrt($variance);
        $cv = $stdDev / $mean; // coeficiente de variação

        $score = 100 - min(65, $cv * 100);

        return max(30, round($score, 1));
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $months = max(3, min(12, $this->lookbackMonths));
        $start = now()->copy()->subMonths($months)->startOfMonth();
        $end = now()->copy()->startOfMonth();

        $expenses = Expense::where('workspace_id', $workspaceId)
            ->where('spent_at', '>=', $start)
            ->where('spent_at', '<', $end)
            ->get();

        // Fallback para contas recentes sem histórico fechado: usa as despesas já registadas
        // este mês tal como estão (não são recorrentes, por isso não se extrapola um ritmo diário).
        $currentMonthExpenses = Expense::where('workspace_id', $workspaceId)
            ->where('spent_at', '>=', $end)
            ->get();

        $monthKeys = collect();
        for ($i = $months; $i >= 1; $i--) {
            $monthKeys->push(now()->copy()->subMonths($i)->format('Y-m'));
        }

        // Assinaturas ativas são custos garantidos todos os meses: contam sempre para a
        // previsão da categoria a que pertencem, convertidas ao equivalente mensal.
        $subscriptionsByCategory = Subscription::where('workspace_id', $workspaceId)
            ->get(['category_id', 'amount', 'cycle', 'status', 'is_active'])
            ->filter(fn ($sub) => ($sub->status ?: ($sub->is_active ? 'active' : 'paused')) === 'active')
            ->groupBy('category_id')
            ->map(fn ($subs) => (float) $subs->sum(fn ($sub) => SubscriptionCycleService::toMonthly((float) $sub->amount, $sub->cycle)));

        $categories = Category::where('workspace_id', $workspaceId)->orderBy('name')->get();

        $forecastRows = $categories->map(function ($cat) use ($expenses, $monthKeys, $currentMonthExpenses, $subscriptionsByCategory) {
            $subscriptionMonthly = (float) ($subscriptionsByCategory[$cat->id] ?? 0);

            $series = $monthKeys->map(function ($mk) use ($expenses, $cat) {
                return (float) $expenses
                    ->where('category_id', $cat->id)
                    ->filter(fn ($e) => Carbon::parse($e->spent_at)->format('Y-m') === $mk)
                    ->sum('amount');
            })->values()->all();

            if (array_sum($series) <= 0) {
                $currentSpend = (float) $currentMonthExpenses->where('category_id', $cat->id)->sum('amount');

                if ($currentSpend <= 0 && $subscriptionMonthly <= 0) {
                    return null;
                }

                // Sem meses fechados: usa a despesa já registada este mês tal como está (é um
                // registo pontual, não repete todos os meses) e soma as assinaturas garantidas.
                $projected = round($currentSpend + $subscriptionMonthly, 2);

                return [
                    'category' => $cat,
                    'series' => $series,
                    'last' => 0.0,
                    'average' => 0.0,
                    'trend' => 0.0,
                    'predicted' => $projected,
                    'minBand' => round($subscriptionMonthly, 2),
                    'maxBand' => $projected,
                    'confidence' => $currentSpend > 0 ? 30.0 : 60.0,
                    'deltaVsLast' => $projected,
                    'noHistory' => true,
                    'hasSubscriptions' => $subscriptionMonthly > 0,
                ];
            }

            $weighted = $this->weightedAverage($series, $this->recencyWeight);
            $trend = $this->linearTrend($series);
            $last = (float) end($series);

            $predicted = max(0, $weighted + ($trend * 0.8)) + $subscriptionMonthly;
            $minBand = max(0, $predicted - abs($trend) - $subscriptionMonthly) + $subscriptionMonthly;
            $maxBand = $predicted + abs($trend);

            return [
                'category' => $cat,
                'series' => $series,
                'last' => $last,
                'average' => array_sum($series) / max(1, count($series)),
                'trend' => $trend,
                'predicted' => $predicted,
                'minBand' => $minBand,
                'maxBand' => $maxBand,
                'confidence' => $this->confidence($series),
                'deltaVsLast' => $predicted - $last,
                'hasSubscriptions' => $subscriptionMonthly > 0,
            ];
        })->filter()->sortByDesc('predicted')->values();

        // A assinatura da própria plataforma (plano Pro/Business) é um custo garantido mas não
        // existe como registo na tabela de subscriptions, por isso soma-se à parte.
        $user = auth()->user();
        $platformPlanSlug = $user->currentPlanSlug();
        $platformPlanCost = $platformPlanSlug !== 'free'
            ? (float) (SubscriptionPlan::where('slug', $platformPlanSlug)->value('price') ?? 0)
            : 0.0;

        $totalPredicted = (float) $forecastRows->sum('predicted') + $platformPlanCost;
        $totalLast = (float) $forecastRows->sum('last');
        $deltaTotalPct = $totalLast > 0 ? (($totalPredicted - $totalLast) / $totalLast) * 100 : 0;

        $topRisers = $forecastRows->sortByDesc('deltaVsLast')->take(3)->values();
        $topDrops = $forecastRows->sortBy('deltaVsLast')->take(3)->values();

        return view('livewire.expense-forecast-hub', [
            'forecastRows' => $forecastRows,
            'totalPredicted' => $totalPredicted,
            'totalLast' => $totalLast,
            'deltaTotalPct' => $deltaTotalPct,
            'platformPlanCost' => $platformPlanCost,
            'topRisers' => $topRisers,
            'topDrops' => $topDrops,
            'months' => $months,
            'monthKeys' => $monthKeys,
        ]);
    }
}
