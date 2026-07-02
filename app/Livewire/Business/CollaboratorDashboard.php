<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Task, Project, User, Employee};
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class CollaboratorDashboard extends Component
{
    public $isClockedIn = false;
    public $isTerminated = false;

    public function mount()
    {
        $user = Auth::user();

        // Verifica na base de dados se existe um ponto aberto hoje
        $this->isClockedIn = DB::table('attendance_logs')
            ->where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->whereNull('clock_out')
            ->exists();
    }

    /**
     * Lógica Real de Registar Ponto (Entrada e Saída)
     */
    public function registerPunch()
{
    $user = Auth::user();
    $today = now()->toDateString();

    // 1. Procurar o ponto aberto
    $log = DB::table('attendance_logs')
        ->where('user_id', $user->id)
        ->where('date', $today)
        ->whereNull('clock_out')
        ->first();

    if (!$log) {
        // --- ENTRADA ---
        DB::table('attendance_logs')->insert([
            'user_id' => $user->id,
            'workspace_id' => $user->current_workspace_id,
            'date' => $today,
            'clock_in' => now()->toDateTimeString(), // Guarda a string completa
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->isClockedIn = true;
        $this->dispatch('toast', variant: 'success', heading: 'Entrada', message: 'Ponto de entrada registado.');
    } else {
        // --- SAÍDA ---
        $now = now();
        $clockIn = \Carbon\Carbon::parse($log->clock_in);

        // Calculamos a diferença e usamos abs() para garantir que nunca seja negativo
        // Se a diferença for menor que 1 minuto, assume 1 para não ficar 0h 0m
        $totalMinutes = abs($now->diffInMinutes($clockIn));

        DB::table('attendance_logs')->where('id', $log->id)->update([
            'clock_out' => $now->toDateTimeString(),
            'total_minutes' => $totalMinutes,
            'updated_at' => $now,
        ]);
        $this->isClockedIn = false;
        $this->dispatch('toast', variant: 'success', heading: 'Saída', message: 'Ponto de saída registado.');
    }
}

    public function exitBusinessMode()
    {
        $user = Auth::user();
        $personalWs = $user->workspaces()->where('type', 'personal')->first();
        if ($personalWs) {
            $user->update(['current_workspace_id' => $personalWs->id]);
        }
        session()->forget('viewing_as_collaborator_id');
        return redirect()->route('dashboard');
    }

    public function acknowledgeTermination()
    {
        $user = Auth::user();
        if ($user->current_workspace_id) {
            $user->workspaces()->detach($user->current_workspace_id);
        }
        $user->update(['current_workspace_id' => null]);
        return redirect()->route('hub.business.gateway');
    }

    public function completeTask($taskId)
    {
        $user = Auth::user();
        $task = Task::where('workspace_id', $user->current_workspace_id)
            ->where('assigned_to', $user->id)
            ->findOrFail($taskId);

        $task->update(['status' => 'concluido', 'completed_at' => now()]);
        $this->dispatch('toast', variant: 'success', heading: 'Tarefa Finalizada!');
    }

    public function render()
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return redirect()->route('hub.business.gateway');
        }

        $employee = Employee::where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->first();

        if ($employee && ($employee->terminated_at || !$employee->active)) {
            $this->isTerminated = true;
        }

        $myTasks = Task::where('workspace_id', $workspace->id)
            ->where('assigned_to', $user->id)
            ->where('status', '!=', 'concluido')
            ->orderBy('due_date', 'asc')
            ->get();

        $stats = [
            'pending' => $myTasks->count(),
            'completed_today' => Task::where('workspace_id', $workspace->id)
                ->where('assigned_to', $user->id)
                ->where('status', 'concluido')
                ->whereDate('updated_at', now())
                ->count(),
            'overdue' => Task::where('workspace_id', $workspace->id)
                ->where('assigned_to', $user->id)
                ->where('status', '!=', 'concluido')
                ->where('due_date', '<', now())
                ->count(),
        ];

        $myProjects = $workspace->projects()->where('status', 'em_curso')->get();

        $ceo = $workspace->owner;

        return view('livewire.business.collaborator-dashboard', [
            'workspace'  => $workspace,
            'myTasks'    => $myTasks,
            'stats'      => $stats,
            'myProjects' => $myProjects,
            'ceo'        => $ceo,
        ]);
    }
}
