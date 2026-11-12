<div class="dashboard-page space-y-8 pb-10">
    {{-- CSS dentro da root div para evitar o erro de Multiple Root Elements --}}
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* Scroll suave da página quando a dashboard está presente. */
        html:has(.dashboard-page) {
            scroll-behavior: smooth;
        }
        .dashboard-page {
            scroll-behavior: smooth;
        }
        .dashboard-page [id^="dashboard-"] {
            scroll-margin-top: 7rem;
        }
        @media (prefers-reduced-motion: reduce) {
            html:has(.dashboard-page),
            .dashboard-page {
                scroll-behavior: auto;
            }
        }

        /* Cartões bancários: proporção física 85,6 × 54 mm e rotação manual suave */
        .dashboard-card-flipper {
            transform-style: preserve-3d;
            transition: transform 1.35s cubic-bezier(0.22, 0.61, 0.36, 1) !important;
            will-change: transform;
        }
        .dashboard-card-flipper.is-flipped { transform: rotateY(180deg); }
        .dashboard-card-face {
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }

        /* Movimento subtil e contínuo para os cartões da dashboard */
        .dashboard-card-surface {
            isolation: isolate;
            transition: filter .6s ease, box-shadow .6s ease;
        }
        .dashboard-card-surface::after {
            content: "";
            position: absolute;
            inset: -45% auto -45% -65%;
            width: 42%;
            pointer-events: none;
            z-index: 1;
            transform: skewX(-20deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.16), transparent);
            animation: dashboardCardShine 8s ease-in-out infinite;
        }
        .dashboard-card-surface > .relative {
            z-index: 2;
        }
        .dashboard-card-flipper > div:first-child > .pointer-events-none:first-child {
            animation: dashboardCardFloat 9s ease-in-out infinite alternate;
        }
        .dashboard-card-flipper > div:nth-child(2) > .pointer-events-none:first-child {
            animation: dashboardCardFloat 11s ease-in-out infinite alternate-reverse;
        }
        @keyframes dashboardCardShine {
            0%, 58% { left: -65%; opacity: 0; }
            66% { opacity: 1; }
            88%, 100% { left: 125%; opacity: 0; }
        }
        @keyframes dashboardCardFloat {
            from { transform: translate3d(0, 0, 0) scale(1); }
            to { transform: translate3d(-10px, 8px, 0) scale(1.08); }
        }
        .dashboard-card-shell {
            transition: transform .65s cubic-bezier(.2,.8,.2,1), filter .65s ease;
        }
        .dashboard-card-shell:hover {
            transform: translateY(-5px);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.18));
        }
        @media (prefers-reduced-motion: reduce) {
            .dashboard-card-surface::after,
            .dashboard-card-flipper > div:first-child > .pointer-events-none:first-child,
            .dashboard-card-flipper > div:nth-child(2) > .pointer-events-none:first-child {
                animation: none !important;
            }
            .dashboard-card-shell { transition: none !important; }
        }
        @media (prefers-reduced-motion: reduce) {
            .dashboard-card-flipper { transition-duration: 0.01ms !important; }
        }

        /* Garante que os modais (não-flyout) desta página ficam sempre centrados no ecrã */
        .dashboard-page [data-flux-modal] > dialog:not([data-flux-flyout]) {
            position: fixed !important;
            inset: 0 !important;
            margin: auto !important;
        }
    </style>

    @php
        $firstName = explode(' ', auth()->user()->name)[0] ?? auth()->user()->name;
        $currentWs = auth()->user()->currentWorkspace;
        $others = $currentWs
            ? $currentWs->users->where('id', '!=', auth()->id())->pluck('name')->map(fn($name) => explode(' ', $name)[0])
            : collect();
        $sharedText = $others->count() > 0 ? "Partilhada com " . $others->implode(', ') : "Conta Individual";

        $tickers = [
            // Os mais conhecidos primeiro (visíveis sem scroll)
            'BTC'   => ['price' => 104850, 'change' => 1.2],
            'ETH'   => ['price' => 3920,   'change' => 2.4],
            'NVDA'  => ['price' => 1280,   'change' => 4.2],
            'AAPL'  => ['price' => 224,    'change' => 0.3],
            'SPY'   => ['price' => 548,    'change' => 0.5],
            'GOLD'  => ['price' => 3320,   'change' => 0.3],
            // Resto das cryptos
            'SOL'   => ['price' => 178,    'change' => 3.8],
            'BNB'   => ['price' => 712,    'change' => 0.9],
            'XRP'   => ['price' => 2.45,   'change' => -1.1],
            'ADA'   => ['price' => 0.62,   'change' => 1.5],
            'AVAX'  => ['price' => 38,     'change' => 2.7],
            'DOT'   => ['price' => 6.80,   'change' => -0.5],
            'LINK'  => ['price' => 18.20,  'change' => 1.8],
            'DOGE'  => ['price' => 0.19,   'change' => -2.3],
            'MATIC' => ['price' => 0.52,   'change' => 1.1],
            'UNI'   => ['price' => 8.90,   'change' => 0.6],
            // Resto das ações
            'MSFT'  => ['price' => 475,    'change' => 0.7],
            'AMZN'  => ['price' => 210,    'change' => 1.6],
            'GOOGL' => ['price' => 185,    'change' => 1.1],
            'META'  => ['price' => 620,    'change' => 2.0],
            'TSLA'  => ['price' => 248,    'change' => -1.4],
            'NFLX'  => ['price' => 920,    'change' => 0.8],
            'AMD'   => ['price' => 162,    'change' => 3.5],
            'TSM'   => ['price' => 195,    'change' => 1.2],
            // ETFs
            'QQQ'   => ['price' => 478,    'change' => 0.9],
            'VTI'   => ['price' => 265,    'change' => 0.4],
            'VOO'   => ['price' => 502,    'change' => 0.5],
            'IUSA'  => ['price' => 48,     'change' => 0.6],
            'CSPX'  => ['price' => 545,    'change' => 0.5],
            'VWCE'  => ['price' => 128,    'change' => 0.4],
            // Commodities
            'OIL'   => ['price' => 78,     'change' => -0.7],
        ];

        // Substituir pelos preços reais da API (quando disponíveis)
        foreach ($marketPrices as $symbol => $data) {
            $tickers[$symbol] = $data;
        }

        // Nunca apresentar preços de crypto antigos/fictícios quando a API não respondeu.
        // As cryptos devem vir exclusivamente dos dados atuais da CoinGecko.
        foreach (['BTC', 'ETH', 'SOL', 'BNB', 'XRP', 'ADA', 'AVAX', 'DOT', 'LINK', 'DOGE', 'MATIC', 'UNI'] as $cryptoSymbol) {
            if (! isset($marketPrices[$cryptoSymbol])) {
                unset($tickers[$cryptoSymbol]);
            }
        }
    @endphp

    {{-- 1. TICKER DE MERCADO ULTRA COMPACTO --}}
    <div x-data="{
            scrollNext() { $refs.tickerContainer.scrollBy({ left: 250, behavior: 'smooth' }) },
            scrollPrev() { $refs.tickerContainer.scrollBy({ left: -250, behavior: 'smooth' }) }
         }"
         class="relative group px-1">

        {{-- Setas de navegação (Apenas PC) --}}
        <button x-on:click="scrollPrev" class="absolute -left-1 top-1/2 -translate-y-1/2 z-20 size-8 flex items-center justify-center bg-white/90 dark:bg-zinc-800/90 border border-zinc-200 dark:border-zinc-700 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex">
            <flux:icon name="chevron-left" class="size-3 text-zinc-600 dark:text-zinc-300" />
        </button>

        {{-- Contentor do Ticker --}}
        <div x-ref="tickerContainer" class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-2 flex-nowrap touch-pan-x snap-x">
            @foreach($tickers as $symbol => $data)
                <div class="shrink-0 flex items-center gap-3 px-4 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 rounded-2xl shadow-sm hover:border-emerald-500/40 hover:shadow-md transition-all snap-start">
                    <div class="size-1.5 rounded-full {{ $data['change'] >= 0 ? 'bg-emerald-500 shadow-[0_0_6px_#10b981]' : 'bg-red-500 shadow-[0_0_6px_#ef4444]' }}"></div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-wider leading-none">{{ $symbol }}</span>
                        <span class="text-sm font-black dark:text-white tabular-nums leading-tight mt-0.5">{{ number_format($data['price'], $data['price'] < 10 ? 3 : ($data['price'] < 100 ? 2 : 0), ',', ' ') }}€</span>
                    </div>
                    <span class="text-[10px] font-bold {{ $data['change'] >= 0 ? 'text-emerald-500' : 'text-red-500' }} ml-1">
                        {{ $data['change'] >= 0 ? '▲' : '▼' }}{{ abs(round($data['change'], 1)) }}%
                    </span>
                </div>
                @if($loop->iteration === (int) ceil(count($tickers) / 2))
                    <button
                        type="button"
                        wire:click="refreshMarketPrices"
                        wire:loading.attr="disabled"
                        wire:target="refreshMarketPrices"
                        title="Atualizar cotações"
                        aria-label="Atualizar cotações"
                        class="shrink-0 flex size-10 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 shadow-sm transition-all duration-200 hover:scale-110 hover:bg-emerald-100 hover:shadow-md disabled:cursor-wait disabled:opacity-50 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-400 dark:hover:bg-emerald-400/20">
                        <flux:icon name="arrow-path" class="size-4" wire:loading.remove wire:target="refreshMarketPrices" />
                        <flux:icon name="arrow-path" class="size-4 animate-spin" wire:loading wire:target="refreshMarketPrices" />
                    </button>
                @endif
            @endforeach
        </div>

        <button x-on:click="scrollNext" class="absolute -right-1 top-1/2 -translate-y-1/2 z-20 size-8 flex items-center justify-center bg-white/90 dark:bg-zinc-800/90 border border-zinc-200 dark:border-zinc-700 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex">
            <flux:icon name="chevron-right" class="size-3 text-zinc-600 dark:text-zinc-300" />
        </button>
    </div>



{{-- NOTÍCIAS 24H --}}
<div
    x-data="{ speed: 300 }"
    class="news24h-card relative flex items-center gap-5 w-full min-h-[54px] py-1.5 px-4 rounded-2xl border border-sky-400/20 bg-gradient-to-r from-zinc-950 via-slate-950 to-zinc-950 backdrop-blur-2xl shadow-xl shadow-sky-950/20 group overflow-hidden select-none transition-all duration-300 hover:-translate-y-1 hover:border-sky-300/50 hover:shadow-2xl hover:shadow-sky-900/30">

    <div class="absolute inset-0 pointer-events-none opacity-25 bg-gradient-to-r from-sky-600/40 via-indigo-500/20 to-cyan-400/10 animate-bgFlow"></div>
    <div class="news24h-shine absolute inset-y-0 -left-1/2 w-1/3 pointer-events-none"></div>
    <div class="absolute top-0 left-8 right-8 h-px bg-gradient-to-r from-transparent via-sky-300/70 to-transparent"></div>
    <div class="absolute -right-10 -top-14 size-36 rounded-full bg-sky-500/10 blur-3xl pointer-events-none"></div>

    <a href="{{ route('hub.news') }}" wire:navigate title="Clica para abrir a página dedicada de notícias" aria-label="Abrir Notícias 24h" class="relative flex items-center gap-3 shrink-0 z-20 px-3.5 py-2 rounded-xl border-2 border-sky-300/60 bg-gradient-to-br from-sky-500/35 via-blue-500/25 to-indigo-500/35 shadow-lg shadow-sky-500/20 cursor-pointer transition-all duration-200 hover:scale-[1.04] hover:border-sky-200 hover:from-sky-400/45 hover:to-indigo-400/40 hover:shadow-xl hover:shadow-sky-400/30 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300">
        <div class="relative flex items-center justify-center">
            <div class="size-3 rounded-full bg-sky-300 animate-pulse shadow-[0_0_12px_rgba(125,211,252,0.9)]"></div>
            <div class="absolute inset-0 size-3 rounded-full bg-sky-300/40 blur-md animate-ping"></div>
        </div>
        <div class="flex flex-col gap-0.5">
            <span class="text-sm font-black uppercase text-white tracking-[0.16em] leading-none drop-shadow-[0_0_8px_rgba(56,189,248,0.55)]">Notícias 24h</span>
            <span class="text-[9px] font-bold uppercase tracking-[0.16em] text-sky-200">Ver notícias</span>
        </div>
        <span class="ml-1 px-2 py-1 rounded-md bg-sky-300/15 border border-sky-300/25 text-[9px] font-black tracking-wider text-sky-200">LIVE</span>
    </a>

    <div class="relative flex-1 overflow-hidden h-8 z-10">
        <div class="ticker-ultra flex items-center gap-20 whitespace-nowrap" :style="`animation-duration: ${speed}s`">
            @forelse($this->news24h as $news)
                <div class="ticker-text-ultra flex items-center gap-3">
                    <span class="text-[10px] font-black tracking-widest {{ $news['region'] === 'PT' ? 'text-emerald-400' : 'text-sky-400' }}">
                        {{ $news['region'] === 'PT' ? 'PORTUGAL' : 'MUNDO' }}
                    </span>
                    <a href="{{ $news['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="mono-item normal-case tracking-normal hover:text-sky-300 transition-colors"
                       title="{{ $news['source'] }} • {{ $news['published_label'] }}">
                        {{ $news['title'] }}
                    </a>
                    <span class="text-[10px] text-zinc-500 font-semibold">{{ $news['source'] }} · {{ $news['published_label'] }}</span>
                    <span class="mono-separator">/</span>
                </div>
            @empty
                <span class="mono-empty italic uppercase">A obter notícias recentes de Portugal e do mundo...</span>
            @endforelse
            @if(count($this->news24h) > 0)
                @foreach($this->news24h as $news)
                    <div class="ticker-text-ultra flex items-center gap-3" aria-hidden="true">
                        <span class="text-[10px] font-black tracking-widest {{ $news['region'] === 'PT' ? 'text-emerald-400' : 'text-sky-400' }}">
                            {{ $news['region'] === 'PT' ? 'PORTUGAL' : 'MUNDO' }}
                        </span>
                        <span class="mono-item normal-case tracking-normal">{{ $news['title'] }}</span>
                        @if(!empty($news['summary']))
                            <span class="text-zinc-400 normal-case tracking-normal font-medium">— {{ $news['summary'] }}</span>
                        @endif
                        <span class="text-[10px] text-zinc-500 font-semibold">{{ $news['source'] }} · {{ $news['published_label'] }}</span>
                        <span class="mono-separator">/</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
@keyframes tickerUltra {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@keyframes bgFlow {
    0%   { transform: translateX(-30%); }
    100% { transform: translateX(30%); }
}

.animate-bgFlow {
    animation: bgFlow 12s linear infinite;
}

.news24h-card {
    isolation: isolate;
}

.news24h-shine {
    background: linear-gradient(105deg, transparent 15%, rgba(125, 211, 252, 0.10) 48%, transparent 82%);
    transform: skewX(-18deg);
    animation: news24hShine 8s ease-in-out infinite;
}

@keyframes news24hShine {
    0%, 65%, 100% { left: -45%; opacity: 0; }
    72% { opacity: 1; }
    90% { left: 120%; opacity: 0.7; }
}

.news24h-card:hover .news24h-shine {
    animation-duration: 3s;
}

.ticker-ultra {
    display: inline-flex;
    animation: tickerUltra linear infinite;
    will-change: transform;
}

.group:hover .ticker-ultra {
    animation-play-state: paused;
}

/* Texto monocromático premium */
.ticker-text-ultra {
    display: flex;
    align-items: center;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: #d4d4d8;
}

/* Notícias monocromáticas */
.mono-item {
    color: #f4f4f5;
    font-family: "Inter", "Segoe UI", Arial, sans-serif;
    font-size: 16px;
    font-weight: 650;
    letter-spacing: -0.015em;
    text-shadow: 0 0 4px rgba(0,0,0,0.4);
}

/* Barra separadora maior */
.mono-separator {
    margin: 0 2.5rem;
    color: #3f3f46;
    opacity: 0.7;
    font-weight: 900;
    font-size: 11px;
}

/* Fallback */
.mono-empty {
    color: #71717a;
    letter-spacing: 0.25em;
}
</style>







{{-- 3. HEADER PRINCIPAL COM CLIMA DINÂMICO E MODAL DETALHADO --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pt-4"
     x-data="{
        loading: true,
        error: '',
        locationMessage: '',
        data: { temp: '--', city: 'A obter localização…', code: 0, humidity: '--', wind: '--', feels_like: '--', forecast: [] },

        getWeatherIcon(code) {
            if ([0, 1].includes(Number(code))) return 'sun';
            if ([2, 3, 45, 48].includes(Number(code))) return 'cloud';
            return 'bolt';
        },

        getWeatherDescription(code) {
            const descriptions = {
                0: 'Céu limpo', 1: 'Pouco nublado', 2: 'Parcialmente nublado', 3: 'Nublado',
                45: 'Nevoeiro', 48: 'Nevoeiro com geada', 51: 'Chuvisco ligeiro',
                53: 'Chuvisco moderado', 55: 'Chuvisco intenso', 56: 'Chuvisco gelado',
                57: 'Chuvisco gelado intenso', 61: 'Chuva ligeira', 63: 'Chuva moderada',
                65: 'Chuva intensa', 66: 'Chuva gelada', 67: 'Chuva gelada intensa',
                71: 'Neve ligeira', 73: 'Neve moderada', 75: 'Neve intensa',
                77: 'Grãos de neve', 80: 'Aguaceiros ligeiros', 81: 'Aguaceiros moderados',
                82: 'Aguaceiros fortes', 85: 'Aguaceiros de neve', 86: 'Aguaceiros de neve fortes',
                95: 'Trovoada', 96: 'Trovoada com granizo', 99: 'Trovoada forte com granizo'
            };
            return descriptions[Number(code)] || 'Condições meteorológicas';
        },

        async fetchWeather(latitude, longitude, city, isFallback = false) {
            this.loading = true;
            this.error = '';
            try {
                const params = new URLSearchParams({
                    latitude: latitude,
                    longitude: longitude,
                    current: 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m',
                    daily: 'weather_code,temperature_2m_max,temperature_2m_min',
                    forecast_days: '5',
                    timezone: 'auto'
                });
                const response = await fetch('https://api.open-meteo.com/v1/forecast?' + params.toString());
                if (!response.ok) throw new Error('A fonte meteorológica não respondeu (HTTP ' + response.status + ').');
                const result = await response.json();
                if (!result.current || !result.daily) throw new Error('A fonte meteorológica devolveu dados incompletos.');

                const current = result.current;
                this.data = {
                    temp: Math.round(current.temperature_2m),
                    city: city,
                    code: current.weather_code,
                    humidity: Math.round(current.relative_humidity_2m),
                    wind: Math.round(current.wind_speed_10m),
                    feels_like: Math.round(current.apparent_temperature),
                    forecast: result.daily.time.map((date, index) => ({
                        day: index === 0 ? 'Hoje' : new Date(date + 'T12:00:00').toLocaleDateString('pt-PT', { weekday: 'short' }).replace('.', ''),
                        code: result.daily.weather_code[index],
                        max: Math.round(result.daily.temperature_2m_max[index]),
                        min: Math.round(result.daily.temperature_2m_min[index])
                    }))
                };
                if (isFallback) {
                    this.locationMessage = 'A localização não foi autorizada. A mostrar a previsão de Lisboa.';
                } else {
                    this.locationMessage = '';
                }
            } catch (exception) {
                this.error = 'Não foi possível carregar a meteorologia. Verifica a ligação à Internet e tenta novamente.';
                console.error('Erro ao carregar meteorologia:', exception);
            } finally {
                this.loading = false;
            }
        },

        loadWeather() {
            this.loading = true;
            this.error = '';
            if (!('geolocation' in navigator)) {
                this.fetchWeather(38.7223, -9.1393, 'Lisboa', true);
                return;
            }

            navigator.geolocation.getCurrentPosition(
                position => this.fetchWeather(
                    position.coords.latitude,
                    position.coords.longitude,
                    'A tua localização'
                ),
                error => {
                    console.warn('Não foi possível obter a localização:', error.message);
                    this.fetchWeather(38.7223, -9.1393, 'Lisboa', true);
                },
                { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 }
            );
        },

        init() {
            this.loadWeather();
        }
     }"
     x-init="init()"
>
    <div class="flex items-center gap-5">
        {{-- Avatar Dinâmico --}}
        <div class="size-16 rounded-[1.5rem] flex items-center justify-center text-4xl shadow-2xl shrink-0 transition-all duration-500"
             style="background-color: {{ auth()->user()->profile_color }}15; border: 2px solid {{ auth()->user()->profile_color }}30;">
            {{ auth()->user()->display_emoji }}
        </div>

        <div class="text-left">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-zinc-900 dark:text-white tracking-tighter uppercase italic leading-none">
                    {{ $this->greeting }}, <span style="color: {{ auth()->user()->profile_color }}">{{ $firstName }}</span>
                </h1>

                @if(session()->has('admin_impersonation'))
                    <form method="POST" action="{{ route('admin.stop-impersonating') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center gap-2 px-3 py-1 bg-amber-500 text-white rounded-full animate-pulse text-[9px] font-black uppercase tracking-widest shadow-lg shadow-amber-500/30">
                        Suporte Ativo · Sair
                        </button>
                    </form>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3 mt-2">
                <div class="flex items-center gap-2 text-zinc-400 font-bold italic text-sm">
                    <span class="flex size-2 rounded-full" style="background-color: {{ auth()->user()->profile_color }}"></span>
                    {{ $sharedText }} · {{ now()->translatedFormat('F Y') }}
                </div>

                <span class="hidden md:block text-zinc-300 dark:text-zinc-700">·</span>

                {{-- WIDGET CLICÁVEL (TRIGGER) --}}
                <flux:modal.trigger name="weather-details">
                    <button type="button" class="group flex items-center gap-2 px-3 py-1 bg-white dark:bg-zinc-800/40 rounded-full border border-zinc-200 dark:border-white/5 hover:border-brand-500/50 hover:shadow-lg transition-all duration-300 cursor-pointer">
                        <flux:icon name="map-pin" variant="micro" class="size-3 text-zinc-400 group-hover:text-brand-500" />
                        <span class="text-[9px] font-black uppercase text-zinc-500 tracking-wider" x-text="data.city"></span>

                        <div class="h-3 w-px bg-zinc-300 dark:bg-zinc-700 mx-0.5"></div>

                        <div class="flex items-center gap-1.5">
                            <template x-if="getWeatherIcon(data.code) === 'sun'"><flux:icon name="sun" variant="micro" class="size-3 text-amber-500 animate-spin-slow" /></template>
                            <template x-if="getWeatherIcon(data.code) === 'cloud'"><flux:icon name="cloud" variant="micro" class="size-3 text-zinc-400" /></template>
                            <template x-if="getWeatherIcon(data.code) === 'bolt'"><flux:icon name="bolt" variant="micro" class="size-3 text-blue-400" /></template>
                            <span class="text-[10px] font-black text-zinc-700 dark:text-zinc-200 uppercase italic" x-text="data.temp + '°C'"></span>
                        </div>
                    </button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- MODAL DETALHADO: conteúdo centrado e sem botão X duplicado/fora do modal --}}
    <flux:modal name="weather-details" position="center" :closable="false" class="!min-w-0 !w-[calc(100vw-2rem)] !max-w-[450px] !p-0 !overflow-hidden" wire:ignore.self>
        <div class="relative w-full overflow-hidden rounded-3xl bg-zinc-950 p-6 text-center text-white sm:p-8">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-600/20 via-transparent to-transparent"></div>

            <div class="relative z-10 space-y-2">
                <h2 class="text-2xl font-black italic uppercase tracking-tight sm:text-3xl" x-text="data.city"></h2>
                <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-zinc-400">Condições atmosféricas</p>
                <p x-show="loading" class="pt-2 text-xs font-semibold text-zinc-400" role="status">A carregar dados meteorológicos…</p>
                <p x-show="locationMessage && !loading" x-text="locationMessage" class="pt-2 text-xs text-amber-300"></p>
                <p x-show="error && !loading" x-text="error" class="pt-2 text-xs text-red-300" role="alert"></p>
                <button x-show="error && !loading" x-on:click="loadWeather()" type="button" class="mt-2 rounded-full border border-white/15 px-4 py-2 text-xs font-bold hover:bg-white/10">Tentar novamente</button>
            </div>

            <div class="relative z-10 flex items-center justify-center gap-5 py-7">
                <template x-if="getWeatherIcon(data.code) === 'sun'"><flux:icon name="sun" class="size-16 text-amber-400 sm:size-20" /></template>
                <template x-if="getWeatherIcon(data.code) === 'cloud'"><flux:icon name="cloud" class="size-16 text-zinc-300 sm:size-20" /></template>
                <template x-if="getWeatherIcon(data.code) === 'bolt'"><flux:icon name="bolt" class="size-16 text-blue-400 sm:size-20" /></template>
                <span class="text-6xl font-black italic tracking-tighter sm:text-7xl" x-text="data.temp + '°'"></span>
            </div>

            <div class="relative z-10 grid grid-cols-2 gap-3 text-left">
                <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3 sm:p-4">
                    <flux:icon name="beaker" class="size-5 shrink-0 text-blue-400" />
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold uppercase tracking-wider text-zinc-400">Humidade</p>
                        <p class="text-sm font-black sm:text-base" x-text="data.humidity + '%'"></p>
                    </div>
                </div>
                <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3 sm:p-4">
                    <flux:icon name="flag" class="size-5 shrink-0 text-emerald-400" />
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold uppercase tracking-wider text-zinc-400">Vento</p>
                        <p class="text-sm font-black sm:text-base" x-text="data.wind + ' km/h'"></p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-7 space-y-3">
                <p class="border-b border-white/10 pb-2 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-400">Previsão semanal</p>
                <div class="grid grid-cols-5 gap-1 sm:gap-2">
                    <template x-for="item in data.forecast" :key="item.day">
                        <div class="flex min-w-0 flex-col items-center rounded-xl px-1 py-3 transition-colors hover:bg-white/5 sm:px-2">
                            <span class="text-[9px] font-bold uppercase text-zinc-400" x-text="item.day"></span>
                            <div class="my-3">
                                <template x-if="getWeatherIcon(item.code) === 'sun'"><flux:icon name="sun" variant="micro" class="size-4 text-amber-400" /></template>
                                <template x-if="getWeatherIcon(item.code) === 'cloud'"><flux:icon name="cloud" variant="micro" class="size-4 text-zinc-300" /></template>
                                <template x-if="getWeatherIcon(item.code) === 'bolt'"><flux:icon name="bolt" variant="micro" class="size-4 text-blue-400" /></template>
                            </div>
                            <span class="text-xs font-black italic" x-text="item.max + '°'"></span>
                            <span class="mt-1 text-[10px] text-zinc-400" x-text="item.min + '°'"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </flux:modal>














@php
    $user = auth()->user();
    $hasProAccess = $user->isPaidPlan();
@endphp

{{-- TERMINAL DE ACÇÕES ESTRATÉGICAS --}}
<div class="relative z-40 flex flex-wrap items-center justify-center sm:justify-start gap-1.5 bg-white dark:bg-zinc-900 p-1.5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-visible">

    {{-- BOTÃO IA PRO --}}
    @if($hasProAccess)
        <a href="{{ route('ai') }}" wire:navigate
            class="group flex items-center justify-center gap-2 px-3 sm:px-4 h-10 rounded-xl bg-zinc-950 text-brand-400 font-black uppercase text-[10px] tracking-[0.2em] border border-zinc-800 hover:bg-brand-600 hover:text-white transition-all shadow-lg active:scale-95 shrink-0">
            <flux:icon name="sparkles" class="size-4 group-hover:rotate-12 transition-transform shrink-0" />
            <span class="hidden sm:inline">IA PRO</span>
        </a>
    @else
        <a href="{{ route('hub.pricing') }}" wire:navigate
            class="group flex items-center justify-center gap-2 px-3 sm:px-4 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 text-zinc-400 font-black uppercase text-[10px] tracking-[0.2em] border border-zinc-200 dark:border-zinc-700 opacity-60 shrink-0">
            <flux:icon name="lock-closed" variant="micro" class="size-3" />
            <span class="hidden sm:inline">IA PRO</span>
        </a>
    @endif

    <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-0.5 shrink-0"></div>

    {{-- BOTÃO RELATÓRIO PDF --}}
    @if($hasProAccess)
        <flux:modal.trigger name="export-pdf-modal">
            <button type="button"
                class="group flex items-center justify-center gap-2 px-3 sm:px-4 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 font-black uppercase text-[10px] tracking-widest border border-indigo-100 dark:border-indigo-500/20 hover:bg-indigo-600 hover:text-white transition-all shadow-sm active:scale-95 shrink-0">
                <flux:icon name="document-arrow-down" class="size-4 group-hover:-translate-y-0.5 transition-transform shrink-0" />
                <span class="hidden sm:inline">Relatório</span>
            </button>
        </flux:modal.trigger>
    @else
        <a href="{{ route('hub.pricing') }}" wire:navigate
            class="group flex items-center justify-center gap-2 px-3 sm:px-4 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 text-zinc-400 font-black uppercase text-[10px] tracking-widest border border-zinc-200 dark:border-zinc-700 opacity-60 shrink-0">
            <flux:icon name="lock-closed" variant="micro" class="size-3" />
            <span class="hidden sm:inline">Relatório</span>
        </a>
    @endif

    <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-0.5 shrink-0"></div>

    {{-- NAVEGAÇÃO DE MOVIMENTOS E CRIAÇÃO RÁPIDA --}}
    <div class="flex items-center gap-1.5 shrink-0">
        <a href="{{ route('movements.index') }}" wire:navigate
            class="flex items-center justify-center gap-2 px-3 sm:px-5 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-black uppercase text-[10px] tracking-widest border border-blue-100 dark:border-blue-900/50 hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-95 shrink-0">
            <flux:icon name="arrows-right-left" variant="micro" class="size-4 shrink-0" />
            <span>Movimentos</span>
        </a>

        <div x-data="{ openQuickCreate: false }" class="relative">
            <button type="button" x-on:click="openQuickCreate = !openQuickCreate" x-on:keydown.escape.window="openQuickCreate = false"
                :aria-expanded="openQuickCreate.toString()" aria-label="Adicionar novo registo"
                class="group flex size-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-blue-700 hover:scale-105 hover:shadow-xl active:scale-95">
                <flux:icon name="plus" class="size-5 transition-transform duration-200" x-bind:class="openQuickCreate ? 'rotate-45' : ''" />
            </button>

            <div x-cloak x-show="openQuickCreate" x-transition.origin.top.right x-on:click.outside="openQuickCreate = false"
                class="absolute right-0 top-full z-[100] mt-2 w-64 overflow-hidden rounded-2xl border border-zinc-200 bg-white p-2 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
                <p class="px-3 pb-2 pt-1 text-[9px] font-black uppercase tracking-[0.18em] text-zinc-400">Criar novo registo</p>
                <a href="{{ route('expenses', ['open_new' => 1]) }}" wire:navigate x-on:click="openQuickCreate = false"
                    class="flex items-center gap-3 rounded-xl px-3 py-3 text-left transition hover:bg-rose-50 dark:hover:bg-rose-950/30">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300"><span class="text-sm font-black leading-none">▤</span></span>
                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Nova despesa</span>
                </a>
                <a href="{{ route('hub.subscriptions', ['open_extra' => 1]) }}" wire:navigate x-on:click="openQuickCreate = false"
                    class="flex items-center gap-3 rounded-xl px-3 py-3 text-left transition hover:bg-violet-50 dark:hover:bg-violet-950/30">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300"><flux:icon name="arrow-path" class="size-4" /></span>
                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Nova subscrição</span>
                </a>
                <a href="{{ route('hub.incomes', ['open_extra' => 1]) }}" wire:navigate x-on:click="openQuickCreate = false"
                    class="flex items-center gap-3 rounded-xl px-3 py-3 text-left transition hover:bg-emerald-50 dark:hover:bg-emerald-950/30">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300"><span class="text-sm font-black leading-none">€</span></span>
                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Nova receita extra</span>
                </a>
            </div>
        </div>
    </div>
</div>













</div>




{{-- 4. SELETOR DE ESPAÇO (WORKSPACE SWITCHER) --}}
@php
    // Definimos a variável aqui para evitar o erro de 'Undefined variable'
    $isBusiness = auth()->user()->isBusinessPlan();
@endphp

<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
@if($isBusiness && $userWorkspaces->count() >= 1)
    <div class="flex items-center gap-2 sm:gap-4 bg-zinc-100/50 dark:bg-zinc-900/50 p-1.5 rounded-2xl w-fit border border-zinc-200/50 dark:border-zinc-800/50 shadow-sm">

        {{-- Label: Escondemos o texto "Espaços" no mobile para não ficar vertical --}}
        <div class="px-2 sm:px-3 py-1 text-[9px] font-black uppercase text-zinc-500 tracking-widest border-r border-zinc-200 dark:border-zinc-800 flex items-center gap-2 shrink-0">
            <flux:icon name="building-office-2" variant="micro" class="size-3 text-violet-500 shrink-0" />
            <span class="hidden sm:inline">Espaços</span>
        </div>

        <div class="flex gap-1 sm:gap-1.5 overflow-x-auto no-scrollbar items-center">
            @foreach($userWorkspaces as $ws)
                @php $isActive = ($ws->id == $currentWs->id); @endphp
                <button
                    wire:click="switchWorkspace({{ $ws->id }})"
                    wire:loading.class="opacity-50 cursor-wait"
                    wire:target="switchWorkspace({{ $ws->id }})"
                    class="group flex items-center justify-center gap-2 px-3 sm:px-4 py-2 rounded-xl transition-all duration-200 shrink-0
                    {{ $isActive
                        ? 'bg-white dark:bg-zinc-800 shadow-md text-brand-600 font-black'
                        : 'text-zinc-500 hover:bg-white dark:hover:bg-zinc-800' }}"
                >
                    <div class="size-1.5 rounded-full shrink-0
                        {{ $isActive ? 'bg-brand-500 shadow-[0_0_8px_#3b82f6]' : 'bg-zinc-300' }}">
                    </div>

                    {{-- No telemóvel mostra apenas a 1ª Letra, no PC mostra o nome todo --}}
                    <span class="hidden sm:inline text-xs uppercase tracking-tighter">{{ $ws->name }}</span>
                    <span class="sm:hidden text-[10px] font-black uppercase">{{ substr($ws->name, 0, 1) }}</span>
                </button>
            @endforeach
        </div>

        {{-- Botão Adicionar (+) --}}
        <a href="{{ route('hub.business.gateway', ['new' => 1]) }}"
           wire:navigate
           class="flex items-center justify-center size-8 rounded-xl bg-zinc-200/60 dark:bg-zinc-800 text-zinc-400 hover:text-brand-500 transition-all shrink-0">
            <flux:icon name="plus" class="size-4" />
        </a>
    </div>
@endif

    @php
        $dashboardInvestmentCards = collect($dashboardInvestments ?? [])->map(function ($investment) use ($currentWs) {
            $invested = round(((float) $investment['quantity'] * (float) $investment['average_price']) + (float) $investment['fees'], 2);
            $current = round((float) $investment['quantity'] * (float) $investment['current_price'], 2);
            $profit = round($current - $invested, 2);

            return [
                'symbol' => $investment['symbol'] ?: 'Ativo',
                'name' => $investment['name'] ?: $investment['symbol'],
                'profit' => $profit,
                'profit_label' => ($profit >= 0 ? '+' : '').$currentWs->money($profit),
                'percent_label' => ($invested > 0 && $profit >= 0 ? '+' : '').number_format($invested > 0 ? ($profit / $invested) * 100 : 0, 2, ',', ' ').'%',
            ];
        })->values()->all();
    @endphp

    <div class="flex min-w-0 flex-1 flex-wrap items-center justify-end gap-2.5">
        <div
            x-data="{ items: @js($dashboardInvestmentCards), activeInvestment: 0, next() { if (this.items.length) this.activeInvestment = (this.activeInvestment + 1) % this.items.length }, previous() { if (this.items.length) this.activeInvestment = (this.activeInvestment - 1 + this.items.length) % this.items.length } }"
            x-init="$wire.on('dashboard-investments-updated', event => { items = event.detail.items; activeInvestment = 0; })"
            x-show="items.length > 0"
            x-cloak
            class="flex min-w-0 items-center justify-between gap-3 self-start rounded-2xl border border-emerald-200/70 bg-white/80 px-3 py-2 shadow-sm shadow-emerald-950/5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-emerald-400/15 dark:bg-zinc-900/80 lg:min-w-[270px] lg:self-auto"
        >
            <div class="flex min-w-0 items-center gap-2.5">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon name="chart-bar" class="size-4" />
                </span>
                <div class="min-w-0">
                    <p class="text-[9px] font-black uppercase tracking-wider text-zinc-400">Lucro dos investimentos</p>
                    <div class="flex min-w-0 items-baseline gap-2">
                        <span class="truncate text-xs font-black text-zinc-800 dark:text-zinc-100" x-text="items[activeInvestment]?.symbol"></span>
                        <span class="truncate text-sm font-black tabular-nums" :class="items[activeInvestment]?.profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="items[activeInvestment]?.profit_label"></span>
                        <span class="shrink-0 text-[10px] font-bold tabular-nums" :class="items[activeInvestment]?.profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="items[activeInvestment]?.percent_label"></span>
                    </div>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-1">
                <button
                    type="button"
                    wire:click="refreshInvestmentPrices"
                    wire:loading.attr="disabled"
                    wire:target="refreshInvestmentPrices"
                    aria-label="Atualizar preços dos investimentos"
                    title="Atualizar preços dos investimentos"
                    class="flex size-7 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-emerald-50 hover:text-emerald-600 disabled:cursor-wait disabled:opacity-50 dark:hover:bg-emerald-400/10 dark:hover:text-emerald-400">
                    <flux:icon name="arrow-path" class="size-4" wire:loading.remove wire:target="refreshInvestmentPrices" />
                    <flux:icon name="arrow-path" class="size-4 animate-spin" wire:loading wire:target="refreshInvestmentPrices" />
                </button>
                <span class="mr-1 text-[9px] font-semibold tabular-nums text-zinc-400" x-text="(activeInvestment + 1) + ' / ' + items.length"></span>
                <button type="button" x-on:click="previous()" aria-label="Ativo anterior" title="Ativo anterior" class="flex size-7 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-emerald-600 dark:hover:bg-zinc-800 dark:hover:text-emerald-400">
                    <flux:icon name="chevron-left" class="size-4" />
                </button>
                <button type="button" x-on:click="next()" aria-label="Ativo seguinte" title="Ativo seguinte" class="flex size-7 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-emerald-600 dark:hover:bg-zinc-800 dark:hover:text-emerald-400">
                    <flux:icon name="chevron-right" class="size-4" />
                </button>
            </div>
        </div>

    </div>
</div>

    {{-- MENU DE ÂNCORAS: navegação rápida pelas secções da dashboard --}}
    <nav aria-label="Menu de Âncoras" class="sticky top-2 z-30 flex w-full min-w-0 flex-col gap-2 overflow-hidden rounded-2xl border border-sky-200/80 bg-gradient-to-r from-white via-sky-50/95 to-blue-50/90 p-2.5 shadow-lg shadow-sky-950/10 backdrop-blur-xl dark:border-sky-400/20 dark:from-zinc-950 dark:via-slate-900 dark:to-blue-950/40 sm:flex-row sm:items-center sm:gap-3 sm:px-4">
        <div class="flex shrink-0 items-center gap-2 px-1 sm:border-r sm:border-sky-200 sm:pr-3 dark:sm:border-sky-400/20">
            <span class="flex size-8 items-center justify-center rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white shadow-md shadow-blue-500/20 transition duration-300 hover:rotate-6 hover:scale-110">
                <span aria-hidden="true" class="text-base font-black leading-none">☷</span>
            </span>
            <span class="text-sm font-black uppercase tracking-wider text-sky-900 dark:text-sky-100">Menu inicial</span>
        </div>
        <div class="no-scrollbar flex min-w-0 items-center gap-1.5 overflow-x-auto pb-0.5 sm:grid sm:flex-1 sm:grid-cols-7 sm:gap-1">
            <a href="#dashboard-salario-alimentacao" onclick="event.preventDefault(); const target = document.querySelector(this.getAttribute('href')); if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });" class="group relative flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl border border-transparent px-3 py-2.5 text-sm font-extrabold text-slate-600 transition-all duration-300 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-white hover:text-blue-700 hover:shadow-md hover:shadow-sky-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 dark:text-slate-300 dark:hover:border-sky-400/20 dark:hover:bg-sky-400/10 dark:hover:text-sky-200 sm:min-w-0 sm:px-1 sm:text-center"><span>Salário e alimentação</span><span class="absolute inset-x-3 bottom-1 h-0.5 origin-center scale-x-0 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-transform duration-300 group-hover:scale-x-100"></span></a>
            <a href="#dashboard-investimentos" onclick="event.preventDefault(); const target = document.querySelector(this.getAttribute('href')); if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });" class="group relative flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl border border-transparent px-3 py-2.5 text-sm font-extrabold text-slate-600 transition-all duration-300 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-white hover:text-blue-700 hover:shadow-md hover:shadow-sky-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 dark:text-slate-300 dark:hover:border-sky-400/20 dark:hover:bg-sky-400/10 dark:hover:text-sky-200 sm:min-w-0 sm:px-1 sm:text-center"><span>Investimentos</span><span class="absolute inset-x-3 bottom-1 h-0.5 origin-center scale-x-0 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-transform duration-300 group-hover:scale-x-100"></span></a>
            <a href="#dashboard-cfo" onclick="event.preventDefault(); const target = document.querySelector(this.getAttribute('href')); if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });" class="group relative flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl border border-transparent px-3 py-2.5 text-sm font-extrabold text-slate-600 transition-all duration-300 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-white hover:text-blue-700 hover:shadow-md hover:shadow-sky-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 dark:text-slate-300 dark:hover:border-sky-400/20 dark:hover:bg-sky-400/10 dark:hover:text-sky-200 sm:min-w-0 sm:px-1 sm:text-center"><span>CFO Inteligente</span><span class="absolute inset-x-3 bottom-1 h-0.5 origin-center scale-x-0 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-transform duration-300 group-hover:scale-x-100"></span></a>
            <a href="#dashboard-novos-indicadores" onclick="event.preventDefault(); const target = document.querySelector(this.getAttribute('href')); if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });" class="group relative flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl border border-transparent px-3 py-2.5 text-sm font-extrabold text-slate-600 transition-all duration-300 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-white hover:text-blue-700 hover:shadow-md hover:shadow-sky-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 dark:text-slate-300 dark:hover:border-sky-400/20 dark:hover:bg-sky-400/10 dark:hover:text-sky-200 sm:min-w-0 sm:px-1 sm:text-center"><span>Indicadores</span><span class="absolute inset-x-3 bottom-1 h-0.5 origin-center scale-x-0 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-transform duration-300 group-hover:scale-x-100"></span></a>
            <a href="#dashboard-experiencia" onclick="event.preventDefault(); const target = document.querySelector(this.getAttribute('href')); if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });" class="group relative flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl border border-transparent px-3 py-2.5 text-sm font-extrabold text-slate-600 transition-all duration-300 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-white hover:text-blue-700 hover:shadow-md hover:shadow-sky-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 dark:text-slate-300 dark:hover:border-sky-400/20 dark:hover:bg-sky-400/10 dark:hover:text-sky-200 sm:min-w-0 sm:px-1 sm:text-center"><span>Nível e XP</span><span class="absolute inset-x-3 bottom-1 h-0.5 origin-center scale-x-0 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-transform duration-300 group-hover:scale-x-100"></span></a>
            <a href="#dashboard-movimentos" onclick="event.preventDefault(); const target = document.querySelector(this.getAttribute('href')); if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });" class="group relative flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl border border-transparent px-3 py-2.5 text-sm font-extrabold text-slate-600 transition-all duration-300 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-white hover:text-blue-700 hover:shadow-md hover:shadow-sky-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 dark:text-slate-300 dark:hover:border-sky-400/20 dark:hover:bg-sky-400/10 dark:hover:text-sky-200 sm:min-w-0 sm:px-1 sm:text-center"><span>Movimentos</span><span class="absolute inset-x-3 bottom-1 h-0.5 origin-center scale-x-0 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-transform duration-300 group-hover:scale-x-100"></span></a>
            <a href="#dashboard-resumo-conta" onclick="event.preventDefault(); const target = document.querySelector(this.getAttribute('href')); if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });" class="group relative flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl border border-transparent px-3 py-2.5 text-sm font-extrabold text-slate-600 transition-all duration-300 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-white hover:text-blue-700 hover:shadow-md hover:shadow-sky-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 dark:text-slate-300 dark:hover:border-sky-400/20 dark:hover:bg-sky-400/10 dark:hover:text-sky-200 sm:min-w-0 sm:px-1 sm:text-center"><span>Resumo da conta</span><span class="absolute inset-x-3 bottom-1 h-0.5 origin-center scale-x-0 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-transform duration-300 group-hover:scale-x-100"></span></a>
        </div>
    </nav>

    {{-- Cartões de salário e subsídio de alimentação, com histórico --}}
    <section id="dashboard-salario-alimentacao" x-data="{ showSalaryHistory: false, showMealHistory: false }" class="scroll-mt-28 space-y-4">
        <div x-data="{ openPeriodFilter: false }" class="flex flex-wrap items-center justify-between gap-3 px-1">
            <div class="relative">
                <button type="button" x-on:click="openPeriodFilter = !openPeriodFilter" class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-xs font-black uppercase tracking-wider text-zinc-600 shadow-sm transition hover:border-brand-400 hover:text-brand-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                    <flux:icon name="calendar-days" variant="micro" class="size-4 text-emerald-500" />
                    {{ $dashboardPeriodLabel }}
                    <flux:icon name="chevron-down" variant="micro" class="size-3.5 transition-transform" />
                </button>
                <div x-cloak x-show="openPeriodFilter" x-on:click.outside="openPeriodFilter = false" x-transition class="absolute left-0 top-full z-40 mt-2 w-[min(92vw,24rem)] rounded-2xl border border-zinc-200 bg-white p-4 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
                    @php
                        $periodMonthNames = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
                        $selectedMonthYear = (int) substr($dashboardSelectedMonth, 0, 4);
                        $selectedMonthNumber = (int) substr($dashboardSelectedMonth, 5, 2);
                    @endphp
                    <div class="mb-3 grid grid-cols-2 gap-2">
                        <button type="button" wire:click="$set('dashboardSelectedMonth', '{{ now()->format('Y-m') }}'); $set('dashboardPeriodType', 'month')" x-on:click="openPeriodFilter = false" class="rounded-xl border border-brand-200 bg-brand-50 px-3 py-2.5 text-[11px] font-black text-brand-700 transition hover:bg-brand-100 dark:border-brand-500/30 dark:bg-brand-500/10 dark:text-brand-300">Mês atual</button>
                        <button type="button" wire:click="$set('dashboardSelectedYear', '{{ now()->format('Y') }}'); $set('dashboardPeriodType', 'year')" x-on:click="openPeriodFilter = false" class="rounded-xl border border-brand-200 bg-brand-50 px-3 py-2.5 text-[11px] font-black text-brand-700 transition hover:bg-brand-100 dark:border-brand-500/30 dark:bg-brand-500/10 dark:text-brand-300">Ano atual</button>
                    </div>
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Consultar outro período</p>
                        <span class="rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-black text-brand-700 dark:bg-brand-500/10 dark:text-brand-300">{{ $dashboardPeriodType === 'year' ? $dashboardSelectedYear : $dashboardSelectedMonth }}</span>
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-2 rounded-xl bg-zinc-100 p-1 dark:bg-zinc-800">
                        <button type="button" wire:click="$set('dashboardPeriodType', 'month')" class="rounded-lg px-3 py-2 text-xs font-bold transition {{ $dashboardPeriodType === 'month' ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 dark:text-zinc-400' }}">Por mês</button>
                        <button type="button" wire:click="$set('dashboardPeriodType', 'year')" class="rounded-lg px-3 py-2 text-xs font-bold transition {{ $dashboardPeriodType === 'year' ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 dark:text-zinc-400' }}">Por ano</button>
                    </div>

                    @if($dashboardPeriodType === 'year')
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-zinc-500">Escolhe o ano</p>
                        <div class="grid max-h-64 grid-cols-4 gap-2 overflow-y-auto pr-1">
                            @for($year = now()->year; $year >= 2000; $year--)
                                <button type="button" wire:click="$set('dashboardSelectedYear', {{ $year }})" class="rounded-xl border px-2 py-2.5 text-xs font-bold transition {{ (int) $dashboardSelectedYear === $year ? 'border-brand-500 bg-brand-600 text-white shadow-md shadow-brand-600/20' : 'border-zinc-200 bg-zinc-50 text-zinc-600 hover:border-brand-400 hover:bg-brand-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}">{{ $year }}</button>
                            @endfor
                        </div>
                    @else
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-zinc-500">1. Escolhe o ano</p>
                        <div class="mb-4 grid max-h-28 grid-cols-5 gap-1.5 overflow-y-auto pr-1">
                            @for($year = now()->year; $year >= 2000; $year--)
                                <button type="button" wire:click="$set('dashboardSelectedMonth', '{{ $year }}-{{ sprintf('%02d', $selectedMonthNumber) }}')" class="rounded-lg border px-1 py-2 text-[11px] font-bold transition {{ $selectedMonthYear === $year ? 'border-brand-500 bg-brand-600 text-white' : 'border-zinc-200 bg-zinc-50 text-zinc-600 hover:border-brand-400 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ $year }}</button>
                            @endfor
                        </div>
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-zinc-500">2. Escolhe o mês</p>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($periodMonthNames as $monthNumber => $monthName)
                                @php $monthValue = sprintf('%04d-%02d', $selectedMonthYear, $monthNumber); @endphp
                                <button type="button" wire:click="$set('dashboardSelectedMonth', '{{ $monthValue }}')" @disabled($monthValue > now()->format('Y-m')) class="rounded-xl border px-2 py-2.5 text-[11px] font-bold transition disabled:cursor-not-allowed disabled:opacity-30 {{ $selectedMonthNumber === $monthNumber ? 'border-brand-500 bg-brand-600 text-white shadow-md shadow-brand-600/20' : 'border-zinc-200 bg-zinc-50 text-zinc-600 hover:border-brand-400 hover:bg-brand-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}">{{ $monthName }}</button>
                            @endforeach
                        </div>
                    @endif
                    <button type="button" wire:click="$refresh" x-on:click="openPeriodFilter = false" class="mt-4 w-full rounded-xl bg-brand-600 px-4 py-3 text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-500">Aplicar filtro</button>
                </div>
            </div>
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <button type="button" wire:click="$set('dashboardSelectedMonth', '{{ now()->format('Y-m') }}'); $set('dashboardPeriodType', 'month')" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-[10px] font-black uppercase tracking-wider transition {{ $dashboardPeriodType === 'month' && $dashboardSelectedMonth === now()->format('Y-m') ? 'border-brand-500 bg-brand-600 text-white shadow-sm' : 'border-zinc-200 bg-white text-zinc-600 hover:border-brand-400 hover:text-brand-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' }}">
                    <flux:icon name="calendar" variant="micro" class="size-3.5" /> Mês atual
                </button>
                <button type="button" wire:click="$set('dashboardSelectedYear', '{{ now()->format('Y') }}'); $set('dashboardPeriodType', 'year')" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-[10px] font-black uppercase tracking-wider transition {{ $dashboardPeriodType === 'year' && (int) $dashboardSelectedYear === now()->year ? 'border-brand-500 bg-brand-600 text-white shadow-sm' : 'border-zinc-200 bg-white text-zinc-600 hover:border-brand-400 hover:text-brand-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' }}">
                    <flux:icon name="calendar-days" variant="micro" class="size-3.5" /> Ano atual
                </button>
            </div>
            @if($dashboardPeriodType !== 'month' || $dashboardSelectedMonth !== now()->format('Y-m'))
                <button type="button" wire:click="$set('dashboardSelectedMonth', '{{ now()->format('Y-m') }}'); $set('dashboardPeriodType', 'month')" class="text-[10px] font-bold uppercase tracking-wider text-brand-600 hover:underline">Voltar ao mês atual</button>
            @endif
        </div>

        <div class="grid grid-cols-1 items-start gap-8 md:grid-cols-2">
            <div class="min-w-0 space-y-3">
            {{-- CARTÃO BANCÁRIO: FRENTE E VERSO --}}
            <article x-data="{ flipped: false }" role="button" tabindex="0" aria-label="Abrir detalhes e histórico do saldo bancário" x-on:click="showSalaryHistory = true" @keydown.enter.prevent="showSalaryHistory = true" @keydown.space.prevent="showSalaryHistory = true" class="dashboard-card-shell group relative aspect-[1.95/1] w-full min-w-0 cursor-pointer rounded-[1.6rem] focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 focus:ring-offset-zinc-950" style="perspective: 1400px; aspect-ratio: 1.95 / 1;">
                <div class="dashboard-card-flipper relative h-full w-full rounded-[1.6rem]" :class="{ 'is-flipped': flipped }">
                    <div class="dashboard-card-surface absolute inset-0 overflow-hidden rounded-[1.6rem] border p-4 text-white shadow-2xl transition-shadow duration-500 sm:p-5" style="backface-visibility: hidden; background: linear-gradient(135deg, {{ $dashboardBankCardAccount?->color ?: '#ed1c24' }} 0%, color-mix(in srgb, {{ $dashboardBankCardAccount?->color ?: '#ed1c24' }} 72%, #000 28%) 100%); border-color: color-mix(in srgb, {{ $dashboardBankCardAccount?->color ?: '#ed1c24' }} 65%, white 35%);">
                        <div class="pointer-events-none absolute -right-14 -top-20 size-64 rounded-full border border-white/15 transition-transform duration-700 group-hover:rotate-12 group-hover:scale-110"></div>
                <div class="pointer-events-none absolute -right-3 -top-12 size-44 rounded-full border border-white/10"></div>
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-white/10 via-transparent to-black/10"></div>
                <div class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 -skew-x-12 bg-gradient-to-r from-transparent via-white/15 to-transparent transition-transform duration-1000 group-hover:translate-x-[450%]"></div>
                <div class="relative flex min-h-0 flex-col justify-between gap-3 transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-red-100/80">Finance Pro · Carteira bancária</p>
                            <p class="mt-1 truncate text-lg font-black tracking-wide text-white">{{ $dashboardBankCardAccount?->bank_name ?: 'Contas bancárias' }}</p>
                        </div>
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-sm font-black italic tracking-wider text-white shadow-inner backdrop-blur-sm">FP</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="relative h-9 w-12 overflow-hidden rounded-md border border-amber-100/50 bg-gradient-to-br from-amber-100 via-amber-300 to-amber-500 shadow-inner"><span class="absolute left-1/2 top-0 h-full w-px bg-amber-800/25"></span><span class="absolute left-0 top-1/2 h-px w-full bg-amber-800/25"></span><span class="absolute left-1/4 top-0 h-full w-px bg-amber-800/20"></span><span class="absolute right-1/4 top-0 h-full w-px bg-amber-800/20"></span></div>
                        <flux:icon name="wifi" variant="outline" class="size-5 rotate-90 text-white/75" />
                        <span class="ml-auto text-[9px] font-bold uppercase tracking-[0.18em] text-red-100/80">Saldo previsto</span>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-red-100/80">{{ $isCurrentDashboardPeriod ? 'Saldo atual + salário líquido' : 'Saldo no fim do período' }}</p>
                        <p class="mt-1 text-3xl font-black tracking-tight tabular-nums text-white transition-transform duration-300 group-hover:translate-x-1 sm:text-4xl">{{ number_format($dashboardBankCardBalance, 2, ',', ' ') }} <span class="text-xl text-red-100">€</span></p>
                    </div>
                    <div class="grid grid-cols-3 gap-2 border-t border-white/20 pt-2">
                        <div class="min-w-0 transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''"><p class="text-[8px] font-bold uppercase tracking-[0.12em] text-red-100/70">Saldo bancário</p><p class="mt-1 truncate text-[10px] font-bold tabular-nums text-white sm:text-[11px]">{{ number_format($dashboardBankCardCurrentBalance, 2, ',', ' ') }} €</p></div>
                        <div class="min-w-0 text-center transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''"><p class="text-[8px] font-bold uppercase tracking-[0.12em] text-red-100/70">Total investido</p><p class="mt-1 truncate text-[10px] font-bold tabular-nums text-white sm:text-[11px]">{{ number_format($dashboardInvestmentsTotalInvested, 2, ',', ' ') }} €</p></div>
                        <div class="min-w-0 text-right transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''"><p class="text-[8px] font-bold uppercase tracking-[0.12em] text-red-100/70">{{ $isCurrentDashboardPeriod ? 'Salário líquido' : 'Período consultado' }}</p><p class="mt-1 truncate text-[10px] font-bold tabular-nums text-white sm:text-[11px]">{{ $isCurrentDashboardPeriod ? '+ '.number_format($dashboardBankCardSalaryAmount, 2, ',', ' ').' €' : $dashboardPeriodLabel }}</p></div>
                    </div>
                </div>
                    </div>
                    <div class="dashboard-card-surface absolute inset-0 flex flex-col justify-between overflow-hidden rounded-[1.6rem] border p-4 text-white shadow-2xl sm:p-5" style="transform: rotateY(180deg); backface-visibility: hidden; background: linear-gradient(135deg, color-mix(in srgb, {{ $dashboardBankCardAccount?->color ?: '#ed1c24' }} 82%, #000 18%) 0%, color-mix(in srgb, {{ $dashboardBankCardAccount?->color ?: '#ed1c24' }} 48%, #000 52%) 100%); border-color: color-mix(in srgb, {{ $dashboardBankCardAccount?->color ?: '#ed1c24' }} 65%, white 35%);">
                        <div class="pointer-events-none absolute -right-12 -top-20 size-64 rounded-full border border-white/10"></div>
                        <div class="relative transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''">
                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-red-100/70">Finance Pro · Verso do cartão</p>
                            <h3 class="mt-1 text-base font-black text-white">Detalhes da conta</h3>
                            <p class="mt-0.5 text-[10px] text-red-100/75">{{ $dashboardBankCardAccount?->bank_name ?: 'Conta bancária selecionada' }}</p>
                        </div>
                        <div class="relative grid grid-cols-2 gap-x-5 gap-y-2.5 transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''">
                            <div class="min-w-0 py-1"><p class="text-[9px] font-bold uppercase tracking-wider text-red-100/70">Titular</p><p class="mt-1 truncate text-[13px] font-bold">{{ $dashboardBankCardAccount?->holder_name ?: 'Não indicado' }}</p></div>
                            <div class="min-w-0 py-1"><p class="text-[9px] font-bold uppercase tracking-wider text-red-100/70">Conta</p><p class="mt-1 text-[13px] font-bold">{{ $dashboardBankCardAccount?->account_number ? '•••• '.substr(preg_replace('/\s+/', '', $dashboardBankCardAccount->account_number), -4) : 'Não indicada' }}</p></div>
                            <div class="min-w-0 py-1"><p class="text-[9px] font-bold uppercase tracking-wider text-red-100/70">SWIFT / BIC</p><p class="mt-1 truncate text-[13px] font-bold">{{ $dashboardBankCardAccount?->swift ?: '—' }}</p></div>
                            <div class="col-span-2 min-w-0 py-1"><p class="text-[9px] font-bold uppercase tracking-wider text-red-100/70">IBAN</p><p class="mt-1 truncate text-[13px] font-bold tracking-wider">{{ $dashboardBankCardAccount?->iban ? substr(preg_replace('/\s+/', '', $dashboardBankCardAccount->iban), 0, 4).' •••• •••• •••• '.substr(preg_replace('/\s+/', '', $dashboardBankCardAccount->iban), -4) : 'Não registado' }}</p></div>
                        </div>
                    </div>
                </div>
                <button type="button" @click.stop="flipped = !flipped" :aria-label="flipped ? 'Mostrar frente do cartão' : 'Virar cartão'" :aria-pressed="flipped.toString()" title="Virar cartão" class="absolute bottom-3 right-3 z-30 flex size-8 items-center justify-center rounded-full border border-white/25 bg-black/20 text-white shadow-lg backdrop-blur-sm transition duration-300 hover:rotate-[-45deg] hover:bg-black/35 focus:outline-none focus:ring-2 focus:ring-white/70">
                    <span aria-hidden="true" class="text-[22px] font-semibold leading-none">↻</span>
                </button>
            </article>
            </div>

            <div class="min-w-0 space-y-3">
            {{-- CARTÃO DE SUBSÍDIO DE ALIMENTAÇÃO: FRENTE E VERSO --}}
            <article x-data="{ flipped: false }" role="button" tabindex="0" aria-label="Abrir detalhes e histórico do subsídio de alimentação" x-on:click="showMealHistory = true" @keydown.enter.prevent="showMealHistory = true" @keydown.space.prevent="showMealHistory = true" class="dashboard-card-shell group relative aspect-[1.95/1] w-full min-w-0 cursor-pointer rounded-[1.6rem] focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-zinc-950" style="perspective: 1400px; aspect-ratio: 1.95 / 1;">
                <div class="dashboard-card-flipper relative h-full w-full rounded-[1.6rem]" :class="{ 'is-flipped': flipped }">
                    <div class="dashboard-card-surface absolute inset-0 overflow-hidden rounded-[1.6rem] border border-emerald-300/20 bg-gradient-to-br from-[#075443] via-[#06382f] to-[#031e1a] p-4 text-white shadow-2xl shadow-emerald-950/25 transition-shadow duration-500 group-hover:shadow-emerald-950/40 sm:p-5" style="backface-visibility: hidden;">
                        <div class="pointer-events-none absolute -right-12 -top-20 size-64 rounded-full border border-white/10 transition-transform duration-700 group-hover:-rotate-12 group-hover:scale-110"></div>
                <div class="pointer-events-none absolute -right-2 -top-12 size-44 rounded-full border border-emerald-100/10"></div>
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-emerald-300/10 via-transparent to-white/5"></div>
                <div class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-1000 group-hover:translate-x-[450%]"></div>
                <div class="relative flex min-h-44 flex-col justify-between transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''">
                    <div class="flex items-start justify-between">
                        <div><p class="text-[9px] font-black uppercase tracking-[0.28em] text-emerald-100/80">Finance Pro · Cartão benefício</p><p class="mt-1 text-sm font-bold tracking-wide text-white">SUBSÍDIO DE ALIMENTAÇÃO</p></div>
                        <div class="flex items-center gap-2"><span class="text-[10px] font-black italic tracking-[0.18em] text-white/80">FP</span><div class="flex size-9 items-center justify-center rounded-xl border border-white/15 bg-white/10 backdrop-blur-sm"><flux:icon name="cake" variant="outline" class="size-5 text-emerald-100" /></div></div>
                    </div>
                    <div class="flex items-center gap-3 py-2"><div class="relative h-9 w-12 overflow-hidden rounded-md border border-amber-100/40 bg-gradient-to-br from-amber-100 via-amber-300 to-amber-500 shadow-inner"><span class="absolute left-1/2 top-0 h-full w-px bg-amber-800/25"></span><span class="absolute left-0 top-1/2 h-px w-full bg-amber-800/25"></span><span class="absolute left-1/4 top-0 h-full w-px bg-amber-800/20"></span><span class="absolute right-1/4 top-0 h-full w-px bg-amber-800/20"></span></div><flux:icon name="wifi" variant="outline" class="size-5 rotate-90 text-white/65" /><span class="ml-auto text-[9px] font-bold uppercase tracking-[0.18em] text-emerald-100/65">{{ $dashboardPeriodType === 'year' ? 'Benefício no ano' : 'Benefício mensal' }}</span></div>
                    <div><p class="text-[9px] font-bold uppercase tracking-[0.2em] text-emerald-100/75">{{ $dashboardPeriodType === 'year' ? 'Total do período' : 'Valor projetado' }}</p><p class="mt-1 text-3xl font-black tracking-tight tabular-nums text-white transition-transform duration-300 group-hover:translate-x-1 sm:text-4xl">{{ number_format($dashboardProjectedMealAllowance, 2, ',', ' ') }} <span class="text-xl text-emerald-200">€</span></p></div>
                    <div class="flex items-end justify-between gap-3 border-t border-white/15 pt-2">
                        <div class="min-w-0 transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''"><p class="text-[8px] font-bold uppercase tracking-[0.16em] text-emerald-100/65">Atribuído por</p><p class="mt-1 truncate text-[11px] font-bold text-white">MIND</p></div>
                        <div class="shrink-0 text-right transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''"><p class="text-[8px] font-bold uppercase tracking-[0.16em] text-emerald-100/65">{{ $dashboardPeriodType === 'year' ? 'Total no ano' : 'Projeção anual' }}</p><p class="mt-1 text-xs font-bold tabular-nums text-white">{{ number_format($dashboardMealAllowanceAnnualProjection, 2, ',', ' ') }} €</p></div>
                    </div>
                </div>
                    </div>
                    <div class="dashboard-card-surface absolute inset-0 flex flex-col justify-between overflow-hidden rounded-[1.6rem] border border-emerald-200/30 bg-gradient-to-br from-[#064b3d] via-[#03352c] to-[#011c17] p-3.5 text-white shadow-2xl sm:p-4" style="transform: rotateY(180deg); backface-visibility: hidden;">
                        <div class="pointer-events-none absolute -right-12 -top-20 size-64 rounded-full border border-white/10"></div>
                        <div class="relative transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''">
                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-emerald-100/70">Finance Pro · Verso do cartão</p>
                            <h3 class="mt-2 text-lg font-black text-white">Detalhes do benefício</h3>
                            <p class="mt-1 text-xs text-emerald-100/75">Configuração dos contratos ativos</p>
                        </div>
                        <div class="relative grid grid-cols-2 gap-3 transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''">
                            <div class="rounded-xl border border-white/15 bg-black/15 p-3"><p class="text-[8px] font-bold uppercase tracking-wider text-emerald-100/65">{{ $dashboardPeriodType === 'year' ? 'Total no ano' : 'Valor mensal' }}</p><p class="mt-1 text-base font-black tabular-nums">{{ number_format($dashboardProjectedMealAllowance, 2, ',', ' ') }} €</p></div>
                            <div class="rounded-xl border border-white/15 bg-black/15 p-3"><p class="text-[8px] font-bold uppercase tracking-wider text-emerald-100/65">{{ $dashboardPeriodType === 'year' ? 'Total registado' : 'Projeção anual' }}</p><p class="mt-1 text-base font-black tabular-nums">{{ number_format($dashboardMealAllowanceAnnualProjection, 2, ',', ' ') }} €</p></div>
                        </div>
                        <div class="relative space-y-2 transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''">
                            @forelse($dashboardSalaryContracts as $contract)
                                <div class="flex items-center justify-between gap-3 border-t border-white/10 pt-2"><span class="truncate text-[10px] font-semibold text-emerald-50">{{ $contract->description }}</span><span class="shrink-0 text-[10px] font-black tabular-nums">{{ number_format((float) ($contract->metadata['calculated_sa'] ?? 0), 2, ',', ' ') }} €</span></div>
                            @empty
                                <p class="text-xs text-emerald-100/70">Sem subsídio configurado nos contratos ativos.</p>
                            @endforelse
                        </div>
                        <div class="relative flex items-center justify-between gap-2 border-t border-white/15 pt-3 transition-all duration-300" :class="privacyMode ? 'blur-md select-none' : ''"><span class="text-[9px] font-bold uppercase tracking-wider text-emerald-100/75">Clica para abrir resumo e histórico</span><flux:icon name="arrow-up-right" class="size-4 text-emerald-100" /></div>
                    </div>
                </div>
                <button type="button" @click.stop="flipped = !flipped" :aria-label="flipped ? 'Mostrar frente do cartão' : 'Virar cartão'" :aria-pressed="flipped.toString()" title="Virar cartão" class="absolute bottom-3 right-3 z-30 flex size-8 items-center justify-center rounded-full border border-white/25 bg-black/20 text-white shadow-lg backdrop-blur-sm transition duration-300 hover:rotate-[-45deg] hover:bg-black/35 focus:outline-none focus:ring-2 focus:ring-white/70">
                    <span aria-hidden="true" class="text-[22px] font-semibold leading-none">↻</span>
                </button>
            </article>

        </div>

        {{-- RESUMO DE INVESTIMENTOS --}}
        <section x-data="{ investmentsExpanded: false }" id="dashboard-investimentos" class="dashboard-investments-summary col-span-full relative mt-6 w-full min-w-0 overflow-hidden rounded-[2rem] border border-sky-200 bg-gradient-to-br from-sky-50 via-white to-blue-50 shadow-lg shadow-sky-950/10 dark:border-sky-400/20 dark:from-slate-900 dark:via-slate-900 dark:to-blue-950/40">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-300 via-blue-400 to-cyan-300"></div>
            <div class="pointer-events-none absolute -right-20 -top-24 size-64 rounded-full bg-sky-400/15 blur-3xl"></div>

            <div class="relative px-5 py-4 sm:px-7 sm:py-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white shadow-md shadow-blue-500/20">
                            <flux:icon name="chart-bar" class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-black tracking-tight text-zinc-950 dark:text-white sm:text-lg">Resumo dos investimentos</h3>
                                <span class="rounded-full border border-sky-200 bg-sky-100 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider text-sky-800 dark:border-violet-500/20 dark:bg-sky-400/15 dark:text-violet-300">Portefólio</span>
                            </div>
                            <p class="mt-0.5 text-[11px] text-zinc-500 dark:text-zinc-400">
                                {{ $dashboardInvestmentsCount }} {{ $dashboardInvestmentsCount === 1 ? 'ativo' : 'ativos' }}
                                <span class="mx-1 text-zinc-300 dark:text-zinc-700">•</span>
                                {{ $dashboardInvestmentsLastUpdated ? 'Atualizado em '.$dashboardInvestmentsLastUpdated : 'Sem atualização registada' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('hub.investments') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-500 px-4 py-2.5 text-[11px] font-black text-white shadow-md shadow-blue-500/20 transition hover:-translate-y-0.5 hover:bg-blue-400">
                            Ver investimentos
                            <flux:icon name="arrow-up-right" class="size-4" />
                        </a>
                        <button type="button" x-on:click="investmentsExpanded = !investmentsExpanded" :aria-expanded="investmentsExpanded.toString()" aria-label="Mostrar composição dos investimentos" title="Mostrar composição dos investimentos" class="flex size-10 items-center justify-center rounded-xl border border-sky-300/60 bg-white/80 text-sky-700 shadow-sm transition hover:bg-sky-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 dark:border-sky-400/30 dark:bg-slate-800 dark:text-sky-300 dark:hover:bg-slate-700">
                            <flux:icon name="chevron-down" class="size-5 transition-transform duration-300" ::class="{ 'rotate-180': investmentsExpanded }" />
                        </button>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-y-4 border-t border-zinc-100 pt-4 dark:border-zinc-800 sm:grid-cols-4 sm:gap-0">
                    <div class="min-w-0 pr-3 sm:border-r sm:border-zinc-200 sm:pr-5 dark:sm:border-zinc-700">
                        <p class="text-[9px] font-extrabold uppercase tracking-widest text-sky-800 dark:text-violet-300">Valor atual</p>
                        <p class="mt-1 break-words text-xl font-black leading-tight tracking-tight tabular-nums text-zinc-950 dark:text-white sm:text-2xl" :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($portfolioValue) }}</p>
                        <p class="mt-0.5 text-[10px] text-zinc-500 dark:text-zinc-400">Valor de mercado</p>
                    </div>

                    <div class="min-w-0 pl-3 sm:border-r sm:border-zinc-200 sm:px-5 dark:sm:border-zinc-700">
                        <p class="text-[9px] font-extrabold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Capital investido</p>
                        <p class="mt-1 break-words text-xl font-black leading-tight tracking-tight tabular-nums text-zinc-900 dark:text-white sm:text-2xl" :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($dashboardInvestmentsTotalInvested) }}</p>
                        <p class="mt-0.5 text-[10px] text-zinc-500 dark:text-zinc-400">Total aplicado</p>
                    </div>

                    <div class="min-w-0 border-t border-zinc-100 pt-3 pr-3 dark:border-zinc-800 sm:border-t-0 sm:px-5 sm:pt-0">
                        <p class="text-[9px] font-extrabold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Lucro / prejuízo</p>
                        <div class="mt-1 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                            <p class="break-words text-xl font-black leading-tight tracking-tight tabular-nums {{ $dashboardInvestmentsProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}" :class="privacyMode ? 'blur-md select-none' : ''">
                                {{ $dashboardInvestmentsProfit >= 0 ? '+' : '' }}{{ $currentWs->money($dashboardInvestmentsProfit) }}
                            </p>
                            <span class="text-[11px] font-black tabular-nums {{ $dashboardInvestmentsProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}" :class="privacyMode ? 'blur-md select-none' : ''">
                                {{ $dashboardInvestmentsProfit >= 0 ? '+' : '' }}{{ number_format($dashboardInvestmentsProfitPercent, 2, ',', ' ') }}%
                            </span>
                        </div>
                        <p class="mt-0.5 text-[10px] text-zinc-500 dark:text-zinc-400">Rentabilidade</p>
                    </div>

                    <div class="min-w-0 border-t border-zinc-100 pl-3 pt-3 dark:border-zinc-800 sm:border-t-0 sm:pl-5 sm:pt-0">
                        <p class="text-[9px] font-extrabold uppercase tracking-widest text-blue-700 dark:text-blue-300">Ativos na carteira</p>
                        <p class="mt-1 text-xl font-black leading-tight tracking-tight tabular-nums text-zinc-950 dark:text-white sm:text-2xl">{{ $dashboardInvestmentsCount }}</p>
                        <p class="mt-0.5 text-[10px] text-zinc-500 dark:text-zinc-400">Instrumentos</p>
                    </div>
                </div>

                <div x-show="investmentsExpanded" x-cloak x-transition.opacity.duration.250ms class="mt-5 border-t border-sky-100 pt-5 dark:border-slate-700">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-sky-900 dark:text-sky-200">Composição da carteira</h4>
                            <p class="mt-1 text-[10px] text-zinc-500 dark:text-zinc-400">Detalhe por ativo, com valores calculados a partir das quantidades e cotações guardadas.</p>
                        </div>
                        <span class="rounded-full bg-sky-100 px-2.5 py-1 text-[10px] font-bold text-sky-800 dark:bg-sky-400/10 dark:text-sky-200">{{ $dashboardInvestmentsCount }} {{ $dashboardInvestmentsCount === 1 ? 'posição' : 'posições' }}</span>
                    </div>

                    @if(count($dashboardInvestments) > 0)
                        <div class="space-y-3">
                            @foreach($dashboardInvestments as $investment)
                                @php
                                    $investmentCurrentValue = round($investment['quantity'] * $investment['current_price'], 2);
                                    $investmentInvestedValue = round(($investment['quantity'] * $investment['average_price']) + $investment['fees'], 2);
                                    $investmentProfit = round($investmentCurrentValue - $investmentInvestedValue, 2);
                                    $investmentProfitPercent = $investmentInvestedValue > 0 ? ($investmentProfit / $investmentInvestedValue) * 100 : 0;
                                @endphp
                                <article wire:key="dashboard-investment-{{ $loop->index }}" class="rounded-2xl border border-sky-100 bg-white/80 p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/60">
                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h5 class="text-sm font-black text-zinc-900 dark:text-white">{{ $investment['name'] ?: ($investment['symbol'] ?: 'Ativo sem nome') }}</h5>
                                                @if($investment['symbol'])
                                                    <span class="rounded-md bg-sky-100 px-2 py-0.5 text-[10px] font-extrabold uppercase text-sky-800 dark:bg-sky-400/10 dark:text-sky-200">{{ $investment['symbol'] }}</span>
                                                @endif
                                                @if($investment['type'])
                                                    <span class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400">{{ $investment['type'] }}</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-[10px] text-zinc-500 dark:text-zinc-400">
                                                {{ rtrim(rtrim(number_format($investment['quantity'], 8, ',', ' '), '0'), ',') }} unidades
                                                <span class="mx-1">·</span>
                                                Cotação atual: <span class="font-semibold text-zinc-700 dark:text-zinc-200" :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($investment['current_price']) }}</span>
                                            </p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-x-5 gap-y-3 sm:grid-cols-4 lg:min-w-[580px]">
                                            <div><p class="text-[9px] font-bold uppercase tracking-wider text-zinc-500">Capital aplicado</p><p class="mt-1 text-xs font-black tabular-nums text-zinc-800 dark:text-zinc-100" :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($investmentInvestedValue) }}</p></div>
                                            <div><p class="text-[9px] font-bold uppercase tracking-wider text-zinc-500">Valor atual</p><p class="mt-1 text-xs font-black tabular-nums text-zinc-800 dark:text-zinc-100" :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($investmentCurrentValue) }}</p></div>
                                            <div><p class="text-[9px] font-bold uppercase tracking-wider text-zinc-500">Lucro / prejuízo</p><p class="mt-1 text-xs font-black tabular-nums {{ $investmentProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}" :class="privacyMode ? 'blur-md select-none' : ''">{{ $investmentProfit >= 0 ? '+' : '' }}{{ $currentWs->money($investmentProfit) }}</p></div>
                                            <div><p class="text-[9px] font-bold uppercase tracking-wider text-zinc-500">Rentabilidade</p><p class="mt-1 text-xs font-black tabular-nums {{ $investmentProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}" :class="privacyMode ? 'blur-md select-none' : ''">{{ $investmentProfit >= 0 ? '+' : '' }}{{ number_format($investmentProfitPercent, 2, ',', ' ') }}%</p></div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-sky-200 bg-white/60 px-4 py-8 text-center dark:border-slate-700 dark:bg-slate-900/40">
                            <flux:icon name="chart-bar" class="mx-auto size-7 text-sky-500" />
                            <p class="mt-2 text-xs font-bold text-zinc-700 dark:text-zinc-200">Ainda não tens investimentos registados.</p>
                            <p class="mt-1 text-[10px] text-zinc-500">Adiciona ativos na área de investimentos para veres aqui a composição da carteira.</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- MODAL DO CARTÃO BANCÁRIO --}}
        <div x-data="{ activeModalTab: 'summary' }" x-show="showSalaryHistory" x-cloak x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-950/80 p-3 backdrop-blur-sm sm:p-6" @click.self="showSalaryHistory = false" x-on:keydown.escape.window="showSalaryHistory = false; activeModalTab = 'summary'">
            <div x-show="showSalaryHistory" x-transition.scale class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900" @click.stop>
                <div class="flex items-center justify-between gap-4 border-b border-zinc-100 bg-gradient-to-r from-red-50 to-white p-5 dark:border-zinc-800 dark:from-red-950/30 dark:to-zinc-900 sm:p-6">
                    <div class="flex items-center gap-3"><span class="flex size-11 items-center justify-center rounded-2xl bg-red-600 text-white shadow-lg shadow-red-600/20"><flux:icon name="wallet" class="size-5" /></span><div><h3 class="text-lg font-black text-zinc-900 dark:text-white">Detalhes do saldo bancário</h3><p class="text-xs text-zinc-500">Conta, projeção e movimentos associados</p></div></div>
                    <button type="button" x-on:click="showSalaryHistory = false; activeModalTab = 'summary'" class="rounded-xl p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="Fechar"><flux:icon name="x-mark" class="size-5" /></button>
                </div>
                <div class="flex gap-2 border-b border-zinc-100 px-5 pt-4 dark:border-zinc-800 sm:px-6">
                    <button type="button" x-on:click="activeModalTab = 'summary'" :class="activeModalTab === 'summary' ? 'border-red-600 text-red-600 dark:text-red-300' : 'border-transparent text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'" class="border-b-2 px-3 pb-3 text-xs font-black uppercase tracking-wider transition">Resumo</button>
                    <button type="button" x-on:click="activeModalTab = 'history'" :class="activeModalTab === 'history' ? 'border-red-600 text-red-600 dark:text-red-300' : 'border-transparent text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'" class="border-b-2 px-3 pb-3 text-xs font-black uppercase tracking-wider transition">Histórico</button>
                </div>
                <div x-show="activeModalTab === 'summary'" x-cloak class="min-h-0 flex-1 space-y-6 overflow-y-auto p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-red-100 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-950/30"><p class="text-[10px] font-black uppercase tracking-wider text-red-600 dark:text-red-300">Saldo projetado</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($dashboardBankCardBalance, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">{{ $dashboardPeriodLabel }}</p></div>
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"><p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Saldo bancário</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($dashboardBankCardCurrentBalance, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">Saldo da conta no período selecionado</p></div>
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"><p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Salário líquido incluído</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($dashboardBankCardSalaryAmount, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">{{ $isCurrentDashboardPeriod ? 'Projeção dos contratos ativos' : 'Sem salário projetado em períodos anteriores' }}</p></div>
                    </div>
                    <div class="rounded-2xl border border-red-100 p-4 dark:border-red-900/50">
                        <h4 class="text-xs font-black uppercase tracking-wider text-red-700 dark:text-red-300">Como é calculado este cartão?</h4>
                        <div class="mt-3 space-y-2 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                            @if($isCurrentDashboardPeriod)
                                <p>O valor apresentado resulta da soma do saldo bancário atual com o salário líquido mensal estimado dos contratos de trabalho ativos.</p>
                                <p><strong>Fórmula:</strong> {{ number_format($dashboardBankCardCurrentBalance, 2, ',', ' ') }} € + {{ number_format($dashboardBankCardSalaryAmount, 2, ',', ' ') }} € = {{ number_format($dashboardBankCardBalance, 2, ',', ' ') }} €.</p>
                            @else
                                <p>Para períodos anteriores, o saldo é reconstruído a partir do saldo inicial da conta e dos movimentos bancários registados até ao final do período selecionado. Não é acrescentado o salário projetado atual.</p>
                            @endif
                            <p><strong>Importante:</strong> o salário projetado não significa que já tenha sido recebido. Consulta o separador Histórico para confirmar os movimentos efetivamente registados.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-zinc-500">Conta considerada no cálculo</h4>
                        @if($dashboardBankCardAccount)
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex flex-wrap items-start justify-between gap-2"><div><p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $dashboardBankCardAccount->bank_name ?: 'Conta bancária' }}</p><p class="mt-1 text-[10px] text-zinc-500">Titular: {{ $dashboardBankCardAccount->holder_name ?: 'Não indicado' }}</p></div><span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[9px] font-black uppercase text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Conta selecionada</span></div>
                                <div class="mt-3 grid grid-cols-2 gap-3 text-xs sm:grid-cols-3"><div><p class="text-[10px] text-zinc-500">Número de conta</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ $dashboardBankCardAccount->account_number ? '•••• '.substr(preg_replace('/\s+/', '', $dashboardBankCardAccount->account_number), -4) : 'Não indicado' }}</p></div><div><p class="text-[10px] text-zinc-500">IBAN</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ $dashboardBankCardAccount->iban ? substr(preg_replace('/\s+/', '', $dashboardBankCardAccount->iban), 0, 4).' •••• •••• '.substr(preg_replace('/\s+/', '', $dashboardBankCardAccount->iban), -4) : 'Não indicado' }}</p></div><div><p class="text-[10px] text-zinc-500">SWIFT / BIC</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ $dashboardBankCardAccount->swift ?: 'Não indicado' }}</p></div></div>
                            </div>
                        @else
                            <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Não existe uma conta bancária selecionada para apresentar os detalhes.</p>
                        @endif
                        <h4 class="pt-2 text-xs font-black uppercase tracking-wider text-zinc-500">Contratos considerados no salário projetado</h4>
                        @forelse($dashboardSalaryContracts as $contract)
                            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700"><div class="flex flex-wrap items-center justify-between gap-2"><div><p class="text-xs font-bold text-zinc-900 dark:text-white">{{ $contract->description }}</p><p class="mt-1 text-[10px] text-zinc-500">{{ $contract->metadata['position'] ?? 'Função não indicada' }} · {{ $contract->metadata['contract_type'] ?? 'Tipo de contrato não indicado' }}</p></div><p class="text-xs font-black text-red-600 dark:text-red-300">{{ number_format((float) $contract->projected_salary_net, 2, ',', ' ') }} € líquidos</p></div></div>
                        @empty
                            <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Não existem contratos ativos considerados no salário projetado.</p>
                        @endforelse
                    </div>
                </div>
                <div x-show="activeModalTab === 'history'" x-cloak class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5 sm:p-6">
                    <div><h4 class="text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-200">Movimentos bancários</h4><p class="mt-1 text-[10px] text-zinc-500">Entradas e despesas associadas à conta selecionada · {{ $dashboardPeriodLabel }}</p></div>
                    @forelse($dashboardBankCardIncomes as $movement)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-zinc-200 p-3 dark:border-zinc-700"><div class="min-w-0"><p class="truncate text-xs font-bold text-zinc-900 dark:text-white">{{ $movement->description ?: 'Entrada bancária' }}</p><p class="mt-1 text-[10px] text-zinc-500">{{ $movement->received_at?->format('d/m/Y') }}</p></div><p class="shrink-0 text-sm font-black text-emerald-600">+ {{ number_format((float) ($movement->amount_converted ?? $movement->amount), 2, ',', ' ') }} €</p></div>
                    @empty
                    @endforelse
                    @forelse($dashboardBankCardExpenses as $movement)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-zinc-200 p-3 dark:border-zinc-700"><div class="min-w-0"><p class="truncate text-xs font-bold text-zinc-900 dark:text-white">{{ $movement->description ?: 'Despesa bancária' }}</p><p class="mt-1 text-[10px] text-zinc-500">{{ $movement->spent_at?->format('d/m/Y') }}</p></div><p class="shrink-0 text-sm font-black text-red-600">− {{ number_format((float) ($movement->amount_converted ?? $movement->amount), 2, ',', ' ') }} €</p></div>
                    @empty
                    @endforelse
                    @if($dashboardBankCardIncomes->isEmpty() && $dashboardBankCardExpenses->isEmpty())
                        <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Não existem movimentos bancários registados nesta conta e período.</p>
                    @endif
                </div>
                <div class="border-t border-zinc-100 p-4 text-right dark:border-zinc-800"><button type="button" x-on:click="showSalaryHistory = false; activeModalTab = 'summary'" class="rounded-xl bg-zinc-900 px-5 py-2.5 text-xs font-black uppercase tracking-wider text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">Fechar</button></div>
            </div>
        </div>

        {{-- MODAL DO CARTÃO DE SUBSÍDIO DE ALIMENTAÇÃO --}}
        <div x-data="{ activeModalTab: 'summary' }" x-show="showMealHistory" x-cloak x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-950/80 p-3 backdrop-blur-sm sm:p-6" @click.self="showMealHistory = false" x-on:keydown.escape.window="showMealHistory = false; activeModalTab = 'summary'">
            <div x-show="showMealHistory" x-transition.scale class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900" @click.stop>
                <div class="flex items-center justify-between gap-4 border-b border-zinc-100 bg-gradient-to-r from-emerald-50 to-white p-5 dark:border-zinc-800 dark:from-emerald-950/40 dark:to-zinc-900 sm:p-6">
                    <div class="flex items-center gap-3"><span class="flex size-11 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/20"><flux:icon name="cake" class="size-5" /></span><div><h3 class="text-lg font-black text-zinc-900 dark:text-white">Detalhes do subsídio de alimentação</h3><p class="text-xs text-zinc-500">Projeção, contratos e pagamentos associados</p></div></div>
                    <button type="button" x-on:click="showMealHistory = false; activeModalTab = 'summary'" class="rounded-xl p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="Fechar"><flux:icon name="x-mark" class="size-5" /></button>
                </div>
                <div class="flex gap-2 border-b border-zinc-100 px-5 pt-4 dark:border-zinc-800 sm:px-6">
                    <button type="button" x-on:click="activeModalTab = 'summary'" :class="activeModalTab === 'summary' ? 'border-emerald-600 text-emerald-700 dark:text-emerald-300' : 'border-transparent text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'" class="border-b-2 px-3 pb-3 text-xs font-black uppercase tracking-wider transition">Resumo</button>
                    <button type="button" x-on:click="activeModalTab = 'history'" :class="activeModalTab === 'history' ? 'border-emerald-600 text-emerald-700 dark:text-emerald-300' : 'border-transparent text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'" class="border-b-2 px-3 pb-3 text-xs font-black uppercase tracking-wider transition">Histórico</button>
                </div>
                <div x-show="activeModalTab === 'summary'" x-cloak class="min-h-0 flex-1 space-y-6 overflow-y-auto p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30"><p class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">{{ $dashboardPeriodType === 'year' ? 'Total no ano selecionado' : 'Projeção mensal' }}</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($dashboardProjectedMealAllowance, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">{{ $dashboardPeriodType === 'year' ? 'Soma dos pagamentos registados no ano selecionado' : 'Soma dos valores mensais configurados' }}</p></div>
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"><p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">{{ $dashboardPeriodType === 'year' ? 'Total registado no ano' : 'Projeção anual' }}</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($dashboardMealAllowanceAnnualProjection, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">{{ $dashboardPeriodType === 'year' ? 'Soma dos pagamentos registados no ano selecionado' : 'Estimativa mensal × 11 meses' }}</p></div>
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"><p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Registos de pagamento</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ $projectedSalaryHistoryPayments->count() }}</p><p class="mt-1 text-[10px] text-zinc-500">Pagamentos salariais guardados</p></div>
                    </div>
                    <div class="rounded-2xl border border-emerald-100 p-4 dark:border-emerald-900/50">
                        <h4 class="text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Como é calculado este cartão?</h4>
                        <div class="mt-3 space-y-2 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                            <p>O valor mensal corresponde à soma do subsídio de alimentação configurado em cada contrato ativo. O valor diário e os dias de trabalho definidos no contrato determinam a projeção mensal.</p>
                            <p>A estimativa anual apresentada no cartão corresponde à projeção mensal × 11 meses, considerando que o subsídio poderá não ser pago durante o mês de férias.</p>
                            <p><strong>Importante:</strong> a projeção não confirma que o valor já tenha sido recebido. O estado e a data do pagamento devem ser consultados no separador Histórico.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-zinc-500">Configuração por contrato ativo</h4>
                        @forelse($dashboardSalaryContracts as $contract)
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex flex-wrap items-start justify-between gap-2"><div><p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $contract->description }}</p><p class="mt-1 text-[10px] text-zinc-500">Destino: {{ ($contract->metadata['meal_allowance_destination'] ?? '') === 'meal_card' ? 'Cartão de refeição' : 'Dinheiro / transferência' }}</p></div><p class="text-sm font-black text-emerald-600 dark:text-emerald-300">{{ number_format((float) ($contract->metadata['calculated_sa'] ?? 0), 2, ',', ' ') }} € / mês</p></div>
                                <div class="mt-3 grid grid-cols-2 gap-3 text-xs sm:grid-cols-4"><div><p class="text-[10px] text-zinc-500">Valor diário</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ number_format((float) ($contract->metadata['meal_allowance'] ?? 0), 2, ',', ' ') }} €</p></div><div><p class="text-[10px] text-zinc-500">Dias configurados</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ (int) ($contract->metadata['working_days'] ?? 0) }}</p></div><div><p class="text-[10px] text-zinc-500">Frequência</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">Mensal</p></div><div><p class="text-[10px] text-zinc-500">Estado</p><p class="mt-1 font-semibold text-emerald-600 dark:text-emerald-300">Ativo</p></div></div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Não existem subsídios configurados em contratos ativos.</p>
                        @endforelse
                    </div>
                </div>
                <div x-show="activeModalTab === 'history'" x-cloak class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5 sm:p-6">
                    <div><h4 class="text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-200">Histórico de pagamentos salariais</h4><p class="mt-1 text-[10px] text-zinc-500">Inclui pagamentos pagos e pendentes e o subsídio de alimentação registado em cada um.</p></div>
                    @forelse($projectedSalaryHistoryPayments as $payment)
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="flex flex-wrap items-center justify-between gap-2"><div><p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $payment->recurringIncome?->description ?? 'Subsídio de alimentação' }}</p><p class="mt-1 text-[10px] text-zinc-500">Período: {{ str_pad((string) $payment->payment_month, 2, '0', STR_PAD_LEFT) }}/{{ $payment->payment_year }} · {{ $payment->paid_at?->format('d/m/Y') ?? 'Sem data de pagamento' }}</p></div><span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase {{ $payment->status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">{{ $payment->status === 'paid' ? 'Pago' : 'Pendente' }}</span></div>
                            <div class="mt-3 flex flex-wrap items-end justify-between gap-3"><div><p class="text-[10px] text-zinc-500">Subsídio registado</p><p class="mt-1 text-lg font-black text-emerald-600 dark:text-emerald-300">{{ number_format((float) $payment->meal_allowance, 2, ',', ' ') }} €</p></div><p class="text-[10px] text-zinc-500">O estado indica se o pagamento salarial associado foi marcado como pago.</p></div>
                        </div>
                    @empty
                        <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Ainda não existem registos mensais de pagamento com subsídio de alimentação.</p>
                    @endforelse
                </div>
                <div class="border-t border-zinc-100 p-4 text-right dark:border-zinc-800"><button type="button" x-on:click="showMealHistory = false; activeModalTab = 'summary'" class="rounded-xl bg-zinc-900 px-5 py-2.5 text-xs font-black uppercase tracking-wider text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">Fechar</button></div>
            </div>
        </div>
    </section>

    {{-- 6. SECÇÃO DE INTELIGÊNCIA E PREVISÃO --}}
    <div id="dashboard-cfo" class="scroll-mt-28 grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- CARD: PREVISÃO INTELIGENTE (ESTILO BLACK GLASS) --}}
        <div class="lg:col-span-2 relative overflow-hidden bg-zinc-950 text-white rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            {{-- Efeito de Luz IA ao fundo --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/10 blur-[100px] rounded-full -mr-20 -mt-20"></div>

            <div class="relative z-10 p-8 flex flex-col md:flex-row justify-between gap-10">
                <div class="space-y-6 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-brand-500/20 rounded-lg">
                            <flux:icon name="sparkles" class="size-5 text-brand-400" />
                        </div>
                        <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Projeção de Fim de Mês</h2>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400 font-medium mb-2 uppercase tracking-widest">Saldo Estimado</p>
                        <h3 class="text-5xl font-black tracking-tighter {{ $projectedBalance >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            <span :class="privacyMode ? 'blur-xl select-none' : ''" class="transition-all duration-700 inline-block">
                                {{ $currentWs->money($projectedBalance) }}
                            </span>
                        </h3>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-white/5 rounded-2xl border border-white/5 backdrop-blur-sm">
                        <div class="relative flex-shrink-0">
                            <div class="size-3 rounded-full {{ $projectionStatus == 'critical' ? 'bg-red-500' : ($projectionStatus == 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}"></div>
                            <div class="absolute inset-0 size-3 rounded-full {{ $projectionStatus == 'critical' ? 'bg-red-500' : ($projectionStatus == 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }} animate-ping"></div>
                        </div>
                        <p class="text-xs font-bold text-zinc-300">
                            @if($projectionStatus == 'critical') A previsão aponta para saldo negativo no fim do mês. Revê os pagamentos previstos.
                            @elseif($projectionStatus == 'warning') O saldo final previsto fica abaixo de 10% do saldo bancário atual.
                            @else Com os movimentos previstos, deverás manter saldo positivo até ao fim do mês. @endif
                        </p>
                    </div>
                    <p class="text-[10px] leading-relaxed text-zinc-500">
                        Considera as contas incluídas no saldo global, salários por receber e dívidas com vencimento até ao fim do mês. Subscrições sem conta bancária associada não entram nesta previsão.
                    </p>
                </div>

                {{-- Mini Métricas de Apoio --}}
                <div class="grid grid-cols-2 content-center gap-x-5 gap-y-6 border-l border-white/10 pl-8 min-w-[220px]">
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-1">Saldo Atual</p>
                        <p class="text-xl font-black text-zinc-200">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                {{ $currentWs->money($totalBankBalance) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-1">Entradas Previstas</p>
                        <p class="text-xl font-black text-emerald-400">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                {{ $currentWs->money($projectedIncome) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-1">Saídas Previstas</p>
                        <p class="text-xl font-black text-red-300">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                {{ $currentWs->money($projectedExpenses) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-1">Margem Diária</p>
                        <p class="text-xl font-black text-sky-300">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                {{ $currentWs->money($projectionDailyAvailable) }}
                            </span>
                        </p>
                        <p class="col-span-2 text-[9px] leading-relaxed text-zinc-500">Saldo previsto dividido pelos dias restantes do mês.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: PATRIMÓNIO TOTAL --}}
        <div class="relative flex flex-col justify-between overflow-hidden rounded-[2.5rem] border border-emerald-500/20 bg-gradient-to-br from-emerald-950 via-zinc-950 to-zinc-900 p-7 text-white shadow-2xl group">
            <div class="absolute -right-12 -top-12 size-44 rounded-full bg-emerald-500/10 blur-3xl transition-all duration-700 group-hover:bg-emerald-400/20"></div>
            <div class="relative z-10">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex size-12 items-center justify-center rounded-2xl border border-emerald-400/20 bg-emerald-500/10 text-emerald-400">
                        <flux:icon name="banknotes" variant="solid" class="size-6" />
                    </div>
                    <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-300">Visão global</span>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-emerald-300/70">Património Total</p>
                <h3 class="mt-2 text-4xl font-black tracking-tighter text-white">
                    <span :class="privacyMode ? 'blur-xl select-none' : ''" class="inline-block transition-all duration-500">{{ $currentWs->money($totalPatrimony) }}</span>
                </h3>
                <p class="mt-2 text-xs font-medium leading-relaxed text-zinc-400">O valor acumulado entre contas, investimentos e objetivos de poupança.</p>
            </div>
            <div class="relative z-10 mt-8 space-y-4 border-t border-white/10 pt-5">
                <div class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-2 text-xs font-semibold text-zinc-300"><span class="size-2 rounded-full bg-sky-400"></span> Contas bancárias</span>
                    <span class="text-sm font-black text-white"><span :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($totalBankBalance) }}</span></span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-2 text-xs font-semibold text-zinc-300"><span class="size-2 rounded-full bg-violet-400"></span> Investimentos</span>
                    <span class="text-sm font-black text-white"><span :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($portfolioValue) }}</span></span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-2 text-xs font-semibold text-zinc-300"><span class="size-2 rounded-full bg-emerald-400"></span> Objetivos de poupança</span>
                    <span class="text-sm font-black text-white"><span :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($totalSaved) }}</span></span>
                </div>
            </div>
        </div>

    </div>

    {{-- NOVOS INDICADORES FINANCEIROS --}}
    <section id="dashboard-novos-indicadores" class="scroll-mt-28 mt-8 space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <span class="w-fit rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-[10px] font-bold text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                {{ $dashboardPeriodType === 'year' ? 'Comparação anual' : 'Comparação mensal' }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <article class="group relative overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-emerald-500/20 dark:from-emerald-950/30 dark:to-zinc-900">
                <div class="absolute -right-6 -top-6 size-20 rounded-full bg-emerald-400/10 blur-xl transition group-hover:scale-150"></div>
                <p class="relative text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Taxa de poupança</p>
                <p class="relative mt-3 text-2xl font-black tabular-nums {{ $indicatorSavingsRate !== null && $indicatorSavingsRate >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-400' }}">
                    <span :class="privacyMode ? 'blur-md select-none' : ''">{{ $indicatorSavingsRate !== null ? number_format($indicatorSavingsRate, 1, ',', '.') . '%' : '—' }}</span>
                </p>
                <p class="relative mt-1 text-[10px] text-zinc-500">Receitas que ficam depois das despesas</p>
            </article>

            <article class="group relative overflow-hidden rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-blue-500/20 dark:from-blue-950/30 dark:to-zinc-900">
                <div class="absolute -right-6 -top-6 size-20 rounded-full bg-blue-400/10 blur-xl transition group-hover:scale-150"></div>
                <p class="relative text-[10px] font-black uppercase tracking-wider text-blue-700 dark:text-blue-400">Orçamento utilizado</p>
                <p class="relative mt-3 text-2xl font-black tabular-nums text-blue-700 dark:text-blue-300">
                    <span :class="privacyMode ? 'blur-md select-none' : ''">{{ $indicatorBudgetUsage !== null ? number_format($indicatorBudgetUsage, 1, ',', '.') . '%' : '—' }}</span>
                </p>
                @if($indicatorBudgetUsage !== null)
                    <div class="relative mt-2 h-1.5 overflow-hidden rounded-full bg-blue-100 dark:bg-blue-950">
                        <div class="h-full rounded-full {{ $indicatorBudgetUsage > 100 ? 'bg-rose-500' : 'bg-blue-500' }} transition-all duration-700" style="width: {{ min(100, max(0, $indicatorBudgetUsage)) }}%"></div>
                    </div>
                    <p class="relative mt-1 text-[10px] {{ $indicatorBudgetUsage > 100 ? 'text-rose-600' : 'text-zinc-500' }}">{{ $indicatorBudgetUsage > 100 ? 'Limites ultrapassados' : 'Face aos limites definidos' }}</p>
                @else
                    <p class="relative mt-1 text-[10px] text-zinc-500">Ainda não existem limites de orçamento</p>
                @endif
            </article>

            <article class="group relative overflow-hidden rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-violet-500/20 dark:from-violet-950/30 dark:to-zinc-900">
                <div class="absolute -right-6 -top-6 size-20 rounded-full bg-violet-400/10 blur-xl transition group-hover:scale-150"></div>
                <p class="relative text-[10px] font-black uppercase tracking-wider text-violet-700 dark:text-violet-400">Despesa média diária</p>
                <p class="relative mt-3 text-2xl font-black tabular-nums text-violet-700 dark:text-violet-300">
                    <span :class="privacyMode ? 'blur-md select-none' : ''">{{ $currentWs->money($indicatorDailyAverage) }}</span>
                </p>
                <p class="relative mt-1 text-[10px] text-zinc-500">Média dos {{ $indicatorDaysElapsed }} dias decorridos</p>
            </article>

            <article class="group relative overflow-hidden rounded-2xl border border-rose-200 bg-gradient-to-br from-rose-50 to-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-rose-500/20 dark:from-rose-950/30 dark:to-zinc-900">
                <div class="absolute -right-6 -top-6 size-20 rounded-full bg-rose-400/10 blur-xl transition group-hover:scale-150"></div>
                <p class="relative text-[10px] font-black uppercase tracking-wider text-rose-700 dark:text-rose-400">Variação das despesas</p>
                <p class="relative mt-3 text-2xl font-black tabular-nums {{ $indicatorExpenseChange === null ? 'text-zinc-500' : ($indicatorExpenseChange > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                    <span :class="privacyMode ? 'blur-md select-none' : ''">{{ $indicatorExpenseChange !== null ? (($indicatorExpenseChange > 0 ? '+' : '') . number_format($indicatorExpenseChange, 1, ',', '.') . '%') : '—' }}</span>
                </p>
                <p class="relative mt-1 text-[10px] text-zinc-500">{{ $dashboardPeriodType === 'year' ? 'Face ao ano anterior' : 'Face ao mês anterior' }}</p>
            </article>

            <article class="group relative overflow-hidden rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-amber-500/20 dark:from-amber-950/30 dark:to-zinc-900">
                <div class="absolute -right-6 -top-6 size-20 rounded-full bg-amber-400/10 blur-xl transition group-hover:scale-150"></div>
                <p class="relative text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400">Variação das receitas</p>
                <p class="relative mt-3 text-2xl font-black tabular-nums {{ $indicatorIncomeChange === null ? 'text-zinc-500' : ($indicatorIncomeChange >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400') }}">
                    <span :class="privacyMode ? 'blur-md select-none' : ''">{{ $indicatorIncomeChange !== null ? (($indicatorIncomeChange > 0 ? '+' : '') . number_format($indicatorIncomeChange, 1, ',', '.') . '%') : '—' }}</span>
                </p>
                <p class="relative mt-1 text-[10px] text-zinc-500">{{ $dashboardPeriodType === 'year' ? 'Face ao ano anterior' : 'Face ao mês anterior' }}</p>
            </article>
        </div>
        <p class="text-[10px] leading-relaxed text-zinc-400">A taxa de poupança considera as receitas e despesas do período. A comparação utiliza o mês ou ano imediatamente anterior. Quando não existe histórico ou orçamento suficiente, o indicador apresenta —.</p>
    </section>

    {{-- BANNER CFO INTELIGENTE (DESTAQUE MÁXIMO) --}}
<a href="{{ route('ai') }}" wire:navigate
   class="group relative flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden
          bg-gradient-to-br from-zinc-950 via-zinc-900 to-brand-900
          p-8 rounded-[2.5rem] border border-brand-500/30 shadow-2xl shadow-brand-500/10
          hover:scale-[1.01] transition-all duration-300">

    <div class="absolute -top-16 -left-16 size-64 bg-brand-500/20 blur-[100px] rounded-full group-hover:bg-brand-500/30 transition-all duration-700"></div>
    <div class="absolute -bottom-10 right-10 size-40 bg-purple-500/10 blur-[80px] rounded-full"></div>

    <div class="relative z-10 flex items-center gap-5">
        <div class="relative flex-shrink-0">
            <div class="size-16 rounded-3xl bg-brand-500/20 border border-brand-400/30 flex items-center justify-center">
                <flux:icon name="sparkles" variant="solid" class="size-8 text-brand-400 animate-pulse" />
            </div>
            <span class="absolute -top-2 -right-2 text-[8px] font-black bg-emerald-500 text-white px-2 py-0.5 rounded-full uppercase tracking-widest shadow-lg">Novo</span>
        </div>

        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-400">CFO Inteligente</span>
            <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter leading-none mt-1">
                O teu consultor financeiro com IA
            </h2>
            <p class="text-xs text-zinc-400 font-medium mt-2 max-w-md">
                Análise automática do teu património, alertas de risco e um diagnóstico escrito só para ti.
            </p>
        </div>
    </div>

    <div class="relative z-10 flex items-center gap-4 flex-shrink-0">
        @if(isset($overallScore))
            <div class="hidden sm:flex flex-col items-center justify-center size-16 rounded-2xl bg-white/5 border border-white/10">
                <span class="text-lg font-black text-white">{{ $overallScore }}%</span>
                <span class="text-[8px] font-black uppercase text-zinc-500">Saúde</span>
            </div>
        @endif

        <span class="flex items-center gap-2 px-6 py-3 bg-brand-500 group-hover:bg-brand-400 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg shadow-brand-500/30 transition-colors">
            Ver Relatório
            <flux:icon name="arrow-right" class="size-4 group-hover:translate-x-1 transition-transform" />
        </span>
    </div>
</a>




    {{-- 2. BARRA DE NÍVEL E XP (DESIGN INTEGRADO) --}}
    <div id="dashboard-experiencia" class="scroll-mt-28 group relative flex items-center gap-6 bg-gradient-to-r from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-5 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden">
        {{-- Efeito Decorativo de Fundo --}}
        <div class="absolute -right-10 -top-10 size-40 bg-brand-500/5 blur-3xl rounded-full group-hover:bg-brand-500/10 transition-all duration-700"></div>

        {{-- Badge de Nível --}}
        <div class="relative flex-shrink-0">
            <div class="flex flex-col items-center justify-center w-20 h-20 rounded-[1.8rem] bg-brand-600 text-white shadow-2xl shadow-brand-500/40 relative z-10">
                <span class="text-[9px] font-black uppercase opacity-60 tracking-tighter">Nível</span>
                <span class="text-3xl font-black leading-none">{{ auth()->user()->level }}</span>
            </div>
            {{-- Indicador de Progresso Circular em volta do nível (CSS Subtil) --}}
            <div class="absolute inset-0 rounded-[1.8rem] border-2 border-brand-500/20 scale-110"></div>
        </div>

        <div class="flex-1 space-y-3">
            <div class="flex justify-between items-end">
                <div>
                    <h4 class="text-xs font-black uppercase text-zinc-500 tracking-[0.15em]">Experiência Financeira</h4>
                    <p class="text-[10px] text-zinc-400 font-bold mt-0.5">Faltam {{ 1000 - (auth()->user()->xp % 1000) }} XP para o próximo escalão</p>
                </div>
                <div class="text-right">
                    <span class="text-xs font-black text-brand-600 dark:text-brand-400 uppercase italic">{{ (auth()->user()->xp % 1000) / 10 }}%</span>
                </div>
            </div>

            {{-- Barra de Progresso Estilizada --}}
            <div class="relative h-3 w-full bg-zinc-200/50 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-100 dark:border-zinc-700 shadow-inner">
                <div class="h-full bg-brand-500 rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(59,130,246,0.6)]" style="width: {{ (auth()->user()->xp % 1000) / 10 }}%"></div>
            </div>

            {{-- Saúde Financeira integrada na Experiência Financeira --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-zinc-200/70 dark:border-zinc-700/70 pt-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $overallScore > 70 ? 'bg-emerald-500/10 text-emerald-500' : ($overallScore > 40 ? 'bg-amber-500/10 text-amber-500' : 'bg-red-500/10 text-red-500') }}">
                        <flux:icon name="heart" variant="solid" class="size-5" />
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Saúde Financeira</span>
                            <span class="text-sm font-black {{ $overallScore > 70 ? 'text-emerald-500' : ($overallScore > 40 ? 'text-amber-500' : 'text-red-500') }}">{{ $overallScore }}%</span>
                        </div>
                        <p class="text-[10px] text-zinc-400 font-medium">Baseado no teu rácio de poupança e cumprimento de orçamentos.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Medalhas/Badges --}}
        <div class="hidden lg:flex items-center gap-3 pl-6 border-l border-zinc-200 dark:border-zinc-800">
           @forelse(collect(auth()->user()->badges ?? [])->take(3) as $badge)
                <div title="{{ $badge->name }}" class="size-10 rounded-2xl flex items-center justify-center text-xl shadow-lg hover:scale-110 transition-transform cursor-help" style="background: {{ $badge->color }}15; border: 1px solid {{ $badge->color }}30;">
                    {{ $badge->icon }}
                </div>
            @empty
                <span class="text-[10px] text-zinc-400 uppercase font-black italic w-24 text-center leading-tight">Sem medalhas este mês</span>
            @endforelse
        </div>
    </div>


    {{-- 8. MOVIMENTOS RECENTES (ESTILO TIMELINE) --}}
    <div id="dashboard-movimentos" class="scroll-mt-28 glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Fluxo de Caixa</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Movimentos Recentes</p>
            </div>
            <flux:button variant="ghost" size="sm" class="rounded-xl text-[10px] font-black uppercase tracking-widest" href="{{ url('/movimentos') }}" wire:navigate>Ver Histórico Completo</flux:button>
        </div>

        <div class="max-h-[504px] overflow-y-auto overscroll-contain divide-y divide-zinc-100 dark:divide-zinc-800" style="scrollbar-width: thin;">
            @forelse ($recentMovements as $movement)
                <article wire:key="dashboard-{{ $movement['key'] }}" class="border-b border-zinc-100 last:border-0 dark:border-zinc-800">
                    <div class="flex min-h-[84px] flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $movement['direction'] === 'in' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30' : ($movement['direction'] === 'company' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30' : 'bg-rose-100 text-rose-600 dark:bg-rose-900/30') }}">
                                <flux:icon name="{{ $movement['type'] === 'income' ? 'arrow-down-left' : ($movement['type'] === 'expense' ? 'arrow-up-right' : ($movement['type'] === 'subscription' ? 'clock' : 'arrows-right-left')) }}" class="size-5" />
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-bold text-zinc-900 dark:text-white">{{ $movement['title'] }}</p>
                                    <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[9px] font-black uppercase text-zinc-500 dark:bg-zinc-800">{{ $movement['type_label'] }}</span>
                                </div>
                                <p class="mt-1 truncate text-xs text-zinc-500">{{ $movement['detail'] }} · {{ $movement['date']?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 sm:justify-end">
                            <span class="text-[10px] font-semibold text-zinc-400">{{ $movement['status'] }}</span>
                            <p class="text-base font-black tabular-nums {{ $movement['direction'] === 'in' ? 'text-emerald-600' : ($movement['direction'] === 'company' ? 'text-blue-600' : 'text-rose-600') }}">
                                {{ $movement['direction'] === 'in' ? '+' : ($movement['direction'] === 'company' ? '' : '−') }}{{ number_format($movement['amount'], 2, ',', '.') }} €
                            </p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-5 py-14 text-center">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800"><flux:icon name="inbox" class="size-7" /></div>
                    <h3 class="mt-4 text-sm font-black text-zinc-900 dark:text-white">Não existem movimentos registados</h3>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 9. MODAL: COLABORAÇÃO (VISUAL SaaS) --}}
    <flux:modal name="collab-modal" position="center" class="md:w-[500px] !p-0 overflow-hidden">
        <div class="bg-white dark:bg-zinc-950">

            {{-- HEADER --}}
            <div class="p-8 pb-0 text-center space-y-2">
                <flux:heading size="xl" class="font-black uppercase italic tracking-tighter">Gerir Espaços</flux:heading>
                <p class="text-xs text-zinc-500 font-medium">Cria a tua empresa ou junta-te a um espaço existente.</p>
            </div>

            <div class="p-8 space-y-6">

                {{-- OPÇÃO 1: CRIAR / ENTRAR NA MINHA EMPRESA --}}
                <a href="{{ route('hub.business.gateway') }}" wire:navigate
                    class="group flex items-center gap-5 p-5 bg-zinc-950 hover:bg-zinc-900 border border-zinc-800 rounded-2xl transition-all duration-200 hover:border-brand-500/40 hover:shadow-lg hover:shadow-brand-500/10">
                    <div class="flex items-center justify-center size-12 rounded-xl bg-brand-500/15 border border-brand-500/20 shrink-0 group-hover:bg-brand-500/25 transition-colors">
                        <flux:icon name="building-office-2" class="size-6 text-brand-400" />
                    </div>
                    <div class="flex-1 text-left">
                        <p class="text-sm font-black text-white uppercase tracking-tight">Criar / Gerir a minha empresa</p>
                        <p class="text-[10px] text-zinc-500 font-medium mt-0.5">Acede ao Hub Business como CEO ou colaborador</p>
                    </div>
                    <flux:icon name="arrow-right" class="size-4 text-zinc-600 group-hover:text-brand-400 group-hover:translate-x-0.5 transition-all" />
                </a>

                {{-- DIVISOR --}}
                <div class="relative">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-zinc-100 dark:border-zinc-800"></div></div>
                    <div class="relative flex justify-center">
                        <span class="bg-white dark:bg-zinc-950 px-3 text-[10px] font-semibold text-zinc-400 uppercase tracking-widest">ou partilha / junta-te</span>
                    </div>
                </div>

                {{-- CÓDIGO DE CONVITE (partilhar) --}}
                <div class="p-6 bg-zinc-900 rounded-2xl border border-zinc-800 text-center space-y-3 relative overflow-hidden">
                    <div class="absolute inset-0 bg-brand-500/3 blur-3xl rounded-full"></div>
                    <h4 class="relative z-10 text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">O Teu Código de Convite</h4>
                    <div class="relative z-10 flex items-center justify-center gap-4">
                        <span class="text-3xl font-mono font-black text-brand-500 uppercase tracking-[0.3em]">{{ $currentWs->invite_code ?: '--------' }}</span>
                        <flux:button wire:click="generateInviteCode" icon="arrow-path" variant="ghost" size="sm" class="text-zinc-500 hover:text-brand-500" />
                    </div>
                </div>

                {{-- ENTRAR NUM ESPAÇO --}}
                <div class="space-y-2">
                    <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest px-1">Entrar com código</p>
                    <div class="flex gap-2 p-2 bg-zinc-100 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <flux:input wire:model="inviteCodeInput" placeholder="INSERIR CÓDIGO" class="flex-1 !bg-transparent border-none uppercase font-mono font-black tracking-widest" />
                        <flux:button wire:click="joinWorkspace" variant="primary" class="rounded-xl px-6 font-black uppercase text-[10px]">Entrar</flux:button>
                    </div>
                </div>

                <flux:button x-on:click="$dispatch('modal-close')" variant="ghost" class="w-full font-bold text-zinc-400">Fechar</flux:button>
            </div>
        </div>
    </flux:modal>

{{-- 10. MODAL: EXPORTAÇÃO E AUDITORIA PDF --}}
<flux:modal name="export-pdf-modal" position="center" class="md:w-[550px] !p-0 overflow-hidden">
    <div class="relative bg-white dark:bg-zinc-900">
        {{-- Faixa de Cor Superior (Branding) --}}
        <div class="h-1.5 w-full bg-brand-600"></div>

        <div class="p-8 space-y-8">
            {{-- HEADER DO MODAL --}}
            <div class="flex flex-col items-center text-center gap-4">
                <div class="size-16 bg-zinc-950 rounded-[2rem] border border-zinc-800 flex items-center justify-center shadow-2xl">
                    <flux:icon name="document-arrow-down" class="size-8 text-brand-400 animate-bounce" />
                </div>
                <div>
                    <h2 class="text-2xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Gerar Relatório de Auditoria</h2>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em] mt-2 italic">Exportação Certificada • PDF Profissional</p>
                </div>
            </div>

            <div class="space-y-6">
                {{-- PRESETS DE DATA (Ação Rápida - Agora definem Início e Fim) --}}
                <div class="flex items-center justify-between gap-2 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                    <button type="button"
                        wire:click="setExportPeriod('this_month')"
                        class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl hover:bg-white dark:hover:bg-zinc-700 transition-all shadow-sm">Mês Atual</button>

                    <button type="button"
                        wire:click="setExportPeriod('last_month')"
                        class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl hover:bg-white dark:hover:bg-zinc-700 transition-all shadow-sm">Mês Anterior</button>

                    <button type="button"
                        wire:click="setExportPeriod('this_year')"
                        class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl hover:bg-white dark:hover:bg-zinc-700 transition-all shadow-sm">Ano {{ date('Y') }}</button>
                </div>

                {{-- INPUTS DE DATA --}}
                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="exportStart" type="date" label="Data de Início" class="font-bold" />
                    <flux:input wire:model="exportEnd" type="date" label="Data de Fim" class="font-bold" />
                </div>

                {{-- CONFIGURAÇÕES DO CONTEÚDO --}}
                <div class="bg-zinc-50 dark:bg-zinc-950 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 space-y-5 shadow-inner">
                    <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                        <flux:icon name="adjustments-horizontal" class="size-4 text-zinc-400" />
                        <span class="text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">Parâmetros do Documento</span>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        {{-- Toggle Gastos --}}
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                            <div class="flex flex-col">
                                <span class="text-xs font-black dark:text-white uppercase tracking-tighter italic">Listagem de Gastos</span>
                                <span class="text-[9px] text-zinc-500 font-medium italic">Incluir histórico detalhado de saídas.</span>
                            </div>
                            <flux:checkbox wire:model.live="exportExpenses" variant="toggle" color="emerald" />
                        </div>

                        {{-- Toggle Receitas --}}
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                            <div class="flex flex-col">
                                <span class="text-xs font-black dark:text-white uppercase tracking-tighter italic">Fluxo de Receitas</span>
                                <span class="text-[9px] text-zinc-500 font-medium italic">Resumo consolidado de ganhos.</span>
                            </div>
                            <flux:checkbox wire:model.live="exportIncomes" variant="toggle" color="emerald" />
                        </div>

                        {{-- Opções Extra --}}
                        <div class="flex items-center gap-4 pt-2">
                             <flux:checkbox wire:model.live="includeReceipts" label="Comprovativos" class="font-bold text-[10px] uppercase italic" />
                             <flux:checkbox wire:model.live="hideDescriptions" label="Ocultar Nomes" class="font-bold text-[10px] uppercase italic" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTÕES DE ACÇÃO --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close class="flex-1">
                    <button type="button" class="w-full h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all italic border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700">
                        Cancelar
                    </button>
                </flux:modal.close>

                <button type="button"
                    wire:click="downloadCustomPdf"
                    wire:loading.attr="disabled"
                    class="flex-[2] h-14 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-[0.1em] text-xs shadow-xl shadow-brand-600/20 active:scale-95 transition-all flex items-center justify-center gap-3 group">

                    <span wire:loading.remove wire:target="downloadCustomPdf" class="flex items-center gap-2">
                        <flux:icon name="arrow-down-tray" class="size-4 group-hover:translate-y-1 transition-transform" />
                        Gerar Protocolo PDF
                    </span>

                    <span wire:loading wire:target="downloadCustomPdf" class="flex items-center gap-3">
                        <div class="size-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                        A Processar...
                    </span>
                </button>
            </div>
        </div>

        {{-- Rodapé de Segurança --}}
        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 text-center border-t border-zinc-100 dark:border-zinc-800">
            <p class="text-[8px] font-black text-zinc-400 uppercase tracking-[0.3em]">Ambiente Seguro • Encriptação de Ponta-a-Ponta</p>
        </div>
    </div>
</flux:modal>






@php
        $report = $this->dailyReport;
        $isPremium = $report['is_premium'] ?? false;
    @endphp

    {{-- 11. TERMINAL DE OPERAÇÕES 24H (BLACK OPS ⭐) --}}
    <div id="dashboard-resumo-conta" class="account-overview-widget scroll-mt-28 relative mt-12 overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-xl transition-all duration-500 dark:border-zinc-800 dark:bg-zinc-950">
        <div class="account-overview-header relative overflow-hidden border-b border-blue-100 p-6 dark:border-blue-900/40 md:p-8">
            <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-2 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-blue-700 dark:text-blue-300"><span class="size-2 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]"></span>Painel geral da conta</div>
                    <h2 class="mt-1 text-2xl font-black tracking-tight text-zinc-900 dark:text-white md:text-3xl">Resumo da Conta</h2>
                    <p class="mt-1 text-sm text-zinc-500">Visão 360º · {{ $currentWs->name }} · Todos os registos guardados</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Atualizado {{ now()->format('H:i') }}</span>
                    <button
                        type="button"
                        wire:click="refreshAccountOverview"
                        wire:loading.attr="disabled"
                        wire:target="refreshAccountOverview"
                        class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-3.5 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100 disabled:cursor-wait disabled:opacity-60 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300 dark:hover:bg-blue-500/20"
                        title="Atualizar os dados do resumo da conta"
                    >
                        <span wire:loading.remove wire:target="refreshAccountOverview" aria-hidden="true">↻</span>
                        <span wire:loading wire:target="refreshAccountOverview" class="size-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                        <span wire:loading.remove wire:target="refreshAccountOverview">Atualizar dados</span>
                        <span wire:loading wire:target="refreshAccountOverview">A atualizar...</span>
                    </button>
                    <span class="rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $accountOverview['account_count'] }} contas bancárias</span>
                </div>
            </div>
        </div>

        <div class="account-overview-content space-y-8 p-5 md:p-8">
            {{-- Indicadores principais --}}
            <section>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Situação financeira</h3>
                    <span class="text-xs text-zinc-500">Valores acumulados e atuais</span>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/5 account-overview-kpi">
                        <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Saldo em contas</p>
                        <p class="mt-2 text-2xl font-black text-emerald-800 dark:text-emerald-300">{{ $currentWs->money($totalBankBalance) }}</p>
                        <p class="mt-1 text-xs text-emerald-700/70 dark:text-emerald-400/70">Contas incluídas no património</p>
                    </div>
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-500/20 dark:bg-blue-500/5 account-overview-kpi">
                        <p class="text-xs font-semibold text-blue-700 dark:text-blue-400">Investimentos · valor atual</p>
                        <p class="mt-2 text-2xl font-black text-blue-800 dark:text-blue-300">{{ $currentWs->money($accountOverview['investment_value']) }}</p>
                        <p class="mt-1 text-xs text-blue-700/70 dark:text-blue-400/70">{{ $accountOverview['investment_count'] }} ativos registados</p>
                    </div>
                    <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4 dark:border-violet-500/20 dark:bg-violet-500/5 account-overview-kpi">
                        <p class="text-xs font-semibold text-violet-700 dark:text-violet-400">Poupanças em objetivos</p>
                        <p class="mt-2 text-2xl font-black text-violet-800 dark:text-violet-300">{{ $currentWs->money($accountOverview['goal_saved']) }}</p>
                        <p class="mt-1 text-xs text-violet-700/70 dark:text-violet-400/70">Meta total: {{ $currentWs->money($accountOverview['goal_target']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900 account-overview-kpi">
                        <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Património total</p>
                        <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-white">{{ $currentWs->money($totalPatrimony) }}</p>
                        <p class="mt-1 text-xs text-zinc-500">Contas + investimentos + objetivos</p>
                    </div>
                </div>
            </section>

            {{-- Receitas e despesas --}}
            <section>
                <h3 class="mb-4 text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Movimentos registados · histórico completo</h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900/60 dark:hover:border-blue-500/40">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Receitas</span>
                            <span class="rounded-lg bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">{{ number_format($accountOverview['income_count'], 0, ',', '.') }} registos</span>
                        </div>
                        <p class="mt-2 text-2xl font-black text-emerald-600">{{ $currentWs->money($accountOverview['income_total']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Despesas</span>
                            <span class="rounded-lg bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">{{ number_format($accountOverview['expense_count'], 0, ',', '.') }} registos</span>
                        </div>
                        <p class="mt-2 text-2xl font-black text-rose-600">{{ $currentWs->money($accountOverview['expense_total']) }}</p>
                    </div>
                </div>
            </section>

            {{-- Contas bancárias --}}
            <section>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Contas bancárias</h3>
                    <span class="text-xs text-zinc-500">{{ $accountOverview['account_count'] }} registadas</span>
                </div>
                <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900/40">
                    @forelse($accountOverview['accounts'] as $account)
                        <div class="account-overview-row flex flex-col gap-2 border-b border-zinc-100 p-4 last:border-0 transition-colors duration-200 hover:bg-blue-50/70 dark:border-zinc-800 dark:hover:bg-blue-500/5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $account['name'] }}</p>
                                <p class="text-xs text-zinc-500">{{ $account['bank'] ?: 'Banco não indicado' }} · {{ ucfirst(str_replace('_', ' ', $account['type'] ?: 'conta')) }} · {{ $account['currency'] }}</p>
                            </div>
                            <div class="sm:text-right">
                                <p class="text-base font-black text-zinc-900 dark:text-white">{{ $currentWs->money($account['balance']) }}</p>
                                <p class="text-[11px] {{ $account['included'] ? 'text-emerald-600' : 'text-amber-600' }}">{{ $account['included'] ? 'Incluída no património' : 'Excluída do património' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="p-5 text-sm text-zinc-500">Ainda não existem contas bancárias registadas.</p>
                    @endforelse
                </div>
            </section>

            {{-- Investimentos --}}
            <section>
                <h3 class="mb-4 text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Investimentos</h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="account-overview-kpi rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-900"><p class="text-xs text-zinc-500">Capital investido</p><p class="mt-2 text-lg font-black text-zinc-900 dark:text-white">{{ $currentWs->money($accountOverview['investment_invested']) }}</p></div>
                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-900"><p class="text-xs text-zinc-500">Valor atual</p><p class="mt-2 text-lg font-black text-zinc-900 dark:text-white">{{ $currentWs->money($accountOverview['investment_value']) }}</p></div>
                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-900"><p class="text-xs text-zinc-500">Lucro / prejuízo</p><p class="mt-2 text-lg font-black {{ $accountOverview['investment_profit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $currentWs->money($accountOverview['investment_profit']) }}</p></div>
                </div>
            </section>

            {{-- Compromissos e entradas recorrentes --}}
            <section>
                <h3 class="mb-4 text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Compromissos e valores recorrentes</h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800"><p class="text-xs text-zinc-500">Assinaturas ativas</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ $accountOverview['subscription_count'] }}</p><p class="mt-1 text-xs text-zinc-500">Custo pessoal/mês: {{ $currentWs->money($accountOverview['subscription_personal_monthly']) }}</p></div>
                    <div class="account-overview-kpi rounded-2xl border border-amber-200 p-4 dark:border-amber-500/20"><p class="text-xs text-amber-700 dark:text-amber-400">Pago pela empresa/mês</p><p class="mt-2 text-xl font-black text-amber-700 dark:text-amber-400">{{ $currentWs->money($accountOverview['subscription_company_monthly']) }}</p><p class="mt-1 text-xs text-zinc-500">Separado das despesas pessoais</p></div>
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800"><p class="text-xs text-zinc-500">Rendimentos recorrentes ativos</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ $accountOverview['recurring_count'] }}</p><p class="mt-1 text-xs text-zinc-500">Valor mensal registado: {{ $currentWs->money($accountOverview['recurring_total']) }}</p></div>
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800"><p class="text-xs text-zinc-500">Orçamentos definidos</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ $accountOverview['category_count'] }}</p><p class="mt-1 text-xs text-zinc-500">Limites: {{ $currentWs->money($accountOverview['budget_total']) }}</p></div>
                </div>
            </section>

            <section>
                <h3 class="mb-4 text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Salário e alimentação</h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800"><p class="text-xs text-zinc-500">Contratos de salário ativos</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ $accountOverview['salary_contract_count'] }}</p></div>
                    <div class="account-overview-kpi rounded-2xl border border-emerald-200 p-4 dark:border-emerald-500/20"><p class="text-xs text-zinc-500">Salário líquido projetado/mês</p><p class="mt-2 text-xl font-black text-emerald-600">{{ $currentWs->money($accountOverview['salary_net_projected']) }}</p></div>
                    <div class="rounded-2xl border border-amber-200 p-4 dark:border-amber-500/20"><p class="text-xs text-zinc-500">Subsídio alimentação/mês</p><p class="mt-2 text-xl font-black text-amber-600">{{ $currentWs->money($accountOverview['meal_allowance_projected']) }}</p></div>
                    <div class="rounded-2xl border border-amber-200 p-4 dark:border-amber-500/20"><p class="text-xs text-zinc-500">Subsídio alimentação/ano</p><p class="mt-2 text-xl font-black text-amber-600">{{ $currentWs->money($accountOverview['meal_allowance_annual']) }}</p></div>
                </div>
                <p class="mt-2 text-xs text-zinc-500">Pagamentos salariais no período selecionado: {{ $accountOverview['salary_payments_count'] }}</p>
            </section>

            {{-- Dívidas e objetivos --}}
            <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900/60">
                    <h3 class="text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Dívidas por liquidar</h3>
                    <p class="mt-3 text-xs text-zinc-500">A pagar</p><p class="text-xl font-black text-rose-600">{{ $currentWs->money($accountOverview['debt_pay']) }}</p>
                    <p class="mt-3 text-xs text-zinc-500">A receber</p><p class="text-xl font-black text-emerald-600">{{ $currentWs->money($accountOverview['debt_receive']) }}</p>
                    <p class="mt-2 text-xs text-zinc-500">{{ $accountOverview['debt_count'] }} registos pendentes</p>
                </div>
                <div class="rounded-2xl border border-zinc-200 p-5 dark:border-zinc-800">
                    <h3 class="text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Objetivos de poupança</h3>
                    <p class="mt-3 text-xs text-zinc-500">Montante acumulado</p><p class="text-xl font-black text-violet-600">{{ $currentWs->money($accountOverview['goal_saved']) }}</p>
                    <p class="mt-3 text-xs text-zinc-500">Objetivo total</p><p class="text-xl font-black text-zinc-900 dark:text-white">{{ $currentWs->money($accountOverview['goal_target']) }}</p>
                    @php
                        $goalProgress = $accountOverview['goal_target'] > 0
                            ? min(100, max(0, ($accountOverview['goal_saved'] / $accountOverview['goal_target']) * 100))
                            : 0;
                    @endphp
                    <div class="mt-4">
                        <div class="mb-1.5 flex items-center justify-between text-[11px] font-semibold text-zinc-500">
                            <span>Progresso global</span>
                            <span>{{ number_format($goalProgress, 1, ',', '.') }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-500 transition-all duration-1000" style="width: {{ $goalProgress }}%"></div>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-zinc-500">{{ $accountOverview['goal_count'] }} objetivos criados</p>
                </div>
            </section>

            {{-- Atividade e organização --}}
            <section>
                <h3 class="mb-4 text-sm font-extrabold text-zinc-800 dark:text-zinc-100">Atividade e organização</h3>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-900"><p class="text-xs text-zinc-500">Lembretes por concluir</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ $accountOverview['reminder_open'] }}</p></div>
                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-900"><p class="text-xs text-zinc-500">Lembretes concluídos</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ $accountOverview['reminder_done'] }}</p></div>
                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-900"><p class="text-xs text-zinc-500">Atividades registadas</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ $accountOverview['activity_count'] }}</p><p class="mt-1 text-xs text-zinc-500">{{ number_format($accountOverview['activity_km'], 1, ',', '.') }} km · {{ $accountOverview['activity_minutes'] }} min</p></div>
                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-900"><p class="text-xs text-zinc-500">Saúde financeira</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ $overallScore }}%</p><p class="mt-1 text-xs text-zinc-500">Saldo do período: {{ $currentWs->money($netBalance) }}</p></div>
                </div>
            </section>
        </div>
<style>
        #dashboard-resumo-conta .account-overview-header {
            background:
                radial-gradient(ellipse at 90% 0%, rgba(59,130,246,.14), transparent 42%),
                linear-gradient(115deg, rgba(239,246,255,.9), rgba(255,255,255,.98) 58%, rgba(245,243,255,.75));
        }
        .dark #dashboard-resumo-conta .account-overview-header {
            background:
                radial-gradient(ellipse at 90% 0%, rgba(59,130,246,.18), transparent 42%),
                linear-gradient(115deg, rgba(23,37,84,.35), rgba(9,9,11,.98) 58%, rgba(46,16,101,.2));
        }
        #dashboard-resumo-conta .account-overview-kpi {
            position: relative;
            overflow: hidden;
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
            animation: accountOverviewEnter .55s both;
        }
        #dashboard-resumo-conta .account-overview-kpi::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(115deg, transparent 25%, rgba(255,255,255,.32) 48%, transparent 70%);
            transform: translateX(-130%);
            transition: transform .7s ease;
        }
        #dashboard-resumo-conta .account-overview-kpi:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px -18px rgba(37,99,235,.45);
        }
        #dashboard-resumo-conta .account-overview-kpi:hover::after { transform: translateX(130%); }
        #dashboard-resumo-conta .account-overview-content > section {
            animation: accountOverviewEnter .5s both;
        }
        #dashboard-resumo-conta .account-overview-content > section:nth-child(2) { animation-delay: .04s; }
        #dashboard-resumo-conta .account-overview-content > section:nth-child(3) { animation-delay: .08s; }
        #dashboard-resumo-conta .account-overview-content > section:nth-child(4) { animation-delay: .12s; }
        #dashboard-resumo-conta .account-overview-content > section:nth-child(5) { animation-delay: .16s; }
        @keyframes accountOverviewEnter {
            from { opacity: 0; transform: translateY(9px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            #dashboard-resumo-conta .account-overview-kpi,
            #dashboard-resumo-conta .account-overview-content > section {
                animation: none !important;
                transition: none !important;
            }
            #dashboard-resumo-conta .account-overview-kpi:hover { transform: none; }
        }
    </style>

    </div>

    {{-- RODAPÉ DISCRETO --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Gestão Inteligente
        </p>
    </footer>

    {{-- ESTILO DA SCROLLBAR --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }

        .dashboard-page .glass-card,
        .dashboard-page .backdrop-blur-sm,
        .dashboard-page .backdrop-blur-md {
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
        }

        .dashboard-page .animate-ping,
        .dashboard-page .animate-pulse {
            animation-duration: 2.4s;
        }

        .dashboard-page .transition-all,
        .dashboard-page .transition-transform,
        .dashboard-page .transition-colors {
            transition-duration: 150ms;
        }
    </style>

    {{-- MODAL: CONFIRMAR PASSWORD PARA DESFOCAR --}}
@if($showPrivacyModal)
<div
    x-data="{ show: @entangle('showPrivacyModal') }"
    x-show="show"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
>
    <div
        x-transition.scale
        class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-[2rem] p-8 shadow-2xl border border-zinc-200 dark:border-zinc-800"
    >
        <div class="text-center mb-6">
            <h2 class="text-lg font-black uppercase tracking-widest dark:text-white">
                Confirmar Identidade
            </h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed">
                Introduz a tua password para revelar os valores privados.
            </p>
        </div>

        <form wire:submit.prevent="unlockPrivacy" class="space-y-5">
            <div>
                <input
                    type="password"
                    wire:model.defer="privacyPassword"
                    class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-brand-500"
                    placeholder="Password"
                    autofocus
                >
                @error('privacyPassword')
                    <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button
                    type="button"
                    wire:click="$set('showPrivacyModal', false)"
                    class="flex-1 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl py-2 text-xs font-black uppercase tracking-widest hover:bg-zinc-300 dark:hover:bg-zinc-700 transition"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="flex-1 bg-brand-600 text-white rounded-xl py-2 text-xs font-black uppercase tracking-widest shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition"
                >
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>
@endif
{{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: SUGESTÃO INTELIGENTE PÓS-PAGAMENTO                      --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-data="{ open: @entangle('showSubSuggestion') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-[300] flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-xl" x-on:click="open = false"></div>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative w-full max-w-md bg-white dark:bg-zinc-900 rounded-[3rem] p-10 shadow-2xl border border-emerald-500/20 text-center">

            <div class="relative size-24 mx-auto mb-8">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full animate-pulse"></div>
                <div class="relative size-full bg-emerald-500 text-white rounded-[2.2rem] flex items-center justify-center shadow-2xl">
                    <flux:icon name="check-badge" variant="solid" class="size-12" />
                </div>
            </div>

            <h2 class="text-3xl font-black dark:text-white uppercase italic tracking-tighter leading-none">Pagamento Confirmado!</h2>

            <p class="mt-6 text-sm text-zinc-500 font-medium leading-relaxed">
                Olá {{ explode(' ', auth()->user()->name)[0] }}, o teu plano <span class="text-emerald-600 font-black">{{ $suggestedName }}</span> ({{ number_format($suggestedPrice, 2) }}€/mês) já está ativo. Podes acompanhá-lo na área "Assinaturas".
            </p>

            <div class="mt-10 space-y-3">
                <flux:button wire:click="dismissSubSuggestion" variant="primary" class="w-full h-16 bg-emerald-600 hover:bg-emerald-500 border-none font-black uppercase tracking-widest text-xs shadow-xl shadow-emerald-500/20 rounded-2xl">
                    Perfeito, obrigado ✅
                </flux:button>
            </div>
        </div>
    </div>
</div>
