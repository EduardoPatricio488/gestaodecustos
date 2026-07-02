<?php

namespace App\Services;

use App\Models\StoreWishlist;
use Illuminate\Support\Facades\Auth;

class StoreWishlistService
{
    public function ids(): array
    {
        if (! Auth::check()) {
            return [];
        }

        return StoreWishlist::where('user_id', Auth::id())->pluck('product_id')->all();
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    public function toggle(int $productId): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $existing = StoreWishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        StoreWishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);

        return true;
    }
}
