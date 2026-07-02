<?php

namespace App\Livewire\Store;

use App\Services\StoreCartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('cart-updated')]
    #[On('cart-item-added')]
    public function refreshCount(): void
    {
        $this->count = app(StoreCartService::class)->count();
    }

    public function render()
    {
        return view('livewire.store.cart-icon');
    }
}
