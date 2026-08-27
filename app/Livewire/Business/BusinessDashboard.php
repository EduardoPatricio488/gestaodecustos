<?php

namespace App\Livewire\Business;

use App\Models\Employee;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BusinessDashboard extends Component
{
    public function mount()
    {
        if (Auth::check()) {
            NotificationService::checkAll(Auth::user());
        }
    }

    /**
     * Gera colaboradores de teste (Seed)
     * Útil para o comprador testar a funcionalidade de equipa imediatamente.
     */
    public function createTestEmployees()
    {
        $workspace = Auth::user()->currentWorkspace;

        $colaboradores = [
            ['name' => 'Sara Oliveira', 'role' => 'Gestora de Projetos', 'email' => 'sara@exemplo.com'],
            ['name' => 'Ricardo Silva', 'role' => 'Contabilista', 'email' => 'ricardo@exemplo.com'],
            ['name' => 'Maria Santos', 'role' => 'Administrativa', 'email' => 'maria@exemplo.com'],
        ];

        foreach ($colaboradores as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'current_workspace_id' => $workspace->id,
                ]
            );

            $workspace->users()->syncWithoutDetaching([$user->id => ['role' => 'editor']]);

            Employee::updateOrCreate(
                ['user_id' => $user->id, 'workspace_id' => $workspace->id],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'salary' => rand(1500, 3000),
                ]
            );
        }

        $this->dispatch('toast', variant: 'success', heading: 'Modo Demo Ativo', text: 'Colaboradores criados com sucesso!');
    }

    /**
     * Trocar entre as empresas do utilizador
     */
    public function switchBusinessWorkspace(int $workspaceId): void
    {
        $user = Auth::user();
        $workspace = $user->workspaces()->where('workspaces.id', $workspaceId)->first();

        if (! $workspace) {
            $this->dispatch('toast', variant: 'error', heading: 'Acesso Negado');

            return;
        }

        session()->forget('viewing_as_collaborator_id');
        $user->update(['current_workspace_id' => $workspaceId]);

        $this->redirect(route('hub.business.dashboard'), navigate: true);
    }

    /**
     * Shadow Mode: Visualizar como colaborador
     */
    public function switchToEmployee($id)
    {
        $employee = Employee::find($id);

        if (! $employee || ! $employee->user_id) {
            $this->dispatch('toast', variant: 'error', text: 'Utilizador não vinculado.');

            return;
        }

        session()->put('viewing_as_collaborator_id', $id);

        return redirect()->route('hub.business.dashboard');
    }

    /**
     * Sair do modo empresa para o cofre pessoal
     */
    public function exitBusinessMode()
    {
        $user = Auth::user();
        $personalWs = $user->workspaces()->where('type', 'personal')->first();

        if ($personalWs) {
            $user->update(['current_workspace_id' => $personalWs->id]);
            session()->forget('viewing_as_collaborator_id');

            return redirect()->route('dashboard');
        }
    }

    public function stopViewingAsCollaborator()
    {
        session()->forget('viewing_as_collaborator_id');

        return redirect()->route('hub.business.dashboard');
    }

    public function render()
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (! $workspace) {
            return <<<'HTML'
                <div class="p-20 text-center italic text-zinc-500 font-medium">Nenhum workspace empresarial detetado.</div>
            HTML;
        }

        $month = now()->month;
        $year = now()->year;

        // --- CÁLCULOS FINANCEIROS (MÉTRICAS DO MÊS ATUAL) ---
        $revenue = (float) $workspace->invoices()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'paga')
            ->sum('total_amount');

        $opEx = (float) $workspace->expenses()
            ->where('workspace_id', $workspace->id)
            ->where('is_company', true)
            ->whereYear('spent_at', $year)
            ->whereMonth('spent_at', $month)
            ->sum('amount');

        $payroll = (float) $workspace->employees()->sum('salary');
        $totalCosts = $opEx + $payroll;
        $netProfit = $revenue - $totalCosts;

        // SALDO TOTAL: Capital Inicial + Fluxo de Caixa acumulado
        $totalBalance = (float) ($workspace->initial_capital ?? 0) + $revenue - $totalCosts;

        // --- FISCALIDADE (ESTIMATIVAS PREMIUM) ---
        // Cálculo simplificado de IVA (Ex: 23%) - Mostra que o sistema pensa no fisco
        $vatProvision = max(0, ($revenue * 0.23) - ($opEx * 0.23));

        // Provisão de IRC (Ex: 21% sobre o lucro líquido)
        $ircProvision = $netProfit > 0 ? ($netProfit * 0.21) : 0;

        // --- OPERAÇÕES ---
        $activeProjects = $workspace->projects()->where('status', 'em_curso')->get();

        // Alerta de Stock Baixo
        $lowStockCount = $workspace->products()
            ->whereRaw('stock <= min_stock')
            ->count();

        // Alerta de Documentos Críticos (Expirados ou a expirar em 15 dias)
        $criticalDocsCount = $workspace->documents()
            ->where(fn ($q) => $q->where('expires_at', '<', now())->orWhere('expires_at', '<=', now()->addDays(15)))
            ->count();

        // Alerta de Tarefas em Atraso
        $overdueTasksCount = $workspace->tasks()
            ->where('due_date', '<', now())
            ->where('status', '!=', 'concluido')
            ->count();

        // Workspaces para o switcher da sidebar
        $businessWorkspaces = $user->workspaces()->where('type', '!=', 'personal')->get();

        return view('livewire.business.business-dashboard', [
            'workspace' => $workspace,
            'businessWorkspaces' => $businessWorkspaces,
            'revenue' => $revenue,
            'totalCosts' => $totalCosts,
            'payroll' => $payroll,
            'netProfit' => $netProfit,
            'totalBalance' => $totalBalance,
            'runway' => $workspace->getRunway(),
            'margin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
            'accountsReceivable' => (float) $workspace->invoices()->where('status', 'pendente')->sum('total_amount'),
            'activeProjects' => $activeProjects,
            'lowStockCount' => $lowStockCount,
            'criticalDocsCount' => $criticalDocsCount,
            'overdueTasksCount' => $overdueTasksCount,
            'teamCount' => $workspace->employees()->count(),
            'vatProvision' => $vatProvision,
            'ircProvision' => $ircProvision,
        ]);
    }
}
