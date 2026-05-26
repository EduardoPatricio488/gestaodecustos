<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Expense, Invoice, Employee, Project, Product, BusinessDocument, Task};
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
#[Layout('components.layouts.app')]
class BusinessDashboard extends Component
{
    public function mount()
{
    NotificationService::checkAll(auth()->user());
}
    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $month = now()->month;
        $year = now()->year;

        if (!$workspace) {
            return <<<'HTML'
                <div class="p-10 text-center italic text-zinc-500 underline decoration-brand-500">
                    Nenhum workspace empresarial selecionado. Por favor, crie um no perfil.
                </div>
            HTML;
        }

        // --- 1. MÉTROCAS FINANCEIRAS (MÊS ATUAL) ---
        // Faturação Real (O que já entrou)
        $revenue = (float) $workspace->invoices()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'paga')
            ->sum('total_amount');

        // Custos Operacionais (is_company = true)
        $opEx = (float) $workspace->expenses()
            ->where('is_company', true)
            ->whereMonth('spent_at', $month)
            ->sum('amount');

        // Custo Salarial (Payroll)
        $payroll = (float) $workspace->employees()->sum('salary');

        $totalCosts = $opEx + $payroll;
        $netProfit = $revenue - $totalCosts;

        // --- 2. CASHFLOW E COBRANÇAS ---
        // Dinheiro a entrar (Faturas pendentes)
        $accountsReceivable = (float) $workspace->invoices()
            ->where('status', 'pendente')
            ->sum('total_amount');

        // --- 3. PROVISÃO DE IMPOSTOS (IA LÓGICA) ---
        $vatCollected = (float) $workspace->invoices()->whereMonth('created_at', $month)->sum('vat_amount');
        $vatDeductible = (float) $workspace->expenses()->where('is_company', true)->whereMonth('spent_at', $month)->sum('vat_amount');
        $vatProvision = max(0, $vatCollected - $vatDeductible);

        // IRC Estimado (21% sobre o lucro antes de impostos)
        $ircProvision = $netProfit > 0 ? ($netProfit * 0.21) : 0;

        // --- 4. OPERAÇÕES E RISCO ---
        $activeProjects = $workspace->projects()->where('status', 'em_curso')->get();
        $lowStockCount = $workspace->products()->get()->filter(fn($p) => $p->isLowStock())->count();
        $criticalDocsCount = $workspace->documents()->get()->filter(fn($d) => $d->isExpired() || $d->isExpiringSoon())->count();
        $overdueTasksCount = $workspace->tasks()->get()->filter(fn($t) => $t->isOverdue())->count();

        return view('livewire.business.business-dashboard', [
            'workspace' => $workspace,
            'revenue' => $revenue,
            'totalCosts' => $totalCosts,
            'payroll' => $payroll,
            'netProfit' => $netProfit,
            'vatProvision' => $vatProvision,
            'ircProvision' => $ircProvision,
            'runway' => $workspace->getRunway(),
            'margin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
            'accountsReceivable' => $accountsReceivable,
            'activeProjects' => $activeProjects,
            'lowStockCount' => $lowStockCount,
            'criticalDocsCount' => $criticalDocsCount,
            'overdueTasksCount' => $overdueTasksCount,
            'teamCount' => $workspace->employees()->count(),
            'recentInvoices' => $workspace->invoices()->latest()->take(3)->get(),
        ]);
    }
}
