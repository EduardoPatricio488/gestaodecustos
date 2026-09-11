<?php

namespace App\Livewire\Business;

use App\Models\Employee;
use App\Models\User;
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

    public function createTestEmployees(): void
    {
        abort_unless(Auth::user()->isOwner(), 403);
        $workspace = Auth::user()->currentWorkspace;
        abort_unless($workspace, 404);

        $employees = [
            ['name' => 'Sara Oliveira', 'role' => 'Gestora de Projetos', 'email' => 'sara@exemplo.com'],
            ['name' => 'Ricardo Silva', 'role' => 'Contabilista', 'email' => 'ricardo@exemplo.com'],
            ['name' => 'Maria Santos', 'role' => 'Administrativa', 'email' => 'maria@exemplo.com'],
        ];

        foreach ($employees as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], [
                'name' => $data['name'], 'password' => bcrypt(str()->random(32)),
                'email_verified_at' => now(), 'current_workspace_id' => $workspace->id,
            ]);
            $workspace->users()->syncWithoutDetaching([$user->id => ['role' => 'editor']]);
            Employee::updateOrCreate(
                ['user_id' => $user->id, 'workspace_id' => $workspace->id],
                ['name' => $data['name'], 'role' => $data['role'], 'salary' => 2000]
            );
        }

        $this->dispatch('toast', variant: 'success', heading: 'Dados de demonstração criados', text: 'Foram adicionados colaboradores de exemplo.');
    }

    public function switchBusinessWorkspace(int $workspaceId): void
    {
        $user = Auth::user();
        abort_unless($user->workspaces()->whereKey($workspaceId)->exists(), 403);
        $user->update(['current_workspace_id' => $workspaceId]);
        session()->forget('viewing_as_collaborator_id');
        $this->redirect(route('hub.business.dashboard'), navigate: true);
    }

    public function switchToEmployee(int $id): void
    {
        $user = Auth::user();
        abort_unless($user->isOwner() || $user->isAdminRole(), 403);
        abort_unless(Employee::where('workspace_id', $user->current_workspace_id)->whereKey($id)->exists(), 404);
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
        abort_unless(Auth::user()->isOwner() || Auth::user()->isAdminRole(), 403);
        session()->forget('viewing_as_collaborator_id');
        return redirect()->route('hub.business.dashboard');
    }

    public function render()
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;
        if (! $workspace) return <<<'HTML'
            <div class="p-20 text-center italic text-zinc-500 font-medium">Nenhum workspace empresarial detetado.</div>
        HTML;

        $metrics = app(BusinessFinancialMetrics::class)->forMonth($workspace);
        $activeProjects = $workspace->projects()->where('status', 'em_curso')->get();
        $businessWorkspaces = $user->workspaces()->where('type', '!=', 'personal')->get();
        $lowStockCount = $workspace->products()->whereRaw('stock <= min_stock')->count();
        $criticalDocsCount = $workspace->documents()->where(fn ($q) => $q->where('expires_at', '<', now())->orWhere('expires_at', '<=', now()->addDays(15)))->count();
        $overdueTasksCount = $workspace->tasks()->where('due_date', '<', now())->where('status', '!=', 'concluido')->count();

        return view('livewire.business.business-dashboard', [
            'workspace' => $workspace, 'businessWorkspaces' => $businessWorkspaces,
            'revenue' => $metrics['revenue_cash'], 'totalCosts' => $metrics['total_costs'],
            'payroll' => $metrics['payroll'], 'netProfit' => $metrics['net_result'],
            'totalBalance' => $metrics['cash'], 'runway' => $workspace->getRunway(),
            'margin' => $metrics['margin'], 'accountsReceivable' => $metrics['receivables'],
            'activeProjects' => $activeProjects, 'lowStockCount' => $lowStockCount,
            'criticalDocsCount' => $criticalDocsCount, 'overdueTasksCount' => $overdueTasksCount,
            'teamCount' => $workspace->employees()->where('active', true)->where('suspended', false)->whereNull('terminated_at')->count(),
            'vatProvision' => 0, 'ircProvision' => 0,
            'financialDisclaimer' => 'Os valores financeiros são calculados a partir dos dados registados. Os indicadores fiscais são estimativas e devem ser validados pelo contabilista.',
        ]);
    }
}
