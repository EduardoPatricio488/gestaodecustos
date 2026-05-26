<div class="space-y-10 pb-20">
    {{-- 1. HEADER CORPORATIVO --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <img src="{{ $workspace->logo_url }}" class="size-24 rounded-[2.5rem] shadow-2xl border-4 border-white dark:border-zinc-800 object-cover bg-white group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute -bottom-1 -right-1 size-7 bg-emerald-500 border-4 border-zinc-50 dark:border-zinc-950 rounded-full shadow-lg"></div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">{{ $workspace->name }}</h1>
                        <flux:badge variant="neutral" size="sm" class="font-black text-[9px] tracking-[0.2em] border-none bg-zinc-100 dark:bg-zinc-800 text-zinc-500 uppercase">Empresa Ativa</flux:badge>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3">
                        <p class="text-xs font-black text-zinc-400 uppercase tracking-widest flex items-center gap-2">
                            <flux:icon name="identification" class="size-3" />
                            {{-- BLUR NO NIF --}}
                            NIF: <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-zinc-600 dark:text-zinc-300 transition-all duration-500">{{ $workspace->tax_number ?? 'S/ NIF' }}</span>
                        </p>
                        <span class="text-zinc-300 dark:text-zinc-800">|</span>
                        <p class="text-xs font-black text-brand-600 dark:text-brand-400 uppercase tracking-widest flex items-center gap-2">
                            <flux:icon name="building-office" class="size-3" />
                            {{ $workspace->industry ?? 'Gestão de Negócio' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:button href="{{ route('export.business') }}" variant="ghost" icon="document-arrow-down" class="rounded-2xl font-bold text-zinc-500 hover:text-brand-600">
                    Contabilista
                </flux:button>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:button href="{{ route('hub.business.ai') }}" variant="primary" icon="sparkles" class="bg-brand-600 border-none shadow-lg shadow-brand-500/20 rounded-2xl font-black uppercase tracking-tighter px-6 text-white">
                    Estrategista IA
                </flux:button>
            </div>
        </div>
    </div>

    {{-- 2. GRID DE PERFORMANCE FINANCEIRA (KPIs) COM PRIVACIDADE --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Faturação --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-7 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group hover:border-emerald-500/30 transition-all">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Faturação Paga</p>
            <h3 class="text-3xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($revenue, 2, ',', ' ') }} €
                </span>
            </h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-[10px] font-bold text-emerald-500">▲ 12%</span>
                <span class="text-[10px] text-zinc-400 font-medium italic">vs anterior</span>
            </div>
        </div>

        {{-- Custos --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-7 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group hover:border-red-500/30 transition-all">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Custos Operacionais</p>
            <h3 class="text-3xl font-black text-red-500 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($totalCosts, 2, ',', ' ') }} €
                </span>
            </h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-[10px] font-bold text-red-500">▼ 4%</span>
                <span class="text-[10px] text-zinc-400 font-medium italic">poupança ativa</span>
            </div>
        </div>

        {{-- Resultado Líquido --}}
        <div class="relative overflow-hidden bg-zinc-950 p-7 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <p class="text-[10px] font-black text-brand-400 uppercase tracking-[0.2em] mb-1">Resultado Líquido</p>
            <h3 class="text-3xl font-black {{ $netProfit >= 0 ? 'text-white' : 'text-red-400' }} tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($netProfit, 2, ',', ' ') }} €
                </span>
            </h3>
            <div class="mt-4 h-1 w-full bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500 shadow-[0_0_10px_#3b82f6]" style="width: 75%"></div>
            </div>
        </div>

        {{-- Runway --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-7 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Business Runway</p>
            <h3 class="text-3xl font-black dark:text-white italic tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $runway }}
                </span>
            </h3>
            <p class="mt-4 text-[10px] text-zinc-500 font-medium italic">Tempo de sobrevivência.</p>
        </div>
    </div>

    {{-- 3. BENTO GRID: OPERAÇÕES & RADAR IA COM PRIVACIDADE --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- PROJETOS EM CURSO --}}
        <div class="lg:col-span-2 glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="flex justify-between items-center mb-10">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand-500/10 rounded-lg text-brand-600">
                        <flux:icon name="presentation-chart-bar" variant="outline" class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Pipeline de Execução</h3>
                        <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Projetos & Eficiência</p>
                    </div>
                </div>
                <flux:badge variant="neutral" class="font-black text-[9px] uppercase bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1">
                    {{-- BLUR NO CONTADOR DE PROJETOS --}}
                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                        {{ $activeProjects->count() }} Ativos
                    </span>
                </flux:badge>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($activeProjects as $project)
                    <div class="p-6 bg-zinc-50/50 dark:bg-zinc-800/40 rounded-[2rem] border border-zinc-100 dark:border-zinc-700/50 hover:border-brand-500/50 transition-all duration-300 group/item">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-black dark:text-white uppercase tracking-tight truncate w-32">{{ $project->name }}</span>
                                <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mt-0.5">Rentabilidade</span>
                            </div>
                            {{-- BLUR NA MARGEM DO PROJETO --}}
                            <span class="text-xs font-black {{ $project->margin >= 30 ? 'text-emerald-500' : 'text-amber-500' }}">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                    {{ round($project->margin) }}% Margem
                                </span>
                            </span>
                        </div>

                        <div class="h-2 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden border border-zinc-100 dark:border-zinc-800 shadow-inner">
                            <div class="h-full bg-brand-500 transition-all duration-1000 shadow-[0_0_10px_rgba(59,130,246,0.4)]"
                                 style="width: {{ $project->margin }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-10 text-center"><p class="text-zinc-400 italic text-sm font-medium">Sem projetos ativos.</p></div>
                @endforelse
            </div>
        </div>

        {{-- RADAR DE RISCO IA --}}
        <div class="glass-card p-8 bg-zinc-950 dark:bg-zinc-900 border-2 border-dashed border-zinc-800 dark:border-brand-500/20 rounded-[2.5rem] flex flex-col group relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-brand-500/5 to-transparent pointer-events-none"></div>

            <div class="relative z-10 flex-1 flex flex-col">
                <div class="flex items-center gap-3 mb-8">
                    <div class="size-2 rounded-full bg-brand-500 animate-ping"></div>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Radar de Riscos IA</h3>
                </div>

                <div class="space-y-6 flex-1">
                    {{-- Inventário --}}
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5 transition-colors">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Inventário</p>
                        {{-- BLUR NO STATUS DE STOCK --}}
                        <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-[10px] font-black px-3 py-1 rounded-lg {{ $lowStockCount > 0 ? 'bg-red-500 text-white' : 'bg-emerald-500/10 text-emerald-500' }} transition-all duration-500">
                            {{ $lowStockCount > 0 ? $lowStockCount . ' Alertas' : 'Nível OK' }}
                        </span>
                    </div>

                    {{-- Documentação --}}
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5 transition-colors">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Arquivo Fiscal</p>
                        {{-- BLUR NO STATUS DE DOCUMENTOS --}}
                        <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-[10px] font-black px-3 py-1 rounded-lg {{ $criticalDocsCount > 0 ? 'bg-amber-500 text-white' : 'bg-emerald-500/10 text-emerald-500' }} transition-all duration-500">
                            {{ $criticalDocsCount > 0 ? $criticalDocsCount . ' Críticos' : 'Tudo em dia' }}
                        </span>
                    </div>

                    {{-- Operações --}}
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5 transition-colors">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Tarefas Equipa</p>
                        {{-- BLUR NO STATUS DE TAREFAS --}}
                        <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-[10px] font-black px-3 py-1 rounded-lg {{ $overdueTasksCount > 0 ? 'bg-red-500 text-white shadow-[0_0_10px_rgba(239,68,68,0.3)]' : 'bg-emerald-500/10 text-emerald-500' }} transition-all duration-500">
                            {{ $overdueTasksCount > 0 ? $overdueTasksCount . ' Atrasadas' : 'OK' }}
                        </span>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-white/5">
                    <p class="text-[9px] text-zinc-500 font-bold uppercase leading-relaxed italic">
                        <span class="text-brand-400 font-black tracking-widest mr-1">Análise IA:</span>
                        Foram detetados
                        {{-- BLUR NO TOTAL DE ALERTAS --}}
                        <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                            {{ $lowStockCount + $criticalDocsCount + $overdueTasksCount }}
                        </span>
                        pontos de atenção operacional.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. LINHA FINAL: FISCALIDADE, CASHFLOW & RH COM PRIVACIDADE --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- PROVISÃO DE IMPOSTOS --}}
        <div class="glass-card p-8 bg-amber-50/50 dark:bg-amber-900/10 rounded-[2.5rem] border border-amber-200 dark:border-amber-800/50 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-amber-500 rounded-xl shadow-lg text-white">
                        <flux:icon name="receipt-percent" variant="mini" class="size-4" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-700 dark:text-amber-500">Provisão Fiscal Estimada</h3>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-amber-900/60 dark:text-zinc-400">IVA (Saldo do Mês)</span>
                        {{-- BLUR NO IVA --}}
                        <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-sm font-black text-amber-700 dark:text-zinc-200 transition-all duration-500">
                            {{ number_format($vatProvision, 2, ',', ' ') }} €
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-amber-900/60 dark:text-zinc-400">IRC (Estimativa)</span>
                        {{-- BLUR NO IRC --}}
                        <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-sm font-black text-amber-700 dark:text-zinc-200 transition-all duration-500">
                            {{ number_format($ircProvision, 2, ',', ' ') }} €
                        </span>
                    </div>

                    <div class="pt-6 mt-2 border-t border-amber-200/50 dark:border-zinc-800 flex justify-between items-end">
                        <div>
                            <p class="text-[9px] font-black uppercase text-amber-600 dark:text-amber-500 tracking-widest">Reserva Total</p>
                        </div>
                        <p class="text-3xl font-black text-amber-600 tracking-tighter">
                            {{-- BLUR NO TOTAL DE RESERVA --}}
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($vatProvision + $ircProvision, 2, ',', ' ') }} €
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CASHFLOW PENDENTE (RECEITAS EM TRÂNSITO) --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between group hover:border-emerald-500/30 transition-all">
            <div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-emerald-500 rounded-xl shadow-lg text-white">
                        <flux:icon name="arrow-up-right" variant="mini" class="size-4" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Receitas em Trânsito</h3>
                </div>

                <p class="text-4xl font-black text-emerald-600 tracking-tighter italic">
                    {{-- BLUR NO CASHFLOW A RECEBER --}}
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($accountsReceivable, 2, ',', ' ') }} €
                    </span>
                </p>
            </div>
            <p class="mt-6 text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Faturação emitida pendente</p>
        </div>

        {{-- EQUIPA & PAYROLL --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between group hover:border-brand-500/30 transition-all">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand-600 rounded-xl shadow-lg text-white">
                        <flux:icon name="users" variant="mini" class="size-4" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Payroll Mensal</h3>
                </div>
                <flux:badge variant="neutral" size="sm" class="font-black text-[9px] tracking-widest bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1">
                    {{-- BLUR NO NÚMERO DE PESSOAS --}}
                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                        {{ $teamCount }} PESSOAS
                    </span>
                </flux:badge>
            </div>

            <div class="mt-8">
                <p class="text-3xl font-black dark:text-white tracking-tighter">
                    {{-- BLUR NO VALOR DO PAYROLL --}}
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($payroll, 2, ',', ' ') }} €
                    </span>
                </p>
                <div class="mt-4 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-700">
                    <p class="text-[9px] font-black uppercase text-zinc-500">Custo RH Mensal Fixo</p>
                </div>
            </div>
        </div>
    </div>

{{-- RODAPÉ CORPORATIVO --}}
@php
    $openTickets = \App\Models\SupportTicket::where('status', 'open')
        ->where('workspace_id', $workspace->id)
        ->count();
@endphp

<div class="pt-10 border-t border-zinc-100 dark:border-zinc-800 mt-10 flex flex-col md:flex-row justify-between items-center gap-4">

    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
        © {{ date('Y') }} {{ $workspace->name }} · Sistema de Gestão Enterprise
    </p>

    <div class="flex items-center gap-6">

        {{-- SUPORTE TÉCNICO EMPRESA --}}
        <flux:sidebar.item
            icon="chat-bubble-left-right"
            :href="route('hub.business.support')"
            :current="request()->routeIs('hub.business.support')"
            wire:navigate
        >
            <div class="flex items-center gap-2">

                <span class="text-[10px] font-black uppercase tracking-widest">
                    Suporte Técnico
                </span>

                @if($openTickets > 0)
                    <span class="inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 rounded-full bg-red-500 text-white text-[10px] font-black shadow-lg shadow-red-500/30">
                        {{ $openTickets }}
                    </span>
                @endif

            </div>
        </flux:sidebar.item>

        {{-- SEGURANÇA --}}
        <a href="#"
           class="text-[9px] font-black text-zinc-400 hover:text-brand-500 uppercase tracking-widest transition-colors">
            Segurança
        </a>

    </div>
</div>
