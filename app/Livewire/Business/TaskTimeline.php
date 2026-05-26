<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class TaskTimeline extends Component
{
    public $search = '';
    public $activeProjectId = null;

    /**
     * Atualiza o estado da tarefa (Kanban Drag/Drop alternativo via click)
     */
    public function updateTaskStatus($taskId, $newStatus)
    {
        $task = Task::where('workspace_id', auth()->user()->current_workspace_id)
                    ->findOrFail($taskId);

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'concluida') {
            $updateData['completed_at'] = now();
        } else {
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        $this->dispatch('toast', text: 'Estado da tarefa atualizado com sucesso!', variant: 'success');
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        if (!$workspace) {
            return <<<'HTML'
                <div class="p-10 text-center italic text-zinc-500">Nenhum workspace empresarial selecionado.</div>
            HTML;
        }

        // Buscar todos os projetos para o filtro
        $projects = $workspace->projects()->get();

        // Query principal de tarefas filtrada por busca e projeto
        $query = $workspace->tasks()->with(['project', 'assignee'])
            ->where('title', 'like', '%' . $this->search . '%')
            ->when($this->activeProjectId, fn($q) => $q->where('project_id', $this->activeProjectId));

        $allTasks = $query->orderBy('due_date', 'asc')->get();

        // Organizar tarefas por colunas para o Kanban
        return view('livewire.business.task-timeline', [
            'projects' => $projects,
            'pendingTasks' => $allTasks->where('status', 'pendente'),
            'inProgressTasks' => $allTasks->where('status', 'em_curso'),
            'reviewTasks' => $allTasks->where('status', 'revisao'),
            'completedTasks' => $allTasks->where('status', 'concluida'),
            'overdueCount' => $allTasks->filter(fn($t) => $t->isOverdue())->count(),
        ]);
    }
}
