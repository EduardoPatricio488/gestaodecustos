<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\PriceHistory;
use App\Models\Workspace;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class PriceComparisonService
{
    public function recordFromExpense(Expense $expense, Category $category): void
    {
        $unit = $this->extractUnitPrice($expense);
        if (! $unit) {
            return;
        }

        PriceHistory::create([
            'workspace_id' => $expense->workspace_id,
            'category_id' => $category->id,
            'expense_id' => $expense->id,
            'unit_price' => $unit['price'],
            'unit_type' => $unit['type'],
            'merchant' => $unit['merchant'],
            'recorded_at' => $expense->spent_at,
        ]);
    }

    public function getAnalysis(Workspace $workspace, Category $category, ?CarbonInterface $month = null): array
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $prevStart = $month->copy()->subMonth()->startOfMonth();
        $prevEnd = $month->copy()->subMonth()->endOfMonth();

        $history = PriceHistory::where('workspace_id', $workspace->id)
            ->where('category_id', $category->id)
            ->orderByDesc('recorded_at')
            ->limit(30)
            ->get();

        $thisMonth = $history->filter(fn ($h) => $h->recorded_at->between($start, $end));
        $lastMonth = PriceHistory::where('workspace_id', $workspace->id)
            ->where('category_id', $category->id)
            ->whereBetween('recorded_at', [$prevStart, $prevEnd])
            ->get();

        $avgThis = $thisMonth->avg('unit_price');
        $avgLast = $lastMonth->avg('unit_price');

        $priceChange = ($avgLast > 0 && $avgThis > 0)
            ? round((($avgThis - $avgLast) / $avgLast) * 100, 1)
            : null;

        $spentThis = (float) Expense::where('workspace_id', $workspace->id)
            ->where('category_id', $category->id)
            ->whereBetween('spent_at', [$start, $end])
            ->sum('amount');

        $spentLast = (float) Expense::where('workspace_id', $workspace->id)
            ->where('category_id', $category->id)
            ->whereBetween('spent_at', [$prevStart, $prevEnd])
            ->sum('amount');

        $insight = $this->buildInsight($category->name, $priceChange, $spentThis, $spentLast);

        return [
            'history' => $history,
            'avg_this_month' => $avgThis ? round($avgThis, 3) : null,
            'avg_last_month' => $avgLast ? round($avgLast, 3) : null,
            'price_change_pct' => $priceChange,
            'spent_this' => $spentThis,
            'spent_last' => $spentLast,
            'by_merchant' => $this->merchantBreakdown($history),
            'insight' => $insight,
        ];
    }

    private function extractUnitPrice(Expense $expense): ?array
    {
        $meta = is_array($expense->metadata) ? $expense->metadata : [];

        if (! empty($meta['preco_litro'])) {
            return [
                'price' => (float) $meta['preco_litro'],
                'type' => 'litro',
                'merchant' => $meta['local'] ?? $expense->subcategory,
            ];
        }

        if (! empty($meta['preco_unitario'])) {
            return [
                'price' => (float) $meta['preco_unitario'],
                'type' => 'unidade',
                'merchant' => $meta['supermercado'] ?? $expense->subcategory,
            ];
        }

        $desc = strtolower($expense->description.' '.$expense->subcategory);

        if (preg_match('/(\d+[,.]?\d*)\s*l(?:itros?)?/i', $desc, $m) && (float) $expense->amount > 0) {
            $liters = (float) str_replace(',', '.', $m[1]);
            if ($liters > 0) {
                return [
                    'price' => round((float) $expense->amount / $liters, 3),
                    'type' => 'litro',
                    'merchant' => $expense->subcategory,
                ];
            }
        }

        if (preg_match('/(\d+)\s*(?:un|x|unidades?)/i', $desc, $m) && (float) $expense->amount > 0) {
            $units = (int) $m[1];
            if ($units > 0) {
                return [
                    'price' => round((float) $expense->amount / $units, 3),
                    'type' => 'unidade',
                    'merchant' => $expense->subcategory,
                ];
            }
        }

        return null;
    }

    private function merchantBreakdown(Collection $history): array
    {
        return $history->groupBy('merchant')
            ->map(fn ($items, $merchant) => [
                'merchant' => $merchant ?: 'Outro',
                'avg_price' => round($items->avg('unit_price'), 3),
                'count' => $items->count(),
                'last_price' => round($items->first()->unit_price, 3),
            ])
            ->sortByDesc('count')
            ->values()
            ->take(5)
            ->all();
    }

    private function buildInsight(string $category, ?float $priceChange, float $spentThis, float $spentLast): ?string
    {
        if ($priceChange !== null && $priceChange >= 15 && $spentThis > $spentLast) {
            return "Este mês gastaste mais em {$category} porque o preço unitário subiu {$priceChange}%.";
        }
        if ($priceChange !== null && $priceChange <= -10) {
            return "Boa notícia: o preço em {$category} baixou ".abs($priceChange).'% vs mês passado.';
        }
        if ($spentLast > 0 && $spentThis > $spentLast * 1.2) {
            $pct = round((($spentThis - $spentLast) / $spentLast) * 100);

            return "Gastaste {$pct}% mais em {$category} este mês — verifica se foi volume ou preço.";
        }

        return null;
    }
}
