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





    {{-- 3. GRELHA DE CONTAS (ESTILO DIGITAL WALLET PREMIUM) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($accounts as $account)
            <div class="relative group">
                {{-- Efeito de Sombra Colorida no Hover --}}
                <div class="absolute inset-0 rounded-[2.5rem] opacity-0 group-hover:opacity-20 transition-opacity duration-500 blur-xl" style="background-color: {{ $account->color }}"></div>

                <div
    x-data="{ expanded: false }"
    class="glass-card relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800
           rounded-[2.5rem] shadow-sm transition-all duration-500 group-hover:-translate-y-1
           group-hover:shadow-xl flex flex-col p-8 min-h-[380px]"
>

    {{-- Barra de cor superior --}}
    <div class="h-2 w-full rounded-t-[2.5rem] mb-5" style="background-color: {{ $account->color }}"></div>

    {{-- Topo --}}
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-start gap-4">
            <div class="size-14 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-inner"
                 style="background-color: {{ $account->color }}">
                <flux:icon name="{{ $account->getIcon() }}" variant="mini" class="size-7" />
            </div>

            <div class="space-y-1">
                <h4 class="font-black dark:text-white uppercase text-base tracking-tight leading-none">
                    {{ $account->name }}
                </h4>

                @if($account->bank_name)
                    <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-tight mt-1">
                        {{ $account->bank_name }} · {{ $account->country }}
                    </p>
                @endif

                <span class="inline-flex mt-2 px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500
                             text-[9px] font-black uppercase tracking-[0.15em] rounded-md border
                             border-zinc-200 dark:border-zinc-700">
                    {{ $account->type }}
                </span>

                {{-- Tags --}}
                @if($account->tags)
                    <div class="flex flex-wrap gap-1 mt-3">
                        @foreach($account->tags as $tag)
                            <span class="px-2 py-0.5 bg-brand-500/10 text-brand-600 text-[9px]
                                         font-black uppercase rounded-md">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Risco + Menu --}}
        <div class="flex flex-col items-end gap-3">
            @if(!is_null($account->risk_score))
                <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                    @if($account->risk_score < 30) bg-emerald-500/10 text-emerald-600
                    @elseif($account->risk_score < 60) bg-amber-500/10 text-amber-600
                    @else bg-red-500/10 text-red-600 @endif">
                    Risco {{ $account->risk_score }}%
                </span>
            @endif

            <flux:dropdown>
                <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm"
                             class="rounded-xl opacity-0 group-hover:opacity-100 transition-opacity" />
                <flux:menu class="min-w-[200px] p-2">
                    <flux:menu.item wire:click="edit({{ $account->id }})" icon="pencil-square"
                                    class="font-bold text-xs uppercase">Configurar Conta</flux:menu.item>
                    <flux:menu.item wire:click="openHistory({{ $account->id }})" icon="clock"
                                    class="font-bold text-xs uppercase">Histórico Financeiro</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click="delete({{ $account->id }})"
                                    wire:confirm="Eliminar conta e todo o histórico associado?"
                                    variant="danger" icon="trash"
                                    class="font-bold text-xs uppercase text-red-500">
                        Remover do Sistema
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>



    {{-- Compacto --}}
    <div class="space-y-1 text-[11px] text-zinc-500 font-mono"
         x-show="!expanded"
         x-transition.opacity>
        @if($account->iban)
            <p><span class="font-bold uppercase">IBAN:</span> {{ Str::limit($account->iban, 28) }}</p>
        @endif

        @if($account->swift)
            <p><span class="font-bold uppercase">SWIFT:</span> {{ $account->swift }}</p>
        @endif
    </div>

    {{-- Expandido --}}
    <div class="space-y-3 text-[11px] text-zinc-500 font-mono"
         x-show="expanded"
         x-transition>
        @if($account->iban)
            <p><span class="font-bold uppercase">IBAN:</span> {{ $account->iban }}</p>
        @endif

        @if($account->swift)
            <p><span class="font-bold uppercase">SWIFT:</span> {{ $account->swift }}</p>
        @endif

        @if($account->holder_name)
            <p><span class="font-bold uppercase">Titular:</span> {{ $account->holder_name }}</p>
        @endif

        @if($account->credit_limit)
            <p><span class="font-bold uppercase">Limite Crédito:</span>
                {{ number_format($account->credit_limit, 2, ',', ' ') }} €
            </p>
        @endif

        @if($account->notes)
            <p class="text-[10px] leading-relaxed">
                <span class="font-bold uppercase">Notas:</span> {{ $account->notes }}
            </p>
        @endif
    </div>

    {{-- Botão --}}
    <button
        @click="expanded = !expanded"
        class="mt-4 text-[10px] font-black uppercase tracking-widest text-brand-600 hover:text-brand-700">
        <span x-show="!expanded">Expandir detalhes</span>
        <span x-show="expanded">Recolher detalhes</span>
    </button>

    {{-- Saldos --}}
    <div class="mt-8 flex justify-between items-end">
        <div>
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Saldo Disponível</p>
            <h3 class="text-4xl font-black dark:text-white tracking-tighter italic leading-none">
                {{ number_format($account->current_balance, 2, ',', ' ') }} €
            </h3>
        </div>

        <div class="text-right">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Saldo Projetado</p>
            <p class="text-lg font-black text-amber-600 italic">
                {{ number_format($account->forecast_balance ?? $account->current_balance, 2, ',', ' ') }} €
            </p>
        </div>
    </div>
</div>

            </div>
        @empty
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

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">IBAN</flux:label>
                        <flux:input
                            wire:model="iban"
                            placeholder="PT50 0000 0000 0000 0000 0000 0"
                            class="font-mono text-xs !bg-white dark:!bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl h-12 shadow-sm"
                        />
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

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Tesouraria Digital
        </p>
    </footer>
</div>

