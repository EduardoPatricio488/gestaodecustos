<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class GlobalLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $filterAction = '';
    public $filterType = '';

    // Para ver detalhes do log
    public $selectedLog = null;

    protected $queryString = ['search', 'filterAction', 'filterType'];

    public function updatingSearch() { $this->resetPage(); }

    public function clearOldLogs()
    {
        ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
        $this->dispatch('toast', text: 'Histórico antigo (30 dias+) foi eliminado.');
    }

    public function showLogDetails($id)
    {
        $this->selectedLog = ActivityLog::with('user')->find($id);
        $this->dispatch('modal-show', name: 'log-details-modal');
    }

    public function render()
    {
        $logs = ActivityLog::query()
            ->with(['user'])
            ->when($this->search, function($q) {
                $q->where('description', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.global-logs', [
            'logs' => $logs,
            'stats' => [
                'total_actions' => ActivityLog::count(),
                'unique_users_24h' => ActivityLog::where('created_at', '>=', now()->subDay())->distinct('user_id')->count(),
                'security_alerts' => ActivityLog::where('type', 'seguranca')->where('created_at', '>=', now()->subWeek())->count(),
                'last_error' => $this->getLastLaravelError(),
            ]
        ]);
    }

    private function getLastLaravelError()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $content = tailCustom($logPath, 5); // Função hipotética ou ler as últimas linhas
            $lines = array_filter(explode("\n", $content));
            return count($lines) > 0 ? trim(substr(end($lines), 0, 150)) : 'Silêncio no servidor...';
        }
        return 'Ficheiro de logs não acessível.';
    }
}

// Helper simples para não carregar o ficheiro todo em memória
function tailCustom($filepath, $lines = 10) {
    $data = file_get_contents($filepath);
    $data = explode("\n", $data);
    $data = array_slice($data, -$lines);
    return implode("\n", $data);
}
