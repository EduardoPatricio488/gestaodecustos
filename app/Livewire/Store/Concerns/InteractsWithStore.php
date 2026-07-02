<?php

namespace App\Livewire\Store\Concerns;

use App\Services\StoreCartService;
use App\Services\StoreCompareService;
use App\Services\StoreWishlistService;

trait InteractsWithStore
{
    public function addToCart(int $productId): void
    {
        $cart = app(StoreCartService::class);

        if ($cart->isOwned($productId)) {
            $this->dispatch('toast', text: 'Já tens este produto no inventário.');

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

        if ($cart->isOwned($productId)) {
            $this->dispatch('toast', text: 'Já tens este produto no inventário.');

            return;
        }

        $cart->clear();
        $cart->add($productId);
        $this->dispatch('cart-updated');
        $this->dispatch('cart-item-added');

        return redirect()->route('store.checkout');
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
