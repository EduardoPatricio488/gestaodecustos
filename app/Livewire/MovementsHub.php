<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
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
            $items = $items->concat($scope(Expense::query()->with(['category', 'supplier', 'bankAccount', 'project', 'task', 'costCenter']))->get()->map(fn ($item) => [
                'key' => 'expense-'.$item->id, 'type' => 'expense', 'type_label' => 'Despesa',
                'title' => $item->title ?: $item->description ?: 'Despesa',
                'detail' => $item->category?->name ?: 'Sem categoria',
                'date' => $item->spent_at, 'amount' => (float) ((float) $item->amount_paid > 0 ? $item->amount_paid : $item->amount),
                'direction' => 'out', 'status' => $item->status ?: 'Registada',
                'details' => [
                    'Descrição' => $item->description,
                    'Categoria' => $item->category?->name,
                    'Fornecedor' => $item->supplier?->name,
                    'Conta bancária' => $item->bankAccount?->name,
                    'Projeto' => $item->project?->name,
                    'Tarefa' => $item->task?->name,
                    'Centro de custo' => $item->costCenter?->name,
                    'Valor total' => number_format((float) $item->amount, 2, ',', '.').' '.($item->currency ?: 'EUR'),
                    'Valor pago' => number_format((float) $item->amount_paid, 2, ',', '.').' '.($item->currency ?: 'EUR'),
                    'Em falta' => number_format(max(0, (float) $item->amount - (float) $item->amount_paid), 2, ',', '.').' '.($item->currency ?: 'EUR'),
                    'IVA' => $item->vat_amount !== null ? number_format((float) $item->vat_amount, 2, ',', '.').' '.($item->currency ?: 'EUR') : null,
                    'Data' => $item->spent_at?->format('d/m/Y'),
                    'Estado' => $item->status,
                    'Notas' => $item->metadata ? json_encode($item->metadata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null,
                ],
            ]));
        }

        if (in_array($this->type, ['all', 'income'], true)) {
            $items = $items->concat($scope(Income::query()->with(['category', 'bankAccount']))
                ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
                ->get()->map(fn ($item) => [
                    'key' => 'income-'.$item->id, 'type' => 'income', 'type_label' => 'Receita',
                    'title' => $item->description ?: 'Receita',
                    'detail' => $item->category?->name ?: ($item->source ?: 'Receita'),
                    'date' => $item->received_at, 'amount' => (float) $item->amount,
                    'direction' => 'in', 'status' => 'Recebida',
                    'details' => [
                        'Descrição' => $item->description,
                        'Categoria' => $item->category?->name,
                        'Origem' => $item->source,
                        'Tipo' => $item->type,
                        'Frequência' => $item->frequency,
                        'Conta bancária' => $item->bankAccount?->name,
                        'Valor' => number_format((float) $item->amount, 2, ',', '.').' '.($item->currency ?: 'EUR'),
                        'Data de recebimento' => $item->received_at?->format('d/m/Y'),
                        'Notas' => $item->notes,
                    ],
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
                'details' => [
                    'Descrição' => $item->description,
                    'Destinatário' => $item->recipient,
                    'Conta de origem' => $item->fromAccount?->name,
                    'Conta de destino' => $item->toAccount?->name,
                    'Direção' => ($item->direction ?? 'out') === 'in' ? 'Entrada' : 'Saída',
                    'Método' => $item->transfer_method,
                    'Categoria' => $item->category,
                    'Valor' => number_format((float) $item->amount, 2, ',', '.').' '.($item->currency ?: 'EUR'),
                    'Data' => $item->transferred_at?->format('d/m/Y'),
                    'Estado' => $item->status,
                    'Notas' => $item->notes,
                ],
            ]));
        }

        if (in_array($this->type, ['all', 'subscription'], true)) {
            $items = $items->concat($scope(Subscription::query())->get()->map(fn ($item) => [
                'key' => 'subscription-'.$item->id, 'type' => 'subscription', 'type_label' => 'Assinatura',
                'title' => $item->name,
                'detail' => 'Periodicidade: '.($item->cycle ?: 'Recorrente'),
                'date' => $item->renewal_date ?: $item->started_at,
                'amount' => (float) $item->amount,
                'direction' => $item->paid_by_company ? 'company' : 'out',
                'status' => ! $item->is_active ? 'Inativa' : ($item->paid_by_company && $item->renewal_date && Carbon::parse($item->renewal_date)->lte(today()) ? 'Pagamento pendente' : ($item->paid_by_company ? 'Pago pela empresa na renovação' : 'Ativa')),
                'details' => [
                    'Nome' => $item->name,
                    'Valor por ciclo' => number_format((float) $item->amount, 2, ',', '.').' €',
                    'Periodicidade' => $item->cycle,
                    'Método de pagamento' => $item->paid_by_company ? 'Pago pela empresa' : $item->payment_method,
                    'Data de início' => $item->started_at?->format('d/m/Y'),
                    'Próxima renovação' => $item->renewal_date?->format('d/m/Y'),
                    'Estado' => $item->is_active ? 'Ativa' : 'Inativa',
                    'Notas' => $item->notes,
                ],
            ]));
            $items = $items->concat($scope(\App\Models\SubscriptionPayment::query()->with('subscription'))->get()->map(fn ($payment) => [
                'key' => 'subscription-payment-'.$payment->id,
                'type' => 'subscription',
                'type_label' => 'Mensalidade',
                'title' => $payment->subscription?->name ?: 'Assinatura',
                'detail' => 'Mensalidade paga pela empresa',
                'date' => $payment->paid_at,
                'amount' => (float) $payment->amount,
                'direction' => 'company',
                'status' => 'Pago pela empresa',
                'details' => [
                    'Assinatura' => $payment->subscription?->name,
                    'Data da mensalidade' => $payment->billing_date?->format('d/m/Y'),
                    'Data de registo do pagamento' => $payment->paid_at?->format('d/m/Y'),
                    'Valor' => number_format((float) $payment->amount, 2, ',', '.').' €',
                    'Origem do pagamento' => $payment->payment_source === 'company' ? 'Empresa' : $payment->payment_source,
                    'Estado' => 'Pago pela empresa',
                ],
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
