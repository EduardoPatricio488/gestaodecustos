<?php

namespace App\Livewire\Store;

use App\Models\StorePurchase;
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
            ->get();

        $totalSpent = $purchases->sum('amount_paid');

        return view('livewire.store.user-inventory', [
            'items' => $purchases,
            'totalSpent' => $totalSpent,
            'totalItems' => $purchases->count(),
        ]);
    }
}
