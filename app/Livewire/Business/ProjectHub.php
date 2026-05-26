<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Project;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ProjectHub extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;

    // Campos do formulário
    public $name, $description, $budget, $status = 'em_curso', $start_date, $deadline;

    protected $rules = [
        'name' => 'required|string|max:255',
        'budget' => 'required|numeric|min:0',
        'status' => 'required|in:planeamento,em_curso,concluido,pausado',
    ];

    public function save()
    {
        $this->validate();

        auth()->user()->currentWorkspace->projects()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => auth()->user()->current_workspace_id,
                'name' => $this->name,
                'description' => $this->description,
                'budget' => $this->budget,
                'status' => $this->status,
                'start_date' => $this->start_date,
                'deadline' => $this->deadline,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'project-modal');
        $this->dispatch('toast', text: 'Projeto atualizado com sucesso!');
    }

    public function edit($id)
    {
        $project = auth()->user()->currentWorkspace->projects()->findOrFail($id);
        $this->editingId = $project->id;
        $this->name = $project->name;
        $this->description = $project->description;
        $this->budget = $project->budget;
        $this->status = $project->status;
        $this->start_date = $project->start_date;
        $this->deadline = $project->deadline;

        $this->dispatch('modal-show', name: 'project-modal');
    }

    public function delete($id)
    {
        auth()->user()->currentWorkspace->projects()->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Projeto arquivado.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset(['name', 'description', 'budget', 'status', 'start_date', 'deadline', 'editingId']);
    }

    public function render()
    {
        $projects = auth()->user()->currentWorkspace->projects()
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();

        return view('livewire.business.project-hub', [
            'projects' => $projects,
            'totalBudget' => $projects->sum('budget'),
            'activeCount' => $projects->where('status', 'em_curso')->count(),
            'avgMargin' => $projects->avg(fn($p) => $project->margin ?? 0) // Usa a inteligência do Modelo
        ]);
    }
}
