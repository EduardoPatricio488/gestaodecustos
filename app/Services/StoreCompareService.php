<?php

namespace App\Services;

use App\Models\StoreProduct;

class StoreCompareService
{
    private const SESSION_KEY = 'store_compare';
    private const MAX_ITEMS = 4;

    public function ids(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function products()
    {
        $ids = $this->ids();

        if (empty($ids)) {
            return collect();
        }

        return StoreProduct::whereIn('id', $ids)->get()->sortBy(fn ($p) => array_search($p->id, $ids));
    }

    public function add(int $productId): bool
    {
        StoreProduct::findOrFail($productId);
        $ids = $this->ids();

        if (in_array($productId, $ids, true)) {
            return true;
        }

        if (count($ids) >= self::MAX_ITEMS) {
            return false;
        }

        $ids[] = $productId;
        session([self::SESSION_KEY => $ids]);

        return true;
    }

    public function remove(int $productId): void
    {
        $ids = array_values(array_filter($this->ids(), fn ($id) => $id !== $productId));
        session([self::SESSION_KEY => $ids]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
