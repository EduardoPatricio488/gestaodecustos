<?php

namespace App\Livewire\Business;

use App\Models\Expense;
use App\Models\Project;
use App\Models\Task;
use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ExpenseApprovalHub extends Component
{
    // Funções de decisão (CEO)
    public function approve($id)
    {
        Expense::findOrFail($id)->update(['status' => 'aprovado']);
        $this->dispatch('toast', variant: 'success', text: 'Despesa aprovada!');
    }

    public function reject($id)
    {
        Expense::findOrFail($id)->update(['status' => 'rejeitado']);
        $this->dispatch('toast', variant: 'warning', text: 'Despesa rejeitada.');
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;

        // BUSCAR DADOS DA TABELA PRINCIPAL DE DESPESAS
        $query = Expense::where('workspace_id', $workspaceId)
            ->where('is_company', true)
            ->with(['user', 'project', 'task', 'category']);

        return view('livewire.business.expense-approval-hub', [
            // O que o CEO precisa de aprovar agora
            'pending' => (clone $query)->where('status', 'pendente')->latest()->get(),

            // O que já foi decidido (Histórico)
            'history' => (clone $query)->whereIn('status', ['aprovado', 'rejeitado'])->latest()->limit(10)->get(),

            'stats' => [
                'total_pending' => (clone $query)->where('status', 'pendente')->sum('amount'),
                'total_approved_month' => (clone $query)->where('status', 'aprovado')
                    ->whereMonth('spent_at', now()->month)
                    ->sum('amount'),
            ]
        ]);
    }
}
