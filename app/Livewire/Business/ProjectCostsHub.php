<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Project;
use App\Models\Expense;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ProjectCostsHub extends Component
{
    use WithPagination;

    public $search = '';
    public $filterProject = '';
    public $filterUser = '';
    public $filterStatus = '';

    public function approve($id)
    {
        Expense::findOrFail($id)->update(['status' => 'aprovado']);
        $this->dispatch('toast', text: 'Despesa aprovada com sucesso!', variant: 'success');
    }

    public function reject($id)
    {
        Expense::findOrFail($id)->update(['status' => 'rejeitado']);
        $this->dispatch('toast', text: 'Despesa rejeitada.', variant: 'warning');
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        // 1. Projetos e Breakdown de Custos
        $projects = $workspace->projects()
            ->with(['manager', 'expenses'])
            ->get()
            ->map(function ($project) {
                $project->total_costs = $project->expenses->where('status', 'aprovado')->sum('amount');
                $project->pending_costs = $project->expenses->where('status', 'pendente')->sum('amount');
                return $project;
            });

        // 2. Despesas PENDENTES (Ação Imediata)
        $pendingExpenses = Expense::where('workspace_id', $workspace->id)
            ->where('status', 'pendente')
            ->where('is_company', true)
            ->with(['user', 'project', 'task'])
            ->latest()
            ->get();

        // 3. HISTÓRICO GLOBAL (Com Filtros)
        $historyQuery = Expense::where('workspace_id', $workspace->id)
            ->where('is_company', true)
            ->whereIn('status', ['aprovado', 'rejeitado']) // Apenas o que já foi decidido
            ->with(['user', 'project', 'task', 'category'])
            ->when($this->search, fn($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->filterProject, fn($q) => $q->where('project_id', $this->filterProject))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus));

        return view('livewire.business.project-costs-hub', [
            'projects' => $projects,
            'pendingExpenses' => $pendingExpenses,
            'history' => $historyQuery->latest('spent_at')->paginate(10),
            'totalOperationalCost' => $projects->sum('total_costs'),
            'allUsers' => $workspace->users // Para o filtro
        ]);
    }
}
