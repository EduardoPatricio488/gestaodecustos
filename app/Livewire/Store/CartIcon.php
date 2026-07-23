<?php

namespace App\Livewire\Store;

use App\Services\StoreCartService;
use App\Models\StoreProduct;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCart();
    }

    #[On('cart-updated'), On('cart-item-added'), On('cart-item-removed')]
    public function refreshCart(): void
    {
        $this->count = app(StoreCartService::class)->count();
    }

    public function removeFromCart($productId)
    {
        // 1. Usar o serviço oficial para remover
        app(StoreCartService::class)->remove($productId);

        // 2. Garantir que a sessão 'store_cart' é limpa manualmente
        $cart = session()->get('store_cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('store_cart', $cart);
        }

        $this->dispatch('cart-updated');
        $this->dispatch('toast', text: 'Artigo removido!');
        $this->refreshCart();
    }

    public function render()
    {
        // 1. Buscar os IDs da chave correta: 'store_cart'
        $cart = session()->get('store_cart', []);
        $productIds = array_keys($cart);

        // 2. Buscar os produtos reais para mostrar na aba lateral
        $cartItems = StoreProduct::whereIn('id', $productIds)->get();

        return view('livewire.store.cart-icon', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartItems->sum('price'),
            'count' => $this->count,
        ]);
    }
}
