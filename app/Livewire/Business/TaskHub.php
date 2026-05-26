<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class TaskHub extends Component
{
    use WithPagination;

    public $search = '';
    public $projectFilter = '';
    public $statusFilter = '';

    // Campos do Formulário
    public $title, $description, $project_id, $user_id, $priority = 'media', $due_date, $estimated_hours;
    public $editingId = null;

    protected $rules = [
        'title' => 'required|string|max:255',
        'project_id' => 'required|exists:projects,id',
        'priority' => 'required|in:baixa,media,alta,critica',
        'due_date' => 'nullable|date',
    ];

    /**
     * FUNÇÃO ESTRELA: Ligar/Desligar Cronómetro
     */
    public function toggleTimer($taskId)
    {
        $task = Task::where('workspace_id', auth()->user()->current_workspace_id)
                    ->findOrFail($taskId);

        if ($task->is_timer_running) {
            // PARAR: Calcula o tempo decorrido e soma ao total
            $elapsed = now()->diffInSeconds($task->timer_started_at);
            $task->update([
                'is_timer_running' => false,
                'total_seconds' => $task->total_seconds + $elapsed,
                'timer_started_at' => null
            ]);
            $this->dispatch('toast', text: 'Cronómetro parado.');
        } else {
            // INICIAR: Primeiro para todos os outros timers ativos deste utilizador
            Task::where('user_id', auth()->id())
                ->where('is_timer_running', true)
                ->get()
                ->each(fn($t) => $t->stopTimer());

            // Inicia este timer
            $task->update([
                'is_timer_running' => true,
                'timer_started_at' => now(),
                'status' => 'em_curso' // Muda automaticamente para "Em Curso"
            ]);
            $this->dispatch('toast', text: 'Cronómetro iniciado! Bom trabalho.');
        }
    }

    public function save()
    {
        $this->validate();

        auth()->user()->currentWorkspace->tasks()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'project_id' => $this->project_id,
                'user_id' => $this->user_id,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'due_date' => $this->due_date,
                'estimated_hours' => $this->estimated_hours,
                'workspace_id' => auth()->user()->current_workspace_id,
            ]
        );

        $this->resetForm();
        $this->dispatch('modal-close', name: 'task-modal');
        $this->dispatch('toast', text: 'Tarefa guardada com sucesso!');
    }

    public function updateStatus($id, $newStatus)
    {
        $task = Task::findOrFail($id);

        // Se concluir a tarefa, paramos o timer se ele estiver a correr
        if ($newStatus === 'concluida' && $task->is_timer_running) {
            $task->stopTimer();
        }

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'concluida') {
            $updateData['completed_at'] = now();
        }

        $task->update($updateData);
        $this->dispatch('toast', text: 'Estado atualizado.');
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $this->editingId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->project_id = $task->project_id;
        $this->user_id = $task->user_id;
        $this->priority = $task->priority;
        $this->due_date = $task->due_date?->format('Y-m-d');
        $this->estimated_hours = $task->estimated_hours;

        $this->dispatch('modal-show', name: 'task-modal');
    }

    public function delete($id)
    {
        Task::findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Tarefa eliminada.', variant: 'warning');
    }

    public function resetForm()
    {
        $this->reset(['title', 'description', 'project_id', 'user_id', 'priority', 'due_date', 'estimated_hours', 'editingId']);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        $query = $workspace->tasks()->with(['project', 'assignee'])
            ->where('title', 'like', '%' . $this->search . '%')
            ->when($this->projectFilter, fn($q) => $q->where('project_id', $this->projectFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter));

        $tasks = $query->orderBy('is_timer_running', 'desc') // Running timers em primeiro
                       ->orderBy('due_date', 'asc')
                       ->get();

        return view('livewire.business.task-hub', [
            'tasks' => $tasks,
            'projects' => $workspace->projects,
            'team' => $workspace->users,
            'pendingCount' => $tasks->where('status', '!=', 'concluida')->count(),
            'overdueCount' => $tasks->filter(fn($t) => $t->isOverdue())->count(),
            'completionRate' => $tasks->count() > 0 ? ($tasks->where('status', 'concluida')->count() / $tasks->count()) * 100 : 0,
        ]);
    }
}
