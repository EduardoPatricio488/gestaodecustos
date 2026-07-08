<div class="space-y-10 pb-20">

    {{-- 1. HEADER BANCÁRIO --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 {{ $isBusinessMode ? 'bg-brand-500/5' : 'bg-brand-500/10' }}
             blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 {{ $isBusinessMode ? 'bg-brand-500/20' : 'bg-brand-500/30' }}
                         blur-2xl rounded-full group-hover:scale-125 transition-all duration-700"></div>

                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800
                         rounded-[2rem] shadow-2xl">
                        <flux:icon name="building-library"
                                   class="w-10 h-10 {{ $isBusinessMode ? 'text-zinc-800 dark:text-brand-400' : 'text-brand-600' }}" />
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                            {{ $modeTitle }}
                        </h1>
                        <flux:badge variant="neutral"
                                    class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">
                            Tesouraria Ativa
                        </flux:badge>
                    </div>

                    <p class="text-sm text-zinc-500 font-medium italic mt-2">
                        {{ $isBusinessMode
                            ? 'Controlo estratégico de capitais e cashflow empresarial'
                            : 'Gestão consolidada de contas e ativos pessoais' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem]
                        border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="relative flex-1 min-w-[200px]">
                    <flux:input wire:model.live="search" icon="magnifying-glass"
                                placeholder="Procurar conta..."
                                class="!bg-transparent border-none shadow-none" />
                </div>

                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

                <flux:modal.trigger name="bank-modal">
                    <flux:button variant="primary" icon="plus"
                                 class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Nova Conta
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- 2. KPIs — ORGANIZADOS 2×2 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- CASH-ON-HAND (LARGO + PREMIUM) --}}
        <div class="stat-card p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-200
             dark:border-zinc-800 flex flex-col justify-between min-h-80 transition-all hover:scale-[1.02]
             {{ $isBusinessMode ? 'bg-zinc-950 text-white' : 'bg-brand-600 text-white border-none' }}">

            <div class="absolute top-0 right-0 w-40 h-40 {{ $isBusinessMode ? 'bg-brand-500/10' : 'bg-white/10' }}
                 blur-3xl rounded-full -mr-12 -mt-12"></div>

            <div class="relative z-10 mb-8">
                <p class="text-[12px] font-black uppercase tracking-[0.4em]
                    {{ $isBusinessMode ? 'text-brand-400' : 'text-white/70' }}">
                    Cash-on-Hand
                </p>

                <h3 class="text-7xl font-black mt-5 tracking-tighter italic leading-none">
                    {{ number_format($totalLiquidez, 2, ',', ' ') }}
                    <span class="text-4xl">€</span>
                </h3>

                <p class="text-[12px] opacity-90 mt-4 italic font-semibold">
                    Total em disponibilidades imediatas
                </p>
            </div>

            <div class="relative z-10 space-y-2 text-[12px] font-semibold opacity-95 leading-relaxed">
                <p>Variação diária: <span class="font-bold text-emerald-400">+2.3%</span></p>
                <p>Variação semanal: <span class="font-bold text-emerald-400">+5.8%</span></p>
                <p>Liquidez sobre total de contas: <span class="font-bold">68%</span></p>
                <p>Estabilidade: <span class="font-bold text-emerald-300">Alta</span></p>
            </div>

            <flux:icon name="banknotes"
                       class="absolute -right-8 -bottom-8 size-40 text-white/5 rotate-12" />
        </div>

        {{-- PASSIVO CIRCULANTE --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200
             dark:border-zinc-800 shadow-sm flex flex-col justify-between min-h-72 group hover:border-red-500/30 transition-all">

            <div>
                <p class="text-[12px] font-black text-zinc-400 uppercase tracking-widest mb-2">
                    Passivo Circulante
                </p>

                <h3 class="text-5xl font-black text-red-500 tracking-tighter italic leading-none">
                    {{ number_format($totalDividaCartao, 2, ',', ' ') }} €
                </h3>

                <p class="text-[12px] text-zinc-500 italic font-semibold mt-3">
                    Utilização de crédito
                </p>
            </div>

            <div class="space-y-2 text-[12px] font-semibold text-zinc-600 dark:text-zinc-300 leading-relaxed">
                <p>Limite total: <span class="font-bold">{{ number_format($limiteTotalCartoes, 2, ',', ' ') }} €</span></p>
                <p>Utilização: <span class="font-bold text-red-500">{{ $percentUtilizacao }}%</span></p>
                <p>Risco associado: <span class="font-bold">{{ $riscoCartoes }}%</span></p>
            </div>
        </div>

        {{-- NET CASHFLOW REAL --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200
             dark:border-zinc-800 shadow-sm flex flex-col justify-between min-h-72 group hover:border-emerald-500/30 transition-all">

            <div>
                <p class="text-[12px] font-black text-zinc-400 uppercase tracking-widest mb-2">
                    Net Cashflow Real
                </p>

                <h3 class="text-5xl font-black dark:text-white tracking-tighter italic leading-none">
                    {{ number_format($netCash, 2, ',', ' ') }} €
                </h3>

                <flux:badge variant="success"
                            class="w-fit font-black text-[10px] uppercase tracking-tighter bg-emerald-500/10
                                   text-emerald-600 border-none px-4 py-1 italic mt-3">
                    Saldo Consolidado
                </flux:badge>
            </div>

            <div class="space-y-2 text-[12px] font-semibold text-zinc-600 dark:text-zinc-300 leading-relaxed">
                <p>Entradas hoje: <span class="font-bold text-emerald-500">+{{ number_format($entradasHoje, 2, ',', ' ') }} €</span></p>
                <p>Saídas hoje: <span class="font-bold text-red-500">-{{ number_format($saidasHoje, 2, ',', ' ') }} €</span></p>
                <p>Fluxo líquido: <span class="font-bold">{{ number_format($fluxoHoje, 2, ',', ' ') }} €</span></p>
            </div>
        </div>

        {{-- SALDO PROJETADO --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200
             dark:border-zinc-800 shadow-sm flex flex-col justify-between min-h-72 group hover:border-amber-500/30 transition-all">

            <div>
                <p class="text-[12px] font-black text-zinc-400 uppercase tracking-widest mb-2">
                    Saldo Projetado
                </p>

                <h3 class="text-4xl font-black dark:text-white tracking-tighter italic leading-none">
                    {{ number_format($forecastCash, 2, ',', ' ') }} €
                </h3>
            </div>

            <div class="space-y-2 text-[12px] font-semibold text-zinc-600 dark:text-zinc-300 leading-relaxed">
                <p>Previsão 7 dias: <span class="font-bold">{{ number_format($forecast7, 2, ',', ' ') }} €</span></p>
                <p>Previsão 30 dias: <span class="font-bold">{{ number_format($forecast30, 2, ',', ' ') }} €</span></p>
            </div>

            <div class="flex items-center justify-between mt-3">
                <p class="text-[12px] font-black text-zinc-400 uppercase tracking-widest">Risco Global</p>

                <span class="inline-flex px-3 py-1 rounded-full text-[12px] font-black uppercase tracking-widest
                    @if($globalRisk < 30) bg-emerald-500/10 text-emerald-600
                    @elseif($globalRisk < 60) bg-amber-500/10 text-amber-600
                    @else bg-red-500/10 text-red-600 @endif">
                    {{ $globalRisk }}%
                </span>
            </div>
        </div>

    </div>









{{-- 3. LISTAGEM DE CONTAS — DOSSIÊ EXECUTIVO (VERSÃO FINAL COM MENU 3 PONTOS) --}}
<div class="flex flex-col gap-6 w-full">
    @forelse($accounts as $account)
        <div wire:key="account-row-{{ $account->id }}" x-data="{ isOpen: false }" class="relative w-full group">

            {{-- 1. CARTÃO PRINCIPAL (VISÍVEL) --}}
            <div class="glass-card relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm transition-all duration-500 hover:shadow-xl w-full overflow-hidden text-left">

                <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background-color: {{ $account->color }}"></div>

                <div class="p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">

                    {{-- Identificação --}}
                    <div class="flex items-center gap-5 flex-1 min-w-0 w-full text-left">
                        <div class="size-12 rounded-xl flex items-center justify-center text-white shadow-md shrink-0" style="background-color: {{ $account->color }}">
                            <flux:icon name="{{ $account->getIcon() }}" variant="mini" class="size-6" />
                        </div>

                        <div class="truncate">
                            <h4 class="font-black dark:text-white uppercase text-lg tracking-tight leading-none truncate">{{ $account->name }}</h4>
                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                <p class="text-[9px] text-zinc-500 font-black uppercase tracking-[0.2em]">{{ $account->bank_name ?? 'Instituição' }}</p>
                                @if($account->tags)
                                    <div class="flex gap-1.5">
                                        @foreach($account->tags as $tag)
                                            <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[7px] font-black uppercase tracking-widest rounded border border-zinc-200 dark:border-zinc-700">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Health Score --}}
                    <div class="hidden xl:flex flex-col items-center px-8 border-l border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-1.5 mb-1 relative group/tip">
                            <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Health Score</p>
                            <flux:icon name="information-circle" variant="micro" class="size-3 text-zinc-300 cursor-help" />
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-zinc-950 text-white text-[10px] font-bold rounded-xl opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-50 shadow-2xl border border-white/10 text-center uppercase tracking-tighter leading-relaxed">
                                Indica a robustez financeira baseada na liquidez disponível.
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-zinc-950"></div>
                            </div>
                        </div>
                        <span class="text-base font-black {{ $account->risk_score < 30 ? 'text-emerald-500' : 'text-amber-500' }}">{{ 100 - ($account->risk_score ?? 0) }}%</span>
                    </div>

                    {{-- Risco --}}
                    <div class="hidden xl:flex flex-col items-center px-8 border-x border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-1.5 mb-1 relative group/tip">
                            <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Risco</p>
                            <flux:icon name="information-circle" variant="micro" class="size-3 text-zinc-300 cursor-help" />
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-zinc-950 text-white text-[10px] font-bold rounded-xl opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-50 shadow-2xl border border-white/10 text-center uppercase tracking-tighter leading-relaxed">
                                Probabilidade de rutura de tesouraria ou excesso de alavancagem.
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-zinc-950"></div>
                            </div>
                        </div>
                        <span class="text-base font-black {{ $account->risk_score > 60 ? 'text-red-500' : 'text-emerald-500' }}">{{ $account->risk_score ?? 0 }}%</span>
                    </div>

                    {{-- Saldo --}}
                    <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-end">
                        <div class="text-right">
                            <div class="flex items-center justify-end gap-1.5 mb-1 relative group/tip">
                                <p class="text-[8px] font-black text-zinc-400 uppercase tracking-[0.2em]">Saldo Disponível</p>
                                <flux:icon name="information-circle" variant="micro" class="size-3 text-zinc-300 cursor-help" />
                                <div class="absolute bottom-full right-0 mb-2 w-40 p-2 bg-zinc-950 text-white text-[9px] font-bold rounded-lg opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-50 text-center uppercase border border-white/10">
                                    Capital líquido real que pode ser movimentado hoje.
                                    <div class="absolute top-full right-4 border-4 border-transparent border-t-zinc-950"></div>
                                </div>
                            </div>
                            <h3 class="text-3xl font-black dark:text-white tracking-tighter italic leading-none">{{ number_format($account->current_balance, 2, ',', ' ') }}€</h3>
                        </div>
                        <button @click="isOpen = !isOpen" class="size-10 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-400 hover:text-brand-500 transition-all shadow-inner" :class="isOpen ? 'rotate-180 bg-brand-50/10 text-brand-600' : ''">
                            <flux:icon name="chevron-down" class="size-4" />
                        </button>
                    </div>
                </div>

                {{-- 2. CONTEÚDO EXPANSÍVEL (DADOS TÉCNICOS + NOTAS + LOG) --}}
                <div x-show="isOpen" x-collapse x-cloak class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-black/10">
                    <div class="p-8 space-y-10 text-left">

                       {{-- GRELHA DE DADOS REFEITA EM DUAS LINHAS --}}
<div class="grid grid-cols-1 md:grid-cols-12 gap-y-10 gap-x-8 items-start">

    {{-- LINHA 1: IDENTIFICAÇÃO --}}
    {{-- IBAN --}}
    <div class="md:col-span-5 space-y-2">
        <div class="flex items-center gap-1.5 relative group/tip">
            <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">IBAN Internacional</span>
            <flux:icon name="information-circle" variant="micro" class="size-2.5 text-zinc-300" />
            <div class="absolute bottom-full left-0 mb-2 w-48 p-2 bg-zinc-950 text-white text-[8px] rounded opacity-0 group-hover/tip:opacity-100 transition-opacity z-50">Identificação completa da conta para transferências.</div>
        </div>
        <div class="flex items-center gap-3 bg-white dark:bg-zinc-800 p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-800 w-fit shadow-sm">
            <p class="text-xs font-mono font-black dark:text-zinc-100 tracking-tight whitespace-nowrap">{{ $account->iban ?? '---' }}</p>
            @if($account->iban)
                <button @click="navigator.clipboard.writeText('{{ $account->iban }}')" class="p-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-md transition-all text-zinc-400 hover:text-brand-500"><flux:icon name="clipboard" variant="micro" class="size-3.5" /></button>
            @endif
        </div>
    </div>

    {{-- SWIFT --}}
    <div class="md:col-span-3 space-y-1">
        <div class="flex items-center gap-1.5 relative group/tip">
            <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">SWIFT / BIC</span>
            <flux:icon name="information-circle" variant="micro" class="size-2.5 text-zinc-300" />
            <div class="absolute bottom-full left-0 mb-2 w-40 p-2 bg-zinc-950 text-white text-[8px] rounded opacity-0 group-hover/tip:opacity-100 transition-opacity z-50">Código mundial de identificação do banco.</div>
        </div>
        <p class="text-sm font-mono font-bold dark:text-zinc-200 uppercase">{{ $account->swift ?? '---' }}</p>
    </div>

    {{-- TITULAR --}}
    <div class="md:col-span-4 space-y-1">
        <div class="flex items-center gap-1.5 relative group/tip">
            <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Titular da Conta</span>
            <flux:icon name="information-circle" variant="micro" class="size-2.5 text-zinc-300" />
            <div class="absolute bottom-full left-0 mb-2 w-40 p-2 bg-zinc-950 text-white text-[8px] rounded opacity-0 group-hover/tip:opacity-100 transition-opacity z-50">Nome oficial da entidade dona da conta.</div>
        </div>
        <p class="text-xs font-black dark:text-white uppercase truncate">{{ $account->holder_name ?? '---' }}</p>
    </div>

    {{-- LINHA 2: FINANCEIRO (Abaixo) --}}
    {{-- LIMITE --}}
    <div class="md:col-span-5 space-y-2 border-t border-zinc-100 dark:border-zinc-800 pt-6">
        <div class="flex items-center gap-1.5 relative group/tip">
            <span class="text-[8px] font-black text-red-500 uppercase tracking-widest">Limite de Crédito Atribuído</span>
            <flux:icon name="information-circle" variant="micro" class="size-2.5 text-red-300" />
            <div class="absolute bottom-full left-0 mb-2 w-48 p-2 bg-red-950 text-white text-[8px] rounded opacity-0 group-hover/tip:opacity-100 transition-opacity z-50 italic">Plafond máximo de crédito ou descoberto autorizado.</div>
        </div>
        <p class="text-2xl font-black text-red-500 italic tracking-tighter">
            {{ number_format($account->credit_limit ?? 0, 2, ',', ' ') }}€
        </p>
    </div>

    {{-- PROJEÇÃO --}}
    <div class="md:col-span-5 space-y-2 border-t border-zinc-100 dark:border-zinc-800 pt-6">
        <div class="flex items-center gap-1.5 relative group/tip">
            <span class="text-[8px] font-black text-amber-600 uppercase tracking-widest">Saldo Projetado (30 Dias)</span>
            <flux:icon name="information-circle" variant="micro" class="size-2.5 text-amber-300" />
            <div class="absolute bottom-full left-0 mb-2 w-48 p-2 bg-amber-950 text-white text-[8px] rounded opacity-0 group-hover/tip:opacity-100 transition-opacity z-50 italic">Estimativa de capital baseado no fluxo histórico.</div>
        </div>
        <p class="text-2xl font-black text-amber-600 italic tracking-tighter">
            {{ number_format($account->forecast_balance ?? $account->current_balance, 2, ',', ' ') }}€
        </p>
    </div>

    {{-- AÇÕES (MENU 3 PONTOS) --}}
    <div class="md:col-span-2 flex justify-end items-end pt-6 border-t border-zinc-100 dark:border-zinc-800 h-full" x-data="{ optionsOpen: false }">
        <div class="relative mb-1">
            <button type="button" @click.stop="optionsOpen = !optionsOpen" class="flex items-center gap-2 px-3 py-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700">
                <flux:icon name="ellipsis-horizontal" class="size-4" />
            </button>

            <div x-show="optionsOpen" x-cloak @click.outside="optionsOpen = false" class="absolute right-0 bottom-full mb-2 w-48 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-2xl z-[100] overflow-hidden text-left">
                <div class="p-1.5 space-y-0.5">
                    <button type="button" wire:click="edit({{ $account->id }})" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-brand-50 hover:text-brand-600 transition-all">
                        <flux:icon name="pencil-square" class="size-4 text-brand-500" /> Configurar Conta
                    </button>
                     {{-- ✅ ADICIONA ESTE NOVO BOTÃO AQUI --}}
    <button type="button" wire:click="generateAuditCode" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 transition-all">
        <flux:icon name="shield-check" class="size-4 text-zinc-900 dark:text-white" /> Gerar Acesso Bancário
    </button>

    <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>
                    <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>
                    <button type="button" wire:click="delete({{ $account->id }})" wire:confirm="Eliminar conta permanentemente?" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 transition-all">
                        <flux:icon name="trash" class="size-4 text-red-500" /> Remover
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

                        {{-- NOTAS ADMINISTRATIVAS --}}
                        @if($account->notes)
                            <div class="p-5 bg-white dark:bg-zinc-900/50 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-inner">
                                <div class="flex items-center gap-2 mb-2 relative group/tip">
                                    <flux:icon name="chat-bubble-bottom-center-text" variant="mini" class="size-3 text-zinc-400" />
                                    <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Notas Administrativas</span>
                                    <flux:icon name="information-circle" variant="micro" class="size-2.5 text-zinc-300" />
                                    <div class="absolute bottom-full left-6 mb-2 w-48 p-2 bg-zinc-950 text-white text-[8px] rounded opacity-0 group-hover/tip:opacity-100 transition-opacity z-50 italic">Observações e memorandos internos sobre a conta.</div>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 italic leading-relaxed">"{{ $account->notes }}"</p>
                            </div>
                        @endif

                        {{-- LOG DE ATIVIDADE --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between px-1">
                                <div class="flex items-center gap-2 relative group/tip">
                                    <h5 class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em]">Log de Atividade</h5>
                                    <flux:icon name="information-circle" variant="micro" class="size-2.5 text-zinc-300" />
                                    <div class="absolute bottom-full left-0 mb-2 w-48 p-2 bg-zinc-950 text-white text-[8px] rounded opacity-0 group-hover/tip:opacity-100 transition-opacity z-50">Transações recentes processadas nesta conta.</div>
                                </div>
                                <button wire:click="openHistory({{ $account->id }})" class="text-[9px] font-black text-brand-600 uppercase hover:underline">Abrir Extrato Digital →</button>
                            </div>

                            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                                <table class="w-full text-left border-collapse">
                                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                                        @php $miniLogs = collect($historyTransactions)->where('account_id', $account->id)->take(3); @endphp
                                        @forelse($miniLogs as $t)
                                            <tr class="text-[10px] font-bold text-zinc-600 dark:text-zinc-300">
                                                <td class="px-6 py-3 uppercase">{{ \Carbon\Carbon::parse($t['date'])->format('d M') }}</td>
                                                <td class="px-6 py-3">{{ $t['desc'] }}</td>
                                                <td class="px-6 py-3 text-right font-black {{ $t['amount'] > 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ number_format($t['amount'], 2, ',', ' ') }}€</td>
                                            </tr>
                                        @empty
                                            <tr><td class="px-6 py-8 text-center text-zinc-400 text-[9px] font-black uppercase italic">Sincronização de dados em curso...</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="py-24 text-center opacity-30 flex flex-col items-center">
            <flux:icon name="building-library" class="size-16 mb-4" />
            <p class="text-xs font-black uppercase tracking-widest italic">Cofre de Tesouraria Vazio</p>
        </div>

</div>







            <div class="col-span-full py-24 text-center">
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                        <flux:icon name="building-library" class="size-12 text-zinc-200 dark:text-zinc-700" />
                    </div>
                    <div class="space-y-1 text-center">
                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Cofre Inativo</p>
                        <p class="text-zinc-400 text-xs italic font-medium">Não foram encontradas contas no contexto {{ $isBusinessMode ? 'empresarial' : 'pessoal' }}.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 4. MODAL: CONFIGURAÇÃO DE CONTA (mantido como está) --}}
    <flux:modal name="bank-modal" position="center" class="md:w-[650px] !p-0">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho --}}
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-2xl text-white shadow-lg shadow-brand-500/20 {{ $isBusinessMode ? 'bg-zinc-900' : 'bg-brand-600' }}">
                    <flux:icon name="building-library" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Configurar Conta' : 'Nova Conta Digital' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">
                        Contexto Atual:
                        <span class="text-brand-600 font-bold uppercase">{{ $isBusinessMode ? 'Empresa' : 'Pessoal' }}</span>
                    </p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- SECÇÃO: IDENTIFICAÇÃO --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 px-1">
                        <flux:icon name="identification" class="size-3 text-zinc-400" />
                        <p class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em]">Identificação da Conta</p>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome do Banco ou Identificação</flux:label>
                        <flux:input
                            wire:model="name"
                            placeholder="Ex: Santander À Ordem, Revolut Business..."
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome do Banco</flux:label>
                            <flux:input
                                wire:model="bank_name"
                                placeholder="Ex: Santander Totta"
                                class="font-bold !bg-white dark:!bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl h-12 shadow-sm"
                            />
                        </div>
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">País</flux:label>
                            <flux:input
                                wire:model="country"
                                placeholder="Portugal"
                                class="font-bold !bg-white dark:!bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl h-12 shadow-sm"
                            />
                        </div>
                    </div>
                </div>

                {{-- SECÇÃO: DADOS BANCÁRIOS --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2">
                        <flux:icon name="credit-card" class="size-3 text-brand-500" />
                        <p class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">Dados Bancários</p>
                    </div>

                    {{-- ZONA DO IBAN NO PAINEL EXPANSÍVEL --}}
<div class="space-y-1 col-span-2"> {{-- Col-span-2 para dar mais largura --}}
    <div class="flex items-center gap-1.5 relative group/tip">
        <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">IBAN Internacional</span>
        <flux:icon name="information-circle" variant="micro" class="size-2.5 text-zinc-300" />
        <div class="absolute bottom-full left-0 mb-2 w-40 p-2 bg-zinc-950 text-white text-[9px] rounded-lg opacity-0 group-hover/tip:opacity-100 transition-opacity z-50 uppercase tracking-tighter">
            Número completo para transferências.
        </div>
    </div>

    <div class="flex items-center gap-3 bg-zinc-100 dark:bg-zinc-800/50 p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 w-fit">
        {{-- Removido o TRUNCATE para ver tudo --}}
        <p class="text-xs font-mono font-black dark:text-zinc-100 tracking-tight">
            {{ $account->iban ?? '---' }}
        </p>

        {{-- BOTÃO DE CÓPIA RÁPIDA --}}
        @if($account->iban)
            <button
                x-data="{ copied: false }"
                @click="navigator.clipboard.writeText('{{ $account->iban }}'); copied = true; setTimeout(() => copied = false, 2000)"
                class="p-1 hover:bg-white dark:hover:bg-zinc-700 rounded transition-all shadow-sm"
                title="Copiar IBAN"
            >
                <flux:icon x-show="!copied" name="clipboard" variant="micro" class="size-3 text-zinc-400" />
                <flux:icon x-show="copied" name="check" variant="micro" class="size-3 text-emerald-500" />
            </button>
        @endif
    </div>
</div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">SWIFT / BIC</flux:label>
                            <flux:input
                                wire:model="swift"
                                placeholder="BCOMPTPL"
                                class="font-mono text-xs !bg-white dark:!bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl h-12 shadow-sm"
                            />
                        </div>
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Titular da Conta</flux:label>
                            <flux:input
                                wire:model="holder_name"
                                placeholder="Nome do titular"
                                class="font-bold !bg-white dark:!bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl h-12 shadow-sm"
                            />
                        </div>
                    </div>
                </div>

                {{-- SECÇÃO: CONFIGURAÇÃO FINANCEIRA --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2">
                        <flux:icon name="chart-bar" class="size-3 text-emerald-500" />
                        <p class="text-[9px] font-black uppercase text-emerald-600 tracking-[0.2em]">Configuração Financeira</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Categoria de Conta</flux:label>
                            <flux:select wire:model="type" class="font-black uppercase text-[10px] !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
                                <option value="corrente">💳 Conta Corrente</option>
                                <option value="poupanca">📈 Poupança / Investimento</option>
                                <option value="cash">💵 Dinheiro Vivo</option>
                                <option value="credito">🔥 Cartão de Crédito</option>
                                <option value="tesouraria">🏦 Tesouraria</option>
                                <option value="operacoes">⚙ Operações</option>
                                <option value="salarios">👥 Salários</option>
                                <option value="impostos">📑 Impostos</option>
                            </flux:select>
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Saldo Inicial (€)</flux:label>
                            <flux:input
                                wire:model="balance"
                                type="number"
                                step="0.01"
                                class="font-black text-lg text-brand-600 !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                                placeholder="0,00"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Limite de Crédito (€)</flux:label>
                            <flux:input
                                wire:model="credit_limit"
                                type="number"
                                step="0.01"
                                class="font-black text-sm !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                                placeholder="Ex: 5000,00"
                            />
                        </div>
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Saldo Projetado (€)</flux:label>
                            <flux:input
                                wire:model="forecast_balance"
                                type="number"
                                step="0.01"
                                class="font-black text-sm !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                                placeholder="Ex: 12000,00"
                            />
                        </div>
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Risco Financeiro (%)</flux:label>
                            <flux:input
                                wire:model="risk_score"
                                type="number"
                                step="1"
                                min="0"
                                max="100"
                                class="font-black text-sm !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                                placeholder="Ex: 35"
                            />
                        </div>
                    </div>

                    {{-- Identidade Visual --}}
                    <div class="space-y-3 pt-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Cor de Identidade no Sistema</flux:label>
                        <div class="flex items-center gap-4 p-3 bg-white dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                            <input type="color" wire:model="color" class="size-10 rounded-lg border-none cursor-pointer bg-transparent">
                            <div class="flex flex-col">
                                <span class="text-xs font-mono font-black text-zinc-800 dark:text-zinc-200 uppercase">{{ $color }}</span>
                                <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-tight">Esta cor definirá o aspeto do seu cartão</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECÇÃO: TAGS E NOTAS --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 px-1">
                        <flux:icon name="tag" class="size-3 text-zinc-400" />
                        <p class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em]">Tags & Notas Internas</p>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Tags (separadas por vírgulas)</flux:label>
                        <flux:input
                            wire:model="tags_input"
                            placeholder="Ex: Crítica, Alto Volume, Cartão Principal"
                            class="text-xs !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-12 shadow-inner"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Notas Internas</flux:label>
                        <flux:textarea
                            wire:model="notes"
                            rows="3"
                            placeholder="Regista observações internas, renegociações, alertas de risco, etc..."
                            class="rounded-2xl shadow-sm border-none !bg-zinc-50 dark:!bg-zinc-900 text-sm p-4"
                        />
                    </div>
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Conta' : 'Ativar Conta no Sistema' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
{{-- MODAL DE HISTÓRICO FINANCEIRO --}}
    <flux:modal name="account-history-modal" position="center" class="md:w-[700px] !p-0 overflow-hidden" wire:ignore.self>
        <div class="p-10 bg-white dark:bg-zinc-950 space-y-8">

            <div class="flex items-center gap-4 text-left">
                <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg">
                    <flux:icon name="clock" class="size-6" />
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">
                        Extrato de Movimentos
                    </h2>
                    <p class="text-xs text-zinc-500 font-bold uppercase mt-1 tracking-widest">{{ $selectedAccountName }}</p>
                </div>
            </div>

            <div class="glass-card border border-zinc-200 dark:border-zinc-800 rounded-[2rem] overflow-hidden shadow-inner max-h-[450px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800 text-[9px] font-black uppercase text-zinc-400 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4">Data</th>
                            <th class="px-6 py-4">Descrição</th>
                            <th class="px-6 py-4 text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                        @forelse($historyTransactions as $trans)
                            <tr class="text-xs font-bold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 uppercase text-[10px]">
                                    {{ \Carbon\Carbon::parse($trans['date'])->translatedFormat('d M, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="size-1.5 rounded-full {{ $trans['type'] === 'income' ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                                        <span class="truncate max-w-[200px]">{{ $trans['desc'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-black {{ $trans['amount'] > 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                    {{ number_format($trans['amount'], 2, ',', ' ') }} €
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-20 text-center text-zinc-400 uppercase text-[10px] font-black italic">
                                    <flux:icon name="magnifying-glass" class="size-10 mx-auto mb-4 opacity-20" />
                                    Sem movimentos registados nesta conta
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex gap-4">
                <flux:modal.close class="w-full">
                    <flux:button variant="ghost" class="w-full font-black uppercase text-[10px] h-12 rounded-xl">Fechar Extrato</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
    {{-- MODAL: CREDENCIAIS DE AUDITORIA BANCÁRIA --}}
    <flux:modal name="audit-code-modal" position="center" class="md:w-[500px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800 text-left">

            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-900 rounded-2xl text-white shadow-lg">
                    <flux:icon name="shield-check" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">Protocolo de Auditoria</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium mt-1">Chave de acesso seguro para instituições financeiras.</p>
                </div>
            </div>

            <div class="space-y-6">
                {{-- O CÓDIGO (PIN) --}}
                <div class="p-8 bg-zinc-950 rounded-[2rem] border border-zinc-800 text-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-zinc-500/5 blur-3xl rounded-full"></div>
                    <p class="relative z-10 text-[9px] font-black text-zinc-500 uppercase tracking-[0.4em] mb-4">Token de Verificação Ativo</p>

                    <div class="relative z-10 flex items-center justify-center gap-6">
                        <span class="text-5xl font-mono font-black text-white tracking-[0.2em]">
                            {{ $generatedAuditCode }}
                        </span>

                        <button
                            x-data="{ copiedAudit: false }"
                            @click="navigator.clipboard.writeText('{{ $generatedAuditCode }}'); copiedAudit = true; setTimeout(() => copiedAudit = false, 2000)"
                            class="p-3 rounded-xl bg-white/5 hover:bg-white/10 text-zinc-400 transition-all border border-white/5"
                        >
                            <flux:icon x-show="!copiedAudit" name="clipboard" variant="micro" class="size-5" />
                            <flux:icon x-show="copiedAudit" name="check" variant="micro" class="size-5 text-emerald-500" />
                        </button>
                    </div>
                </div>

                {{-- INSTRUÇÕES PARA O BANCO --}}
                <div class="bg-zinc-50 dark:bg-zinc-900/50 p-6 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-4">
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest text-left">Como o Auditor entra:</p>
                    <div class="space-y-3">
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-300 flex items-start gap-3">
                            <span class="size-4 shrink-0 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[8px] mt-0.5">1</span>
                            <span>Endereço: <br><span class="text-zinc-900 dark:text-zinc-100 font-mono break-all text-[10px]">http://localhost:8000/portal/banco</span></span>
                        </p>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-300 flex items-center gap-3">
                            <span class="size-4 shrink-0 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[8px]">2</span>
                            <span>NIF da Empresa: <span class="font-black text-zinc-900 dark:text-white">{{ $companyTaxNumber }}</span></span>
                        </p>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-300 flex items-center gap-3">
                            <span class="size-4 shrink-0 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[8px]">3</span>
                            <span>Token Único: <span class="font-black text-zinc-900 dark:text-white">{{ $generatedAuditCode }}</span></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-2">
                <flux:modal.close class="flex-1">
                    <flux:button variant="ghost" class="w-full font-black uppercase text-[10px]">Fechar</flux:button>
                </flux:modal.close>

                <button
                    x-data="{ copiedPortal: false }"
                    @click="navigator.clipboard.writeText('http://localhost:8000/portal/banco'); copiedPortal = true; setTimeout(() => copiedPortal = false, 2000)"
                    class="flex-[2] h-14 bg-zinc-900 text-white rounded-2xl font-black uppercase text-xs shadow-xl hover:bg-zinc-800 transition-all flex items-center justify-center gap-2 border-none"
                >
                    <flux:icon x-show="!copiedPortal" name="share" class="size-4" />
                    <flux:icon x-show="copiedPortal" name="check" class="size-4" />
                    <span x-text="copiedPortal ? 'Link Copiado!' : 'Partilhar Acesso'"></span>
                </button>
            </div>
        </div>
    </flux:modal>
    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Tesouraria Digital
        </p>
    </footer>
</div>

