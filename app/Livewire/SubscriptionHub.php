<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SubscriptionHub extends Component
{
    public $name, $amount, $category_id, $billing_day;
    public $billing_cycle = 'monthly';
    public $payment_method, $status = 'active', $started_at, $renewal_date, $notes;
    public bool $notify_before_billing = false;
    public $notify_days_before;
    public $showModal = false;

    public string $search = '';
    public string $categoryFilter = 'all';
    public string $statusFilter = 'all';
    public string $cycleFilter = 'all';
    public string $amountFilter = 'all';
    public string $sortBy = 'billing_day';

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
            'category_id' => 'required',
            'billing_day' => 'required|integer|between:1,31',
            'billing_cycle' => 'nullable|in:monthly,quarterly,semiannual,annual',
            'status' => 'nullable|in:active,paused,cancelled',
            'payment_method' => 'nullable|in:card,direct_debit,bank_transfer,paypal,cash',
            'started_at' => 'nullable|date',
            'renewal_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'notify_before_billing' => 'boolean',
            'notify_days_before' => 'nullable|integer|between:1,30',
        ]);

        Subscription::create([
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'billing_day' => $this->billing_day,
            'cycle' => $this->billing_cycle ?: 'monthly',
            'is_active' => ($this->status ?: 'active') === 'active',
            'payment_method' => $this->payment_method,
            'status' => $this->status ?: 'active',
            'started_at' => $this->started_at,
            'renewal_date' => $this->renewal_date,
            'notes' => $this->notes,
            'notify_before_billing' => $this->notify_before_billing,
            'notify_days_before' => $this->notify_days_before,
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
        Subscription::where('user_id', auth()->id())->find($id)?->delete();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = 'all';
        $this->statusFilter = 'all';
        $this->cycleFilter = 'all';
        $this->amountFilter = 'all';
        $this->sortBy = 'billing_day';
    }

    public function render()
    {
        $baseQuery = Subscription::where('user_id', auth()->id())->with('category');
        $allSubs = (clone $baseQuery)->get()->map(fn ($sub) => $this->decorateSubscription($sub));

        $query = (clone $baseQuery);

        if (filled($this->search)) {
            $query->where(function ($inner) {
                $inner->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter !== 'all') {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->cycleFilter !== 'all') {
            $query->where('cycle', $this->cycleFilter);
        }

        $subs = $query->get()->map(fn ($sub) => $this->decorateSubscription($sub));

        $subs = match ($this->amountFilter) {
            'under_10' => $subs->where('amount', '<', 10),
            '10_30' => $subs->filter(fn ($sub) => $sub->amount >= 10 && $sub->amount <= 30),
            'over_30' => $subs->where('amount', '>', 30),
            default => $subs,
        };

        $subs = match ($this->sortBy) {
            'amount_desc' => $subs->sortByDesc('amount'),
            'amount_asc' => $subs->sortBy('amount'),
            'name' => $subs->sortBy('name'),
            'next_billing' => $subs->sortBy('days_until_billing'),
            default => $subs->sortBy('billing_day'),
        }->values();

        $activeSubs = $allSubs->where('status', 'active');
        $totalMonthly = $activeSubs->sum('monthly_equivalent');
        $alreadyPaid = $activeSubs->where('billing_day', '<', now()->day)->sum('monthly_equivalent');

        return view('livewire.subscription-hub', [
            'subscriptions' => $subs,
            'totalMonthly' => $totalMonthly,
            'totalAnnual' => $totalMonthly * 12,
            'upcoming' => max($totalMonthly - $alreadyPaid, 0),
            'nextSub' => $activeSubs->sortBy('days_until_billing')->first(),
            'activeCount' => $activeSubs->count(),
            'pausedCount' => $allSubs->where('status', 'paused')->count(),
            'cancelledCount' => $allSubs->where('status', 'cancelled')->count(),
            'averageMonthly' => $activeSubs->count() ? $totalMonthly / $activeSubs->count() : 0,
            'categories' => Category::where('user_id', auth()->id())->orderBy('name')->get(),
        ]);
    }

    private function decorateSubscription(Subscription $sub): Subscription
    {
        $sub->status = $sub->status ?: ($sub->is_active ? 'active' : 'paused');
        $sub->monthly_equivalent = match ($sub->cycle) {
            'quarterly' => (float) $sub->amount / 3,
            'semiannual' => (float) $sub->amount / 6,
            'annual' => (float) $sub->amount / 12,
            default => (float) $sub->amount,
        };

        $today = Carbon::now();
        $billingDate = $today->copy()->day(min((int) $sub->billing_day, $today->daysInMonth));

        if ($billingDate->isPast() && ! $billingDate->isToday()) {
            $nextMonth = $today->copy()->addMonthNoOverflow();
            $billingDate = $nextMonth->day(min((int) $sub->billing_day, $nextMonth->daysInMonth));
        }

        $sub->next_billing_date = $billingDate;
        $sub->days_until_billing = (int) $today->copy()->startOfDay()->diffInDays($billingDate->copy()->startOfDay(), false);

        return $sub;
    }
}
