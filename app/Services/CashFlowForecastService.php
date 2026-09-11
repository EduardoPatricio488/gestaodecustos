<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Workspace;
use Carbon\Carbon;

class CashFlowForecastService
{
    public function getForecast(Workspace $workspace, int $days = 90): array
    {
        $days = max(7, min(365, $days));
        $start = now()->startOfDay();
        $end = now()->addDays($days)->endOfDay();

        $currentBalance = (float) $workspace->bankAccounts()->sum('balance');
        if (! $workspace->bankAccounts()->exists()) {
            $currentBalance = (float) $workspace->getLiquidezAtual();
        }

        $events = collect();

        $pendingInvoices = $workspace->invoices()
            ->whereIn('status', ['pendente', 'vencida'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        foreach ($pendingInvoices as $invoice) {
            $events->push([
                'date' => Carbon::parse($invoice->due_date),
                'amount' => (float) $invoice->total_amount,
                'type' => 'inflow',
                'label' => "Recebimento previsto · Fatura #{$invoice->invoice_number}",
            ]);
        }

        $monthlyPayroll = (float) $workspace->employees()
            ->where('active', true)->where('suspended', false)->whereNull('terminated_at')->sum('salary');

        for ($m = 0; $m <= (int) ceil($days / 30); $m++) {
            $payDate = now()->addMonths($m)->endOfMonth();
            if ($payDate->between($start, $end) && $monthlyPayroll > 0) {
                $events->push(['date' => $payDate, 'amount' => -$monthlyPayroll, 'type' => 'outflow', 'label' => 'Custos salariais previstos']);
            }
        }

        // Estimativa operacional: média dos últimos meses com atividade, sem assumir que cada despesa é recorrente.
        $history = $workspace->expenses()->where('is_company', true)->where('spent_at', '>=', now()->subMonths(3))->get();
        $monthlyTotals = $history->groupBy(fn ($expense) => Carbon::parse($expense->spent_at)->format('Y-m'))->map(fn ($items) => (float) $items->sum('amount'));
        $monthlyOpEx = $monthlyTotals->count() > 0 ? (float) $monthlyTotals->avg() : 0;

        for ($m = 0; $m <= (int) ceil($days / 30); $m++) {
            $expDate = now()->addMonths($m)->startOfMonth()->addDays(14);
            if ($expDate->between($start, $end) && $monthlyOpEx > 0) {
                $events->push(['date' => $expDate, 'amount' => -$monthlyOpEx, 'type' => 'outflow', 'label' => 'Despesas operacionais estimadas']);
            }
        }

        $events = $events->sortBy('date')->values();
        $running = $currentBalance;
        $timeline = [['date' => $start->format('Y-m-d'), 'balance' => round($running, 2), 'label' => 'Hoje', 'amount' => 0, 'type' => 'opening']];

        foreach ($events as $event) {
            $running += $event['amount'];
            $timeline[] = [
                'date' => $event['date']->format('Y-m-d'), 'balance' => round($running, 2),
                'label' => $event['label'], 'amount' => round($event['amount'], 2), 'type' => $event['type'],
            ];
        }

        $minBalance = (float) collect($timeline)->min('balance');
        $maxBalance = (float) collect($timeline)->max('balance');

        return [
            'current_balance' => round($currentBalance, 2),
            'forecast_end' => round($running, 2),
            'min_balance' => round($minBalance, 2),
            'max_balance' => round($maxBalance, 2),
            'timeline' => $timeline,
            'events' => $events->toArray(),
            'days' => $days,
            'estimated_monthly_opex' => round($monthlyOpEx, 2),
            'alert' => $minBalance < 0 ? 'Atenção: o saldo projetado fica negativo no período analisado.' : null,
            'disclaimer' => 'Previsão indicativa baseada em recebimentos previstos, custos salariais registados e média histórica de despesas. Não é uma garantia de fluxo de caixa futuro.',
        ];
    }
}
