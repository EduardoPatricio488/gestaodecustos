<?php

namespace App\Services;

use App\Models\Workspace;
use Illuminate\Support\Carbon;

class BusinessFinancialMetrics
{
    public function forMonth(Workspace $workspace, ?Carbon $month = null): array
    {
        $month = ($month ?: now())->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $invoices = $workspace->invoices();
        $expenses = $workspace->expenses()->where('is_company', true);

        $paidRevenue = (float) (clone $invoices)
            ->where('status', 'paga')
            ->whereBetween('paid_at', [$month, $end])
            ->sum('amount_excl_vat_converted');

        $issuedRevenue = (float) (clone $invoices)
            ->whereBetween('created_at', [$month, $end])
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->sum('amount_excl_vat_converted');

        $operatingExpenses = (float) (clone $expenses)
            ->whereBetween('spent_at', [$month->toDateString(), $end->toDateString()])
            ->sum('amount_converted');

        // A base de dados atual não guarda histórico de salários. Por isso, não podemos
        // aplicar o salário atual retroativamente aos meses fechados sem fabricar dados.
        $activePayroll = $month->isSameMonth(now())
            ? (float) $workspace->employees()
                ->where('active', true)
                ->where('suspended', false)
                ->whereNull('terminated_at')
                ->sum('salary')
            : 0.0;

        $receivables = (float) (clone $invoices)
            ->whereIn('status', ['pendente', 'vencida'])
            ->sum('total_amount_converted');

        $overdueReceivables = (float) (clone $invoices)
            ->where(function ($query) use ($end) {
                $query->where('status', 'vencida')
                    ->orWhere(fn ($q) => $q->where('status', 'pendente')->whereDate('due_date', '<', $end->toDateString()));
            })
            ->sum('total_amount_converted');

        $cash = (float) $workspace->bankAccounts()->sum('balance');
        if (! $workspace->bankAccounts()->exists()) {
            $cash = (float) ($workspace->initial_capital ?? 0)
                + (float) $workspace->invoices()->where('status', 'paga')->sum('total_amount_converted')
                - (float) $workspace->expenses()->where('is_company', true)->sum('amount_converted');
        }

        $totalCosts = $operatingExpenses + $activePayroll;
        $netOperatingResult = $paidRevenue - $totalCosts;

        return [
            'period' => $month->format('Y-m'),
            'revenue_cash' => round($paidRevenue, 2),
            'revenue_issued' => round($issuedRevenue, 2),
            'operating_expenses' => round($operatingExpenses, 2),
            'payroll' => round($activePayroll, 2),
            'total_costs' => round($totalCosts, 2),
            'net_result' => round($netOperatingResult, 2),
            'margin' => round($paidRevenue > 0 ? ($netOperatingResult / $paidRevenue) * 100 : 0, 2),
            'cash' => round($cash, 2),
            'receivables' => round($receivables, 2),
            'overdue_receivables' => round(max(0, $overdueReceivables), 2),
            'payroll_is_current_run_rate' => $month->isSameMonth(now()),
        ];
    }

    public function forYear(Workspace $workspace, int $year): array
    {
        return collect(range(1, 12))
            ->map(fn (int $month) => $this->forMonth($workspace, Carbon::create($year, $month, 1)))
            ->all();
    }
}
