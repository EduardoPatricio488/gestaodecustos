<?php

namespace App\Livewire\Business;

use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ProjectCostsHub extends Component
{
    use WithPagination;

    public $search = '';

    public $filterProject = '';

    public $filterUser = '';

    public $filterStatus = '';

    public $historySearch = '';

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
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->historySearch, fn ($q) => $q->where('description', 'like', "%{$this->historySearch}%"))
            ->when($this->filterProject, fn ($q) => $q->where('project_id', $this->filterProject))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus));

        // 4. CÁLCULO DE MÉTRICAS GLOBAIS
        $allExpenses = Expense::where('workspace_id', $workspace->id)
            ->where('is_company', true)
            ->get();

        $totalApproved = $allExpenses->where('status', 'aprovado')->sum('amount');
        $totalRejected = $allExpenses->where('status', 'rejeitado')->sum('amount');
        $totalPending = $allExpenses->where('status', 'pendente')->sum('amount');
        $approvalRate = $allExpenses->count() > 0 ? round(($allExpenses->where('status', 'aprovado')->count() / $allExpenses->count()) * 100) : 0;

        return view('livewire.business.project-costs-hub', [
            'projects' => $projects,
            'pendingExpenses' => $pendingExpenses,
            'history' => $historyQuery->latest('spent_at')->paginate(10),
            'totalOperationalCost' => $projects->sum('total_costs'),
            'allUsers' => $workspace->users,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
            'totalPending' => $totalPending,
            'approvalRate' => $approvalRate,
        ]);
    }
}
