<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\User;
use App\Models\Workspace;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class SubscriptionHub extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all';
    public $filterPlan = 'all'; // NOVO FILTRO

    protected $queryString = ['search', 'filterStatus', 'filterPlan'];

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $payments = Payment::query()
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select('payments.*', 'users.name as user_name', 'users.email as user_email')
            ->when($this->search, function($q) {
                $q->where('payments.invoice_id', 'like', "%{$this->search}%")
                  ->orWhere('users.name', 'like', "%{$this->search}%");
            })
            ->when($this->filterStatus !== 'all', fn($q) => $q->where('payments.status', $this->filterStatus))
            // FILTRO DE PLANO REAL
            ->when($this->filterPlan !== 'all', fn($q) => $q->where('payments.plan_type', $this->filterPlan))
            ->orderBy('payments.paid_at', 'desc')
            ->paginate(12);

        $stats = [
            'mrr' => (float) Payment::where('status', 'paid')
                        ->where('paid_at', '>=', now()->startOfMonth())
                        ->sum('amount'),

            'total_revenue' => (float) Payment::where('status', 'paid')->sum('amount'),

            'count_premium' => Workspace::where('plan', 'premium')->count(),
            'count_business' => Workspace::where('plan', 'business')->count(),
            'active_premium' => Workspace::whereIn('plan', ['premium', 'business'])->count(),

            'failed_payments' => Payment::where('status', 'failed')->count(),
        ];

        return view('livewire.admin.subscription-hub', [
            'payments' => $payments,
            'stats' => $stats
        ]);
    }
}
