<?php

namespace App\Livewire;

use App\Models\AppNotification;
use Livewire\Component;

class NotificationCenter extends Component
{
    /**
     * Marcar uma notificação como lida e navegar para o link
     */
    public function readAndNavigate($id)
    {
        $notification = AppNotification::where('user_id', auth()->id())->findOrFail($id);

        $notification->markAsRead();

        if ($notification->link) {
            $path = parse_url($notification->link, PHP_URL_PATH);
            $query = parse_url($notification->link, PHP_URL_QUERY);

            // As notificações só podem navegar dentro da própria aplicação.
            if (
                is_string($path)
                && str_starts_with($path, '/')
                && ! str_starts_with($path, '//')
                && ! str_contains($path, '\\')
            ) {
                return $this->redirect($path.($query ? '?'.$query : ''), navigate: true);
            }
        }
    }

    /**
     * Marcar todas como lidas de uma vez
     */
    public function markAllAsRead()
    {
        auth()->user()->appNotifications()->unread()->update(['read_at' => now()]);
        $this->dispatch('toast', text: 'Todas as notificações foram lidas.');
    }

    /**
     * Limpar histórico (apagar lidas)
     */
    public function clearHistory()
    {
        auth()->user()->appNotifications()->whereNotNull('read_at')->delete();
        $this->dispatch('toast', text: 'Histórico de alertas limpo.');
    }

    public function render()
    {
        $user = auth()->user();

        // Vamos buscar as últimas 10 notificações (lidas e não lidas)
        // Mas priorizamos as não lidas no topo
        $notifications = $user->appNotifications()
            ->orderByRaw('read_at IS NULL DESC')
            ->latest()
            ->take(10)
            ->get();

        $unreadCount = $user->appNotifications()->unread()->count();

        return view('livewire.notification-center', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
