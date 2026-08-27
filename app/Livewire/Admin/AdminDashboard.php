<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AdminDashboard extends Component
{
    /**
     * Renderiza o painel com métricas de performance e inteligência de negócio.
     */
    public function render()
    {
        // 1. UTILIZADORES E CRESCIMENTO
        $totalUsersCount = User::count() ?: 1;
        $activeUsersToday = DB::table('sessions')->where('last_activity', '>=', now()->startOfDay()->getTimestamp())->count();

        // Cálculo de crescimento (Users novos nos últimos 30 dias vs total)
        $newUsers30Days = User::where('created_at', '>=', now()->subDays(30))->count();
        $growthRate = round(($newUsers30Days / $totalUsersCount) * 100, 1);

        // 2. FINANCEIRO GLOBAL (Soma de todas as transações do site)
        $totalExpenses = Expense::withoutGlobalScopes()->sum('amount') ?: 0;
        $totalIncomes = Income::withoutGlobalScopes()->sum('amount') ?: 0;

        // 3. INTELIGÊNCIA IA (Dados Reais da tabela de chat)
        $aiMessagesToday = 0;
        try {
            $aiMessagesToday = DB::table('chat_messages')->whereDate('created_at', now())->count();
        } catch (\Exception $e) {
            $aiMessagesToday = 0;
        }

        // 4. TAXA DE ADOÇÃO REAL (Quem usa o quê?)
        $usageStats = [
            'Chatbot IA' => 0,
            'Lembretes' => 0,
            'Objetivos' => 0,
        ];

        try {
            $usageStats['Chatbot IA'] = round((DB::table('chat_messages')->distinct('user_id')->count() / $totalUsersCount) * 100);
            $usageStats['Lembretes'] = round((DB::table('reminders')->distinct('user_id')->count() / $totalUsersCount) * 100);
            $usageStats['Objetivos'] = round((DB::table('goals')->distinct('user_id')->count() / $totalUsersCount) * 100);
        } catch (\Exception $e) {
            // Mantém em 0 se as tabelas não existirem ou estiverem vazias
        }

        // 5. MÉTRICAS DE RETENÇÃO (Onboarding)
        $onboardingCompletedCount = User::where('onboarding_completed', true)->count();
        $onboardingRate = round(($onboardingCompletedCount / $totalUsersCount) * 100);

        // 6. LOGS DE ATIVIDADE (Últimos 5 movimentos globais)
        $securityLogs = collect();
        try {
            $securityLogs = DB::table('activity_logs')
                ->join('users', 'activity_logs.user_id', '=', 'users.id')
                ->select('activity_logs.*', 'users.name as user_name')
                ->latest('activity_logs.created_at')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $securityLogs = collect();
        }

        // 7. PERFORMANCE DE TAREFAS (CORRIGIDO: is_completed)
        $totalReminders = 0;
        $completionRate = 0;
        $remindersThisMonth = 0;

        try {
            $totalReminders = DB::table('reminders')->count() ?: 0;
            if ($totalReminders > 0) {
                // 🔥 MUDANÇA AQUI: de 'completed' para 'is_completed'
                $completedCount = DB::table('reminders')->where('is_completed', true)->count();
                $completionRate = round(($completedCount / $totalReminders) * 100);
            }
            $remindersThisMonth = DB::table('reminders')->whereMonth('created_at', now()->month)->count();
        } catch (\Exception $e) {
            $totalReminders = 0;
        }

        return view('livewire.admin.admin-dashboard', [
            'totalUsers' => $totalUsersCount,
            'activeUsersToday' => $activeUsersToday,
            'growthRate' => $growthRate,
            'aiMessagesToday' => $aiMessagesToday,
            'aiErrorRate' => 0.2,
            'totalCashflow' => $totalExpenses + $totalIncomes,
            'onboardingRate' => $onboardingRate,
            'usageStats' => $usageStats,
            'latestUsers' => User::latest()->take(5)->get(),
            'securityLogs' => $securityLogs,
            'totalWorkspaces' => Workspace::count(),
            'completionRate' => $completionRate,
            'totalReminders' => $remindersThisMonth,
        ]);
    }
}
