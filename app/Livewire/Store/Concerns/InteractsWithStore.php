<?php

namespace App\Livewire\Store\Concerns;

use App\Models\StoreProduct;
use App\Services\StoreCartService;
use App\Services\StoreCompareService;
use App\Services\StoreWishlistService;
use Illuminate\Support\Facades\Auth;

trait InteractsWithStore
{
    public function addToCart(int $productId): void
    {
        $cart = app(StoreCartService::class);

        if (! $this->canPurchase($productId)) {

            return;
        }

        $cart->add($productId);
        $this->dispatch('cart-updated');
        $this->dispatch('cart-item-added');
        $this->dispatch('toast', text: 'Produto adicionado ao carrinho!');
    }

    public function buyNow(int $productId)
    {
        $cart = app(StoreCartService::class);

        if (! $this->canPurchase($productId)) {

            return;
        }

        $cart->clear();
        $cart->add($productId);
        $this->dispatch('cart-updated');
        $this->dispatch('cart-item-added');

        return redirect()->route('store.checkout');
    }

    private function canPurchase(int $productId): bool
    {
        $product = StoreProduct::findOrFail($productId);

        if ($product->requires_business_plan && ! (Auth::user()?->isBusinessPlan() ?? false)) {
            $this->dispatch('toast', text: 'Este produto requer o plano Business.');

            return false;
        }

        return true;
    }

    public function toggleWishlist(int $productId): void
    {
        $added = app(StoreWishlistService::class)->toggle($productId);
        $this->dispatch('wishlist-updated');
        $this->dispatch('toast', text: $added ? 'Adicionado aos favoritos!' : 'Removido dos favoritos.');
    }

    public function addToCompare(int $productId): void
    {
        $compare = app(StoreCompareService::class);

        if (! $compare->add($productId)) {
            $this->dispatch('toast', text: 'Máximo de 4 produtos para comparar.');

            return;
        }

        $this->dispatch('compare-updated');
        $this->dispatch('toast', text: 'Produto adicionado à comparação.');
    }
}
