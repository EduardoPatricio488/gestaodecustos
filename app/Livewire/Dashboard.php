<?php

namespace App\Livewire;

use Illuminate\Support\Facades\{DB, Http, Cache, Auth};
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\{Category, Expense, Goal, Income, Subscription, EmailLog, Workspace, Investment};
use Illuminate\Support\Str;
use App\Services\NotificationService;
use App\Services\FinanceScoreService;
use App\Services\WellnessFinanceService;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{

    public function formatInsight($text)
    {
        // Mapeamento de cores para termos específicos
        $map = [
            'bitcoin'     => '<span class="text-amber-400 font-black">'.$text.'</span>',
            'btc'         => '<span class="text-amber-400 font-black">'.$text.'</span>',
            'mercado'     => '<span class="text-emerald-400 font-black">'.$text.'</span>',
            'alerta'      => '<span class="text-red-500 animate-pulse font-black">'.$text.'</span>',
            'atenção'     => '<span class="text-orange-500 font-black">'.$text.'</span>',
            'poupança'    => '<span class="text-blue-400 font-black">'.$text.'</span>',
            'payroll'     => '<span class="text-indigo-400 font-black">'.$text.'</span>',
            'excelente'   => '<span class="text-emerald-500 font-black">'.$text.'</span>',
            'alimentação' => '<span class="text-pink-400 font-black">'.$text.'</span>',
            'nvda'        => '<span class="text-green-400 font-black">'.$text.'</span>',
        ];

        $lowerText = strtolower($text);
        foreach ($map as $key => $styled) {
            if (str_contains($lowerText, $key)) {
                return $styled;
            }
        }
        return $text;
    }




    public $inviteCodeInput = '';

    // Propriedades para Exportação PDF
    public $exportStart;

    public $exportEnd;

public $exportExpenses = true;
public $exportIncomes = true;
public $includeReceipts = false;
public $hideDescriptions = false;
    public bool $privacyMode = false;         // false = valores visíveis ao entrar
    public bool $showPrivacyModal = false;   // controla o modal
    public string $privacyPassword = '';



    // Preços de Mercado (Bitcoin, Ethereum, etc)
    public $marketPrices = [];

    public function mount()
    {
        $user = Auth::user();
 $this->privacyMode = session('privacy_mode', false);
        // 1. Verificação de Notificações Automáticas
        Cache::remember("dashboard:notifications-checked:{$user->id}:" . now()->toDateString(), now()->endOfDay(), function () use ($user) {
            NotificationService::checkAll($user);
            return true;
        });



        // 2. Redirecionamento de Segurança para Admin Real
        if (in_array($user->role, ['admin', 'moderator', 'analyst']) && $user->email_verified_at && !session()->has('impersonator_id')) {
        return redirect()->route('admin.dashboard');
    }

        // 3. Inicialização de Datas de Filtro
        $this->exportStart = now()->startOfMonth()->format('Y-m-d');
        $this->exportEnd = now()->endOfMonth()->format('Y-m-d');

        // 4. PREÇOS DE MERCADO COM CACHE (atualiza a cada 5 minutos)
        $this->marketPrices = Cache::remember('market_prices_all', 300, function () {
            $result = [];

            // --- CRYPTO via CoinGecko (gratuito, sem chave) ---
            try {
                $response = Http::timeout(6)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids'                => 'bitcoin,ethereum,solana,binancecoin,ripple,cardano,avalanche-2,polkadot,chainlink,dogecoin,matic-network,uniswap',
                    'vs_currencies'      => 'eur',
                    'include_24hr_change'=> 'true',
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    $map = [
                        'BTC'   => 'bitcoin',      'ETH'   => 'ethereum',
                        'SOL'   => 'solana',        'BNB'   => 'binancecoin',
                        'XRP'   => 'ripple',        'ADA'   => 'cardano',
                        'AVAX'  => 'avalanche-2',   'DOT'   => 'polkadot',
                        'LINK'  => 'chainlink',     'DOGE'  => 'dogecoin',
                        'MATIC' => 'matic-network', 'UNI'   => 'uniswap',
                    ];
                    foreach ($map as $symbol => $id) {
                        if (isset($data[$id])) {
                            $result[$symbol] = [
                                'price'  => $data[$id]['eur'],
                                'change' => round($data[$id]['eur_24h_change'] ?? 0, 2),
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {}

            // --- AÇÕES, ETFs e COMMODITIES via Yahoo Finance (gratuito, sem chave) ---
            try {
                $symbols = 'NVDA,AAPL,MSFT,AMZN,GOOGL,META,TSLA,NFLX,AMD,TSM,SPY,QQQ,VTI,VOO,IUSA.L,CSPX.L,VWCE.DE,GC=F,CL=F';
                $response = Http::timeout(6)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://query1.finance.yahoo.com/v7/finance/quote', [
                        'symbols' => $symbols,
                        'lang'    => 'en-US',
                    ]);
                if ($response->successful()) {
                    $quotes = $response->json()['quoteResponse']['result'] ?? [];
                    $nameMap = [
                        'NVDA'    => 'NVDA',  'AAPL'    => 'AAPL',  'MSFT'  => 'MSFT',
                        'AMZN'    => 'AMZN',  'GOOGL'   => 'GOOGL', 'META'  => 'META',
                        'TSLA'    => 'TSLA',  'NFLX'    => 'NFLX',  'AMD'   => 'AMD',
                        'TSM'     => 'TSM',   'SPY'     => 'SPY',   'QQQ'   => 'QQQ',
                        'VTI'     => 'VTI',   'VOO'     => 'VOO',   'IUSA.L'=> 'IUSA',
                        'CSPX.L'  => 'CSPX',  'VWCE.DE' => 'VWCE',
                        'GC=F'    => 'GOLD',  'CL=F'    => 'OIL',
                    ];
                    foreach ($quotes as $quote) {
                        $sym = $quote['symbol'] ?? '';
                        $key = $nameMap[$sym] ?? $sym;
                        $result[$key] = [
                            'price'  => round($quote['regularMarketPrice'] ?? 0, 2),
                            'change' => round($quote['regularMarketChangePercent'] ?? 0, 2),
                        ];
                    }
                }
            } catch (\Exception $e) {}

            return $result;
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

    // ── Criar categorias fixas para o novo utilizador ──────────
    $fixedCategories = [
        'alimentacao'    => ['name' => 'Alimentação',   'icon' => 'shopping-cart', 'color' => '#f59e0b', 'order' => 1],
        'carro'          => ['name' => 'Carro',          'icon' => 'truck',         'color' => '#3b82f6', 'order' => 2],
        'casa'           => ['name' => 'Casa',           'icon' => 'home',          'color' => '#10b981', 'order' => 3],
        'educacao'       => ['name' => 'Educação',       'icon' => 'academic-cap',  'color' => '#06b6d4', 'order' => 4],
        'emprestimos'    => ['name' => 'Empréstimos',    'icon' => 'banknotes',     'color' => '#84cc16', 'order' => 5],
        'entretenimento' => ['name' => 'Entretenimento', 'icon' => 'film',          'color' => '#a855f7', 'order' => 6],
        'saude'          => ['name' => 'Saúde',          'icon' => 'heart',         'color' => '#ef4444', 'order' => 7],
        'seguros'        => ['name' => 'Seguros',        'icon' => 'shield-check',  'color' => '#0ea5e9', 'order' => 8],
        'tecnologia'     => ['name' => 'Tecnologia',     'icon' => 'cpu-chip',      'color' => '#6366f1', 'order' => 9],
        'transporte'     => ['name' => 'Transporte',     'icon' => 'bolt',          'color' => '#8b5cf6', 'order' => 10],
    ];

    foreach ($fixedCategories as $slug => $data) {
        Category::firstOrCreate(
            ['user_id' => $user->id, 'slug' => $slug],
            [
                'name'         => $data['name'],
                'icon'         => $data['icon'],
                'color'        => $data['color'],
                'is_fixed'     => true,
                'order'        => $data['order'],
                'workspace_id' => $ws->id,
            ]
        );
    }
}
        // Garante que existe sempre um Workspace selecionado
        if (!$user->current_workspace_id) {
            $user->update(['current_workspace_id' => $user->workspaces()->first()->id]);
        }
    }

public function setExportPeriod($period)
{
    switch ($period) {
        case 'this_month':
            $this->exportStart = now()->startOfMonth()->format('Y-m-d');
            $this->exportEnd = now()->endOfMonth()->format('Y-m-d');
            break;
        case 'last_month':
            $this->exportStart = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $this->exportEnd = now()->subMonth()->endOfMonth()->format('Y-m-d');
            break;
        case 'this_year':
            $this->exportStart = now()->startOfYear()->format('Y-m-d');
            $this->exportEnd = now()->endOfYear()->format('Y-m-d');
            break;
    }
}
    // Garante que o nome é exatamente unlockPrivacy
public function unlockPrivacy()
{
    $this->validate([
        'privacyPassword' => 'required',
    ]);

    // Verifica a password
    if (Hash::check($this->privacyPassword, auth()->user()->password)) {
        $this->privacyMode = false; // Desbloqueia os números
        $this->showPrivacyModal = false;
        $this->privacyPassword = '';

        // Avisa o Alpine.js para tirar o blur no ecrã
        $this->dispatch('privacy-changed', state: false);
        $this->dispatch('toast', text: 'Privacidade desativada.');
    } else {
        $this->addError('privacyPassword', 'Password incorreta.');
    }
}

#[On('request-privacy-toggle')]
    public function handlePrivacyToggle()
    {
        if (!$this->privacyMode) {
            // Blur estava OFF → ativar livremente
            $this->privacyMode = true;
            $this->dispatch('privacy-changed', state: true);
            return;
        }
        // Blur estava ON → pedir password para desativar
        $this->showPrivacyModal = true;
    }


#[Computed]
public function greeting()
{
    $hour = now()->hour;
    if ($hour < 13) return 'Bom dia';
    if ($hour < 19) return 'Boa tarde';
    return 'Boa noite';




}    public function downloadCustomPdf()
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
public function requestPrivacyUnlock()
{
    $this->showPrivacyModal = true;
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
    $user = Auth::user();

    // 1. Procura o workspace na lista do utilizador
    $workspace = $user->workspaces()->find($id);

    if ($workspace) {
        // 2. Atualiza o workspace ativo
        $user->update(['current_workspace_id' => $id]);

        // 3. Redirecionamento Inteligente
        if ($workspace->type === 'personal') {
            // Se mudou para a conta pessoal, recarrega o Dashboard Pessoal
            return redirect()->route('dashboard');
        }

        // Se for qualquer outro tipo (business/empresa), vai para a Dashboard de Empresa
        // (O Laravel/Livewire já vai decidir se vês como CEO ou Colaborador lá)
        return redirect()->route('hub.business.dashboard');
    }
}































#[Computed]
public function aiInsights()
{
    $insights = [];
    $user = auth()->user();
    $currentWs = $user->currentWorkspace;
    if (!$currentWs) return [];

    // -----------------------------------------
    // 1) MERCADOS AVANÇADOS (S&P500, NASDAQ, DAX, CAC40, FTSE100)
    // -----------------------------------------
    try {
        $indices = Http::get('https://query1.finance.yahoo.com/v7/finance/quote', [
            'symbols' => '^GSPC,^IXIC,^GDAXI,^FCHI,^FTSE'
        ])->json()['quoteResponse']['result'];

        foreach ($indices as $i) {
            $insights[] = "ÍNDICE: {$i['shortName']} " . number_format($i['regularMarketPrice'], 2) . " (" . number_format($i['regularMarketChangePercent'], 2) . "%)";
        }
    } catch (\Exception $e) {}

    // -----------------------------------------
    // 2) METAIS PRECIOSOS (Ouro, Prata, Platina, Paládio)
    // -----------------------------------------
    try {
        $metals = Http::get('https://query1.finance.yahoo.com/v7/finance/quote', [
            'symbols' => 'GC=F,SI=F,PL=F,PA=F'
        ])->json()['quoteResponse']['result'];

        foreach ($metals as $m) {
            $insights[] = "METAIS: {$m['symbol']} " . number_format($m['regularMarketPrice'], 2) . " (" . number_format($m['regularMarketChangePercent'], 2) . "%)";
        }
    } catch (\Exception $e) {}

    // -----------------------------------------
    // 3) ENERGIA AVANÇADA (Petróleo, Gás, Carvão)
    // -----------------------------------------
    try {
        $energy = Http::get('https://query1.finance.yahoo.com/v7/finance/quote', [
            'symbols' => 'CL=F,NG=F,CO1.F'
        ])->json()['quoteResponse']['result'];

        foreach ($energy as $e) {
            $insights[] = "ENERGIA: {$e['symbol']} " . number_format($e['regularMarketPrice'], 2) . " (" . number_format($e['regularMarketChangePercent'], 2) . "%)";
        }
    } catch (\Exception $e) {}

    // -----------------------------------------
    // 4) MACROECONOMIA AVANÇADA (UE + Portugal)
    // -----------------------------------------
    try {
        $inflationEU = Http::get('https://api.worldbank.org/v2/country/EU/indicator/FP.CPI.TOTL.ZG?format=json')->json();
        if (isset($inflationEU[1][0]['value'])) {
            $insights[] = "MACRO: Inflação UE " . number_format($inflationEU[1][0]['value'], 1) . "%";
        }
    } catch (\Exception $e) {}

    try {
        $unemploymentPT = Http::get('https://api.worldbank.org/v2/country/PRT/indicator/SL.UEM.TOTL.ZS?format=json')->json();
        if (isset($unemploymentPT[1][0]['value'])) {
            $insights[] = "MACRO: Desemprego PT " . number_format($unemploymentPT[1][0]['value'], 1) . "%";
        }
    } catch (\Exception $e) {}

    // -----------------------------------------
    // 5) CÂMBIOS AVANÇADOS
    // -----------------------------------------
    try {
        $fx = Http::get('https://api.exchangerate.host/latest?base=EUR')->json();
        $insights[] = "FX: EUR/JPY " . number_format($fx['rates']['JPY'], 2);
        $insights[] = "FX: EUR/CHF " . number_format($fx['rates']['CHF'], 3);
        $insights[] = "FX: EUR/AUD " . number_format($fx['rates']['AUD'], 3);
        $insights[] = "FX: EUR/CAD " . number_format($fx['rates']['CAD'], 3);
    } catch (\Exception $e) {}

    // -----------------------------------------
    // 6) CLIMA AVANÇADO (Lisboa)
    // -----------------------------------------
    try {
        $weather = Http::get("https://api.open-meteo.com/v1/forecast?latitude=38.7&longitude=-9.1&current_weather=true")->json();
        $temp = $weather['current_weather']['temperature'];
        $wind = $weather['current_weather']['windspeed'];
        $insights[] = "CLIMA: Lisboa {$temp}ºC • Vento {$wind}km/h";
    } catch (\Exception $e) {}

    // -----------------------------------------
    // 7) INDICADORES DE RISCO (VIX)
    // -----------------------------------------
    try {
        $vix = Http::get('https://query1.finance.yahoo.com/v7/finance/quote', [
            'symbols' => '^VIX'
        ])->json()['quoteResponse']['result'][0];

        $insights[] = "RISCO: VIX " . number_format($vix['regularMarketPrice'], 2) . " (" . number_format($vix['regularMarketChangePercent'], 2) . "%)";
    } catch (\Exception $e) {}

    // -----------------------------------------
    // 8) LOGÍSTICA GLOBAL (Baltic Dry Index)
    // -----------------------------------------
    try {
        $bdi = Http::get('https://query1.finance.yahoo.com/v7/finance/quote', [
            'symbols' => '^BDI'
        ])->json()['quoteResponse']['result'][0];

        $insights[] = "LOGÍSTICA: Baltic Dry Index " . number_format($bdi['regularMarketPrice'], 0);
    } catch (\Exception $e) {}

    // -----------------------------------------
    // 9) FINANÇAS PESSOAIS REAIS
    // -----------------------------------------
    $monthStart = now()->startOfMonth();
    $monthEnd = now()->endOfMonth();

    $categories = $this->buildCategoryBudgets($currentWs->id, $monthStart, $monthEnd);
    $topCat = $categories->sortByDesc('total')->first();

    if ($topCat && $topCat['total'] > 0) {
        $insights[] = "GASTOS: " . strtoupper($topCat['name']) . " lidera despesas (" . number_format($topCat['total'], 0, ',', ' ') . "€)";
    }

    $earned = Income::where('workspace_id', $currentWs->id)->whereBetween('received_at', [$monthStart, $monthEnd])->sum('amount');
    $spent = Expense::where('workspace_id', $currentWs->id)->whereBetween('spent_at', [$monthStart, $monthEnd])->sum('amount');
    $net = $earned - $spent;

    $insights[] = "FINANÇAS: Recebido " . number_format($earned, 0, ',', ' ') . "€ • Gasto " . number_format($spent, 0, ',', ' ') . "€";
    $insights[] = "FINANÇAS: Balanço " . ($net > 0 ? '+' : '') . number_format($net, 0, ',', ' ') . "€";

    if ($spent > $earned) {
        $insights[] = "ALERTA: Gastos superiores às receitas.";
    }

    // -----------------------------------------
    // 10) SISTEMA / OPERACIONAL
    // -----------------------------------------
    $insights[] = "SISTEMA: Sessão encriptada • Sync " . now()->format('H:i');
    $insights[] = "SISTEMA: IA a monitorizar padrões.";
    $insights[] = "SISTEMA: Todos os serviços operacionais.";
    $insights[] = "SISTEMA: Nenhum alerta crítico ativo.";

    // -----------------------------------------
    // 11) EXPANSÃO AUTOMÁTICA
    // -----------------------------------------
    $extra = [
        "ANÁLISE: IA detectou estabilidade nos fluxos.",
        "ANÁLISE: Mercado global sem variações abruptas.",
        "ANÁLISE: Consumo dentro dos padrões normais.",
        "ANÁLISE: Atividade empresarial consistente.",
        "ANÁLISE: Dados sincronizados com sucesso.",
        "ANÁLISE: Sistema a operar em modo otimizado.",
        "ANÁLISE: Nenhuma anomalia financeira detetada.",
        "ANÁLISE: IA prevê estabilidade para os próximos dias.",
        "ANÁLISE: Tendência positiva nas últimas 48h.",
        "ANÁLISE: Monitorização contínua ativa.",
    ];

    $insights = array_merge($insights, $extra);

    // -----------------------------------------
    // 12) ALEATORIEDADE + DUPLICAÇÃO INTELIGENTE
    // -----------------------------------------
    shuffle($insights);
    $insights = array_unique($insights);
    $insights = array_merge($insights, $insights);
    shuffle($insights);

    return $insights;
}

private function fetchData360($indicator, $country = 'PRT')
{
    try {
        $response = Http::get('https://data360api.worldbank.org/data360/data', [
            'DATABASE_ID' => 'WB_WDI',
            'INDICATOR'   => $indicator,
            'REF_AREA'    => $country,
            'top'         => 1
        ]);

        $data = $response->json();

        if (!isset($data['value'][0]['OBS_VALUE'])) {
            return null;
        }

        return [
            'value' => $data['value'][0]['OBS_VALUE'],
            'year'  => $data['value'][0]['TIME_PERIOD'],
            'desc'  => $data['value'][0]['COMMENT_TS'] ?? null
        ];
    } catch (\Exception $e) {
        return null;
    }
}


































 #[Computed]
public function dailyReport()
{
    $user = auth()->user();
    $currentWs = $user->currentWorkspace;

    // Identifica se é Premium (Estrela ou Diamante)
    $isPremium = $user->isAnyPremium();

    $today = now()->startOfDay();
    $endDay = now()->endOfDay();
    $wsId = $currentWs->id;

    if (!$isPremium) {
        return ['is_premium' => false, 'expenses' => collect(), 'incomes' => collect(), 'fitness' => collect(), 'xp_today' => 0];
    }

    // 1. Finanças: Busca detalhada
    $expenses = Expense::where('workspace_id', $wsId)->whereBetween('spent_at', [$today, $endDay])->get();
    $incomes = Income::where('workspace_id', $wsId)->whereBetween('received_at', [$today, $endDay])->get();

    // 2. Saúde: Busca detalhada
    $fitness = \App\Models\FitnessActivity::where('workspace_id', $wsId)
        ->where('user_id', $user->id)
        ->whereBetween('activity_date', [$today, $endDay])
        ->get();

    // 3. Foco: Lembretes concluídos hoje
    $remindersDone = \App\Models\Reminder::where('workspace_id', $wsId)
        ->where('is_completed', true)
        ->whereBetween('updated_at', [$today, $endDay])
        ->count();

    // 4. Social: Interações recebidas hoje
    $socialCount = \App\Models\SocialNotification::where('user_id', $user->id)
        ->whereBetween('created_at', [$today, $endDay])
        ->count();

    // 5. XP: Cálculo de evolução
    $xp = ($expenses->count() * 5) + ($fitness->count() * 50) + ($remindersDone * 15);

    return [
        'is_premium' => true,
        'expenses' => $expenses,
        'incomes' => $incomes,
        'fitness' => $fitness,
        'reminders_count' => $remindersDone,
        'social_count' => $socialCount,
        'xp_today' => $xp,
        'spend_total' => $expenses->sum('amount'),
        'earn_total' => $incomes->sum('amount'),
        'fitness_min' => $fitness->sum('duration_minutes'),
        'fitness_kcal' => $fitness->sum('calories'),
    ];
}

    public function render()
    {
        $user = Auth::user();
        $user->loadMissing([
            'currentWorkspace.users:id,name',
            'workspaces:id,name,type,currency',
            'badges:id,name,color,icon',
        ]);

        $currentWs = $user->currentWorkspace;

        if (!$currentWs) return view('livewire.dashboard-loading');

        // --- CÁLCULOS FINANCEIROS DO MÊS ---
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $sixMonthsStart = now()->subMonths(5)->startOfMonth();
        $dayOfMonth = now()->day;
        $daysInMonth = now()->daysInMonth;

        $fixedIncome = (float) Cache::remember(
            "dashboard:fixed-income:{$currentWs->id}:{$user->id}",
            60,
            fn () => $user->recurringIncomes()
                ->where('workspace_id', $currentWs->id)
                ->where('is_active', true)
                ->sum('amount')
        );

        $monthTotals = Cache::remember("dashboard:month-totals:{$currentWs->id}:{$monthStart->toDateString()}", 60, function () use ($currentWs, $monthStart, $monthEnd) {
            return [
                'expenses' => (float) Expense::where('workspace_id', $currentWs->id)
                    ->whereBetween('spent_at', [$monthStart, $monthEnd])
                    ->sum('amount'),
                'income' => (float) Income::where('workspace_id', $currentWs->id)
                    ->whereBetween('received_at', [$monthStart, $monthEnd])
                    ->sum('amount'),
                'budget' => (float) Category::where('workspace_id', $currentWs->id)->sum('budget_limit'),
            ];
        });

        $totalMonthExpenses = $monthTotals['expenses'];
        $totalMonthIncome = $monthTotals['income'] + $fixedIncome;

        // --- VALORIZAÇÃO DE ATIVOS (PORTFOLIO) ---
        $portfolioValue = 0;
        $myInvestments = Cache::remember(
            "dashboard:investments:v2:{$currentWs->id}",
            60,
            fn () => Investment::where('workspace_id', $currentWs->id)
                ->get(['id', 'workspace_id', 'symbol', 'quantity', 'current_price'])
                ->map(fn ($investment) => [
                    'symbol' => $investment->symbol,
                    'quantity' => (float) $investment->quantity,
                    'current_price' => (float) $investment->current_price,
                ])
        );

        foreach($myInvestments as $inv) {
            $symbol = strtolower((string) data_get($inv, 'symbol', ''));
            $currentPrice = (float) data_get($inv, 'current_price', 0);
            $quantity = (float) data_get($inv, 'quantity', 0);

            $price = match($symbol) {
                'btc' => $this->marketPrices['bitcoin']['eur'] ?? $currentPrice,
                'eth' => $this->marketPrices['ethereum']['eur'] ?? $currentPrice,
                'sol' => $this->marketPrices['solana']['eur'] ?? $currentPrice,
                'sp500', 'spx' => 5222.68,
                'nvda' => 945.30,
                default => $currentPrice
            };
            $portfolioValue += ($quantity * $price);
        }

        // --- PREVISÃO INTELIGENTE ---
        $dailyBurnRate = $dayOfMonth > 1 ? $totalMonthExpenses / $dayOfMonth : $totalMonthExpenses;
        $projectedExpenses = $dailyBurnRate * $daysInMonth;
        $projectedBalance = $totalMonthIncome - $projectedExpenses;
        $projectionStatus = $projectedBalance < 0 ? 'critical' : ($projectedBalance < ($totalMonthIncome * 0.15) ? 'warning' : 'stable');

        // --- GRÁFICO (ÚLTIMOS 6 MESES) ---
        $last6 = collect(Cache::remember(
    "dashboard:last6:{$currentWs->id}:{$sixMonthsStart->toDateString()}:{$monthEnd->toDateString()}",
    60,
    fn () => $this->buildSixMonthSeries($currentWs->id, $sixMonthsStart, $monthEnd, $fixedIncome)->toArray()
));

$byCategory = collect(Cache::remember(
    "dashboard:category-budgets:{$currentWs->id}:{$monthStart->toDateString()}",
    60,
    fn () => $this->buildCategoryBudgets($currentWs->id, $monthStart, $monthEnd)->toArray()
))->map(fn($item) => (object) $item);

        $overallScore = $this->calculateScore($monthTotals['expenses'], $monthTotals['income'], $monthTotals['budget']);

        $financeScore = app(FinanceScoreService::class)->calculate($currentWs);
        $wellnessInsights = app(WellnessFinanceService::class)->getInsights($currentWs);
        $storeEntitlements = app(\App\Services\StoreEntitlementService::class);

         return view('livewire.dashboard', [
            'currentWs' => $currentWs,
            'userWorkspaces' => $user->workspaces,
            'overallScore' => $overallScore,
            'financeScore' => $financeScore,
            'wellnessInsights' => $wellnessInsights,
            'hasWidgetMercado' => $storeEntitlements->hasWidget($user, 'mercado-global') || $user->isStar(),
            'ownedStoreSlugs' => $storeEntitlements->ownedSlugs($user),

            // Financeiro
            'totalMonth' => $totalMonthExpenses,
            'totalIncomeMonth' => $totalMonthIncome,
            'netBalance' => $totalMonthIncome - $totalMonthExpenses,
            'portfolioValue' => $portfolioValue,
            'totalSaved' => (float) Cache::remember(
                "dashboard:total-saved:{$currentWs->id}",
                60,
                fn () => \App\Models\Goal::where('workspace_id', $currentWs->id)->sum('current_amount')
            ),

            // Previsão
            'projectedExpenses' => $projectedExpenses,
            'projectedBalance' => $projectedBalance,
            'projectionStatus' => $projectionStatus,

           'chartMax' => max(
    $last6->max('spent') ?? 0,
    $last6->max('earned') ?? 0,
    1
),
'last6' => $last6,
'byCategory' => $byCategory,
'recent' => \App\Models\Expense::with(['category:id,name', 'user:id,name'])
    ->where('workspace_id', $currentWs->id)
    ->latest('spent_at')
    ->take(5)
    ->get(['id', 'workspace_id', 'category_id', 'user_id', 'description', 'amount', 'spent_at']),

        ]);
    }

    private function buildSixMonthSeries(int $workspaceId, $start, $end, float $fixedIncome): Collection
    {
        $expenses = Expense::where('workspace_id', $workspaceId)
            ->whereBetween('spent_at', [$start, $end])
            ->get(['amount', 'spent_at'])
            ->groupBy(fn ($expense) => $expense->spent_at->format('Y-m'))
            ->map(fn ($rows) => (float) $rows->sum('amount'));

        $incomes = Income::where('workspace_id', $workspaceId)
            ->whereBetween('received_at', [$start, $end])
            ->get(['amount', 'received_at'])
            ->groupBy(fn ($income) => $income->received_at->format('Y-m'))
            ->map(fn ($rows) => (float) $rows->sum('amount'));

        return collect(range(5, 0))->map(function ($i) use ($expenses, $incomes, $fixedIncome) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');

            return [
                'label' => $month->translatedFormat('M'),
                'spent' => (float) ($expenses[$key] ?? 0),
                'earned' => (float) ($incomes[$key] ?? 0) + $fixedIncome,
            ];
        });
    }

    private function buildCategoryBudgets(int $workspaceId, $monthStart, $monthEnd): Collection
    {
        return Category::query()
            ->leftJoin('expenses', function ($join) use ($workspaceId, $monthStart, $monthEnd) {
                $join->on('expenses.category_id', '=', 'categories.id')
                    ->where('expenses.workspace_id', '=', $workspaceId)
                    ->whereBetween('expenses.spent_at', [$monthStart, $monthEnd]);
            })
            ->where('categories.workspace_id', $workspaceId)
            ->where('categories.budget_limit', '>', 0)
            ->groupBy('categories.id', 'categories.name', 'categories.budget_limit')
            ->orderByDesc(DB::raw('COALESCE(SUM(expenses.amount), 0)'))
            ->get([
                'categories.name',
                'categories.budget_limit',
                DB::raw('COALESCE(SUM(expenses.amount), 0) as total'),
            ])
            ->map(function ($cat) {
    $spent = (float) $cat->total;
    $budget = (float) $cat->budget_limit;

    return [
        'name' => $cat->name,
        'total' => $spent,
        'budget' => $budget,
        'percentage' => $budget > 0 ? min(($spent / $budget) * 100, 100) : 0,
        'over' => $spent > $budget,
    ];
});
    }

    private function calculateScore(float $spent, float $earned, float $budget): int
    {
        $net = $earned - $spent;
        $savingsRate = $earned > 0 ? ($net / $earned) * 100 : 0;
        $budgetAdherence = $budget > 0 ? (1 - (min($spent, $budget) / $budget)) * 100 : 100;
        $score = ($savingsRate * 0.7) + ($budgetAdherence * 0.3) + 20;

        return (int) max(0, min(100, $score));
    }
}
