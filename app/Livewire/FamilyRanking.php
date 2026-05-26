<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Expense;
use App\Models\Income;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class FamilyRanking extends Component
{
    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $monthStart = now()->startOfMonth();

        // 1. RANKING COMPLETO (Ganhos, Gastos e Saldo por utilizador)
        $memberStats = User::join('workspace_user', 'users.id', '=', 'workspace_user.user_id')
            ->where('workspace_user.workspace_id', $workspace->id)
            ->select('users.*')
            ->withSum(['expenses' => function($q) use ($monthStart, $workspace) {
                $q->where('spent_at', '>=', $monthStart)->where('workspace_id', $workspace->id);
            }], 'amount')
            ->withSum(['incomes' => function($q) use ($monthStart, $workspace) {
                $q->where('received_at', '>=', $monthStart)->where('workspace_id', $workspace->id);
            }], 'amount')
            ->get()
            ->map(function($user) {
                $user->total_expenses = $user->expenses_sum_amount ?: 0;
                $user->total_incomes = $user->incomes_sum_amount ?: 0;
                $user->net_balance = $user->total_incomes - $user->total_expenses;
                return $user;
            })->sortByDesc('net_balance');

        // 2. RANKING DE ATIVIDADE (Quem mais trabalha na conta)
        $topRecorders = User::join('workspace_user', 'users.id', '=', 'workspace_user.user_id')
            ->where('workspace_user.workspace_id', $workspace->id)
            ->select('users.*')
            ->withCount(['expenses' => function($q) use ($monthStart, $workspace) {
                $q->where('spent_at', '>=', $monthStart)->where('workspace_id', $workspace->id);
            }])
            ->orderByDesc('expenses_count')
            ->get();

        // 3. ÚLTIMAS ATIVIDADES DO GRUPO (O que fizeram na conta)
        $recentActivities = ActivityLog::with('user')
            ->where('workspace_id', $workspace->id)
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.family-ranking', [
            'workspaceName' => $workspace->name,
            'memberStats' => $memberStats,
            'topRecorders' => $topRecorders,
            'recentActivities' => $recentActivities,
            'levelLeaders' => $workspace->users()->orderByDesc('level')->orderByDesc('xp')->get(),
        ]);
    }
}
