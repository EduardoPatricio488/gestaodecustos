<?php

namespace App\Services;

use App\Models\StoreProduct;
use App\Models\StorePurchase;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StoreEntitlementService
{
    private const WIDGET_SLUGS = [
        'mercado-global' => 'widget-mercado-global-pro',
        'energia-commodities' => 'widget-energia-commodities',
        'macro-data' => 'dados-macroeconomicos-pro',
    ];

    public function userOwns(User $user, string $slug): bool
    {
        return StorePurchase::where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->whereHas('product', fn ($q) => $q->where('slug', $slug))
            ->exists();
    }

    public function hasWidget(User $user, string $widgetKey): bool
    {
        $slug = self::WIDGET_SLUGS[$widgetKey] ?? $widgetKey;

        return $this->userOwns($user, $slug);
    }

    public function ownedProducts(User $user): Collection
    {
        $slugs = $this->ownedSlugs($user);

        if ($slugs === []) {
            return collect();
        }

        return StoreProduct::query()
            ->whereIn('slug', $slugs)
            ->get(['id', 'slug', 'type', 'title']);
    }

    public function ownedSlugs(User $user): array
    {
        return Cache::remember("store:owned-slugs:{$user->id}", 300, function () use ($user) {
            return StorePurchase::query()
                ->where('user_id', $user->id)
                ->where('payment_status', 'completed')
                ->whereHas('product')
                ->with('product:id,slug')
                ->get()
                ->pluck('product.slug')
                ->filter()
                ->values()
                ->all();
        });
    }

    public function clearCache(User $user): void
    {
        Cache::forget("store:owned:{$user->id}");
        Cache::forget("store:owned-slugs:{$user->id}");
    }

    public function templatesForStore(): Collection
    {
        return StoreProduct::whereIn('type', ['guide', 'pack', 'widget'])
            ->where(function ($q) {
                $q->where('slug', 'like', 'template-%')
                    ->orWhere('slug', 'like', 'pack-%')
                    ->orWhere('type', 'widget');
            })
            ->orderBy('price')
            ->get();
    }
}
