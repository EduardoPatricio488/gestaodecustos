<div class="space-y-10 pb-20">
    {{-- 1. HEADER ESTRATÉGICO --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
        <div class="flex items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="briefcase" class="w-10 h-10 text-brand-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Património Líquido</h1>
                    <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Wealth Report</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Análise estrutural de ativos, passivos e solvabilidade real.</p>
            </div>
        </div>
        <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate class="rounded-2xl font-bold">Voltar</flux:button>
    </div>

    {{-- 2. PAINEL EXECUTIVO (ESTILO BLACK GLASS PREMIUM) --}}
    <div class="stat-card bg-zinc-950 text-white p-10 rounded-[3rem] shadow-2xl relative overflow-hidden border border-zinc-800 group">
        {{-- Efeito decorativo de profundidade --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-500/10 blur-[120px] rounded-full -mr-32 -mt-32"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-5 gap-12 items-center">

            {{-- Valor Principal --}}
            <div class="lg:col-span-3 space-y-8">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-brand-400 mb-2">Valor Líquido Total</p>
                    <h2 class="text-6xl sm:text-8xl font-black tracking-tighter italic leading-none">
                        {{ number_format($netWorth, 2, ',', ' ') }} <span class="text-3xl sm:text-4xl">€</span>
                    </h2>
                </div>

                <div class="flex flex-wrap gap-4">
                    <div class="px-6 py-4 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-sm group/item hover:bg-white/10 transition-colors">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Ativos Brutos</p>
                        <p class="text-2xl font-black text-emerald-400 tracking-tighter">{{ number_format($totalAssets, 0, ',', ' ') }} €</p>
                    </div>
                    <div class="px-6 py-4 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-sm group/item hover:bg-white/10 transition-colors">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Dívida Total</p>
                        <p class="text-2xl font-black text-red-500 tracking-tighter">{{ number_format($liabilities, 0, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>

            {{-- Raio-X Visual --}}
            <div class="lg:col-span-2 bg-white/5 p-8 rounded-[2.5rem] border border-white/10 backdrop-blur-xl shadow-inner">
                <div class="flex items-center gap-2 mb-8">
                    <flux:icon name="magnifying-glass-circle" class="size-4 text-brand-400" />
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Distribuição de Capital</h3>
                </div>

                <div class="space-y-6">
                    {{-- Liquidez --}}
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-zinc-300">
                            <span>Liquidez (Cash)</span>
                            <span class="text-emerald-400">{{ round($liquidityRatio) }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)] transition-all duration-1000" style="width: {{ $liquidityRatio }}%"></div>
                        </div>
                    </div>

                    {{-- Investimentos --}}
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-zinc-300">
                            <span>Exposição a Ativos</span>
                            <span class="text-indigo-400">{{ round($investmentExposure) }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-indigo-500 shadow-[0_0_12px_rgba(99,102,241,0.5)] transition-all duration-1000" style="width: {{ $investmentExposure }}%"></div>
                        </div>
                    </div>

                    {{-- Metas --}}
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-zinc-300">
                            <span>Saúde de Poupança</span>
                            <span class="text-brand-400">{{ round($savingsHealth) }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-brand-500 shadow-[0_0_12px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: {{ $savingsHealth }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
