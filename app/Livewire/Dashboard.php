<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

    // Propriedade para Preços de Mercado
    public $marketPrices = [];

    public function mount()
    {
        $user = auth()->user();

        // Verifica notificações automáticas
        NotificationService::checkAll($user);

        // 1. Redireciona o Admin real se não estiver em modo suporte
        if ($user->email === 'admin@admin.pt' && !session()->has('impersonator_id')) {
            return redirect()->route('admin.dashboard');
        }

        // 2. Inicializa as datas de exportação
        $this->exportStart = now()->startOfMonth()->format('Y-m-d');
        $this->exportEnd = now()->endOfMonth()->format('Y-m-d');

        // 3. Procurar Preços Reais de Mercado (Cripto via CoinGecko)
        try {
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'bitcoin,ethereum,solana',
                'vs_currencies' => 'eur',
                'include_24hr_change' => 'true'
            ]);
            $this->marketPrices = $response->json();
        } catch (\Exception $e) {
            $this->marketPrices = [];
        }

        // 4. Auto-criação de Workspace para novos utilizadores
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

        if (!$user->current_workspace_id && $user->workspaces()->exists()) {
            $user->update(['current_workspace_id' => $user->workspaces()->first()->id]);
            $user->refresh();
        }
    }

    // Lógica de Exportação PDF
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

    // Gerar código de partilha
    public function generateInviteCode()
    {
        $workspace = auth()->user()->currentWorkspace;
        if ($workspace) {
            $code = strtoupper(Str::random(8));
            $workspace->update(['invite_code' => $code]);
            $this->dispatch('toast', text: 'Novo código gerado!');
        }
    }

    // Entrar em conta de outro via código
    public function joinWorkspace()
    {
        $this->validate(['inviteCodeInput' => 'required|string|exists:workspaces,invite_code']);
        $workspace = Workspace::where('invite_code', $this->inviteCodeInput)->first();

        if ($workspace->users()->where('user_id', auth()->id())->exists()) {
            $this->dispatch('toast', variant: 'error', text: 'Já fazes parte desta conta.');
            return;
        }

        auth()->user()->workspaces()->attach($workspace->id, ['role' => 'member']);
        auth()->user()->update(['current_workspace_id' => $workspace->id]);
        return redirect()->route('dashboard');
    }

    // Alternar entre espaços
    public function switchWorkspace($id)
    {
        if (auth()->user()->workspaces()->where('workspaces.id', $id)->exists()) {
            auth()->user()->update(['current_workspace_id' => $id]);
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $currentWs = $user->currentWorkspace;

        if (!$currentWs) return <<<'HTML'
            <div class="p-10 text-center text-zinc-500 italic">A carregar o seu espaço de trabalho...</div>
        HTML;

        // --- DADOS DO MÊS ATUAL ---
        $monthStart = now()->startOfMonth();
        $dayOfMonth = now()->day;
        $daysInMonth = now()->daysInMonth;

        $fixedIncome = (float) $user->recurringIncomes()->where('is_active', true)->sum('amount');
        $totalMonthExpenses = (float) Expense::where('spent_at', '>=', $monthStart)->sum('amount');
        $totalMonthIncome = (float) Income::where('received_at', '>=', $monthStart)->sum('amount') + $fixedIncome;

        // --- CÁLCULO DE PORTEFÓLIO DE INVESTIMENTOS ---
        $myInvestments = Investment::all(); // Trait filtra por workspace
        $portfolioValue = 0;
        foreach($myInvestments as $inv) {
            $currentMarketPrice = match(strtolower($inv->symbol)) {
                'btc' => $this->marketPrices['bitcoin']['eur'] ?? $inv->current_price,
                'eth' => $this->marketPrices['ethereum']['eur'] ?? $inv->current_price,
                'sol' => $this->marketPrices['solana']['eur'] ?? $inv->current_price,
                'sp500', 'spx' => 5222.68, // Preços simulados para índices
                'nvda' => 945.30,
                default => $inv->current_price
            };
            $portfolioValue += ($inv->quantity * $currentMarketPrice);
        }

        // --- LÓGICA DE PREVISÃO FINANCEIRA ---
        $dailyBurnRate = $dayOfMonth > 0 ? $totalMonthExpenses / $dayOfMonth : 0;
        $projectedExpenses = $dailyBurnRate * $daysInMonth;
        $projectedBalance = $totalMonthIncome - $projectedExpenses;
        $projectionStatus = $projectedBalance < 0 ? 'critical' : ($projectedBalance < ($totalMonthIncome * 0.15) ? 'warning' : 'stable');

        // --- GRÁFICO DE HISTÓRICO (ÚLTIMOS 6 MESES) ---
        $last6 = collect(range(5, 0))->map(fn($i) => [
            'label' => now()->subMonths($i)->translatedFormat('M'),
            'spent' => (float) Expense::whereBetween('spent_at', [now()->subMonths($i)->startOfMonth(), now()->subMonths($i)->endOfMonth()])->sum('amount'),
            'earned' => (float) Income::whereBetween('received_at', [now()->subMonths($i)->startOfMonth(), now()->subMonths($i)->endOfMonth()])->sum('amount') + $fixedIncome,
        ]);

        // --- CATEGORIAS E ORÇAMENTOS ---
        $byCategory = Category::where('budget_limit', '>', 0)->get()->map(function($cat) use ($monthStart) {
            $spent = (float) Expense::where('category_id', $cat->id)->where('spent_at', '>=', $monthStart)->sum('amount');
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

            // Balanços
            'totalMonth' => $totalMonthExpenses,
            'totalIncomeMonth' => $totalMonthIncome,
            'netBalance' => $totalMonthIncome - $totalMonthExpenses,
            'portfolioValue' => $portfolioValue,

            // Gamificação
            'userLevel' => $user->level,
            'userXp' => $user->xp,
            'xpPercentage' => ($user->xp % 1000) / 10,
            'myBadges' => $user->badges()->take(5)->get(),

            // Previsão
            'projectedExpenses' => $projectedExpenses,
            'projectedBalance' => $projectedBalance,
            'projectionStatus' => $projectionStatus,

            // Gráficos e Tabelas
            'chartMax' => max($last6->max('spent'), $last6->max('earned'), 1),
            'last6' => $last6,
            'byCategory' => $byCategory,
            'recent' => Expense::with(['category', 'user'])->latest('spent_at')->take(5)->get(),
            'totalSaved' => (float) Goal::sum('current_amount'),
        ]);
    }
}
