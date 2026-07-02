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

    // Para o modal
    public $activeTask = null;

    /**
     * Abre o modal com os detalhes da tarefa
     */
    public function openTask($taskId)
    {
        $task = Task::where('workspace_id', auth()->user()->current_workspace_id)
                    ->with(['project', 'assignee'])
                    ->findOrFail($taskId);

        $this->activeTask = $task;

        $this->dispatch('open-modal', name: 'task-modal');
    }

    /**
     * Editar tarefa (podes ligar a outro modal se quiseres)
     */
    public function editTask($taskId)
    {
        $task = Task::where('workspace_id', auth()->user()->current_workspace_id)
                    ->findOrFail($taskId);

        // Aqui podes abrir outro modal de edição se quiseres
        $this->activeTask = $task;

        $this->dispatch('toast', text: 'Modo de edição ainda não implementado.', variant: 'info');
    }

    /**
     * Eliminar tarefa
     */
    public function deleteTask($taskId)
    {
        $task = Task::where('workspace_id', auth()->user()->current_workspace_id)
                    ->findOrFail($taskId);

        $task->delete();

        $this->dispatch('toast', text: 'Tarefa eliminada com sucesso!', variant: 'danger');
    }

    /**
     * Atualiza o estado da tarefa (Kanban)
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

    /**
     * Renderização principal
     */
    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        if (!$workspace) {
            return <<<'HTML'
                <div class="p-10 text-center italic text-zinc-500">
                    Nenhum workspace empresarial selecionado.
                </div>
            HTML;
        }

        // Projetos para o filtro
        $projects = $workspace->projects()->get();

        // Query principal
        $query = $workspace->tasks()
            ->with(['project', 'assignee'])
            ->where('title', 'like', '%' . $this->search . '%')
            ->when($this->activeProjectId, fn($q) => $q->where('project_id', $this->activeProjectId));

        $allTasks = $query->orderBy('due_date', 'asc')->get();

        return view('livewire.business.task-timeline', [
            'projects' => $projects,
            'pendingTasks' => $allTasks->where('status', 'pendente'),
            'inProgressTasks' => $allTasks->where('status', 'em_curso'),
            'completedTasks' => $allTasks->where('status', 'concluida'),
            'overdueCount' => $allTasks->filter(fn($t) => $t->isOverdue())->count(),
        ]);
    }
}
