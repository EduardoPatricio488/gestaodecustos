<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Expense, Invoice, Employee, Project, Product, BusinessDocument, Task, User};
use Livewire\Attributes\Layout;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class BusinessDashboard extends Component
{
    public function mount()
    {
        NotificationService::checkAll(auth()->user());
    }

    /**
     * Gera colaboradores de teste (Seed)
     */
    public function createTestEmployees()
    {
        $workspace = auth()->user()->currentWorkspace;

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
                    'current_workspace_id' => $workspace->id
                ]
            );

            $workspace->users()->syncWithoutDetaching([$user->id => ['role' => 'editor']]);

            Employee::updateOrCreate(
                ['user_id' => $user->id, 'workspace_id' => $workspace->id],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'salary' => rand(1000, 2500)
                ]
            );
        }

        $this->dispatch('toast', variant: 'success', heading: 'Colaboradores Criados!');
    }

    /**
     * Trocar entre as tuas empresas (CEO) ou onde és colaborador
     */
    public function switchBusinessWorkspace(int $workspaceId): void
    {
        $user = Auth::user();
        $workspace = $user->workspaces()->where('workspaces.id', $workspaceId)->first();

        if (!$workspace) {
            $this->dispatch('toast', variant: 'error', heading: 'Acesso Negado');
            return;
        }

        // Limpa qualquer visualização de colaborador ativa ao mudar de empresa
        session()->forget('viewing_as_collaborator_id');

        $user->update(['current_workspace_id' => $workspaceId]);
        $this->redirect(route('hub.business.dashboard'), navigate: true);
    }

    /**
     * MUDANÇA AQUI: Entrar em "Modo Visualização"
     * Não faz login real, apenas guarda na sessão para a Rota e o Dashboard saberem
     */
    public function switchToEmployee($id)
    {
        $employee = Employee::find($id);

        if (!$employee || !$employee->user_id) {
            $this->dispatch('toast', variant: 'error', heading: 'Erro', message: 'Utilizador não vinculado.');
            return;
        }

        // EM VEZ DE Auth::login(), guardamos apenas o ID do colaborador na sessão
        session()->put('viewing_as_collaborator_id', $id);

        // Redireciona para o dashboard (a rota agora vai carregar o terminal do colaborador)
        return redirect()->route('hub.business.dashboard');
    }
public function exitBusinessMode()
{
    $user = auth()->user();

    // 1. Procurar o teu workspace pessoal (Individual)
    $personalWs = $user->workspaces()->where('type', 'personal')->first();

    if ($personalWs) {
        // 2. Atualizar o ID para o pessoal
        $user->update(['current_workspace_id' => $personalWs->id]);

        // 3. Limpar qualquer visualização de colaborador ativa
        session()->forget('viewing_as_collaborator_id');

        return redirect()->route('dashboard');
    }
}

/**
 * Se estiveres a ver um colaborador, este botão volta para a tua vista de CEO
 */
public function stopViewingAsCollaborator()
{
    session()->forget('viewing_as_collaborator_id');
    return redirect()->route('hub.business.dashboard');
}
    public function render()
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return <<<'HTML'
                <div class="p-10 text-center italic text-zinc-500">Nenhum workspace selecionado.</div>
            HTML;
        }

        $month = now()->month;
        $year  = now()->year;

        // --- CÁLCULOS FINANCEIROS (VISTA CEO) ---
        $revenue = (float) $workspace->invoices()->whereYear('created_at', $year)->whereMonth('created_at', $month)->where('status', 'paga')->sum('total_amount');
        $opEx = (float) $workspace->expenses()->where('is_company', true)->whereYear('spent_at', $year)->whereMonth('spent_at', $month)->sum('amount');
        $payroll = (float) $workspace->employees()->sum('salary');
        $totalCosts = $opEx + $payroll;
        $netProfit  = $revenue - $totalCosts;

        // --- OPERAÇÕES ---
        $activeProjects = $workspace->projects()->where('status', 'em_curso')->get();
        $lowStockCount = $workspace->products()->whereColumn('stock', '<=', 'min_stock')->count();
        $criticalDocsCount = $workspace->documents()->where(fn($q) => $q->where('expires_at', '<', now())->orWhere('expires_at', '<=', now()->addDays(15)))->count();
        $overdueTasksCount = $workspace->tasks()->where('due_date', '<', now())->where('status', '!=', 'concluido')->count();

        // Workspaces para o seletor da sidebar
        $businessWorkspaces = $user->workspaces()
            ->where('type', '!=', 'personal')
            ->get()
            ->map(function ($ws) use ($user) {
                $ws->user_role = $ws->users()->where('users.id', $user->id)->first()?->pivot->role ?? 'viewer';
                return $ws;
            });

        return view('livewire.business.business-dashboard', [
            'workspace'           => $workspace,
            'businessWorkspaces'  => $businessWorkspaces,
            'revenue'             => $revenue,
            'totalCosts'          => $totalCosts,
            'payroll'             => $payroll,
            'netProfit'           => $netProfit,
            'vatProvision'        => max(0, $workspace->invoices()->whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('vat_amount') - $workspace->expenses()->where('is_company', true)->whereYear('spent_at', $year)->whereMonth('spent_at', $month)->sum('vat_amount')),
            'ircProvision'        => $netProfit > 0 ? ($netProfit * 0.21) : 0,
            'runway'              => $workspace->getRunway(),
            'margin'              => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
            'accountsReceivable'  => (float) $workspace->invoices()->where('status', 'pendente')->sum('total_amount'),
            'activeProjects'      => $activeProjects,
            'lowStockCount'       => $lowStockCount,
            'criticalDocsCount'   => $criticalDocsCount,
            'overdueTasksCount'   => $overdueTasksCount,
            'teamCount'           => $workspace->employees()->count(),
        ]);
    }
}
