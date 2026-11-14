<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionCycleService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Stripe\StripeClient;

#[Layout('components.layouts.app')]
class SubscriptionHub extends Component
{
    public bool $dashboardMode = false;

    public $name;

    public $amount;

    public $category_id;

    public $billing_day;

    public $billing_cycle = 'monthly';

    public ?int $bank_account_id = null;

    public $status = 'active';

    public $started_at;

    public $renewal_date;

    public $notes;

    public bool $transportPassMode = false;

    public $transportPassName = 'Passe mensal';

    public $transportPassAmount;

    public $transportPassStartedAt;

    public $transportPassPaymentMethod = 'card';

    public bool $transportPassPaidByCompany = false;

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

    public bool $showPaymentHistoryModal = false;

    public string $paymentHistoryTitle = '';

    public array $paymentHistory = [];

    public float $paymentHistoryTotal = 0;

    public int $paymentHistoryCount = 0;

    public function mount(bool $dashboardMode = false): void
    {
        $this->dashboardMode = $dashboardMode;

        if (request()->boolean('open_extra')) {
            $this->openExtraModal();
        }
    }

    #[On('dashboard-subscription-create')]
    #[On('open-mobile-subscription-create')]
    public function openDashboardSubscriptionModal(?string $date = null): void
    {
        if (! $this->dashboardMode) {
            return;
        }

        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas utilizadores com permissões de edição podem criar assinaturas.');

            return;
        }

        $this->openExtraModal();
        if ($date) {
            $this->started_at = $date;
            $this->billing_day = (int) Carbon::parse($date)->day;
        }
    }

    public function edit($id)
    {
        $this->editingId = $id;
        $sub = Subscription::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        $this->name = $sub->name;
        $this->amount = $sub->amount;
        $this->category_id = $sub->category_id;
        $this->billing_day = $sub->billing_day;
        $this->billing_cycle = $sub->cycle;
        $this->status = $sub->status;
        $this->bank_account_id = BankAccount::query()
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->whereNotIn('type', ['investimento', 'investimentos'])
            ->whereKey($sub->bank_account_id)
            ->value('id');
        $this->started_at = $sub->started_at?->format('Y-m-d');
        $this->syncRenewalDate();
        $this->notes = $sub->notes;
        $this->notify_before_billing = (bool) $sub->notify_before_billing;
        $this->notify_days_before = $sub->notify_days_before;
        $this->transportPassMode = (bool) $sub->paid_by_company;
        if ($this->transportPassMode) {
            $this->transportPassName = $sub->name;
            $this->transportPassAmount = $sub->amount;
            $this->transportPassStartedAt = $sub->started_at?->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->transportPassPaymentMethod = $sub->payment_method ?: 'card';
            $this->transportPassPaidByCompany = true;
        }
        $this->showExtraModal = true;
    }

    public function openPaymentHistory(int $id): void
    {
        $user = auth()->user();

        $subscription = Subscription::query()
            ->where('workspace_id', $user->current_workspace_id)
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $this->paymentHistoryTitle = $subscription->name;

        // Calcula as mensalidades/ciclos desde a data de início até hoje.
        // A data de início conta como o primeiro pagamento previsto.
        $startDate = $subscription->started_at
            ? Carbon::parse($subscription->started_at)->startOfDay()
            : null;
        $today = Carbon::today();
        $intervalMonths = match ($subscription->cycle) {
            'quarterly' => 3,
            'semiannual' => 6,
            'annual' => 12,
            default => 1,
        };

        $this->paymentHistory = [];
        $this->paymentHistoryTotal = 0;
        $this->paymentHistoryCount = 0;

        if ($startDate && $startDate->lte($today)) {
            $paymentDate = $startDate->copy();
            while ($paymentDate->lte($today)) {
                $this->paymentHistory[] = [
                    'billing_date' => $paymentDate->format('d/m/Y'),
                    'paid_at' => null,
                    'amount' => (float) $subscription->amount,
                    'source' => 'Ciclo contabilizado desde o início',
                ];
                $this->paymentHistoryTotal += (float) $subscription->amount;
                $this->paymentHistoryCount++;
                $paymentDate->addMonthsNoOverflow($intervalMonths);
            }
        }

        $this->paymentHistoryTotal = round($this->paymentHistoryTotal, 2);
        $this->paymentHistory = array_reverse($this->paymentHistory);

        $this->showPaymentHistoryModal = true;
    }

    public function closePaymentHistory(): void
    {
        $this->showPaymentHistoryModal = false;
        $this->paymentHistory = [];
        $this->paymentHistoryTotal = 0;
        $this->paymentHistoryCount = 0;
        $this->paymentHistoryTitle = '';
    }

    public function toggleStatus($id)
    {
        $sub = Subscription::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($id);
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
            $stripe = new StripeClient($secret);
            $customer = $stripe->customers->retrieve($customerId, []);

            $subscription = null;
            if ($cashierSub?->stripe_id) {
                $subscription = $stripe->subscriptions->retrieve($cashierSub->stripe_id, [
                    'expand' => ['default_payment_method', 'latest_invoice', 'items.data.price'],
                ]);
            }

            // Cashier may not have a local subscription even when Stripe does.
            // Fall back to Stripe's subscriptions for this customer.
            if (! $subscription) {
                $stripeSubscriptions = $stripe->subscriptions->all([
                    'customer' => $customerId,
                    'status' => 'all',
                    'limit' => 100,
                    'expand' => ['data.default_payment_method', 'data.latest_invoice', 'data.items.data.price'],
                ])->data ?? [];

                $priority = [
                    'active' => 1,
                    'trialing' => 2,
                    'past_due' => 3,
                    'unpaid' => 4,
                    'incomplete' => 5,
                    'incomplete_expired' => 6,
                    'canceled' => 7,
                ];

                usort($stripeSubscriptions, function ($a, $b) use ($priority) {
                    $aPriority = $priority[$a->status ?? ''] ?? 99;
                    $bPriority = $priority[$b->status ?? ''] ?? 99;

                    if ($aPriority !== $bPriority) {
                        return $aPriority <=> $bPriority;
                    }

                    return ($b->created ?? 0) <=> ($a->created ?? 0);
                });

                $subscription = $stripeSubscriptions[0] ?? null;
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
        $this->reset(['name', 'amount', 'billing_day', 'category_id', 'billing_cycle', 'bank_account_id', 'status', 'started_at', 'renewal_date', 'notes', 'notify_before_billing', 'notify_days_before', 'editingId']);
        $this->syncRenewalDate();
        $this->transportPassMode = false;
        $this->transportPassName = 'Passe mensal';
        $this->transportPassAmount = null;
        $this->transportPassStartedAt = now()->format('Y-m-d');
        $this->transportPassPaymentMethod = 'card';
        $this->transportPassPaidByCompany = false;
        $this->showExtraModal = true;
    }

    public function closeExtraModal()
    {
        $this->showExtraModal = false;
    }

    public function updatedBillingCycle(): void
    {
        $this->syncRenewalDate();
    }

    public function updatedStartedAt(): void
    {
        $this->syncRenewalDate();
    }

    private function syncRenewalDate(): void
    {
        $intervalMonths = match ($this->billing_cycle) {
            'quarterly' => 3,
            'semiannual' => 6,
            'annual' => 12,
            default => 1,
        };

        $anchor = filled($this->started_at)
            ? Carbon::parse($this->started_at)->startOfDay()
            : Carbon::today();

        $today = Carbon::today();
        $monthsElapsed = max(0, (($today->year - $anchor->year) * 12) + $today->month - $anchor->month);
        $cycles = max(1, intdiv($monthsElapsed, $intervalMonths));
        $renewal = $anchor->copy()->addMonthsNoOverflow($intervalMonths * $cycles);

        while ($renewal->lt($today)) {
            $cycles++;
            $renewal = $anchor->copy()->addMonthsNoOverflow($intervalMonths * $cycles);
        }

        $this->renewal_date = $renewal->toDateString();
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
        if ($this->transportPassMode) {
            $this->saveTransportPass();
            return;
        }

        $this->syncRenewalDate();

        $data = $this->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
            'category_id' => 'required|integer',
            'billing_day' => 'required|integer|between:1,31',
            'billing_cycle' => 'nullable|in:monthly,quarterly,semiannual,annual',
            'status' => 'nullable|in:active,paused,cancelled',
            'bank_account_id' => 'required|integer',
            'started_at' => 'nullable|date',
            'renewal_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'notify_before_billing' => 'boolean',
            'notify_days_before' => 'nullable|integer|between:1,30',
        ]);

        abort_unless(
            Category::where('workspace_id', auth()->user()->current_workspace_id)
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->whereKey($this->category_id)
                ->exists(),
            422,
            'Categoria inválida.'
        );

        $bankAccount = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->whereNotIn('type', ['investimento', 'investimentos'])
            ->findOrFail($this->bank_account_id);

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
            'bank_account_id' => $bankAccount->id,
            'payment_method' => null,
            'started_at' => $this->started_at,
            'renewal_date' => $this->renewal_date,
            'notes' => $this->notes,
            'notify_before_billing' => $this->notify_before_billing,
            'notify_days_before' => $this->notify_days_before,
        ];

        if ($this->editingId) {
            Subscription::where('workspace_id', auth()->user()->current_workspace_id)
                ->where('user_id', auth()->id())
                ->find($this->editingId)?->update($subscriptionData);
            $msg = 'Assinatura atualizada!';
        } else {
            Subscription::create($subscriptionData);
            $user = auth()->user();
            $user->awardXp(20, 'assinatura registada');
            $msg = 'Assinatura ativada! '.$user->xpToastText(20, 'assinatura registada');
        }

        $isCreating = ! $this->editingId;
        $this->reset(['name', 'amount', 'category_id', 'billing_day', 'bank_account_id', 'started_at', 'renewal_date', 'notes', 'notify_days_before', 'editingId']);

        if ($this->dashboardMode && $isCreating) {
            $this->showExtraModal = false;
            $this->dispatch('dashboard-record-created');
            $this->dispatch('dashboard-quick-create-saved');
            $this->dispatch('toast', variant: 'success', text: $msg);

            return;
        }

        $this->showExtraModal = false;
        $this->dispatch('modal-close-add-sub');
        $this->dispatch('toast', text: $msg);
    }

    private function saveTransportPass(): void
    {
        $this->validate([
            'transportPassName' => 'required|string|max:120',
            'transportPassAmount' => 'required|numeric|min:0.01',
            'transportPassStartedAt' => 'required|date',
            'transportPassPaymentMethod' => 'required|in:card,direct_debit,bank_transfer,paypal,cash',
            'transportPassPaidByCompany' => 'boolean',
        ]);

        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;
        $paidByCompany = (bool) $this->transportPassPaidByCompany;

        $categoryQuery = Category::where('workspace_id', $workspaceId)->where('name', 'Transporte');
        if ($user->currentWorkspace?->type === 'personal') {
            $categoryQuery->where('user_id', $user->id);
        }

        $category = $categoryQuery->first();
        if (! $category) {
            $category = Category::create([
                'name' => 'Transporte',
                'workspace_id' => $workspaceId,
                'user_id' => $user->id,
                'icon' => 'truck',
                'color' => '#0ea5e9',
                'hidden_from_sidebar' => true,
            ]);
        }

        $startDate = Carbon::parse($this->transportPassStartedAt);
        $subscriptionData = [
            'category_id' => $category->id,
            'name' => trim($this->transportPassName),
            'amount' => $this->transportPassAmount,
            'billing_day' => $startDate->day,
            'cycle' => 'monthly',
            'payment_method' => $this->transportPassPaymentMethod,
            'started_at' => $startDate->toDateString(),
            'renewal_date' => $startDate->copy()->addMonthNoOverflow()->toDateString(),
            'notes' => $paidByCompany
                ? 'Passe de transporte pago pela empresa. Excluído dos cálculos financeiros.'
                : 'Passe mensal de transporte.',
            'notify_before_billing' => false,
            'paid_by_company' => $paidByCompany,
        ];

        if ($this->editingId) {
            Subscription::query()
                ->where('workspace_id', $workspaceId)
                ->where('user_id', $user->id)
                ->findOrFail($this->editingId)
                ->update($subscriptionData);

            $message = 'Passe mensal atualizado!';
        } else {
            Subscription::create(array_merge($subscriptionData, [
                'user_id' => $user->id,
                'workspace_id' => $workspaceId,

                'status' => 'active',
                'is_active' => true,
            ]));

            $user->awardXp(20, 'passe mensal registado');
            $message = $paidByCompany
                ? 'Passe registado. Como é pago pela empresa, não entra nas tuas contas.'
                : 'Passe mensal adicionado às assinaturas.';
        }

        Illuminate\Support\Facades\Cache::forget("dashboard:subscriptions-monthly:{$workspaceId}:{$user->id}");
        $this->reset(['transportPassName', 'transportPassAmount', 'transportPassStartedAt', 'transportPassPaymentMethod', 'transportPassPaidByCompany', 'editingId']);
        $this->transportPassMode = false;
        $this->showExtraModal = false;

        if ($this->dashboardMode) {
            $this->dispatch('dashboard-record-created');
            $this->dispatch('dashboard-quick-create-saved');
            $this->dispatch('toast', variant: 'success', text: $message);
            return;
        }

        $this->dispatch('modal-close-add-sub');
        $this->dispatch('toast', text: $message);
    }

    public function delete($id)
    {
        $sub = Subscription::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->find($id);
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
        $isPersonalWorkspace = $user->currentWorkspace?->type === 'personal';

        $subCatNames = [
            'Streaming (Vídeo/TV)', 'Música & Podcasts', 'Software & SaaS', 'Gaming',
            'Fitness & Ginásio', 'Cloud & Armazenamento', 'Notícias & Revistas',
            'Educação & Cursos', 'VPN & Segurança', 'Seguros & Finanças',
            'Serviços Casa (Net/TV)', 'Transporte', 'Outros',
        ];

        foreach ($subCatNames as $name) {
            Category::firstOrCreate(
                ['name' => $name, 'workspace_id' => $wsId],
                ['user_id' => $user->id, 'icon' => 'credit-card', 'color' => '#6366f1', 'hidden_from_sidebar' => true]
            );
        }

        $bankAccounts = BankAccount::where('workspace_id', $wsId)
            ->where('user_id', $user->id)
            ->whereNotIn('type', ['investimento', 'investimentos'])
            ->orderBy('name')
            ->get();

        $subscriptionCategories = Category::where('workspace_id', $wsId)
            ->whereIn('name', $subCatNames)
            ->orderBy('name')
            ->get();

        if ($this->dashboardMode) {
            return view('livewire.subscription-hub', [
                'subscriptions' => collect(),
                'totalMonthly' => 0,
                'totalAnnual' => 0,
                'companyPaidMonthly' => 0,
                'companyPaidAnnual' => 0,
                'totalMonthlyIncludingCompany' => 0,
                'companyPaidAlreadyPaid' => 0,
                'upcoming' => 0,
                'nextSub' => null,
                'activeCount' => 0,
                'pausedCount' => 0,
                'cancelledCount' => 0,
                'averageMonthly' => 0,
                'categories' => $subscriptionCategories,
                'bankAccounts' => $bankAccounts,
                'platformPlan' => null,
                'platformPayments' => collect(),
                'platformCashierSubscription' => null,
            ]);
        }

        $baseQuery = Subscription::where('workspace_id', $wsId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->with(['category', 'bankAccount']);

        if (filled($this->search)) {
            $baseQuery->where(function ($inner) {
                $inner->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('notes', 'like', '%'.$this->search.'%');
            });
        }
        if ($this->categoryFilter !== 'all') {
            $baseQuery->where('category_id', $this->categoryFilter);
        }
        if ($this->statusFilter !== 'all') {
            $baseQuery->where('status', $this->statusFilter);
        }
        if ($this->cycleFilter !== 'all') {
            $baseQuery->where('cycle', $this->cycleFilter);
        }

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
        $companyPaidActiveSubs = $activeSubs->where('paid_by_company', true);
        $billableActiveSubs = $activeSubs->where('paid_by_company', false);
        $planSlug = $user->currentPlanSlug();
        $platformPlan = $planSlug !== 'free' ? SubscriptionPlan::where('slug', $planSlug)->first() : null;
        $platformEntry = null;

        if ($platformPlan) {
            $cashierSub = $user->subscription($planSlug);
            $firstPayment = Payment::where('user_id', $user->id)->where('plan_type', $planSlug)->oldest('paid_at')->first();
            $billingDay = $cashierSub?->created_at?->day ?? $firstPayment?->paid_at?->day ?? now()->day;
            $today = Carbon::now()->startOfDay();
            $nextBilling = $today->copy()->day(min($billingDay, $today->daysInMonth));
            if ($nextBilling->lte($today)) {
                $nextBilling = $nextBilling->addMonthNoOverflow();
            }

            $platformEntry = (object) [
                'name' => 'Finance Pro '.$platformPlan->name,
                'billing_day' => $billingDay,
                'next_billing_date' => $nextBilling,
                'days_until_billing' => (int) $today->diffInDays($nextBilling, false),
                'monthly_equivalent' => (float) $platformPlan->price,
            ];
        }

        $totalMonthly = $billableActiveSubs->sum('monthly_equivalent') + ($platformEntry->monthly_equivalent ?? 0);
        $companyPaidMonthly = $companyPaidActiveSubs->sum('monthly_equivalent');
        $totalMonthlyIncludingCompany = $totalMonthly + $companyPaidMonthly;
        $alreadyPaid = $billableActiveSubs->where('billing_day', '<', now()->day)->sum('monthly_equivalent');
        $companyPaidAlreadyPaid = $companyPaidActiveSubs->where('billing_day', '<', now()->day)->sum('monthly_equivalent');
        if ($platformEntry && $platformEntry->billing_day <= now()->day) {
            $alreadyPaid += $platformEntry->monthly_equivalent;
        }

        $activeCount = $activeSubs->count() + ($platformEntry ? 1 : 0);
        $nextSub = $billableActiveSubs->values()->push($platformEntry)->filter()->sortBy('days_until_billing')->first();

        $platformPayments = $platformPlan
            ? Payment::where('user_id', $user->id)->where('plan_type', $planSlug)->latest('paid_at')->get()
            : collect();

        return view('livewire.subscription-hub', [
            'subscriptions' => $subs->values(),
            'totalMonthly' => $totalMonthly,
            'totalAnnual' => $totalMonthly * 12,
            'companyPaidMonthly' => $companyPaidMonthly,
            'companyPaidAnnual' => $companyPaidMonthly * 12,
            'totalMonthlyIncludingCompany' => $totalMonthlyIncludingCompany,
            'companyPaidAlreadyPaid' => $companyPaidAlreadyPaid,
            'upcoming' => max($totalMonthly - $alreadyPaid, 0),
            'nextSub' => $nextSub,
            'activeCount' => $activeCount,
            'pausedCount' => $allSubs->where('status', 'paused')->count(),
            'cancelledCount' => $allSubs->where('status', 'cancelled')->count(),
            'averageMonthly' => $activeCount ? $totalMonthly / $activeCount : 0,
            'categories' => $subscriptionCategories,
            'bankAccounts' => $bankAccounts,
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
