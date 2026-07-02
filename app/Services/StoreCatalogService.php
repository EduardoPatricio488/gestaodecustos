<?php

namespace App\Services;

use App\Models\StoreProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StoreCatalogService
{
    private const CACHE_KEY = 'store.catalog.products';
    private const CACHE_TTL = 3600;

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function allProducts(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return StoreProduct::orderBy('type')->orderBy('title')->get();
        });
    }

    public function filter(array $filters): Collection
    {
        $query = StoreProduct::query();

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    public function applyFilters(Builder $query, array $filters): Builder
    {
        $tab = $filters['tab'] ?? 'all';
        $search = trim($filters['search'] ?? '');
        $priceMin = $filters['priceMin'] ?? null;
        $priceMax = $filters['priceMax'] ?? null;
        $sortBy = $filters['sortBy'] ?? 'popular';
        $onlyFeatured = (bool) ($filters['onlyFeatured'] ?? false);

        if ($tab === 'all') {
            $query->where('type', '!=', 'plan');
        } else {
            $query->where('type', $tab);
        }

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($priceMin !== null && $priceMin !== '') {
            $query->where('price', '>=', (float) $priceMin);
        }

        if ($priceMax !== null && $priceMax !== '') {
            $query->where('price', '<=', (float) $priceMax);
        }

        if ($onlyFeatured) {
            $query->where('is_featured', true);
        }

        match ($sortBy) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating_avg'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('sales_count')->orderByDesc('rating_avg'),
        };

        return $query->orderBy('title');
    }
}
