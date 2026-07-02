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

        if (empty($ids)) {
            return collect();
        }

        $products = StoreProduct::whereIn('id', $ids)->get()->keyBy('id');

        return collect($this->raw())
            ->map(function (int $quantity, int $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function total(): float
    {
        return $this->items()->sum('subtotal');
    }

    public function add(int $productId, int $quantity = 1): void
    {
        StoreProduct::findOrFail($productId);

        $cart = $this->raw();
        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
        $this->save($cart);
    }

    public function setQuantity(int $productId, int $quantity): void
    {
        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $this->save($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        $this->save($cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function isOwned(int $productId): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()
            ->storePurchases()
            ->where('product_id', $productId)
            ->where('payment_status', 'completed')
            ->exists();
    }

    private function raw(): array
    {
        return session(self::SESSION_KEY, []);
    }

    private function save(array $cart): void
    {
        session([self::SESSION_KEY => $cart]);
    }
}
