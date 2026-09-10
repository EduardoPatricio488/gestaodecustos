<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionCycleService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SubscriptionHub extends Component
{
    public $name;
    public $amount;
    public $category_id;
    public $billing_day;
    public $billing_cycle = 'monthly';
    public $payment_method;
    public $status = 'active';
    public $started_at;
    public $renewal_date;
    public $notes;
    public bool $notify_before_billing = false;
    public $notify_days_before;
    public $showModal = false;
    public $editingId = null;
    public string $search = '';
    public string $categoryFilter = 'all';
    public bool $showExtraModal = false;
    public bool $showPlatformPlanModal = false;
    public string $statusFilter = 'all';
    public string $cycleFilter = 'all';
    public string $amountFilter = 'all';
    public string $sortBy = 'billing_day';

    public array $stripePlanDetails = [];
    public ?string $stripePlanError = null;

    public function edit($id)
    {
        $this->editingId = $id;
        $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $this->name = $sub->name;
        $this->amount = $sub->amount;
        $this->category_id = $sub->category_id;
        $this->billing_day = $sub->billing_day;
        $this->billing_cycle = $sub->cycle;
        $this->status = $sub->status;
        $this->payment_method = $sub->payment_method;
        $this->started_at = $sub->started_at?->format('Y-m-d');
        $this->renewal_date = $sub->renewal_date?->format('Y-m-d');
        $this->notes = $sub->notes;
        $this->notify_before_billing = $sub->notify_before_billing;
        $this->notify_days_before = $sub->notify_days_before;
        $this->dispatch('modal-show-add-sub');
    }

    public function toggleStatus($id)
    {
        $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $sub->status = ($sub->status === 'active') ? 'paused' : 'active';
        $sub->save();
        $this->dispatch('toast', text: 'Estado atualizado!');
    }

    public function openPlatformPlanModal(): void
    {
        $this->showPlatformPlanModal = true;
        $this->loadStripePlanDetails();
    }

    public function closePlatformPlanModal(): void
    {
        $this->showPlatformPlanModal = false;
    }

    protected function loadStripePlanDetails(): void
    {
        $this->stripePlanDetails = [];
        $this->stripePlanError = null;

        $user = auth()->user();
        $planSlug = $user?->currentPlanSlug();
        $cashierSub = $planSlug && $planSlug !== 'free' ? $user?->subscription($planSlug) : null;
        $customerId = $user?->stripe_id;

        if (! $customerId) {
            $this->stripePlanError = 'Não existe um Stripe Customer associado a esta conta.';
            return;
        }

        $secret = config('services.stripe.secret');
        if (! $secret) {
            $this->stripePlanError = 'A integração Stripe não está configurada no servidor.';
            return;
        }

        try {
            $stripe = new \Stripe\StripeClient($secret);
            $customer = $stripe->customers->retrieve($customerId, []);

            $subscription = null;
            if ($cashierSub?->stripe_id) {
                $subscription = $stripe->subscriptions->retrieve($cashierSub->stripe_id, [
                    'expand' => ['default_payment_method', 'latest_invoice'],
                ]);
            }

            $paymentMethodId = null;
            if ($subscription?->default_payment_method) {
                $paymentMethodId = is_string($subscription->default_payment_method)
                    ? $subscription->default_payment_method
                    : $subscription->default_payment_method->id;
            } elseif ($customer->invoice_settings?->default_payment_method) {
                $paymentMethodId = is_string($customer->invoice_settings->default_payment_method)
                    ? $customer->invoice_settings->default_payment_method
                    : $customer->invoice_settings->default_payment_method->id;
            }

            $paymentMethod = null;
            if ($paymentMethodId) {
                $paymentMethod = is_object($subscription?->default_payment_method)
                    ? $subscription->default_payment_method
                    : $stripe->paymentMethods->retrieve($paymentMethodId, []);
            }

            $invoices = $stripe->invoices->all([
                'customer' => $customerId,
                'limit' => 100,
            ])->data ?? [];

            $safeAddress = function ($address): array {
                return [
                    'line1' => $address?->line1,
                    'line2' => $address?->line2,
                    'city' => $address?->city,
                    'state' => $address?->state,
                    'postal_code' => $address?->postal_code,
                    'country' => $address?->country,
                ];
            };

            $invoiceRows = collect($invoices)->map(function ($invoice): array {
                $paymentIntentId = null;
                if ($invoice->payment_intent) {
                    $paymentIntentId = is_string($invoice->payment_intent)
                        ? $invoice->payment_intent
                        : $invoice->payment_intent->id;
                }

                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'status' => $invoice->status,
                    'amount_paid' => $invoice->amount_paid,
                    'amount_due' => $invoice->amount_due,
                    'currency' => strtoupper((string) $invoice->currency),
                    'created' => $invoice->created,
                    'paid_at' => $invoice->status_transitions?->paid_at,
                    'hosted_invoice_url' => $invoice->hosted_invoice_url,
                    'invoice_pdf' => $invoice->invoice_pdf,
                    'payment_intent_id' => $paymentIntentId,
                ];
            })->values()->all();

            $latestInvoice = $subscription?->latest_invoice;
            $subscriptionAmount = null;
            $subscriptionCurrency = null;
            if ($subscription && isset($subscription->items->data[0]->price)) {
                $price = $subscription->items->data[0]->price;
                $subscriptionAmount = $price->unit_amount;
                $subscriptionCurrency = strtoupper((string) $price->currency);
            }

            $stripeStatus = $subscription?->status;
            $statusLabel = match ($stripeStatus) {
                'active' => 'Ativa',
                'trialing' => 'Em período de teste',
                'past_due' => 'Pagamento em atraso',
                'unpaid' => 'Não paga',
                'incomplete' => 'Incompleta',
                'incomplete_expired' => 'Expirada',
                'canceled' => 'Cancelada',
                default => $stripeStatus ? ucfirst(str_replace('_', ' ', $stripeStatus)) : 'Não encontrada',
            };

            $this->stripePlanDetails = [
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'currency' => $customer->currency ? strtoupper((string) $customer->currency) : null,
                    'description' => $customer->description,
                    'address' => $safeAddress($customer->address),
                ],
                'subscription' => $subscription ? [
                    'id' => $subscription->id,
                    'status' => $stripeStatus,
                    'status_label' => $statusLabel,
                    'current_period_start' => $subscription->current_period_start,
                    'current_period_end' => $subscription->current_period_end,
                    'cancel_at_period_end' => (bool) $subscription->cancel_at_period_end,
                    'cancel_at' => $subscription->cancel_at,
                    'created' => $subscription->created,
                    'amount' => $subscriptionAmount,
                    'currency' => $subscriptionCurrency,
                    'latest_invoice_id' => is_string($latestInvoice ?? null) ? $latestInvoice : ($latestInvoice?->id),
                ] : null,
                'payment_method' => $paymentMethod ? [
                    'id' => $paymentMethod->id,
                    'type' => $paymentMethod->type,
                    'brand' => $paymentMethod->card?->brand,
                    'last4' => $paymentMethod->card?->last4,
                    'exp_month' => $paymentMethod->card?->exp_month,
                    'exp_year' => $paymentMethod->card?->exp_year,
                    'holder' => $paymentMethod->billing_details?->name,
                    'billing_email' => $paymentMethod->billing_details?->email,
                    'billing_address' => $safeAddress($paymentMethod->billing_details?->address),
                ] : null,
                'invoices' => $invoiceRows,
            ];
        } catch (\Throwable $e) {
            report($e);
            $this->stripePlanError = 'Não foi possível carregar os dados do Stripe neste momento.';
        }
    }

    public function openExtraModal()
    {
        $this->reset(['name', 'amount', 'billing_day', 'category_id', 'billing_cycle', 'payment_method', 'status', 'started_at', 'renewal_date', 'notes', 'notify_before_billing', 'notify_days_before', 'editingId']);
        $this->showExtraModal = true;
    }

    public function closeExtraModal()
    {
        $this->showExtraModal = false;
    }

    public function duplicate($id)
    {
        $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $newSub = $sub->replicate();
        $newSub->name = $sub->name.' (Cópia)';
        $newSub->save();
        $this->dispatch('toast', text: 'Assinatura duplicada!');
    }

    public function save()
    {
        $data = $this->validate([
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

        $subscriptionData = [
            'user_id' => auth()->id(),
            'workspace_id' => auth()->user()->current_workspace_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'billing_day' => $this->billing_day,
            'cycle' => $this->billing_cycle ?: 'monthly',
            'status' => $this->status ?: 'active',
            'is_active' => ($this->status ?: 'active') === 'active',
            'payment_method' => $this->payment_method,
            'started_at' => $this->started_at,
            'renewal_date' => $this->renewal_date,
            'notes' => $this->notes,
            'notify_before_billing' => $this->notify_before_billing,
            'notify_days_before' => $this->notify_days_before,
        ];

        if ($this->editingId) {
            Subscription::where('user_id', auth()->id())->find($this->editingId)?->update($subscriptionData);
            $msg = 'Assinatura atualizada!';
        } else {
            Subscription::create($subscriptionData);
            $user = auth()->user();
            $user->awardXp(20, 'assinatura registada');
            $msg = 'Assinatura ativada! '.$user->xpToastText(20, 'assinatura registada');
        }

        $this->reset(['name', 'amount', 'category_id', 'billing_day', 'payment_method', 'started_at', 'renewal_date', 'notes', 'notify_days_before', 'editingId']);
        $this->dispatch('modal-close-add-sub');
        $this->dispatch('toast', text: $msg);
    }

    public function delete($id)
    {
        $sub = Subscription::where('user_id', auth()->id())->find($id);
        if ($sub) {
            $sub->delete();
            $this->dispatch('toast', text: 'Assinatura removida com sucesso!');
        }
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
        $user = auth()->user();
        $wsId = $user->current_workspace_id;
        $cashierSub = null;

        $subCatNames = [
            'Streaming (Vídeo/TV)', 'Música & Podcasts', 'Software & SaaS', 'Gaming',
            'Fitness & Ginásio', 'Cloud & Armazenamento', 'Notícias & Revistas',
            'Educação & Cursos', 'VPN & Segurança', 'Seguros & Finanças',
            'Serviços Casa (Net/TV)', 'Outros',
        ];

        foreach ($subCatNames as $name) {
            Category::firstOrCreate(
                ['name' => $name, 'workspace_id' => $wsId],
                ['user_id' => $user->id, 'icon' => 'credit-card', 'color' => '#6366f1', 'hidden_from_sidebar' => true]
            );
        }

        $subscriptionCategories = Category::where('workspace_id', $wsId)
            ->whereIn('name', $subCatNames)
            ->orderBy('name')
            ->get();

        $baseQuery = Subscription::where('workspace_id', $wsId)->with('category');

        if (filled($this->search)) {
            $baseQuery->where(function ($inner) {
                $inner->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('notes', 'like', '%'.$this->search.'%');
            });
        }
        if ($this->categoryFilter !== 'all') $baseQuery->where('category_id', $this->categoryFilter);
        if ($this->statusFilter !== 'all') $baseQuery->where('status', $this->statusFilter);
        if ($this->cycleFilter !== 'all') $baseQuery->where('cycle', $this->cycleFilter);

        $allSubs = $baseQuery->get()->map(fn ($sub) => $this->decorateSubscription($sub));

        $subs = match ($this->amountFilter) {
            'under_10' => $allSubs->where('amount', '<', 10),
            '10_30' => $allSubs->filter(fn ($sub) => $sub->amount >= 10 && $sub->amount <= 30),
            'over_30' => $allSubs->where('amount', '>', 30),
            default => $allSubs,
        };

        $subs = match ($this->sortBy) {
            'amount_desc' => $subs->sortByDesc('amount'),
            'amount_asc' => $subs->sortBy('amount'),
            'name' => $subs->sortBy('name'),
            'next_billing' => $subs->sortBy('days_until_billing'),
            default => $subs->sortBy('billing_day'),
        };

        $activeSubs = $allSubs->where('status', 'active');
        $planSlug = $user->currentPlanSlug();
        $platformPlan = $planSlug !== 'free' ? SubscriptionPlan::where('slug', $planSlug)->first() : null;
        $platformEntry = null;

        if ($platformPlan) {
            $cashierSub = $user->subscription($planSlug);
            $firstPayment = Payment::where('user_id', $user->id)->where('plan_type', $planSlug)->oldest('paid_at')->first();
            $billingDay = $cashierSub?->created_at?->day ?? $firstPayment?->paid_at?->day ?? now()->day;
            $today = Carbon::now()->startOfDay();
            $nextBilling = $today->copy()->day(min($billingDay, $today->daysInMonth));
            if ($nextBilling->lte($today)) $nextBilling = $nextBilling->addMonthNoOverflow();

            $platformEntry = (object) [
                'name' => 'Finance Pro '.$platformPlan->name,
                'billing_day' => $billingDay,
                'next_billing_date' => $nextBilling,
                'days_until_billing' => (int) $today->diffInDays($nextBilling, false),
                'monthly_equivalent' => (float) $platformPlan->price,
            ];
        }

        $totalMonthly = $activeSubs->sum('monthly_equivalent') + ($platformEntry->monthly_equivalent ?? 0);
        $alreadyPaid = $activeSubs->where('billing_day', '<', now()->day)->sum('monthly_equivalent');
        if ($platformEntry && $platformEntry->billing_day <= now()->day) $alreadyPaid += $platformEntry->monthly_equivalent;

        $activeCount = $activeSubs->count() + ($platformEntry ? 1 : 0);
        $nextSub = $activeSubs->values()->push($platformEntry)->filter()->sortBy('days_until_billing')->first();

        $platformPayments = $platformPlan
            ? Payment::where('user_id', $user->id)->where('plan_type', $planSlug)->latest('paid_at')->get()
            : collect();

        return view('livewire.subscription-hub', [
            'subscriptions' => $subs->values(),
            'totalMonthly' => $totalMonthly,
            'totalAnnual' => $totalMonthly * 12,
            'upcoming' => max($totalMonthly - $alreadyPaid, 0),
            'nextSub' => $nextSub,
            'activeCount' => $activeCount,
            'pausedCount' => $allSubs->where('status', 'paused')->count(),
            'cancelledCount' => $allSubs->where('status', 'cancelled')->count(),
            'averageMonthly' => $activeCount ? $totalMonthly / $activeCount : 0,
            'categories' => $subscriptionCategories,
            'platformPlan' => $platformPlan,
            'platformPayments' => $platformPayments,
            'platformCashierSubscription' => $cashierSub,
        ]);
    }

    private function decorateSubscription(Subscription $sub): Subscription
    {
        $sub->status = $sub->status ?: ($sub->is_active ? 'active' : 'paused');
        $sub->monthly_equivalent = SubscriptionCycleService::toMonthly((float) $sub->amount, $sub->cycle);
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
