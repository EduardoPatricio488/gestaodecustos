<?php

namespace App\Livewire;

use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Expenses extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $filterCategory = null;

    /**
     * Reseta a paginação ao pesquisar.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Redireciona para a página de criação.
     * BLOQUEIO: Apenas Editores ou Admins podem criar.
     */
    public function newExpense(): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas leitura: não tens permissão para criar registos.');
            return;
        }

        $this->redirect(route('expenses.create'), navigate: true);
    }

    /**
     * Redireciona para a página de edição.
     * BLOQUEIO: Visualizadores não podem editar.
     */
    public function edit(int $id): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas leitura: não podes editar este registo.');
            return;
        }

        $this->redirect(route('expenses.edit', $id), navigate: true);
    }

    /**
     * Elimina a despesa.
     * BLOQUEIO: Apenas o Administrador (Dono) pode apagar registos.
     */
    public function delete(int $id): void
    {
        if (!auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação negada: apenas o administrador do grupo pode apagar dados.');
            return;
        }

        // O Global Scope garante que só apaga se pertencer ao workspace ativo
        Expense::where('id', $id)->delete();

        session()->flash('ok', 'Despesa eliminada com sucesso.');
    }

    public function render()
    {
        $user = auth()->user();

        // Consulta filtrada automaticamente pelo Workspace ativo via Trait
        $expenses = Expense::with(['category', 'user'])
            ->when($this->search, fn ($q) =>
                $q->where('description', 'like', '%'.$this->search.'%')
            )
            ->when($this->filterCategory, fn ($q) =>
                $q->where('category_id', $this->filterCategory)
            )
            ->latest('spent_at')
            ->latest('id')
            ->paginate(20);

        $monthTotal = (float) Expense::where('spent_at', '>=', now()->startOfMonth())->sum('amount');

        return view('livewire.expenses', [
            'expenses'   => $expenses,
            'categories' => $user->categories()->orderBy('name')->get(),
            'monthTotal' => $monthTotal,
            'isShared'   => $user->currentWorkspace->users()->count() > 1,

            // Passamos as permissões para a vista esconder os botões visualmente
            'canEdit'    => !$user->isViewer(),
            'canDelete'  => $user->isOwner(),
        ]);
    }
}
