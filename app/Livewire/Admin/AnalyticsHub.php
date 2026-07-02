<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AnalyticsHub extends Component
{
    use WithPagination;

    public $period = '30';
    public $searchUser = '';

    // Propriedades para o Modal de Dados Profundos
    public $detailedUser = null;
    public $userFullStats = [];

    /**
     * Carrega todos os dados reais e extra do utilizador para o Modal
     */
    public function showUserModal($userId)
    {
        // 1. Localizar o utilizador
        $user = User::findOrFail($userId);
        $this->detailedUser = $user;

        // 2. CÁLCULO DE DADOS PROFUNDOS E REAIS
        $this->userFullStats = [
            'total_spent' => (float) DB::table('expenses')->where('user_id', $userId)->sum('amount'),
            'total_earned' => (float) DB::table('incomes')->where('user_id', $userId)->sum('amount'),
            'avg_expense' => (float) DB::table('expenses')->where('user_id', $userId)->avg('amount') ?? 0,

            'reminders_done' => DB::table('reminders')->where('user_id', $userId)->where('completed', true)->count(),
            'reminders_pending' => DB::table('reminders')->where('user_id', $userId)->where('completed', false)->count(),

            'goals_reached' => DB::table('goals')->where('user_id', $userId)->whereRaw('current_amount >= target_amount')->count(),
            'total_goals' => DB::table('goals')->where('user_id', $userId)->count(),

            'last_logs' => DB::table('activity_logs')->where('user_id', $userId)->latest()->limit(10)->get(),
            'workspaces_count' => DB::table('workspace_user')->where('user_id', $userId)->count(),

            'ai_messages_count' => DB::table('chat_messages')->where('user_id', $userId)->count(),
            'ai_tokens_estimate' => DB::table('chat_messages')->where('user_id', $userId)->sum('tokens') ?? 0,

            // Cálculo de progresso para o próximo nível (assumindo 1000 XP por nível)
            'xp_progress' => ($user->xp % 1000) / 10,
        ];

        // 3. Abrir o modal
        $this->dispatch('modal-show', name: 'user-deep-details');
    }

    /**
     * ESTATÍSTICAS GERAIS: Adoção de Funcionalidades
     */
    public function getFeatureUsageProperty()
    {
        $totalUsers = User::count() ?: 1;

        $getUsage = function($table) use ($totalUsers) {
            try {
                return round((DB::table($table)->distinct('user_id')->count() / $totalUsers) * 100);
            } catch (\Exception $e) { return 0; }
        };

        return [
            'reminders'   => $getUsage('reminders'),
            'chatbot'     => $getUsage('chat_messages'),
            'goals'       => $getUsage('goals'),
            'investments' => $getUsage('investments'),
        ];
    }

    /**
     * ESTATÍSTICAS GERAIS: Onboarding
     */
    public function getOnboardingStatsProperty()
    {
        return [
            'registered' => User::count(),
            'setup_profile' => User::whereNotNull('name')->where('name', '!=', '')->count(),
            'created_first_expense' => DB::table('expenses')->distinct('user_id')->count(),
            'completed_tutorial' => User::where('onboarding_completed', true)->count(),
        ];
    }

    /**
     * ESTATÍSTICAS GERAIS: Dispositivos
     */
    public function getDeviceStatsProperty()
    {
        try {
            $sessions = DB::table('sessions')->select('user_agent')->get();
            $mobile = 0; $desktop = 0; $total = $sessions->count();
            if ($total === 0) return [['name' => 'Computador', 'value' => 100, 'icon' => 'computer-desktop']];

            foreach ($sessions as $s) {
                if (preg_match('/mobile/i', $s->user_agent)) $mobile++; else $desktop++;
            }
            return [
                ['name' => 'Telemóvel', 'value' => round(($mobile / $total) * 100), 'icon' => 'device-phone-mobile'],
                ['name' => 'Computador', 'value' => round(($desktop / $total) * 100), 'icon' => 'computer-desktop'],
            ];
        } catch (\Exception $e) { return [['name' => 'Computador', 'value' => 100, 'icon' => 'computer-desktop']]; }
    }

    public function render()
    {
        return view('livewire.admin.analytics-hub', [
            'featureUsage' => $this->featureUsage,
            'onboarding' => $this->onboardingStats,
            'devices' => $this->getDeviceStatsProperty(),
            'individualStats' => User::query()
                ->when($this->searchUser, function($q) {
                    $q->where('name', 'like', '%' . $this->searchUser . '%')
                      ->orWhere('email', 'like', '%' . $this->searchUser . '%');
                })
                ->addSelect([
                    'chatbot_count' => DB::table('chat_messages')->selectRaw('count(*)')->whereColumn('user_id', 'users.id'),
                    'reminders_count' => DB::table('reminders')->selectRaw('count(*)')->whereColumn('user_id', 'users.id'),
                    'goals_count' => DB::table('goals')->selectRaw('count(*)')->whereColumn('user_id', 'users.id'),
                ])
                ->latest()
                ->paginate(10),
        ]);
    }
}
