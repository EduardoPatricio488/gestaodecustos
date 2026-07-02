<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Workspace;
use Carbon\Carbon;

class CashFlowForecastService
{
    public function getForecast(Workspace $workspace, int $days = 90): array
    {
        $start = now()->startOfDay();
        $end = now()->addDays($days)->endOfDay();

        $currentBalance = (float) $workspace->bankAccounts()->sum('current_balance');
        if ($currentBalance === 0.0) {
            $currentBalance = $workspace->getLiquidezAtual();
        }

        $events = collect();

        $pendingInvoices = Invoice::where('workspace_id', $workspace->id)
            ->where('status', 'pendente')
            ->whereBetween('due_date', [$start, $end])
            ->get();

        foreach ($pendingInvoices as $inv) {
            $events->push([
                'date' => Carbon::parse($inv->due_date),
                'amount' => (float) $inv->total_amount,
                'type' => 'inflow',
                'label' => "Fatura #{$inv->invoice_number}",
            ]);
        }

        $monthlyPayroll = (float) Employee::where('workspace_id', $workspace->id)->sum('salary');
        for ($m = 0; $m <= intval($days / 30); $m++) {
            $payDate = now()->addMonths($m)->endOfMonth();
            if ($payDate->between($start, $end) && $monthlyPayroll > 0) {
                $events->push([
                    'date' => $payDate,
                    'amount' => -$monthlyPayroll,
                    'type' => 'outflow',
                    'label' => 'Folha Salarial',
                ]);
            }
        }

        $recurringExpenses = Expense::where('workspace_id', $workspace->id)
            ->where('is_company', true)
            ->where('spent_at', '>=', now()->subMonths(3))
            ->get()
            ->groupBy('subcategory');

        $avgMonthly = $recurringExpenses->map(fn ($g) => $g->sum('amount') / 3);
        $monthlyOpEx = (float) $avgMonthly->sum();

        for ($m = 0; $m <= intval($days / 30); $m++) {
            $expDate = now()->addMonths($m)->day(15);
            if ($expDate->between($start, $end) && $monthlyOpEx > 0) {
                $events->push([
                    'date' => $expDate,
                    'amount' => -$monthlyOpEx,
                    'type' => 'outflow',
                    'label' => 'Despesas Operacionais (est.)',
                ]);
            }
        }

        $events = $events->sortBy('date')->values();

        $running = $currentBalance;
        $timeline = [];
        $timeline[] = ['date' => $start->format('Y-m-d'), 'balance' => $running, 'label' => 'Hoje'];

        foreach ($events as $event) {
            $running += $event['amount'];
            $timeline[] = [
                'date' => $event['date']->format('Y-m-d'),
                'balance' => round($running, 2),
                'label' => $event['label'],
                'amount' => $event['amount'],
                'type' => $event['type'],
            ];
        }

        $minBalance = collect($timeline)->min('balance');
        $maxBalance = collect($timeline)->max('balance');

        return [
            'current_balance' => $currentBalance,
            'forecast_end' => round($running, 2),
            'min_balance' => $minBalance,
            'max_balance' => $maxBalance,
            'timeline' => $timeline,
            'events' => $events->toArray(),
            'days' => $days,
            'alert' => $minBalance < 0 ? 'Atenção: fluxo de caixa negativo previsto!' : null,
        ];
    }
}
