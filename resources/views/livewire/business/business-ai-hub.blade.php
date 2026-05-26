<div class="space-y-10 pb-24">
    {{-- 1. HEADER NEURAL (ESTILO PREMIUM SaaS) --}}
    <div class="relative">
        {{-- Glow decorativo IA --}}
        <div class="absolute -top-10 left-0 size-72 bg-brand-500/10 blur-[120px] rounded-full pointer-events-none animate-pulse"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/30 blur-2xl rounded-full group-hover:scale-125 transition-all duration-1000"></div>
                    <div class="relative p-5 bg-zinc-900 border border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="sparkles" class="w-10 h-10 text-brand-400" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">CFO Strategist</h1>
                        <flux:badge variant="neutral" class="bg-brand-500/10 text-brand-400 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Neural Core v2</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Auditoria estratégica de dados cruzados e <span class="text-brand-600 font-bold uppercase">Previsão de Risco</span></p>
                </div>
            </div>

            <flux:button wire:click="runAnalysis" variant="primary" icon="cpu-chip" wire:loading.attr="disabled" class="rounded-2xl px-8 h-14 font-black uppercase tracking-widest shadow-xl shadow-brand-500/20">
                <span wire:loading.remove wire:target="runAnalysis">Correr Auditoria</span>
                <span wire:loading wire:target="runAnalysis" class="flex items-center gap-2">
                    <div class="size-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    A cruzar dados...
                </span>
            </flux:button>
        </header>
    </div>

    {{-- ESTADO DE LOADING (UI SaaS) --}}
    <div wire:loading wire:target="runAnalysis" class="w-full">
        <div class="p-32 text-center space-y-6 bg-zinc-950/50 rounded-[3rem] border-2 border-dashed border-zinc-800 backdrop-blur-xl">
            <div class="relative inline-block">
                <div class="absolute inset-0 bg-brand-500/20 blur-3xl animate-pulse rounded-full"></div>
                <flux:icon name="arrow-path" class="size-20 text-brand-500 animate-spin mx-auto relative z-10" />
            </div>
            <div class="space-y-2">
                <p class="text-xl font-black text-white uppercase tracking-tighter italic">O Estrategista está a pensar...</p>
                <p class="text-zinc-500 font-bold uppercase text-[10px] tracking-[0.3em] animate-pulse">A analisar faturas, níveis de stock e performance da equipa</p>
            </div>
        </div>
    </div>

    {{-- CONTEÚDO PRINCIPAL --}}
    <div wire:loading.remove wire:target="runAnalysis" class="space-y-10 animate-fade-in">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- SCORE DE RESILIÊNCIA (ESTILO BLACK VAULT) --}}
            <div class="stat-card bg-zinc-950 text-white p-10 rounded-[3rem] shadow-2xl relative overflow-hidden flex flex-col justify-between h-80 border border-zinc-800 group">
                {{-- Brilho dinâmico baseado no score --}}
                <div class="absolute -right-20 -top-20 size-64 {{ $healthScore > 70 ? 'bg-emerald-500/10' : ($healthScore > 40 ? 'bg-orange-500/10' : 'bg-red-500/10') }} blur-[100px] rounded-full transition-colors duration-1000"></div>

                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-500 mb-2">Resiliência do Negócio</p>
                    <h2 class="text-8xl font-black tracking-tighter italic leading-none {{ $healthScore > 70 ? 'text-emerald-500' : ($healthScore > 40 ? 'text-orange-500' : 'text-red-500') }}">
                        {{ $healthScore }}%
                    </h2>
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div class="px-4 py-1.5 bg-white/5 rounded-full border border-white/10">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Runway: {{ $runway }}</span>
                    </div>
                    <flux:icon name="fire" class="size-12 text-white/5 group-hover:scale-110 transition-transform duration-700" />
                </div>
            </div>

            {{-- MONITOR DE COBERTURA & DEPENDÊNCIA (AUDIT STYLE) --}}
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Cobertura Salarial --}}
                <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:border-emerald-500/30 transition-all">
                    <div>
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Segurança de Payroll</h3>
                            <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600">
                                <flux:icon name="user-group" variant="outline" class="size-4" />
                            </div>
                        </div>
                        <h4 class="text-5xl font-black dark:text-white tracking-tighter italic">
                            {{ $payrollCoverage }} <span class="text-lg text-zinc-400 not-italic uppercase tracking-widest">Meses</span>
                        </h4>
                    </div>

                    <div class="space-y-3 mt-8">
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black uppercase text-zinc-500 tracking-widest italic">Capacidade de Reserva</span>
                            <span class="text-[10px] font-black text-emerald-500">{{ min(round(($payrollCoverage / 12) * 100), 100) }}%</span>
                        </div>
                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800 shadow-inner">
                            <div class="h-full bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)] transition-all duration-1000"
                                 style="width: {{ min(($payrollCoverage / 12) * 100, 100) }}%"></div>
                        </div>
                        <p class="text-[9px] text-zinc-400 font-bold uppercase leading-tight italic">
                            Liquidez imediata para garantir {{ number_format($totalPayroll, 0, ',', ' ') }}€ de ordenados.
                        </p>
                    </div>
                </div>

                {{-- Risco de Concentração --}}
                <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:border-red-500/30 transition-all">
                    <div>
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Concentração de Risco</h3>
                            <div class="p-2 {{ $riskConcentration > 40 ? 'bg-red-500/10 text-red-600' : 'bg-emerald-500/10 text-emerald-600' }} rounded-lg">
                                <flux:icon name="exclamation-triangle" variant="outline" class="size-4" />
                            </div>
                        </div>
                        <h4 class="text-5xl font-black tracking-tighter italic {{ $riskConcentration > 40 ? 'text-red-500' : 'text-emerald-500' }}">
                            {{ $riskConcentration }}%
                        </h4>
                    </div>

                    <div class="space-y-3 mt-8">
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black uppercase text-zinc-500 tracking-widest italic">Dependência de Facturação</span>
                            <span class="text-[10px] font-black {{ $riskConcentration > 40 ? 'text-red-500' : 'text-zinc-400' }}">{{ $riskConcentration }}%</span>
                        </div>
                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800 shadow-inner">
                            <div class="h-full {{ $riskConcentration > 40 ? 'bg-red-500 shadow-[0_0_12px_rgba(239,68,68,0.5)]' : 'bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)]' }} transition-all duration-1000"
                                 style="width: {{ $riskConcentration }}%"></div>
                        </div>
                        <p class="text-[9px] text-zinc-400 font-bold uppercase leading-tight italic truncate">
                            Maior Cliente: <span class="text-zinc-900 dark:text-zinc-200">{{ $topClientName }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        {{-- 3. ANÁLISE DE PERFORMANCE E PLANO DE ACÇÃO IA --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- PLANO DE ACÇÃO ESTRATÉGICO (INSIGHTS) --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3 px-2">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="list-bullet" variant="outline" class="size-4" />
                    </div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400">Plano de Acção Neuronal</h3>
                </div>

                <div class="space-y-4">
                    @forelse($insights as $insight)
                        @php
                            $styles = match($insight['type']) {
                                'danger'  => ['border' => 'border-red-500/30', 'bg' => 'bg-red-500/5', 'text' => 'text-red-600', 'icon' => 'x-circle'],
                                'warning' => ['border' => 'border-amber-500/30', 'bg' => 'bg-amber-500/5', 'text' => 'text-amber-600', 'icon' => 'exclamation-triangle'],
                                default   => ['border' => 'border-emerald-500/30', 'bg' => 'bg-emerald-500/5', 'text' => 'text-emerald-600', 'icon' => 'check-circle']
                            };
                        @endphp

                        <div class="glass-card p-6 border-l-4 {{ $styles['border'] }} {{ $styles['bg'] }} rounded-[2rem] shadow-sm flex gap-5 transition-all hover:translate-x-1 group">
                            <div class="mt-1">
                                <flux:icon name="{{ $styles['icon'] }}" class="{{ $styles['text'] }} size-6 group-hover:scale-110 transition-transform" />
                            </div>
                            <div>
                                <h4 class="font-black text-sm uppercase dark:text-white leading-none tracking-tight">{{ $insight['title'] }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed font-medium italic">
                                    {{ $insight['text'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                            <p class="text-zinc-400 font-black uppercase text-[10px] tracking-[0.3em]">Neural Core: Sem ameaças detetadas</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- RANKING DE PROJETOS (EFICIÊNCIA HORÁRIA NEON) --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm relative overflow-hidden group">
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-brand-500/10 rounded-xl text-brand-600">
                            <flux:icon name="chart-bar" variant="outline" class="size-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400">Rácio de Lucratividade / Hora</h3>
                    </div>
                </div>

                <div class="space-y-8">
                    @forelse($projects as $p)
                        @php
                            $isProfitable = $p['hourly_profit'] >= $targetHourlyRate;
                            $barWidth = $p['hourly_profit'] > 0 ? min(($p['hourly_profit'] / ($targetHourlyRate * 1.5)) * 100, 100) : 0;
                        @endphp
                        <div class="group/item">
                            <div class="flex justify-between items-end mb-3">
                                <div>
                                    <span class="text-sm font-black dark:text-white uppercase tracking-tighter group-hover/item:text-brand-500 transition-colors">{{ $p['name'] }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="size-1 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>
                                        <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest">{{ $p['hours'] }}h Investidas</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-base font-black italic {{ $isProfitable ? 'text-emerald-500' : 'text-red-500' }}">
                                        {{ number_format($p['hourly_profit'], 2, ',', ' ') }} €<small class="text-[10px] ml-1 uppercase not-italic">/h</small>
                                    </span>
                                </div>
                            </div>
                            {{-- Barra Neon --}}
                            <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800">
                                <div class="h-full transition-all duration-1000 ease-out {{ $isProfitable ? 'bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)]' : 'bg-red-500 shadow-[0_0_12px_rgba(239,68,68,0.5)]' }}"
                                     style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 opacity-30">
                            <p class="text-zinc-400 text-[10px] font-black uppercase tracking-widest italic">A aguardar dados de faturação e tempo...</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 4. AUDIT DE INVENTÁRIO (CENTRO DE ATIVOS FÍSICOS) --}}
        <div class="glass-card p-8 bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col md:flex-row justify-between items-center gap-8 relative overflow-hidden group">
            {{-- Efeito decorativo de fundo --}}
            <div class="absolute -left-10 -bottom-10 size-40 bg-brand-500/5 blur-3xl rounded-full group-hover:scale-150 transition-transform duration-1000"></div>

            <div class="flex items-center gap-6 relative z-10">
                <div class="p-4 bg-brand-600 rounded-2xl shadow-xl shadow-brand-500/20 text-white">
                    <flux:icon name="archive-box" class="size-8" />
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.3em] mb-1">Audit de Ativos em Stock</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black dark:text-white tracking-tighter italic">
                            {{ number_format($inventoryValue, 2, ',', ' ') }} <span class="text-sm not-italic">€</span>
                        </span>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest ml-2">Imobilizado em Armazém</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 relative z-10 w-full md:w-auto">
                @if($lowStockCount > 0)
                    <div class="px-6 py-3 bg-red-500 text-white rounded-2xl flex items-center gap-3 shadow-lg shadow-red-500/20 animate-pulse">
                        <flux:icon name="exclamation-triangle" variant="micro" class="size-4" />
                        <span class="text-[10px] font-black uppercase tracking-widest">{{ $lowStockCount }} Artigos em Rutura</span>
                    </div>
                @else
                    <div class="px-6 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 rounded-2xl flex items-center gap-3 shadow-sm">
                        <flux:icon name="check-badge" variant="micro" class="size-4" />
                        <span class="text-[10px] font-black uppercase tracking-widest">Cadeia de Stock Saudável</span>
                    </div>
                @endif

                <flux:button href="{{ route('hub.business.inventory') }}" variant="ghost" class="rounded-xl font-black uppercase text-[9px] tracking-[0.2em] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    Gerir Inventário →
                </flux:button>
            </div>
        </div>

        {{-- RODAPÉ NEURAL (ESTILO SaaS PRO) --}}
        <footer class="pt-16 pb-6 mt-10 border-t border-zinc-100 dark:border-zinc-800 flex flex-col md:flex-row justify-between items-center gap-6 opacity-60 group">
            <div class="flex items-center gap-4">
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
                    © {{ date('Y') }} {{ auth()->user()->currentWorkspace->name }} · Neural Strategist Protocol
                </p>
                <div class="h-4 w-px bg-zinc-200 dark:bg-zinc-800 hidden md:block"></div>
                <div class="flex items-center gap-2">
                    <div class="size-1.5 rounded-full bg-brand-500 animate-pulse"></div>
                    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest italic">Core Engine Synced</span>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="flex flex-col items-end">
                    <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest mb-1">Última Análise IA</span>
                    <span class="text-[10px] font-bold dark:text-zinc-300 uppercase tracking-tighter">{{ now()->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <div class="h-8 w-px bg-zinc-100 dark:bg-zinc-800"></div>
                <flux:icon name="cpu-chip" class="size-6 text-zinc-400 group-hover:text-brand-500 transition-colors" />
            </div>
        </footer>
    </div> {{-- Fim do wire:loading.remove --}}

    {{-- ESTILOS DE ANIMAÇÃO --}}
    <style>
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
    </style>
</div>
