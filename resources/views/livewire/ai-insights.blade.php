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
            <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic">CFO Inteligente</h1>
            <p class="text-zinc-500 font-medium text-sm max-w-md">O teu consultor financeiro processou os teus ativos e hábitos.</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap flex-shrink-0">
            @if($aiAnalysis && !$isAnalyzing)
                <flux:button
                    x-data="{ copied: false, text: @js($aiAnalysis) }"
                    x-on:click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                    variant="ghost"
                    class="rounded-2xl font-black uppercase tracking-widest text-[10px] text-zinc-500"
                >
                    <span x-show="!copied" class="flex items-center gap-2">
                        <flux:icon name="clipboard-document" class="size-4" /> Copiar
                    </span>
                    <span x-show="copied" x-cloak class="flex items-center gap-2 text-emerald-500">
                        <flux:icon name="check" class="size-4" /> Copiado
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

    {{-- 3. ESTADO: A GERAR (ecrã dramático mantido) --}}
    <div wire:loading wire:target="generateInsights"
         class="glass-card p-10 bg-zinc-950 text-white rounded-[3rem] border border-zinc-800 shadow-2xl"
         x-data="{
            messages: [
                '🔍 A espiar as tuas despesas com o Uber Eats...',
                '💸 A contar quantas vezes foste ao café este mês...',
                '📊 A perguntar ao Banco de Portugal se estás bem...',
                '🧮 A fazer contas que a tua professora de matemática não aprovaria...',
                '🤖 A treinar a IA com os teus erros financeiros...',
                '😬 A descobrir quanto gastaste em subscrições que te esqueceste...',
                '📈 A inventar um gráfico que te faça sentir bem...',
                '🏦 A negociar com os algoritmos em teu nome...',
                '💡 Quase lá... a IA está a tomar um café antes de te julgar...',
                '✍️ A escrever o relatório com luvas para não deixar impressões digitais...',
            ],
            current: 0,
            init() {
                setInterval(() => {
                    this.current = (this.current + 1) % this.messages.length;
                }, 2500);
            }
         }"
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
                <p class="text-lg font-black text-white italic transition-all duration-500" x-text="messages[current]"></p>
            </div>

            <div class="w-full max-w-sm space-y-2">
                <div class="h-1.5 w-full bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-brand-500 to-emerald-500 rounded-full" style="animation: progress 8s ease-in-out forwards;"></div>
                </div>
                <p class="text-[9px] font-black text-zinc-600 uppercase tracking-widest">A processar o teu perfil financeiro...</p>
            </div>
        </div>
    </div>

    {{-- 4. ESTADO VAZIO (ainda sem relatório) --}}
    @if(!$aiAnalysis)
        <div wire:loading.remove wire:target="generateInsights"
             class="relative overflow-hidden bg-gradient-to-br from-zinc-950 via-zinc-900 to-brand-900 text-white p-10 rounded-[3rem] border border-brand-500/20 shadow-2xl">
            <div class="absolute -top-10 -right-10 size-56 bg-brand-500/10 blur-[100px] rounded-full"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                <div class="size-20 rounded-3xl bg-brand-500/20 border border-brand-400/30 flex items-center justify-center flex-shrink-0">
                    <flux:icon name="sparkles" variant="solid" class="size-10 text-brand-400" />
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-black uppercase italic tracking-tighter">Ainda não geraste o teu diagnóstico</h2>
                    <p class="text-zinc-400 text-sm font-medium mt-2 max-w-lg">
                        A IA analisa o teu padrão de gastos, receitas e poupança para te dar um diagnóstico escrito, em segundos.
                    </p>
                    <div class="flex flex-wrap gap-4 mt-5">
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                            <flux:icon name="check-circle" class="size-4 text-emerald-500" /> Deteta riscos de saldo negativo
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                            <flux:icon name="check-circle" class="size-4 text-emerald-500" /> Sinaliza subscrições esquecidas
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                            <flux:icon name="check-circle" class="size-4 text-emerald-500" /> Compara com o teu histórico
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 5. RELATÓRIO DA IA --}}
    @if($aiAnalysis)
        <div wire:loading.class="opacity-40 pointer-events-none" wire:target="generateInsights"
             class="glass-card p-10 bg-zinc-950 text-white rounded-[3rem] border-2 border-brand-500 shadow-[0_0_50px_-12px_rgba(16,185,129,0.3)] transition-opacity duration-300">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8 border-b border-white/10 pb-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-brand-500/20 rounded-2xl">
                        <flux:icon name="chat-bubble-left-right" class="size-6 text-brand-400" />
                    </div>
                    <div>
                        <h2 class="text-lg font-black uppercase tracking-tighter">Diagnóstico Digital</h2>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Análise baseada no teu património real</p>
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

            <div class="prose prose-invert max-w-none
                prose-h3:text-brand-400 prose-h3:uppercase prose-h3:text-sm prose-h3:tracking-widest
                prose-p:text-zinc-300 prose-p:leading-relaxed
                prose-strong:text-white prose-strong:font-black
                prose-li:text-zinc-400">
                {!! Str::markdown($aiAnalysis) !!}
            </div>

            <div class="mt-8 pt-6 border-t border-white/5 flex justify-between items-center">
                <span class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.4em]">Gemini 2.5 Flash Engine</span>
                <flux:badge variant="success" size="sm" class="bg-brand-500/10 text-brand-400 border-none">+150 XP GANHOS</flux:badge>
            </div>
        </div>
    @endif

    {{-- 6. VERIFICAÇÕES ALGORÍTMICAS --}}
    <div class="space-y-4" x-data="{ filter: 'all' }">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-zinc-400">Verificações de Segurança</h2>
                @if(count($insights) > 0)
                    <span class="text-[9px] font-black bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-2 py-0.5 rounded-full">{{ count($insights) }}</span>
                @endif
            </div>

            @if(count($insights) > 1)
                <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200 dark:border-zinc-800">
                    <button x-on:click="filter = 'all'" :class="filter === 'all' ? 'bg-white dark:bg-zinc-800 shadow-sm text-zinc-800 dark:text-white' : 'text-zinc-400'" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Todos</button>
                    <button x-on:click="filter = 'danger'" :class="filter === 'danger' ? 'bg-white dark:bg-zinc-800 shadow-sm text-red-500' : 'text-zinc-400'" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Riscos</button>
                    <button x-on:click="filter = 'info'" :class="filter === 'info' ? 'bg-white dark:bg-zinc-800 shadow-sm text-blue-500' : 'text-zinc-400'" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Avisos</button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($insights as $insight)
               <div x-show="filter === 'all' || filter === '{{ $insight['type'] }}'"
     class="glass-card p-6 border-l-4 shadow-sm flex gap-5 transition-all hover:translate-x-1
    {{ $insight['type'] === 'danger' ? 'border-red-500 bg-red-50/10' : ($insight['type'] === 'warning' ? 'border-amber-500 bg-amber-50/10' : 'border-blue-500 bg-blue-50/10') }}">
    <div class="mt-1">
                        @if($insight['type'] === 'danger')
    <flux:icon name="x-circle" class="size-6 text-red-500" />
@elseif($insight['type'] === 'warning')
    <flux:icon name="bell" class="size-6 text-amber-500" />
@else
    <flux:icon name="information-circle" class="size-6 text-blue-500" />
@endif
                    </div>
                    <div class="flex-1">
                        <h4 class="font-black text-sm uppercase dark:text-white leading-none tracking-tight">{{ $insight['title'] }}</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed font-medium">{{ $insight['text'] }}</p>
                        @isset($insight['route'])
                            <a href="{{ $insight['route'] }}" wire:navigate class="inline-flex items-center gap-1 mt-3 text-[10px] font-black uppercase tracking-widest text-brand-600 hover:underline">
                                Resolver agora <flux:icon name="arrow-right" class="size-3" />
                            </a>
                        @endisset
                    </div>
                </div>
            @empty
                <div class="col-span-2 p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[3rem]">
                    <flux:icon name="check-badge" class="size-10 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                    <p class="text-zinc-500 font-bold uppercase tracking-widest text-[10px]">Nenhum risco crítico detetado algoritmicamente.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    @keyframes progress {
        0% { width: 0%; }
        20% { width: 25%; }
        50% { width: 55%; }
        75% { width: 75%; }
        95% { width: 90%; }
        100% { width: 95%; }
    }
</style>
