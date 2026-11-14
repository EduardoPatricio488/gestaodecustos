<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\Reminder;
use Illuminate\Support\Collection;
use Livewire\Component;

class MobileRecentRecords extends Component
{
    public function getRecordsProperty(): Collection
    {
        $user = auth()->user();
        $since = now()->subHours(48);
        $workspaceId = $user->current_workspace_id;

        $records = collect();

        Expense::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->where('is_company', false)
            ->where('created_at', '>=', $since)
            ->get()
            ->each(fn (Expense $item) => $records->push([
                'id' => 'expense-'.$item->id,
                'type' => 'Despesa',
                'title' => $item->title ?: ($item->description ?: 'Nova despesa'),
                'detail' => $item->spent_at?->format('d/m/Y'),
                'amount' => $item->amount,
                'created_at' => $item->created_at,
                'icon' => '↘',
                'tone' => 'rose',
            ]));

        Income::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->get()
            ->each(fn (Income $item) => $records->push([
                'id' => 'income-'.$item->id,
                'type' => 'Receita',
                'title' => $item->description ?: 'Nova receita',
                'detail' => $item->received_at?->format('d/m/Y'),
                'amount' => $item->amount,
                'created_at' => $item->created_at,
                'icon' => '↗',
                'tone' => 'emerald',
            ]));

        Investment::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->get()
            ->each(fn (Investment $item) => $records->push([
                'id' => 'investment-'.$item->id,
                'type' => 'Investimento',
                'title' => $item->name ?: ($item->symbol ?: 'Novo investimento'),
                'detail' => $item->symbol,
                'amount' => null,
                'created_at' => $item->created_at,
                'icon' => '◆',
                'tone' => 'blue',
            ]));

        Goal::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->get()
            ->each(fn (Goal $item) => $records->push([
                'id' => 'goal-'.$item->id,
                'type' => 'Objetivo',
                'title' => $item->name,
                'detail' => 'Valor alvo',
                'amount' => $item->target_amount,
                'created_at' => $item->created_at,
                'icon' => '◎',
                'tone' => 'violet',
            ]));

        Reminder::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->get()
            ->each(fn (Reminder $item) => $records->push([
                'id' => 'reminder-'.$item->id,
                'type' => 'Lembrete',
                'title' => $item->title,
                'detail' => $item->remind_at?->format('d/m/Y H:i'),
                'amount' => null,
                'created_at' => $item->created_at,
                'icon' => '◷',
                'tone' => 'amber',
            ]));

        return $records->sortByDesc('created_at')->values();
    }

    public function render()
    {
        return view('livewire.mobile-recent-records');
    }
}
