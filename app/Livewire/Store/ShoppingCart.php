<?php

namespace App\Livewire\Store;

use App\Livewire\Store\Concerns\InteractsWithStore;
use App\Services\StoreCartService;
use App\Services\StoreRecommendationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ShoppingCart extends Component
{
    use InteractsWithStore;

    public function updateQuantity(int $productId, int $quantity): void
    {
        app(StoreCartService::class)->setQuantity($productId, $quantity);
        $this->dispatch('cart-updated');
    }

    public function removeItem(int $productId): void
    {
        app(StoreCartService::class)->remove($productId);
        $this->dispatch('cart-updated');
        $this->dispatch('toast', text: 'Produto removido do carrinho.');
    }

    public function clearCart(): void
    {
        app(StoreCartService::class)->clear();
        $this->dispatch('cart-updated');
        $this->dispatch('toast', text: 'Carrinho esvaziado.');
    }

    public function render()
    {
        $cart = app(StoreCartService::class);
        $cartProducts = $cart->items()->pluck('product');

        return view('livewire.store.shopping-cart', [
            'items' => $cart->items(),
            'total' => $cart->total(),
            'count' => $cart->count(),
            'crossSell' => app(StoreRecommendationService::class)->crossSell($cartProducts),
        ]);
    }
}
