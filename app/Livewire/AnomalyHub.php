<?php

namespace App\Livewire;

use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AnomalyHub extends Component
{
    public int $lookbackMonths = 6;
    public float $sensitivity = 2.8;
    public float $minMultiplier = 1.5;

    private function median(array $values): float
    {
        if (empty($values)) {
            return 0.0;
        }

        sort($values);
        $count = count($values);
        $mid = intdiv($count, 2);

        if ($count % 2 === 0) {
            return ((float) $values[$mid - 1] + (float) $values[$mid]) / 2;
        }

        return (float) $values[$mid];
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $startCurrentMonth = now()->startOfMonth();
        $startHistory = now()->copy()->subMonths($this->lookbackMonths)->startOfMonth();

        $history = Expense::with('category')
            ->where('workspace_id', $workspaceId)
            ->where('spent_at', '>=', $startHistory)
            ->where('spent_at', '<', $startCurrentMonth)
            ->get();

        $currentMonth = Expense::with('category')
            ->where('workspace_id', $workspaceId)
            ->where('spent_at', '>=', $startCurrentMonth)
            ->latest('spent_at')
            ->get();

        $historyByCategory = $history
            ->groupBy(fn ($e) => $e->category_id ?? 0)
            ->map(fn ($items) => $items->pluck('amount')->map(fn ($v) => (float) $v)->values()->all());

        $anomalies = collect();

        foreach ($currentMonth as $expense) {
            $catKey = $expense->category_id ?? 0;
            $samples = $historyByCategory->get($catKey, []);

            if (count($samples) < 3) {
                continue;
            }

            $median = $this->median($samples);
            if ($median <= 0) {
                continue;
            }

            $deviations = array_map(fn ($v) => abs($v - $median), $samples);
            $mad = $this->median($deviations);

            $robustSigma = $mad > 0 ? ($mad * 1.4826) : max(1.0, $median * 0.15);
            $score = (((float) $expense->amount) - $median) / $robustSigma;

            $isAnomaly = $score >= $this->sensitivity
                && ((float) $expense->amount) >= ($median * $this->minMultiplier);

            if (! $isAnomaly) {
                continue;
            }

            $anomalies->push([
                'id' => $expense->id,
                'date' => $expense->spent_at,
                'description' => $expense->description,
                'category' => $expense->category?->name ?? 'Sem categoria',
                'amount' => (float) $expense->amount,
                'median' => $median,
                'score' => $score,
                'delta' => ((float) $expense->amount) - $median,
            ]);
        }

        $anomalies = $anomalies->sortByDesc('score')->values();

        $prev3Months = collect(range(1, 3))->map(function ($i) use ($workspaceId) {
            $date = now()->copy()->subMonths($i);
            return (float) Expense::where('workspace_id', $workspaceId)
                ->whereYear('spent_at', $date->year)
                ->whereMonth('spent_at', $date->month)
                ->sum('amount');
        });

        $currentTotal = (float) $currentMonth->sum('amount');
        $baselineTotal = (float) $prev3Months->avg();
        $monthlySpikePct = $baselineTotal > 0 ? (($currentTotal - $baselineTotal) / $baselineTotal) * 100 : 0;

        return view('livewire.anomaly-hub', [
            'anomalies' => $anomalies,
            'currentTotal' => $currentTotal,
            'baselineTotal' => $baselineTotal,
            'monthlySpikePct' => $monthlySpikePct,
            'lookbackMonths' => $this->lookbackMonths,
        ]);
    }
}
