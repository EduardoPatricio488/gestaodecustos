<?php

namespace App\Livewire\Store;

use App\Models\StorePurchase;
use App\Services\StoreEntitlementService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class UserInventory extends Component
{
    public function render()
    {
        $purchases = StorePurchase::with(['product', 'license'])
            ->where('user_id', Auth::id())
            ->where('payment_status', 'completed')
            ->latest()
            ->get()
            ->unique('product_id')
            ->values();

        return view('livewire.store.user-inventory', [
            'items' => $purchases,
            'totalSpent' => StorePurchase::where('user_id', Auth::id())->where('payment_status', 'completed')->sum('amount_paid'),
            'totalItems' => $purchases->count(),
            'entitlements' => app(StoreEntitlementService::class)->ownedProducts(Auth::user()),
        ]);
    }
}
