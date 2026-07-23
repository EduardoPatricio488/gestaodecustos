<?php

namespace App\Livewire\Store;

use App\Livewire\Store\Concerns\InteractsWithStore;
use App\Models\StoreBundle;
use App\Models\StoreProduct;
use App\Models\StorePurchase;
use App\Services\StoreCartService;
use App\Services\StoreCatalogService;
use App\Services\StoreCompareService;
use App\Services\StoreWishlistService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class HubStore extends Component
{
    use InteractsWithStore;

    #[Url(as: 'tab')]
    public string $activeTab = 'all';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'min')]
    public ?string $priceMin = null;

    #[Url(as: 'max')]
    public ?string $priceMax = null;

    #[Url(as: 'sort')]
    public string $sortBy = 'popular';

    public bool $onlyFeatured = false;

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function updatedSearch(): void {}

    public function updatedPriceMin(): void {}

    public function updatedPriceMax(): void {}

    public function updatedSortBy(): void {}

    public function updatedOnlyFeatured(): void {}

    public function clearFilters(): void
    {
        $this->search = '';
        $this->priceMin = null;
        $this->priceMax = null;
        $this->sortBy = 'popular';
        $this->onlyFeatured = false;
    }

 public function render()
{
    $catalog = app(StoreCatalogService::class);
    $query = StoreProduct::query();

    $catalog->applyFilters($query, [
        'tab' => $this->activeTab,
        'search' => $this->search,
        'priceMin' => $this->priceMin,
        'priceMax' => $this->priceMax,
        'sortBy' => $this->sortBy,
        'onlyFeatured' => $this->onlyFeatured,
    ]);

    // --- BUSCAR DADOS DO CARRINHO DIRETAMENTE DA SESSÃO ---
    $cartItems = session()->get('cart', []);
    $cartTotal = collect($cartItems)->sum('price');
    // ----------------------------------------------------

    return view('livewire.store.hub-store', [
        'products' => $query->get(),
        'planProducts' => StoreProduct::where('type', 'plan')->orderBy('price')->get(),
        'bundles' => StoreBundle::where('is_active', true)->with('products')->get(),

        // Passamos as variáveis que o Blade (aba lateral) precisa
        'cartItems' => $cartItems,
        'cartTotal' => $cartTotal,
        'cartCount' => count($cartItems),

        'wishlistIds' => app(StoreWishlistService::class)->ids(),
        'compareCount' => app(StoreCompareService::class)->count(),
        'inventoryCount' => Auth::check()
            ? StorePurchase::where('user_id', Auth::id())->where('payment_status', 'completed')->count()
            : 0,
    ]);
}
}
