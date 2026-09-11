<?php

namespace App\Services;

use App\Models\StoreProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StoreCartService
{
    private const SESSION_KEY = 'store_cart';

    public function items(): Collection
    {
        $ids = array_keys($this->raw());
        if (empty($ids)) return collect();

        $products = StoreProduct::whereIn('id', $ids)->where('is_active', true)->get()->keyBy('id');

        return collect($this->raw())->map(function (int $quantity, int $productId) use ($products) {
            $product = $products->get($productId);
            if (! $product) return null;
            return ['product' => $product, 'quantity' => 1, 'subtotal' => (float) $product->price];
        })->filter()->values();
    }

    public function count(): int { return $this->items()->count(); }
    public function total(): float { return (float) $this->items()->sum('subtotal'); }

    public function add(int $productId, int $quantity = 1): void
    {
        StoreProduct::query()->where('is_active', true)->findOrFail($productId);
        $cart = $this->raw();
        $cart[$productId] = 1;
        $this->save($cart);
    }

    public function setQuantity(int $productId, int $quantity): void
    {
        $cart = $this->raw();
        if ($quantity <= 0) unset($cart[$productId]);
        else $cart[$productId] = 1;
        $this->save($cart);
    }

    public function remove(int $productId): void { $cart = $this->raw(); unset($cart[$productId]); $this->save($cart); }
    public function clear(): void { session()->forget(self::SESSION_KEY); }

    public function isOwned(int $productId): bool
    {
        if (! Auth::check()) return false;
        return Auth::user()->storePurchases()->where('product_id', $productId)->where('payment_status', 'completed')->exists();
    }

    private function raw(): array { return (array) session(self::SESSION_KEY, []); }
    private function save(array $cart): void { session([self::SESSION_KEY => $cart]); }
}
