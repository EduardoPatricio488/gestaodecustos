<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AiMonitor extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        // 1. Estatísticas Reais de Uso
        $stats = [
            'total_messages' => DB::table('chat_messages')->count(),
            'messages_today' => DB::table('chat_messages')->whereDate('created_at', now())->count(),
            'error_rate' => DB::table('chat_messages')->count() > 0
                ? round((DB::table('chat_messages')->where('is_error', true)->count() / DB::table('chat_messages')->count()) * 100, 2)
                : 0,
            'active_users' => DB::table('chat_messages')->distinct('user_id')->count(),
        ];

        // 2. Listagem de Conversas Reais
        $conversations = DB::table('chat_messages')
            ->join('users', 'chat_messages.user_id', '=', 'users.id')
            ->select('chat_messages.*', 'users.name as user_name', 'users.email as user_email')
            ->when($this->search, function($q) {
                $q->where('chat_messages.content', 'like', '%' . $this->search . '%')
                  ->orWhere('users.name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.ai-monitor', [
            'stats' => $stats,
            'conversations' => $conversations
        ]);
    }
}
