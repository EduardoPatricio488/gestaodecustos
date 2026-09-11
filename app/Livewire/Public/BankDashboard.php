<?php

namespace App\Livewire\Public;

use App\Models\Workspace;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BankDashboard extends Component
{
    public $workspace;

    public string $period = '12';

    #[Layout('layouts.guest')]
    public function mount()
    {
        $this->workspace = $this->authenticatedWorkspace();
    }

    private function authenticatedWorkspace(): Workspace
    {
        $workspaceId = session('bank_portal_workspace_id');
        abort_unless($workspaceId, 403);

        $workspace = Workspace::whereKey($workspaceId)
            ->where('audit_token_purpose', 'bank_audit')
            ->whereNull('audit_token_revoked_at')
            ->where(function ($query) {
                $query->whereNull('audit_token_expires_at')
                    ->orWhere('audit_token_expires_at', '>', now());
            })
            ->first();

        abort_unless($workspace, 403);

        return $workspace;
    }

    public function updatedPeriod(): void
    {
        if (! in_array($this->period, ['3', '6', '12'], true)) {
            $this->period = '12';
        }
    }

    public function render()
    {
        $this->workspace = $this->authenticatedWorkspace();
        $workspace = $this->workspace;

        $accounts = $workspace->bankAccounts()->orderBy('bank_name')->orderBy('name')->get();
        $liquidez = (float) $accounts->where('type', '!=', 'credito')->sum('current_balance');
        $passivo = (float) $accounts->where('type', 'credito')->sum(fn ($account) => abs((float) $account->current_balance));

        $periodMonths = (int) $this->period;
        $periodStart = now()->subMonths($periodMonths - 1)->startOfMonth();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $revenue = (float) $workspace->invoices()
            ->where('status', 'paga')
            ->whereBetween('created_at', [$periodStart, now()])
            ->sum('total_amount');

        $expenses = (float) $workspace->expenses()
            ->where('is_company', true)
            ->where('spent_at', '>=', $periodStart->toDateString())
            ->sum('amount');

        $receivables = (float) $workspace->invoices()
            ->where('status', 'pendente')
            ->sum('total_amount');

        $overdueReceivables = (float) $workspace->invoices()
            ->where('status', 'pendente')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->sum('total_amount');

        $payroll = (float) $workspace->employees()->sum('salary');
        $employeeCount = $workspace->employees()->count();
        $clientCount = $workspace->clients()->count();
        $supplierCount = $workspace->suppliers()->count();
        $projectCount = $workspace->projects()->count();

        $monthRevenue = (float) $workspace->invoices()
            ->where('status', 'paga')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_amount');

        $monthExpenses = (float) $workspace->expenses()
            ->where('is_company', true)
            ->whereBetween('spent_at', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('amount');

        $netPosition = $monthRevenue - $monthExpenses;
        $currentRatio = $passivo > 0 ? $liquidez / $passivo : ($liquidez > 0 ? 10 : 0);
        $debtRatio = ($liquidez + $passivo) > 0 ? ($passivo / ($liquidez + $passivo)) * 100 : 0;
        $collectionRisk = $receivables > 0 ? ($overdueReceivables / $receivables) * 100 : 0;

        if ($currentRatio >= 3 && $collectionRisk <= 15) {
            $rating = 'A+';
        } elseif ($currentRatio >= 2 && $collectionRisk <= 25) {
            $rating = 'A';
        } elseif ($currentRatio >= 1.25 && $collectionRisk <= 40) {
            $rating = 'B';
        } elseif ($currentRatio >= 0.8) {
            $rating = 'C';
        } else {
            $rating = 'D';
        }

        $monthlyTrend = collect(range($periodMonths - 1, 0))->map(function (int $monthsAgo) use ($workspace) {
            $date = now()->subMonths($monthsAgo);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $revenue = (float) $workspace->invoices()
                ->where('status', 'paga')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount');

            $expenses = (float) $workspace->expenses()
                ->where('is_company', true)
                ->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');

            return [
                'label' => $date->format('M'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'net' => $revenue - $expenses,
            ];
        });

        $trendMax = max(1, $monthlyTrend->max(fn ($month) => max($month['revenue'], $month['expenses'])));

        $recentInvoices = $workspace->invoices()
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $recentExpenses = $workspace->expenses()
            ->where('is_company', true)
            ->orderByDesc('spent_at')
            ->limit(8)
            ->get();

        $pendingBankRequests = $workspace->bankAccessRequests()
            ->where('status', 'pending')
            ->count();

        $lastBankAccessRequest = $workspace->bankAccessRequests()
            ->latest('requested_at')
            ->first();

        return view('livewire.public.bank-dashboard', [
            'accounts' => $accounts,
            'liquidez' => $liquidez,
            'passivo' => $passivo,
            'rating' => $rating,
            'currentRatio' => $currentRatio,
            'debtRatio' => $debtRatio,
            'collectionRisk' => $collectionRisk,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'receivables' => $receivables,
            'overdueReceivables' => $overdueReceivables,
            'payroll' => $payroll,
            'employeeCount' => $employeeCount,
            'clientCount' => $clientCount,
            'supplierCount' => $supplierCount,
            'projectCount' => $projectCount,
            'monthRevenue' => $monthRevenue,
            'monthExpenses' => $monthExpenses,
            'netPosition' => $netPosition,
            'monthlyTrend' => $monthlyTrend,
            'trendMax' => $trendMax,
            'recentInvoices' => $recentInvoices,
            'recentExpenses' => $recentExpenses,
            'pendingBankRequests' => $pendingBankRequests,
            'lastBankAccessRequest' => $lastBankAccessRequest,
        ]);
    }
}
