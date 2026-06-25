<div style="--cat-color: {{ $categoryColor }};">
    {{-- CSS ADICIONAL PARA RESPONSIVIDADE --}}
    <style>
        @media (max-width: 640px) {
            .custom-scrollbar::-webkit-scrollbar { width: 0px; }
            .mobile-card-shadow { shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
            [class*="rounded-[2.5rem]"] { border-radius: 1.5rem !important; }
            [class*="rounded-[3rem]"] { border-radius: 2rem !important; }
        }
        @keyframes scan {
            0% { top: 0%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan-line {
            position: absolute; left: 0; width: 100%; height: 3px; z-index: 40;
            background: linear-gradient(to right, transparent, #10b981, transparent);
            box-shadow: 0 0 20px #10b981; animation: scan 2.5s infinite ease-in-out;
        }
        [x-cloak] { display: none !important; }
    </style>

    {{-- BARRA SUPERIOR: XP E FINANCE CONNECT --}}
    <div class="p-2 sm:p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[1.5rem] sm:rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between w-full px-2 sm:px-4 gap-4">

            {{-- LADO ESQUERDO: PROGRESSÃO DE NÍVEL --}}
            <div class="flex items-center gap-4 sm:gap-8 w-full sm:w-auto justify-between sm:justify-start">
                <div class="hidden md:block border-r border-zinc-100 dark:border-zinc-800 pr-10">
                    <div class="flex items-center gap-3 mb-1.5">
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] italic">Experiência do Perfil</span>
                        <span class="text-[9px] font-black bg-brand-600 text-white px-2.5 py-0.5 rounded-full shadow-lg shadow-brand-500/20">NÍVEL {{ auth()->user()->level }}</span>
                    </div>
                    <div class="w-56 h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-inner">
                        <div class="h-full bg-brand-500 shadow-[0_0_12px_rgba(59,130,246,0.5)] transition-all duration-1000"
                             style="width: {{ (auth()->user()->xp % 1000) / 10 }}%"></div>
                    </div>
                    <p class="text-[9px] font-bold text-zinc-400 mt-1.5 uppercase tracking-tighter italic">Faltam <span class="text-brand-600 font-black">{{ 1000 - (auth()->user()->xp % 1000) }} XP</span></p>
                </div>

                {{-- FINANCE CONNECT (Rede Social) --}}
                @php
                    $unreadSocial = \App\Models\SocialNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
                @endphp
                <a href="/social" wire:navigate class="group flex items-center gap-3 sm:gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 p-2 sm:p-2.5 rounded-[1.2rem] sm:rounded-[1.5rem] transition-all border border-transparent hover:border-indigo-500/20">
                    <div class="relative">
                        <flux:icon name="globe-alt" class="size-6 sm:size-7 text-indigo-500 group-hover:rotate-12 transition-transform" />
                        @if($unreadSocial > 0)
                            <span class="absolute -top-1 -right-1 flex h-3 w-3 rounded-full bg-red-500 border-2 border-white dark:border-zinc-900 animate-pulse"></span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] sm:text-[11px] font-black text-indigo-600 uppercase tracking-widest leading-none italic">Finance Connect</p>
                        <p class="text-[9px] font-bold text-zinc-500 mt-1 sm:mt-1.5 uppercase">
                            {{ $unreadSocial > 0 ? $unreadSocial . ' Alertas' : 'Feed Atualizado' }}
                        </p>
                    </div>
                </a>
            </div>

            {{-- LADO DIREITO: STATUS IA --}}
            <div class="flex items-center gap-4 sm:gap-6 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-none pt-2 sm:pt-0">
                <div class="space-y-0.5 sm:space-y-1 text-left sm:text-right">
                    <p class="text-[9px] font-black text-zinc-400 uppercase italic">Status de Gestão</p>
                    <div class="flex items-center gap-2 sm:gap-3 sm:justify-end">
                        <span class="text-sm sm:text-2xl font-black italic tracking-tighter leading-none {{ $spentThisMonth > $budgetLimit && $budgetLimit > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                            {{ $spentThisMonth > $budgetLimit && $budgetLimit > 0 ? 'LIMITE EXCEDIDO' : 'SOB CONTROLO' }}
                        </span>
                        <div class="size-2 sm:size-2.5 rounded-full {{ $spentThisMonth > $budgetLimit && $budgetLimit > 0 ? 'bg-red-500 shadow-[0_0_10px_#ef4444]' : 'bg-emerald-500 shadow-[0_0_10px_#10b981]' }}"></div>
                    </div>
                </div>
                <div class="p-2 sm:p-4 bg-zinc-950 rounded-xl sm:rounded-[1.5rem] border border-zinc-800 shadow-xl">
                    <flux:icon name="sparkles" class="size-4 sm:size-6 text-brand-400 {{ $spentThisMonth <= $budgetLimit ? 'animate-pulse' : '' }}" />
                </div>
            </div>
        </div>
    </div>

    @php
        $themes = [
            'carro' => ['color' => 'text-amber-500', 'label' => 'Logística de Veículo', 'budgetLabel' => 'Plafond de Manutenção'],
            'casa' => ['color' => 'text-blue-500', 'label' => 'Gestão de Habitação', 'budgetLabel' => 'Gestão de Rendas'],
            'alimentacao' => ['color' => 'text-orange-500', 'label' => 'Controlo de Consumo', 'budgetLabel' => 'Budget de Nutrição'],
            'saude' => ['color' => 'text-red-500', 'label' => 'Bem-estar e Saúde', 'budgetLabel' => 'Reserva Médica'],
            'tecnologia' => ['color' => 'text-indigo-500', 'label' => 'Infraestrutura Digital', 'budgetLabel' => 'Budget SaaS'],
            'educacao' => ['color' => 'text-emerald-500', 'label' => 'Educação e Formação', 'budgetLabel' => 'Investimento Educacional'],
            'emprestimos' => ['color' => 'text-rose-500', 'label' => 'Gestão de Empréstimos', 'budgetLabel' => 'Controlo de Dívida'],
            'seguros' => ['color' => 'text-sky-500', 'label' => 'Proteção e Seguros', 'budgetLabel' => 'Prémios de Seguros'],
        ];

        $hubTheme = $themes[$slug] ?? [
            'color' => 'text-brand-600',
            'label' => 'Gestão Estratégica',
            'budgetLabel' => 'Teto Orçamental'
        ];
    @endphp

    <div
        x-data="{
            scannerOpen: false,
            reviewOpen: false,
            formOpen: false,
            scannerPreview: null,
            uploadingFile: false,
        }"
        x-init="
            if (new URLSearchParams(window.location.search).get('open_scanner')) {
                scannerOpen = true;
                window.history.replaceState({}, '', window.location.pathname);
            }
        "
        x-on:open-add-expense-modal.window="scannerOpen = false; reviewOpen = false; formOpen = true;"
        x-on:modal-close-add-expense.window="formOpen = false"
        x-on:scan-completed.window="scannerOpen = false; setTimeout(() => reviewOpen = true, 250);"
        x-on:open-review-modal.window="scannerOpen = false; setTimeout(() => reviewOpen = true, 250);"
        x-on:expense-saved.window="formOpen = false"
        x-on:keydown.escape.window="scannerOpen = false; reviewOpen = false; formOpen = false;"
        class="space-y-6 sm:space-y-10 pb-24"
    >

    {{-- ── HEADER PRINCIPAL ── --}}
    <div class="relative px-2">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 sm:gap-8 relative z-10 pt-4 sm:pt-6">
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl sm:rounded-[2rem] shadow-2xl">
                   <flux:icon name="{{ $icon }}" class="size-8 sm:size-10" style="color: var(--cat-color);" />
                </div>
                <div>
                    <div class="relative inline-block">
                        <h1 class="text-2xl sm:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">{{ $title }}</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[8px] sm:text-[9px] font-black uppercase tracking-widest border-none px-2 py-0.5">Hub Inteligente</flux:badge>
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-500 font-medium italic mt-1 sm:mt-2">{{ $hubTheme['label'] }} · <span class="text-brand-600 font-bold uppercase tracking-tighter">{{ $currentWs->name }}</span></p>
                </div>
            </div>

            @if($canManage)
            <div class="grid grid-cols-2 sm:flex items-center gap-3 w-full sm:w-auto bg-white dark:bg-zinc-900 p-2 sm:p-3 rounded-2xl sm:rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <button type="button" @click="scannerPreview = null; scannerOpen = true"
                    class="flex items-center justify-center gap-2 px-3 h-10 sm:h-11 rounded-xl sm:rounded-2xl text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-500/10 font-black uppercase text-[10px] sm:text-sm transition-colors">
                    <flux:icon name="sparkles" class="size-4" />
                    Scanner IA
                </button>

                <button type="button" wire:click="openCreateModal"
                    class="flex items-center justify-center gap-2 px-4 h-10 sm:h-11 rounded-xl sm:rounded-2xl text-white font-black uppercase text-[10px] sm:text-sm shadow-lg transition-all hover:scale-[1.02]"
                    style="background-color: var(--cat-color);">
                    <flux:icon name="plus" class="size-4" />
                    Novo Registo
                </button>

                <div class="hidden sm:block h-8 w-px bg-zinc-200 dark:border-zinc-800"></div>
                <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate class="hidden sm:flex rounded-xl" />
            </div>
            @endif
        </header>
    </div>

    {{-- ── PAINEL ORÇAMENTAL ── --}}
    <div class="relative overflow-hidden bg-zinc-950 p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] shadow-2xl border border-zinc-800 group mx-2">
        <div class="absolute -right-20 -top-20 size-60 sm:size-80 {{ str_replace('text','bg',$hubTheme['color']) }}/10 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 sm:gap-10">
            <div class="space-y-2">
                <p class="text-[9px] sm:text-[10px] font-black text-brand-400 uppercase tracking-[0.3em] mb-2 sm:mb-4 italic">{{ $hubTheme['budgetLabel'] }} ({{ now()->translatedFormat('F') }})</p>
                <div class="flex flex-wrap items-baseline gap-3 sm:gap-4">
                    <h3 class="text-4xl sm:text-6xl font-black text-white tracking-tighter italic leading-none">{{ number_format($spentThisMonth,2,',',' ') }}€</h3>
                    <span class="text-xl sm:text-2xl font-black text-zinc-600 uppercase tracking-tighter">/</span>

                    @if($editingBudget && $isOwner)
                        <input type="number" wire:model="budgetLimit" wire:keydown.enter="updateBudget" wire:blur="updateBudget" autofocus
                               class="w-32 sm:w-44 bg-white/5 border border-white/10 rounded-xl sm:rounded-2xl px-3 sm:px-4 py-1 {{ $hubTheme['color'] }} font-black text-2xl sm:text-4xl outline-none shadow-inner">
                    @else
                        <button @if($isOwner) wire:click="$set('editingBudget', true)" @endif class="group/btn flex items-center gap-2 sm:gap-3 outline-none">
                            <span class="text-2xl sm:text-4xl font-black {{ $budgetLimit > 0 ? 'text-zinc-500' : 'text-zinc-700 animate-pulse' }} tracking-tighter italic uppercase">
                                {{ $budgetLimit > 0 ? number_format($budgetLimit,0).'€' : 'Definir' }}
                            </span>
                            @if($isOwner)<flux:icon name="pencil" class="size-3 sm:size-4 text-zinc-600 group-hover/btn:{{ $hubTheme['color'] }} transition-colors" />@endif
                        </button>
                    @endif
                </div>
            </div>

            @if($budgetLimit > 0)
                @php $perc = ($spentThisMonth / $budgetLimit) * 100; @endphp
                <div class="text-left sm:text-right border-t border-white/5 pt-4 sm:border-none sm:pt-0">
                    <p class="text-[9px] sm:text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Eficiência de Consumo</p>
                    <p class="text-3xl sm:text-5xl font-black {{ $perc >= 100 ? 'text-red-500' : ($perc >= 80 ? 'text-orange-500' : 'text-emerald-500') }} tracking-tighter italic">{{ round($perc) }}%</p>
                </div>
            @endif
        </div>

        @if($budgetLimit > 0)
        <div class="mt-6 sm:mt-10 h-2 sm:h-2.5 w-full bg-white/5 rounded-full overflow-hidden p-0.5 border border-white/5 shadow-inner">
            <div class="h-full rounded-full transition-all duration-1000 ease-out"
                 style="width: {{ min($perc,100) }}%;
                        background-color: {{ $perc >= 100 ? '#ef4444' : 'var(--cat-color)' }};
                        box-shadow: 0 0 15px {{ $perc >= 100 ? '#ef4444' : 'var(--cat-color)' }};">
            </div>
        </div>
        @endif
    </div>

    {{-- ── WIDGETS DINÂMICOS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 px-2">

        {{-- CARRO: Estatísticas Veículo --}}
        @if($slug === 'carro')
            @php
                $totalGasto = $expenses->sum('amount');
                $mediaGasto = $expenses->count() > 0 ? $totalGasto / $expenses->count() : 0;
                $ultimoGasto = $expenses->first();

                $proximaInspecaoExpense = $expenses->first(function ($e) {
                    $m = is_array($e->metadata) ? $e->metadata : (json_decode($e->metadata, true) ?? []);
                    return !empty($m['proxima_inspecao']);
                });
                $proximaInspecaoMeta = $proximaInspecaoExpense
                    ? (is_array($proximaInspecaoExpense->metadata) ? $proximaInspecaoExpense->metadata : json_decode($proximaInspecaoExpense->metadata, true))
                    : null;

                $expensesComLitros = $expenses->filter(function ($e) {
                    $m = is_array($e->metadata) ? $e->metadata : (json_decode($e->metadata, true) ?? []);
                    return !empty($m['litros']);
                });
                $custoPorLitro = $expensesComLitros->count() > 0
                    ? $expensesComLitros->sum(function ($e) {
                        $m = is_array($e->metadata) ? $e->metadata : json_decode($e->metadata, true);
                        return $m['litros'] > 0 ? $e->amount / $m['litros'] : 0;
                    }) / $expensesComLitros->count()
                    : 0;
            @endphp

            <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/20 rounded-2xl p-4 sm:p-6 border border-amber-200 dark:border-amber-800/30 shadow-md">
                <div class="flex items-center justify-between mb-2 sm:mb-4">
                    <flux:icon name="bolt" class="size-6 sm:size-8 text-amber-600" />
                    <span class="hidden sm:block text-[8px] font-black text-amber-700 dark:text-amber-400 bg-amber-200/50 dark:bg-amber-950/50 px-2 py-1 rounded uppercase">Consumo</span>
                </div>
                <p class="text-[9px] text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider mb-1">Média Serviço</p>
                <p class="text-xl sm:text-3xl font-black text-amber-700 dark:text-amber-300 tracking-tighter">{{ number_format($mediaGasto, 1, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-950/20 dark:to-yellow-950/20 rounded-2xl p-4 sm:p-6 border border-amber-200 dark:border-amber-800/30 shadow-md">
                <div class="flex items-center justify-between mb-2 sm:mb-4">
                    <flux:icon name="bars-3" class="size-6 sm:size-8 text-yellow-600" />
                </div>
                <p class="text-[9px] text-yellow-600 dark:text-yellow-400 font-bold uppercase tracking-wider mb-1">Manutenções</p>
                <p class="text-xl sm:text-3xl font-black text-yellow-700 dark:text-yellow-300 tracking-tighter">{{ $expenses->count() }}</p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-red-50 dark:from-amber-950/20 dark:to-red-950/20 rounded-2xl p-4 sm:p-6 border border-amber-200 dark:border-amber-800/30 shadow-md">
                <div class="flex items-center justify-between mb-2 sm:mb-4">
                    <flux:icon name="calendar" class="size-6 sm:size-8 text-red-600" />
                </div>
                <p class="text-[9px] text-red-600 dark:text-red-400 font-bold uppercase tracking-wider mb-1">Último Registo</p>
                <p class="text-lg sm:text-2xl font-black text-red-700 dark:text-red-300 tracking-tighter">{{ $ultimoGasto ? $ultimoGasto->spent_at->format('d/m') : 'N/A' }}</p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-green-50 dark:from-amber-950/20 dark:to-green-950/20 rounded-2xl p-4 sm:p-6 border border-amber-200 dark:border-amber-800/30 shadow-md">
                <div class="flex items-center justify-between mb-2 sm:mb-4">
                    <flux:icon name="star" class="size-6 sm:size-8 text-green-600" />
                </div>
                <p class="text-[9px] text-green-600 dark:text-green-400 font-bold uppercase tracking-wider mb-1">Próx. Inspeção</p>
                <p class="text-base sm:text-2xl font-black text-green-700 dark:text-green-300 tracking-tighter italic">
                    {{ $proximaInspecaoMeta['proxima_inspecao'] ?? null ? \Carbon\Carbon::parse($proximaInspecaoMeta['proxima_inspecao'])->format('m/Y') : 'N/A' }}
                </p>
            </div>

        {{-- CASA: Comparativo e Utilidades --}}
        @elseif($slug === 'casa')
            @php
                $totalGasto = $expenses->sum('amount');
                $mesAnterior = $expenses->where('spent_at', '<', now()->startOfMonth())->sum('amount');
                $economiaOuGasto = $totalGasto - $mesAnterior;
                $percentualChange = $mesAnterior > 0 ? (($economiaOuGasto / $mesAnterior) * 100) : 0;
            @endphp

            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-950/20 dark:to-cyan-950/20 rounded-2xl p-4 sm:p-6 border border-blue-200 dark:border-blue-800/30 shadow-md">
                <p class="text-[9px] text-blue-600 dark:text-blue-400 font-bold uppercase mb-1">Custo Mensal</p>
                <p class="text-xl sm:text-3xl font-black text-blue-700 dark:text-blue-300 tracking-tighter">{{ number_format($totalGasto, 0, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-violet-50 dark:from-blue-950/20 dark:to-violet-950/20 rounded-2xl p-4 sm:p-6 border border-blue-200 dark:border-blue-800/30 shadow-md">
                <p class="text-[9px] text-violet-600 dark:text-violet-400 font-bold uppercase mb-1">Variação %</p>
                <p class="text-xl sm:text-3xl font-black {{ $percentualChange < 0 ? 'text-emerald-500' : 'text-red-500' }} tracking-tighter">
                    {{ $percentualChange > 0 ? '+' : '' }}{{ round($percentualChange) }}%
                </p>
            </div>

        {{-- ALIMENTAÇÃO --}}
        @elseif($slug === 'alimentacao')
             @php
                $totalGasto = $expenses->sum('amount');
                $expensesWithPeople = $expenses->filter(function ($e) {
                    $m = is_array($e->metadata) ? $e->metadata : (json_decode($e->metadata, true) ?? []);
                    return isset($m['pessoas']) && $m['pessoas'] > 0;
                });
                $custoPorPessoa = $expensesWithPeople->count() > 0
                    ? $expensesWithPeople->sum(fn($e) => $e->amount / (json_decode($e->metadata,true)['pessoas'])) / $expensesWithPeople->count()
                    : 0;
            @endphp


















            <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-950/20 dark:to-red-950/20 rounded-2xl p-4 sm:p-6 border border-orange-200 dark:border-orange-800/30 shadow-md">
                <p class="text-[9px] text-orange-600 dark:text-orange-400 font-bold uppercase mb-1">Média p/ Pessoa</p>
                <p class="text-xl sm:text-3xl font-black text-orange-700 dark:text-orange-300 tracking-tighter">{{ number_format($custoPorPessoa, 2, ',', '.') }}€</p>
            </div>

        {{-- TECNOLOGIA --}}
        @elseif($slug === 'tecnologia')
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-950/20 dark:to-blue-950/20 rounded-2xl p-4 sm:p-6 border border-indigo-200 dark:border-indigo-800/30 shadow-md">
                <p class="text-[9px] text-indigo-600 dark:text-indigo-400 font-bold uppercase mb-1">Burn Rate SaaS</p>
                <p class="text-xl sm:text-3xl font-black text-indigo-700 dark:text-indigo-300 tracking-tighter">{{ number_format($spentThisMonth, 2, ',', '.') }}€</p>
            </div>
        @endif

    </div>{{-- /grid widgets --}}

    {{-- ── WIDGETS ADICIONAIS (Continuação) ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 px-2 mt-4">

        {{-- EDUCAÇÃO: Análise Educacional --}}
        @if($slug === 'educacao')
            @php
                $totalGasto = $expenses->sum('amount');
                $expensesAtivas = $expenses->count();
                $comCertificacao = $expenses->filter(function ($e) {
                    $m = is_array($e->metadata) ? $e->metadata : (json_decode($e->metadata, true) ?? []);
                    return !empty($m['certificacao']);
                })->count();
            @endphp
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/20 dark:to-teal-950/20 rounded-2xl p-4 sm:p-6 border border-emerald-200 dark:border-emerald-800/30 shadow-md">
                <p class="text-[9px] text-emerald-600 dark:text-emerald-400 font-bold uppercase mb-1">Total Investido</p>
                <p class="text-xl sm:text-3xl font-black text-emerald-700 dark:text-emerald-300 tracking-tighter">{{ number_format($totalGasto, 0, ',', '.') }}€</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-50 to-amber-50 dark:from-emerald-950/20 dark:to-amber-950/20 rounded-2xl p-4 sm:p-6 border border-emerald-200 dark:border-emerald-800/30 shadow-md">
                <p class="text-[9px] text-amber-600 dark:text-amber-400 font-bold uppercase mb-1">Certificações</p>
                <p class="text-xl sm:text-3xl font-black text-amber-700 dark:text-amber-300 tracking-tighter">{{ $comCertificacao }}</p>
            </div>

        {{-- EMPRÉSTIMOS: Análise de Dívida --}}
        @elseif($slug === 'emprestimos')
            @php
                $saldoTotalAtual = 0;
                $valorInicialTotal = 0;
                $taxaMediaJuros = 0;
                $countComTaxa = 0;
                foreach ($expenses as $e) {
                    $m = is_array($e->metadata) ? $e->metadata : (json_decode($e->metadata, true) ?? []);
                    if (!empty($m['saldo_atual'])) $saldoTotalAtual += $m['saldo_atual'];
                    if (!empty($m['valor_inicial'])) $valorInicialTotal += $m['valor_inicial'];
                    if (!empty($m['taxa_juros'])) { $taxaMediaJuros += $m['taxa_juros']; $countComTaxa++; }
                }
                $taxaMediaJuros = $countComTaxa > 0 ? $taxaMediaJuros / $countComTaxa : 0;
                $jaPago = $valorInicialTotal > 0 ? round((($valorInicialTotal - $saldoTotalAtual) / $valorInicialTotal) * 100) : 0;
            @endphp
            <div class="bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-950/20 dark:to-red-950/20 rounded-2xl p-4 sm:p-6 border border-rose-200 dark:border-rose-800/30 shadow-md">
                <p class="text-[9px] text-rose-600 dark:text-rose-400 font-bold uppercase mb-1">Saldo Devedor</p>
                <p class="text-xl sm:text-3xl font-black text-rose-700 dark:text-rose-300 tracking-tighter">{{ number_format($saldoTotalAtual, 0, ',', '.') }}€</p>
            </div>
            <div class="bg-gradient-to-br from-rose-50 to-emerald-50 dark:from-rose-950/20 dark:to-emerald-950/20 rounded-2xl p-4 sm:p-6 border border-rose-200 dark:border-rose-800/30 shadow-md">
                <p class="text-[9px] text-emerald-600 dark:text-emerald-400 font-bold uppercase mb-1">Amortizado</p>
                <p class="text-xl sm:text-3xl font-black text-emerald-700 dark:text-emerald-300 tracking-tighter">{{ $jaPago }}%</p>
            </div>

        {{-- SEGUROS: Análise de Cobertura --}}
        @elseif($slug === 'seguros')
            @php
                $coverturaTotal = 0;
                foreach ($expenses as $e) {
                    $m = is_array($e->metadata) ? $e->metadata : (json_decode($e->metadata, true) ?? []);
                    if (!empty($m['cobertura_valor'])) $coverturaTotal += $m['cobertura_valor'];
                }
                $proximaRenovacaoSeguro = $expenses->filter(function ($e) {
                    $m = is_array($e->metadata) ? $e->metadata : (json_decode($e->metadata, true) ?? []);
                    return !empty($m['data_renovacao']) && \Carbon\Carbon::parse($m['data_renovacao'])->isFuture();
                })->sortBy(fn($e) => json_decode($e->metadata, true)['data_renovacao'] ?? null)->first();
            @endphp
            <div class="bg-gradient-to-br from-sky-50 to-blue-50 dark:from-sky-950/20 dark:to-blue-950/20 rounded-2xl p-4 sm:p-6 border border-sky-200 dark:border-sky-800/30 shadow-md">
                <p class="text-[9px] text-sky-600 dark:text-sky-400 font-bold uppercase mb-1">Proteção Total</p>
                <p class="text-xl sm:text-3xl font-black text-sky-700 dark:text-sky-300 tracking-tighter">{{ number_format($coverturaTotal, 0, ',', '.') }}€</p>
            </div>
            <div class="bg-gradient-to-br from-sky-50 to-amber-50 dark:from-sky-950/20 dark:to-amber-950/20 rounded-2xl p-4 sm:p-6 border border-sky-200 dark:border-sky-800/30 shadow-md">
                <p class="text-[9px] text-amber-600 dark:text-amber-400 font-bold uppercase mb-1">Próx. Renovação</p>
                <p class="text-lg sm:text-2xl font-black text-amber-700 dark:text-amber-300">
                    {{ $proximaRenovacaoSeguro ? \Carbon\Carbon::parse(json_decode($proximaRenovacaoSeguro->metadata, true)['data_renovacao'])->format('d/m') : 'N/A' }}
                </p>
            </div>
        @endif
    </div>

    {{-- ── SECÇÃO DE MOVIMENTOS ── --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[1.5rem] sm:rounded-[2.5rem] shadow-sm overflow-hidden mx-2 mt-8">
        <div class="px-4 sm:px-8 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-950/20">
            <p class="text-[10px] sm:text-sm font-black dark:text-white uppercase italic tracking-tight">Histórico Detalhado: {{ $title }}</p>
            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase px-2 py-0.5 sm:px-3 sm:py-1 border-none">{{ $expenses->count() }} Registos</flux:badge>
        </div>

        {{-- VISTA DESKTOP: TABELA (Aparece apenas em ecrãs médios/grandes) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-[0.2em]">
                        <th class="p-6 w-28">Data</th>
                        <th class="p-6">Tipo</th>
                        <th class="p-6">Detalhes Específicos</th>
                        <th class="p-6">Observações</th>
                        <th class="p-6 text-right px-8 w-32">Montante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row">
                        <td class="p-6 align-top whitespace-nowrap">
                            <span class="text-lg font-black dark:text-white leading-none tracking-tighter block">{{ $expense->spent_at->format('d') }}</span>
                            <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest mt-1.5 block">{{ $expense->spent_at->translatedFormat('M') }}</span>
                        </td>
                        <td class="p-6 align-top">
                            <span class="text-[9px] w-fit font-black {{ $hubTheme['color'] }} uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 px-3 py-1.5 rounded-lg inline-block">{{ $expense->subcategory }}</span>
                        </td>
                        <td class="p-6 align-top">
                            <div class="flex flex-col gap-2.5">
                                @php $meta = is_array($expense->metadata) ? $expense->metadata : (json_decode($expense->metadata, true) ?? []); @endphp

                                {{-- METADADOS DESKTOP: CARRO --}}
                                @if($slug === 'carro' && !empty($meta))
                                    @if(!empty($meta['km']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ number_format($meta['km']) }}km</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['urgencia']))
                                        <div class="text-xs font-semibold {{ $meta['urgencia'] === 'Urgente' ? 'text-red-600 dark:text-red-400' : ($meta['urgencia'] === 'Preventiva' ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-600 dark:text-zinc-400') }}">
                                            {{ $meta['urgencia'] }}
                                        </div>
                                    @endif
                                    @if(!empty($meta['local']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">📍 {{ $meta['local'] }}</div>
                                    @endif
                                    @if(!empty($meta['litros']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">🛢️ {{ $meta['litros'] }}L abastecidos</div>
                                    @endif
                                    @if(!empty($meta['proxima_inspecao']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">🗓️ Inspeção: {{ \Carbon\Carbon::parse($meta['proxima_inspecao'])->format('d/m/Y') }}</div>
                                    @endif
                                    @if(!empty($meta['oficina_confianca']))
                                        <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">✓ Oficina de confiança</div>
                                    @endif

                                {{-- METADADOS DESKTOP: CASA --}}
                                @elseif($slug === 'casa' && !empty($meta))
                                    @if(!empty($meta['entidade']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $meta['entidade'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['consumo']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">📊 {{ $meta['consumo'] }} kWh/m³</div>
                                    @endif
                                    @if(!empty($meta['data_vencimento']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">🗓️ Venc.: {{ \Carbon\Carbon::parse($meta['data_vencimento'])->format('d/m/Y') }}</div>
                                    @endif

                                    {{-- ALIMENTAÇÃO --}}
                                @elseif($slug === 'alimentacao' && !empty($meta))
                                    @if(!empty($meta['pessoas']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                            @php $custoPessoa = $meta['pessoas'] > 0 ? $expense->amount / $meta['pessoas'] : $expense->amount; @endphp
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ number_format($custoPessoa, 2, ',', '.') }}€/pessoa ({{ $meta['pessoas'] }})</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['estabelecimento']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">🏪 {{ $meta['estabelecimento'] }}</div>
                                    @endif
                                    @if(!empty($meta['saudavel']))
                                        <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">✓ Opção saudável</div>
                                    @endif

                                {{-- SAÚDE --}}
                                @elseif($slug === 'saude' && !empty($meta))
                                    @if(!empty($meta['profissional']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">👨‍⚕️ {{ $meta['profissional'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['cobertura_seguro']))
                                        <div class="text-xs font-semibold {{ strpos($meta['cobertura_seguro'], 'Sim') !== false ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }}">
                                            🛡️ {{ $meta['cobertura_seguro'] }}
                                        </div>
                                    @endif
                                    @if(!empty($meta['proxima_consulta']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">🗓️ Próx.: {{ \Carbon\Carbon::parse($meta['proxima_consulta'])->format('d/m/Y') }}</div>
                                    @endif

                                {{-- TECNOLOGIA --}}
                                @elseif($slug === 'tecnologia' && !empty($meta))
                                    @if(!empty($meta['fornecedor']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">💼 {{ $meta['fornecedor'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['status']))
                                        <div class="text-xs font-semibold {{ $meta['status'] === 'Ativo' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            Status: {{ $meta['status'] }}
                                        </div>
                                    @endif
                                    @if(!empty($meta['proxima_renovacao']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">🗓️ Renova: {{ \Carbon\Carbon::parse($meta['proxima_renovacao'])->format('d/m/Y') }}</div>
                                    @endif

                                {{-- EDUCAÇÃO --}}
                                @elseif($slug === 'educacao' && !empty($meta))
                                    @if(!empty($meta['instituicao']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">🎓 {{ $meta['instituicao'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['certificacao']))
                                        <div class="text-xs text-amber-600 dark:text-amber-400 font-semibold">🏅 Com certificação</div>
                                    @endif

                                {{-- EMPRÉSTIMOS --}}
                                @elseif($slug === 'emprestimos' && !empty($meta))
                                    @if(!empty($meta['credor']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">🏦 {{ $meta['credor'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['saldo_atual']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">Saldo: {{ number_format($meta['saldo_atual'], 2, ',', '.') }}€</div>
                                    @endif

                                {{-- SEGUROS --}}
                                @elseif($slug === 'seguros' && !empty($meta))
                                    @if(!empty($meta['tipo_seguro']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">🛡️ {{ $meta['tipo_seguro'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['cobertura_valor']))
                                        <div class="text-xs font-semibold text-sky-600 dark:text-sky-400">💰 Cobertura: {{ number_format($meta['cobertura_valor'], 0, ',', '.') }}€</div>
                                    @endif
                                @else
                                    @if(!empty($meta))
                                        <div class="text-xs text-zinc-500 italic">{{ count($meta) }} campo(s) registado(s)</div>
                                    @else
                                        <div class="text-xs text-zinc-400 italic">—</div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="p-6 align-top">
                            @if($expense->description)
                            <div class="relative pl-4 py-1">
                                <div class="absolute left-0 top-0 bottom-0 w-0.5 {{ str_replace('text','bg',$hubTheme['color']) }}/40 rounded-full"></div>
                                <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300 italic line-clamp-2">"{{ $expense->description }}"</p>
                            </div>
                            @else
                            <span class="text-xs text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="p-6 text-right px-8 align-middle whitespace-nowrap">
                            <span class="text-sm font-black text-red-500 tracking-wide italic block">-{{ number_format($expense->amount,2,',',' ') }}€</span>

                            <div class="mt-3 flex justify-end gap-2">
                                <button wire:click="editExpense({{ $expense->id }})" class="p-1.5 text-zinc-300 hover:text-brand-500 opacity-0 group-hover/row:opacity-100 transition-all">
                                    <flux:icon name="pencil-square" variant="mini" class="size-4" />
                                </button>
                                <button wire:click="deleteExpense({{ $expense->id }})" wire:confirm="Tem a certeza?" class="p-1.5 text-zinc-300 hover:text-red-500 opacity-0 group-hover/row:opacity-100 transition-all">
                                    <flux:icon name="trash" variant="mini" class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-32 text-center text-zinc-400 italic font-medium">Sem movimentos registados neste Hub.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div> {{-- Fim da Tabela Desktop --}}

        {{-- VISTA MOBILE: CARDS (Aparece apenas em telemóveis) --}}
        <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-800">
            @forelse($expenses as $expense)
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            {{-- BLOCO DE DATA --}}
                            <div class="text-center bg-zinc-100 dark:bg-zinc-800 p-2 rounded-xl min-w-[50px]">
                                <span class="text-sm font-black dark:text-white block leading-none">{{ $expense->spent_at->format('d') }}</span>
                                <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest mt-1 block">{{ $expense->spent_at->translatedFormat('M') }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-black {{ $hubTheme['color'] }} uppercase tracking-widest bg-zinc-50 dark:bg-zinc-800 px-2 py-1 rounded-md">{{ $expense->subcategory }}</span>
                                @if($expense->description)
                                    <p class="text-[11px] text-zinc-500 italic mt-1 line-clamp-1 max-w-[150px]">"{{ $expense->description }}"</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-red-500 italic block">-{{ number_format($expense->amount,2,',',' ') }}€</span>
                            <div class="flex justify-end gap-1 mt-2">
                                <button wire:click="editExpense({{ $expense->id }})" class="p-2 text-zinc-400 hover:text-brand-500 active:scale-90 transition-transform">
                                    <flux:icon name="pencil-square" variant="mini" class="size-4" />
                                </button>
                                <button wire:click="deleteExpense({{ $expense->id }})" wire:confirm="Apagar registo?" class="p-2 text-zinc-400 hover:text-red-500 active:scale-90 transition-transform">
                                    <flux:icon name="trash" variant="mini" class="size-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-zinc-400 italic text-xs">Sem movimentos registados.</div>
            @endforelse
        </div>
    </div> {{-- FIM DO CONTAINER DO HISTÓRICO --}}

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1 — SCANNER IA                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}

    {{-- Backdrop --}}
    <div x-show="scannerOpen" x-cloak
        x-transition:enter="transition-opacity ease-out duration-75"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-75"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="scannerOpen = false"
        class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm"></div>

    {{-- Panel --}}
    <div x-show="scannerOpen" x-cloak @click.self="scannerOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
        <div @click.stop x-show="scannerOpen"
            x-transition:enter="transition ease-out duration-100 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-lg bg-zinc-950 rounded-[1.8rem] sm:rounded-[2.5rem] border border-white/10 shadow-2xl max-h-[95vh] flex flex-col overflow-hidden">

            {{-- Loader Animado --}}
            <div wire:loading wire:target="scanReceiptWithAI"
                 class="absolute inset-0 bg-zinc-950/95 backdrop-blur-md z-50 flex flex-col items-center justify-center rounded-[1.8rem] sm:rounded-[2.5rem] gap-6">
                <div class="relative">
                    <div class="size-16 sm:size-20 border-4 border-brand-500/20 border-t-brand-500 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <flux:icon name="sparkles" class="size-6 sm:size-7 text-brand-400 animate-pulse" />
                    </div>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-brand-400 font-black uppercase tracking-[0.4em] text-[10px]">IA a processar...</p>
                    <p class="text-zinc-600 text-[9px] uppercase tracking-widest">Extração Vision em curso</p>
                </div>
            </div>

            <div class="absolute -top-24 -left-24 size-64 bg-brand-500/10 blur-[100px] rounded-full animate-pulse pointer-events-none"></div>

            <div class="p-6 sm:p-10 space-y-6 sm:space-y-8 overflow-y-auto custom-scrollbar">

                {{-- Cabeçalho do Modal --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 sm:p-3 bg-brand-500/10 rounded-xl sm:rounded-2xl text-brand-400">
                            <flux:icon name="sparkles" class="size-5 sm:size-6" />
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-white uppercase italic tracking-tighter leading-none">Scanner IA Vision</h2>
                            <p class="text-[8px] sm:text-[9px] text-zinc-500 font-bold uppercase tracking-widest italic mt-1">Processamento de Faturas</p>
                        </div>
                    </div>
                    <button type="button" @click="scannerOpen = false" class="rounded-full p-2 hover:bg-white/5 text-zinc-500 transition-colors">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                {{-- Erro do Scan --}}
                @if($scanError)
                <div class="flex items-start gap-3 bg-red-500/10 border border-red-500/20 rounded-2xl px-4 py-3">
                    <flux:icon name="exclamation-triangle" class="size-4 text-red-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-red-300 font-medium">{{ $scanError }}</p>
                </div>
                @endif

                {{-- Dropzone / Upload --}}
                <div class="relative bg-zinc-900/50 rounded-[1.5rem] sm:rounded-[2.5rem] p-6 sm:p-10 border-2 border-dashed border-zinc-800 hover:border-brand-500/50 transition-all text-center">
                   <input
                        id="receiptInput"
                        type="file"
                        accept="image/*"
                        wire:model="receipt"
                        class="block w-full text-xs text-zinc-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer"
                    >

                    <div class="mt-4">
                        @if ($receipt && !$errors->has('receipt'))
                            <div class="relative inline-block">
                                <img src="{{ $receipt->temporaryUrl() }}" class="size-32 sm:size-44 object-cover rounded-2xl sm:rounded-3xl border-4 border-brand-500/50 shadow-2xl mx-auto">
                                <div class="animate-scan-line"></div>
                            </div>
                        @endif

                        @error('receipt') <p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p> @enderror

                        <div wire:loading wire:target="receipt" class="mt-4">
                            <div class="flex items-center justify-center gap-3">
                                <div class="size-4 border-2 border-brand-500/20 border-t-brand-500 rounded-full animate-spin"></div>
                                <p class="text-[9px] font-black text-brand-400 uppercase tracking-widest">A carregar...</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botões de Ação --}}
                <div class="flex gap-3 sm:gap-4 pb-2">
                    <button type="button" @click="scannerOpen = false; scannerPreview = null"
                        class="flex-1 h-12 sm:h-14 text-zinc-500 font-bold uppercase text-[10px] sm:text-xs tracking-widest">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="scanReceiptWithAI"
                        wire:loading.attr="disabled"
                        wire:target="receipt, scanReceiptWithAI"
                        class="flex-[2] h-12 sm:h-14 rounded-xl sm:rounded-2xl bg-brand-600 text-white font-black uppercase shadow-xl disabled:opacity-50 transition-all text-xs sm:text-sm"
                    >
                        <span wire:loading.remove wire:target="receipt, scanReceiptWithAI">Iniciar Extração</span>
                        <span wire:loading wire:target="receipt">Aguarde...</span>
                        <span wire:loading wire:target="scanReceiptWithAI">A ler com IA...</span>
                    </button>
                </div>

            </div>{{-- /scrollable content --}}
        </div>{{-- /panel scanner --}}
    </div>{{-- /modal 1 --}}

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2 — REVISÃO IA                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}

    {{-- Backdrop --}}
    <div x-show="reviewOpen" x-cloak
        x-transition:enter="transition-opacity ease-out duration-75"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-75"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="reviewOpen = false"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-md"></div>

    {{-- Panel --}}
    <div x-show="reviewOpen" x-cloak @click.self="reviewOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
        <div @click.stop x-show="reviewOpen"
            x-transition:enter="transition ease-out duration-100 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-2xl bg-zinc-950 rounded-[1.5rem] sm:rounded-[2.5rem] shadow-2xl border border-white/10 max-h-[92vh] flex flex-col overflow-hidden">

            <div class="absolute -top-32 -right-32 size-64 sm:size-96 bg-brand-500/10 blur-[120px] rounded-full pointer-events-none"></div>

            {{-- Header Revisão --}}
            <div class="relative p-5 sm:p-8 border-b border-white/5 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                        <flux:icon name="sparkles" class="size-5 sm:size-6 text-emerald-400" />
                    </div>
                    <div>
                        <h2 class="text-base sm:text-xl font-black text-white uppercase italic tracking-tighter leading-none">Revisão IA</h2>
                        <p class="text-[8px] sm:text-[10px] text-zinc-500 mt-1 font-bold uppercase tracking-widest">Confirme os dados extraídos</p>
                    </div>
                </div>
                <button type="button" @click="reviewOpen = false" class="p-2 rounded-full hover:bg-white/5 text-zinc-500 transition-colors">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>

            @if(!empty($scannedData))
            <div class="p-5 sm:p-8 space-y-4 sm:space-y-6 overflow-y-auto custom-scrollbar flex-1">

                {{-- Valor Extraído (Ajustado para Mobile) --}}
                <div class="relative overflow-hidden bg-white/5 border border-white/10 rounded-[1.5rem] sm:rounded-[2rem] p-6 sm:p-8 text-center">
                    <p class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.3em] mb-2">Total Extraído</p>
                    <p class="text-4xl sm:text-7xl font-black text-white tracking-tighter italic leading-none">
                        {{ number_format($scannedData['amount'] ?? 0, 2, ',', '.') }}€
                    </p>
                </div>

                {{-- Detalhes em Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Estabelecimento --}}
                    <div class="sm:col-span-2 bg-white/5 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-brand-500/10 text-brand-400">
                            <flux:icon name="building-storefront" class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[8px] font-black text-zinc-500 uppercase">Local/Loja</p>
                            <p class="text-sm font-black text-white truncate">{{ $scannedData['store'] ?: 'Não detetado' }}</p>
                        </div>
                    </div>

                    {{-- Data --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-zinc-800 text-zinc-400">
                            <flux:icon name="calendar" class="size-5" />
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-zinc-500 uppercase">Data</p>
                            <p class="text-sm font-black text-white">
                                {{ !empty($scannedData['date']) ? \Carbon\Carbon::parse($scannedData['date'])->format('d/m/Y') : '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Subcategoria IA --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-zinc-800 text-zinc-400">
                            <flux:icon name="tag" class="size-5" />
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-zinc-500 uppercase">Classificação</p>
                            <p class="text-sm font-black {{ $hubTheme['color'] }} uppercase">{{ $scannedData['subcategory'] ?? 'Geral' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Campos Específicos Detetados (Meta) ── --}}
                @if(!empty($scannedData['meta']) && is_array($scannedData['meta']))
                <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-white/5 flex items-center gap-2 bg-white/5">
                        <div class="size-1.5 rounded-full {{ str_replace('text','bg',$hubTheme['color']) }}"></div>
                        <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Atributos Inteligentes</p>
                    </div>
                    <div class="divide-y divide-white/5">
                        @foreach($scannedData['meta'] as $key => $value)
                            <div class="px-4 py-3 flex items-center justify-between gap-4">
                                <p class="text-xs text-zinc-400 font-medium lowercase first-letter:uppercase">{{ str_replace('_', ' ', $key) }}</p>
                                <span class="text-xs font-black text-white">
                                    {{ is_bool($value) ? ($value ? 'Sim' : 'Não') : $value }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Itens Detetados na Fatura ── --}}
                @if(!empty($scannedData['items']) && count($scannedData['items']) > 0)
                <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-white/5 flex items-center gap-2 bg-white/5">
                        <div class="size-1.5 rounded-full bg-brand-500"></div>
                        <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Produtos / Serviços ({{ count($scannedData['items']) }})</p>
                    </div>
                    <div class="divide-y divide-white/5 max-h-48 overflow-y-auto custom-scrollbar">
                        @foreach($scannedData['items'] as $item)
                        <div class="px-4 py-3 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2 min-w-0">
                                @if(!empty($item['qty']) && $item['qty'] > 1)
                                    <span class="shrink-0 text-[8px] font-black text-brand-400 bg-brand-500/10 rounded px-1.5 py-0.5">x{{ $item['qty'] }}</span>
                                @endif
                                <p class="text-xs text-zinc-300 font-medium truncate">{{ $item['name'] ?? 'Item sem nome' }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-black text-white whitespace-nowrap">{{ number_format($item['price'] ?? 0, 2, ',', '.') }}€</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Inteligência de Contexto (Notas) ── --}}
                @if(!empty($scannedData['notes']))
                <div class="flex gap-3 bg-brand-500/5 border border-brand-500/10 rounded-xl px-4 py-3">
                    <flux:icon name="light-bulb" class="size-4 text-brand-400 shrink-0 mt-0.5" />
                    <p class="text-[11px] text-zinc-400 italic leading-relaxed">{{ $scannedData['notes'] }}</p>
                </div>
                @endif

                <div class="flex gap-3 bg-white/5 border border-white/5 rounded-xl px-4 py-3">
                    <flux:icon name="information-circle" class="size-4 text-zinc-500 shrink-0" />
                    <p class="text-[9px] text-zinc-500 leading-tight uppercase font-black">Pode ajustar estes valores no passo final.</p>
                </div>

            </div>{{-- /scrollable content --}}

            {{-- Botões de Acção do Modal de Revisão --}}
            <div class="px-5 sm:px-8 pb-6 sm:pb-8 pt-4 flex items-center gap-3 sm:gap-4 shrink-0">
                <button type="button" @click="reviewOpen = false"
                    class="flex-1 h-12 sm:h-14 rounded-xl sm:rounded-2xl font-bold text-[10px] sm:text-xs text-zinc-500 hover:text-white border border-white/5 uppercase tracking-widest">
                    Cancelar
                </button>
                <button
                    type="button"
                    @click="reviewOpen = false; setTimeout(() => formOpen = true, 250)"
                    class="flex-[2] h-12 sm:h-14 rounded-xl sm:rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-black uppercase tracking-widest text-[10px] sm:text-sm flex items-center justify-center gap-2 shadow-xl shadow-brand-500/20 transition-all"
                >
                    <flux:icon name="check-circle" class="size-4 sm:size-5" />
                    Confirmar Dados
                </button>
            </div>

            @else
            <div class="p-16 text-center text-zinc-500 italic text-xs">A processar dados...</div>
            @endif

        </div>{{-- /panel review --}}
    </div>{{-- /modal 2 --}}
























{{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 3 — FORMULÁRIO DE REGISTO (ESTILO CLONADO DAS ASSINATURAS) --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}

    {{-- Backdrop --}}
    <div x-show="formOpen" x-cloak
        x-transition:enter="transition-opacity ease-out duration-75"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="formOpen = false"
        class="fixed inset-0 z-50 bg-zinc-950/50 will-change-opacity">
    </div>

    {{-- Wrapper --}}
    <div x-show="formOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">

        {{-- Painel --}}
        <div x-show="formOpen"
            x-transition:enter="transition ease-out duration-75 transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.99] translate-y-0.5"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.99] translate-y-0.5"
            class="relative z-10 w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden transform-gpu will-change-transform"
            @click.stop>

            <form wire:submit.prevent="save" class="flex max-h-[86vh] flex-col" autocomplete="off">

                {{-- HEADER (Igual às Assinaturas) --}}
                <div class="shrink-0 p-5 sm:p-6 pb-4 flex items-center gap-4 border-b border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <div class="p-3 rounded-2xl text-white shadow-md shadow-brand-500/20" style="background-color: var(--cat-color);">
                        <flux:icon name="{{ $editingId ? 'pencil-square' : 'plus' }}" class="size-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                            {{ $editingId ? 'Editar Registo' : 'Novo Registo' }}
                        </h3>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                            {{ $title }} • Gestão Direta de Custos
                        </p>
                    </div>
                    <button type="button" @click="formOpen = false"
                        class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 transition-colors">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                {{-- BODY (Scrollable) --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-5">

                    {{-- Valor e Data --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Valor da Transação</span>
                            <input wire:model="amount" type="number" step="0.01" placeholder="0.00"
                                class="w-full h-14 rounded-2xl border-0 bg-zinc-50 px-4 text-sm font-black text-brand-600 shadow-inner outline-none ring-0 transition focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-900">
                        </label>
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Data do Gasto</span>
                            <input wire:model="spent_at" type="date"
                                class="w-full h-14 rounded-2xl border-0 bg-zinc-50 px-4 text-sm font-bold shadow-inner outline-none ring-0 transition focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-900 dark:text-white">
                        </label>
                    </div>

                    {{-- Classificação --}}
                    <label class="block space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Subcategoria</span>
                        <select wire:model="subcategory" class="w-full h-14 rounded-2xl border-0 bg-zinc-50 px-4 text-sm font-bold shadow-inner outline-none ring-0 transition focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-900 dark:text-white">
                            <option value="">Selecionar tipo...</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub }}">{{ ucfirst(strtolower($sub)) }}</option>
                            @endforeach
                        </select>
                    </label>

                    {{-- Campos Inteligentes (Mesmo Estilo) --}}
                    <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 p-5 space-y-5">
                        <div class="flex items-center gap-3 border-b border-zinc-200/50 dark:border-zinc-800 pb-3">
                            <flux:icon name="{{ $categoryFields[$slug]['icon'] ?? 'tag' }}" class="size-4" style="color: var(--cat-color);" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Informações Adicionais</span>
                        </div>

                        @if(isset($categoryFields[$slug]))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($categoryFields[$slug]['fields'] as $field)
                                    @if($field['type'] === 'select')
                                        <label class="block space-y-2">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">{{ $field['label'] }}</span>
                                            <select wire:model="meta.{{ $field['name'] }}" class="w-full h-12 rounded-xl border-0 bg-white px-4 text-sm font-bold shadow-sm outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white">
                                                <option value="">Escolher...</option>
                                                @foreach($field['options'] as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    @elseif($field['type'] === 'checkbox')
                                        <label class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 cursor-pointer">
                                            <span class="text-xs font-bold text-zinc-500">{{ $field['label'] }}</span>
                                            <input type="checkbox" wire:model="meta.{{ $field['name'] }}" class="rounded border-zinc-300 text-brand-600 focus:ring-brand-600">
                                        </label>
                                    @else
                                        <label class="block space-y-2">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">{{ $field['label'] }}</span>
                                            <input wire:model="meta.{{ $field['name'] }}" type="{{ $field['type'] }}" placeholder="{{ $field['placeholder'] ?? '' }}"
                                                class="w-full h-12 rounded-xl border-0 bg-white px-4 text-sm font-bold shadow-sm outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white">
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Descrição --}}
                        <label class="block space-y-2 pt-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Notas / Observações</span>
                            <textarea wire:model="description" rows="2" class="w-full resize-none rounded-2xl border-0 bg-white px-4 py-3 text-sm font-bold shadow-sm outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white"></textarea>
                        </label>
                    </div>
                </div>

                {{-- FOOTER (Igual às Assinaturas) --}}
                <div class="shrink-0 p-5 sm:p-6 pt-4 flex gap-3 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <button type="button" @click="formOpen = false"
                        class="flex-1 uppercase font-black text-[10px] h-12 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-[2] h-12 rounded-2xl font-black uppercase tracking-widest shadow-lg text-white active:scale-95 transition-all"
                        style="background-color: var(--cat-color); box-shadow: 0 10px 20px -5px color-mix(in srgb, var(--cat-color), transparent 50%);">
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Atualizar' : 'Confirmar Gasto' }}</span>
                        <span wire:loading wire:target="save">A gravar...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>












    {{-- ── RODAPÉ DE PROTOCOLO ── --}}
    <footer class="pt-16 pb-12 text-center opacity-40 mx-4 border-t border-zinc-100 dark:border-zinc-800 mt-10">
        <div class="flex flex-col items-center gap-3">
            <div class="size-10 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center border border-zinc-200 dark:border-zinc-800">
                <flux:icon name="shield-check" class="size-5 text-zinc-400" />
            </div>
            <p class="text-[8px] sm:text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em] leading-relaxed">
                © {{ date('Y') }} {{ config('app.name') }}<br>
                Protocolo Hub Inteligente v2.7 • {{ $title }} <br>
                Encriptação de Ponta-a-Ponta Ativa
            </p>
        </div>
    </footer>

    {{-- SCRIPTS DE INTERAÇÃO (Scanner e Preview) --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Lógica para detetar mudança no input de ficheiro e gerar preview local instantâneo
            const input = document.getElementById('receiptInput');
            if (input) {
                input.addEventListener('change', function () {
                    const file = this.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = e => {
                        // Acede ao componente Alpine para injetar o preview da imagem
                        const el = document.querySelector('[x-data]');
                        if (el && el._x_dataStack) {
                            el._x_dataStack[0].scannerPreview = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Ouvinte para fechar modais ao clicar no botão de sucesso (Opcional)
            window.addEventListener('expense-saved', () => {
                // Pequeno feedback tátil se suportado
                if (window.navigator && window.navigator.vibrate) {
                    window.navigator.vibrate(50);
                }
            });
        });
    </script>

</div>{{-- /FIM DO WRAPPER x-data root --}}
</div>{{-- /FIM DO WRAPPER DE COR --}}
