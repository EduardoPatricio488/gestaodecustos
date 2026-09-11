<?php

namespace App\Livewire\Business;

use App\Models\Employee;
use App\Services\BusinessAccessService;
use App\Services\BusinessFinancialMetrics;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BusinessDashboard extends Component
{
    public function mount(): void
    {
        if (Auth::check()) NotificationService::checkAll(Auth::user());
    }

    public function switchBusinessWorkspace(int $workspaceId): void
    {
        $user = Auth::user();
        $workspace = $user->workspaces()->whereKey($workspaceId)->firstOrFail();
        abort_unless(in_array($workspace->type, ['business', 'company'], true), 403);

        $user->update(['current_workspace_id' => $workspaceId]);
        session()->forget('viewing_as_collaborator_id');
        $this->redirect(route('hub.business.dashboard'), navigate: true);
    }

    public function switchToEmployee(int $id): void
    {
        $user = Auth::user();
        $workspace = app(BusinessAccessService::class)->assertWorkspace($user);
        app(BusinessAccessService::class)->assert('manage_team', $user, $workspace);

        abort_unless(Employee::where('workspace_id', $workspace->id)->whereKey($id)->exists(), 404);
        session()->put('viewing_as_collaborator_id', $id);
        $this->redirect(route('hub.business.dashboard'), navigate: true);
    }

    public function exitBusinessMode()
    {
        $user = Auth::user();
        $personalWs = $user->workspaces()->where('type', 'personal')->first();
        if ($personalWs) {
            $user->update(['current_workspace_id' => $personalWs->id]);
            session()->forget('viewing_as_collaborator_id');
        }
        return redirect()->route('dashboard');
    }

    public function stopViewingAsCollaborator()
    {
        $user = Auth::user();
        $workspace = app(BusinessAccessService::class)->assertWorkspace($user);
        app(BusinessAccessService::class)->assert('manage_team', $user, $workspace);

        session()->forget('viewing_as_collaborator_id');
        return redirect()->route('hub.business.dashboard');
    }

    public function render()
    {
        $user = Auth::user();
        $workspace = app(BusinessAccessService::class)->assertWorkspace($user);
        $metrics = app(BusinessFinancialMetrics::class)->forMonth($workspace);
        $activeProjects = $workspace->projects()->where('status', 'em_curso')->get();
        $businessWorkspaces = $user->workspaces()->whereIn('type', ['business', 'company'])->get();
        $lowStockCount = $workspace->products()->whereColumn('stock_quantity', '<=', 'min_stock_alert')->count();
        $criticalDocsCount = $workspace->documents()
            ->where(fn ($q) => $q->where('expires_at', '<', now())->orWhere('expires_at', '<=', now()->addDays(15)))
            ->count();
        $overdueTasksCount = $workspace->tasks()->where('due_date', '<', now())->where('status', '!=', 'concluido')->count();

        $vatCollected = (float) $workspace->invoices()
            ->where('status', 'paga')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('vat_amount');
        $vatDeductible = (float) $workspace->expenses()
            ->where('is_company', true)
            ->whereBetween('spent_at', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('vat_amount');
        $vatBalance = round($vatCollected - $vatDeductible, 2);

        return view('livewire.business.business-dashboard', [
            'workspace' => $workspace,
            'businessWorkspaces' => $businessWorkspaces,
            'businessRole' => app(BusinessAccessService::class)->role($user, $workspace),
            'revenue' => $metrics['revenue_cash'],
            'totalCosts' => $metrics['total_costs'],
            'payroll' => $metrics['payroll'],
            'netProfit' => $metrics['net_result'],
            'totalBalance' => $metrics['cash'],
            'runway' => $workspace->getRunway(),
            'margin' => $metrics['margin'],
            'accountsReceivable' => $metrics['receivables'],
            'overdueReceivables' => $metrics['overdue_receivables'],
            'activeProjects' => $activeProjects,
            'lowStockCount' => $lowStockCount,
            'criticalDocsCount' => $criticalDocsCount,
            'overdueTasksCount' => $overdueTasksCount,
            'teamCount' => $workspace->employees()->where('active', true)->where('suspended', false)->whereNull('terminated_at')->count(),
            'vatProvision' => max(0, $vatBalance),
            'ircProvision' => 0,
            'financialDisclaimer' => 'Os valores financeiros são calculados a partir dos dados registados. O resultado é uma métrica operacional em base de caixa; o IVA é informativo e as obrigações fiscais oficiais devem ser validadas pelo contabilista.',
        ]);
    }
}
