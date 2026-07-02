<?php

namespace App\Livewire\Store;

use App\Livewire\Store\Concerns\InteractsWithStore;
use App\Services\StoreCartService;
use App\Services\StoreCompareService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProductCompare extends Component
{
    use InteractsWithStore;

    public function remove(int $productId): void
    {
        app(StoreCompareService::class)->remove($productId);
        $this->dispatch('compare-updated');
    }

    public function clear(): void
    {
        app(StoreCompareService::class)->clear();
        $this->dispatch('compare-updated');
    }

    public function render()
    {
        return view('livewire.store.product-compare', [
            'products' => app(StoreCompareService::class)->products(),
            'cartCount' => app(StoreCartService::class)->count(),
        ]);
    }
}
