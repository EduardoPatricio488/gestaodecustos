<div class="space-y-8">
    {{-- 1. CABEÇALHO --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2 flex-wrap">
                <flux:icon name="sparkles" variant="solid" class="size-6 text-brand-500 {{ $isAnalyzing ? 'animate-spin' : 'animate-pulse' }}" />
                <span class="text-xs font-black uppercase tracking-[0.3em] text-brand-600">Inteligência Estratégica</span>
                @isset($reportGeneratedAt)
    @if($reportGeneratedAt)
        <span class="text-[9px] font-black uppercase text-zinc-400 px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-full tracking-widest">
            Atualizado {{ $reportGeneratedAt->diffForHumans() }}
        </span>
    @endif
@endisset
            </div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-black dark:text-white uppercase tracking-tighter italic">CFO Inteligente</h1>
            <p class="text-zinc-500 font-medium text-sm max-w-md">O teu consultor financeiro processou os teus ativos e hábitos.</p>

            <div x-data x-on:cfo-report-finished.window="$wire.refreshAiQuota()"
                 wire:init="refreshAiQuota" wire:poll.60s="refreshAiQuota" class="mt-3 text-xs text-zinc-500">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <span class="font-semibold">Quota IA · OpenRouter</span>
                    @if($aiQuota === null)
                        <span role="status">A consultar...</span>
                    @elseif($aiQuota['available'])
                        <span>{{ number_format($aiQuota['used'], 0, ',', '.') }} utilizados</span>
                        <span class="{{ $aiQuota['remaining'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ number_format($aiQuota['remaining'], 0, ',', '.') }} restantes / {{ number_format($aiQuota['limit'], 0, ',', '.') }}
                        </span>
                    @else
                        <span class="text-amber-700 dark:text-amber-400" role="status">Quota indisponível. {{ $aiQuota['error'] }}</span>
                    @endif
                    <button type="button" wire:click="refreshAiQuota" wire:loading.attr="disabled" wire:target="refreshAiQuota"
                            class="font-semibold text-brand-600 hover:text-brand-700 disabled:opacity-50">
                        Atualizar quota
                    </button>
                </div>
                @if($aiQuota !== null && $aiQuota['available'])
                    <p class="mt-1 text-[11px] text-zinc-400">Pedidos partilhados com outras funções · Renova às 00:00 UTC · Um relatório pode consumir mais de um pedido. Outros limites podem aplicar-se.</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap flex-shrink-0">
            @if($aiAnalysis && !$isAnalyzing)
                <flux:button
                    x-data="{ copied: false, copyError: false }"
                    x-on:click="copyError = false; copied = false; try { await navigator.clipboard.writeText($wire.aiAnalysis); copied = true; setTimeout(() => copied = false, 2000) } catch (error) { copyError = true }"
                    variant="ghost"
                    class="rounded-2xl font-black uppercase tracking-widest text-[10px] text-zinc-500"
                >
                    <span x-show="!copied && !copyError" class="flex items-center gap-2">
                        <flux:icon name="clipboard-document" class="size-4" /> Copiar
                    </span>
                    <span x-show="copied" x-cloak class="flex items-center gap-2 text-emerald-500">
                        <flux:icon name="check" class="size-4" /> Copiado
                    </span>
                    <span x-show="copyError" x-cloak class="flex items-center gap-2 text-red-500" role="alert">
                        Não foi possível copiar
                    </span>
                </flux:button>
            @endif

            <flux:button
                wire:click="generateInsights"
                wire:loading.attr="disabled"
                wire:target="generateInsights"
                variant="primary"
                class="shadow-2xl !h-14 px-8 rounded-2xl font-black uppercase tracking-widest text-xs"
            >
                <span wire:loading.remove wire:target="generateInsights" class="flex items-center gap-2">
                    <flux:icon name="{{ $aiAnalysis ? 'arrow-path' : 'sparkles' }}" class="size-4" />
                    {{ $aiAnalysis ? 'Atualizar Relatório' : 'Gerar Relatório IA' }}
                </span>
                <span wire:loading wire:target="generateInsights" class="flex items-center gap-2">
                    <flux:icon name="arrow-path" class="size-4 animate-spin" />
                    A processar...
                </span>
            </flux:button>
        </div>
    </div>

    {{-- 2. RESUMO RÁPIDO --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Saúde Financeira (anel circular, igual ao dashboard) --}}
        <div class="stat-card relative overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm flex items-center gap-5">
            <div class="relative size-20 flex-shrink-0 flex items-center justify-center">
                <svg class="absolute inset-0 size-full -rotate-90">
                    <circle cx="40" cy="40" r="34" stroke="currentColor" stroke-width="6" fill="transparent" class="text-zinc-100 dark:text-zinc-800" />
                    <circle cx="40" cy="40" r="34" stroke="currentColor" stroke-width="6" fill="transparent"
                        class="{{ $healthScore > 70 ? 'text-emerald-500' : ($healthScore > 40 ? 'text-amber-500' : 'text-red-500') }} transition-all duration-1000"
                        stroke-dasharray="213.6"
                        stroke-dashoffset="{{ 213.6 - (213.6 * min($healthScore, 100)) / 100 }}"
                        stroke-linecap="round" />
                </svg>
                <span class="text-lg font-black dark:text-white">{{ $healthScore }}%</span>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Saúde Financeira</p>
                <p class="text-xs font-bold text-zinc-500 mt-1">Ganhos vs Gastos</p>
                @isset($healthScoreDelta)
                    <span class="inline-flex items-center gap-1 mt-2 text-[9px] font-black {{ $healthScoreDelta >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $healthScoreDelta >= 0 ? '▲' : '▼' }} {{ abs($healthScoreDelta) }}% vs mês anterior
                    </span>
                @endisset
            </div>
        </div>

        {{-- Património Líquido --}}
        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Património Líquido</p>
            <p class="mt-2 text-2xl font-black dark:text-white tracking-tighter">{{ number_format($netWorth, 2, ',', ' ') }} €</p>
            <div class="flex items-center justify-between mt-2">
                <span class="text-[9px] text-zinc-400 font-bold uppercase">Ativos + Investimentos</span>
                @isset($netWorthDelta)
                    <span class="text-[9px] font-black {{ $netWorthDelta >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $netWorthDelta >= 0 ? '▲' : '▼' }}{{ abs($netWorthDelta) }}%
                    </span>
                @endisset
            </div>
        </div>

        {{-- Receitas do Mês --}}
        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Receitas do Mês</p>
            <p class="mt-2 text-2xl font-black text-emerald-600 tracking-tighter">{{ number_format($totalEarned, 2, ',', ' ') }} €</p>
            <div class="flex items-center justify-between mt-2">
                <span class="text-[9px] text-zinc-400 font-bold uppercase italic">Salário + Extras</span>
                @isset($earnedDelta)
                    <span class="text-[9px] font-black {{ $earnedDelta >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $earnedDelta >= 0 ? '▲' : '▼' }}{{ abs($earnedDelta) }}%
                    </span>
                @endisset
            </div>
        </div>

        {{-- Gastos do Mês --}}
        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
            <p class="text-[10px] font-black uppercase tracking-widest text-red-500">Gastos do Mês</p>
            <p class="mt-2 text-2xl font-black text-red-500 tracking-tighter">{{ number_format($totalSpent, 2, ',', ' ') }} €</p>
            <div class="flex items-center justify-between mt-2">
                <span class="text-[9px] text-zinc-400 font-bold uppercase italic">Saídas Pessoais</span>
                @isset($spentDelta)
                    <span class="text-[9px] font-black {{ $spentDelta <= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $spentDelta >= 0 ? '▲' : '▼' }}{{ abs($spentDelta) }}%
                    </span>
                @endisset
            </div>
        </div>
    </div>

    {{-- 3. ESTADO: A GERAR --}}
    <div wire:loading wire:target="generateInsights"
         class="glass-card p-10 bg-zinc-950 text-white rounded-[3rem] border border-zinc-800 shadow-2xl"
         role="status" aria-live="polite"
    >
        <div class="flex flex-col items-center justify-center gap-8 py-6 text-center">
            <div class="relative">
                <div class="size-24 rounded-full border-4 border-brand-500/20 flex items-center justify-center">
                    <div class="size-20 rounded-full border-4 border-t-brand-500 border-r-brand-500/50 border-b-transparent border-l-transparent animate-spin"></div>
                    <flux:icon name="sparkles" class="absolute size-8 text-brand-400 animate-pulse" />
                </div>
                <div class="absolute -top-2 -right-2 size-6 bg-emerald-500 rounded-full animate-ping"></div>
            </div>

            <div class="space-y-3 max-w-md">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Inteligência Artificial em Ação</p>
                <p class="text-lg font-black text-white italic">A preparar o teu diagnóstico financeiro</p>
            </div>

            <div class="w-full max-w-sm space-y-2">
                <div class="h-1.5 w-full bg-zinc-800 rounded-full overflow-hidden">
                    <div class="cfo-loading-progress h-full bg-gradient-to-r from-brand-500 to-emerald-500 rounded-full"></div>
                </div>
                <p class="text-xs text-zinc-400">Barra indicativa: o fornecedor não envia progresso real e pode ser necessária uma nova tentativa. Mantém esta página aberta.</p>
            </div>
        </div>
    </div>

    @if($analysisError)
        <p role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300">{{ $analysisError }}</p>
    @endif
    @if($aiAnalysis && $reportNeedsReview)
        <p role="alert" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-800 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-300">Este relatório foi gerado antes da verificação de valores e pode conter erros. Atualiza-o antes de o utilizar.</p>
    @endif

    {{-- 4. ESTADO VAZIO (ainda sem relatório) --}}
    @if(!$aiAnalysis)
        <div id="diagnostico-financeiro" wire:loading.remove wire:target="generateInsights"
             class="relative overflow-hidden bg-gradient-to-br from-zinc-950 via-zinc-900 to-brand-900 text-white p-10 rounded-[3rem] border border-brand-500/20 shadow-2xl">
            <div class="absolute -top-10 -right-10 size-56 bg-brand-500/10 blur-[100px] rounded-full"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                <div class="size-20 rounded-3xl bg-brand-500/20 border border-brand-400/30 flex items-center justify-center flex-shrink-0">
                    <flux:icon name="sparkles" variant="solid" class="size-10 text-brand-400" />
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-black uppercase italic tracking-tighter">Ainda não geraste o teu diagnóstico</h2>
                    <p class="text-zinc-400 text-sm font-medium mt-2 max-w-lg">
                        A IA analisa os teus registos e apresenta sugestões e cenários quando existem dados suficientes.
                    </p>
                    <div class="flex flex-wrap gap-4 mt-5">
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                            <flux:icon name="check-circle" class="size-4 text-emerald-500" /> Analisa despesas registadas
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                            <flux:icon name="check-circle" class="size-4 text-emerald-500" /> Apresenta cenários possíveis
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                            <flux:icon name="check-circle" class="size-4 text-emerald-500" /> Sugere próximos passos
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 5. RELATÓRIO DA IA --}}
    @if($aiAnalysis)
        <div id="diagnostico-financeiro" wire:loading.class="opacity-40 pointer-events-none" wire:target="generateInsights"
             class="glass-card p-10 bg-zinc-950 text-white rounded-[3rem] border-2 border-brand-500 shadow-[0_0_50px_-12px_rgba(16,185,129,0.3)] transition-opacity duration-300">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8 border-b border-white/10 pb-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-brand-500/20 rounded-2xl">
                        <flux:icon name="chat-bubble-left-right" class="size-6 text-brand-400" />
                    </div>
                    <div>
                        <h2 class="text-lg font-black uppercase tracking-tighter">Diagnóstico financeiro</h2>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Análise financeira por tópicos</p>
                    </div>
                </div>
                @isset($reportGeneratedAt)
    @if($reportGeneratedAt)
        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest px-3 py-1.5 bg-white/5 rounded-full border border-white/10">
            Gerado {{ $reportGeneratedAt->diffForHumans() }}
        </span>
    @endif
@endisset
            </div>

            @php
                $reportMarkdown = preg_replace(
                    '/^[ \t]*\*\*(Resultados|O que penso|Cenários|Pontos fortes|Pontos negativos|O que aconselho|Resumo):?\*\*[ \t]*$/imu',
                    '### $1',
                    $aiAnalysis
                );
            @endphp
            <div class="cfo-report">
                {!! Str::markdown($reportMarkdown, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
            </div>

            <div class="mt-8 pt-6 border-t border-white/5 flex justify-between items-center">
                <span class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.4em]">OpenRouter</span>
            </div>
        </div>
    @endif

</div>

<style>
    .cfo-report { color: #d4d4d8; line-height: 1.75; overflow-wrap: anywhere; }
    .cfo-report h3 {
        margin: 2rem 0 1rem;
        padding: 0.8rem 1rem;
        border: 1px solid rgba(52, 211, 153, 0.2);
        border-left: 4px solid #34d399;
        border-radius: 0.75rem;
        background: rgba(16, 185, 129, 0.08);
        color: #6ee7b7;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.4;
    }
    .cfo-report h3:first-child { margin-top: 0; }
    .cfo-report p { margin: 0.75rem 0; }
    .cfo-report ul, .cfo-report ol { margin: 0.75rem 0; padding-left: 1.5rem; }
    .cfo-report ul { list-style-type: disc; }
    .cfo-report ol { list-style-type: decimal; }
    .cfo-report li { margin: 0.6rem 0; padding-left: 0.2rem; }
    .cfo-report li::marker { color: #34d399; }
    .cfo-report strong { color: #fff; font-weight: 700; }

    .cfo-loading-progress {
        width: 3%;
        animation: cfo-report-progress 75s ease-out forwards, cfo-report-pulse 2s ease-in-out infinite;
    }
    @keyframes cfo-report-progress {
        from { width: 3%; }
        to { width: 95%; }
    }
    @keyframes cfo-report-pulse {
        50% { opacity: 0.6; }
    }
    @media (prefers-reduced-motion: reduce) {
        .cfo-loading-progress { width: 50%; animation: none; }
    }
</style>
