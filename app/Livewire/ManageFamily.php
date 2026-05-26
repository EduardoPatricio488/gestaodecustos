<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Workspace;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class ManageFamily extends Component
{
    public $workspaceName;
    public $inviteCode;

    public function mount()
    {
        $workspace = auth()->user()->currentWorkspace;
        if ($workspace) {
            $this->workspaceName = $workspace->name;
            $this->inviteCode = $workspace->invite_code;
        }
    }

    public function updateWorkspaceName()
    {
        if (!auth()->user()->is_admin) {
            $this->dispatch('toast', variant: 'error', text: 'Permissão negada.');
            return;
        }
        auth()->user()->currentWorkspace->update(['name' => $this->workspaceName]);
        $this->dispatch('toast', text: 'Nome do espaço atualizado!');
    }

    public function updateRole($userId, $newRole)
    {
        if (!auth()->user()->is_admin) return;
        if ($userId === auth()->id()) return;

        DB::table('workspace_user')
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', $userId)
            ->update(['role' => $newRole]);

        $this->dispatch('toast', text: 'Permissão atualizada!');
    }

    public function removeMember($userId)
    {
        if (!auth()->user()->is_admin || $userId === auth()->id()) return;
        auth()->user()->currentWorkspace->users()->detach($userId);
        $this->dispatch('toast', text: 'Membro removido.');
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $startOfMonth = now()->startOfMonth();

        // 1. Estatísticas Financeiras por Membro (Performance)
        $memberStats = $workspace->users->map(function ($user) use ($workspace, $startOfMonth) {
            $user->total_incomes = $user->incomes()
                ->where('workspace_id', $workspace->id)
                ->where('received_at', '>=', $startOfMonth)
                ->sum('amount') ?: 0;

            $user->total_expenses = $user->expenses()
                ->where('workspace_id', $workspace->id)
                ->where('spent_at', '>=', $startOfMonth)
                ->sum('amount') ?: 0;

            $user->net_balance = $user->total_incomes - $user->total_expenses;
            return $user;
        });

        // 2. Ranking de Registos (Quem mais trabalhou no app)
        $topRecorders = $workspace->users()
            ->withCount(['expenses' => function($q) use ($workspace) {
                $q->where('workspace_id', $workspace->id);
            }])
            ->orderBy('expenses_count', 'desc')
            ->take(5)
            ->get();

        // 3. Atividades recentes do grupo
        // Nota: Assumindo que tens uma tabela activity_logs ligada ao workspace ou users
        $recentActivities = ActivityLog::whereIn('user_id', $workspace->users->pluck('id'))
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.manage-family', [
            'members' => $workspace->users()->withPivot('role')->get(),
            'iAmAdmin' => auth()->user()->is_admin,
            'memberStats' => $memberStats,
            'topRecorders' => $topRecorders,
            'recentActivities' => $recentActivities
        ]);
    }
}
