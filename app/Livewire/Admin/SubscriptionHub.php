<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class SubscriptionHub extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = 'all';

    public string $filterPlan = 'all';

    public bool $showPaymentForm = false;

    public ?int $editingPaymentId = null;

    public string $pay_user_email = '';

    public string $pay_invoice_id = '';

    public string $pay_plan_type = 'pro';

    public $pay_amount = 0;

    public string $pay_status = 'paid';

    public string $pay_method = 'stripe';

    public ?string $pay_paid_at = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
        'filterPlan' => ['except' => 'all'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPlan(): void
    {
        $this->resetPage();
    }

    public function startCreatePayment(): void
    {
        $this->resetPaymentForm();
        $this->pay_invoice_id = 'INV-'.strtoupper(str()->random(6));
        $this->pay_paid_at = now()->format('Y-m-d\TH:i');
        $this->showPaymentForm = true;
    }

    public function editPayment(int $id): void
    {
        $payment = Payment::with('user')->findOrFail($id);

        $this->editingPaymentId = $payment->id;
        $this->pay_user_email = $payment->user?->email ?? '';
        $this->pay_invoice_id = $payment->invoice_id;
        $this->pay_plan_type = $payment->plan_type ?: 'free';
        $this->pay_amount = $payment->amount;
        $this->pay_status = $payment->status ?: 'paid';
        $this->pay_method = $payment->method ?: 'stripe';
        $this->pay_paid_at = optional($payment->paid_at)?->format('Y-m-d\TH:i');
        $this->showPaymentForm = true;
    }

    public function savePayment(): void
    {
        $this->validate([
            'pay_user_email' => 'required|email',
            'pay_invoice_id' => [
                'required',
                'string',
                'max:80',
                Rule::unique('payments', 'invoice_id')->ignore($this->editingPaymentId),
            ],
            'pay_plan_type' => 'required|string|max:80',
            'pay_amount' => 'required|numeric|min:0',
            'pay_status' => 'required|in:paid,pending,failed,refunded',
            'pay_method' => 'required|string|max:40',
            'pay_paid_at' => 'nullable|date',
        ], [
            'pay_user_email.required' => 'Indica o email do utilizador.',
            'pay_invoice_id.unique' => 'Já existe uma fatura com este número.',
        ]);

        $user = User::where('email', $this->pay_user_email)->first();

        if (! $user) {
            $this->addError('pay_user_email', 'Não existe nenhum utilizador com este email.');

            return;
        }

        $payload = [
            'user_id' => $user->id,
            'invoice_id' => $this->pay_invoice_id,
            'plan_type' => $this->pay_plan_type,
            'amount' => $this->pay_amount,
            'status' => $this->pay_status,
            'method' => $this->pay_method,
            'paid_at' => $this->pay_status === 'paid'
                ? ($this->pay_paid_at ?: now())
                : ($this->pay_paid_at ?: null),
        ];

        if ($this->editingPaymentId) {
            Payment::findOrFail($this->editingPaymentId)->update($payload);
        } else {
            Payment::create($payload);
        }

        $this->resetPaymentForm();
        $this->dispatch('toast', text: 'Registo de faturação atualizado.');
    }

    public function deletePayment(int $id): void
    {
        Payment::destroy($id);
        if ($this->editingPaymentId === $id) {
            $this->resetPaymentForm();
        }
        $this->dispatch('toast', text: 'Registo removido.', variant: 'warning');
    }

    public function resetPaymentForm(): void
    {
        $this->reset([
            'showPaymentForm',
            'editingPaymentId',
            'pay_user_email',
            'pay_invoice_id',
            'pay_plan_type',
            'pay_amount',
            'pay_status',
            'pay_method',
            'pay_paid_at',
        ]);
        $this->pay_plan_type = 'pro';
        $this->pay_status = 'paid';
        $this->pay_method = 'stripe';
        $this->pay_amount = 0;
        $this->resetValidation();
    }

    public function render()
    {
        $dbPlans = SubscriptionPlan::orderBy('price')->get();

        $payments = Payment::query()
            ->with('user')
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('invoice_id', 'like', $term)
                        ->orWhere('plan_type', 'like', $term)
                        ->orWhereHas('user', function ($user) use ($term) {
                            $user->where('name', 'like', $term)
                                ->orWhere('email', 'like', $term);
                        });
                });
            })
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPlan !== 'all', fn ($q) => $q->where('plan_type', $this->filterPlan))
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->paginate(12);

        $planDetails = $dbPlans->map(function ($plan) {
            return [
                'name' => $plan->name,
                'slug' => $plan->slug,
                'count' => User::where('plan', $plan->slug)->count(),
                'color' => $plan->price >= 10 ? 'violet' : 'emerald',
            ];
        });

        $countFree = User::where(function ($q) {
            $q->where('plan', 'free')->orWhereNull('plan')->orWhere('plan', '');
        })->count();

        $estimatedMRR = $planDetails->sum(fn ($p) => $p['count'] * (float) $dbPlans->firstWhere('slug', $p['slug'])?->price);
        $realRevenue = (float) Payment::where('status', 'paid')->sum('amount');
        $monthRevenue = (float) Payment::where('status', 'paid')
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $stats = [
            'mrr' => $estimatedMRR,
            'month_revenue' => $monthRevenue,
            'total_revenue' => $realRevenue,
            'count_free' => $countFree,
            'plan_details' => $planDetails,
            'total_users' => $countFree + $planDetails->sum('count'),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        return view('livewire.admin.subscription-hub', [
            'payments' => $payments,
            'stats' => $stats,
            'dbPlans' => $dbPlans,
        ]);
    }
}
