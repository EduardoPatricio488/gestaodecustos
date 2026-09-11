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
            ->sum('amount_excl_vat');

        $issuedRevenue = (float) (clone $invoices)
            ->whereBetween('created_at', [$month, $end])
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->sum('amount_excl_vat');

        $operatingExpenses = (float) (clone $expenses)
            ->whereBetween('spent_at', [$month->toDateString(), $end->toDateString()])
            ->sum('amount');

        $activePayroll = (float) $workspace->employees()
            ->where('active', true)
            ->where('suspended', false)
            ->whereNull('terminated_at')
            ->sum('salary');

        $receivables = (float) (clone $invoices)
            ->whereIn('status', ['pendente', 'vencida'])
            ->sum('total_amount');

        $overdueReceivables = (float) (clone $invoices)
            ->where('status', 'vencida')
            ->orWhere(function ($query) use ($end) {
                $query->where('status', 'pendente')->whereDate('due_date', '<', $end->toDateString());
            })
            ->sum('total_amount');

        $cash = (float) $workspace->bankAccounts()->sum('balance');
        if (! $workspace->bankAccounts()->exists()) {
            $cash = (float) ($workspace->initial_capital ?? 0)
                + (float) $workspace->invoices()->where('status', 'paga')->sum('total_amount')
                - (float) $workspace->expenses()->where('is_company', true)->sum('amount');
        }

        $netOperatingResult = $paidRevenue - $operatingExpenses - $activePayroll;
        $margin = $paidRevenue > 0 ? ($netOperatingResult / $paidRevenue) * 100 : 0;

        return [
            'period' => $month->format('Y-m'),
            'revenue_cash' => round($paidRevenue, 2),
            'revenue_issued' => round($issuedRevenue, 2),
            'operating_expenses' => round($operatingExpenses, 2),
            'payroll' => round($activePayroll, 2),
            'total_costs' => round($operatingExpenses + $activePayroll, 2),
            'net_result' => round($netOperatingResult, 2),
            'margin' => round($margin, 2),
            'cash' => round($cash, 2),
            'receivables' => round($receivables, 2),
            'overdue_receivables' => round(max(0, $overdueReceivables), 2),
        ];
    }

    public function forYear(Workspace $workspace, int $year): array
    {
        return collect(range(1, 12))->map(fn (int $month) =>
            $this->forMonth($workspace, Carbon::create($year, $month, 1))
        )->all();
    }
}
