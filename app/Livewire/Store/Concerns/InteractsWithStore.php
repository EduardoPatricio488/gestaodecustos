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

        if (! $this->canPurchase($productId)) return;

        $cart->add($productId);
        $this->dispatch('cart-updated');
        $this->dispatch('cart-item-added');
        $this->dispatch('toast', text: 'Produto adicionado ao carrinho!');
    }

    public function buyNow(int $productId)
    {
        $cart = app(StoreCartService::class);

        if (! $this->canPurchase($productId)) return;

        $cart->clear();
        $cart->add($productId);
        $this->dispatch('cart-updated');
        $this->dispatch('cart-item-added');

        return redirect()->route('store.checkout');
    }

    private function canPurchase(int $productId): bool
    {
        $user = Auth::user();
        $product = StoreProduct::query()->where('is_active', true)->findOrFail($productId);

        if (! $user) {
            $this->dispatch('toast', text: 'Inicia sessão para comprar produtos.');
            return false;
        }

        if ($product->type === 'plan') {
            $this->dispatch('toast', text: 'Os planos são geridos na área de subscrição.');
            return false;
        }

        if (app(StoreCartService::class)->isOwned($productId)) {
            $this->dispatch('toast', text: 'Já tens este produto no teu inventário.');
            return false;
        }

        if ($product->requires_business_plan && ! $user->isBusinessPlan()) {
            $this->dispatch('toast', text: 'Este produto requer o plano Business.');
            return false;
        }

        if (($product->audience ?? 'both') === 'business' && ! $user->isBusinessPlan()) {
            $this->dispatch('toast', text: 'Este produto está disponível apenas para negócios.');
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
