<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Client;

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
    public $client_id; // ✅ Nova Propriedade

    protected $rules = [
        'name'       => 'required|string|max:255',
        'budget'     => 'nullable|numeric|min:0',
        'status'     => 'required|in:planeamento,em_curso,concluido,pausado',
        'revenue'    => 'nullable|numeric|min:0',
        'costs'      => 'nullable|numeric|min:0',
        'margin'     => 'nullable|numeric|min:0|max:100',
        'manager_id' => 'nullable|integer',
        'client_id'  => 'nullable|exists:clients,id', // ✅ Validação
        'start_date' => 'nullable|date',
        'deadline'   => 'nullable|date',
    ];

    public function updatedRevenue() { $this->recalculateMargin(); }
    public function updatedCosts() { $this->recalculateMargin(); }

    protected function recalculateMargin()
    {
        $revenue = $this->revenue ?? 0;
        $costs   = $this->costs ?? 0;
        $this->margin = ($revenue > 0) ? round((($revenue - $costs) / $revenue) * 100, 2) : 0;
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
                'client_id'    => $this->client_id, // ✅ Salvar Cliente
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'project-modal');
        $this->dispatch('toast', text: $this->editingId ? 'Projeto atualizado!' : 'Projeto criado com sucesso!');
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
        $this->client_id   = $project->client_id; // ✅ Carregar Cliente

        $this->recalculateMargin();
        $this->dispatch('modal-show', name: 'project-modal');
    }
public function updateProjectClient($projectId, $clientId)
{
    // Procuramos o projeto específico e atualizamos apenas esse
    $project = Project::find($projectId);

    // Se o valor for vazio, pomos null, caso contrário o ID
    $project->client_id = $clientId ?: null;
    $project->save();

    $this->dispatch('toast', text: 'Cliente do projeto "' . $project->name . '" atualizado!');
}
    public function delete($id)
    {
        auth()->user()->currentWorkspace->projects()->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Projeto arquivado.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset(['editingId', 'name', 'description', 'budget', 'status', 'start_date', 'deadline', 'revenue', 'costs', 'margin', 'manager_id', 'client_id']);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        $projects = $workspace->projects()
            ->with(['manager', 'client']) // ✅ Eager load do cliente
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();

        return view('livewire.business.project-hub', [
            'projects'    => $projects,
            'totalBudget' => $projects->sum('budget'),
            'activeCount' => $projects->where('status', 'em_curso')->count(),
            'avgMargin'   => $projects->avg('margin'),
            'team'        => Employee::all(),
            'clients'     => Client::where('workspace_id', $workspace->id)->get(), // ✅ Lista de clientes
        ]);
    }
}
