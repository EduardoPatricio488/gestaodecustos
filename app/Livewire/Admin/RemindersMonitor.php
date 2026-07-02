<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class RemindersMonitor extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        // 1. Estatísticas de Produtividade
        $totalReminders = DB::table('reminders')->count();
        $completedReminders = DB::table('reminders')->where('completed', true)->count();
        $completionRate = $totalReminders > 0 ? round(($completedReminders / $totalReminders) * 100) : 0;

        // 2. Lembretes Recentes (Cruzado com nomes de utilizadores)
        $reminders = DB::table('reminders')
            ->join('users', 'reminders::user_id', '=', 'users.id')
            ->select('reminders.*', 'users.name as user_name')
            ->when($this->search, function($q) {
                $q->where('reminders.title', 'like', '%' . $this->search . '%')
                  ->orWhere('users.name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.reminders-monitor', [
            'reminders' => $reminders,
            'stats' => [
                'total' => $totalReminders,
                'completed' => $completedReminders,
                'rate' => $completionRate,
                'today' => DB::table('reminders')->whereDate('created_at', now())->count(),
            ]
        ]);
    }
}
