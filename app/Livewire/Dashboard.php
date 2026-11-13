<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\DailyMealAllowance;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\FitnessActivity;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\Reminder;
use App\Models\RecurringIncome;
use App\Models\SalaryPayment;
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
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public bool $refreshingInvestmentPrices = false;

    public ?string $investmentRefreshMessage = null;

    public bool $investmentRefreshFailed = false;

    #[On('dashboard-record-created')]
    public function refreshAfterQuickCreate(): void
    {
        $this->forgetDashboardPeriodCaches();
    }

    private function forgetDashboardPeriodCaches(): void
    {
        $user = Auth::user();
        $workspaceId = $user->current_workspace_id;
        $userId = $user->id;
        $monthStart = Carbon::createFromFormat('Y-m', $this->dashboardSelectedMonth ?: now()->format('Y-m'))->startOfMonth()->toDateString();
        $yearStart = Carbon::create((int) ($this->dashboardSelectedYear ?: now()->year), 1, 1)->toDateString();

        Cache::forget("dashboard:period-totals:{$workspaceId}:{$userId}:month:{$monthStart}");
        Cache::forget("dashboard:period-totals:{$workspaceId}:{$userId}:year:{$yearStart}");
        Cache::forget("dashboard:subscriptions-monthly:{$workspaceId}:{$userId}");
    }

    public function refreshInvestmentPrices(): void
    {
        $user = Auth::user();
        $workspace = $user?->currentWorkspace;

        if (! $user || ! $workspace || $this->refreshingInvestmentPrices) {
            return;
        }

        $this->refreshingInvestmentPrices = true;
        $this->investmentRefreshMessage = null;
        $this->investmentRefreshFailed = false;

        try {
            $refresher = app(InvestmentsHub::class);
            $result = $refresher->refreshPrices(true);

            Cache::forget("dashboard:investments:v3:{$workspace->id}:{$user->id}");
            Cache::forget("dashboard:account-overview:v1:{$workspace->id}:{$user->id}");

            $updatedInvestments = Investment::where('workspace_id', $workspace->id)
                ->when($workspace->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                ->get(['symbol', 'name', 'type', 'quantity', 'average_price', 'current_price', 'fees'])
                ->groupBy(fn ($investment) => mb_strtolower(trim((string) ($investment->symbol ?: ($investment->name.'|'.$investment->type)))))
                ->map(function ($positions) use ($workspace) {
                    $quantity = (float) $positions->sum(fn ($position) => (float) $position->quantity);
                    $invested = (float) $positions->sum(fn ($position) => ((float) $position->quantity * (float) $position->average_price) + (float) ($position->fees ?? 0));
                    $current = (float) $positions->sum(fn ($position) => (float) $position->quantity * (float) ($position->current_price ?: $position->average_price));
                    $profit = round($current - $invested, 2);
                    $investment = $positions->first();

                    return [
                        'symbol' => $investment->symbol ?: 'Ativo',
                        'name' => $investment->name ?: $investment->symbol,
                        'profit' => $profit,
                        'profit_label' => ($profit >= 0 ? '+' : '').$workspace->money($profit),
                        'percent_label' => ($invested > 0 && $profit >= 0 ? '+' : '').number_format($invested > 0 ? ($profit / $invested) * 100 : 0, 2, ',', ' ').'%',
                    ];
                })->values()->all();

            $this->dispatch('dashboard-investments-updated', items: $updatedInvestments);
            if ($result === false) {
                $this->investmentRefreshFailed = true;
                $this->investmentRefreshMessage = 'Não foi possível concluir a atualização. Algumas cotações podem ter sido alteradas.';
                $this->dispatch('toast', variant: 'error', text: $this->investmentRefreshMessage);
            } elseif ($result['updated'] === 0 && $result['current'] === 0 && $result['partial'] === 0) {
                $this->investmentRefreshFailed = true;
                $this->investmentRefreshMessage = 'Nenhuma cotação foi atualizada. Alguns ativos não têm atualização automática ou cotação disponível.';
                $this->dispatch('toast', variant: 'error', text: $this->investmentRefreshMessage);
            } else {
                $updated = $result['updated'];
                $current = $result['current'];
                $partial = $result['partial'];
                $unavailable = $result['unavailable'];
                $this->investmentRefreshMessage = $updated.' '.($updated === 1 ? 'ativo atualizado' : 'ativos atualizados').'.';
                if ($current > 0) {
                    $this->investmentRefreshMessage .= ' '.$current.' '.($current === 1 ? 'ativo sem alterações pendentes' : 'ativos sem alterações pendentes').'.';
                }
                if ($partial > 0) {
                    $this->investmentRefreshFailed = true;
                    $this->investmentRefreshMessage .= ' '.$partial.' '.($partial === 1 ? 'ativo atualizado parcialmente' : 'ativos atualizados parcialmente').' por falta de dados.';
                }
                if ($unavailable > 0) {
                    $this->investmentRefreshFailed = true;
                    $this->investmentRefreshMessage .= ' '.$unavailable.' '.($unavailable === 1 ? 'ativo não pôde ser atualizado' : 'ativos não puderam ser atualizados').'.';
                }
                $this->dispatch('toast', variant: $this->investmentRefreshFailed ? 'error' : 'success', text: $this->investmentRefreshMessage);
            }
        } finally {
            $this->refreshingInvestmentPrices = false;
        }
    }

    public function refreshAccountOverview(): void
    {
        $user = Auth::user();
        $currentWs = $user?->currentWorkspace;

        if (! $user || ! $currentWs) {
            return;
        }

        Cache::forget("dashboard:account-overview:v1:{$currentWs->id}:{$user->id}");
        Cache::forget("dashboard:investments:v4:{$currentWs->id}:{$user->id}");

        $this->dispatch('account-overview-refreshed');
    }

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

    public string $dashboardPeriodType = 'month';

    public string $dashboardSelectedMonth = '';

    public string $dashboardSelectedYear = '';

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

    public bool $refreshingMarketPrices = false;

    public function refreshMarketPrices(): void
    {
        if ($this->refreshingMarketPrices) {
            return;
        }

        $this->refreshingMarketPrices = true;

        try {
            $prices = $this->fetchMarketPrices();

            if (empty($prices)) {
                $this->dispatch('toast', variant: 'error', text: 'Não foi possível atualizar os preços. Tenta novamente dentro de momentos.');

                return;
            }

            $this->marketPrices = $prices;
            Cache::forever('market_prices_all', $prices);
            $this->dispatch('toast', text: 'Todos os preços de mercado foram atualizados.');
        } finally {
            $this->refreshingMarketPrices = false;
        }
    }

    private function fetchMarketPrices(): array
    {
        $result = [];
            try {
                $responses = Http::pool(function (Pool $pool) {
                    return [
                        'crypto' => $pool->as('crypto')->connectTimeout(3)->timeout(6)->get('https://api.coingecko.com/api/v3/simple/price', [
                            'ids' => 'bitcoin,ethereum,solana,binancecoin,ripple,cardano,avalanche-2,polkadot,chainlink,dogecoin,matic-network,uniswap',
                            'vs_currencies' => 'eur',
                            'include_24hr_change' => 'true',
                        ]),
                        'stocks' => $pool->as('stocks')->connectTimeout(1)->timeout(2)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get('https://query1.finance.yahoo.com/v7/finance/quote', [
                            'symbols' => 'NVDA,AAPL,MSFT,AMZN,GOOGL,META,TSLA,NFLX,AMD,TSM,SPY,QQQ,VTI,VOO,IUSA.L,CSPX.L,VWCE.DE,GC=F,CL=F',
                            'lang' => 'en-US',
                        ]),
                    ];
                });

                $crypto = $responses['crypto'];
                if ($crypto->successful()) {
                    $data = $crypto->json();
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

                $stocks = $responses['stocks'];
                if ($stocks->successful()) {
                    $quotes = $stocks->json()['quoteResponse']['result'] ?? [];
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
            } catch (\Throwable $e) {
                Log::debug('Falha ao atualizar preços de mercado no dashboard: '.$e->getMessage());
            }
        return $result;
    }

    public function mount()
    {
        $user = Auth::user();
        if (request()->query('checkout') === 'success') {
            $user->refresh();
            $sessionId = (string) request()->query('session_id', '');

            if ($sessionId !== '') {
                try {
                    $stripeSession = $user->stripe()->checkout->sessions->retrieve($sessionId);
                    if (($stripeSession->payment_status ?? null) === 'paid'
                        && (string) ($stripeSession->client_reference_id ?? '') === (string) $user->id
                    ) {
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

        $this->marketPrices = Cache::get('market_prices_all', []);

        if (! $user->workspaces()->exists()) {
            $ws = Workspace::create([
                'name' => 'Gestão de '.explode(' ', $user->name)[0],
                'type' => 'personal',
                'owner_id' => $user->id,
                'invite_code' => strtoupper(Str::random(8)),
            ]);
            $user->workspaces()->attach($ws->id, ['role' => 'admin']);
            $user->forceFill(['current_workspace_id' => $ws->id])->save();
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
            $user->forceFill(['current_workspace_id' => $user->workspaces()->first()->id])->save();
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
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        abort_unless($workspace && $user->workspaces()->whereKey($workspace->id)->exists(), 403);

        $role = $workspace->owner_id === $user->id
            ? 'owner'
            : (string) $workspace->users()->whereKey($user->id)->first()?->pivot?->role;

        abort_unless(in_array(strtolower($role), ['owner', 'admin'], true), 403);

        $key = 'workspace-invite:'.$user->id.':'.$workspace->id;
        abort_unless(RateLimiter::attempt($key, 5, fn () => true, 300), 429);

        $workspace->update(['invite_code' => strtoupper(Str::random(8))]);
        $this->dispatch('toast', text: 'Novo código de convite gerado!');
    }

    public function requestPrivacyUnlock()
    {
        $this->showPrivacyModal = true;
    }

    public function joinWorkspace()
    {
        $this->validate(['inviteCodeInput' => 'required|string|size:8']);
        $key = 'workspace-join:'.Auth::id().':'.request()->ip();
        abort_unless(RateLimiter::attempt($key, 10, fn () => true, 600), 429);

        $workspace = Workspace::where('invite_code', strtoupper(trim($this->inviteCodeInput)))->firstOrFail();
        if ($workspace->users()->where('user_id', Auth::id())->exists()) {
            $this->dispatch('toast', variant: 'error', text: 'Já fazes parte desta conta.');

            return;
        }
        Auth::user()->workspaces()->attach($workspace->id, ['role' => 'member']);
        Auth::user()->forceFill(['current_workspace_id' => $workspace->id])->save();

        return redirect()->route('dashboard');
    }

    public function switchWorkspace($id)
    {
        $user = Auth::user();
        $workspace = $user->workspaces()->find($id);
        if ($workspace) {
            $user->forceFill(['current_workspace_id' => $id])->save();
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
        $isPersonalWorkspace = $currentWs?->type === 'personal';
        if (! $currentWs) {
            return [];
        }

        return Cache::flexible(
            "dashboard:ai-insights:{$currentWs->id}:{$user->id}:".now()->format('Y-m'),
            [300, 1800],
            function () use ($currentWs, $user) {
                $insights = [];
            try {
                $responses = Http::pool(function (Pool $pool) {
                    return [
                        'market' => $pool->as('market')->connectTimeout(1)->timeout(4)->get(
                            'https://query1.finance.yahoo.com/v7/finance/quote',
                            ['symbols' => '^GSPC,^IXIC,^GDAXI,^FCHI,^FTSE,GC=F,SI=F,PL=F,PA=F,CL=F,NG=F,CO1.F,^VIX,^BDI']
                        ),
                        'inflation' => $pool->as('inflation')->connectTimeout(1)->timeout(2)->get(
                            'https://api.worldbank.org/v2/country/EU/indicator/FP.CPI.TOTL.ZG?format=json'
                        ),
                        'unemployment' => $pool->as('unemployment')->connectTimeout(1)->timeout(2)->get(
                            'https://api.worldbank.org/v2/country/PRT/indicator/SL.UEM.TOTL.ZS?format=json'
                        ),
                        'fx' => $pool->as('fx')->connectTimeout(1)->timeout(1)->get(
                            'https://api.exchangerate.host/latest?base=EUR'
                        ),
                        'weather' => $pool->as('weather')->connectTimeout(1)->timeout(2)->get(
                            'https://api.open-meteo.com/v1/forecast?latitude=38.7&longitude=-9.1&current_weather=true'
                        ),
                    ];
                });

                $marketQuotes = $responses['market']->json()['quoteResponse']['result'] ?? [];
                $indexSymbols = ['^GSPC', '^IXIC', '^GDAXI', '^FCHI', '^FTSE'];
                $metalSymbols = ['GC=F', 'SI=F', 'PL=F', 'PA=F'];
                $energySymbols = ['CL=F', 'NG=F', 'CO1.F'];

                foreach ($marketQuotes as $quote) {
                    $symbol = $quote['symbol'] ?? '';
                    if (in_array($symbol, $indexSymbols, true)) {
                        $insights[] = "ÍNDICE: ".($quote['shortName'] ?? $symbol).' '.number_format($quote['regularMarketPrice'] ?? 0, 2).' ('.number_format($quote['regularMarketChangePercent'] ?? 0, 2).'%)';
                    } elseif (in_array($symbol, $metalSymbols, true)) {
                        $insights[] = "METAIS: {$symbol} ".number_format($quote['regularMarketPrice'] ?? 0, 2).' ('.number_format($quote['regularMarketChangePercent'] ?? 0, 2).'%)';
                    } elseif (in_array($symbol, $energySymbols, true)) {
                        $insights[] = "ENERGIA: {$symbol} ".number_format($quote['regularMarketPrice'] ?? 0, 2).' ('.number_format($quote['regularMarketChangePercent'] ?? 0, 2).'%)';
                    }
                }
                $inflation = $responses['inflation']->json();
                if (isset($inflation[1][0]['value'])) $insights[] = 'MACRO: Inflação UE '.number_format($inflation[1][0]['value'], 1).'%';
                $unemployment = $responses['unemployment']->json();
                if (isset($unemployment[1][0]['value'])) $insights[] = 'MACRO: Desemprego PT '.number_format($unemployment[1][0]['value'], 1).'%';
                $fx = $responses['fx']->json();
                if (isset($fx['rates'])) {
                    $insights[] = 'FX: EUR/JPY '.number_format($fx['rates']['JPY'] ?? 0, 2);
                    $insights[] = 'FX: EUR/CHF '.number_format($fx['rates']['CHF'] ?? 0, 3);
                    $insights[] = 'FX: EUR/AUD '.number_format($fx['rates']['AUD'] ?? 0, 3);
                    $insights[] = 'FX: EUR/CAD '.number_format($fx['rates']['CAD'] ?? 0, 3);
                }
                $weather = $responses['weather']->json();
                if (isset($weather['current_weather'])) $insights[] = "CLIMA: Lisboa {$weather['current_weather']['temperature']}ºC • Vento {$weather['current_weather']['windspeed']}km/h";
                $vix = collect($marketQuotes)->firstWhere('symbol', '^VIX');
                if ($vix) $insights[] = 'RISCO: VIX '.number_format($vix['regularMarketPrice'], 2).' ('.number_format($vix['regularMarketChangePercent'], 2).'%)';
                $bdi = collect($marketQuotes)->firstWhere('symbol', '^BDI');
                if ($bdi) $insights[] = 'LOGÍSTICA: Baltic Dry Index '.number_format($bdi['regularMarketPrice'], 0);
            } catch (\Throwable $e) {
                Log::debug('Falha ao obter dados externos dos insights do dashboard: '.$e->getMessage());
            }

                $monthStart = now()->startOfMonth();
                $monthEnd = now()->endOfMonth();
                $categories = $this->buildCategoryBudgets($currentWs->id, $monthStart, $monthEnd);
                $topCat = $categories->sortByDesc('total')->first();
                if ($topCat && $topCat['total'] > 0) {
                    $insights[] = 'GASTOS: '.strtoupper($topCat['name']).' lidera despesas ('.number_format($topCat['total'], 0, ',', ' ').'€)';
                }
                $earned = Income::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereBetween('received_at', [$periodStart, $periodEnd])->sum('amount');
                $spent = Expense::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereBetween('spent_at', [$periodStart, $periodEnd])->sum('amount');
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

    #[Computed]
    public function news24h(): array
    {
        $user = auth()->user();
        $workspaceId = $user?->current_workspace_id ?? 'none';

        return Cache::flexible("dashboard:news-24h:{$workspaceId}", [300, 900], function () {
            $feeds = [
                'PT' => 'https://news.google.com/rss/search?q=not%C3%ADcias+Portugal+when%3A1d&hl=pt-PT&gl=PT&ceid=PT%3Apt-PT',
                'INT' => 'https://news.google.com/rss/search?q=not%C3%ADcias+internacionais+when%3A1d&hl=pt-PT&gl=PT&ceid=PT%3Apt-PT',
            ];

            $articles = [];

            try {
                $responses = Http::pool(function (Pool $pool) use ($feeds) {
                    return [
                        'PT' => $pool->as('PT')->connectTimeout(3)->timeout(7)->withHeaders(['User-Agent' => 'FinanceProNews/1.0'])->get($feeds['PT']),
                        'INT' => $pool->as('INT')->connectTimeout(3)->timeout(7)->withHeaders(['User-Agent' => 'FinanceProNews/1.0'])->get($feeds['INT']),
                    ];
                });

                foreach (['PT', 'INT'] as $region) {
                    $response = $responses[$region] ?? null;
                    if (! $response || ! $response->successful()) {
                        continue;
                    }

                    $xml = @simplexml_load_string($response->body());
                    if (! $xml || ! isset($xml->channel->item)) {
                        continue;
                    }

                    foreach ($xml->channel->item as $item) {
                        try {
                            $publishedAt = \Illuminate\Support\Carbon::parse((string) $item->pubDate);
                        } catch (\Throwable $e) {
                            continue;
                        }

                        if ($publishedAt->lt(now()->subDay()) || $publishedAt->gt(now()->addMinutes(10))) {
                            continue;
                        }

                        $articles[] = [
                            'id' => $region.'-'.count($articles),
                            'region' => $region,
                            'title' => trim((string) $item->title),
                            'source' => trim((string) ($item->source ?? '')),
                            'url' => trim((string) $item->link),
                            'published_at' => $publishedAt->toIso8601String(),
                            'published_label' => $publishedAt->diffForHumans(),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Falha ao obter notícias das últimas 24 horas: '.$e->getMessage());
            }

            return collect($articles)
                ->filter(fn ($article) => $article['title'] !== '' && $article['url'] !== '')
                ->unique('url')
                ->sortByDesc('published_at')
                ->take(20)
                ->values()
                ->all();
        });
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
                $expenses = Expense::where('workspace_id', $wsId)->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereBetween('spent_at', [$today, $endDay])->get();
                $incomes = Income::where('workspace_id', $wsId)->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereBetween('received_at', [$today, $endDay])->get();
                $fitness = FitnessActivity::where('workspace_id', $wsId)->where('user_id', $user->id)->whereBetween('activity_date', [$today, $endDay])->get();
                $remindersDone = Reminder::where('workspace_id', $wsId)->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->where('is_completed', true)->whereBetween('updated_at', [$today, $endDay])->count();
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

        $this->dashboardSelectedMonth = $this->dashboardSelectedMonth ?: now()->format('Y-m');
        $this->dashboardSelectedYear = $this->dashboardSelectedYear ?: now()->format('Y');

        $selectedDate = $this->dashboardPeriodType === 'year'
            ? now()->setDate((int) $this->dashboardSelectedYear, 1, 1)
            : now()->createFromFormat('Y-m', $this->dashboardSelectedMonth)->startOfMonth();

        $monthStart = $selectedDate->copy()->startOfMonth();
        $monthEnd = $selectedDate->copy()->endOfMonth();
        $periodStart = $this->dashboardPeriodType === 'year'
            ? $selectedDate->copy()->startOfYear()
            : $monthStart->copy();
        $periodEnd = $this->dashboardPeriodType === 'year'
            ? $selectedDate->copy()->endOfYear()
            : $monthEnd->copy();
        $isCurrentDashboardPeriod = $periodStart->isSameDay(now()->startOfMonth())
            && $this->dashboardPeriodType === 'month';
        $sixMonthsStart = $periodEnd->copy()->subMonths(5)->startOfMonth();

        $fixedIncome = (float) Cache::remember(
            "dashboard:fixed-income:{$currentWs->id}:{$user->id}", 60,
            fn () => $user->recurringIncomes()->where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->where('is_active', true)->sum('amount')
        );

        $monthTotals = Cache::remember("dashboard:period-totals:{$currentWs->id}:{$user->id}:{$this->dashboardPeriodType}:{$periodStart->toDateString()}", 60, function () use ($currentWs, $periodStart, $periodEnd, $user) {
            return [
                'expenses' => (float) Expense::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereBetween('spent_at', [$periodStart, $periodEnd])->sum('amount'),
                'income' => (float) Income::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereBetween('received_at', [$periodStart, $periodEnd])->sum('amount'),
                'budget' => (float) Category::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->sum('budget_limit'),
            ];
        });

        $totalMonthExpenses = $monthTotals['expenses'];
        $totalMonthIncome = $monthTotals['income'] + ($isCurrentDashboardPeriod ? $fixedIncome : 0);
        // Resumo de investimentos: usa as mesmas cotações guardadas no portefólio,
        // sem preços fixos ou valores de mercado inventados na Dashboard.
        $myInvestments = Cache::remember("dashboard:investments:v3:{$currentWs->id}:{$user->id}", 60, fn () => Investment::where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->get(['id', 'workspace_id', 'symbol', 'name', 'type', 'quantity', 'average_price', 'current_price', 'fees', 'updated_at'])
            ->groupBy(fn ($investment) => mb_strtolower(trim((string) ($investment->symbol ?: ($investment->name.'|'.$investment->type)))))
            ->map(function ($positions) {
                $quantity = (float) $positions->sum(fn ($position) => (float) $position->quantity);
                $cost = (float) $positions->sum(fn ($position) => (float) $position->quantity * (float) $position->average_price);
                $latest = $positions->sortByDesc(fn ($position) => $position->updated_at?->timestamp ?? 0)->first();

                return [
                    'symbol' => $latest->symbol,
                    'name' => $latest->name,
                    'type' => $latest->type,
                    'quantity' => $quantity,
                    'average_price' => $quantity > 0 ? $cost / $quantity : 0,
                    'current_price' => (float) ($latest->current_price ?: $latest->average_price),
                    'fees' => (float) $positions->sum(fn ($position) => (float) ($position->fees ?? 0)),
                    'updated_at' => $positions->max(fn ($position) => $position->updated_at?->timestamp),
                ];
            })->values()->all());

        $portfolioValue = round(collect($myInvestments)->sum(fn (array $investment): float => $investment['quantity'] * $investment['current_price']), 2);
        $dashboardInvestmentsTotalInvested = round(collect($myInvestments)->sum(fn (array $investment): float => ($investment['quantity'] * $investment['average_price']) + $investment['fees']), 2);
        $dashboardInvestmentsProfit = round($portfolioValue - $dashboardInvestmentsTotalInvested, 2);
        $dashboardInvestmentsProfitPercent = $dashboardInvestmentsTotalInvested > 0
            ? round(($dashboardInvestmentsProfit / $dashboardInvestmentsTotalInvested) * 100, 2)
            : 0;
        $dashboardInvestmentsCount = count($myInvestments);
        $dashboardInvestmentsLastUpdatedTimestamp = collect($myInvestments)->max('updated_at');
        $dashboardInvestmentsLastUpdated = $dashboardInvestmentsLastUpdatedTimestamp
            ? now()->setTimestamp($dashboardInvestmentsLastUpdatedTimestamp)->format('d/m/Y · H:i')
            : null;

        $subscriptionsMonthlyCost = (float) Cache::remember(
            "dashboard:subscriptions-monthly:{$currentWs->id}:{$user->id}", 60,
            fn () => Subscription::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->get(['amount', 'cycle', 'status', 'is_active', 'paid_by_company'])->filter(fn ($sub) => ! $sub->paid_by_company && ($sub->status ?: ($sub->is_active ? 'active' : 'paused')) === 'active')->sum(fn ($sub) => SubscriptionCycleService::toMonthly((float) $sub->amount, $sub->cycle))
        );
        $platformPlanSlug = $user->currentPlanSlug();
        if ($platformPlanSlug !== 'free') {
            $subscriptionsMonthlyCost += (float) (SubscriptionPlan::where('slug', $platformPlanSlug)->value('price') ?? 0);
        }

        $pendingDebts = Cache::remember("dashboard:pending-debts:{$currentWs->id}:{$user->id}", 60, fn () => [
            'pay' => (float) Debt::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->where('type', 'owe')->where('is_paid', false)->sum('amount'),
            'receive' => (float) Debt::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->where('type', 'owed')->where('is_paid', false)->sum('amount'),
        ]);
        $pendingDebtsToPay = $pendingDebts['pay'];
        $pendingDebtsToReceive = $pendingDebts['receive'];

        // A projeção parte do saldo bancário atual. Só considera contas marcadas
        // para integrar o património e movimentos futuros ainda não liquidados.
        $includedBankAccountIds = BankAccount::where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->where('include_in_total', true)
            ->pluck('id')
            ->all();

        $totalBankBalance = (float) Cache::remember(
            "dashboard:bank-balance:{$currentWs->id}:{$user->id}",
            60,
            function () use ($currentWs, $user, $includedBankAccountIds): float {
                $baseBalance = (float) BankAccount::where('workspace_id', $currentWs->id)
                    ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                    ->whereIn('id', $includedBankAccountIds)
                    ->sum('balance');

                $incomeBalance = (float) Income::where('workspace_id', $currentWs->id)
                    ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                    ->whereIn('bank_account_id', $includedBankAccountIds)
                    ->sum(DB::raw('COALESCE(amount_converted, amount)'));

                $expenseBalance = (float) Expense::where('workspace_id', $currentWs->id)
                    ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                    ->whereIn('bank_account_id', $includedBankAccountIds)
                    ->sum(DB::raw('COALESCE(amount_converted, amount)'));

                return round($baseBalance + $incomeBalance - $expenseBalance, 2);
            }
        );

        $today = now()->startOfDay();
        $forecastMonthEnd = now()->endOfMonth()->startOfDay();

        // Salários deste mês ainda por receber, associados a uma conta incluída
        // no saldo global. Os pagamentos já marcados como pagos já estão no saldo.
        $pendingSalaryPayments = SalaryPayment::with('recurringIncome')
            ->where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->where('payment_year', $today->year)
            ->where('payment_month', $today->month)
            ->where('status', '!=', 'paid')
            ->whereNotNull('bank_account_id')
            ->whereIn('bank_account_id', $includedBankAccountIds)
            ->get();

        $pendingSalaryIncome = (float) $pendingSalaryPayments->sum(function (SalaryPayment $payment): float {
            $mealCard = ($payment->recurringIncome?->metadata['meal_allowance_destination'] ?? null) === 'meal_card';

            return max(0, (float) $payment->net_received - ($mealCard ? (float) $payment->meal_allowance : 0));
        });

        // Se a automação ainda não criou o registo salarial deste mês, prevê o
        // valor contratual uma única vez, desde que não exista já receita ligada.
        $salaryContracts = RecurringIncome::where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->where('source', 'emprego')
            ->where('is_active', true)
            ->whereNotNull('bank_account_id')
            ->whereIn('bank_account_id', $includedBankAccountIds)
            ->get();

        $salaryContractIds = $salaryContracts->modelKeys();
        $salaryPaymentsThisMonth = SalaryPayment::where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->where('payment_year', $today->year)
            ->where('payment_month', $today->month)
            ->pluck('recurring_income_id')
            ->all();

        $salaryIncomeAlreadyRegistered = Income::where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->whereIn('salary_recurring_income_id', $salaryContractIds)
            ->whereBetween('received_at', [$monthStart, $monthEnd])
            ->pluck('salary_recurring_income_id')
            ->all();

        $unregisteredSalaryIncome = (float) $salaryContracts
            ->reject(function (RecurringIncome $salary) use ($salaryPaymentsThisMonth, $salaryIncomeAlreadyRegistered, $today): bool {
                $metadata = $salary->metadata ?? [];
                $startDate = $metadata['start_date'] ?? null;
                $endDate = $metadata['end_date'] ?? null;

                return ($metadata['income_type'] ?? null) === 'meal_card_allowance'
                    || ($metadata['contract_status'] ?? 'active') !== 'active'
                    || in_array($salary->id, $salaryPaymentsThisMonth, true)
                    || in_array($salary->id, $salaryIncomeAlreadyRegistered, true)
                    || ($startDate && $today->toDateString() < $startDate)
                    || ($endDate && $today->toDateString() > $endDate);
            })
            ->sum(fn (RecurringIncome $salary): float => (float) $salary->amount);

        // Dívidas com vencimento até ao fim do mês e ainda sem movimento associado.
        $futureDebts = Debt::where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->where('is_paid', false)
            ->whereBetween('due_at', [$today->toDateString(), $forecastMonthEnd->toDateString()])
            ->whereNotNull('bank_account_id')
            ->whereIn('bank_account_id', $includedBankAccountIds)
            ->whereNull('expense_id')
            ->whereNull('income_id')
            ->get(['type', 'amount']);

        $futureDebtPayments = (float) $futureDebts->where('type', 'owe')->sum('amount');
        $futureDebtReceipts = (float) $futureDebts->where('type', 'owed')->sum('amount');

        $projectedIncome = round($pendingSalaryIncome + $unregisteredSalaryIncome + $futureDebtReceipts, 2);
        $projectedExpenses = round($futureDebtPayments, 2);
        $projectedBalance = round($totalBankBalance + $projectedIncome - $projectedExpenses, 2);

        $daysRemaining = max(1, (int) $today->diffInDays($forecastMonthEnd) + 1);
        $projectionDailyAvailable = round($projectedBalance / $daysRemaining, 2);
        $projectionStatus = $projectedBalance < 0
            ? 'critical'
            : ($projectedBalance < max(0, $totalBankBalance * 0.10) ? 'warning' : 'stable');

        $topBankAccounts = Cache::remember("dashboard:top-accounts:{$currentWs->id}:{$user->id}", 60, fn () => BankAccount::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->withCurrentBalanceTotals()
            ->get(['id', 'name', 'balance', 'icon', 'color'])
            ->map(fn ($account) => [
                'name' => $account->name,
                'balance' => $account->current_balance,
                'icon' => $account->getIcon(),
                'color' => $account->color ?? '#6366f1',
            ])
            ->sortByDesc('balance')->take(3)->values()->all()
        );

        $topExpenseCategory = Cache::remember("dashboard:top-category:{$currentWs->id}:{$user->id}:{$periodStart->toDateString()}:{$periodEnd->toDateString()}", 60, function () use ($currentWs, $periodStart, $periodEnd, $user) {
            $row = Expense::where('workspace_id', $currentWs->id)
                ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                ->whereBetween('spent_at', [$periodStart, $periodEnd])
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->with('category:id,name,icon,color')
                ->first();
            if (! $row || ! $row->category) {
                return null;
            }

            return ['name' => $row->category->name, 'total' => (float) $row->total];
        });

        $last6 = collect(Cache::remember("dashboard:last6:{$currentWs->id}:{$user->id}:{$sixMonthsStart->toDateString()}:{$monthEnd->toDateString()}", 60, fn () => $this->buildSixMonthSeries($currentWs->id, $sixMonthsStart, $periodEnd, $isCurrentDashboardPeriod ? $fixedIncome : 0)->toArray()));
        $byCategory = collect(Cache::remember("dashboard:category-budgets:{$currentWs->id}:{$user->id}:{$periodStart->toDateString()}:{$periodEnd->toDateString()}", 60, fn () => $this->buildCategoryBudgets($currentWs->id, $periodStart, $periodEnd)->toArray()))->map(fn ($item) => (object) $item);
        $overallScore = $this->calculateScore($totalMonthExpenses, $totalMonthIncome, $monthTotals['budget']);

        $prevMonthStart = $periodStart->copy()->subDay()->startOfMonth();
        $prevMonthEnd = $prevMonthStart->copy()->endOfMonth();
        $prevMonthTotals = Cache::remember("dashboard:month-totals:{$currentWs->id}:{$user->id}:{$prevMonthStart->toDateString()}", 60, function () use ($currentWs, $prevMonthStart, $prevMonthEnd, $user) {
            return [
                'expenses' => (float) Expense::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereBetween('spent_at', [$prevMonthStart, $prevMonthEnd])->sum('amount'),
                'income' => (float) Income::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereBetween('received_at', [$prevMonthStart, $prevMonthEnd])->sum('amount'),
                'budget' => (float) Category::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->sum('budget_limit'),
            ];
        });
        $prevOverallScore = $this->calculateScore($prevMonthTotals['expenses'], $prevMonthTotals['income'] + ($isCurrentDashboardPeriod ? $fixedIncome : 0), $prevMonthTotals['budget']);
        $overallScoreTrend = $overallScore - $prevOverallScore;

        // Indicadores adicionais: comparam sempre períodos equivalentes.
        $comparisonPeriodStart = $this->dashboardPeriodType === 'year'
            ? $periodStart->copy()->subYear()->startOfYear()
            : $periodStart->copy()->subMonth()->startOfMonth();
        $comparisonPeriodEnd = $this->dashboardPeriodType === 'year'
            ? $comparisonPeriodStart->copy()->endOfYear()
            : $comparisonPeriodStart->copy()->endOfMonth();

        $comparisonTotals = Cache::remember(
            "dashboard:indicator-comparison:{$currentWs->id}:{$user->id}:{$this->dashboardPeriodType}:{$comparisonPeriodStart->toDateString()}",
            60,
            function () use ($currentWs, $comparisonPeriodStart, $comparisonPeriodEnd, $user): array {
                $scope = fn ($query) => $query->where('workspace_id', $currentWs->id)
                    ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id));

                return [
                    'expenses' => (float) $scope(Expense::query())->whereBetween('spent_at', [$comparisonPeriodStart, $comparisonPeriodEnd])->sum('amount'),
                    'income' => (float) $scope(Income::query())->whereBetween('received_at', [$comparisonPeriodStart, $comparisonPeriodEnd])->sum('amount'),
                ];
            }
        );

        $indicatorIncome = (float) $totalMonthIncome;
        $indicatorSavingsRate = $indicatorIncome > 0
            ? round((($indicatorIncome - $totalMonthExpenses) / $indicatorIncome) * 100, 1)
            : null;
        $indicatorBudgetUsage = (float) $monthTotals['budget'] > 0
            ? round(($totalMonthExpenses / (float) $monthTotals['budget']) * 100, 1)
            : null;

        $indicatorPeriodEnd = $periodEnd->lessThan(now()->endOfDay()) ? $periodEnd->copy() : now()->endOfDay();
        $indicatorDaysElapsed = $periodStart->greaterThan($indicatorPeriodEnd)
            ? 0
            : max(1, (int) $periodStart->diffInDays($indicatorPeriodEnd) + 1);
        $indicatorDailyAverage = $indicatorDaysElapsed > 0
            ? round($totalMonthExpenses / $indicatorDaysElapsed, 2)
            : 0.0;

        $comparisonIncome = (float) $comparisonTotals['income'] + ($isCurrentDashboardPeriod ? $fixedIncome : 0);
        $indicatorExpenseChange = (float) $comparisonTotals['expenses'] > 0
            ? round((($totalMonthExpenses - (float) $comparisonTotals['expenses']) / (float) $comparisonTotals['expenses']) * 100, 1)
            : null;
        $indicatorIncomeChange = $comparisonIncome > 0
            ? round((($indicatorIncome - $comparisonIncome) / $comparisonIncome) * 100, 1)
            : null;

        $financeScore = Cache::remember("dashboard:finance-score:{$currentWs->id}:{$user->id}", 60, fn () => app(FinanceScoreService::class)->calculate($currentWs));
        $wellnessInsights = Cache::remember("dashboard:wellness:{$currentWs->id}:{$user->id}", 60, fn () => app(WellnessFinanceService::class)->getInsights($currentWs));
        $storeEntitlements = app(StoreEntitlementService::class);
        $hasMarketWidget = Cache::remember("dashboard:entitlement:market:{$user->id}", 60, fn () => $storeEntitlements->hasWidget($user, 'mercado-global') || $user->isStar());
        $ownedStoreSlugs = Cache::remember("dashboard:entitlements:{$user->id}", 60, fn () => $storeEntitlements->ownedSlugs($user));
        $totalSaved = (float) Cache::remember("dashboard:total-saved:{$currentWs->id}:{$user->id}", 60, fn () => Goal::where('workspace_id', $currentWs->id)->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->sum('current_amount'));
        $totalPatrimony = $totalBankBalance + $portfolioValue + $totalSaved;

        // Inventário completo dos registos introduzidos nesta conta.
        $accountOverview = Cache::remember("dashboard:account-overview:v1:{$currentWs->id}:{$user->id}", 60, function () use ($currentWs, $user, $myInvestments, $dashboardInvestmentsTotalInvested, $portfolioValue, $dashboardInvestmentsProfit): array {
            $scope = fn ($query) => $query->where('workspace_id', $currentWs->id)
                ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id));

            $expenses = $scope(Expense::query());
            $incomes = $scope(Income::query());
            $goals = $scope(Goal::query());
            $debts = $scope(Debt::query());
            $subscriptions = $scope(Subscription::query());
            $recurring = $scope(RecurringIncome::query());
            $reminders = $scope(Reminder::query());
            $activities = $scope(FitnessActivity::query());
            $accounts = $scope(BankAccount::query())->withCurrentBalanceTotals()->get();

            $subscriptionRows = $subscriptions->get(['amount', 'cycle', 'status', 'is_active', 'paid_by_company']);
            $activeSubscriptions = $subscriptionRows->filter(fn ($item) => ($item->status ?: ($item->is_active ? 'active' : 'paused')) === 'active');
            $personalSubscriptionsMonthly = $activeSubscriptions->where('paid_by_company', false)->sum(fn ($item) => SubscriptionCycleService::toMonthly((float) $item->amount, $item->cycle));
            $companySubscriptionsMonthly = $activeSubscriptions->where('paid_by_company', true)->sum(fn ($item) => SubscriptionCycleService::toMonthly((float) $item->amount, $item->cycle));

            return [
                'income_count' => (clone $incomes)->count(),
                'income_total' => (float) (clone $incomes)->sum('amount'),
                'expense_count' => (clone $expenses)->count(),
                'expense_total' => (float) (clone $expenses)->sum('amount'),
                'account_count' => $accounts->count(),
                'accounts' => $accounts->map(fn ($account) => [
                    'name' => $account->name,
                    'bank' => $account->bank_name,
                    'type' => $account->type,
                    'currency' => $account->currency ?: 'EUR',
                    'balance' => (float) $account->current_balance,
                    'base_balance' => (float) $account->balance,
                    'included' => (bool) $account->include_in_total,
                ])->values()->all(),
                'investment_count' => count($myInvestments),
                'investment_invested' => (float) $dashboardInvestmentsTotalInvested,
                'investment_value' => (float) $portfolioValue,
                'investment_profit' => (float) $dashboardInvestmentsProfit,
                'goal_count' => (clone $goals)->count(),
                'goal_target' => (float) (clone $goals)->sum('target_amount'),
                'goal_saved' => (float) (clone $goals)->sum('current_amount'),
                'debt_count' => (clone $debts)->where('is_paid', false)->count(),
                'debt_pay' => (float) (clone $debts)->where('type', 'owe')->where('is_paid', false)->sum('amount'),
                'debt_receive' => (float) (clone $debts)->where('type', 'owed')->where('is_paid', false)->sum('amount'),
                'subscription_count' => $activeSubscriptions->count(),
                'subscription_personal_monthly' => (float) $personalSubscriptionsMonthly,
                'subscription_company_monthly' => (float) $companySubscriptionsMonthly,
                'recurring_count' => (clone $recurring)->where('is_active', true)->count(),
                'recurring_total' => (float) (clone $recurring)->where('is_active', true)->sum('amount'),
                'reminder_open' => (clone $reminders)->where('is_completed', false)->count(),
                'reminder_done' => (clone $reminders)->where('is_completed', true)->count(),
                'activity_count' => (clone $activities)->count(),
                'activity_km' => (float) (clone $activities)->sum('distance_km'),
                'activity_minutes' => (int) (clone $activities)->sum('duration_minutes'),
                'category_count' => $scope(Category::query())->count(),
                'budget_total' => (float) $scope(Category::query())->sum('budget_limit'),
            ];
        });

        $dashboardSalaryContracts = RecurringIncome::where('workspace_id', $currentWs->id)
            ->where('user_id', $user->id)
            ->where('source', 'emprego')
            ->get()
            ->reject(fn (RecurringIncome $income) => ($income->metadata['income_type'] ?? null) === 'meal_card_allowance')
            ->filter(fn (RecurringIncome $income) => ($income->metadata['contract_status'] ?? ($income->is_active ? 'active' : 'suspended')) === 'active')
            ->values();
        $dashboardMealCardSalaryContracts = $dashboardSalaryContracts
            ->filter(fn (RecurringIncome $income): bool => ($income->metadata['meal_allowance_destination'] ?? null) === 'meal_card')
            ->values();
        $mealCardIncomeIds = $dashboardMealCardSalaryContracts
            ->map(fn (RecurringIncome $income): ?int => ! empty($income->metadata['meal_card_income_id'])
                ? (int) $income->metadata['meal_card_income_id']
                : null)
            ->filter()
            ->unique()
            ->values();
        $mealCardIncomeCreatedAt = $mealCardIncomeIds->isEmpty()
            ? collect()
            : RecurringIncome::where('workspace_id', $currentWs->id)
                ->where('user_id', $user->id)
                ->whereIn('id', $mealCardIncomeIds)
                ->pluck('created_at', 'id');
        $dashboardMealCardReferenceDate = $dashboardMealCardSalaryContracts
            ->map(function (RecurringIncome $income) use ($mealCardIncomeCreatedAt): ?string {
                $mealCardIncomeId = (int) ($income->metadata['meal_card_income_id'] ?? 0);
                $createdAt = $mealCardIncomeId ? $mealCardIncomeCreatedAt->get($mealCardIncomeId) : null;

                return $createdAt
                    ? Carbon::parse($createdAt)->toDateString()
                    : $income->created_at?->toDateString();
            })
            ->filter()
            ->sort()
            ->first();

        $dashboardProjectedSalaryNet = round($dashboardSalaryContracts->sum(function (RecurringIncome $income): float {
            $metadata = $income->metadata ?? [];
            $mealAllowance = (float) ($metadata['calculated_sa'] ?? 0);

            return max(0, (float) $income->amount - (empty($metadata['meal_card_income_id']) ? $mealAllowance : 0));
        }), 2);
        // O cartão de alimentação respeita as datas de início e fim do contrato
        // que originou o subsídio, usando o período atualmente selecionado no dashboard.
        $dashboardMealAllowanceContracts = $dashboardMealCardSalaryContracts->filter(function (RecurringIncome $income) use ($periodStart, $periodEnd): bool {
            $metadata = $income->metadata ?? [];
            $startDate = $metadata['start_date'] ?? $income->created_at?->toDateString();
            $endDate = $metadata['end_date'] ?? null;

            if ($startDate && Carbon::parse($startDate)->startOfDay()->greaterThan($periodEnd)) {
                return false;
            }

            if ($endDate && Carbon::parse($endDate)->endOfDay()->lessThan($periodStart)) {
                return false;
            }

            return true;
        })->values();

        $dashboardMealAllowanceMonthlyProjection = round($dashboardMealAllowanceContracts
            ->sum(fn (RecurringIncome $income): float => (float) ($income->metadata['calculated_sa'] ?? 0)), 2);
        $dashboardProjectedMealAllowance = $this->dashboardPeriodType === 'year' ? 0.0 : $dashboardMealAllowanceMonthlyProjection;

        // Projeção anual limitada aos meses em que o contrato esteve em vigor (máximo de 11 meses).
        $dashboardMealAllowanceAnnualProjection = round($dashboardMealCardSalaryContracts
            ->sum(function (RecurringIncome $income) use ($periodStart, $periodEnd): float {
            $metadata = $income->metadata ?? [];
            $contractStart = Carbon::parse($metadata['start_date'] ?? $income->created_at?->toDateString() ?? $periodStart->toDateString())->startOfMonth();
            $contractEnd = ! empty($metadata['end_date'])
                ? Carbon::parse($metadata['end_date'])->endOfMonth()
                : $periodEnd->copy()->endOfYear();
            $rangeStart = $contractStart->greaterThan($periodStart->copy()->startOfYear())
                ? $contractStart
                : $periodStart->copy()->startOfYear();
            $rangeEnd = $contractEnd->lessThan($periodEnd->copy()->endOfYear())
                ? $contractEnd
                : $periodEnd->copy()->endOfYear();

            if ($rangeEnd->lessThan($rangeStart)) {
                return 0.0;
            }

            $activeMonths = (int) $rangeStart->copy()->startOfMonth()->diffInMonths($rangeEnd->copy()->startOfMonth()) + 1;
            $projectedMonths = min(11, $activeMonths);

            return (float) ($metadata['calculated_sa'] ?? 0) * $projectedMonths;
        }), 2);

        // Cartão bancário do dashboard: saldo real da conta + salário líquido projetado.
        // O saldo real exclui rendimentos recorrentes ainda não recebidos, para não duplicar o salário.
        $dashboardBankCardAccount = BankAccount::where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->where('include_in_total', true)
            ->orderByRaw("CASE WHEN LOWER(COALESCE(bank_name, '')) LIKE ? THEN 0 ELSE 1 END", ['%santander%'])
            ->orderBy('name')
            ->first(['id', 'name', 'bank_name', 'iban', 'account_number', 'swift', 'holder_name', 'balance', 'color', 'opened_at', 'created_at']);

        if ($dashboardBankCardAccount) {
            // O saldo introduzido no registo é o saldo de referência na data de abertura/registo.
            // Só somamos movimentos ocorridos a partir dessa data, evitando contar movimentos anteriores.
            // O saldo base é o valor introduzido quando a conta foi registada no Finance Pro.
            // Não usar opened_at: essa data pode ser anterior à introdução do saldo.
            $bankBalanceReferenceDate = $dashboardBankCardAccount->created_at?->toDateString();
            $bankBalancePeriodEnd = $isCurrentDashboardPeriod ? now()->endOfDay() : $periodEnd->copy()->endOfDay();

            if ($bankBalanceReferenceDate && Carbon::parse($bankBalanceReferenceDate)->startOfDay()->greaterThan($bankBalancePeriodEnd)) {
                $dashboardBankCardCurrentBalance = 0.0;
            } else {
                $accountIncomeUntilPeriod = (float) Income::where('workspace_id', $currentWs->id)
                    ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                    ->where('bank_account_id', $dashboardBankCardAccount->id)
                    ->when($bankBalanceReferenceDate, fn ($q) => $q->whereDate('received_at', '>=', $bankBalanceReferenceDate))
                    ->where('received_at', '<=', $bankBalancePeriodEnd)
                    ->sum(DB::raw('COALESCE(amount_converted, amount)'));
                $accountExpensesUntilPeriod = (float) Expense::where('workspace_id', $currentWs->id)
                    ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                    ->where('bank_account_id', $dashboardBankCardAccount->id)
                    ->when($bankBalanceReferenceDate, fn ($q) => $q->whereDate('spent_at', '>', $bankBalanceReferenceDate))
                    ->where('spent_at', '<=', $bankBalancePeriodEnd)
                    ->sum(DB::raw('COALESCE(amount_converted, amount)'));

                $dashboardBankCardCurrentBalance = round(
                    (float) $dashboardBankCardAccount->balance + $accountIncomeUntilPeriod - $accountExpensesUntilPeriod,
                    2
                );
            }

            $dashboardBankCardSalaryAmount = $isCurrentDashboardPeriod ? $dashboardProjectedSalaryNet : 0.0;
            $dashboardBankCardBalance = round($dashboardBankCardCurrentBalance + $dashboardBankCardSalaryAmount, 2);
        } else {
            $dashboardBankCardCurrentBalance = $totalBankBalance;
            $dashboardBankCardSalaryAmount = $isCurrentDashboardPeriod ? $dashboardProjectedSalaryNet : 0.0;
            $dashboardBankCardBalance = round($dashboardBankCardCurrentBalance + $dashboardBankCardSalaryAmount, 2);
        }

        // Movimentos apresentados no separador Histórico do cartão bancário.
        $dashboardBankCardIncomes = $dashboardBankCardAccount
            ? Income::where('workspace_id', $currentWs->id)
                ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                ->where('bank_account_id', $dashboardBankCardAccount->id)
                ->whereBetween('received_at', [$periodStart, $periodEnd])
                ->orderByDesc('received_at')
                ->limit(30)
                ->get()
            : collect();

        $dashboardBankCardExpenses = $dashboardBankCardAccount
            ? Expense::where('workspace_id', $currentWs->id)
                ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                ->where('bank_account_id', $dashboardBankCardAccount->id)
                ->when($dashboardBankCardAccount->created_at, fn ($q, $createdAt) => $q->whereDate('spent_at', '>', $createdAt->toDateString()))
                ->whereBetween('spent_at', [$periodStart, $periodEnd])
                ->with('category')
                ->orderByDesc('spent_at')
                ->limit(30)
                ->get()
            : collect();

        $dashboardSalaryContracts->each(function (RecurringIncome $income): void {
            $metadata = $income->metadata ?? [];
            $mealAllowance = (float) ($metadata['calculated_sa'] ?? 0);
            $income->setAttribute(
                'projected_salary_net',
                round(max(0, (float) $income->amount - (empty($metadata['meal_card_income_id']) ? $mealAllowance : 0)), 2),
            );
        });

        $dashboardSalaryContractIds = $dashboardSalaryContracts->modelKeys();
        $projectedSalaryHistoryPayments = SalaryPayment::with(['recurringIncome', 'bankAccount'])
            ->where('workspace_id', $currentWs->id)
            ->where('user_id', $user->id)
            ->whereIn('recurring_income_id', $dashboardSalaryContractIds)
            ->when($this->dashboardPeriodType === 'year', fn ($q) => $q->where('payment_year', (int) $this->dashboardSelectedYear))
            ->when($this->dashboardPeriodType === 'month', function ($q): void {
                [$selectedYear, $selectedMonth] = array_map('intval', explode('-', $this->dashboardSelectedMonth));
                $q->where('payment_year', $selectedYear)->where('payment_month', $selectedMonth);
            })
            ->orderByDesc('payment_year')
            ->orderByDesc('payment_month')
            ->orderByDesc('paid_at')
            ->get();

        // Valores efetivamente registados no cartão para o mês/ano selecionado.
        $dashboardMealCardPayments = $projectedSalaryHistoryPayments->filter(function (SalaryPayment $payment): bool {
            $metadata = $payment->recurringIncome?->metadata ?? [];
            if (($metadata['meal_allowance_destination'] ?? null) !== 'meal_card') {
                return false;
            }

            $paymentDate = Carbon::create((int) $payment->payment_year, (int) $payment->payment_month, 1);
            $startDate = $metadata['start_date'] ?? $payment->recurringIncome?->created_at?->toDateString();
            $endDate = $metadata['end_date'] ?? null;

            return (! $startDate || Carbon::parse($startDate)->startOfMonth()->lessThanOrEqualTo($paymentDate))
                && (! $endDate || Carbon::parse($endDate)->endOfMonth()->greaterThanOrEqualTo($paymentDate));
        });

        if ($dashboardMealCardPayments->isNotEmpty()) {
            $dashboardProjectedMealAllowance = round((float) $dashboardMealCardPayments->sum('meal_allowance'), 2);

            if ($this->dashboardPeriodType === 'year') {
                $dashboardMealAllowanceAnnualProjection = $dashboardProjectedMealAllowance;
            }
        } elseif ($this->dashboardPeriodType === 'year') {
            $dashboardProjectedMealAllowance = $dashboardMealAllowanceAnnualProjection;
        }

        $mealAllowanceAdjustment = $dashboardMealCardSalaryContracts->isEmpty()
            ? 0.0
            : (float) DailyMealAllowance::where('workspace_id', $currentWs->id)
                ->where('user_id', $user->id)
                ->whereIn('recurring_income_id', $dashboardMealCardSalaryContracts->modelKeys())
                ->whereBetween('allowance_date', [$periodStart, $periodEnd])
                ->sum(DB::raw('received_amount - expected_amount'));
        $dashboardProjectedMealAllowance = round($dashboardProjectedMealAllowance + $mealAllowanceAdjustment, 2);

        $mealCardExpenseQuery = Expense::where('workspace_id', $currentWs->id)
            ->where('user_id', $user->id)
            ->where('metadata->payment_source', 'meal_card')
            ->whereBetween('spent_at', [$periodStart, $periodEnd]);
        if ($dashboardMealCardReferenceDate) {
            $mealCardExpenseQuery->whereDate('spent_at', '>', $dashboardMealCardReferenceDate);
        } else {
            $mealCardExpenseQuery->whereRaw('1 = 0');
        }
        $dashboardMealCardExpensesTotal = round((float) (clone $mealCardExpenseQuery)
            ->sum(DB::raw('COALESCE(amount_converted, amount)')), 2);
        $dashboardMealCardBalance = round($dashboardProjectedMealAllowance - $dashboardMealCardExpensesTotal, 2);
        $dashboardMealCardExpenses = (clone $mealCardExpenseQuery)
            ->with('category')
            ->orderByDesc('spent_at')
            ->orderByDesc('id')
            ->limit(30)
            ->get();
        $dashboardMealCardHistory = $dashboardMealCardPayments
            ->map(fn (SalaryPayment $payment): array => [
                'type' => 'payment',
                'date' => $payment->paid_at ?? Carbon::create((int) $payment->payment_year, (int) $payment->payment_month, 1),
                'title' => $payment->recurringIncome?->description ?? 'Subsídio de alimentação',
                'subtitle' => 'Período: '.str_pad((string) $payment->payment_month, 2, '0', STR_PAD_LEFT).'/'.$payment->payment_year
                    .' · '.($payment->paid_at?->format('d/m/Y') ?? 'Sem data de pagamento'),
                'amount' => (float) $payment->meal_allowance,
                'status' => $payment->status,
            ])
            ->concat($dashboardMealCardExpenses->map(fn (Expense $expense): array => [
                'type' => 'expense',
                'date' => $expense->spent_at,
                'title' => $expense->description ?: ($expense->category?->name ?? 'Despesa no cartão de refeição'),
                'subtitle' => $expense->spent_at?->format('d/m/Y').' · '.($expense->category?->name ?? 'Alimentação'),
                'amount' => (float) ($expense->amount_converted ?? $expense->amount),
                'status' => null,
                'category_slug' => $expense->category?->slug ?: Str::slug($expense->category?->name ?? 'Alimentação'),
                'expense_id' => $expense->id,
            ]))
            ->sortByDesc(fn (array $movement): int => $movement['date']->getTimestamp())
            ->values();

        $projectedSalaryHistoryExtras = Income::with('bankAccount')
            ->where('workspace_id', $currentWs->id)
            ->where('user_id', $user->id)
            ->whereIn('salary_recurring_income_id', $dashboardSalaryContractIds)
            ->latest('received_at')
            ->get();

        $dashboardSalaryLabels = $dashboardSalaryContracts->pluck('description')->filter()->implode(' · ');
        $accountOverview['salary_contract_count'] = $dashboardSalaryContracts->count();
        $accountOverview['salary_net_projected'] = $dashboardProjectedSalaryNet;
        $accountOverview['meal_allowance_projected'] = $dashboardMealAllowanceMonthlyProjection;
        $accountOverview['meal_allowance_annual'] = $dashboardMealAllowanceAnnualProjection;
        $accountOverview['salary_payments_count'] = $projectedSalaryHistoryPayments->count();

        // Histórico unificado da dashboard: usa os mesmos tipos e critérios da página Movimentos.
        $movementScope = fn ($query) => $query->where('workspace_id', $currentWs->id)
            ->when($currentWs->type === 'personal', fn ($q) => $q->where('user_id', $user->id));

        $recentMovements = collect();

        $recentMovements = $recentMovements->concat($movementScope(Expense::query()->with('category'))
            ->get()->map(fn ($item) => [
                'key' => 'expense-'.$item->id,
                'type' => 'expense',
                'type_label' => 'Despesa',
                'title' => $item->title ?: $item->description ?: 'Despesa',
                'detail' => $item->category?->name ?: 'Sem categoria',
                'date' => $item->spent_at,
                'amount' => (float) ((float) $item->amount_paid > 0 ? $item->amount_paid : $item->amount),
                'direction' => 'out',
                'status' => 'Completo',
            ]));

        $recentMovements = $recentMovements->concat($movementScope(Income::query()->with('category'))
            ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
            ->get()->map(fn ($item) => [
                'key' => 'income-'.$item->id,
                'type' => 'income',
                'type_label' => 'Receita',
                'title' => $item->description ?: 'Receita',
                'detail' => $item->category?->name ?: ($item->source ?: 'Receita'),
                'date' => $item->received_at,
                'amount' => (float) $item->amount,
                'direction' => 'in',
                'status' => 'Completo',
            ]));

        $recentMovements = $recentMovements->concat($movementScope(\App\Models\BankTransfer::query())
            ->get()->map(fn ($item) => [
                'key' => 'transfer-'.$item->id,
                'type' => 'transfer',
                'type_label' => 'Transferência',
                'title' => $item->description ?: ($item->recipient ?: 'Transferência bancária'),
                'detail' => 'Transferência bancária',
                'date' => $item->transferred_at,
                'amount' => (float) $item->amount,
                'direction' => ($item->direction ?? 'out') === 'in' ? 'in' : 'out',
                'status' => 'Completo',
            ]));

        $recentMovements = $recentMovements->concat($movementScope(Subscription::query())
            ->get()->map(fn ($item) => [
                'key' => 'subscription-'.$item->id,
                'type' => 'subscription',
                'type_label' => 'Assinatura',
                'title' => $item->name ?: 'Assinatura',
                'detail' => ($item->paid_by_company ? 'Pago pela empresa · ' : '').'Periodicidade: '.match (strtolower((string) $item->cycle)) { 'monthly' => 'Mensal', 'yearly', 'annual' => 'Anual', 'weekly' => 'Semanal', 'quarterly' => 'Trimestral', 'semiannual' => 'Semestral', default => ($item->cycle ?: 'Recorrente') },
                'date' => $item->renewal_date ?: $item->started_at,
                'amount' => (float) $item->amount,
                'direction' => $item->paid_by_company ? 'company' : 'out',
                'status' => $item->paid_by_company ? 'Empresa paga esta assinatura' : 'Completo',
            ]));

        $recentMovements = $recentMovements->concat($movementScope(\App\Models\SubscriptionPayment::query()->with('subscription'))
            ->get()->map(fn ($item) => [
                'key' => 'subscription-payment-'.$item->id,
                'type' => 'subscription',
                'type_label' => 'Mensalidade',
                'title' => $item->subscription?->name ?: 'Assinatura',
                'detail' => 'Mensalidade paga pela empresa',
                'date' => $item->paid_at,
                'amount' => (float) $item->amount,
                'direction' => 'company',
                'status' => 'Completo',
            ]));

        $recentMovements = $recentMovements
            ->sortByDesc(fn ($item) => $item['date'] ? Carbon::parse($item['date'])->getTimestamp() : 0)
            ->values();

        return view('livewire.dashboard', [
            'currentWs' => $currentWs,
            'dashboardProjectedSalaryNet' => $dashboardProjectedSalaryNet,
            'dashboardPeriodType' => $this->dashboardPeriodType,
            'dashboardSelectedMonth' => $this->dashboardSelectedMonth,
            'dashboardSelectedYear' => $this->dashboardSelectedYear,
            'dashboardPeriodLabel' => $this->dashboardPeriodType === 'year'
                ? $periodStart->translatedFormat('Y')
                : ($isCurrentDashboardPeriod ? 'Mês atual' : $periodStart->translatedFormat('F Y')),

            'dashboardBankCardAccount' => $dashboardBankCardAccount,
            'dashboardBankCardCurrentBalance' => $dashboardBankCardCurrentBalance,
            'dashboardBankCardBalance' => $dashboardBankCardBalance,
            'dashboardBankCardSalaryAmount' => $dashboardBankCardSalaryAmount,
            'dashboardBankCardIncomes' => $dashboardBankCardIncomes,
            'dashboardBankCardExpenses' => $dashboardBankCardExpenses,
            'isCurrentDashboardPeriod' => $isCurrentDashboardPeriod,
            'dashboardProjectedMealAllowance' => $dashboardProjectedMealAllowance,
            'mealAllowanceAdjustment' => $mealAllowanceAdjustment,
            'dashboardMealCardBalance' => $dashboardMealCardBalance,
            'dashboardMealCardExpensesTotal' => $dashboardMealCardExpensesTotal,
            'dashboardMealCardHistory' => $dashboardMealCardHistory,
            'dashboardMealAllowanceAnnualProjection' => $dashboardMealAllowanceAnnualProjection,
            'dashboardMealAllowanceMonthlyProjection' => $dashboardMealAllowanceMonthlyProjection,
            'dashboardSalaryLabels' => $dashboardSalaryLabels,
            'dashboardSalaryContracts' => $dashboardSalaryContracts,
            'projectedSalaryHistoryPayments' => $projectedSalaryHistoryPayments,
            'projectedSalaryHistoryExtras' => $projectedSalaryHistoryExtras,
            'userWorkspaces' => $user->workspaces,
            'overallScore' => $overallScore,
            'overallScoreTrend' => $overallScoreTrend,
            'indicatorSavingsRate' => $indicatorSavingsRate,
            'indicatorBudgetUsage' => $indicatorBudgetUsage,
            'indicatorDailyAverage' => $indicatorDailyAverage,
            'indicatorExpenseChange' => $indicatorExpenseChange,
            'indicatorIncomeChange' => $indicatorIncomeChange,
            'indicatorDaysElapsed' => $indicatorDaysElapsed,
            'financeScore' => $financeScore,
            'wellnessInsights' => $wellnessInsights,
            'hasWidgetMercado' => $hasMarketWidget,
            'ownedStoreSlugs' => $ownedStoreSlugs,
            'totalMonth' => $totalMonthExpenses,
            'totalIncomeMonth' => $projectedIncome,
            'netBalance' => $totalMonthIncome - $totalMonthExpenses,
            'portfolioValue' => $portfolioValue,
            'dashboardInvestmentsTotalInvested' => $dashboardInvestmentsTotalInvested,
            'dashboardInvestmentsProfit' => $dashboardInvestmentsProfit,
            'dashboardInvestmentsProfitPercent' => $dashboardInvestmentsProfitPercent,
            'dashboardInvestmentsCount' => $dashboardInvestmentsCount,
            'dashboardInvestmentsLastUpdated' => $dashboardInvestmentsLastUpdated,
            'dashboardInvestments' => $myInvestments,
            'totalSaved' => $totalSaved,
            'projectedExpenses' => $projectedExpenses,
            'projectedIncome' => $projectedIncome,
            'projectionDailyAvailable' => $projectionDailyAvailable,
            'projectedBalance' => $projectedBalance,
            'projectionStatus' => $projectionStatus,
            'totalBankBalance' => $totalBankBalance,
            'totalPatrimony' => $totalPatrimony,
            'accountOverview' => $accountOverview,
            'topBankAccounts' => $topBankAccounts,
            'topExpenseCategory' => $topExpenseCategory,
            'subscriptionsMonthlyCost' => $subscriptionsMonthlyCost,
            'pendingDebtsToPay' => $pendingDebtsToPay,
            'pendingDebtsToReceive' => $pendingDebtsToReceive,
            'chartMax' => max($last6->max('spent') ?? 0, $last6->max('earned') ?? 0, 1),
            'last6' => $last6,
            'byCategory' => $byCategory,
            'recentMovements' => $recentMovements,
        ]);
    }

    private function buildSixMonthSeries(int $workspaceId, $start, $end, float $fixedIncome): Collection
    {
        $user = auth()->user();
        $workspace = $user?->currentWorkspace;
        $isPersonal = $workspace?->id === $workspaceId && $workspace?->type === 'personal';

        $driver = DB::connection()->getDriverName();
        $expenseMonth = $driver === 'sqlite' ? "strftime('%Y-%m', spent_at)" : "DATE_FORMAT(spent_at, '%Y-%m')";
        $incomeMonth = $driver === 'sqlite' ? "strftime('%Y-%m', received_at)" : "DATE_FORMAT(received_at, '%Y-%m')";

        $expenses = Expense::where('workspace_id', $workspaceId)
            ->when($isPersonal, fn ($q) => $q->where('user_id', $user->id))
            ->whereBetween('spent_at', [$start, $end])
            ->selectRaw("{$expenseMonth} as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->map(fn ($value) => (float) $value);

        $incomes = Income::where('workspace_id', $workspaceId)
            ->when($isPersonal, fn ($q) => $q->where('user_id', $user->id))
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
        $user = auth()->user();
        $isPersonal = $user?->currentWorkspace?->id === $workspaceId && $user->currentWorkspace?->type === 'personal';
        return Category::query()->when($isPersonal, fn ($q) => $q->where('categories.user_id', $user->id))->leftJoin('expenses', function ($join) use ($workspaceId, $monthStart, $monthEnd, $isPersonal, $user) {
            $join->on('expenses.category_id', '=', 'categories.id')->where('expenses.workspace_id', '=', $workspaceId)->when($isPersonal, fn ($q) => $q->where('expenses.user_id', $user->id))->whereBetween('expenses.spent_at', [$monthStart, $monthEnd]);
        })->where('categories.workspace_id', $workspaceId)->when($isPersonal, fn ($q) => $q->where('categories.user_id', $user->id))->where('categories.budget_limit', '>', 0)->groupBy('categories.id', 'categories.name', 'categories.budget_limit')->orderByDesc(DB::raw('COALESCE(SUM(expenses.amount), 0)'))->get(['categories.name', 'categories.budget_limit', DB::raw('COALESCE(SUM(expenses.amount), 0) as total')])->map(function ($cat) {
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
