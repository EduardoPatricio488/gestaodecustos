<?php

namespace App\Livewire;

use Illuminate\Support\Facades\{DB, Http, Cache, Auth};
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\{Category, Expense, Goal, Income, Subscription, EmailLog, Workspace, Investment};
use Illuminate\Support\Str;
use App\Services\NotificationService;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public $inviteCodeInput = '';

    // Propriedades para Exportação PDF
    public $exportStart;
    public $exportEnd;
    public $exportIncomes = true;
    public $exportExpenses = true;

    // Preços de Mercado (Bitcoin, Ethereum, etc)
    public $marketPrices = [];

    public function mount()
    {
        $user = Auth::user();

        // 1. Verificação de Notificações Automáticas
        NotificationService::checkAll($user);

        // 2. Redirecionamento de Segurança para Admin Real
        if ($user->email === 'admin@admin.pt' && !session()->has('impersonator_id')) {
            return redirect()->route('admin.dashboard');
        }

        // 3. Inicialização de Datas de Filtro
        $this->exportStart = now()->startOfMonth()->format('Y-m-d');
        $this->exportEnd = now()->endOfMonth()->format('Y-m-d');

        // 4. PREÇOS DE MERCADO COM CACHE (Otimização de Performance)
        // Evita chamadas repetitivas à API que tornam o site lento
        $this->marketPrices = Cache::remember('market_prices_crypto', 3600, function () {
            try {
                $response = Http::timeout(3)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids' => 'bitcoin,ethereum,solana',
                    'vs_currencies' => 'eur',
                    'include_24hr_change' => 'true'
                ]);
                return $response->successful() ? $response->json() : [];
            } catch (\Exception $e) {
                return [];
            }
        });

        // 5. AUTO-CONFIGURAÇÃO DE WORKSPACE (Para novos utilizadores)
        if (!$user->workspaces()->exists()) {
            $ws = Workspace::create([
                'name' => 'Gestão de ' . explode(' ', $user->name)[0],
                'type' => 'personal',
                'owner_id' => $user->id,
                'invite_code' => strtoupper(Str::random(8))
            ]);

            $user->workspaces()->attach($ws->id, ['role' => 'admin']);
            $user->update(['current_workspace_id' => $ws->id]);
            $user->refresh();
        }

        // Garante que existe sempre um Workspace selecionado
        if (!$user->current_workspace_id) {
            $user->update(['current_workspace_id' => $user->workspaces()->first()->id]);
        }
    }

    public function downloadCustomPdf()
    {
        $params = [
            'start'    => $this->exportStart,
            'end'      => $this->exportEnd,
            'expenses' => $this->exportExpenses ? '1' : '0',
            'incomes'  => $this->exportIncomes ? '1' : '0',
        ];

        return redirect()->to(route('export.dashboard.pdf') . '?' . http_build_query($params));
    }

    public function generateInviteCode()
    {
        $workspace = Auth::user()->currentWorkspace;
        if ($workspace) {
            $workspace->update(['invite_code' => strtoupper(Str::random(8))]);
            $this->dispatch('toast', text: 'Novo código de convite gerado!');
        }
    }

    public function joinWorkspace()
    {
        $this->validate(['inviteCodeInput' => 'required|string|exists:workspaces,invite_code']);
        $workspace = Workspace::where('invite_code', $this->inviteCodeInput)->first();

        if ($workspace->users()->where('user_id', Auth::id())->exists()) {
            $this->dispatch('toast', variant: 'error', text: 'Já fazes parte desta conta.');
            return;
        }

        Auth::user()->workspaces()->attach($workspace->id, ['role' => 'member']);
        Auth::user()->update(['current_workspace_id' => $workspace->id]);
        return redirect()->route('dashboard');
    }

    public function switchWorkspace($id)
    {
        if (Auth::user()->workspaces()->where('workspaces.id', $id)->exists()) {
            Auth::user()->update(['current_workspace_id' => $id]);
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $currentWs = $user->currentWorkspace;

        if (!$currentWs) return view('livewire.dashboard-loading');

        // --- CÁLCULOS FINANCEIROS DO MÊS ---
        $monthStart = now()->startOfMonth();
        $dayOfMonth = now()->day;
        $daysInMonth = now()->daysInMonth;

        $fixedIncome = (float) $user->recurringIncomes()
            ->where('workspace_id', $currentWs->id)
            ->where('is_active', true)
            ->sum('amount') ?? 0;

        $totalMonthExpenses = (float) Expense::where('workspace_id', $currentWs->id)
            ->where('spent_at', '>=', $monthStart)
            ->sum('amount') ?? 0;

        $totalMonthIncome = (float) Income::where('workspace_id', $currentWs->id)
            ->where('received_at', '>=', $monthStart)
            ->sum('amount') + $fixedIncome;

        // --- VALORIZAÇÃO DE ATIVOS (PORTFOLIO) ---
        $portfolioValue = 0;
        $myInvestments = Investment::where('workspace_id', $currentWs->id)->get();

        foreach($myInvestments as $inv) {
            $price = match(strtolower($inv->symbol)) {
                'btc' => $this->marketPrices['bitcoin']['eur'] ?? $inv->current_price,
                'eth' => $this->marketPrices['ethereum']['eur'] ?? $inv->current_price,
                'sol' => $this->marketPrices['solana']['eur'] ?? $inv->current_price,
                'sp500', 'spx' => 5222.68,
                'nvda' => 945.30,
                default => $inv->current_price
            };
            $portfolioValue += ($inv->quantity * $price);
        }

        // --- PREVISÃO INTELIGENTE ---
        $dailyBurnRate = $dayOfMonth > 1 ? $totalMonthExpenses / $dayOfMonth : $totalMonthExpenses;
        $projectedExpenses = $dailyBurnRate * $daysInMonth;
        $projectedBalance = $totalMonthIncome - $projectedExpenses;
        $projectionStatus = $projectedBalance < 0 ? 'critical' : ($projectedBalance < ($totalMonthIncome * 0.15) ? 'warning' : 'stable');

        // --- GRÁFICO (ÚLTIMOS 6 MESES) ---
        $last6 = collect(range(5, 0))->map(fn($i) => [
            'label' => now()->subMonths($i)->translatedFormat('M'),
            'spent' => (float) Expense::where('workspace_id', $currentWs->id)
                ->whereBetween('spent_at', [now()->subMonths($i)->startOfMonth(), now()->subMonths($i)->endOfMonth()])->sum('amount'),
            'earned' => (float) Income::where('workspace_id', $currentWs->id)
                ->whereBetween('received_at', [now()->subMonths($i)->startOfMonth(), now()->subMonths($i)->endOfMonth()])->sum('amount') + $fixedIncome,
        ]);

        // --- ORÇAMENTOS POR CATEGORIA ---
        $byCategory = Category::where('workspace_id', $currentWs->id)
            ->where('budget_limit', '>', 0)
            ->get()->map(function($cat) use ($monthStart, $currentWs) {
                $spent = (float) Expense::where('category_id', $cat->id)
                    ->where('workspace_id', $currentWs->id)
                    ->where('spent_at', '>=', $monthStart)->sum('amount');

                return (object)[
                    'name' => $cat->name,
                    'total' => $spent,
                    'budget' => (float) $cat->budget_limit,
                    'percentage' => $cat->budget_limit > 0 ? min(($spent / $cat->budget_limit) * 100, 100) : 0,
                    'over' => $spent > $cat->budget_limit
                ];
            })->sortByDesc('total');

        return view('livewire.dashboard', [
            'currentWs' => $currentWs,
            'userWorkspaces' => $user->workspaces,
            'overallScore' => $currentWs->calculateScore(),

            // Financeiro
            'totalMonth' => $totalMonthExpenses,
            'totalIncomeMonth' => $totalMonthIncome,
            'netBalance' => $totalMonthIncome - $totalMonthExpenses,
            'portfolioValue' => $portfolioValue,
            'totalSaved' => (float) Goal::where('workspace_id', $currentWs->id)->sum('current_amount'),

            // Previsão
            'projectedExpenses' => $projectedExpenses,
            'projectedBalance' => $projectedBalance,
            'projectionStatus' => $projectionStatus,

            // Visualização
            'chartMax' => max($last6->max('spent'), $last6->max('earned'), 1),
            'last6' => $last6,
            'byCategory' => $byCategory,
            'recent' => Expense::with(['category', 'user'])->where('workspace_id', $currentWs->id)->latest('spent_at')->take(5)->get(),
        ]);
    }
}
