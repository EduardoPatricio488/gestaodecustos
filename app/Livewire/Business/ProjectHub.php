<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Project;
use App\Models\Employee; // Modelo correto

#[Layout('components.layouts.app')]
class ProjectHub extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;

    public $name;
    public $description;
    public $budget;
    public $status = 'em_curso';
    public $start_date;
    public $deadline;

    public $revenue;
    public $costs;
    public $margin;

    public $manager_id;

    protected $rules = [
        'name'       => 'required|string|max:255',
        'budget'     => 'nullable|numeric|min:0',

        'status'     => 'required|in:planeamento,em_curso,concluido,pausado',
        'revenue'    => 'nullable|numeric|min:0',
        'costs'      => 'nullable|numeric|min:0',
        'margin'     => 'nullable|numeric|min:0|max:100',
        // ❌ Removido exists:employees,id porque estava a bloquear
        'manager_id' => 'nullable|integer',
        'start_date' => 'nullable|date',
        'deadline'   => 'nullable|date',
    ];

    public function updatedRevenue()
    {
        $this->recalculateMargin();
    }

    public function updatedCosts()
    {
        $this->recalculateMargin();
    }

    protected function recalculateMargin()
{
    $revenue = $this->revenue ?? 0;
    $costs   = $this->costs ?? 0;

    if ($revenue > 0) {
        $this->margin = round((($revenue - $costs) / $revenue) * 100, 2);
    } else {
        $this->margin = 0;
    }
    }

    public function save()
{
    $this->validate();

    $workspace = auth()->user()->currentWorkspace;

    $workspace->projects()->updateOrCreate(
        ['id' => $this->editingId],
        [
            'workspace_id' => $workspace->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'budget'       => $this->budget,
            'status'       => $this->status,
            'start_date'   => $this->start_date,
            'deadline'     => $this->deadline,
            'revenue'      => $this->revenue,
            'costs'        => $this->costs,
            'margin'       => $this->margin,
            'profit'       => ($this->revenue ?? 0) - ($this->costs ?? 0),
            'manager_id'   => $this->manager_id,


        ]
    );

    $this->resetForm();
    $this->dispatch('modal-close', name: 'project-modal');
    $this->dispatch('toast', text: 'Projeto criado com sucesso!');
}

    public function edit($id)
{
    $workspace = auth()->user()->currentWorkspace;
    $project   = $workspace->projects()->findOrFail($id);

    $this->editingId   = $project->id;
    $this->name        = $project->name;
    $this->description = $project->description;
    $this->budget      = $project->budget;
    $this->status      = $project->status;
    $this->start_date  = $project->start_date;
    $this->deadline    = $project->deadline;
    $this->revenue     = $project->revenue;
    $this->costs       = $project->costs;
    $this->manager_id  = $project->manager_id;

    $this->recalculateMargin(); // 🔥 margem automática

    $this->dispatch('modal-show', name: 'project-modal');
}


    public function delete($id)
    {
        $workspace = auth()->user()->currentWorkspace;
        $workspace->projects()->findOrFail($id)->delete();

        $this->dispatch('toast', text: 'Projeto arquivado.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset([
            'editingId',
            'name',
            'description',
            'budget',
            'status',
            'start_date',
            'deadline',
            'revenue',
            'costs',
            'margin',
            'manager_id',
        ]);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        $projects = $workspace->projects()
            ->with(['manager'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();

        $totalBudget = $projects->sum('budget');
        $activeCount = $projects->where('status', 'em_curso')->count();
        $avgMargin   = $projects->avg(fn ($p) => $p->margin ?? 0);

        // 🔥 Como a coluna status não existe, mostrar todos os colaboradores
        $team = Employee::all();

        return view('livewire.business.project-hub', [
            'projects'    => $projects,
            'totalBudget' => $totalBudget,
            'activeCount' => $activeCount,
            'avgMargin'   => $avgMargin,
            'team'        => $team,
        ]);
    }
}
