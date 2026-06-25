<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ProductivityHub extends Component
{
    use WithPagination;

    public $search = '';

    /**
     * Renderiza o Centro de Produtividade com métricas reais e timeline de eventos.
     */
    public function render()
    {
        // 1. MÉTRICAS DE TAREFAS (Lembretes)
        $totalReminders = DB::table('reminders')->count();
        $completedReminders = DB::table('reminders')->where('completed', true)->count();
        $taskRate = $totalReminders > 0 ? round(($completedReminders / $totalReminders) * 100) : 0;

        // 2. MÉTRICAS DE OBJETIVOS (Metas alcançadas)
        $totalGoals = DB::table('goals')->count();
        $reachedGoals = DB::table('goals')->whereRaw('current_amount >= target_amount')->count();
        $goalRate = $totalGoals > 0 ? round(($reachedGoals / $totalGoals) * 100) : 0;

        // 3. FLUXO DE ATIVIDADE HOJE (Soma de interações nas últimas 24h)
        $activityToday = DB::table('expenses')->whereDate('created_at', now())->count() +
                         DB::table('incomes')->whereDate('created_at', now())->count() +
                         DB::table('reminders')->whereDate('created_at', now())->count() +
                         DB::table('chat_messages')->whereDate('created_at', now())->count();

        // 4. RANKING DE UTILIZADORES MAIS ATIVOS (Power Users)
        $topUsers = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', DB::raw('count(activity_logs.id) as actions_count'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('actions_count', 'desc')
            ->limit(10)
            ->get();

        // 5. TIMELINE DE CONQUISTAS (Ações de sucesso em tempo real)
        $recentAchievements = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->where(function($q) {
                $q->where('activity_logs.action', 'like', '%concluiu%')
                  ->orWhere('activity_logs.action', 'like', '%alcançou%')
                  ->orWhere('activity_logs.action', 'like', '%bateu meta%');
            })
            ->select('activity_logs.action', 'activity_logs.created_at', 'users.name as user_name')
            // CORREÇÃO AQUI: Especificamos activity_logs.created_at para evitar ambiguidade
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(6)
            ->get();

        return view('livewire.admin.productivity-hub', [
            'stats' => [
                'task_rate' => $taskRate,
                'goal_rate' => $goalRate,
                'activity_today' => $activityToday,
                'total_reminders' => $totalReminders,
                'total_goals' => $totalGoals,
                'avg_tasks' => $totalReminders > 0 ? round($totalReminders / (max(DB::table('users')->count(), 1)), 1) : 0,
            ],
            'topUsers' => $topUsers,
            'recentAchievements' => $recentAchievements
        ]);
    }
}
