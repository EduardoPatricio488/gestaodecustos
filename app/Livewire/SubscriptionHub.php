<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subscription;
use App\Models\Category;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SubscriptionHub extends Component
{
    public $name, $amount, $category_id, $billing_day;
    public $billing_cycle = 'monthly';
    public $payment_method, $status = 'active', $started_at, $renewal_date, $notes;
    public bool $notify_before_billing = false;
    public $notify_days_before;
    public $showModal = false;

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
            'category_id' => 'required',
            'billing_day' => 'required|integer|between:1,31',
            'billing_cycle' => 'nullable|in:monthly,quarterly,semiannual,annual',
            'status' => 'nullable|in:active,paused,cancelled',
        ]);

        Subscription::create([
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'billing_day' => $this->billing_day,
            'cycle' => $this->billing_cycle ?: 'monthly',
            'is_active' => ($this->status ?: 'active') === 'active',
        ]);

        $this->reset([
            'name',
            'amount',
            'category_id',
            'billing_day',
            'payment_method',
            'started_at',
            'renewal_date',
            'notes',
            'notify_days_before',
        ]);

        $this->billing_cycle = 'monthly';
        $this->status = 'active';
        $this->notify_before_billing = false;
        $this->dispatch('modal-close-add-sub');
    }

    public function delete($id)
    {
        Subscription::where('user_id', auth()->id())->find($id)->delete();
    }

    public function render()
    {
        $subs = Subscription::where('user_id', auth()->id())->with('category')->orderBy('billing_day')->get();

        $totalMonthly = $subs->where('is_active', true)->sum('amount');

        // Calcula o que já foi "pago" (dia atual > dia billing) e o que falta
        $alreadyPaid = $subs->where('billing_day', '<', now()->day)->sum('amount');
        $upcoming = $totalMonthly - $alreadyPaid;

        return view('livewire.subscription-hub', [
            'subscriptions' => $subs,
            'totalMonthly' => $totalMonthly,
            'upcoming' => $upcoming,
            'categories' => Category::where('user_id', auth()->id())->get()
        ]);
    }
}
