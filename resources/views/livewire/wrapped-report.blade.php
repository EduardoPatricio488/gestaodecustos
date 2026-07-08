<div class="space-y-10 pb-32 overflow-x-hidden"
     x-data="{
        slide: 0,
        total: 9,
        next() { if(this.slide < this.total - 1) this.slide++ },
        prev() { if(this.slide > 0) this.slide-- }
     }"
     x-on:keydown.right.window="next()"
     x-on:keydown.left.window="prev()"
>
    {{-- ANIMAÇÕES --}}
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }
        @keyframes shine { from { left: -120%; } to { left: 120%; } }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .perspective-lg { perspective: 2000px; }
    </style>

    {{-- CONTROLES DE NAVEGAÇÃO --}}
    <div class="flex flex-col items-center gap-6 pt-6">
        <div class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-900 rounded-full border border-zinc-200 dark:border-zinc-800 shadow-inner">
            <button wire:click="setView('year')" class="px-6 py-2 rounded-full text-[10px] font-bold uppercase transition-all {{ $view === 'year' ? 'bg-white dark:bg-zinc-800 shadow text-brand-600' : 'text-zinc-500' }}">Ano</button>
            <button wire:click="setView('month')" class="px-6 py-2 rounded-full text-[10px] font-bold uppercase transition-all {{ $view === 'month' ? 'bg-white dark:bg-zinc-800 shadow text-brand-600' : 'text-zinc-500' }}">Mês</button>
        </div>

        <div class="flex items-center gap-4">
            @if($view === 'year')
                @foreach($availableYears as $y)
                    <button wire:click="setYear({{ $y }})" class="text-xs font-black {{ $year == $y ? 'text-brand-500 underline' : 'text-zinc-400' }}">{{ $y }}</button>
                @endforeach
            @else
                <select wire:model.live="month" class="bg-transparent border-none text-xs font-bold uppercase tracking-widest focus:ring-0">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    {{-- SLIDES --}}
    <div class="max-w-xl mx-auto px-4 perspective-lg">
        <div class="relative min-h-[600px] w-full flex items-center justify-center">

            @php
                $base = "absolute inset-0 p-10 rounded-[3.5rem] flex flex-col justify-center text-center shadow-2xl border border-white/10 transition-all duration-700 ease-out backdrop-blur-xl overflow-hidden";
            @endphp

            {{-- SLIDE 0: CAPA --}}
            <div x-show="slide === 0" class="{{ $base }} bg-gradient-to-br from-brand-600 to-indigo-900 text-white">
    <p class="text-[12px] font-black uppercase tracking-widest opacity-70 mb-4">O Teu Resumo Financeiro</p>

    {{-- Alterado aqui para mostrar o nome do mês dinamicamente --}}
    <h1 class="text-[70px] md:text-[100px] font-black italic leading-none animate-float uppercase">
        {{ $view === 'year' ? $year : Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
    </h1>

    <p class="text-white/60 font-bold uppercase tracking-[0.3em] mt-8">Finance Pro Wrapped</p>
</div>

            {{-- SLIDE 1: GASTOS & VOLUME --}}
            <div x-show="slide === 1" x-cloak class="{{ $base }} bg-zinc-950 text-white border-red-500/30 border-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-red-500 mb-6">Volume de Transações</p>
                <p class="text-[80px] font-black italic leading-none text-red-500">{{ number_format($spent, 0, ',', ' ') }}€</p>
                <div class="mt-10 grid grid-cols-2 gap-4 text-left">
                    <div class="bg-white/5 p-4 rounded-3xl">
                        <p class="text-[10px] uppercase opacity-50">Transações</p>
                        <p class="text-xl font-black">{{ $transactionCount }}</p>
                    </div>
                    <div class="bg-white/5 p-4 rounded-3xl">
                        <p class="text-[10px] uppercase opacity-50">Média/Mês</p>
                        <p class="text-xl font-black">{{ number_format($avgSpending, 0) }}€</p>
                    </div>
                </div>
            </div>

            {{-- SLIDE 2: POUPANÇA --}}
            <div x-show="slide === 2" x-cloak class="{{ $base }} bg-emerald-600 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-200 mb-2">Escudo Financeiro</p>
                <p class="text-[90px] font-black italic leading-none mb-8 animate-float">{{ number_format($saved, 0) }}€</p>
                <div class="relative h-4 bg-emerald-900/40 rounded-full overflow-hidden mb-4">
                    <div class="absolute h-full bg-emerald-300 transition-all duration-1000" style="width: {{ $savingsRate }}%"></div>
                </div>
                <p class="text-xs font-bold uppercase">Taxa de Poupança: {{ $savingsRate }}%</p>
            </div>

            {{-- SLIDE 3: MAIOR DESPESA --}}
            <div x-show="slide === 3" x-cloak class="{{ $base }} bg-orange-500 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-orange-100 mb-8">O Grande Impacto</p>
                @if($biggestExpense)
                    <div class="space-y-4">
                        <p class="text-4xl font-black italic leading-tight uppercase">{{ $biggestExpense->description }}</p>
                        <p class="text-7xl font-black tabular-nums">{{ number_format($biggestExpense->amount, 0) }}€</p>
                    </div>
                @else
                    <p class="italic">Sem registos significativos.</p>
                @endif
            </div>

            {{-- SLIDE 4: TOP CATEGORIAS --}}
            <div x-show="slide === 4" x-cloak class="{{ $base }} bg-indigo-800 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-10">Onde o dinheiro flui</p>
                <div class="space-y-6">
                    @foreach($topCategories as $index => $cat)
                        <div class="flex items-center justify-between bg-white/10 p-5 rounded-3xl">
                            <span class="font-black italic text-xl">#{{ $index + 1 }} {{ $cat->name }}</span>
                            <span class="font-bold opacity-70">{{ number_format($cat->expenses_sum_amount, 0) }}€</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SLIDE 5: METAS --}}
            <div x-show="slide === 5" x-cloak class="{{ $base }} bg-amber-500 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-amber-900 mb-8">Evolução de Metas</p>
                <p class="text-[140px] font-black italic leading-none animate-float">{{ $goalsCompleted }}</p>
                <p class="text-sm font-black uppercase tracking-widest mt-4">Objetivos Concluídos</p>
            </div>

            {{-- SLIDE 6: INVESTIMENTOS (NOVO) --}}
            <div x-show="slide === 6" x-cloak class="{{ $base }} bg-zinc-900 text-white border-emerald-500/20 border-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400 mb-6">Património Ativo</p>
                <div class="space-y-2 mb-10">
                    <p class="text-[10px] uppercase opacity-50">Valor da Carteira</p>
                    <p class="text-6xl font-black italic text-emerald-400">{{ number_format($portfolioValue, 0) }}€</p>
                </div>
                <div class="bg-white/5 p-6 rounded-[2.5rem] flex items-center justify-between">
                    <div class="text-left">
                        <p class="text-[10px] uppercase opacity-50">Ganhos Totais</p>
                        <p class="text-2xl font-black {{ $portfolioGain >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $portfolioGain >= 0 ? '+' : '' }}{{ number_format($portfolioGain, 0) }}€
                        </p>
                    </div>
                    <div class="size-12 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <flux:icon name="chart-bar" class="size-6 text-emerald-400" />
                    </div>
                </div>
            </div>

            {{-- SLIDE 7: PADRÃO MENSAL (NOVO) --}}
            <div x-show="slide === 7" x-cloak class="{{ $base }} bg-gradient-to-br from-purple-700 to-indigo-900 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-purple-200 mb-10">Ritmo de Gastos</p>
                <div class="space-y-8">
                    @if($worstMonth)
                        <div class="text-left bg-black/20 p-6 rounded-3xl border border-white/5">
                            <p class="text-[10px] uppercase text-red-300 font-bold mb-1">Mês de Maior Gasto</p>
                            <div class="flex justify-between items-end">
                                <p class="text-3xl font-black italic">{{ Carbon\Carbon::create()->month((int)$worstMonth->month)->translatedFormat('F') }}</p>
                                <p class="text-xl font-bold">{{ number_format($worstMonth->total, 0) }}€</p>
                            </div>
                        </div>
                    @endif
                    @if($bestMonth)
                        <div class="text-left bg-white/10 p-6 rounded-3xl border border-white/5">
                            <p class="text-[10px] uppercase text-emerald-300 font-bold mb-1">Mês de Maior Economia</p>
                            <div class="flex justify-between items-end">
                                <p class="text-3xl font-black italic">{{ Carbon\Carbon::create()->month((int)$bestMonth->month)->translatedFormat('F') }}</p>
                                <p class="text-xl font-bold">{{ number_format($bestMonth->total, 0) }}€</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SLIDE 8: SCORE FINAL --}}
            <div x-show="slide === 8" x-cloak class="{{ $base }} bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white border-brand-500/20 border-8">
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-600 mb-6">O Teu Veredito</p>
                <p class="text-[120px] font-black italic leading-none text-brand-600 mb-2 drop-shadow-xl">{{ $score }}</p>
                <p class="text-2xl font-black italic uppercase mb-10">{{ $scoreGrade }}</p>

                <div class="space-y-2 mb-10 max-w-xs mx-auto w-full">
                    @foreach($scoreFactors as $factor => $val)
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-500" style="width: {{ $val }}%"></div>
                            </div>
                            <span class="text-[8px] font-bold uppercase opacity-50">{{ $factor }}</span>
                        </div>
                    @endforeach
                </div>

                <button wire:click="shareToSocial" class="w-full py-5 bg-brand-600 text-white rounded-3xl font-black uppercase tracking-widest text-xs shadow-xl hover:scale-105 active:scale-95 transition-all">
                    Partilhar Resultados
                </button>
            </div>

        </div>

        {{-- NAVEGAÇÃO --}}
        <div class="flex items-center justify-between mt-10 px-4">
            <button @click="prev()" class="size-12 rounded-2xl bg-zinc-100 dark:bg-zinc-900 text-zinc-400 disabled:opacity-10" :disabled="slide === 0">
                <flux:icon name="chevron-left" class="size-5 mx-auto" />
            </button>
            <div class="flex gap-2">
                <template x-for="i in total" :key="i">
                    <div class="h-1.5 rounded-full transition-all" :class="slide === i-1 ? 'bg-brand-500 w-8' : 'bg-zinc-200 dark:bg-zinc-800 w-1.5'"></div>
                </template>
            </div>
            <button @click="next()" class="size-12 rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-500/20 disabled:opacity-10" :disabled="slide === total-1">
                <flux:icon name="chevron-right" class="size-5 mx-auto" />
            </button>
        </div>
    </div>
</div>
