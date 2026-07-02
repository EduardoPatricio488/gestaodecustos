<?php

namespace App\Livewire\Store;

use App\Livewire\Store\Concerns\InteractsWithStore;
use App\Models\StoreWishlist;
use App\Services\StoreCartService;
use App\Services\StoreCompareService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class WishlistHub extends Component
{
    use InteractsWithStore;

    public function removeFromWishlist(int $productId): void
    {
        StoreWishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();

        $this->dispatch('wishlist-updated');
        $this->dispatch('toast', text: 'Removido dos favoritos.');
    }

    public function render()
    {
        $items = StoreWishlist::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('livewire.store.wishlist-hub', [
            'items' => $items,
            'cartCount' => app(StoreCartService::class)->count(),
            'compareCount' => app(StoreCompareService::class)->count(),
        ]);
    }
}
