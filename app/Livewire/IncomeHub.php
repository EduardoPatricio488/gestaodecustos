<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Income;
use App\Models\RecurringIncome;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class IncomeHub extends Component
{
    // Campos para Receita Pontual/Extra
    public $description, $amount, $received_at, $type = 'Extra';

    // Campos para Receita Fixa (Mensal)
    public $recDescription, $recAmount, $recDay;

    public function mount()
    {
        $this->received_at = now()->format('Y-m-d');
    }

    /**
     * BLOQUEIO: Apenas Editores e Donos podem registar receitas extras.
     */
    public function saveExtra()
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Permissão negada: não podes adicionar receitas.');
            return;
        }

        $this->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'received_at' => 'required|date',
        ]);

        Income::create([
            'user_id' => auth()->id(),
            'description' => $this->description,
            'amount' => $this->amount,
            'received_at' => $this->received_at,
            'type' => 'Extra',
        ]);

        $this->reset(['description', 'amount']);
        $this->received_at = now()->format('Y-m-d');
        $this->dispatch('modal-close', name: 'add-extra-income');
        $this->dispatch('toast', text: 'Receita extra registada!');
    }

    /**
     * BLOQUEIO: Apenas o Administrador (Dono) pode configurar salários fixos.
     */
    public function saveFixed()
    {
        if (!auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Ação restrita ao administrador do grupo.');
            return;
        }

        $this->validate([
            'recDescription' => 'required|string|max:255',
            'recAmount' => 'required|numeric|min:0.01',
            'recDay' => 'required|integer|between:1,31',
        ]);

        RecurringIncome::create([
            'user_id' => auth()->id(),
            'description' => $this->recDescription,
            'amount' => $this->recAmount,
            'day_of_month' => $this->recDay,
            'is_active' => true,
        ]);

        $this->reset(['recDescription', 'recAmount', 'recDay']);
        $this->dispatch('modal-close', name: 'add-fixed-income');
        $this->dispatch('toast', text: 'Rendimento fixo configurado!');
    }

    /**
     * BLOQUEIO: Apenas o Dono pode eliminar registos.
     */
    public function deleteFixed($id)
    {
        if (!auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas o administrador pode apagar rendimentos.');
            return;
        }

        RecurringIncome::where('id', $id)->delete();
        $this->dispatch('toast', text: 'Registo removido.');
    }

    public function deleteExtra($id)
    {
        if (!auth()->user()->isOwner()) {
            $this->dispatch('toast', variant: 'error', text: 'Não tens permissão para apagar este registo.');
            return;
        }

        Income::where('id', $id)->delete();
        $this->dispatch('toast', text: 'Receita removida.');
    }

    public function render()
    {
        $user = auth()->user();

        // Os dados são filtrados automaticamente por Workspace via Trait
        $fixedIncomes = RecurringIncome::all();
        $extraIncomes = Income::whereMonth('received_at', now()->month)
                               ->whereYear('received_at', now()->year)
                               ->latest()
                               ->get();

        $totalMonthly = $fixedIncomes->sum('amount') + $extraIncomes->sum('amount');

        return view('livewire.income-hub', [
            'fixedIncomes' => $fixedIncomes,
            'extraIncomes' => $extraIncomes,
            'totalMonthly' => $totalMonthly,

            // Flags de permissão para ocultar botões na vista
            'isOwner'   => $user->isOwner(),
            'canManage' => !$user->isViewer(),
        ]);
    }
}
