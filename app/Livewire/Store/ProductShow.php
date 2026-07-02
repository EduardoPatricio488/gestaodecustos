<?php

namespace App\Livewire\Store;

use App\Livewire\Store\Concerns\InteractsWithStore;
use App\Models\StoreProduct;
use App\Models\StorePurchase;
use App\Models\StoreReview;
use App\Services\StoreCartService;
use App\Services\StoreCompareService;
use App\Services\StorePurchaseService;
use App\Services\StoreRecommendationService;
use App\Services\StoreWishlistService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProductShow extends Component
{
    use InteractsWithStore;

    public StoreProduct $product;

    public bool $alreadyOwned = false;

    public int $reviewRating = 5;

    public string $reviewComment = '';

    public function mount(StoreProduct $product): void
    {
        $this->product = $product->load(['reviews.user']);
        $this->alreadyOwned = app(StoreCartService::class)->isOwned($product->id);

        app(StorePurchaseService::class)->logActivity('store_product_view', "Viu produto: {$product->title}", [
            'product_id' => $product->id,
        ]);
    }

    public function submitReview(): void
    {
        if (! $this->alreadyOwned) {
            $this->dispatch('toast', text: 'Só quem comprou pode avaliar.');

            return;
        }

        $this->validate([
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewComment' => 'nullable|string|max:1000',
        ]);

        StoreReview::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $this->product->id],
            ['rating' => $this->reviewRating, 'comment' => $this->reviewComment, 'is_approved' => true]
        );

        app(StorePurchaseService::class)->updateProductRating($this->product->id);
        $this->product->refresh();

        $this->dispatch('toast', text: 'Avaliação publicada!');
    }

    public function render()
    {
        $recommendations = app(StoreRecommendationService::class);

        return view('livewire.store.product-show', [
            'cartCount' => app(StoreCartService::class)->count(),
            'inWishlist' => app(StoreWishlistService::class)->has($this->product->id),
            'compareCount' => app(StoreCompareService::class)->count(),
            'ownedPurchase' => $this->alreadyOwned
                ? StorePurchase::where('user_id', Auth::id())
                    ->where('product_id', $this->product->id)
                    ->where('payment_status', 'completed')
                    ->first()
                : null,
            'reviews' => $this->product->reviews()->where('is_approved', true)->latest()->get(),
            'aiExplanation' => $recommendations->aiExplainProduct($this->product),
            'relatedProducts' => $this->product->relatedProductsList()->isNotEmpty()
                ? $this->product->relatedProductsList()
                : StoreProduct::where('type', $this->product->type)
                    ->where('id', '!=', $this->product->id)
                    ->orderByDesc('sales_count')
                    ->limit(3)
                    ->get(),
        ]);
    }
}
