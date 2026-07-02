<?php

namespace App\Services;

use App\Models\StoreProduct;
use App\Models\StorePurchase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StoreAdminService
{
    public function overviewStats(): array
    {
        $completed = StorePurchase::query()->where('payment_status', 'completed');

        return [
            'total_revenue' => (float) (clone $completed)->sum('amount_paid'),
            'total_purchases' => (clone $completed)->count(),
            'purchases_today' => (clone $completed)->whereDate('created_at', today())->count(),
            'revenue_today' => (float) (clone $completed)->whereDate('created_at', today())->sum('amount_paid'),
            'purchases_month' => (clone $completed)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'revenue_month' => (float) (clone $completed)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount_paid'),
            'unique_buyers' => (clone $completed)->distinct('user_id')->count('user_id'),
            'avg_order' => (float) ((clone $completed)->avg('amount_paid') ?? 0),
        ];
    }

    public function topProducts(int $limit = 8): Collection
    {
        return StoreProduct::query()
            ->withCount(['purchases as real_sales' => fn ($q) => $q->where('payment_status', 'completed')])
            ->orderByDesc('real_sales')
            ->orderByDesc('sales_count')
            ->orderByDesc('rating_avg')
            ->limit($limit)
            ->get();
    }

    public function salesByType(): Collection
    {
        return StorePurchase::query()
            ->join('store_products', 'store_purchases.product_id', '=', 'store_products.id')
            ->where('store_purchases.payment_status', 'completed')
            ->select(
                'store_products.type',
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('COUNT(DISTINCT store_products.id) as products_count'),
            )
            ->groupBy('store_products.type')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function purchasesTrend(int $days = 14): Collection
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $rows = StorePurchase::query()
            ->where('payment_status', 'completed')
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count, SUM(amount_paid) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        return collect(range(0, $days - 1))->map(function (int $offset) use ($from, $rows) {
            $day = $from->copy()->addDays($offset)->toDateString();

            return [
                'day' => $day,
                'label' => $from->copy()->addDays($offset)->format('d/m'),
                'count' => (int) ($rows[$day]->count ?? 0),
                'revenue' => (float) ($rows[$day]->revenue ?? 0),
            ];
        });
    }

    public function productTypes(): array
    {
        return [
            'ia' => 'Extensão IA',
            'widget' => 'Widget',
            'automation' => 'Automação',
            'data' => 'Dados PRO',
            'course' => 'Curso',
            'guide' => 'Guia PDF',
            'pack' => 'Pack Digital',
            'plan' => 'Plano',
        ];
    }
}
