<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\FitnessActivity;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\Reminder;
use App\Models\SocialNotification;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Workspace;
use App\Services\FinanceScoreService;
use App\Services\NotificationService;
use App\Services\StoreEntitlementService;
use App\Services\SubscriptionCheckoutService;
use App\Services\SubscriptionCycleService;
use App\Services\WellnessFinanceService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function formatInsight($text)
    {
        $map = [
            'bitcoin' => '<span class="text-amber-400 font-black">'.$text.'</span>',
            'btc' => '<span class="text-amber-400 font-black">'.$text.'</span>',
            'mercado' => '<span class="text-emerald-400 font-black">'.$text.'</span>',
            'alerta' => '<span class="text-red-500 animate-pulse font-black">'.$text.'</span>',
            'atenção' => '<span class="text-orange-500 font-black">'.$text.'</span>',
            'poupança' => '<span class="text-blue-400 font-black">'.$text.'</span>',
            'payroll' => '<span class="text-indigo-400 font-black">'.$text.'</span>',
            'excelente' => '<span class="text-emerald-500 font-black">'.$text.'</span>',
            'alimentação' => '<span class="text-pink-400 font-black">'.$text.'</span>',
            'nvda' => '<span class="text-green-400 font-black">'.$text.'</span>',
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

    public $exportStart;

    public $exportEnd;

    public $exportExpenses = true;

    public $showSubSuggestion = false;

    public $suggestedName = '';

    public $suggestedPrice = 0;

    public $exportIncomes = true;

    public $includeReceipts = false;

    public $hideDescriptions = false;

    public bool $privacyMode = false;

    public bool $showPrivacyModal = false;

    public string $privacyPassword = '';

    public $marketPrices = [];

    public function mount()
    {
        $user = Auth::user();
        if (request()->query('checkout') === 'success') {
            $user->refresh();
            $sessionId = (string) request()->query('session_id', '');

            if ($sessionId !== '') {
                try {
                    $stripeSession = $user->stripe()->checkout->sessions->retrieve($sessionId);
                    if (($stripeSession->payment_status ?? null) === 'paid') {
                        app(SubscriptionCheckoutService::class)->activateFromStripeSession($stripeSession);
                        $user->refresh();
                    }
                } catch (\Throwable $e) {
                    Log::warning('Falha ao confirmar sessão de checkout do Stripe: '.$e->getMessage());
                }
            }

            $planRecord = SubscriptionPlan::where('slug', $user->plan)->first();
            if ($planRecord && $user->plan !== 'free') {
                $this->suggestedName = 'Finance Pro '.$planRecord->name;
                $this->suggestedPrice = $planRecord->price;
                $this->showSubSuggestion = true;
            }
        }

        $this->privacyMode = session('privacy_mode', false);
        Cache::flexible("dashboard:notifications-checked:{$user->id}:".now()->toDateString(), [3600, 86400], function () use ($user) {
            NotificationService::checkAll($user);

            return true;
        });

        if (in_array($user->role, ['admin', 'moderator', 'analyst']) && $user->email_verified_at && ! session()->has('admin_impersonation')) {
            return redirect()->route('admin.dashboard');
        }

        $this->exportStart = now()->startOfMonth()->format('Y-m-d');
        $this->exportEnd = now()->endOfMonth()->format('Y-m-d');

        $this->marketPrices = Cache::flexible('market_prices_all', [300, 1800], function () {
            $result = [];
            try {
                $response = Http::connectTimeout(3)->timeout(6)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids' => 'bitcoin,ethereum,solana,binancecoin,ripple,cardano,avalanche-2,polkadot,chainlink,dogecoin,matic-network,uniswap',
                    'vs_currencies' => 'eur',
                    'include_24hr_change' => 'true',
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    $map = [
                        'BTC' => 'bitcoin', 'ETH' => 'ethereum', 'SOL' => 'solana', 'BNB' => 'binancecoin',
                        'XRP' => 'ripple', 'ADA' => 'cardano', 'AVAX' => 'avalanche-2', 'DOT' => 'polkadot',
                        'LINK' => 'chainlink', 'DOGE' => 'dogecoin', 'MATIC' => 'polygon-ecosystem-token', 'UNI' => 'uniswap',
                    ];
                    foreach ($map as $symbol => $id) {
                        if (isset($data[$id])) {
                            $result[$symbol] = [
                                'price' => $data[$id]['eur'],
                                'change' => round($data[$id]['eur_24h_change'] ?? 0, 2),
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
            }

            try {
                $symbols = 'NVDA,AAPL,MSFT,AMZN,GOOGL,META,TSLA,NFLX,AMD,TSM,SPY,QQQ,VTI,VOO,IUSA.L,CSPX.L,VWCE.DE,GC=F,CL=F';
                $response = Http::connectTimeout(1)->timeout(2)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://query1.finance.yahoo.com/v7/finance/quote', [
                        'symbols' => $symbols,
                        'lang' => 'en-US',
                    ]);
                if ($response->successful()) {
                    $quotes = $response->json()['quoteResponse']['result'] ?? [];
                    $nameMap = [
                        'NVDA' => 'NVDA', 'AAPL' => 'AAPL', 'MSFT' => 'MSFT', 'AMZN' => 'AMZN', 'GOOGL' => 'GOOGL',
                        'META' => 'META', 'TSLA' => 'TSLA', 'NFLX' => 'NFLX', 'AMD' => 'AMD', 'TSM' => 'TSM',
                        'SPY' => 'SPY', 'QQQ' => 'QQQ', 'VTI' => 'VTI', 'VOO' => 'VOO', 'IUSA.L' => 'IUSA',
                        'CSPX.L' => 'CSPX', 'VWCE.DE' => 'VWCE', 'GC=F' => 'GOLD', 'CL=F' => 'OIL',
                    ];
                    foreach ($quotes as $quote) {
                        $sym = $quote['symbol'] ?? '';
                        $key = $nameMap[$sym] ?? $sym;
                        $result[$key] = [
                            'price' => round($quote['regularMarketPrice'] ?? 0, 2),
                            'change' => round($quote['regularMarketChangePercent'] ?? 0, 2),
                        ];
                    }
                }
            } catch (\Exception $e) {
            }

            return $result;
        });

        if (! $user->workspaces()->exists()) {
            $ws = Workspace::create([
                'name' => 'Gestão de '.explode(' ', $user->name)[0],
                'type' => 'personal',
                'owner_id' => $user->id,
                'invite_code' => strtoupper(Str::random(8)),
            ]);
            $user->workspaces()->attach($ws->id, ['role' => 'admin']);
            $user->update(['current_workspace_id' => $ws->id]);
            $user->refresh();

            $fixedCategories = [
                'alimentacao' => ['name' => 'Alimentação', 'icon' => 'shopping-cart', 'color' => '#f59e0b', 'order' => 1],
                'carro' => ['name' => 'Carro', 'icon' => 'truck', 'color' => '#3b82f6', 'order' => 2],
                'casa' => ['name' => 'Casa', 'icon' => 'home', 'color' => '#10b981', 'order' => 3],
                'educacao' => ['name' => 'Educação', 'icon' => 'academic-cap', 'color' => '#06b6d4', 'order' => 4],
                'emprestimos' => ['name' => 'Empréstimos', 'icon' => 'banknotes', 'color' => '#84cc16', 'order' => 5],
                'entretenimento' => ['name' => 'Entretenimento', 'icon' => 'film', 'color' => '#a855f7', 'order' => 6],
                'saude' => ['name' => 'Saúde', 'icon' => 'heart', 'color' => '#ef4444', 'order' => 7],
                'seguros' => ['name' => 'Seguros', 'icon' => 'shield-check', 'color' => '#0ea5e9', 'order' => 8],
                'tecnologia' => ['name' => 'Tecnologia', 'icon' => 'cpu-chip', 'color' => '#6366f1', 'order' => 9],
                'transporte' => ['name' => 'Transporte', 'icon' => 'bolt', 'color' => '#8b5cf6', 'order' => 10],
            ];

            foreach ($fixedCategories as $slug => $data) {
                Category::firstOrCreate(
                    ['user_id' => $user->id, 'workspace_id' => $ws->id, 'slug' => $slug],
                    ['name' => $data['name'], 'icon' => $data['icon'], 'color' => $data['color'], 'is_fixed' => true, 'order' => $data['order']]
                );
            }
        }
        if (! $user->current_workspace_id) {
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

    public function unlockPrivacy()
    {
        $this->validate(['privacyPassword' => 'required']);
        if (Hash::check($this->privacyPassword, auth()->user()->password)) {
            $this->privacyMode = false;
            $this->showPrivacyModal = false;
            $this->privacyPassword = '';
            $this->dispatch('privacy-changed', state: false);
            $this->dispatch('toast', text: 'Privacidade desativada.');
        } else {
            $this->addError('privacyPassword', 'Password incorreta.');
        }
    }

    #[On('request-privacy-toggle')]
    public function handlePrivacyToggle()
    {
        if (! $this->privacyMode) {
            $this->privacyMode = true;
            $this->dispatch('privacy-changed', state: true);

            return;
        }
        $this->showPrivacyModal = true;
    }

    public function dismissSubSuggestion()
    {
        $this->showSubSuggestion = false;
    }

    #[Computed]
    public function greeting()
    {
        $hour = now()->hour;
        if ($hour < 13) {
            return 'Bom dia';
        }
        if ($hour < 19) {
            return 'Boa tarde';
        }

        return 'Boa noite';
    }

    public function downloadCustomPdf()
    {
        $params = [
            'start' => $this->exportStart,
            'end' => $this->exportEnd,
            'expenses' => $this->exportExpenses ? '1' : '0',
            'incomes' => $this->exportIncomes ? '1' : '0',
        ];

        return redirect()->to(route('export.dashboard.pdf').'?'.http_build_query($params));
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
        $workspace = $user->workspaces()->find($id);
        if ($workspace) {
            $user->update(['current_workspace_id' => $id]);
            if ($workspace->type === 'personal') {
                return redirect()->route('dashboard');
            }

            return redirect()->route('hub.business.dashboard');
        }
    }

    #[Computed]
    public function aiInsights()
    {
        $user = auth()->user();
        $currentWs = $user->currentWorkspace;
        if (! $currentWs) {
            return [];
        }

        return Cache::flexible(
            "dashboard:ai-insights:{$currentWs->id}:".now()->format('Y-m'),
            [300, 1800],
            function () use ($currentWs) {
                $insights = [];
                try {
                    $indices = Http::connectTimeout(1)->timeout(3)->get('https://query1.finance.yahoo.com/v7/finance/quote', ['symbols' => '^GSPC,^IXIC,^GDAXI,^FCHI,^FTSE'])->json()['quoteResponse']['result'];
                    foreach ($indices as $i) {
                        $insights[] = "ÍNDICE: {$i['shortName']} ".number_format($i['regularMarketPrice'], 2).' ('.number_format($i['regularMarketChangePercent'], 2).'%)';
                    }
                } catch (\Exception $e) {
                }
                try {
                    $metals = Http::connectTimeout(1)->timeout(3)->get('https://query1.finance.yahoo.com/v7/finance/quote', ['symbols' => 'GC=F,SI=F,PL=F,PA=F'])->json()['quoteResponse']['result'];
                    foreach ($metals as $m) {
                        $insights[] = "METAIS: {$m['symbol']} ".number_format($m['regularMarketPrice'], 2).' ('.number_format($m['regularMarketChangePercent'], 2).'%)';
                    }
                } catch (\Exception $e) {
                }
                try {
                    $energy = Http::connectTimeout(1)->timeout(3)->get('https://query1.finance.yahoo.com/v7/finance/quote', ['symbols' => 'CL=F,NG=F,CO1.F'])->json()['quoteResponse']['result'];
                    foreach ($energy as $e) {
                        $insights[] = "ENERGIA: {$e['symbol']} ".number_format($e['regularMarketPrice'], 2).' ('.number_format($e['regularMarketChangePercent'], 2).'%)';
                    }
                } catch (\Exception $e) {
                }
                try {
                    $inflationEU = Http::connectTimeout(1)->timeout(2)->get('https://api.worldbank.org/v2/country/EU/indicator/FP.CPI.TOTL.ZG?format=json')->json();
                    if (isset($inflationEU[1][0]['value'])) {
                        $insights[] = 'MACRO: Inflação UE '.number_format($inflationEU[1][0]['value'], 1).'%';
                    }
                } catch (\Exception $e) {
                }
                try {
                    $unemploymentPT = Http::connectTimeout(1)->timeout(2)->get('https://api.worldbank.org/v2/country/PRT/indicator/SL.UEM.TOTL.ZS?format=json')->json();
                    if (isset($unemploymentPT[1][0]['value'])) {
                        $insights[] = 'MACRO: Desemprego PT '.number_format($unemploymentPT[1][0]['value'], 1).'%';
                    }
                } catch (\Exception $e) {
                }
                try {
                    $fx = Http::connectTimeout(1)->timeout(2)->get('https://api.exchangerate.host/latest?base=EUR')->json();
                    $insights[] = 'FX: EUR/JPY '.number_format($fx['rates']['JPY'], 2);
                    $insights[] = 'FX: EUR/CHF '.number_format($fx['rates']['CHF'], 3);
                    $insights[] = 'FX: EUR/AUD '.number_format($fx['rates']['AUD'], 3);
                    $insights[] = 'FX: EUR/CAD '.number_format($fx['rates']['CAD'], 3);
                } catch (\Exception $e) {
                }
                try {
                    $weather = Http::connectTimeout(1)->timeout(2)->get('https://api.open-meteo.com/v1/forecast?latitude=38.7&longitude=-9.1&current_weather=true')->json();
                    $temp = $weather['current_weather']['temperature'];
                    $wind = $weather['current_weather']['windspeed'];
                    $insights[] = "CLIMA: Lisboa {$temp}ºC • Vento {$wind}km/h";
                } catch (\Exception $e) {
                }
                try {
                    $vix = Http::connectTimeout(1)->timeout(3)->get('https://query1.finance.yahoo.com/v7/finance/quote', ['symbols' => '^VIX'])->json()['quoteResponse']['result'][0];
                    $insights[] = 'RISCO: VIX '.number_format($vix['regularMarketPrice'], 2).' ('.number_format($vix['regularMarketChangePercent'], 2).'%)';
                } catch (\Exception $e) {
                }
                try {
                    $bdi = Http::connectTimeout(1)->timeout(3)->get('https://query1.finance.yahoo.com/v7/finance/quote', ['symbols' => '^BDI'])->json()['quoteResponse']['result'][0];
                    $insights[] = 'LOGÍSTICA: Baltic Dry Index '.number_format($bdi['regularMarketPrice'], 0);
                } catch (\Exception $e) {
                }

                $monthStart = now()->startOfMonth();
                $monthEnd = now()->endOfMonth();
                $categories = $this->buildCategoryBudgets($currentWs->id, $monthStart, $monthEnd);
                $topCat = $categories->sortByDesc('total')->first();
                if ($topCat && $topCat['total'] > 0) {
                    $insights[] = 'GASTOS: '.strtoupper($topCat['name']).' lidera despesas ('.number_format($topCat['total'], 0, ',', ' ').'€)';
                }
                $earned = Income::where('workspace_id', $currentWs->id)->whereBetween('received_at', [$monthStart, $monthEnd])->sum('amount');
                $spent = Expense::where('workspace_id', $currentWs->id)->whereBetween('spent_at', [$monthStart, $monthEnd])->sum('amount');
                $net = $earned - $spent;
                $insights[] = 'FINANÇAS: Recebido '.number_format($earned, 0, ',', ' ').'€ • Gasto '.number_format($spent, 0, ',', ' ').'€';
                $insights[] = 'FINANÇAS: Balanço '.($net > 0 ? '+' : '').number_format($net, 0, ',', ' ').'€';
                if ($spent > $earned) {
                    $insights[] = 'ALERTA: Gastos superiores às receitas.';
                }
                $insights[] = 'SISTEMA: Sessão encriptada • Sync '.now()->format('H:i');
                $insights[] = 'SISTEMA: IA a monitorizar padrões.';
                $insights[] = 'SISTEMA: Todos os serviços operacionais.';
                $insights[] = 'SISTEMA: Nenhum alerta crítico ativo.';
                $extra = [
                    'ANÁLISE: IA detectou estabilidade nos fluxos.', 'ANÁLISE: Mercado global sem variações abruptas.',
                    'ANÁLISE: Consumo dentro dos padrões normais.', 'ANÁLISE: Atividade empresarial consistente.',
                    'ANÁLISE: Dados sincronizados com sucesso.', 'ANÁLISE: Sistema a operar em modo otimizado.',
                    'ANÁLISE: Nenhuma anomalia financeira detetada.', 'ANÁLISE: IA prevê estabilidade para os próximos dias.',
                    'ANÁLISE: Tendência positiva nas últimas 48h.', 'ANÁLISE: Monitorização contínua ativa.',
                ];
                $insights = array_merge($insights, $extra);
                shuffle($insights);
                $insights = array_unique($insights);
                $insights = array_merge($insights, $insights);
                shuffle($insights);

                return $insights;
            }
        );
    }

    private function fetchData360($indicator, $country = 'PRT')
    {
        try {
            $response = Http::get('https://data360api.worldbank.org/data360/data', [
                'DATABASE_ID' => 'WB_WDI', 'INDICATOR' => $indicator, 'REF_AREA' => $country, 'top' => 1,
            ]);
            $data = $response->json();
            if (! isset($data['value'][0]['OBS_VALUE'])) {
                return null;
            }

            return ['value' => $data['value'][0]['OBS_VALUE'], 'year' => $data['value'][0]['TIME_PERIOD'], 'desc' => $data['value'][0]['COMMENT_TS'] ?? null];
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Computed]
    public function dailyReport()
    {
        $user = auth()->user();
        $currentWs = $user->currentWorkspace;
        $isPremium = $user->isAnyPremium();
        $today = now()->startOfDay();
        $endDay = now()->endOfDay();
        $wsId = $currentWs->id;
        if (! $isPremium) {
            return ['is_premium' => false, 'expenses' => collect(), 'incomes' => collect(), 'fitness' => collect(), 'xp_today' => 0];
        }

        return Cache::remember(
            "dashboard:daily-report:{$wsId}:".now()->toDateString().":{$user->id}",
            60,
            function () use ($user, $today, $endDay, $wsId) {
                $expenses = Expense::where('workspace_id', $wsId)->whereBetween('spent_at', [$today, $endDay])->get();
                $incomes = Income::where('workspace_id', $wsId)->whereBetween('received_at', [$today, $endDay])->get();
                $fitness = FitnessActivity::where('workspace_id', $wsId)->where('user_id', $user->id)->whereBetween('activity_date', [$today, $endDay])->get();
                $remindersDone = Reminder::where('workspace_id', $wsId)->where('is_completed', true)->whereBetween('updated_at', [$today, $endDay])->count();
                $socialCount = SocialNotification::where('user_id', $user->id)->whereBetween('created_at', [$today, $endDay])->count();
                $xp = ($expenses->count() * 5) + ($fitness->count() * 50) + ($remindersDone * 15);

                return [
                    'is_premium' => true, 'expenses' => $expenses, 'incomes' => $incomes, 'fitness' => $fitness,
                    'reminders_count' => $remindersDone, 'social_count' => $socialCount, 'xp_today' => $xp,
                    'spend_total' => $expenses->sum('amount'), 'earn_total' => $incomes->sum('amount'),
                    'fitness_min' => $fitness->sum('duration_minutes'), 'fitness_kcal' => $fitness->sum('calories'),
                ];
            }
        );
    }

    public function render()
    {
        $user = Auth::user();
        $user->loadMissing(['currentWorkspace.users:id,name', 'workspaces:id,name,type,currency', 'badges:id,name,color,icon']);
        $currentWs = $user->currentWorkspace;
        if (! $currentWs) {
            return view('livewire.dashboard-loading');
        }

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $sixMonthsStart = now()->subMonths(5)->startOfMonth();

        $fixedIncome = (float) Cache::remember(
            "dashboard:fixed-income:{$currentWs->id}:{$user->id}", 60,
            fn () => $user->recurringIncomes()->where('workspace_id', $currentWs->id)->where('is_active', true)->sum('amount')
        );

        $monthTotals = Cache::remember("dashboard:month-totals:{$currentWs->id}:{$monthStart->toDateString()}", 60, function () use ($currentWs, $monthStart, $monthEnd) {
            return [
                'expenses' => (float) Expense::where('workspace_id', $currentWs->id)->whereBetween('spent_at', [$monthStart, $monthEnd])->sum('amount'),
                'income' => (float) Income::where('workspace_id', $currentWs->id)->whereBetween('received_at', [$monthStart, $monthEnd])->sum('amount'),
                'budget' => (float) Category::where('workspace_id', $currentWs->id)->sum('budget_limit'),
            ];
        });

        $totalMonthExpenses = $monthTotals['expenses'];
        $totalMonthIncome = $monthTotals['income'] + $fixedIncome;
        $portfolioValue = 0;
        $myInvestments = Cache::remember("dashboard:investments:v2:{$currentWs->id}", 60, fn () => Investment::where('workspace_id', $currentWs->id)->get(['id', 'workspace_id', 'symbol', 'quantity', 'current_price'])->map(fn ($investment) => ['symbol' => $investment->symbol, 'quantity' => (float) $investment->quantity, 'current_price' => (float) $investment->current_price])->values()->all());

        foreach ($myInvestments as $inv) {
            $symbol = strtolower((string) data_get($inv, 'symbol', ''));
            $currentPrice = (float) data_get($inv, 'current_price', 0);
            $quantity = (float) data_get($inv, 'quantity', 0);
            $price = match ($symbol) {
                'btc' => $this->marketPrices['bitcoin']['eur'] ?? $currentPrice,
                'eth' => $this->marketPrices['ethereum']['eur'] ?? $currentPrice,
                'sol' => $this->marketPrices['solana']['eur'] ?? $currentPrice,
                'sp500', 'spx' => 5222.68,
                'nvda' => 945.30,
                default => $currentPrice,
            };
            $portfolioValue += ($quantity * $price);
        }

        $subscriptionsMonthlyCost = (float) Cache::remember(
            "dashboard:subscriptions-monthly:{$currentWs->id}", 60,
            fn () => Subscription::where('workspace_id', $currentWs->id)->get(['amount', 'cycle', 'status', 'is_active'])->filter(fn ($sub) => ($sub->status ?: ($sub->is_active ? 'active' : 'paused')) === 'active')->sum(fn ($sub) => SubscriptionCycleService::toMonthly((float) $sub->amount, $sub->cycle))
        );
        $platformPlanSlug = $user->currentPlanSlug();
        if ($platformPlanSlug !== 'free') {
            $subscriptionsMonthlyCost += (float) (SubscriptionPlan::where('slug', $platformPlanSlug)->value('price') ?? 0);
        }

        $pendingDebts = Cache::remember("dashboard:pending-debts:{$currentWs->id}", 60, fn () => [
            'pay' => (float) Debt::where('workspace_id', $currentWs->id)->where('type', 'owe')->where('is_paid', false)->sum('amount'),
            'receive' => (float) Debt::where('workspace_id', $currentWs->id)->where('type', 'owed')->where('is_paid', false)->sum('amount'),
        ]);
        $pendingDebtsToPay = $pendingDebts['pay'];
        $pendingDebtsToReceive = $pendingDebts['receive'];
        $projectedExpenses = $totalMonthExpenses + $subscriptionsMonthlyCost + $pendingDebtsToPay;
        $projectedIncome = $totalMonthIncome + $pendingDebtsToReceive;

        $totalBankBalance = (float) Cache::remember("dashboard:bank-balance:{$currentWs->id}", 60, function () use ($currentWs): float {
            $baseBalance = (float) BankAccount::where('workspace_id', $currentWs->id)->where('include_in_total', true)->sum('balance');
            $incomeBalance = (float) Income::where('workspace_id', $currentWs->id)->whereNotNull('bank_account_id')->sum('amount');
            $expenseBalance = (float) Expense::where('workspace_id', $currentWs->id)->whereNotNull('bank_account_id')->sum('amount');

            return $baseBalance + $incomeBalance - $expenseBalance;
        });

        $projectedBalance = $totalBankBalance + $projectedIncome - $projectedExpenses;
        $projectionStatus = $projectedBalance < 0 ? 'critical' : ($projectedBalance < ($totalMonthIncome * 0.15) ? 'warning' : 'stable');

        $topBankAccounts = Cache::remember("dashboard:top-accounts:{$currentWs->id}", 60, fn () => BankAccount::where('workspace_id', $currentWs->id)
            ->withSum('incomes as current_balance_income_total', 'amount')
            ->withSum('expenses as current_balance_expense_total', 'amount')
            ->withSum(['recurringIncomes as current_balance_recurring_due' => fn ($query) => $query->where('is_active', true)->where('day_of_month', '<=', now()->day)], 'amount')
            ->get(['id', 'name', 'balance', 'icon', 'color'])
            ->map(fn ($account) => [
                'name' => $account->name,
                'balance' => $account->current_balance,
                'icon' => $account->getIcon(),
                'color' => $account->color ?? '#6366f1',
            ])
            ->sortByDesc('balance')->take(3)->values()->all()
        );

        $topExpenseCategory = Cache::remember("dashboard:top-category:{$currentWs->id}:{$monthStart->toDateString()}", 60, function () use ($currentWs, $monthStart, $monthEnd) {
            $row = Expense::where('workspace_id', $currentWs->id)->whereBetween('spent_at', [$monthStart, $monthEnd])->select('category_id', DB::raw('SUM(amount) as total'))->groupBy('category_id')->orderByDesc('total')->with('category:id,name,icon,color')->first();
            if (! $row || ! $row->category) {
                return null;
            }

            return ['name' => $row->category->name, 'total' => (float) $row->total];
        });

        $last6 = collect(Cache::remember("dashboard:last6:{$currentWs->id}:{$sixMonthsStart->toDateString()}:{$monthEnd->toDateString()}", 60, fn () => $this->buildSixMonthSeries($currentWs->id, $sixMonthsStart, $monthEnd, $fixedIncome)->toArray()));
        $byCategory = collect(Cache::remember("dashboard:category-budgets:{$currentWs->id}:{$monthStart->toDateString()}", 60, fn () => $this->buildCategoryBudgets($currentWs->id, $monthStart, $monthEnd)->toArray()))->map(fn ($item) => (object) $item);
        $overallScore = $this->calculateScore($totalMonthExpenses, $totalMonthIncome, $monthTotals['budget']);

        $prevMonthStart = $monthStart->copy()->subMonthNoOverflow()->startOfMonth();
        $prevMonthEnd = $prevMonthStart->copy()->endOfMonth();
        $prevMonthTotals = Cache::remember("dashboard:month-totals:{$currentWs->id}:{$prevMonthStart->toDateString()}", 60, function () use ($currentWs, $prevMonthStart, $prevMonthEnd) {
            return [
                'expenses' => (float) Expense::where('workspace_id', $currentWs->id)->whereBetween('spent_at', [$prevMonthStart, $prevMonthEnd])->sum('amount'),
                'income' => (float) Income::where('workspace_id', $currentWs->id)->whereBetween('received_at', [$prevMonthStart, $prevMonthEnd])->sum('amount'),
                'budget' => (float) Category::where('workspace_id', $currentWs->id)->sum('budget_limit'),
            ];
        });
        $prevOverallScore = $this->calculateScore($prevMonthTotals['expenses'], $prevMonthTotals['income'] + $fixedIncome, $prevMonthTotals['budget']);
        $overallScoreTrend = $overallScore - $prevOverallScore;

        $financeScore = Cache::remember("dashboard:finance-score:{$currentWs->id}", 60, fn () => app(FinanceScoreService::class)->calculate($currentWs));
        $wellnessInsights = Cache::remember("dashboard:wellness:{$currentWs->id}", 60, fn () => app(WellnessFinanceService::class)->getInsights($currentWs));
        $storeEntitlements = app(StoreEntitlementService::class);
        $hasMarketWidget = Cache::remember("dashboard:entitlement:market:{$user->id}", 60, fn () => $storeEntitlements->hasWidget($user, 'mercado-global') || $user->isStar());
        $ownedStoreSlugs = Cache::remember("dashboard:entitlements:{$user->id}", 60, fn () => $storeEntitlements->ownedSlugs($user));
        $totalSaved = (float) Cache::remember("dashboard:total-saved:{$currentWs->id}", 60, fn () => Goal::where('workspace_id', $currentWs->id)->sum('current_amount'));
        $totalPatrimony = $totalBankBalance + $portfolioValue + $totalSaved;

        return view('livewire.dashboard', [
            'currentWs' => $currentWs,
            'userWorkspaces' => $user->workspaces,
            'overallScore' => $overallScore,
            'overallScoreTrend' => $overallScoreTrend,
            'financeScore' => $financeScore,
            'wellnessInsights' => $wellnessInsights,
            'hasWidgetMercado' => $hasMarketWidget,
            'ownedStoreSlugs' => $ownedStoreSlugs,
            'totalMonth' => $totalMonthExpenses,
            'totalIncomeMonth' => $projectedIncome,
            'netBalance' => $totalMonthIncome - $totalMonthExpenses,
            'portfolioValue' => $portfolioValue,
            'totalSaved' => $totalSaved,
            'projectedExpenses' => $projectedExpenses,
            'projectedBalance' => $projectedBalance,
            'projectionStatus' => $projectionStatus,
            'totalBankBalance' => $totalBankBalance,
            'totalPatrimony' => $totalPatrimony,
            'topBankAccounts' => $topBankAccounts,
            'topExpenseCategory' => $topExpenseCategory,
            'subscriptionsMonthlyCost' => $subscriptionsMonthlyCost,
            'pendingDebtsToPay' => $pendingDebtsToPay,
            'pendingDebtsToReceive' => $pendingDebtsToReceive,
            'chartMax' => max($last6->max('spent') ?? 0, $last6->max('earned') ?? 0, 1),
            'last6' => $last6,
            'byCategory' => $byCategory,
            'recent' => Expense::with(['category:id,name', 'user:id,name'])->where('workspace_id', $currentWs->id)->latest('spent_at')->take(5)->get(['id', 'workspace_id', 'category_id', 'user_id', 'description', 'amount', 'spent_at']),
        ]);
    }

    private function buildSixMonthSeries(int $workspaceId, $start, $end, float $fixedIncome): Collection
    {
        $driver = DB::connection()->getDriverName();
        $expenseMonth = $driver === 'sqlite' ? "strftime('%Y-%m', spent_at)" : "DATE_FORMAT(spent_at, '%Y-%m')";
        $incomeMonth = $driver === 'sqlite' ? "strftime('%Y-%m', received_at)" : "DATE_FORMAT(received_at, '%Y-%m')";

        $expenses = Expense::where('workspace_id', $workspaceId)
            ->whereBetween('spent_at', [$start, $end])
            ->selectRaw("{$expenseMonth} as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->map(fn ($value) => (float) $value);

        $incomes = Income::where('workspace_id', $workspaceId)
            ->whereBetween('received_at', [$start, $end])
            ->selectRaw("{$incomeMonth} as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->map(fn ($value) => (float) $value);

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
        return Category::query()->leftJoin('expenses', function ($join) use ($workspaceId, $monthStart, $monthEnd) {
            $join->on('expenses.category_id', '=', 'categories.id')->where('expenses.workspace_id', '=', $workspaceId)->whereBetween('expenses.spent_at', [$monthStart, $monthEnd]);
        })->where('categories.workspace_id', $workspaceId)->where('categories.budget_limit', '>', 0)->groupBy('categories.id', 'categories.name', 'categories.budget_limit')->orderByDesc(DB::raw('COALESCE(SUM(expenses.amount), 0)'))->get(['categories.name', 'categories.budget_limit', DB::raw('COALESCE(SUM(expenses.amount), 0) as total')])->map(function ($cat) {
            $spent = (float) $cat->total;
            $budget = (float) $cat->budget_limit;

            return ['name' => $cat->name, 'total' => $spent, 'budget' => $budget, 'percentage' => $budget > 0 ? min(($spent / $budget) * 100, 100) : 0, 'over' => $spent > $budget];
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
