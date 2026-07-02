<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class TaskHub extends Component
{
    use WithPagination;

    // FILTROS
    public $search = '';
    public $projectFilter = '';
    public $statusFilter = '';

    // CAMPOS DO FORMULÁRIO
    public $title, $description, $project_id, $user_id;
    public $priority = 'media';
    public $due_date, $estimated_hours;
    public $editingId = null;

    // AUDITORIA
    public $auditLog = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'project_id' => 'required|exists:projects,id',
        'priority' => 'required|in:baixa,media,alta,critica',
        'due_date' => 'nullable|date',
        'estimated_hours' => 'nullable|numeric|min:0',
    ];

    /**
     * Helper para criar notificações no sistema
     */
    protected function notifyUser($userId, $title, $message, $type = 'info')
{
    if (!$userId) return;

    \DB::table('app_notifications')->insert([
        'user_id'    => $userId,
        'title'      => $title,
        'message'    => $message,
        'type'       => $type,
        // Removido aqui também
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

    public function logAction($taskId, $action, $details = null)
    {
        $this->auditLog[] = [
            'task_id' => $taskId,
            'action' => $action,
            'details' => $details,
            'user' => auth()->user()->name,
            'timestamp' => now()->format('d/m/Y H:i:s'),
        ];
    }

    public function toggleTimer($taskId)
    {
        $task = Task::where('workspace_id', auth()->user()->current_workspace_id)
                    ->findOrFail($taskId);

        if ($task->is_timer_running) {
            $elapsed = now()->diffInSeconds($task->timer_started_at);
            $task->update([
                'is_timer_running' => false,
                'total_seconds' => $task->total_seconds + $elapsed,
                'timer_started_at' => null
            ]);
            $this->logAction($taskId, 'timer_stop', "Parou o cronómetro (+{$elapsed}s)");
            $this->dispatch('toast', text: 'Cronómetro parado.');
        } else {
            Task::where('user_id', auth()->id())
                ->where('is_timer_running', true)
                ->get()
                ->each(function ($t) {
                    $elapsed = now()->diffInSeconds($t->timer_started_at);
                    $t->update([
                        'is_timer_running' => false,
                        'total_seconds' => $t->total_seconds + $elapsed,
                        'timer_started_at' => null
                    ]);
                });

            $task->update([
                'is_timer_running' => true,
                'timer_started_at' => now(),
                'status' => 'em_curso'
            ]);

            $this->logAction($taskId, 'timer_start', "Cronómetro iniciado");
            $this->dispatch('toast', text: 'Cronómetro iniciado! Bom trabalho.');
        }
    }

    public function save()
    {
        $this->validate();

        $isNew = $this->editingId === null;

        $task = auth()->user()->currentWorkspace->tasks()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'project_id' => $this->project_id,
                'user_id' => $this->user_id, // Responsável (ID do utilizador)
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'due_date' => $this->due_date,
                'estimated_hours' => $this->estimated_hours,
                'workspace_id' => auth()->user()->current_workspace_id,
            ]
        );

        // NOTIFICAÇÃO DE ATRIBUIÇÃO
        if ($this->user_id) {
            $title = $isNew ? 'Nova Tarefa Atribuída 📋' : 'Tarefa Atualizada 🔄';
            $msg = $isNew ? "Foi-te atribuída a missão: {$task->title}" : "Os detalhes da tarefa '{$task->title}' foram alterados.";

            // Não notifica se o utilizador atribuir a si próprio
            if ($this->user_id != auth()->id()) {
                $this->notifyUser($this->user_id, $title, $msg, 'info');
            }
        }

        $this->logAction($task->id, $isNew ? 'task_create' : 'task_update', $task->title);
        $this->resetForm();
        $this->dispatch('modal-close', name: 'task-modal');
        $this->dispatch('toast', text: 'Tarefa guardada com sucesso!');
    }

    public function updateStatus($id, $newStatus)
    {
        $task = Task::findOrFail($id);
        $workspace = auth()->user()->currentWorkspace;

        if ($newStatus === 'concluida' && $task->is_timer_running) {
            $elapsed = now()->diffInSeconds($task->timer_started_at);
            $task->update([
                'is_timer_running' => false,
                'total_seconds' => $task->total_seconds + $elapsed,
                'timer_started_at' => null
            ]);
        }

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'concluida') {
            $updateData['completed_at'] = now();

            // NOTIFICAÇÃO PARA O ADMIN/CEO (Quando um colaborador termina uma tarefa)
            $owner = $workspace->users()->wherePivot('role', 'admin')->first();
            if ($owner && auth()->id() != $owner->id) {
                $this->notifyUser($owner->id, 'Missão Concluída! ✅', auth()->user()->name . " finalizou a tarefa: " . $task->title, 'success');
            }
        }

        $task->update($updateData);
        $this->logAction($id, 'status_change', "Estado → {$newStatus}");
        $this->dispatch('toast', text: 'Estado atualizado.');
    }

    public function delete($id)
    {
        $task = Task::findOrFail($id);
        $title = $task->title;
        $assigneeId = $task->user_id;

        $task->delete();

        // Notifica o colaborador que a tarefa dele foi removida
        if ($assigneeId && $assigneeId != auth()->id()) {
            $this->notifyUser($assigneeId, 'Tarefa Removida 🗑️', "A tarefa '{$title}' foi eliminada do teu terminal.");
        }

        $this->logAction($id, 'task_delete', "Missão eliminada");
        $this->dispatch('toast', text: 'Tarefa eliminada.', variant: 'warning');
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

        $this->logAction($id, 'task_edit_open', "Editar missão");
        $this->dispatch('modal-show', name: 'task-modal');
    }

    public function resetForm()
    {
        $this->reset(['title', 'description', 'project_id', 'user_id', 'priority', 'due_date', 'estimated_hours', 'editingId']);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        $query = $workspace->tasks()
            ->with(['project', 'assignee'])
            ->where('title', 'like', '%' . $this->search . '%')
            ->when($this->projectFilter, fn($q) => $q->where('project_id', $this->projectFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter));

        $tasks = $query->orderBy('is_timer_running', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();

        return view('livewire.business.task-hub', [
            'tasks' => $tasks,
            'projects' => $workspace->projects,
            'team' => $workspace->users, // Usar users do workspace
            'pendingCount' => $tasks->where('status', '!=', 'concluida')->count(),
            'overdueCount' => $tasks->filter(fn($t) => $t->isOverdue())->count(),
            'completionRate' => $tasks->count() > 0 ? ($tasks->where('status', 'concluida')->count() / $tasks->count()) * 100 : 0,
            'auditLog' => $this->auditLog,
        ]);
    }
}
