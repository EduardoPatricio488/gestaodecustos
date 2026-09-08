<?php

namespace App\Livewire;

use App\Models\Income;
use App\Models\RecurringIncome;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Incomes extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterSource = '';

    public const SOURCE_LABELS = [
        'emprego' => ['label' => 'Emprego', 'icon' => 'briefcase', 'color' => '#4f46e5'],
        'imobiliario' => ['label' => 'Renda Imobiliária', 'icon' => 'home', 'color' => '#0284c7'],
        'freelance' => ['label' => 'Freelance', 'icon' => 'computer-desktop', 'color' => '#7c3aed'],
        'investimento' => ['label' => 'Investimento', 'icon' => 'chart-bar-square', 'color' => '#059669'],
        'reforma' => ['label' => 'Reforma / Pensão', 'icon' => 'building-library', 'color' => '#0369a1'],
        'bolsa' => ['label' => 'Bolsa de Estudo', 'icon' => 'academic-cap', 'color' => '#c026d3'],
        'outro' => ['label' => 'Outro', 'icon' => 'sparkles', 'color' => '#71717a'],
    ];

    /**
     * Reseta a paginação ao pesquisar.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSource()
    {
        $this->resetPage();
    }

    /**
     * Elimina a receita pontual/registada (não aplicável a rendimentos fixos, geridos à parte).
     * BLOQUEIO: Apenas o Administrador (Dono) pode apagar registos.
     */
    public function delete(int $id): void
    {
        if (! auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada: apenas o administrador do grupo pode apagar dados.');

            return;
        }

        // O Global Scope garante que só apaga se pertencer ao workspace ativo
        Income::where('id', $id)->delete();

        $this->dispatch('toast', text: 'Receita eliminada com sucesso.');
    }

    public function render()
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;

        // 1. Receitas pontuais/registadas (tabela incomes)
        $incomeItems = Income::query()
            ->when($this->search, fn ($q) => $q->where('description', 'like', '%'.$this->search.'%'))
            ->when($this->filterSource, fn ($q) => $q->where('source', $this->filterSource))
            ->get()
            ->map(fn ($income) => (object) [
                'key' => 'income-'.$income->id,
                'real_id' => $income->id,
                'is_recurring' => false,
                'description' => $income->description,
                'amount' => (float) $income->amount,
                'source' => $income->source,
                'frequency' => $income->frequency,
                'received_at' => $income->received_at,
                'tax_estimate' => $income->tax_estimate,
                'bank_account_name' => $income->bankAccount?->name,
            ]);

        // 2. Rendimentos fixos/recorrentes ativos (tabela recurring_incomes) — contam como
        // registos também, só que representam o rendimento esperado este mês.
        $recurringItems = RecurringIncome::where('workspace_id', $workspaceId)
            ->where('is_active', true)
            ->when($this->search, fn ($q) => $q->where('description', 'like', '%'.$this->search.'%'))
            ->when($this->filterSource, fn ($q) => $q->where('source', $this->filterSource))
            ->get()
            ->map(function ($recurring) {
                $day = max(1, min(now()->daysInMonth, (int) ($recurring->day_of_month ?: 1)));

                return (object) [
                    'key' => 'recurring-'.$recurring->id,
                    'real_id' => $recurring->id,
                    'is_recurring' => true,
                    'description' => $recurring->description,
                    'amount' => (float) $recurring->amount,
                    'source' => $recurring->source,
                    'frequency' => $recurring->frequency ?: 'mensal',
                    'received_at' => now()->copy()->day($day),
                    'tax_estimate' => $recurring->tax_estimate,
                    'bank_account_name' => $recurring->bankAccount?->name,
                ];
            });

        $all = $incomeItems->concat($recurringItems)->sortByDesc('received_at')->values();

        $perPage = 20;
        $page = $this->getPage();
        $incomes = new LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $monthTotal = (float) Income::where('received_at', '>=', now()->startOfMonth())->sum('amount')
            + (float) RecurringIncome::where('workspace_id', $workspaceId)->where('is_active', true)->sum('amount');

        return view('livewire.incomes', [
            'incomes' => $incomes,
            'sources' => self::SOURCE_LABELS,
            'monthTotal' => $monthTotal,
            'isShared' => $user->currentWorkspace->users()->count() > 1,

            // Passamos as permissões para a vista esconder os botões visualmente
            'canDelete' => $user->isOwner(),
        ]);
    }
}
