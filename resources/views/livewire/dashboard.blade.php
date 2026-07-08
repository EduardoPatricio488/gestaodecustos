<div class="dashboard-page space-y-8 pb-10">
    {{-- CSS dentro da root div para evitar o erro de Multiple Root Elements --}}
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @php
        $firstName = explode(' ', auth()->user()->name)[0] ?? auth()->user()->name;
        $others = $currentWs->users->where('id', '!=', auth()->id())->pluck('name')->map(fn($name) => explode(' ', $name)[0]);
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
    @endphp

    {{-- 1. TICKER DE MERCADO ULTRA COMPACTO --}}
    <div x-data="{
            scrollNext() { $refs.tickerContainer.scrollBy({ left: 250, behavior: 'smooth' }) },
            scrollPrev() { $refs.tickerContainer.scrollBy({ left: -250, behavior: 'smooth' }) }
         }"
         class="relative group px-1">

        {{-- Setas de navegação (Apenas PC) --}}
        <button @click="scrollPrev" class="absolute -left-1 top-1/2 -translate-y-1/2 z-20 size-8 flex items-center justify-center bg-white/90 dark:bg-zinc-800/90 border border-zinc-200 dark:border-zinc-700 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex">
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
            @endforeach
        </div>

        <button @click="scrollNext" class="absolute -right-1 top-1/2 -translate-y-1/2 z-20 size-8 flex items-center justify-center bg-white/90 dark:bg-zinc-800/90 border border-zinc-200 dark:border-zinc-700 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex">
            <flux:icon name="chevron-right" class="size-3 text-zinc-600 dark:text-zinc-300" />
        </button>
    </div>



{{-- IA INSIGHTS TICKER — MONOCROMÁTICO PREMIUM --}}
<div
    x-data="{ speed: 400 }"
    class="relative flex items-center gap-5 w-full p-3.5 rounded-2xl border border-white/5
           bg-zinc-950 backdrop-blur-2xl shadow-xl group overflow-hidden select-none">

    {{-- FUNDO DINÂMICO DE NOTÍCIAS --}}
    <div class="absolute inset-0 pointer-events-none opacity-[0.18] bg-gradient-to-r
                from-emerald-600/40 via-emerald-400/20 to-transparent animate-bgFlow"></div>

    {{-- IA INDICATOR --}}
    <div class="flex items-center gap-3 shrink-0 pl-1 z-10">
        <div class="relative flex items-center justify-center">
            <div class="size-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
            <div class="absolute inset-0 size-2.5 rounded-full bg-emerald-400/30 blur-md"></div>
        </div>
        <span class="text-[10px] font-black uppercase text-emerald-400 tracking-[0.3em]">
            IA Insights
        </span>
    </div>

    {{-- Ticker Container --}}
    <div class="relative flex-1 overflow-hidden h-6 z-10">
        <div class="ticker-ultra flex items-center gap-20 whitespace-nowrap" :style="`animation-duration: ${speed}s`">

            {{-- 1ª Cópia --}}
            <div class="ticker-text-ultra">
                @forelse($this->aiInsights as $insight)
                    <span class="mono-item">{{ $insight }}</span>
                    <span class="mono-separator">/</span>
                @empty
                    <span class="mono-empty italic uppercase">A analisar fluxos financeiros...</span>
                @endforelse
            </div>

            {{-- 2ª Cópia --}}
            <div class="ticker-text-ultra">
                @forelse($this->aiInsights as $insight)
                    <span class="mono-item">{{ $insight }}</span>
                    <span class="mono-separator">/</span>
                @empty
                    <span class="mono-empty italic uppercase">A analisar fluxos financeiros...</span>
                @endforelse
            </div>

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
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: #d4d4d8;
}

/* Notícias monocromáticas */
.mono-item {
    color: #e5e5e5;
    font-weight: 800;
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
        data: { temp: '--', city: 'A detetar...', code: 0, humidity: 0, wind: 0, feels_like: '--', forecast: [] },

        getWeatherIcon(code) {
            if (code <= 3) return 'sun';
            if (code <= 67) return 'cloud';
            return 'bolt';
        },

        async init() {
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(async (pos) => {
                const { latitude: lat, longitude: lon } = pos.coords;
                try {
                    const geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                    const geoData = await geoRes.json();
                    this.data.city = geoData.address.city || geoData.address.town || 'Localização';

                    const wRes = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto`);
                    const wData = await wRes.json();

                    this.data.temp = Math.round(wData.current.temperature_2m);
                    this.data.feels_like = Math.round(wData.current.apparent_temperature);
                    this.data.humidity = wData.current.relative_humidity_2m;
                    this.data.wind = Math.round(wData.current.wind_speed_10m);
                    this.data.code = wData.current.weather_code;

                    this.data.forecast = wData.daily.time.slice(1, 6).map((time, i) => ({
                        day: new Date(time).toLocaleDateString('pt-PT', { weekday: 'short' }),
                        max: Math.round(wData.daily.temperature_2m_max[i+1]),
                        min: Math.round(wData.daily.temperature_2m_min[i+1]),
                        code: wData.daily.weather_code[i+1]
                    }));
                    this.loading = false;
                } catch (e) { console.error('Erro clima:', e); }
            });
        }
     }"
     x-init="init()"
>
    <div class="flex items-center gap-5">
        {{-- Avatar Dinâmico --}}
        <div class="size-16 rounded-[1.5rem] flex items-center justify-center text-4xl shadow-2xl shrink-0 transition-all duration-500"
             style="background-color: {{ auth()->user()->profile_color }}15; border: 2px solid {{ auth()->user()->profile_color }}30;">
            {{ auth()->user()->profile_emoji }}
        </div>

        <div class="text-left">
            <div class="flex items-center gap-3">
                <h1 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter uppercase italic leading-none">
                    {{ $this->greeting }}, <span style="color: {{ auth()->user()->profile_color }}">{{ $firstName }}</span>
                </h1>

                @if(session()->has('impersonator_id'))
                    <a href="{{ route('admin.stop-impersonating') }}" class="flex items-center gap-2 px-3 py-1 bg-amber-500 text-white rounded-full animate-pulse text-[9px] font-black uppercase tracking-widest shadow-lg shadow-amber-500/30">
                        Suporte Ativo · Sair
                    </a>
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

    {{-- MODAL DETALHADO --}}
    <flux:modal name="weather-details" position="center" class="md:w-[450px] !p-0 overflow-hidden" wire:ignore.self>
        <div class="relative bg-zinc-950 text-white p-10 space-y-8 text-left">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-600/20 to-transparent pointer-events-none"></div>

            <div class="relative z-10 flex justify-between items-start">
                <div class="text-left">
                    <h2 class="text-3xl font-black italic tracking-tighter uppercase leading-none" x-text="data.city"></h2>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 mt-2">Condições Atmosféricas</p>
                </div>
                <flux:modal.close><flux:button variant="ghost" icon="x-mark" class="text-white/40 hover:text-white" /></flux:modal.close>
            </div>

            <div class="relative z-10 flex items-center justify-center gap-6 py-4">
                <template x-if="getWeatherIcon(data.code) === 'sun'"><flux:icon name="sun" class="size-20 text-amber-500" /></template>
                <template x-if="getWeatherIcon(data.code) === 'cloud'"><flux:icon name="cloud" class="size-20 text-zinc-400" /></template>
                <template x-if="getWeatherIcon(data.code) === 'bolt'"><flux:icon name="bolt" class="size-20 text-blue-500" /></template>
                <div class="text-left leading-none">
                    <span class="text-7xl font-black tracking-tighter italic" x-text="data.temp + '°'"></span>
                </div>
            </div>

            <div class="relative z-10 grid grid-cols-2 gap-4">
                <div class="bg-white/5 p-4 rounded-3xl border border-white/5 flex items-center gap-4">
                    <flux:icon name="beaker" class="size-5 text-blue-400" />
                    <div><p class="text-[8px] font-black uppercase text-zinc-500">Humidade</p><p class="text-sm font-black" x-text="data.humidity + '%'"></p></div>
                </div>
                <div class="bg-white/5 p-4 rounded-3xl border border-white/5 flex items-center gap-4">
                    <flux:icon name="flag" class="size-5 text-emerald-400" />
                    <div><p class="text-[8px] font-black uppercase text-zinc-500">Vento</p><p class="text-sm font-black" x-text="data.wind + ' km/h'"></p></div>
                </div>
            </div>

            <div class="relative z-10 space-y-4">
                <p class="text-[9px] font-black uppercase text-zinc-500 tracking-[0.2em] text-left border-b border-white/10 pb-2">Previsão Semanal</p>
                <div class="flex justify-between items-center gap-2">
                    <template x-for="item in data.forecast">
                        <div class="flex flex-col items-center flex-1 p-2 rounded-2xl hover:bg-white/5 transition-colors">
                            <span class="text-[9px] font-black uppercase text-zinc-500" x-text="item.day"></span>
                            <div class="my-2 text-center">
                                <template x-if="getWeatherIcon(item.code) === 'sun'"><flux:icon name="sun" variant="micro" class="size-4 text-amber-500" /></template>
                                <template x-if="getWeatherIcon(item.code) === 'cloud'"><flux:icon name="cloud" variant="micro" class="size-4 text-zinc-400" /></template>
                                <template x-if="getWeatherIcon(item.code) === 'bolt'"><flux:icon name="bolt" variant="micro" class="size-4 text-blue-400" /></template>
                            </div>
                            <span class="text-xs font-black italic" x-text="item.max + '°'"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </flux:modal>














@php
    $user = auth()->user();
    // Utilizadores "Star" (Premium) ou "Diamond" (Business) têm acesso
    $hasProAccess = $user->isStar() || $user->isDiamond();
@endphp

{{-- TERMINAL DE ACÇÕES ESTRATÉGICAS --}}
<div class="flex items-center gap-2 bg-white dark:bg-zinc-900 p-1.5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl">

    {{-- BOTÃO IA PRO --}}
    @if($hasProAccess)
        <a href="{{ route('ai') }}" wire:navigate
            class="group flex items-center gap-2 px-4 h-10 rounded-xl bg-zinc-950 text-brand-400 font-black uppercase text-[10px] tracking-[0.2em] border border-zinc-800 hover:bg-brand-600 hover:text-white transition-all shadow-lg shadow-brand-500/10 active:scale-95">
            <flux:icon name="sparkles" class="size-4 animate-pulse group-hover:rotate-12 transition-transform" />
            <span>IA <span class="hidden sm:inline">PRO</span></span>
        </a>
    @else
        {{-- BLOQUEADO: REDIRECIONA PARA HUB.PRICING --}}
        <a href="{{ route('hub.pricing') }}" wire:navigate
            class="group flex items-center gap-3 px-4 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 text-zinc-400 font-black uppercase text-[10px] tracking-[0.2em] border border-zinc-200 dark:border-zinc-700 opacity-60 hover:opacity-100 transition-all"
            title="Requer Plano Premium ou superior">
            <span class="flex items-center gap-1.5">
                IA PRO
                <flux:icon name="lock-closed" variant="micro" class="size-3 text-zinc-400" />
            </span>
        </a>
    @endif

    <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

    {{-- BOTÃO RELATÓRIO PDF --}}
    @if($hasProAccess)
        <flux:modal.trigger name="export-pdf-modal">
            <button type="button"
                class="group flex items-center gap-2 px-4 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 font-black uppercase text-[10px] tracking-widest border border-indigo-100 dark:border-indigo-500/20 hover:bg-indigo-600 hover:text-white transition-all shadow-sm active:scale-95">
                <flux:icon name="document-arrow-down" class="size-4 group-hover:-translate-y-0.5 transition-transform" />
                <span>Relatório</span>
            </button>
        </flux:modal.trigger>
    @else
        {{-- BLOQUEADO: REDIRECIONA PARA HUB.PRICING --}}
        <a href="{{ route('hub.pricing') }}" wire:navigate
            class="group flex items-center gap-3 px-4 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 text-zinc-400 font-black uppercase text-[10px] tracking-widest border border-zinc-200 dark:border-zinc-700 opacity-60 hover:opacity-100 transition-all"
            title="Requer Plano Premium ou superior">
            <span class="flex items-center gap-1.5">
                Relatório
                <flux:icon name="lock-closed" variant="micro" class="size-3 text-zinc-400" />
            </span>
        </a>
    @endif

    <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

    {{-- GRUPO DE NAVEGAÇÃO (SEMPRE LIVRE) --}}
    <div class="flex gap-1.5">
        <a href="{{ route('expenses') }}" wire:navigate
            class="flex items-center px-5 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 font-black uppercase text-[10px] tracking-widest border border-rose-100 dark:border-rose-900/50 hover:bg-rose-600 hover:text-white transition-all shadow-sm active:scale-95">
            Despesas
        </a>

        <a href="{{ route('hub.incomes') }}" wire:navigate
            class="flex items-center px-5 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 font-black uppercase text-[10px] tracking-widest border border-emerald-100 dark:border-emerald-900/50 hover:bg-emerald-600 hover:text-white transition-all shadow-sm active:scale-95">
            Receitas
        </a>
    </div>
</div>













</div>





{{-- 4. SELETOR DE ESPAÇO (WORKSPACE SWITCHER) - APENAS PARA PLANO BUSINESS --}}
@php
    // Define se o utilizador é Business de forma segura
    $isBusiness = ($user->plan ?? '') === 'pro' || (method_exists($user, 'isDiamond') && $user->isDiamond());
@endphp

@if($isBusiness && $userWorkspaces->count() >= 1)
    <div class="flex items-center gap-4 bg-zinc-100/50 dark:bg-zinc-900/50 p-1.5 rounded-2xl w-fit border border-zinc-200/50 dark:border-zinc-800/50 shadow-sm animate-in fade-in slide-in-from-left-4 duration-500">

        <div class="px-3 py-1 text-[9px] font-black uppercase text-zinc-500 tracking-widest border-r border-zinc-200 dark:border-zinc-800 flex items-center gap-2">
            <flux:icon name="building-office-2" variant="micro" class="size-3 text-violet-500" />
            Espaços
        </div>

        <div class="flex gap-1.5 overflow-x-auto no-scrollbar items-center">
            @foreach($userWorkspaces as $ws)
                @php $isActive = ($ws->id == $currentWs->id); @endphp
                <button
                    wire:click="switchWorkspace({{ $ws->id }})"
                    wire:loading.class="opacity-50 cursor-wait"
                    wire:target="switchWorkspace({{ $ws->id }})"
                    class="group flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-200 cursor-pointer select-none
                    {{ $isActive
                        ? 'bg-white dark:bg-zinc-800 shadow-md text-brand-600 font-black scale-[1.02]'
                        : 'text-zinc-500 hover:bg-white dark:hover:bg-zinc-800 hover:text-zinc-800 dark:hover:text-white hover:shadow-md hover:scale-[1.02] active:scale-95' }}"
                >
                    <div class="size-1.5 rounded-full transition-all duration-200
                        {{ $isActive
                            ? 'bg-brand-500 shadow-[0_0_8px_#3b82f6]'
                            : 'bg-zinc-300 group-hover:bg-brand-400 group-hover:shadow-[0_0_6px_#3b82f6]' }}">
                    </div>
                    <span class="text-xs uppercase tracking-tighter">{{ $ws->name }}</span>

                    @if($ws->type !== 'personal')
                        <flux:icon name="arrow-right-circle" variant="micro"
                            class="size-3 transition-all duration-200
                            {{ $isActive ? 'opacity-60' : 'opacity-0 group-hover:opacity-70 group-hover:translate-x-0.5' }}" />
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Botão Adicionar (+) --}}
        <a href="{{ route('hub.business.gateway', ['new' => 1]) }}"
           wire:navigate
           class="flex items-center justify-center size-8 rounded-xl bg-zinc-200/60 dark:bg-zinc-800 text-zinc-400 hover:text-brand-500 hover:bg-white dark:hover:bg-zinc-700 transition-all shadow-sm group">
            <flux:icon name="plus" class="size-4 group-hover:scale-110 transition-transform" />
        </a>
    </div>
@endif




    {{-- 2. BARRA DE NÍVEL E XP (DESIGN INTEGRADO) --}}
    <div class="group relative flex items-center gap-6 bg-gradient-to-r from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-5 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden">
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

    {{-- 6. SECÇÃO DE INTELIGÊNCIA E PREVISÃO --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

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
                            @if($projectionStatus == 'critical') Risco de saldo negativo detetado. Considera adiar compras.
                            @elseif($projectionStatus == 'warning') Poupança ligeiramente abaixo da meta mensal.
                            @else Gestão exemplar. O teu ritmo médio é de <b class="text-white">{{ $currentWs->money($totalMonth / max(now()->day, 1)) }}/dia</b>. @endif
                        </p>
                    </div>
                </div>

                {{-- Mini Métricas de Apoio --}}
                <div class="flex flex-col justify-center gap-6 border-l border-white/10 pl-10 min-w-[200px]">
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-1">Gasto Projetado</p>
                        <p class="text-2xl font-black text-zinc-200">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                {{ $currentWs->money($projectedExpenses) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-1">Receita Prevista</p>
                        <p class="text-2xl font-black text-emerald-500">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                {{ $currentWs->money($totalIncomeMonth) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: SCORE DE SAÚDE (ESTILO CIRCULAR/VISUAL) --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center p-8 text-center group">
            <div class="relative size-32 mb-4 flex items-center justify-center">
                {{-- SVG Circular Progress Subtil --}}
                <svg class="absolute inset-0 size-full -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-zinc-100 dark:text-zinc-800" />
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent"
                        class="{{ $overallScore > 70 ? 'text-emerald-500' : ($overallScore > 40 ? 'text-amber-500' : 'text-red-500') }} transition-all duration-1000"
                        stroke-dasharray="364.4"
                        stroke-dashoffset="{{ 364.4 - (364.4 * $overallScore) / 100 }}"
                        stroke-linecap="round" />
                </svg>
                <span class="text-4xl font-black dark:text-white tracking-tighter">{{ $overallScore }}%</span>
            </div>

            <h4 class="text-xs font-black uppercase text-zinc-500 tracking-widest">Saúde Financeira</h4>
            <p class="text-[10px] text-zinc-400 mt-2 font-medium">Baseado no teu rácio de poupança e cumprimento de orçamentos.</p>

            <div class="mt-6 w-full px-4">
                <flux:button variant="ghost" size="sm" class="w-full rounded-xl text-[10px] font-black uppercase tracking-widest">Ver análise detalhada</flux:button>
            </div>
        </div>
    </div>

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
<div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div class="p-3 bg-brand-500/10 rounded-2xl text-brand-600">
            <flux:icon name="bell" variant="outline" class="size-6" />
        </div>

        <div>
            <h3 class="font-black dark:text-white uppercase text-sm">Alertas Push</h3>
            <p class="text-[10px] text-zinc-500 uppercase font-bold">Recebe avisos de gastos e orçamentos no telemóvel</p>
        </div>
    </div>

    <flux:button onclick="enableNotifications()" variant="primary" class="rounded-xl font-black uppercase text-[10px] px-6">
        Ativar balões
    </flux:button>
</div>



    {{-- 5. GRID DE PERFORMANCE (STATS CARDS) --}}
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-5">

        {{-- Card: Saldo Real --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-5 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <div class="absolute top-0 left-0 w-1 h-full {{ $netBalance >= 0 ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Saldo Líquido</p>
            <h3 class="text-2xl font-black {{ $netBalance >= 0 ? 'text-emerald-600' : 'text-red-500' }} tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $currentWs->money($netBalance) }}
                </span>
            </h3>
            <flux:icon name="banknotes" class="absolute -right-2 -bottom-2 size-12 text-zinc-100 dark:text-zinc-800/50 group-hover:scale-110 transition-transform" />
        </div>

        {{-- Card: Gasto Atual --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-5 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Gasto em {{ now()->translatedFormat('M') }}</p>
            <h3 class="text-2xl font-black dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $currentWs->money($totalMonth) }}
                </span>
            </h3>
            <flux:icon name="arrow-trending-down" class="absolute -right-2 -bottom-2 size-12 text-zinc-100 dark:text-zinc-800/50 group-hover:rotate-12 transition-transform" />
        </div>

        {{-- Card: Património Investido (Destaque Premium) --}}
        <div class="relative overflow-hidden bg-zinc-950 dark:bg-brand-600 p-5 rounded-[2rem] shadow-xl shadow-brand-500/10 group">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-brand-400 dark:text-brand-100 uppercase tracking-widest mb-1">Portefólio Ativo</p>
                <h3 class="text-2xl font-black text-white tracking-tighter">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ $currentWs->money($portfolioValue) }}
                    </span>
                </h3>
            </div>
            <flux:icon name="chart-bar-square" class="absolute -right-3 -bottom-3 size-16 text-white/10 group-hover:scale-125 transition-transform" />
            <div class="absolute top-0 right-0 p-3">
                <div class="size-2 rounded-full bg-brand-400 animate-ping"></div>
            </div>
        </div>

        {{-- Card: Score Saúde --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-5 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Saúde Financeira</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-brand-600 tracking-tighter">{{ $overallScore }}%</h3>
                <span class="text-[9px] font-bold text-emerald-500">▲ 2%</span>
            </div>
            <div class="mt-2 h-1 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500" style="width: {{ $overallScore }}%"></div>
            </div>
        </div>

        {{-- Card: Poupança Total --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-5 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Poupança Total</p>
            <h3 class="text-2xl font-black text-emerald-500 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $currentWs->money($totalSaved) }}
                </span>
            </h3>
            <flux:icon name="shield-check" class="absolute -right-2 -bottom-2 size-12 text-zinc-100 dark:text-zinc-800/50 group-hover:scale-110 transition-transform" />
        </div>

        {{-- Card: Finance Score --}}
        <a href="{{ route('hub.budget') }}" wire:navigate class="glass-card relative overflow-hidden bg-zinc-950 p-5 rounded-[2rem] border border-zinc-800 shadow-sm group block">
            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">Finance Score</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-3xl font-black text-white tracking-tighter italic">{{ $financeScore['score'] }}</h3>
                <span class="text-[9px] font-bold text-emerald-400">/100</span>
            </div>
            <div class="mt-2 h-1 w-full bg-white/10 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500" style="width: {{ $financeScore['score'] }}%"></div>
            </div>
        </a>

        {{-- Card: Wellness Financeiro --}}
        <div class="glass-card relative overflow-hidden bg-gradient-to-br from-orange-500 to-amber-600 p-5 rounded-[2rem] shadow-sm group lg:col-span-2">
            <p class="text-[10px] font-black text-white/70 uppercase tracking-widest mb-1">Wellness Financeiro</p>
            <p class="text-sm font-bold text-white leading-snug">{{ $wellnessInsights['verdict'] }}</p>
            @if($wellnessInsights['total_km'] > 0)
                <div class="flex gap-4 mt-3">
                    <span class="text-[10px] font-black text-white/80">{{ number_format($wellnessInsights['total_km'], 0) }} km</span>
                    <span class="text-[10px] font-black text-white/80">{{ $wellnessInsights['activity_count'] }} atividades</span>
                    @if($wellnessInsights['cost_per_km'])
                        <span class="text-[10px] font-black text-white/80">{{ number_format($wellnessInsights['cost_per_km'], 2, ',', '.') }}€/km</span>
                    @endif
                </div>
            @endif
            <flux:icon name="bolt" class="absolute -right-2 -bottom-2 size-12 text-white/10" />
        </div>
    </div>


    {{-- 7. GRÁFICOS E ORÇAMENTOS (VISUAL ANALYTICS) --}}
    <div class="grid gap-6 lg:grid-cols-5">

        {{-- GRÁFICO: FLUXO DE CAIXA (6 MESES) --}}
        <div class="glass-card p-6 lg:col-span-3 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Tendência de Fluxo</h2>
                    <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Análise Semestral de Caixa</p>
                </div>
                {{-- Legenda Discreta --}}
                <div class="flex gap-4">
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-emerald-500"></div>
                        <span class="text-[9px] font-black uppercase text-zinc-500">Ganhos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-brand-500"></div>
                        <span class="text-[9px] font-black uppercase text-zinc-500">Gastos</span>
                    </div>
                </div>
            </div>

            {{-- Container das Barras --}}
            <div class="flex h-64 items-end gap-3 sm:gap-6 px-2 relative z-10">
                @foreach ($last6 as $m)
                    @php
                        $hE = ($m['earned'] / ($chartMax ?: 1)) * 100;
                        $hS = ($m['spent'] / ($chartMax ?: 1)) * 100;
                    @endphp
                    <div class="flex flex-1 flex-col items-center gap-3 h-full justify-end group/bar">
                        <div class="flex items-end gap-1.5 w-full h-full pb-2">
                            {{-- Barra Ganhos --}}
                            <div class="flex-1 bg-emerald-500/20 hover:bg-emerald-500 rounded-t-lg transition-all duration-500 cursor-help relative"
                                 style="height: {{ max(4, $hE) }}%"
                                 title="Ganhos: {{ $currentWs->money($m['earned']) }}">
                            </div>
                            {{-- Barra Gastos --}}
                            <div class="flex-1 bg-brand-500/20 hover:bg-brand-500 rounded-t-lg transition-all duration-500 cursor-help relative"
                                 style="height: {{ max(4, $hS) }}%"
                                 title="Gastos: {{ $currentWs->money($m['spent']) }}">
                            </div>
                        </div>
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest group-hover/bar:text-brand-500 transition-colors">{{ $m['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Linhas de Grelha de Fundo Subtis --}}
            <div class="absolute inset-0 flex flex-col justify-between p-6 pointer-events-none opacity-20 dark:opacity-10">
                @foreach(range(1, 4) as $i) <div class="w-full border-t border-dashed border-zinc-300 dark:border-zinc-700"></div> @endforeach
                <div class="w-full"></div>
            </div>
        </div>

        {{-- WIDGET: ESTADO DOS ORÇAMENTOS (PROGRESS BARS) --}}
        <div class="glass-card p-6 lg:col-span-2 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden flex flex-col group">
            <div class="mb-8">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Limites por Categoria</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Performance de Gastos</p>
            </div>

            <div class="space-y-6 overflow-y-auto flex-1 pr-2 custom-scrollbar">
                @forelse ($byCategory as $cat)
                    <div class="group/item">
                        <div class="flex justify-between items-end mb-2">
                            <div class="flex flex-col">
                                <span class="text-xs font-black uppercase tracking-tight text-zinc-800 dark:text-zinc-200 group-hover/item:text-brand-500 transition-colors">{{ $cat->name }}</span>
                                <span class="text-[9px] font-bold {{ $cat->over ? 'text-red-500' : 'text-zinc-400' }} uppercase italic">
                                    {{ $cat->over ? 'Orçamento Excedido' : 'Dentro da Meta' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black {{ $cat->over ? 'text-red-500' : 'text-zinc-600 dark:text-zinc-300' }}">
                                    {{ $currentWs->money($cat->total) }}
                                </span>
                                <span class="text-[9px] font-bold text-zinc-400 uppercase">/ {{ $currentWs->money($cat->budget) }}</span>
                            </div>
                        </div>

                        {{-- Barra Neon Subtil --}}
                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800/50 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800">
                            <div class="h-full transition-all duration-1000 ease-out {{ $cat->over ? 'bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.4)]' : 'bg-brand-500 shadow-[0_0_10px_rgba(59,130,246,0.4)]' }}"
                                 style="width: {{ min($cat->percentage, 100) }}%">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <flux:icon name="document-magnifying-glass" class="size-10 text-zinc-200 dark:text-zinc-800 mb-4" />
                        <p class="text-zinc-400 text-[10px] font-black uppercase tracking-widest italic">Sem orçamentos ativos.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <flux:button href="{{ route('categories') }}" variant="ghost" size="sm" class="w-full rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-500" wire:navigate>
                    Configurar Orçamentos
                </flux:button>
            </div>
        </div>
    </div>

    {{-- 8. MOVIMENTOS RECENTES (ESTILO TIMELINE) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Fluxo de Caixa</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Movimentos Recentes</p>
            </div>
            <flux:button variant="ghost" size="sm" class="rounded-xl text-[10px] font-black uppercase tracking-widest" href="{{ route('expenses') }}" wire:navigate>Ver Histórico Completo</flux:button>
        </div>

        <div class="divide-y divide-zinc-50 dark:divide-zinc-800/50">
            @forelse ($recent as $e)
                <div class="flex items-center justify-between p-6 hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row">
                    <div class="flex items-center gap-5">
                        {{-- Ícone da Categoria ou Avatar do User --}}
                        <div class="relative">
                            <div class="size-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 group-hover/row:scale-110 group-hover/row:bg-brand-500 group-hover/row:text-white transition-all shadow-sm">
                                <flux:icon name="banknotes" variant="outline" class="size-5" />
                            </div>
                            <div class="absolute -bottom-1 -right-1 size-5 rounded-full border-2 border-white dark:border-zinc-900 overflow-hidden shadow-sm" title="Registado por {{ $e->user->name }}">
                                <div class="size-full bg-brand-600 flex items-center justify-center text-[8px] font-black text-white uppercase">
                                    {{ substr($e->user->name, 0, 1) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-tight">{{ $e->description ?: $e->category?->name }}</span>
                                <span class="text-[8px] font-black px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 rounded-md uppercase tracking-widest">{{ $e->category?->name ?? 'Geral' }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest italic">{{ $e->spent_at->translatedFormat('d M Y') }}</p>
                                <span class="text-zinc-300 dark:text-zinc-700">·</span>
                                <p class="text-[10px] text-zinc-500 font-black uppercase">{{ $e->user->id === auth()->id() ? 'Tu' : explode(' ', $e->user->name)[0] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- VALOR COM PRIVACIDADE --}}
                    <div class="text-right">
                        <span class="text-lg font-black text-red-500 tracking-tighter">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                -{{ $currentWs->money($e->amount) }}
                            </span>
                        </span>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <flux:icon name="magnifying-glass" class="size-12 text-zinc-200 dark:text-zinc-800 mb-4" />
                    <p class="text-zinc-400 text-xs font-black uppercase tracking-widest italic">Nenhum movimento registado neste espaço.</p>
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
        $isPremium = in_array($currentWs->plan ?? 'free', ['plus', 'premium', 'pro', 'company']);
        $report = $this->dailyReport;
    @endphp
@php
        $report = $this->dailyReport;
        $isPremium = $report['is_premium'] ?? false;
    @endphp

    {{-- 11. TERMINAL DE OPERAÇÕES 24H (BLACK OPS ⭐) --}}
    <div class="relative overflow-hidden bg-zinc-950 text-white rounded-[3rem] border border-zinc-800 shadow-[0_50px_100px_-20px_rgba(0,0,0,1)] mt-16 transition-all duration-700 group">

        {{-- BLOB DE FUNDO (Luz tática) --}}
        <div class="absolute -top-24 -left-24 size-96 bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        {{-- BLOQUEIO VISUAL (OVERLAY STEALTH) --}}
        @if(!$isPremium)
            <div class="absolute inset-0 z-30 flex flex-col items-center justify-center p-8 bg-zinc-950/80 backdrop-blur-2xl transition-all duration-1000">
                <div class="size-20 bg-zinc-900 rounded-[2rem] flex items-center justify-center mb-6 border border-zinc-800 shadow-2xl animate-bounce">
                    <span class="text-4xl text-amber-500 drop-shadow-[0_0_15px_rgba(245,158,11,0.5)]">⭐</span>
                </div>
                <h3 class="text-2xl font-black uppercase italic tracking-tighter text-white">Protocolo Restrito</h3>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-[0.3em] mt-2">Requer Ativação Premium</p>

                <a href="{{ route('hub.pricing') }}" wire:navigate
                   class="mt-10 flex items-center justify-center bg-white text-zinc-950 px-12 h-14 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-xl hover:bg-zinc-200 transition-all hover:scale-105 active:scale-95">
                   Desbloquear Terminal ⭐
                </a>
            </div>
        @endif

        {{-- CONTEÚDO TÁTICO --}}
        <div class="relative z-10 p-10 md:p-14 transition-all duration-1000 {{ !$isPremium ? 'blur-3xl opacity-5' : '' }}">

            {{-- HEADER DO TERMINAL --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-14 pb-10 border-b border-white/5">
                <div class="flex items-center gap-6">
                    <div class="p-5 bg-zinc-900 rounded-3xl border border-zinc-800 shadow-inner relative">
                        <flux:icon name="command-line" variant="solid" class="size-8 text-emerald-500" />
                        <div class="absolute -top-1 -right-1 size-3 bg-emerald-500 rounded-full animate-ping"></div>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black uppercase italic tracking-tighter dark:text-white leading-none">Daily Ops Report</h2>
                        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-500 mt-2">Log de Atividade · Ciclo 24H Ativo</p>
                    </div>
                </div>

                <div class="flex items-center gap-8 bg-zinc-900/40 p-5 rounded-[2rem] border border-zinc-800 backdrop-blur-md">
                    <div class="text-right">
                        <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Evolução</p>
                        <p class="text-2xl font-black text-emerald-400 tracking-tighter">+{{ $report['xp_today'] ?? 0 }} XP</p>
                    </div>
                    <div class="h-10 w-px bg-zinc-800"></div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Status</p>
                        <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-3 py-1 rounded-lg uppercase tracking-widest italic border border-emerald-500/20">Synced</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                {{-- COLUNA 1: FINANÇAS (4/12) --}}
                <div class="lg:col-span-4 space-y-8">
                    <h4 class="text-[11px] font-black uppercase text-zinc-500 tracking-[0.3em] flex items-center gap-3">
                        <span class="size-2 rounded-full bg-red-500 shadow-[0_0_8px_#ef4444]"></span> 01. Financial Flow
                    </h4>

                    <div class="space-y-4">
                        @forelse($report['expenses'] ?? [] as $exp)
                            <div class="flex justify-between items-center bg-white/2 p-5 rounded-[1.5rem] border border-white/5 group/item hover:border-zinc-700 transition-colors">
                                <span class="text-xs font-bold text-zinc-400 truncate pr-4">{{ $exp->description }}</span>
                                <span class="text-sm font-black text-white">-{{ number_format($exp->amount, 2) }}€</span>
                            </div>
                        @empty
                            <div class="py-10 text-center border border-dashed border-white/5 rounded-[2rem] bg-white/2">
                                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest italic">Carteira Imaculada</p>
                                <p class="text-[8px] text-zinc-700 font-bold uppercase mt-1">Nenhum débito registado</p>
                            </div>
                        @endforelse

                        @if(($report['spend_total'] ?? 0) > 0)
                            <div class="pt-6 mt-6 border-t border-white/5 flex justify-between items-center text-white px-2">
                                <span class="text-[10px] font-black uppercase opacity-40 tracking-widest">Total Outflow</span>
                                <span class="text-xl font-black italic tracking-tighter">{{ number_format($report['spend_total'], 2) }}€</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- COLUNA 2: PERFORMANCE HUB (5/12) --}}
                <div class="lg:col-span-5 space-y-8 bg-white/2 p-8 rounded-[3rem] border border-white/5 shadow-inner backdrop-blur-xl">
                    <h4 class="text-[11px] font-black uppercase text-zinc-500 tracking-[0.3em] flex items-center gap-3">
                        <span class="size-2 rounded-full bg-orange-500 shadow-[0_0_8px_#f97316]"></span> 02. Biometric Data
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($report['fitness'] ?? [] as $fit)
                            <div class="p-5 bg-zinc-950 border border-white/5 rounded-[1.8rem] group/fit hover:border-orange-500/40 transition-all">
                                <div class="flex items-center gap-4 mb-4">
                                    <span class="text-3xl transition-transform group-hover/fit:scale-110">{{ $fit->type_icon ?? '🏃' }}</span>
                                    <div>
                                        <p class="text-[10px] font-black text-zinc-500 uppercase leading-none">{{ $fit->type }}</p>
                                        <p class="text-xs font-black text-white mt-1 uppercase">Valid</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-end">
                                    <p class="text-2xl font-black text-orange-500 tracking-tighter leading-none">{{ $fit->duration_minutes }}<small class="text-[10px] ml-1 uppercase opacity-60">min</small></p>
                                    <p class="text-sm font-black text-zinc-300 tracking-tighter leading-none">{{ $fit->distance_km ?? '--' }}<small class="text-[10px] ml-1 opacity-60">km</small></p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 py-12 text-center border border-dashed border-white/5 rounded-[2.5rem]">
                                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest italic">Repouso Sistémico</p>
                                <p class="text-[8px] text-zinc-700 font-bold uppercase mt-1">Nenhum treino detectado hoje</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Barra de Progresso Físico --}}
                    @if(($report['fitness_min'] ?? 0) > 0)
                        <div class="pt-4 border-t border-white/5">
                            <div class="flex justify-between items-center mb-2 px-1">
                                <span class="text-[9px] font-black uppercase text-zinc-500 tracking-widest">Active Time Today</span>
                                <span class="text-[9px] font-black text-orange-500 uppercase">{{ $report['fitness_min'] }} / 60 min</span>
                            </div>
                            <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-500 rounded-full shadow-[0_0_10px_#f97316] transition-all duration-1000" style="width: {{ min(($report['fitness_min'] / 60) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- COLUNA 3: FOCO & SOCIAL (3/12) --}}
                <div class="lg:col-span-3 space-y-8">
                    <h4 class="text-[11px] font-black uppercase text-zinc-500 tracking-[0.3em] flex items-center gap-3">
                        <span class="size-2 rounded-full bg-indigo-500 shadow-[0_0_8px_#6366f1]"></span> 03. Core Stats
                    </h4>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-5 bg-white/2 rounded-[1.5rem] border border-white/5">
                            <div class="flex items-center gap-4">
                                <flux:icon name="check-badge" variant="solid" class="size-5 text-indigo-400" />
                                <span class="text-xs font-bold text-zinc-300 uppercase tracking-tight">Lembretes</span>
                            </div>
                            <span class="text-lg font-black text-white italic">{{ $report['reminders_count'] ?? 0 }}</span>
                        </div>

                        <div class="flex items-center justify-between p-5 bg-white/2 rounded-[1.5rem] border border-white/5">
                            <div class="flex items-center gap-4">
                                <flux:icon name="globe-alt" variant="solid" class="size-5 text-indigo-400" />
                                <span class="text-xs font-bold text-zinc-300 uppercase tracking-tight">Interações</span>
                            </div>
                            <span class="text-lg font-black text-white italic">{{ $report['social_count'] ?? 0 }}</span>
                        </div>

                        <div class="p-6 rounded-[2rem] bg-gradient-to-br from-indigo-600/10 via-white/2 to-transparent border border-indigo-500/20 shadow-2xl">
                            <flux:icon name="sparkles" variant="solid" class="size-5 text-indigo-400 mb-4 animate-pulse" />
                            <p class="text-xs font-medium text-zinc-300 leading-relaxed italic">
                                @if(!$isPremium)
                                    "..."
                                @elseif(($report['xp_today'] ?? 0) > 50)
                                    "A tua performance diária é superior à média do grupo. O teu ganho de XP hoje acelerou a tua subida de nível em 22%."
                                @else
                                    "Dia de estabilidade sistémica. Lembra-te que cada pequeno registo contribui para a pontuação de saúde global."
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- RODAPÉ TÉCNICO --}}
        <div class="relative z-10 bg-zinc-900/50 px-10 py-6 border-t border-zinc-800 flex flex-col md:flex-row justify-between items-center gap-4 opacity-40">
            <div class="flex items-center gap-4">
                <span class="text-[8px] font-mono text-zinc-600 uppercase tracking-[0.2em]">TERMINAL_ID: #{{ auth()->id() }}</span>
                <span class="text-[8px] font-mono text-zinc-600 uppercase tracking-[0.2em]">NODE: v4.8_STABLE</span>
            </div>
            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.4em] italic text-center md:text-right">Finance Pro Intelligent Hub Operations · Last Synced {{ now()->format('H:i:s') }}</p>
        </div>
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

</div>
