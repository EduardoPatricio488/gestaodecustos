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
        if (! $this->canPurchase($productId)) return;
        app(StoreCartService::class)->add($productId);
        $this->dispatch('cart-updated'); $this->dispatch('cart-item-added'); $this->dispatch('toast', text: 'Produto adicionado ao carrinho!');
    }

    public function buyNow(int $productId)
    {
        if (! $this->canPurchase($productId)) return;
        $cart = app(StoreCartService::class); $cart->clear(); $cart->add($productId);
        $this->dispatch('cart-updated'); $this->dispatch('cart-item-added');
        return redirect()->route('store.checkout');
    }

    public function validateStoreCart(): bool
    {
        $cart = app(StoreCartService::class);
        foreach ($cart->items() as $item) {
            if (! $this->canPurchase($item['product']->id, false)) return false;
        }
        return $cart->count() > 0;
    }

    private function canPurchase(int $productId, bool $notify = true): bool
    {
        $user = Auth::user();
        $product = StoreProduct::query()->where('is_active', true)->findOrFail($productId);
        $message = null;

        if (! $user) $message = 'Inicia sessão para comprar produtos.';
        elseif ($product->type === 'plan') $message = 'Os planos são geridos na área de subscrição.';
        elseif (app(StoreCartService::class)->isOwned($productId)) $message = 'Já tens este produto no teu inventário.';
        elseif ($product->requires_business_plan && ! $user->isBusinessPlan()) $message = 'Este produto requer o plano Business.';
        elseif (($product->audience ?? 'both') === 'business' && ! $user->isBusinessPlan()) $message = 'Este produto está disponível apenas para negócios.';

        if ($message && $notify) $this->dispatch('toast', text: $message);
        return $message === null;
    }

    public function toggleWishlist(int $productId): void
    {
        $added = app(StoreWishlistService::class)->toggle($productId);
        $this->dispatch('wishlist-updated'); $this->dispatch('toast', text: $added ? 'Adicionado aos favoritos!' : 'Removido dos favoritos.');
    }

    public function addToCompare(int $productId): void
    {
        $compare = app(StoreCompareService::class);
        if (! $compare->add($productId)) { $this->dispatch('toast', text: 'Máximo de 4 produtos para comparar.'); return; }
        $this->dispatch('compare-updated'); $this->dispatch('toast', text: 'Produto adicionado à comparação.');
    }
}
