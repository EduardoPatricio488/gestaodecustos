{{-- 1. HEADER CORRIGIDO --}}
<div class="space-y-10 pb-24" x-data="{ privacyMode: false, openRaiseModal: false }">

    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="users" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div class="text-left">
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Equipa</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">Capital Humano</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Gestão de competências e estrutura organizacional</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">

                {{-- BOTÃO DO OLHO CORRIGIDO (Sem erro de icon.) --}}
                <button
                    type="button"
                    x-on:click="privacyMode = !privacyMode"
                    class="rounded-xl p-2 transition-all"
                    :class="privacyMode ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500'"
                >
                    {{-- Renderizamos os dois ícones e o Alpine escolhe qual mostrar --}}
                    <flux:icon x-show="!privacyMode" name="eye" class="size-5" />
                    <flux:icon x-show="privacyMode" name="eye-slash" class="size-5" />
                </button>



                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
            </div>
        </header>
    </div>


    {{-- ALERTAS DE RESCISÃO PENDENTES --}}
    @php
        $pendingResignations = $employees->where('resignation_status', 'pending');
    @endphp

    @if($pendingResignations->count() > 0)
        <div class="space-y-4 mb-10">
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-red-500 px-2">Pedidos de Rescisão Urgentes</h3>

            @foreach($pendingResignations as $req)
                <div class="p-6 bg-red-50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/20 rounded-[2rem] flex flex-col md:flex-row justify-between items-center gap-6 animate-pulse">
                    <div class="flex items-center gap-4 text-left w-full">
                        <div class="size-12 rounded-xl bg-red-600 flex items-center justify-center text-white shadow-lg">
                            <flux:icon name="hand-raised" variant="micro" class="size-6" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-black dark:text-white uppercase leading-none">{{ $req->name }} solicita a saída</p>
                            <p class="text-xs text-zinc-500 mt-2 italic">"{{ $req->resignation_reason }}"</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <flux:button wire:click="rejectResignation({{ $req->id }})" variant="ghost" class="flex-1 md:flex-none font-black uppercase text-[9px] text-zinc-500">
                            Rejeitar
                        </flux:button>
                        <flux:button wire:click="acceptResignation({{ $req->id }})" variant="primary" class="flex-1 md:flex-none bg-red-600 border-none font-black uppercase text-[9px] px-6 h-10 shadow-lg shadow-red-500/20">
                            Confirmar Saída
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif


 {{-- 2. WIDGET DE CONVITE INTERNO --}}
    <div class="relative overflow-hidden bg-zinc-900 border border-white/10 rounded-[2.5rem] p-8 shadow-2xl group animate-in fade-in slide-in-from-top-4 duration-1000">
        <div class="absolute -right-20 -top-20 size-64 bg-brand-500/10 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8 relative z-10 text-left">
            <div class="flex items-center gap-6">
                <div class="size-16 rounded-2xl bg-brand-500/20 flex items-center justify-center text-brand-400 shadow-inner">
                    <flux:icon name="user-plus" variant="outline" class="size-8" />
                </div>
                <div>
                    <h3 class="text-xl font-black text-white uppercase italic tracking-tighter leading-none">Acesso de Colaborador</h3>
                    <p class="text-xs text-zinc-400 font-medium mt-2 leading-relaxed">
                        Código para funcionários já contratados criarem conta na <span class="text-brand-400 font-bold">{{ $workspace->name }}</span>.
                    </p>
                </div>
            </div>
            <div class="flex flex-col items-center lg:items-end gap-3 w-full lg:w-auto">
                <div class="flex items-center gap-2 p-1.5 bg-black/40 border border-white/10 rounded-2xl shadow-inner w-full lg:w-auto justify-between lg:justify-start">
                    <span class="px-6 py-2 text-2xl font-black tracking-[0.4em] text-white uppercase font-mono">{{ $workspace->invite_code ?? '---' }}</span>
                    <div class="flex items-center gap-1">
                        <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $workspace->invite_code }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 text-zinc-400">
                            <flux:icon x-show="!copied" name="clipboard" class="size-5" />
                            <flux:icon x-show="copied" name="check" class="size-5 text-emerald-500" />
                        </button>
                        <button wire:click="generateNewInviteCode" class="p-3 rounded-xl bg-white/5 hover:bg-brand-500/20 text-zinc-400"><flux:icon name="arrow-path" class="size-5" /></button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- 2. KPIs INICIAIS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Payroll Mensal --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Payroll Mensal Estimado</p>
                <h3 class="text-4xl font-black text-white tracking-tighter italic">
                    <span x-bind:class="privacyMode ? 'blur-md select-none' : ''" class="inline-block transition-all duration-300">
                        {{ number_format($totalPayroll, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
        </div>

        {{-- Contagem Equipa --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Efetivos em Funções</p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span x-bind:class="privacyMode ? 'blur-sm select-none' : ''" class="inline-block transition-all duration-300">
                    {{ $employeeCount }}
                </span>
                <span class="text-xs text-zinc-400 uppercase font-bold ml-2 tracking-widest italic">Pessoas</span>
            </h3>
        </div>

        {{-- Tesouraria Inteligente --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Ciclo de Tesouraria Inteligente</p>
            <h3 class="text-2xl font-black text-orange-500 tracking-tighter uppercase italic leading-none">
                Próximo pagamento em {{ $daysUntilNext }} dias
            </h3>
            <p class="mt-2 text-xs text-zinc-500 font-bold uppercase tracking-widest italic">
                Dia {{ sprintf('%02d', $nextPayDay) }} de {{ now()->translatedFormat('M') }}
            </p>
            <p class="mt-4 text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Liquidez necessária</p>
            <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tighter italic">
                <span x-bind:class="privacyMode ? 'blur-sm select-none' : ''">
                    {{ number_format($liquidity, 2, ',', ' ') }} €
                </span>
            </h3>
        </div>
    </div>


    {{-- WIDGET PREMIUM DE KPIs DE RH --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Próximo pagamento em X dias --}}
        <div class="relative overflow-hidden bg-brand-600 p-8 rounded-[2.5rem] shadow-xl text-white">
            <div class="absolute inset-0 bg-brand-500/20 blur-[80px]"></div>
            <div class="relative z-10">
                <flux:icon name="calendar" class="size-10 mb-4 opacity-80" />
                <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Próximo Pagamento</p>
                <h3 class="text-4xl font-black italic tracking-tighter mt-2">
                    <span :class="privacyMode ? 'blur-sm select-none' : ''">
                        {{ $daysUntilNext }} dias
                    </span>
                </h3>
                <p class="text-xs opacity-70 mt-2 italic">
                    Dia {{ sprintf('%02d', $nextPayDay) }}
                </p>
            </div>
        </div>

        {{-- Liquidez necessária --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-xl border border-zinc-800 text-white">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full"></div>
            <div class="relative z-10">
                <flux:icon name="banknotes" class="size-10 mb-4 text-brand-400" />
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Liquidez Necessária</p>
                <h3 class="text-4xl font-black italic tracking-tighter mt-2">
                    <span :class="privacyMode ? 'blur-md select-none' : ''">
                        {{ number_format($liquidity, 2, ',', ' ') }} €
                    </span>
                </h3>
                <p class="text-xs text-zinc-500 mt-2 italic">
                    Para o próximo ciclo (TSU + Base)
                </p>
            </div>
        </div>

        {{-- Custo médio por colaborador --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:icon name="chart-bar" class="size-10 mb-4 text-brand-600" />
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Custo Médio</p>
            <h3 class="text-4xl font-black tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''">
                    {{ number_format($avgCost, 2, ',', ' ') }} €
                </span>
            </h3>
        </div>

        {{-- Distribuição salarial --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:icon name="bars-3" class="size-10 mb-4 text-brand-600" />
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Distribuição Salarial</p>

            <div class="mt-4 space-y-2 text-sm font-bold">
                <p class="flex justify-between">
                    <span class="text-zinc-500">Baixo (≤ 1000€)</span>
                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-zinc-900 dark:text-white">{{ $low }}</span>
                </p>
                <p class="flex justify-between">
                    <span class="text-zinc-500">Médio (1000–2500€)</span>
                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-zinc-900 dark:text-white">{{ $mid }}</span>
                </p>
                <p class="flex justify-between">
                    <span class="text-zinc-500">Alto (≥ 2500€)</span>
                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-zinc-900 dark:text-white">{{ $high }}</span>
                </p>
            </div>
        </div>

        {{-- Ativos vs Inativos --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:icon name="users" class="size-10 mb-4 text-brand-600" />
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Estado da Equipa</p>

            <div class="mt-4 space-y-2 text-sm font-bold">
                <p class="flex justify-between">
                    <span class="text-zinc-500">Ativos / Funções</span>
                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-emerald-500">{{ $active }}</span>
                </p>
                <p class="flex justify-between">
                    <span class="text-zinc-500">Inativos / Suspensos</span>
                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-zinc-400">{{ $inactive + $suspended }}</span>
                </p>
            </div>
        </div>

        {{-- Taxa de Rotatividade --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:icon name="arrow-path" class="size-10 mb-4 text-orange-500" />
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Rotatividade (Trimestral)</p>
            <h3 class="text-4xl font-black tracking-tighter text-orange-500">
                {{ $turnover }}
            </h3>
            <p class="text-[9px] font-bold text-zinc-400 uppercase mt-2 italic">Média do setor: 3.1%</p>
        </div>

    </div>

{{-- 5. WIDGET: PANORAMA CLICÁVEL --}}
    <div class="mt-20 space-y-6">
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="chart-pie" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                    Panorama de Capital Humano
                    <span class="ml-2 text-brand-500">
                        {{ $statusFilter !== 'all' ? '— Filtrado por ' . ucfirst($statusFilter) : '' }}
                    </span>
                </h2>
            </div>

            @if($statusFilter !== 'all')
                <button wire:click="$set('statusFilter', 'all')" class="text-[10px] font-black uppercase text-zinc-400 hover:text-brand-500 transition">
                    Limpar Filtro [x]
                </button>
            @endif
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Filtro: Ativos --}}
            <button wire:click="setFilter('active')"
                class="bg-white dark:bg-zinc-900 border {{ $statusFilter === 'active' ? 'border-emerald-500 shadow-lg shadow-emerald-500/10' : 'border-zinc-200 dark:border-zinc-800' }} rounded-[2rem] p-6 flex flex-col items-center text-center group transition-all">
                <div class="size-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <flux:icon name="check-circle" variant="outline" class="size-6" />
                </div>
                <h4 class="text-3xl font-black dark:text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-sm' : ''">{{ $active }}</span>
                </h4>
                <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest mt-1">Ativos</p>
            </button>

            {{-- Filtro: Suspensos --}}
            <button wire:click="setFilter('suspended')"
                class="bg-white dark:bg-zinc-900 border {{ $statusFilter === 'suspended' ? 'border-orange-500 shadow-lg shadow-orange-500/10' : 'border-zinc-200 dark:border-zinc-800' }} rounded-[2rem] p-6 flex flex-col items-center text-center group transition-all">
                <div class="size-12 rounded-2xl bg-orange-500/10 text-orange-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <flux:icon name="pause-circle" variant="outline" class="size-6" />
                </div>
                <h4 class="text-3xl font-black dark:text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-sm' : ''">{{ $suspended }}</span>
                </h4>
                <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest mt-1">Suspensos</p>
            </button>

            {{-- Filtro: Inativos --}}
            <button wire:click="setFilter('inactive')"
                class="bg-white dark:bg-zinc-900 border {{ $statusFilter === 'inactive' ? 'border-zinc-500 shadow-lg' : 'border-zinc-200 dark:border-zinc-800' }} rounded-[2rem] p-6 flex flex-col items-center text-center group transition-all">
                <div class="size-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <flux:icon name="stop-circle" variant="outline" class="size-6" />
                </div>
                <h4 class="text-3xl font-black dark:text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-sm' : ''">{{ $inactive }}</span>
                </h4>
                <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest mt-1">Inativos</p>
            </button>

            {{-- Filtro: Despedidos --}}
            <button wire:click="setFilter('terminated')"
                class="bg-white dark:bg-zinc-900 border {{ $statusFilter === 'terminated' ? 'border-red-500 shadow-lg shadow-red-500/10' : 'border-zinc-200 dark:border-zinc-800' }} rounded-[2rem] p-6 flex flex-col items-center text-center group transition-all">
                <div class="size-12 rounded-2xl bg-red-500/10 text-red-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <flux:icon name="x-circle" variant="outline" class="size-6" />
                </div>
                <h4 class="text-3xl font-black dark:text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-sm' : ''">{{ $turnover }}</span>
                </h4>
                <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest mt-1">Histórico</p>
            </button>

        </div>
    </div>









{{-- 3. DIRETÓRIO DE EQUIPA --}}
    <div class="space-y-8 text-left">
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 px-2">
            <div class="flex items-center gap-3">
                <flux:icon name="user-group" class="size-4 text-zinc-400" />
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 leading-none italic">Diretório de Colaboradores</h2>
            </div>

            {{-- Filtros de Data que afetam a vista de ponto dentro do card --}}
            <div class="flex gap-2">
                <select wire:model.live="selectedMonth" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-[10px] font-black uppercase p-2 outline-none shadow-sm">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
                <select wire:model.live="selectedYear" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-[10px] font-black uppercase p-2 outline-none shadow-sm">
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                </select>
            </div>
        </div>
















<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($employees as $emp)





            <div wire:key="emp-card-{{ $emp->id }}"
     x-data="{ view: 'info', open: false }"
     class="relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] shadow-sm hover:border-brand-500/30 transition-all flex flex-col h-[500px] group">

    {{-- SWITCHER DE VISTA --}}
    <div class="absolute top-6 left-1/2 -translate-x-1/2 flex gap-1 p-1
            bg-zinc-100 dark:bg-zinc-800 rounded-xl z-50 shadow-inner
            border border-zinc-300/40 dark:border-zinc-700/40 backdrop-blur-sm">

    <!-- Botão Info -->
    <button type="button" @click.stop="view = 'info'"
        :class="view === 'info'
            ? 'bg-white dark:bg-zinc-700 text-brand-600 shadow-sm border border-brand-600/40'
            : 'text-zinc-400'"
        class="p-1.5 rounded-lg transition-all cursor-pointer
               hover:bg-white/50 dark:hover:bg-zinc-700/50 flex items-center justify-center">
        <flux:icon name="user" variant="micro" class="size-3.5" />
    </button>

    <!-- Botão Logs -->
    <button type="button" @click.stop="view = 'logs'"
        :class="view === 'logs'
            ? 'bg-white dark:bg-zinc-700 text-brand-600 shadow-sm border border-brand-600/40'
            : 'text-zinc-400'"
        class="p-1.5 rounded-lg transition-all cursor-pointer
               hover:bg-white/50 dark:hover:bg-zinc-700/50 flex items-center justify-center">
        <flux:icon name="clock" variant="micro" class="size-3.5" />
    </button>

</div>



    {{-- VISTA A: INFORMAÇÃO DO PERFIL --}}
    <div x-show="view === 'info'" class="flex flex-col h-full animate-in fade-in duration-300">

        {{-- CABEÇALHO --}}
        <div class="p-7 flex justify-between items-start pt-16 relative z-30">
            <div class="flex items-center gap-5">
                <div class="size-16 rounded-[1.5rem] overflow-hidden border-2 border-white dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-black text-2xl text-brand-600 uppercase shrink-0 shadow-lg">
                    @if ($emp->photo_path) <img src="{{ asset('storage/' . $emp->photo_path) }}" class="w-full h-full object-cover">
                    @else {{ substr($emp->name, 0, 1) }} @endif
                </div>
                <div class="min-w-0 text-left">
                    <h4 class="font-black dark:text-white uppercase text-base tracking-tight leading-none truncate mb-1">{{ $emp->name }}</h4>
                      @if($emp->user?->plan === 'pro' || $emp->user?->isDiamond())
        <span title="Membro Business" class="text-indigo-500 text-xs">💎</span>
    @endif
                    <p class="text-[10px] font-black text-brand-600 dark:text-brand-400 uppercase tracking-[0.15em]">{{ $emp->role }}</p>
                </div>
            </div>
            <div class="relative">
            <button @click.stop="open = !open" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white p-2 transition-colors"><flux:icon name="ellipsis-horizontal" class="size-5" /></button>

            {{-- DROPDOWN MENU --}}
            <div
                x-show="open"
                x-cloak
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 top-10 w-52 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-xl z-50 overflow-hidden"
            >
                <div class="p-1.5 space-y-0.5">
                    <button
                        @click="open = false"
                        wire:click="edit({{ $emp->id }})"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-brand-50 dark:hover:bg-brand-500/10 hover:text-brand-600 transition-all text-left"
                    >
                        <flux:icon name="pencil-square" class="size-4 shrink-0 text-brand-500" />
                        Editar Ficha
                    </button>
{{-- ABAIXO DE EDITAR FICHA --}}
<button
    @click="open = false"
    wire:click="generateEmployeeAccessCode({{ $emp->id }})"
    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all text-left"
>
    <flux:icon name="key" class="size-4 shrink-0 text-zinc-900 dark:text-white" />
    Gerar Chave de Acesso
</button>

{{-- OPÇÃO ATIVAR PLANO BUSINESS --}}
<button
    @click="open = false"
    wire:click="activateBusinessPlan({{ $emp->id }})"
    wire:confirm="Desejas oferecer acesso Premium/Business a este utilizador?"
    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all text-left"
>
    <flux:icon name="sparkles" variant="solid" class="size-4 shrink-0 text-indigo-500" />
    Ativar Plano Business
</button>
<button
    @click="open = false"
    wire:click="sendInviteEmail({{ $emp->id }})"
    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-all text-left"
>
    <flux:icon name="paper-airplane" class="size-4 shrink-0 text-emerald-500" />
    Enviar Token Empresa
</button>
          <button
    type="button"
    {{-- Alpine abre o modal instantaneamente --}}
    @click="$dispatch('modal-show', { name: 'raise-salary-modal' }); open = false;"
    {{-- Livewire carrega os dados --}}
    wire:click="openRaiseModal({{ $emp->id }})"
    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:text-emerald-600 transition-all text-left"
>
    <flux:icon name="arrow-up-circle" class="size-4 shrink-0 text-emerald-500" />
    Aumento Salarial
</button>

                    <button
                        @click="open = false"
                        wire:click="viewAttendance({{ $emp->id }})"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 transition-all text-left"
                    >
                        <flux:icon name="clock" class="size-4 shrink-0 text-blue-500" />
                        Ver Assiduidade
                    </button>
<button
    @click="open = false"
    wire:click="viewDocuments({{ $emp->id }})"
    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all text-left"
>
    <flux:icon name="folder" class="size-4 shrink-0 text-zinc-500" />
    Histórico de Docs
</button>
                    <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>

                    @if($emp->suspended || !$emp->active)
                        <button
                            @click="open = false"
                            wire:click="activate({{ $emp->id }})"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:text-emerald-600 transition-all text-left"
                        >
                            <flux:icon name="check-circle" class="size-4 shrink-0 text-emerald-500" />
                            Reativar
                        </button>
                    @else
                        <button
                            @click="open = false"
                            wire:click="suspend({{ $emp->id }})"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-orange-50 dark:hover:bg-orange-500/10 hover:text-orange-600 transition-all text-left"
                        >
                            <flux:icon name="pause-circle" class="size-4 shrink-0 text-orange-500" />
                            Suspender
                        </button>
                    @endif

                    @unless($emp->terminated_at)
                        <button
                            @click="open = false"
                            wire:click="terminate({{ $emp->id }})"
                            wire:confirm="Tens a certeza que queres terminar o vínculo de {{ $emp->name }}?"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all text-left"
                        >
                            <flux:icon name="x-circle" class="size-4 shrink-0 text-red-500" />
                            Terminar Vínculo
                        </button>
                    @endunless

                    <button
                        @click="open = false"
                        wire:click="deleteEmployee({{ $emp->id }})"
                        wire:confirm="ATENÇÃO: Eliminar permanentemente o registo de {{ $emp->name }}? Esta ação não pode ser revertida."
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 transition-all text-left"
                    >
                        <flux:icon name="trash" class="size-4 shrink-0 text-red-400" />
                        Eliminar Registo
                    </button>
                </div>
            </div>
        </div>
        </div>

        {{-- CONTEÚDO CENTRAL (O PREENCHIMENTO) --}}
        <div class="px-7 space-y-6 flex-1 relative z-10">

            {{-- 1. LISTA DE DADOS TÉCNICOS --}}
            <div class="grid grid-cols-2 gap-y-3 border-b border-zinc-100 dark:border-zinc-800 pb-6">
                <div class="text-left">
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Departamento</p>
                    <p class="text-[10px] font-bold dark:text-zinc-200 uppercase mt-0.5">Geral / Sede</p>
                </div>
                <div class="text-left">
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Pagamento</p>
                    <p class="text-[10px] font-bold dark:text-zinc-200 uppercase mt-0.5">Dia {{ sprintf('%02d', $emp->pay_day) }}</p>
                </div>
                <div class="text-left">
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">ID Interno</p>
                    <p class="text-[10px] font-bold dark:text-zinc-200 uppercase mt-0.5">#{{ 200 + ($emp->id % 50) }}</p>
                </div>
                <div class="text-left">
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Entrada</p>
                    <p class="text-[10px] font-bold dark:text-zinc-200 uppercase mt-0.5">{{ $emp->created_at->format('M Y') }}</p>
                </div>
            </div>

            {{-- 2. MINI KPIs DE PERFORMANCE --}}
            <div class="flex justify-between items-center bg-zinc-50 dark:bg-zinc-950/50 p-4 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-inner">
                <div class="text-center">
                    <p class="text-[11px] font-black text-zinc-900 dark:text-white">12</p>
                    <p class="text-[7px] font-black text-zinc-400 uppercase tracking-widest">Tarefas</p>
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:border-zinc-800"></div>
                <div class="text-center">
                    <p class="text-[11px] font-black text-emerald-500">94%</p>
                    <p class="text-[7px] font-black text-zinc-400 uppercase tracking-widest">Eficiência</p>
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:border-zinc-800"></div>
                <div class="text-center">
                    <p class="text-[11px] font-black text-brand-500">162h</p>
                    <p class="text-[7px] font-black text-zinc-400 uppercase tracking-widest">Este Mês</p>
                </div>
            </div>

            {{-- 3. PROJETO ATUAL --}}
            <div class="text-left">
                <div class="flex justify-between items-center mb-1.5">
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Foco Atual</p>
                    <span class="text-[8px] font-bold text-brand-600">Em Curso</span>
                </div>
                <div class="p-3 bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 rounded-2xl flex items-center gap-3">
                    <div class="size-2 rounded-full bg-brand-500 animate-pulse"></div>
                    <p class="text-[10px] font-black dark:text-zinc-200 uppercase truncate">Otimização de Workflow</p>
                </div>
            </div>
        </div>

        {{-- RODAPÉ --}}
        <div class="mt-auto p-7 bg-zinc-50/50 dark:bg-zinc-800/40 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-end rounded-b-[3rem] relative z-10">
            <div class="text-left leading-none">
                <p class="text-[9px] font-black text-zinc-400 uppercase mb-2 leading-none tracking-widest">Vencimento Mensal</p>
                <h3 class="text-xl font-black dark:text-zinc-100 tracking-tighter leading-none italic">
                    <span x-bind:class="privacyMode ? 'blur-sm select-none' : ''">{{ number_format($emp->salary, 2, ',', ' ') }} €</span>
                </h3>
            </div>
            @php $st = $emp->terminated_at ? ['Despedido','red'] : ($emp->suspended ? ['Suspenso','orange'] : ['Ativo','emerald']); @endphp
            <span class="px-3 py-1 bg-{{ $st[1] }}-500/10 text-{{ $st[1] }}-600 border border-{{ $st[1] }}-500/20 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">{{ $st[0] }}</span>
        </div>
    </div>
















{{-- FACE B: REGISTOS DE TEMPO (ASSIDUIDADE) --}}
<div x-show="view === 'logs'" x-cloak class="flex flex-col h-full animate-in slide-in-from-right-4 duration-300 text-left overflow-hidden rounded-[3rem]">
    <div class="p-7 pt-16 flex-1 overflow-hidden">
        <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4 italic text-left">Histórico de Ponto</h4>

        <div class="space-y-2 overflow-y-auto max-h-[280px] pr-2 custom-scrollbar">
            {{-- Procura os logs do colaborador atual --}}
            @php
                $userLogs = $allLogs->get($emp->user_id) ?? collect();
            @endphp

            @forelse($userLogs as $log)
                <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-700/50 shadow-inner">
                    <div class="text-left leading-none">
                        <p class="text-[9px] font-black text-zinc-400 uppercase mb-1">
                            {{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M') }}
                        </p>
                        <p class="text-[10px] font-bold dark:text-white uppercase tracking-widest">
                            {{ \Carbon\Carbon::parse($log->clock_in)->format('H:i') }}
                            →
                            {{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('H:i') : '--:--' }}
                        </p>
                    </div>

                    <span class="text-[10px] font-black text-brand-600 bg-white dark:bg-zinc-900 px-2 py-1 rounded-lg shadow-sm border border-zinc-100 dark:border-zinc-800">
                        {{ floor($log->total_minutes / 60) }}h {{ $log->total_minutes % 60 }}m
                    </span>
                </div>
            @empty
    <div class="h-48 flex flex-col items-center justify-center opacity-30 text-center">
        <flux:icon name="clock" class="size-12 mb-3" />
        <p class="text-[10px] font-black uppercase tracking-widest leading-tight text-left">
            Sem registos em <br>
            {{-- ADICIONADO O (int) ANTES DA VARIÁVEL --}}
            {{ \Carbon\Carbon::create()->month((int) $selectedMonth)->translatedFormat('F') }}
        </p>
    </div>
@endforelse
        </div>
    </div>

    {{-- TOTALIZADOR NO FUNDO DO CARD --}}
    <div class="p-5 bg-zinc-100 dark:bg-zinc-800/80 border-t border-zinc-200 dark:border-zinc-700 text-center rounded-b-[3rem]">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest leading-none mb-1">Horas Acumuladas</p>
        <p class="text-xl font-black text-brand-600 tracking-tighter italic">
            {{ floor($userLogs->sum('total_minutes') / 60) }}h e {{ $userLogs->sum('total_minutes') % 60 }}m
        </p>
    </div>
</div>
        </div> {{-- fecha emp-card --}}

            @empty
                <div class="col-span-3 flex flex-col items-center justify-center py-24 opacity-30 text-center">
                    <flux:icon name="users" class="size-16 mb-4" />
                    <p class="text-xs font-black uppercase tracking-widest text-zinc-400">Sem colaboradores registados</p>
                </div>
            @endforelse
        </div> {{-- fecha grid --}}






















    </div> {{-- fecha space-y-8 text-left --}}

    {{-- 4. MODAL: REGISTO DE TALENTO --}}
    <flux:modal name="add-employee-modal" position="center" class="md:w-[600px] !p-0 overflow-visible" wire:ignore.self>
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg">
                    <flux:icon name="user-plus" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Editar Ficha Técnica' : 'Contratar Colaborador' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium italic">Define os parâmetros contratuais do talento.</p>
                </div>
            </div>

            {{-- FOTO COM PREVIEW --}}
            <div class="flex items-center justify-center">
                <div class="relative group">
                    <div class="size-32 rounded-3xl overflow-hidden border-4 border-zinc-100 dark:border-zinc-900 shadow-xl bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($editingId && $employee && $employee->photo_path)
                            <img src="{{ asset('storage/' . $employee->photo_path) }}" class="w-full h-full object-cover">
                        @else
                            <flux:icon name="user" class="size-16 text-zinc-400" />
                        @endif

                        <div wire:loading wire:target="photo" class="absolute inset-0 bg-black/60 backdrop-blur-sm flex flex-col items-center justify-center">
                            <flux:icon name="arrow-path" class="size-8 text-white animate-spin" />
                        </div>
                    </div>

                    <label for="photo-upload" class="absolute -bottom-3 -right-3 size-12 bg-brand-600 text-white rounded-2xl flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-all">
                        <flux:icon name="camera" class="size-5" />
                        <input type="file" id="photo-upload" wire:model="photo" class="hidden" accept="image/*">
                    </label>
                </div>
            </div>

            {{-- FORMULÁRIO --}}
            <div class="space-y-8">
                <div class="space-y-6">
                    <flux:input wire:model="name" label="Nome Completo" placeholder="Ex: João Silva" class="font-bold" />
                    <flux:input wire:model="role" label="Cargo / Função" placeholder="Ex: Engenheiro de Software" class="font-bold" />
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="salary" type="number" step="0.01" label="Salário Bruto (€)" placeholder="0.00" class="font-black text-xl" />
                    <flux:input wire:model="pay_day" type="number" min="1" max="31" label="Dia de Processamento" class="font-black text-xl text-center" />
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Ficha' : 'Confirmar Contratação' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
{{-- 5. MODAL: AUMENTO SALARIAL --}}
    <flux:modal name="raise-salary-modal" position="center" class="md:w-[500px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800 text-left">

            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-lg">
                    <flux:icon name="arrow-up-circle" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">Aumento Salarial</flux:heading>
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1">Revisão de Vencimento</p>
                </div>
            </div>

            {{-- O conteúdo interno é que espera pelo ID --}}
            <div class="min-h-[250px]">
                @if($raiseEmployeeId)
                    <div class="space-y-6 animate-in fade-in duration-300">
                        <div class="text-left">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 leading-none">Colaborador em análise</p>
                            <p class="text-xl font-black dark:text-white uppercase italic">{{ $selectedEmployeeName }}</p>
                        </div>

                        <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 shadow-inner">
                            <flux:input
                                wire:model.live="raiseAmount"
                                type="number"
                                step="0.01"
                                label="Valor do Aumento (€)"
                                placeholder="0.00"
                                class="font-black text-xl"
                            />

                            @php
                                $currentSalary = \App\Models\Employee::find($raiseEmployeeId)?->salary ?? 0;
                            @endphp

                            <div class="mt-6 flex justify-between items-end border-t border-zinc-200 dark:border-zinc-800 pt-4">
                                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Novo Vencimento Final</p>
                                <p class="text-2xl font-black text-emerald-500 tracking-tighter italic leading-none">
                                    {{ number_format((float)($currentSalary + (float)$raiseAmount), 2, ',', ' ') }} €
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <flux:modal.close class="flex-1">
                                <flux:button variant="ghost" class="w-full font-black uppercase text-[10px]">Cancelar</flux:button>
                            </flux:modal.close>

                            <flux:button
                                wire:click="applyRaise"
                                variant="primary"
                                class="flex-[2] !bg-emerald-600 border-none font-black uppercase h-14 rounded-2xl shadow-xl"
                            >
                                Confirmar Aumento
                            </flux:button>
                        </div>
                    </div>
                @else
                    {{-- Loader enquanto o Livewire termina o wire:click --}}
                    <div class="flex flex-col items-center justify-center py-12 space-y-4 opacity-40">
                        <flux:icon name="arrow-path" class="size-10 animate-spin" />
                        <p class="text-[10px] font-black uppercase tracking-widest">A carregar dados...</p>
                    </div>
                @endif
            </div>
        </div>
    </flux:modal>
{{-- 6. MODAL: HISTÓRICO DE ASSIDUIDADE INDIVIDUAL --}}
    <flux:modal name="attendance-history-modal" position="center" class="md:w-[700px] !p-0 overflow-hidden" wire:ignore.self>
        <div class="p-10 bg-white dark:bg-zinc-950 space-y-8">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4 text-left">
                    <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="clock" class="size-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">
                            Registo de Assiduidade
                        </h2>
                        <p class="text-xs text-zinc-500 font-bold uppercase mt-1 tracking-widest">{{ $attendanceEmployeeName }}</p>
                    </div>
                </div>

                {{-- Filtro de Mês dentro do Modal --}}
                <div class="flex gap-2">
                    <select wire:model.live="selectedMonth" class="bg-zinc-100 dark:bg-zinc-900 border-none rounded-xl text-[10px] font-black uppercase p-2 outline-none">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="glass-card border border-zinc-200 dark:border-zinc-800 rounded-[2rem] overflow-hidden shadow-inner">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800 text-[9px] font-black uppercase text-zinc-400">
                        <tr>
                            <th class="px-6 py-4">Data</th>
                            <th class="px-6 py-4">Entrada</th>
                            <th class="px-6 py-4">Saída</th>
                            <th class="px-6 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                        @forelse($individualLogs as $log)
                            <tr class="text-xs font-bold text-zinc-600 dark:text-zinc-300">
                                <td class="px-6 py-4 uppercase">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M, Y') }}</td>
                                <td class="px-6 py-4 text-emerald-500">{{ \Carbon\Carbon::parse($log->clock_in)->format('H:i') }}</td>
                                <td class="px-6 py-4 text-amber-500">{{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('H:i') : '---' }}</td>
                                <td class="px-6 py-4 text-right font-black dark:text-white">
                                    {{ floor($log->total_minutes / 60) }}h {{ $log->total_minutes % 60 }}min
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-zinc-400 uppercase text-[10px] font-black italic">Sem registos para este período</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <flux:modal.close class="w-full">
                <flux:button variant="ghost" class="w-full font-black uppercase text-[10px]">Fechar Histórico</flux:button>
            </flux:modal.close>

            {{-- 7. MODAL: UPLOAD DE DOCUMENTO (COFRE DIGITAL) --}}

        </div>
    </flux:modal>


    <flux:modal name="upload-doc-modal" position="center" class="md:w-[500px] !p-0 overflow-visible" wire:ignore.self>
        <div class="p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border dark:border-zinc-800 text-left">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-900 rounded-2xl text-white shadow-lg"><flux:icon name="folder-open" class="size-6" /></div>
                <div class="text-left">
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter leading-none text-zinc-900 dark:text-white">Cofre Digital</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium italic mt-2">Carregar documento oficial para o colaborador.</p>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Título do Documento --}}
                <flux:input wire:model="docTitle" label="Nome do Ficheiro" placeholder="Ex: Recibo Vencimento - Julho" class="font-bold" />

                {{-- Tipo de Documento --}}
                <flux:select wire:model="docType" label="Natureza do Documento" class="font-black uppercase text-[10px]">
                    <option value="recibo">📄 Recibo de Vencimento</option>
                    <option value="contrato">📜 Contrato de Trabalho</option>
                    <option value="outro">📁 Outro Documento</option>
                </flux:select>

                {{-- Zona de Upload --}}
                <div class="p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl text-center group hover:border-brand-500 transition-all relative">
                    <input type="file" wire:model="docFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div class="space-y-2">
                        <flux:icon name="cloud-arrow-up" class="size-8 text-zinc-300 mx-auto group-hover:text-brand-500 transition-colors" />
                        @if ($docFile)
                            <p class="text-xs font-black text-emerald-500 uppercase">{{ $docFile->getClientOriginalName() }}</p>
                        @else
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest leading-tight">Clica ou arrasta o PDF aqui <br> <span class="opacity-50">(Máx. 5MB)</span></p>
                        @endif
                    </div>

                    <div wire:loading wire:target="docFile" class="absolute inset-0 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm flex items-center justify-center rounded-3xl">
                        <div class="flex items-center gap-2">
                            <div class="size-4 border-2 border-brand-600 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-[10px] font-black uppercase text-brand-600">A processar...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <flux:modal.close><flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px]">Cancelar</flux:button></flux:modal.close>
                <flux:button wire:click="saveDocument" variant="primary" class="flex-[2] h-14 bg-brand-600 font-black uppercase shadow-xl rounded-2xl border-none">
                    Confirmar Envio
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- MODAL: HISTÓRICO DE DOCUMENTOS --}}
<flux:modal name="view-documents-modal" position="center" class="md:w-[600px] !p-0 overflow-visible" wire:ignore.self>
    <div class="p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border dark:border-zinc-800 text-left">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4 text-left">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-2xl text-zinc-900 dark:text-white shadow-sm">
                    <flux:icon name="folder" class="size-6" />
                </div>
                <div class="text-left">
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter leading-none">Cofre de Documentos</flux:heading>
                    <p class="text-[10px] font-black text-brand-600 uppercase mt-1 tracking-widest">{{ $selectedEmployeeName }}</p>
                </div>
            </div>
            <flux:modal.close>
                <flux:button variant="ghost" icon="x-mark" size="sm" class="rounded-full" />
            </flux:modal.close>
        </div>

        <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">

            {{-- 1. DOCUMENTO MESTRE: CURRÍCULO (CV) --}}
            @if($selectedEmployee && $selectedEmployee->cv_path)
                <div class="group flex items-center justify-between p-4 bg-brand-50/50 dark:bg-brand-500/10 border border-brand-200 dark:border-brand-800 rounded-2xl hover:border-brand-500/30 transition-all border-l-4 border-l-brand-500">
                    <div class="flex items-center gap-4 text-left">
                        <div class="size-10 rounded-xl bg-brand-600 flex items-center justify-center text-white shadow-sm">
                            <flux:icon name="identification" class="size-5" />
                        </div>
                        <div>
                            <p class="text-xs font-black dark:text-white uppercase truncate max-w-[200px]">Curriculum Vitae (CV)</p>
                            <p class="text-[9px] font-black text-brand-600 uppercase tracking-widest italic">Documento de Recrutamento</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $selectedEmployee->cv_path) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center size-8 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-brand-600 hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                            <flux:icon name="cloud-arrow-down" variant="micro" class="size-4" />
                        </a>
                    </div>
                </div>
            @endif

            {{-- 2. RESTANTES DOCUMENTOS (RECIBOS, CONTRATOS, ETC) --}}
            @forelse($employeeDocuments as $doc)
                <div class="group flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800 rounded-2xl hover:border-brand-500/30 transition-all">
                    <div class="flex items-center gap-4 text-left">
                        <div class="size-10 rounded-xl bg-white dark:bg-zinc-800 flex items-center justify-center border border-zinc-100 dark:border-zinc-700 shadow-sm">
                            @if($doc->type === 'recibo')
                                <flux:icon name="document-text" class="size-5 text-emerald-500" />
                            @elseif($doc->type === 'contrato')
                                <flux:icon name="document-check" class="size-5 text-blue-500" />
                            @else
                                <flux:icon name="paper-clip" class="size-5 text-zinc-400" />
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-black dark:text-white uppercase truncate max-w-[200px]">{{ $doc->title }}</p>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                {{ \Carbon\Carbon::parse($doc->created_at)->translatedFormat('d M Y') }} • {{ $doc->file_size }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <flux:button variant="ghost" size="sm" icon="cloud-arrow-down" class="text-zinc-400 hover:text-brand-600" />

                        <button
                            wire:click="deleteDocument({{ $doc->id }})"
                            wire:confirm="Eliminar este documento permanentemente?"
                            class="p-2 text-zinc-300 hover:text-red-500 transition-colors"
                        >
                            <flux:icon name="trash" class="size-4" />
                        </button>
                    </div>
                </div>
            @empty
                @if(!$selectedEmployee?->cv_path) {{-- Só mostra vazio se também não houver CV --}}
                    <div class="py-12 text-center opacity-30">
                        <flux:icon name="folder-open" class="size-12 mx-auto mb-3" />
                        <p class="text-[10px] font-black uppercase tracking-[0.2em]">Nenhum documento arquivado</p>
                    </div>
                @endif
            @endforelse
        </div>

        <div class="pt-4">
            <flux:button wire:click="openUploadModal({{ $selectedEmpIdForUpload }})" variant="primary" class="w-full h-14 font-black uppercase tracking-widest rounded-2xl border-none shadow-xl">
                Carregar Novo Documento
            </flux:button>
        </div>
    </div>
</flux:modal>
{{-- 8. MODAL: EXIBIÇÃO DE CHAVE DE ACESSO --}}
    <flux:modal name="employee-token-modal" position="center" class="md:w-[450px] !p-0 overflow-visible" wire:ignore.self>
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800 text-center">

            {{-- Ícone com Aura --}}
            <div class="flex justify-center">
                <div class="relative">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full scale-150"></div>
                    <div class="relative size-16 bg-zinc-900 rounded-2xl flex items-center justify-center text-white shadow-xl">
                        <flux:icon name="key" variant="solid" class="size-8 text-brand-400" />
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl font-black dark:text-white uppercase italic tracking-tighter leading-none">Chave de Acesso</h2>
                <p class="text-[10px] font-black text-brand-600 uppercase tracking-[0.2em]">{{ $selectedEmployeeName }}</p>
            </div>

            <p class="text-xs text-zinc-500 font-medium leading-relaxed italic px-4">
                Partilha este código com o colaborador. Com ele, poderá aceder ao Portal da Equipa para consultar recibos e assiduidade.
            </p>

            {{-- O CÓDIGO --}}
            <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 shadow-inner relative group">
                <span class="text-3xl font-mono font-black text-zinc-900 dark:text-white tracking-[0.3em] uppercase select-all">
                    {{ $generatedToken }}
                </span>

                <div class="absolute -top-3 right-4">
                    <button x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText('{{ $generatedToken }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="p-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-sm hover:scale-110 transition-all">
                        <flux:icon x-show="!copied" name="clipboard" class="size-3.5 text-zinc-400" />
                        <flux:icon x-show="copied" name="check" class="size-3.5 text-emerald-500" />
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <flux:modal.close class="w-full">
                    <flux:button variant="primary" class="w-full h-14 bg-zinc-950 dark:bg-brand-600 text-white font-black uppercase text-[10px] tracking-widest rounded-2xl shadow-xl border-none">
                        Concluído
                    </flux:button>
                </flux:modal.close>

                <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest opacity-50 italic">
                    Segurança Financeira Finance Pro • Chave Privada
                </p>
            </div>
        </div>
    </flux:modal>
    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-50">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} Protocolo de Gestão de Capital Humano · Todos os direitos reservados
        </p>
    </footer>

</div> {{-- FIM DO x-data GLOBAL --}}
