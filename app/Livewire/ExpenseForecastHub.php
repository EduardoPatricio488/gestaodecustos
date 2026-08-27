<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
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

        $variance = array_sum(array_map(fn($v) => pow($v - $mean, 2), $values)) / $n;
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

        $monthKeys = collect();
        for ($i = $months; $i >= 1; $i--) {
            $monthKeys->push(now()->copy()->subMonths($i)->format('Y-m'));
        }

        $categories = Category::where('workspace_id', $workspaceId)->orderBy('name')->get();

        $forecastRows = $categories->map(function ($cat) use ($expenses, $monthKeys) {
            $series = $monthKeys->map(function ($mk) use ($expenses, $cat) {
                return (float) $expenses
                    ->where('category_id', $cat->id)
                    ->filter(fn($e) => Carbon::parse($e->spent_at)->format('Y-m') === $mk)
                    ->sum('amount');
            })->values()->all();

            if (array_sum($series) <= 0) {
                return null;
            }

            $weighted = $this->weightedAverage($series, $this->recencyWeight);
            $trend = $this->linearTrend($series);
            $last = (float) end($series);

            $predicted = max(0, $weighted + ($trend * 0.8));
            $minBand = max(0, $predicted - abs($trend));
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
            ];
        })->filter()->sortByDesc('predicted')->values();

        $totalPredicted = (float) $forecastRows->sum('predicted');
        $totalLast = (float) $forecastRows->sum('last');
        $deltaTotalPct = $totalLast > 0 ? (($totalPredicted - $totalLast) / $totalLast) * 100 : 0;

        $topRisers = $forecastRows->sortByDesc('deltaVsLast')->take(3)->values();
        $topDrops = $forecastRows->sortBy('deltaVsLast')->take(3)->values();

        return view('livewire.expense-forecast-hub', [
            'forecastRows' => $forecastRows,
            'totalPredicted' => $totalPredicted,
            'totalLast' => $totalLast,
            'deltaTotalPct' => $deltaTotalPct,
            'topRisers' => $topRisers,
            'topDrops' => $topDrops,
            'months' => $months,
            'monthKeys' => $monthKeys,
        ]);
    }
}
