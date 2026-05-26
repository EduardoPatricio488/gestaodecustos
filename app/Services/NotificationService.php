<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Carbon\Carbon;

class NotificationService
{
    public static function checkAll(User $user)
    {
        self::checkSubscriptions($user);
        self::checkLowStock($user);
        self::checkOverdueInvoices($user);
    }

    private static function checkSubscriptions(User $user)
    {
        $today = now()->day;
        $subs = $user->subscriptions()->where('billing_day', $today)->where('is_active', true)->get();

        foreach ($subs as $sub) {
            $title = "Pagamento Hoje: {$sub->name}";
            // Evita criar duplicados para o mesmo dia
            if (!self::alreadyNotified($user, $title)) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'message' => "A tua assinatura de {$sub->amount}€ vence hoje.",
                    'type' => 'warning',
                    'link' => route('hub.subscriptions')
                ]);
            }
        }
    }

    private static function checkLowStock(User $user)
    {
        $workspace = $user->currentWorkspace;
        if (!$workspace) return;

        $lowStockProducts = $workspace->products()->get()->filter(fn($p) => $p->isLowStock());

        foreach ($lowStockProducts as $product) {
            $title = "Stock Baixo: {$product->name}";
            if (!self::alreadyNotified($user, $title)) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'title' => $title,
                    'message' => "Resta(m) apenas {$product->stock_quantity} unidade(s) em armazém.",
                    'type' => 'danger',
                    'link' => route('hub.business.inventory')
                ]);
            }
        }
    }

    private static function checkOverdueInvoices(User $user)
    {
        $workspace = $user->currentWorkspace;
        if (!$workspace) return;

        $overdue = $workspace->invoices()->where('status', 'pendente')->where('due_date', '<', now())->get();

        foreach ($overdue as $inv) {
            $title = "Fatura Atrasada: #{$inv->invoice_number}";
            if (!self::alreadyNotified($user, $title)) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'title' => $title,
                    'message' => "O cliente {$inv->client_name} tem um pagamento pendente desde " . $inv->due_date->format('d/m'),
                    'type' => 'danger',
                    'link' => route('hub.business.invoices')
                ]);
            }
        }
    }

    private static function alreadyNotified($user, $title)
    {
        return AppNotification::where('user_id', $user->id)
            ->where('title', $title)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
    }
}
