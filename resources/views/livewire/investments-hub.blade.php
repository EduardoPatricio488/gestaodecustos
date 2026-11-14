<div x-data="{ showXtbUploadModal: false }" @open-mobile-investment-upload.window="showXtbUploadModal = true" @open-mobile-investment-create.window="$wire.createAsset()" @keydown.escape.window="showXtbUploadModal = false" class="space-y-8 pb-20 p-4 lg:p-10" wire:init="refreshPrices(true)" wire:poll.60s.visible="refreshPrices(true)">
    @if($mobileMode)
        @include('livewire.partials.investment-modal')
        @include('livewire.partials.investment-report-modal')
    @else


    {{-- 2. HEADER SaaS PREMIUM --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 px-2">
        <div class="flex items-center gap-4 sm:gap-6">
            <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-xl shadow-indigo-500/10">
                <flux:icon name="chart-bar-square" class="w-8 h-8 sm:w-10 sm:h-10 text-indigo-600" />
            </div>
            <div class="min-w-0">
                <h1 class="text-3xl sm:text-2xl sm:text-3xl md:text-4xl font-black uppercase italic tracking-tighter leading-none text-zinc-900 dark:text-white">
                    Investimentos
                </h1>
                @if($lastUpdated)
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1 italic">
                        Update: {{ $lastUpdated }}
                    </p>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 w-full lg:w-auto justify-end">
            <flux:button
                type="button"
                @click="showXtbUploadModal = true"
                variant="primary"
                class="w-full sm:w-auto !h-12 sm:!h-12 !px-5 rounded-xl !border !border-emerald-600 !bg-emerald-600 !text-white text-[11px] sm:text-xs font-bold uppercase tracking-wider !shadow-md hover:!bg-emerald-700 hover:!border-emerald-700 transition-colors duration-150">
                <span class="flex items-center justify-center gap-2.5">
                    <flux:icon name="document-arrow-up" class="size-5 text-white" />
                    <span>Upload de relatórios</span>
                </span>
            </flux:button>

            <flux:button
                wire:click="refreshPrices"
                variant="ghost"
                class="w-full sm:w-auto rounded-xl border border-zinc-200 dark:border-zinc-800 h-11 sm:h-12 text-[10px] font-black uppercase tracking-widest">
                <flux:icon name="arrow-path" class="size-4 mr-2 {{ $isRefreshing ? 'animate-spin' : '' }}" />
                Atualizar Preços
            </flux:button>

            <flux:button
                @click="open = true; $wire.createAsset()"
                variant="primary"
                icon="plus"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 rounded-2xl px-6 sm:px-8 font-black uppercase shadow-lg shadow-indigo-500/20 h-11 sm:h-12 transition-all hover:scale-[1.02]">
                Novo Ativo
            </flux:button>

            <button
                wire:click="toggleNetValues"
                @class([
                    'w-full sm:w-auto flex items-center justify-center sm:justify-start gap-2 px-4 h-11 sm:h-12 rounded-2xl border transition-all font-black uppercase text-[9px] tracking-widest',
                    'bg-emerald-500/10 border-emerald-500 text-emerald-600 shadow-lg shadow-emerald-500/10' => $showNetValues,
                    'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-400' => !$showNetValues
                ])>
                <flux:icon name="{{ $showNetValues ? 'eye' : 'eye-slash' }}" class="size-4" />
                {{ $showNetValues ? 'Ver Valor Líquido (Pós-IRS)' : 'Simular Saída (IRS 28%)' }}
            </button>
        </div>
    </div>

    {{-- 1. MARKET TICKER --}}
    <div class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 py-3">

        {{-- Fade nas extremidades --}}
        <div class="pointer-events-none absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-white dark:from-zinc-900 to-transparent z-10"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white dark:from-zinc-900 to-transparent z-10"></div>

        {{-- Indicador LIVE --}}
        <div class="absolute top-1/2 -translate-y-1/2 left-3 z-20 flex items-center gap-1.5 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm pr-2 rounded-full pl-2 py-1">
            <span class="relative flex size-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"></span>
                <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
            </span>
            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Live</span>
        </div>

        {{-- Faixa animada --}}
        <div class="flex w-max animate-[ticker_40s_linear_infinite] gap-4 pl-24 hover:[animation-play-state:paused]">
            @for ($i = 0; $i < 2; $i++)
                @foreach($marketData as $ticker => $data)
                    @php $isUp = str_contains($data['change'], '+'); @endphp
                    <div
                        wire:key="ticker-{{ $ticker }}-{{ $i }}"
                        class="flex-shrink-0 flex items-center gap-3 px-3 sm:px-4 border-r border-zinc-100 dark:border-zinc-800 last:border-0">
                        <div class="flex items-center gap-2">
                            <flux:icon
                                name="{{ $isUp ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                                class="size-4 {{ $isUp ? 'text-emerald-500' : 'text-red-500' }}" />
                            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                                {{ $ticker }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-black dark:text-white italic tracking-tighter">
                            {{ $data['price'] }} €
                        </p>
                        <span class="text-[10px] font-black {{ $isUp ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ $data['change'] }}
                        </span>
                    </div>
                @endforeach
            @endfor
        </div>
    </div>

    @once
        <style>
            @keyframes ticker {
                from { transform: translateX(0); }
                to { transform: translateX(-50%); }
            }
        </style>
    @endonce
{{-- ABAS DESTACADAS E CENTRADAS --}}
<div class="flex justify-center mt-12 mb-6">
    <div class="inline-flex bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-lg shadow-indigo-500/10 p-1">

        <button wire:click="switchTab('portfolio')"
            class="px-6 py-3 text-sm font-black uppercase tracking-widest rounded-xl transition-all
                {{ $tab === 'portfolio'
                    ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30 scale-[1.03]'
                    : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
            Portefólio
        </button>

        <button wire:click="switchTab('analysis')"
            class="px-6 py-3 text-sm font-black uppercase tracking-widest rounded-xl transition-all
                {{ $tab === 'analysis'
                    ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30 scale-[1.03]'
                    : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
            Análise IA
        </button>

    </div>
</div>


{{-- ========================= --}}
{{-- TAB: PORTFÓLIO (TODO O TEU CONTEÚDO AQUI) --}}
{{-- ========================= --}}
@if($tab === 'portfolio')

    {{-- ⚠️ AQUI VAI TODO O TEU CONTEÚDO ORIGINAL DO PORTFÓLIO ⚠️ --}}
    {{-- Copia TUDO o que tens a seguir no ficheiro e cola aqui dentro --}}
    {{-- Nada pode ficar fora deste bloco --}}

@endif



        {{-- ========================= --}}
{{-- TAB: ANÁLISE IA (ISOLADA) --}}
{{-- ========================= --}}
@if($tab === 'analysis')
    @php
        $advisorCurrency = auth()->user()->currentWorkspace?->currency ?? 'EUR';
        $money = fn ($value) => number_format((float) $value, 2, ',', ' ').' '.$advisorCurrency;
        $hasSalaryConfigured = (bool) ($advisorData['salary_configured'] ?? false);
        $monthlySurplus = (float) ($advisorData['monthly_surplus'] ?? 0);
        $topShare = (float) ($advisorData['top_asset_percentage'] ?? 0);
        $riskLabel = $advisorData['asset_count'] === 0 ? 'Sem carteira' : (($topShare >= 60 || $advisorData['asset_count'] < 3) ? 'Concentração elevada' : ($topShare >= 35 ? 'Concentração moderada' : 'Mais distribuída'));
    @endphp

    <div class="space-y-7 mt-6">
        <section class="relative isolate overflow-hidden rounded-[2rem] border border-zinc-700/80 bg-[#030407] p-6 sm:p-9 lg:p-10 text-white shadow-[0_28px_90px_-35px_rgba(0,0,0,0.95)]">
            <div class="pointer-events-none absolute -right-16 -top-20 size-72 rounded-full bg-indigo-500/[0.09] blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-28 left-[35%] size-64 rounded-full bg-violet-500/[0.07] blur-3xl"></div>

            <div class="relative z-10 grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_340px] items-center gap-8 xl:gap-12">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-300/25 bg-indigo-400/10 px-3 py-1.5 text-[9px] font-black uppercase tracking-[0.2em] text-indigo-200">
                        <span class="relative flex size-2">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-indigo-400 opacity-70"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-indigo-300"></span>
                        </span>
                        Consultor Financeiro IA
                    </div>
                    <h2 class="mt-5 text-3xl sm:text-4xl lg:text-[2.7rem] font-black tracking-tight leading-[1.08]">
                        A tua carteira,<br class="hidden sm:block">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-violet-300 to-sky-300">com uma estratégia.</span>
                    </h2>
                    <p class="mt-4 max-w-xl text-sm sm:text-[15px] leading-7 text-zinc-300">
                        Uma visão conjunta dos teus investimentos e dos teus movimentos financeiros para perceberes o que tens, quanto poderás investir e onde deves ter atenção.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-[10px] font-bold text-zinc-300"><flux:icon name="chart-bar-square" class="size-3.5 text-indigo-300" /> Investimentos</span>
                        <span class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-[10px] font-bold text-zinc-300"><flux:icon name="arrow-path" class="size-3.5 text-violet-300" /> Movimentos</span>
                        <span class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-[10px] font-bold text-zinc-300"><flux:icon name="sparkles" class="size-3.5 text-sky-300" /> Estratégia</span>
                    </div>
                </div>

                <div class="w-full rounded-[1.6rem] border border-white/10 bg-white/[0.045] p-5 sm:p-6 shadow-2xl shadow-black/40 backdrop-blur-sm">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Análise personalizada</p>
                            <p class="mt-1 text-sm font-bold text-white">Transforma dados em decisões.</p>
                        </div>
                        <div class="flex size-10 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300 ring-1 ring-indigo-400/20">
                            <flux:icon name="sparkles" class="size-5" />
                        </div>
                    </div>

                    <button type="button" wire:click="analyzePortfolio" wire:loading.attr="disabled" wire:target="analyzePortfolio"
                        class="group relative flex min-h-[58px] w-full items-center justify-center gap-3 overflow-hidden rounded-xl bg-gradient-to-r from-indigo-500 via-violet-500 to-indigo-600 px-5 py-4 text-sm font-black text-white shadow-[0_12px_35px_-10px_rgba(99,102,241,0.8)] ring-1 ring-white/20 transition duration-300 hover:-translate-y-0.5 hover:scale-[1.015] hover:shadow-[0_18px_45px_-10px_rgba(129,92,246,0.85)] active:translate-y-0 active:scale-[0.99] disabled:cursor-wait disabled:opacity-75">
                        <span class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700 group-hover:translate-x-full"></span>
                        <span wire:loading.remove wire:target="analyzePortfolio" class="relative inline-flex items-center gap-2">
                            <flux:icon name="sparkles" class="size-5" /> Analisar a minha carteira
                            <span class="text-lg transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <span wire:loading wire:target="analyzePortfolio" class="relative inline-flex items-center gap-2">
                            <flux:icon name="arrow-path" class="size-5 animate-spin" /> A analisar a tua carteira...
                        </span>
                    </button>
                    <div wire:loading wire:target="analyzePortfolio" class="mt-3 flex items-center gap-2 text-[10px] font-semibold text-indigo-200">
                        <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-white/10"><span class="block h-full w-1/3 animate-[pulse_1.2s_ease-in-out_infinite] rounded-full bg-indigo-300"></span></span>
                        A cruzar os teus dados financeiros
                    </div>
                    <p class="mt-3 text-center text-[10px] leading-5 text-zinc-400">A análise usa os dados registados no Finance Pro.</p>

                    @if($portfolioAnalysisCompleted)
                        <a href="#portfolio-ai-analysis" class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-emerald-400/25 bg-emerald-400/10 px-3 py-3 text-left transition hover:bg-emerald-400/15">
                            <span class="flex items-center gap-2 text-xs font-bold text-emerald-300"><span class="text-sm font-black">✓</span> Análise concluída! Está no fim da página.</span>
                            <span class="shrink-0 text-[10px] font-black uppercase tracking-wider text-emerald-200">Ver <span class="text-sm">↓</span></span>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Valor atual</p>
                <p class="mt-2 text-2xl font-black tracking-tight dark:text-white">{{ $money($advisorData['current_value']) }}</p>
                <p class="mt-1 text-xs text-zinc-500">Valor estimado da carteira</p>
            </div>
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Capital investido</p>
                <p class="mt-2 text-2xl font-black tracking-tight dark:text-white">{{ $money($advisorData['invested']) }}</p>
                <p class="mt-1 text-xs text-zinc-500">{{ $advisorData['asset_count'] }} posições registadas</p>
            </div>
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Lucro / prejuízo</p>
                <p class="mt-2 text-2xl font-black tracking-tight {{ $advisorData['profit'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ $advisorData['profit'] >= 0 ? '+' : '' }}{{ $money($advisorData['profit']) }}</p>
                <p class="mt-1 text-xs text-zinc-500">{{ number_format($advisorData['profit_percent'], 2, ',', ' ') }}% face ao capital investido</p>
            </div>
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Capacidade mensal estimada</p>
                <p class="mt-2 text-2xl font-black tracking-tight {{ $monthlySurplus >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ $money($monthlySurplus) }}</p>
                <p class="mt-1 text-xs text-zinc-500">Salário líquido menos despesas médias</p>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-5 gap-5">
            <div class="xl:col-span-3 rounded-[1.75rem] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-7">
                <div class="flex items-center justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-lg font-black dark:text-white">Quanto posso investir?</h3>
                        <p class="text-xs text-zinc-500 mt-1">Salário líquido configurado e despesas médias dos últimos 90 dias</p>
                    </div>
                    <span class="rounded-full bg-indigo-500/10 text-indigo-500 px-3 py-1 text-[9px] font-black uppercase tracking-wider">Referência</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Salário líquido / mês</p>
                        <p class="mt-2 text-xl font-black dark:text-white">{{ $money($advisorData['monthly_income']) }}</p>
                    </div>
                    <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Despesas médias / mês</p>
                        <p class="mt-2 text-xl font-black dark:text-white">{{ $money($advisorData['monthly_expenses']) }}</p>
                    </div>
                </div>
                <div class="mt-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-5">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Intervalo indicativo para investir / mês</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight dark:text-white">{{ $money($advisorData['suggested_monthly_min']) }} <span class="text-zinc-400 text-base">a</span> {{ $money($advisorData['suggested_monthly_max']) }}</p>
                    <p class="mt-2 text-xs leading-5 text-zinc-500">Corresponde a 20%–30% do excedente médio positivo. Não é uma ordem de investimento: confirma primeiro despesas futuras, dívidas e reserva de emergência.</p>
                </div>
                @if(!$hasSalaryConfigured)
                    <p class="mt-4 text-xs text-amber-600 dark:text-amber-400">Ainda não tens um salário líquido configurado em Rendimentos Fixos. Regista o teu contrato de trabalho para calcular a capacidade de investimento.</p>
                @elseif($advisorData['expense_records'] < 3)
                    <p class="mt-4 text-xs text-amber-600 dark:text-amber-400">Existem poucos registos de despesas. O cálculo usa o salário líquido configurado e a média das despesas disponíveis.</p>
                @endif
            </div>

            <div class="xl:col-span-2 rounded-[1.75rem] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-7">
                <h3 class="text-lg font-black dark:text-white">Diagnóstico de risco</h3>
                <p class="text-xs text-zinc-500 mt-1">Leitura simples da diversificação atual</p>
                <div class="mt-6 flex items-center gap-3">
                    <div class="size-12 rounded-2xl flex items-center justify-center {{ $topShare >= 60 ? 'bg-amber-500/10 text-amber-500' : 'bg-indigo-500/10 text-indigo-500' }}">
                        <span class="text-xl font-black" aria-hidden="true">!</span>
                    </div>
                    <div>
                        <p class="font-black dark:text-white">{{ $riskLabel }}</p>
                        <p class="text-xs text-zinc-500">Maior posição: {{ number_format($topShare, 1, ',', ' ') }}% da carteira</p>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    @forelse($advisorData['composition'] as $part)
                        <div>
                            <div class="flex justify-between text-xs mb-1.5"><span class="font-bold dark:text-zinc-200">{{ $part['type'] }}</span><span class="text-zinc-500">{{ number_format($part['percentage'], 1, ',', ' ') }}%</span></div>
                            <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden"><div class="h-full rounded-full bg-indigo-500" style="width: {{ min(100, max(0, $part['percentage'])) }}%"></div></div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">Ainda não existem ativos registados.</p>
                    @endforelse
                </div>
                @if($advisorData['top_asset'])
                    <p class="mt-5 text-xs text-zinc-500">Maior posição: <strong class="text-zinc-700 dark:text-zinc-200">{{ $advisorData['top_asset']['name'] }}</strong> · {{ $money($advisorData['top_asset']['value']) }}</p>
                @endif
                <p class="mt-4 text-[10px] leading-4 text-zinc-400">O risco real também depende dos ativos subjacentes, regiões, setores, moeda e horizonte temporal, informação que pode não estar disponível nesta carteira.</p>
            </div>
        </section>

        <section class="rounded-[1.75rem] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div>
                    <h3 class="text-lg font-black dark:text-white">As tuas posições</h3>
                    <p class="text-xs text-zinc-500 mt-1">Resumo de cada ativo registado</p>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ $advisorData['asset_count'] }} ativos</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-left">
                    <thead><tr class="text-[9px] uppercase tracking-widest text-zinc-400 border-b border-zinc-100 dark:border-zinc-800"><th class="pb-3">Ativo</th><th class="pb-3 text-right">Valor atual</th><th class="pb-3 text-right">Resultado</th><th class="pb-3 text-right">Rentabilidade</th></tr></thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($advisorData['assets'] as $asset)
                            <tr>
                                <td class="py-3"><p class="text-sm font-black dark:text-white">{{ $asset['name'] }}</p><p class="text-[10px] text-zinc-500">{{ $asset['symbol'] ?: $asset['type'] }}</p></td>
                                <td class="py-3 text-right text-sm font-bold dark:text-zinc-200">{{ $money($asset['value']) }}</td>
                                <td class="py-3 text-right text-sm font-bold {{ $asset['profit'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ $asset['profit'] >= 0 ? '+' : '' }}{{ $money($asset['profit']) }}</td>
                                <td class="py-3 text-right text-sm font-bold {{ $asset['profit_percent'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ number_format($asset['profit_percent'], 2, ',', ' ') }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-sm text-zinc-500">Adiciona ou importa investimentos para o consultor conseguir analisar a tua carteira.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section id="portfolio-ai-analysis" class="scroll-mt-8 rounded-[1.75rem] border border-indigo-500/20 bg-indigo-500/[0.03] p-6 sm:p-7">
            <div class="flex items-center gap-3 mb-4">
                <div class="size-10 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center"><span class="text-xs font-black tracking-tight" aria-hidden="true">IA</span></div>
                <div><h3 class="text-lg font-black dark:text-white">Opinião do consultor IA</h3><p class="text-xs text-zinc-500">Diagnóstico, riscos e próximos passos</p></div>
            </div>
            @if($portfolioAdvisorAnalysis)
                @if($portfolioAnalysisDate)
                    <p class="mb-3 text-xs font-semibold text-zinc-500">Análise realizada em {{ $portfolioAnalysisDate }}</p>
                @endif
                <div class="rounded-2xl bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-5 text-sm leading-7 text-zinc-700 dark:text-zinc-200 whitespace-pre-line">{{ $portfolioAdvisorAnalysis }}</div>
            @else
                <div class="rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-700 p-6 text-center">
                    <p class="text-sm font-bold dark:text-white">Ainda não foi feita uma análise personalizada.</p>
                    <p class="mt-1 text-xs text-zinc-500">Carrega em «Analisar a minha carteira» para receber uma opinião com base nos teus dados.</p>
                </div>
            @endif
            @if($portfolioAnalysisHistory->isNotEmpty())
                <div class="mt-6 border-t border-zinc-200 dark:border-zinc-800 pt-5">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h4 class="text-sm font-black dark:text-white">Histórico de análises</h4>
                        <span class="text-[10px] text-zinc-500">{{ $portfolioAnalysisHistory->count() }} registos recentes</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($portfolioAnalysisHistory as $historyItem)
                            <button type="button" wire:click="showPortfolioAnalysis({{ $historyItem->id }})"
                                class="rounded-xl border px-3 py-2 text-left transition {{ $selectedPortfolioAnalysisId === $historyItem->id ? 'border-indigo-500 bg-indigo-500/10 text-indigo-600 dark:text-indigo-300' : 'border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:border-indigo-400' }}">
                                <span class="block text-xs font-bold">{{ $historyItem->created_at->format('d/m/Y') }}</span>
                                <span class="block text-[10px] opacity-70">{{ $historyItem->created_at->format('H:i') }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
            <p class="mt-4 text-[10px] leading-5 text-zinc-400">Informação educativa, não constitui aconselhamento financeiro regulado nem substitui a avaliação do teu perfil, objetivos, prazo e tolerância ao risco. Os valores dependem da qualidade e atualização dos dados registados.</p>
        </section>
    </div>
@endif

@if($tab === 'portfolio')
    {{-- 3. CARDS DE PERFORMANCE (WEALTH DASHBOARD) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 px-2">
        {{-- Card Principal: Avaliação Total --}}
        <div class="stat-card bg-zinc-950 text-white p-6 sm:p-8 lg:p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 lg:col-span-2 group">
            <div class="absolute top-0 right-0 w-64 sm:w-80 h-64 sm:h-80 bg-indigo-500/10 blur-[100px] rounded-full -mr-16 sm:-mr-20 -mt-16 sm:-mt-20 group-hover:bg-indigo-500/20 transition-all duration-1000"></div>
            <div class="absolute bottom-0 left-0 w-52 sm:w-64 h-52 sm:h-64 {{ $totalProfit >= 0 ? 'bg-emerald-500/5' : 'bg-red-500/5' }} blur-[100px] rounded-full -ml-16 sm:-ml-20 -mb-16 sm:-mb-20 transition-all duration-1000"></div>

            <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <h2 class="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 italic">
                            Avaliação do Portefólio
                        </h2>

                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full {{ $totalProfit >= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                            <flux:icon
                                name="{{ $totalProfit >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                                class="size-3.5" />
                            <span class="text-[10px] font-black tracking-tighter">
                                {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalPnlPct, 2) }}%
                            </span>
                        </div>
                    </div>

                    <p class="text-5xl sm:text-6xl lg:text-7xl font-black text-white tracking-tighter italic leading-none break-words">
                        {{ number_format($currentValue, 2, ',', ' ') }}
                        <span class="text-2xl sm:text-3xl text-indigo-500 font-bold uppercase not-italic">€</span>
                    </p>

                    {{-- AVISO DE IMPOSTO --}}
                    @if($showNetValues)
                        <p class="text-[9px] sm:text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] mt-4 flex flex-wrap items-center gap-2">
                            <flux:icon name="shield-check" class="size-3" />
                            Já descontado o IRS estimado de {{ number_format($estimatedTax, 2, ',', ' ') }} €
                        </p>
                    @endif
                </div>

                <div class="mt-10 sm:mt-12 flex flex-wrap gap-4 items-stretch">
                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:icon name="banknotes" class="size-3.5 text-zinc-500" />
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest italic">
                                Custo Aquisição
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-zinc-200 tracking-tighter">
                            {{ number_format($totalInvested, 2, ',', ' ') }} €
                        </p>
                    </div>

                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:icon
                                name="{{ $totalProfit >= 0 ? 'plus-circle' : 'minus-circle' }}"
                                class="size-3.5 {{ $totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }}" />
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest italic">
                                Lucro / Prejuízo
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black tracking-tighter {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfit, 2, ',', ' ') }} €
                        </p>
                    </div>

                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 rounded-2xl border backdrop-blur-md {{ $totalProfit >= 0 ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-red-500/10 border-red-500/20' }}">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:icon
                                name="chart-bar-square"
                                class="size-3.5 {{ $totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }}" />
                            <p class="text-[9px] uppercase font-black tracking-widest italic {{ $totalProfit >= 0 ? 'text-emerald-400/80' : 'text-red-400/80' }}">
                                Rentabilidade Global
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black tracking-tighter {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalPnlPct, 2) }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Secundário: Diversificação --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-6 sm:p-8 flex flex-col justify-center text-center shadow-sm group">
            <div class="relative inline-block mx-auto mb-6">
                <div class="absolute inset-0 bg-indigo-500/20 blur-xl rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative p-5 sm:p-6 bg-indigo-50 dark:bg-indigo-950/40 rounded-[2rem] text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800 shadow-inner">
                    <flux:icon name="shield-check" variant="outline" class="size-9 sm:size-10" />
                </div>
            </div>
            <h3 class="font-black text-[9px] sm:text-[10px] uppercase tracking-[0.2em] text-zinc-400 italic">
                Posições Ativas
            </h3>
            <p class="text-4xl sm:text-5xl font-black dark:text-white uppercase italic tracking-tighter mt-1">
                {{ $myAssets->count() }}
            </p>
            <p class="mt-4 text-[10px] text-zinc-400 font-medium italic leading-relaxed px-2 sm:px-4">
                Capital distribuído por {{ $myAssets->count() }} frentes.
            </p>
        </div>
    </div>

    {{-- 4. SMART INSIGHTS DASHBOARD --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-2 mt-8">
        @if($bestPerformer)
            <button
                type="button"
                wire:click="editAsset({{ $bestPerformer->id }})"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-emerald-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <flux:icon name="arrow-trending-up" class="size-5 sm:size-6" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Melhor Performance
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            {{ $bestPerformer->type }}
                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        {{ $bestPerformer->symbol }}
                        <span class="text-emerald-500 ml-1">
                            +{{ number_format($bestPerformer->pnl_percent, 1) }}%
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        {{ $bestPerformer->name }}
                    </p>
                </div>
            </button>
        @endif

        @if($worstPerformer)
            <button
                type="button"
                wire:click="editAsset({{ $worstPerformer->id }})"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-red-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <flux:icon name="arrow-trending-down" class="size-5 sm:size-6" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Pior Performance
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            {{ $worstPerformer->type }}
                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        {{ $worstPerformer->symbol }}
                        <span class="text-red-500 ml-1">
                            {{ number_format($worstPerformer->pnl_percent, 1) }}%
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        {{ $worstPerformer->name }}
                    </p>
                </div>
            </button>
        @endif

        @if($highestExposure)
            <button
                type="button"
                wire:click="editAsset({{ $highestExposure->id }})"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-indigo-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <flux:icon name="chart-pie" class="size-5 sm:size-6" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Maior Exposição
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            {{ $highestExposure->type }}
                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        {{ $highestExposure->symbol }}
                        <span class="text-zinc-500 ml-1">
                            {{ number_format($highestExposure->current_value, 0, ',', ' ') }}€
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        {{ $highestExposure->name }}
                    </p>
                </div>
            </button>
        @endif
    </div>

    {{-- 5. DISTRIBUIÇÃO POR CLASSE --}}
    <div class="px-2 mt-8">
        <div class="p-6 sm:p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">

            {{-- Cabeçalho --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <div class="size-9 sm:size-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner">
                        <flux:icon name="chart-pie" class="size-4 sm:size-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase italic tracking-tighter dark:text-white">
                            Distribuição por Classe
                        </h3>
                        <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                            Composição do Portefólio
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        wire:click="setFilter('Todos')"
                        aria-label="Mostrar todos os ativos, sem filtro por classe"
                        class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-[9px] font-black uppercase tracking-widest transition-all
                            {{ $filterType === 'Todos'
                                ? 'border-indigo-500 bg-indigo-600 text-white shadow-md shadow-indigo-500/20'
                                : 'border-zinc-200 bg-zinc-50 text-zinc-500 hover:border-indigo-400 hover:text-indigo-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:text-indigo-400' }}">
                        <flux:icon name="chart-pie" class="size-3.5" />
                        Todas as classes
                    </button>
                    <p class="text-lg sm:text-xl font-black dark:text-white italic tracking-tighter">
                        {{ number_format($currentValue, 2, ',', ' ') }} €
                    </p>
                </div>
            </div>

            {{-- Barra Combinada --}}
            <div class="h-3 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner flex mb-8 sm:mb-10">
                @foreach(['Acao', 'Cripto', 'ETF', 'Fundo', 'Divida'] as $key)
                    @php
                        $pct = $composition->get($key)['percent'] ?? 0;
                        $barClass = match($key) {
                            'Acao'   => 'bg-emerald-500',
                            'Cripto' => 'bg-indigo-500',
                            'ETF'    => 'bg-amber-500',
                            'Fundo'  => 'bg-pink-500',
                            'Divida' => 'bg-blue-500',
                            default  => 'bg-zinc-500',
                        };
                    @endphp
                    @if($pct > 0)
                        <div
                            class="h-full {{ $barClass }} transition-all duration-1000 first:rounded-l-full last:rounded-r-full"
                            style="width: {{ $pct }}%">
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Categorias --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 sm:gap-8">
                @foreach([
                    'Acao'   => ['Ações',  'chart-bar-square'],
                    'Cripto' => ['Cripto', 'cube'],
                    'ETF'    => ['ETF',    'squares-2x2'],
                    'Fundo'  => ['Fundo',  'briefcase'],
                    'Divida' => ['Dívida', 'building-library'],
                ] as $key => [$label, $icon])
                    @php
                        $data  = $composition->get($key) ?? ['percent' => 0, 'total' => 0];
                        $classes = match($key) {
                            'Acao'   => [
                                'hover'  => 'hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10',
                                'iconBg' => 'bg-emerald-50 dark:bg-emerald-900/30',
                                'iconFg' => 'text-emerald-600 dark:text-emerald-400',
                                'bar'    => 'bg-emerald-500',
                            ],
                            'Cripto' => [
                                'hover'  => 'hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10',
                                'iconBg' => 'bg-indigo-50 dark:bg-indigo-900/30',
                                'iconFg' => 'text-indigo-600 dark:text-indigo-400',
                                'bar'    => 'bg-indigo-500',
                            ],
                            'ETF'    => [
                                'hover'  => 'hover:bg-amber-50/50 dark:hover:bg-amber-900/10',
                                'iconBg' => 'bg-amber-50 dark:bg-amber-900/30',
                                'iconFg' => 'text-amber-600 dark:text-amber-400',
                                'bar'    => 'bg-amber-500',
                            ],
                            'Fundo'  => [
                                'hover'  => 'hover:bg-pink-50/50 dark:hover:bg-pink-900/10',
                                'iconBg' => 'bg-pink-50 dark:bg-pink-900/30',
                                'iconFg' => 'text-pink-600 dark:text-pink-400',
                                'bar'    => 'bg-pink-500',
                            ],
                            'Divida' => [
                                'hover'  => 'hover:bg-blue-50/50 dark:hover:bg-blue-900/10',
                                'iconBg' => 'bg-blue-50 dark:bg-blue-900/30',
                                'iconFg' => 'text-blue-600 dark:text-blue-400',
                                'bar'    => 'bg-blue-500',
                            ],
                            default  => [
                                'hover'  => 'hover:bg-zinc-50/50 dark:hover:bg-zinc-900/10',
                                'iconBg' => 'bg-zinc-50 dark:bg-zinc-900/30',
                                'iconFg' => 'text-zinc-600 dark:text-zinc-400',
                                'bar'    => 'bg-zinc-500',
                            ],
                        };
                    @endphp
                    <button
                        type="button"
                        wire:click="setFilter('{{ $key }}')"
                        wire:key="comp-{{ $key }}"
                        class="space-y-3 text-left p-4 -m-2 sm:-m-4 rounded-2xl transition-all {{ $classes['hover'] }} group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="size-7 sm:size-8 rounded-xl {{ $classes['iconBg'] }} {{ $classes['iconFg'] }} flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                    <flux:icon name="{{ $icon }}" class="size-4" />
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-500 tracking-widest">
                                    {{ $label }}
                                </span>
                            </div>
                            <span class="text-xs sm:text-sm font-black dark:text-white italic tracking-tighter">
                                {{ $data['percent'] }}%
                            </span>
                        </div>

                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner">
                            <div
                                class="h-full {{ $classes['bar'] }} rounded-full transition-all duration-1000"
                                style="width: {{ $data['percent'] }}%">
                            </div>
                        </div>

                        <p class="text-[10px] font-bold text-zinc-400 italic">
                            {{ number_format($data['total'], 2, ',', ' ') }} €
                        </p>
                    </button>
                @endforeach
            </div>

        </div>
    </div>

    {{-- 6. PESQUISA E FILTROS --}}
    <div class="flex flex-col md:flex-row gap-4 md:gap-6 items-center justify-between px-2 mt-10 sm:mt-12">
        <div class="relative w-full md:w-96 group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-indigo-500">
                <flux:icon name="magnifying-glass" class="size-4" />
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="PESQUISAR TICKER..."
                class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl h-11 sm:h-12 pl-12 pr-4 text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" />
        </div>

        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full md:w-auto">
            @foreach(['Todos', 'Acao', 'Cripto', 'ETF', 'Fundo', 'Divida'] as $tab)
                <button
                    type="button"
                    wire:click="setFilter('{{ $tab }}')"
                    class="px-4 sm:px-5 py-2.5 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap
                        {{ $filterType === $tab
                            ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/20 ring-1 ring-indigo-500'
                            : 'bg-white dark:bg-zinc-900 text-zinc-400 border border-zinc-200 dark:border-zinc-800 hover:border-indigo-500/50' }}">
                    {{ match($tab) { 'Acao' => 'Ações', 'Divida' => 'Dívida', default => $tab } }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- 7. TABELA DE POSIÇÕES --}}
    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm mx-2 mt-6"
        x-data="{ open: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800 text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                    <tr>
                        <th class="p-4 sm:p-6">Ativo</th>
                        <th class="p-4 sm:p-6 text-center">Qtd</th>
                        <th class="p-4 sm:p-6 text-right">Preço Compra</th>
                        <th class="p-4 sm:p-6 text-right">Atual</th>
                        <th class="p-4 sm:p-6 text-right">Valor Investido</th>
                        <th class="p-4 sm:p-6 text-right sm:px-10">Lucro ou Prejuízo</th>
                        <th class="p-4 sm:p-6 text-center w-24">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($myAssets as $asset)
                        @php $isGain = $asset->pnl >= 0; @endphp

                        {{-- LINHA PRINCIPAL --}}
                        <tr
                            wire:key="asset-row-{{ $asset->id }}"
                            @click="open = open === {{ $asset->id }} ? null : {{ $asset->id }}"
                            class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all cursor-pointer select-none"
                            :class="open === {{ $asset->id }} ? 'bg-indigo-50/40 dark:bg-indigo-500/5' : ''">

                            {{-- COLUNA: ATIVO --}}
                            <td class="p-4 sm:p-6">
                                <div class="flex items-center gap-3 sm:gap-4">

                                    {{-- Seta expansível --}}
                                    <div class="size-4 sm:size-5 text-zinc-400 transition-transform duration-300 shrink-0"
                                         :class="open === {{ $asset->id }} ? 'rotate-90' : ''">
                                        <flux:icon name="chevron-right" class="size-4" />
                                    </div>

                                    {{-- Ícone / Sigla --}}
                                    <div class="size-10 sm:size-11 rounded-2xl bg-white dark:bg-zinc-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black text-[10px] sm:text-[11px] shadow-sm border border-zinc-100 dark:border-zinc-700 shrink-0">
                                        {{ substr($asset->symbol, 0, 3) }}
                                    </div>

                                    {{-- Nome + detalhes --}}
                                    <div class="min-w-0">
                                        <p class="text-sm sm:text-base font-black dark:text-white uppercase leading-none tracking-tight truncate">
                                            {{ $asset->name }}
                                        </p>

                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-1">
                                            <span class="text-[8px] sm:text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                                {{ $asset->symbol }}
                                            </span>

                                            {{-- Tipo --}}
                                            <span class="text-[7px] sm:text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md
                                                {{ match($asset->type) {
                                                    'ETF'    => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                                                    'Cripto' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400',
                                                    'Acao'   => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                                    'Fundo'  => 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400',
                                                    'Divida' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                                    default  => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500',
                                                } }}">
                                                {{ match($asset->type) {
                                                    'Acao' => 'Ação',
                                                    'Divida' => 'Dívida',
                                                    default => $asset->type,
                                                } }}
                                            </span>

                                            {{-- Corretora --}}
                                            @if($asset->broker)
                                                <span class="text-[8px] sm:text-[9px] text-zinc-400 font-medium">
                                                    · {{ $asset->broker }}
                                                </span>
                                            @endif
                                            @if($asset->broker === 'XTB' && $asset->xtb_position_id)
                                                <span class="text-[8px] sm:text-[9px] text-zinc-400 font-medium">
                                                    · Posição {{ $asset->xtb_position_id }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- QTD --}}
                            <td class="p-4 sm:p-6 text-center font-black dark:text-zinc-200 text-xs sm:text-sm italic">
                                {{ number_format($asset->quantity, 4, ',', ' ') }}
                            </td>

                            {{-- PREÇO COMPRA --}}
                            <td class="p-4 sm:p-6 text-right font-bold text-zinc-500 text-[10px] sm:text-xs italic">
                                {{ number_format($asset->average_price, 2, ',', ' ') }} €
                            </td>

                            {{-- PREÇO ATUAL --}}
                            <td class="p-4 sm:p-6 text-right font-black dark:text-white italic tracking-tighter text-xs sm:text-sm">
                                {{ number_format($asset->current_price ?: $asset->average_price, 2, ',', ' ') }} €
                            </td>

                            {{-- VALOR INVESTIDO --}}
                            <td class="p-4 sm:p-6 text-right font-black dark:text-white text-sm sm:text-base italic tracking-tighter">
                                {{ number_format($asset->current_value, 2, ',', ' ') }} €
                            </td>

                            {{-- LUCRO / PREJUÍZO --}}
                            <td class="p-4 sm:p-6 text-right sm:px-10">
                                <div class="inline-flex flex-col items-end">
                                    <span class="text-sm font-black {{ $isGain ? 'text-emerald-500' : 'text-red-500' }} italic tracking-tighter">
                                        {{ $isGain ? '+' : '' }}{{ number_format($asset->pnl, 2, ',', ' ') }} €
                                    </span>
                                    <span class="text-[8px] sm:text-[9px] font-black {{ $isGain ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }} px-1.5 py-0.5 rounded uppercase mt-1">
                                        {{ $isGain ? '+' : '' }}{{ number_format($asset->pnl_percent, 2) }}%
                                    </span>
                                </div>
                            </td>

                            {{-- AÇÕES --}}
                            <td class="p-4 sm:p-6 text-center" @click.stop>
                                @if(($asset->position_count ?? 1) > 1)
                                    <span class="inline-flex rounded-lg bg-indigo-500/10 px-2 py-1 text-[9px] font-black text-indigo-500">
                                        {{ $asset->position_count }} posições
                                    </span>
                                @else
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="editAsset({{ $asset->id }})" variant="ghost" icon="pencil-square" size="sm" class="text-zinc-400 hover:text-indigo-600 rounded-xl" />
                                        <flux:button wire:click="deleteAsset({{ $asset->id }})" wire:confirm="Remover ativo?" variant="ghost" icon="trash" size="sm" class="text-zinc-400 hover:text-red-600 rounded-xl" />
                                    </div>
                                @endif
                            </td>
                        </tr>

                        {{-- PAINEL EXPANSÍVEL --}}
                        <tr
                            wire:key="asset-detail-{{ $asset->id }}"
                            x-show="open === {{ $asset->id }}"
                            x-collapse
                            class="bg-indigo-50/30 dark:bg-indigo-950/10">
                            <td colspan="7" class="px-6 sm:px-10 py-6">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                    {{-- BLOCO 1: IDENTIFICAÇÃO --}}
                                    <div class="p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-4">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-500 italic">
                                            Identificação
                                        </p>

                                        <div class="space-y-3">

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Ticker</span>
                                                <span class="text-xs font-black dark:text-white uppercase">{{ $asset->symbol }}</span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">ISIN</span>
                                                <span class="text-xs font-mono font-bold dark:text-white">{{ $asset->isin ?: '—' }}</span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Tipo</span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ $asset->type === 'Acao' ? 'Ação' : $asset->type }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Corretora</span>
                                                <span class="text-xs font-black dark:text-white">{{ $asset->broker ?: '—' }}</span>
                                            </div>

                                            {{-- CAMPOS ESPECIAIS PARA DÍVIDA --}}
                                            @if($asset->type === 'Divida')

                                                @if($asset->series)
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Série</span>
                                                        <span class="text-xs font-black dark:text-white">{{ $asset->series }}</span>
                                                    </div>
                                                @endif

                                                @if($asset->interest_rate)
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Taxa Anual Bruta</span>
                                                        <span class="text-xs font-black text-blue-600 dark:text-blue-400">
                                                            {{ number_format($asset->interest_rate, 3, ',', '') }}%
                                                        </span>
                                                    </div>
                                                @endif

                                                @if($asset->loyalty_bonus)
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Prémio Permanência</span>
                                                        <span class="text-xs font-black text-blue-500">
                                                            +{{ number_format($asset->loyalty_bonus, 3, ',', '') }}%
                                                        </span>
                                                    </div>
                                                @endif

                                                @if($asset->capitalization_date)
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Próx. Capitalização</span>
                                                        <span class="text-xs font-black dark:text-white">
                                                            {{ \Carbon\Carbon::parse($asset->capitalization_date)->format('d/m/Y') }}
                                                        </span>
                                                    </div>
                                                @endif

                                            @endif

                                            {{-- Exchange / Rede / Gestora --}}
                                            @if($asset->exchange ?? $asset->network ?? $asset->provider ?? null)
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        {{ $asset->type === 'Acao' ? 'Bolsa' : ($asset->type === 'Cripto' ? 'Rede' : 'Gestora') }}
                                                    </span>
                                                    <span class="text-xs font-black dark:text-white">
                                                        {{ $asset->exchange ?? $asset->network ?? $asset->provider ?? '—' }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- DATA DE COMPRA --}}
                                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Data de Compra
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ $asset->operation_date ? \Carbon\Carbon::parse($asset->operation_date)->format('d/m/Y') : '—' }}
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                                                        {{-- BLOCO 2: TRANSAÇÃO --}}
                                    <div class="p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-4">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-500 italic">
                                            Transação
                                        </p>

                                        <div class="space-y-3">

                                            @if($asset->operation_date)
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        Data
                                                    </span>
                                                    <span class="text-xs font-black dark:text-white">
                                                        {{ \Carbon\Carbon::parse($asset->operation_date)->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Quantidade
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->quantity, 4, ',', ' ') }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Preço / Unidade
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->average_price, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Taxas
                                                </span>
                                                <span class="text-xs font-black {{ ($asset->fees ?? 0) > 0 ? 'text-amber-500' : 'text-zinc-400' }}">
                                                    {{ number_format($asset->fees ?? 0, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Custo Total
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->cost, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                    {{-- BLOCO 3: PERFORMANCE --}}
                                    <div class="p-5 rounded-2xl border space-y-4
                                        {{ $isGain
                                            ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30'
                                            : 'bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-900/30' }}">

                                        <p class="text-[9px] font-black uppercase tracking-widest {{ $isGain ? 'text-emerald-500' : 'text-red-500' }} italic">
                                            Performance
                                        </p>

                                        <div class="space-y-3">

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Preço Atual
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->current_price ?: $asset->average_price, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Avaliação
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->current_value, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                            <div class="pt-2 border-t {{ $isGain ? 'border-emerald-100 dark:border-emerald-900/30' : 'border-red-100 dark:border-red-900/30' }}">

                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        P&L €
                                                    </span>
                                                    <span class="text-sm font-black {{ $isGain ? 'text-emerald-500' : 'text-red-500' }} italic tracking-tighter">
                                                        {{ $isGain ? '+' : '' }}{{ number_format($asset->pnl, 2, ',', ' ') }} €
                                                    </span>
                                                </div>

                                                <div class="flex justify-between items-center gap-2 mt-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        P&L %
                                                    </span>
                                                    <span class="text-sm font-black {{ $isGain ? 'text-emerald-500' : 'text-red-500' }} italic tracking-tighter">
                                                        {{ $isGain ? '+' : '' }}{{ number_format($asset->pnl_percent, 2) }}%
                                                    </span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </div>

                                @if(($asset->position_count ?? 1) > 1)
                                    <div class="mt-5 rounded-2xl border border-indigo-200 dark:border-indigo-900/50 bg-white dark:bg-zinc-900 p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Posições individuais</p>
                                            <span class="text-[10px] font-bold text-zinc-400">Os registos continuam separados</span>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($asset->positions as $position)
                                                <div wire:key="grouped-position-{{ $position->id }}" class="flex flex-wrap items-center justify-between gap-3 border-b last:border-0 border-zinc-100 dark:border-zinc-800 py-2">
                                                    <div>
                                                        <p class="text-xs font-black dark:text-white">{{ $position->name }}</p>
                                                        <p class="text-[10px] text-zinc-500">{{ $position->symbol }} · {{ number_format($position->quantity, 4, ',', ' ') }} unidades · Capital: {{ number_format($position->cost, 2, ',', ' ') }} €</p>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <flux:button wire:click="editAsset({{ $position->id }})" variant="ghost" icon="pencil-square" size="sm" class="text-zinc-400 hover:text-indigo-600" />
                                                        <flux:button wire:click="deleteAsset({{ $position->id }})" wire:confirm="Remover esta posição individual?" variant="ghost" icon="trash" size="sm" class="text-zinc-400 hover:text-red-600" />
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="p-16 sm:p-20 text-center text-zinc-400 italic">
                                Sem ativos registados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>























@include('livewire.partials.investment-modal')

{{-- RENDIMENTOS RECEBIDOS --}}
@if(isset($recentIncomes) && $recentIncomes->count() > 0)
<div class="px-2 mt-10">
    <div class="p-6 sm:p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="size-9 sm:size-10 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-inner">
                    <flux:icon name="banknotes" class="size-4 sm:size-5" />
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase italic tracking-tighter dark:text-white">
                        Rendimentos Recebidos
                    </h3>
                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                        Juros Líquidos · Dívida Pública
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest italic">
                    Total Acumulado
                </p>
                <p class="text-xl sm:text-2xl font-black text-blue-600 dark:text-blue-400 italic tracking-tighter">
                    +{{ number_format($totalIncomeNet, 2, ',', ' ') }} €
                </p>
            </div>
        </div>

        <div class="overflow-y-auto max-h-[380px] custom-scrollbar pr-2">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead class="sticky top-0 bg-white dark:bg-zinc-900 z-10 text-[9px] font-black uppercase text-zinc-400 tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                    <tr>
                        <th class="pb-4">Ativo</th>
                        <th class="pb-4 text-center">Tipo</th>
                        <th class="pb-4 text-right">Data</th>
                        <th class="pb-4 text-right">Taxa Base</th>
                        <th class="pb-4 text-right">Prémio</th>
                        <th class="pb-4 text-right">Juro Bruto</th>
                        <th class="pb-4 text-right">IRS 28%</th>
                        <th class="pb-4 text-right">Juro Líquido</th>
                        <th class="pb-4 text-right">Saldo Após</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @foreach($recentIncomes as $income)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition">
                            <td class="py-4">
                                <p class="text-xs font-black dark:text-white uppercase tracking-tight">
                                    {{ $income->investment?->symbol ?? '—' }}
                                </p>
                                <p class="text-[10px] text-zinc-400 truncate max-w-[160px]">
                                    {{ $income->investment?->name ?? '—' }}
                                </p>
                            </td>

                            <td class="py-4 text-center">
                                <span class="text-[8px] font-black uppercase px-2 py-1 rounded-lg
                                    {{ $income->type === 'CA'
                                        ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                                        : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400' }}">
                                    {{ $income->type === 'CA' ? 'Aforro' : 'Tesouro' }}
                                </span>
                            </td>

                            <td class="py-4 text-right text-xs font-bold text-zinc-500">
                                {{ $income->reference_date->format('d/m/Y') }}
                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-zinc-200">
                                {{ number_format($income->base_rate, 3, ',', '') }}%
                            </td>

                            <td class="py-4 text-right text-xs font-black text-blue-500">
                                +{{ number_format($income->loyalty_bonus, 3, ',', '') }}%
                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-zinc-200">
                                {{ number_format($income->gross_amount, 2, ',', ' ') }} €
                            </td>

                            <td class="py-4 text-right text-xs font-black text-red-500">
                                -{{ number_format($income->tax_amount, 2, ',', ' ') }} €
                            </td>

                            <td class="py-4 text-right">
                                <span class="text-sm font-black text-emerald-500 italic tracking-tighter">
                                    +{{ number_format($income->net_amount, 2, ',', ' ') }} €
                                </span>
                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-white italic">
                                {{ number_format($income->capital_after, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endif
@endif
    {{-- MODAL: IMPORTAÇÃO DO RELATÓRIO XTB --}}
    @include('livewire.partials.investment-report-modal')

{{-- 9. RODAPÉ --}}
<footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 px-4">
    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.5em]">
        © {{ date('Y') }} Finance Pro · Terminal de Ativos Inteligente
    </p>
</footer>


    @endif
</div> {{-- FIM DA DIV RAIZ --}}