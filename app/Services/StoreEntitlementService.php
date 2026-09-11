<?php

namespace App\Services;

use App\Models\StoreProduct;
use App\Models\StorePurchase;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StoreEntitlementService
{
    private const LEGACY_WIDGET_SLUGS = [
        'mercado-global' => 'widget-mercado-global-pro',
        'energia-commodities' => 'widget-energia-commodities',
        'macro-data' => 'dados-macroeconomicos-pro',
    ];

    public function userOwns(User $user, string $slug): bool
    {
        return StorePurchase::query()
            ->where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->whereHas('product', fn ($q) => $q->where('slug', $slug))
            ->exists();
    }

    public function ownsProduct(User $user, StoreProduct|int $product): bool
    {
        $productId = $product instanceof StoreProduct ? $product->id : $product;

        return StorePurchase::query()
            ->where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('payment_status', 'completed')
            ->exists();
    }

    public function hasEntitlement(User $user, string $key): bool
    {
        $key = self::LEGACY_WIDGET_SLUGS[$key] ?? $key;

        if ($this->userOwns($user, $key)) return true;

        return StorePurchase::query()
            ->where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->whereHas('product', function ($q) use ($key) {
                $q->where('entitlement_key', $key)
                    ->orWhereHas('entitlements', fn ($e) => $e->where('key', $key)->where('is_active', true));
            })
            ->exists();
    }

    public function hasWidget(User $user, string $widgetKey): bool
    {
        return $this->hasEntitlement($user, $widgetKey);
    }

    public function ownedProducts(User $user): Collection
    {
        return StoreProduct::query()
            ->whereIn('id', StorePurchase::query()
                ->where('user_id', $user->id)
                ->where('payment_status', 'completed')
                ->select('product_id'))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['id', 'slug', 'type', 'title', 'image', 'delivery_type', 'integration_route', 'integration_label']);
    }

    public function ownedSlugs(User $user): array
    {
        return Cache::remember("store:owned-slugs:{$user->id}", 300, function () use ($user) {
            return StorePurchase::query()
                ->where('user_id', $user->id)
                ->where('payment_status', 'completed')
                ->with('product:id,slug')
                ->get()
                ->pluck('product.slug')
                ->filter()
                ->unique()
                ->values()
                ->all();
        });
    }

    public function clearCache(User $user): void
    {
        Cache::forget("store:owned-slugs:{$user->id}");
    }

    public function templatesForStore(): Collection
    {
        return StoreProduct::whereIn('type', ['guide', 'template', 'pack', 'widget'])
            ->where(function ($q) {
                $q->where('slug', 'like', 'template-%')
                    ->orWhere('slug', 'like', 'pack-%')
                    ->orWhere('type', 'widget')
                    ->orWhere('type', 'template');
            })
            ->orderBy('price')
            ->get();
    }
}
