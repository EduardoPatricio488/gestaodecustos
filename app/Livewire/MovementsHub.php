<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Subscription;
use App\Models\BankTransfer;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class MovementsHub extends Component
{
    public string $type = 'all';
    public string $search = '';
    public string $period = 'all';

    public function clearFilters(): void
    {
        $this->type = 'all';
        $this->search = '';
        $this->period = 'all';
    }

    public function render()
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;
        $personal = $user->currentWorkspace?->type === 'personal';
        $scope = fn ($query) => $query->where('workspace_id', $workspaceId)
            ->when($personal, fn ($q) => $q->where('user_id', $user->id));

        $items = collect();

        if (in_array($this->type, ['all', 'expense'], true)) {
            $items = $items->concat($scope(Expense::query()->with('category'))->get()->map(fn ($item) => [
                'key' => 'expense-'.$item->id, 'type' => 'expense', 'type_label' => 'Despesa',
                'title' => $item->title ?: $item->description ?: 'Despesa',
                'detail' => $item->category?->name ?: 'Sem categoria',
                'date' => $item->spent_at, 'amount' => (float) ($item->amount_paid ?: $item->amount),
                'direction' => 'out', 'status' => $item->status ?: 'Registada',
            ]));
        }

        if (in_array($this->type, ['all', 'income'], true)) {
            $items = $items->concat($scope(Income::query()->with('category'))
                ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
                ->get()->map(fn ($item) => [
                    'key' => 'income-'.$item->id, 'type' => 'income', 'type_label' => 'Receita',
                    'title' => $item->description ?: 'Receita',
                    'detail' => $item->category?->name ?: ($item->source ?: 'Receita'),
                    'date' => $item->received_at, 'amount' => (float) $item->amount,
                    'direction' => 'in', 'status' => 'Recebida',
                ]));
        }

        if (in_array($this->type, ['all', 'transfer'], true)) {
            $items = $items->concat($scope(BankTransfer::query()->with(['fromAccount', 'toAccount']))->get()->map(fn ($item) => [
                'key' => 'transfer-'.$item->id, 'type' => 'transfer', 'type_label' => 'Transferência',
                'title' => $item->description ?: ($item->recipient ?: 'Transferência bancária'),
                'detail' => ($item->fromAccount?->name ?: 'Origem externa').' → '.($item->toAccount?->name ?: 'Destino externo'),
                'date' => $item->transferred_at, 'amount' => (float) $item->amount,
                'direction' => ($item->direction ?? 'out') === 'in' ? 'in' : 'out',
                'status' => $item->status ?: 'Concluída',
            ]));
        }

        if (in_array($this->type, ['all', 'subscription'], true)) {
            $items = $items->concat($scope(Subscription::query())->get()->map(fn ($item) => [
                'key' => 'subscription-'.$item->id, 'type' => 'subscription', 'type_label' => 'Assinatura',
                'title' => $item->name, 'detail' => 'Periodicidade: '.($item->cycle ?: 'Recorrente'),
                'date' => $item->renewal_date ?: $item->started_at, 'amount' => (float) $item->amount,
                'direction' => 'out', 'status' => $item->is_active ? 'Ativa' : 'Inativa',
            ]));
        }

        $items = $items->filter(function ($item) {
            $date = $item['date'] ? Carbon::parse($item['date']) : null;

            if ($this->period === 'month' && (! $date || ! $date->isCurrentMonth())) return false;
            if ($this->period === 'year' && (! $date || ! $date->isCurrentYear())) return false;

            $needle = mb_strtolower(trim($this->search));

            return $needle === '' || str_contains(mb_strtolower($item['title'].' '.$item['detail'].' '.$item['type_label']), $needle);
        })->sortByDesc(fn ($item) => $item['date'] ? Carbon::parse($item['date'])->getTimestamp() : 0)->values();

        return view('livewire.movements-hub', [
            'movements' => $items,
            'movementCount' => $items->count(),
            'incomeTotal' => $items->where('direction', 'in')->sum('amount'),
            'expenseTotal' => $items->where('direction', 'out')->sum('amount'),
        ]);
    }
}
