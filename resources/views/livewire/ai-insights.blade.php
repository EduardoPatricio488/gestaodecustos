<div class="space-y-8">
    {{-- 1. CABEÇALHO COM ESTADO DE ANÁLISE --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <flux:icon name="sparkles" variant="solid" class="size-6 text-brand-500 {{ $isAnalyzing ? 'animate-spin' : 'animate-pulse' }}" />
                <span class="text-xs font-black uppercase tracking-[0.3em] text-brand-600">Inteligência Estratégica</span>
            </div>
            <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic">CFO Inteligente</h1>
            <p class="text-zinc-500 font-medium text-sm">O teu consultor financeiro processou os teus ativos e hábitos.</p>
        </div>

        <flux:button
            wire:click="generateInsights"
            variant="primary"
            class="shadow-2xl !h-14 px-8 rounded-2xl font-black uppercase tracking-widest text-xs"
            :loading="$isAnalyzing"
        >
            @if($isAnalyzing) A Processar Dados... @else Gerar Relatório IA @endif
        </flux:button>
    </div>

    {{-- 2. RELATÓRIO DO GEMINI (CARTÃO DE DESTAQUE) --}}
    @if($aiAnalysis)
        <div class="glass-card p-10 bg-zinc-950 text-white rounded-[3rem] border-2 border-brand-500 shadow-[0_0_50px_-12px_rgba(16,185,129,0.3)] animate-in slide-in-from-bottom-4 duration-700">
            <div class="flex items-center gap-4 mb-8 border-b border-white/10 pb-6">
                <div class="p-3 bg-brand-500/20 rounded-2xl">
                    <flux:icon name="chat-bubble-left-right" class="size-6 text-brand-400" />
                </div>
                <div>
                    <h2 class="text-lg font-black uppercase tracking-tighter">Diagnóstico Digital</h2>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Análise baseada no teu património real</p>
                </div>
            </div>

            <div class="prose prose-invert max-w-none
                prose-h3:text-brand-400 prose-h3:uppercase prose-h3:text-sm prose-h3:tracking-widest
                prose-p:text-zinc-300 prose-p:leading-relaxed
                prose-strong:text-white prose-strong:font-black
                prose-li:text-zinc-400">
                {!! Str::markdown($aiAnalysis) !!}
            </div>

            <div class="mt-8 pt-6 border-t border-white/5 flex justify-between items-center">
                <span class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.4em]">Gemini 1.5 Flash Engine</span>
                <flux:badge variant="success" size="sm" class="bg-brand-500/10 text-brand-400 border-none">+150 XP GANHOS</flux:badge>
            </div>
        </div>
    @endif

    {{-- 3. KPIs DE SAÚDE E PATRIMÓNIO --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- SCORE CIRCULAR SIMULADO --}}
        <div class="stat-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[2.5rem] flex flex-col justify-between shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Score de Saúde</p>
                <p class="text-6xl font-black mt-2 italic {{ $healthScore > 70 ? 'text-emerald-500' : 'text-orange-500' }}">
                    {{ $healthScore }}%
                </p>
            </div>
            <div class="relative z-10 mt-6">
                <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border dark:border-zinc-700">
                    <div class="h-full bg-brand-500 transition-all duration-1000" style="width: {{ $healthScore }}%"></div>
                </div>
                <p class="text-[9px] mt-2 font-black text-zinc-500 uppercase">Rácio Mensal Ganhos vs Gastos</p>
            </div>
            <flux:icon name="bolt" class="absolute -right-6 -bottom-6 size-32 text-zinc-50 dark:text-zinc-950 rotate-12" />
        </div>

        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="stat-card bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:scale-[1.02]">
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest text-zinc-400">Património Líquido</p>
                <p class="mt-2 text-3xl font-black dark:text-white">{{ number_format($netWorth, 2, ',', ' ') }} €</p>
                <p class="text-[9px] text-zinc-500 font-bold uppercase mt-4">Ativos + Investimentos</p>
            </div>

            <div class="stat-card bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:scale-[1.02]">
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Receitas do Mês</p>
                <p class="mt-2 text-3xl font-black text-emerald-600">{{ number_format($totalEarned, 2, ',', ' ') }} €</p>
                <p class="text-[9px] text-zinc-500 font-bold uppercase mt-4 italic">Salário + Extras</p>
            </div>

            <div class="stat-card bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:scale-[1.02]">
                <p class="text-xs font-bold text-red-500 uppercase tracking-widest">Gastos do Mês</p>
                <p class="mt-2 text-3xl font-black text-red-500">{{ number_format($totalSpent, 2, ',', ' ') }} €</p>
                <p class="text-[9px] text-zinc-500 font-bold uppercase mt-4 italic">Saídas Pessoais</p>
            </div>
        </div>
    </div>

    {{-- 4. ALERTAS ALGORÍTMICOS (BACKUP DA IA) --}}
    <div class="space-y-4">
        <h2 class="text-xs font-black uppercase tracking-[0.3em] text-zinc-400">Verificações de Segurança</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($insights as $insight)
                <div class="glass-card p-6 border-l-4 shadow-sm flex gap-5 transition-all hover:translate-x-1
                    {{ $insight['type'] === 'danger' ? 'border-red-500 bg-red-50/10' : 'border-blue-500 bg-blue-50/10' }}">
                    <div class="mt-1">
                        @if($insight['type'] === 'danger')
                            <flux:icon name="x-circle" class="size-6 text-red-500" />
                        @else
                            <flux:icon name="information-circle" class="size-6 text-blue-500" />
                        @endif
                    </div>
                    <div>
                        <h4 class="font-black text-sm uppercase dark:text-white leading-none tracking-tight">{{ $insight['title'] }}</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed font-medium">{{ $insight['text'] }}</p>
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
