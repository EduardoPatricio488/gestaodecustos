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
public $editingId = null;
    public string $search = '';
    public string $categoryFilter = 'all';
    public string $statusFilter = 'all';
    public string $cycleFilter = 'all';
    public string $amountFilter = 'all';
    public string $sortBy = 'billing_day';
public function edit($id)
{
    $this->editingId = $id;
    $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);

    // Preenche as propriedades com os dados atuais
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

    // Abre o modal (usando o evento que o teu Blade já ouve)
    $this->dispatch('modal-show-add-sub');
}

public function toggleStatus($id)
{
    $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
    $sub->status = ($sub->status === 'active') ? 'paused' : 'active';
    $sub->save();
    $this->dispatch('toast', text: 'Estado atualizado!');
}

public function duplicate($id)
{
    $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
    $newSub = $sub->replicate();
    $newSub->name = $sub->name . ' (Cópia)';
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

    // Se tivermos um editingId, atualizamos. Se não, criamos.
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
        $msg = 'Assinatura ativada!';
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

    // 1. Definir categorias que FAZEM SENTIDO para assinaturas
    $subCatNames = [
        'Streaming (Vídeo/TV)',
        'Música & Podcasts',
        'Software & SaaS',
        'Gaming',
        'Fitness & Ginásio',
        'Cloud & Armazenamento',
        'Notícias & Revistas',
        'Educação & Cursos',
        'VPN & Segurança',
        'Seguros & Finanças',
        'Serviços Casa (Net/TV)',
        'Outros'
    ];

    // 2. Garantir que estas categorias existem no banco de dados para este Workspace
    // Nota: Usamos is_fixed = 1 ou um marcador para não as misturarmos totalmente na sidebar se preferires
    foreach ($subCatNames as $name) {
        \App\Models\Category::firstOrCreate(
            ['name' => $name, 'workspace_id' => $wsId],
            ['user_id' => $user->id, 'icon' => 'credit-card', 'color' => '#6366f1']
        );
    }

    // 3. Buscar APENAS estas categorias para o formulário e filtros
    $subscriptionCategories = \App\Models\Category::where('workspace_id', $wsId)
        ->whereIn('name', $subCatNames)
        ->orderBy('name')
        ->get();

    // --- Lógica original de busca de assinaturas ---
    $baseQuery = Subscription::where('workspace_id', $wsId)->with('category');

    // Filtros
    if (filled($this->search)) {
        $baseQuery->where(function ($inner) {
            $inner->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('notes', 'like', '%' . $this->search . '%');
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

    // Filtros de valor e ordenação (original)
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
    $totalMonthly = $activeSubs->sum('monthly_equivalent');
    $alreadyPaid = $activeSubs->where('billing_day', '<', now()->day)->sum('monthly_equivalent');

    return view('livewire.subscription-hub', [
        'subscriptions' => $subs->values(),
        'totalMonthly' => $totalMonthly,
        'totalAnnual' => $totalMonthly * 12,
        'upcoming' => max($totalMonthly - $alreadyPaid, 0),
        'nextSub' => $activeSubs->sortBy('days_until_billing')->first(),
        'activeCount' => $activeSubs->count(),
        'pausedCount' => $allSubs->where('status', 'paused')->count(),
        'cancelledCount' => $allSubs->where('status', 'cancelled')->count(),
        'averageMonthly' => $activeSubs->count() ? $totalMonthly / $activeSubs->count() : 0,
        'categories' => $subscriptionCategories, // Passamos a nova lista para o Blade
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
