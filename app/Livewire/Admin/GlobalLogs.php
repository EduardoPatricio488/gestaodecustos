<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\File;

#[Layout('components.layouts.app')]
class GlobalLogs extends Component
{
    use WithPagination;

    public $filterAction = '';

    /**
     * Limpa todos os logs antigos (Manutenção).
     */
    public function clearOldLogs()
    {
        // Remove logs com mais de 30 dias
        ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
        $this->dispatch('toast', text: 'Logs antigos limpos com sucesso.');
    }

    public function render()
    {
        // Puxamos os logs de TODOS os utilizadores e espaços
        $logs = ActivityLog::withoutGlobalScopes()
            ->with(['user']) // Carrega o utilizador que fez a ação
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.global-logs', [
            'logs' => $logs,
            'stats' => [
                'total_actions' => ActivityLog::withoutGlobalScopes()->count(),
                'unique_users_active' => ActivityLog::withoutGlobalScopes()->distinct('user_id')->count(),
                'last_error' => $this->getLastLaravelError(),
            ]
        ]);
    }

    /**
     * Tenta ler o último erro real do ficheiro de log do servidor.
     */
    private function getLastLaravelError()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $lines = explode("\n", $content);
            return count($lines) > 2 ? substr(end($lines), 0, 100) . '...' : 'Sem erros recentes.';
        }
        return 'Log de erros não encontrado.';
    }
}
