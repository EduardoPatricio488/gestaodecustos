{{-- MODAL DE REGISTO (ESTILO ASSINATURAS) --}}
<div
    x-data="{
        open: false,
        show() {
            this.open = true;
            document.documentElement.classList.add('overflow-hidden');
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        }
    }"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-show-add-investment.window="show()"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-close-add-investment.window="close()"
    x-on:keydown.escape.window="close()">

    {{-- OVERLAY PREMIUM --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/50 backdrop-blur-sm will-change-opacity">
    </div>

    {{-- WRAPPER --}}
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">

        {{-- CAIXA DO MODAL --}}
        <div
            x-show="open"
            @click.stop
            x-transition:enter="transition ease-out duration-100 transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.97] translate-y-1"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.97] translate-y-1"
            class="relative w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden max-h-[90vh] flex flex-col">

            {{-- FORM --}}
            <form
                wire:submit.prevent="save"
                x-data="{ selected: @entangle('type'), mode: 'unit' }"
                class="flex flex-col max-h-[90vh] overflow-hidden"
                autocomplete="off">

                {{-- SCROLL INTERNO --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-6">

                    @include('livewire.partials.onboarding-validation-errors')
                    {{-- CABEÇALHO PREMIUM --}}
                    <div class="flex items-center gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-md shadow-indigo-500/20">
                            <flux:icon name="{{ $editingId ? 'pencil-square' : 'plus' }}" class="size-5" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                                {{ $editingId ? 'Editar Ativo' : 'Novo Investimento' }}
                            </h2>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                                Terminal de Registo de Capital
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="close()"
                            class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition">
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </div>

                    {{-- BARRA DE PROGRESSO --}}
                    <div class="flex items-center gap-1.5 px-1">
                        @for ($s = 1; $s <= 4; $s++)
                            <div class="flex-1 h-0.5 rounded-full bg-indigo-600 {{ $s > 1 ? 'opacity-20' : '' }}"></div>
                        @endfor
                    </div>

                    {{-- PASSO 1: CLASSE --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                1
                            </div>
                            <flux:label class="text-[11px] font-black uppercase tracking-widest text-zinc-400">
                                Seleciona a Classe
                            </flux:label>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                            @foreach([
                                'Acao'   => ['chart-bar-square', 'Ação'],
                                'Cripto' => ['cube',             'Cripto'],
                                'ETF'    => ['squares-2x2',      'ETF'],
                                'Fundo'  => ['briefcase',        'Fundo'],
                                'Divida' => ['building-library', 'Dívida'],
                            ] as $key => [$icon, $label])

                                <button type="button"
                                    @click="selected = '{{ $key }}'; $wire.setType('{{ $key }}')"
                                    :class="selected === '{{ $key }}'
                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 ring-2 ring-indigo-500/20'
                                        : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50'"
                                    class="relative p-4 border-2 rounded-[2rem] flex flex-col items-center gap-2 transition-all duration-300 group">

                                    {{-- CHECK --}}
                                    <div x-show="selected === '{{ $key }}'" x-transition
                                         class="absolute top-2 right-2 size-4 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <flux:icon name="check" class="size-2.5 text-white" />
                                    </div>

                                    {{-- ÍCONE --}}
                                    <flux:icon name="{{ $icon }}"
                                        ::class="selected === '{{ $key }}'
                                            ? 'text-indigo-600 dark:text-indigo-400'
                                            : 'text-zinc-400'"
                                        class="size-6 group-hover:scale-110 transition-transform" />

                                    {{-- LABEL --}}
                                    <span class="text-[10px] font-black uppercase tracking-tight"
                                          ::class="selected === '{{ $key }}'
                                            ? 'text-indigo-600 dark:text-indigo-400'
                                            : 'text-zinc-500'">
                                        {{ $label }}
                                    </span>
                                </button>

                            @endforeach
                        </div>
                    </div>
                    {{-- PASSO 2: IDENTIFICAÇÃO --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                2
                            </div>
                            <flux:label class="text-[11px] font-black uppercase tracking-widest text-zinc-400">
                                Identificação do Ativo
                            </flux:label>
                        </div>

                        {{-- DÍVIDA --}}
                        @if($type === 'Divida')

                            {{-- TOGGLE CA / CT --}}
                            <div class="flex items-center gap-1 p-1 bg-zinc-100 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                                <button type="button" wire:click="$set('product_type', 'CA')"
                                    class="flex-1 flex items-center justify-center gap-2 h-11 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                                        {{ $product_type === 'CA' ? 'bg-blue-600 text-white shadow-lg' : 'text-zinc-400 hover:text-zinc-600' }}">
                                    <flux:icon name="building-library" class="size-3.5" />
                                    Certificados de Aforro
                                </button>

                                <button type="button" wire:click="$set('product_type', 'CT')"
                                    class="flex-1 flex items-center justify-center gap-2 h-11 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                                        {{ $product_type === 'CT' ? 'bg-blue-600 text-white shadow-lg' : 'text-zinc-400 hover:text-zinc-600' }}">
                                    <flux:icon name="document-text" class="size-3.5" />
                                    Certificados do Tesouro
                                </button>
                            </div>

                            {{-- CAMPOS CA / CT --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:input wire:model="name" label="Nome / Descrição"
                                    placeholder="{{ $product_type === 'CA' ? 'Ex: Certificados de Aforro Série F' : 'Ex: Certificados do Tesouro Poupança Crescimento' }}"
                                    class="font-medium !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />

                                <flux:input wire:model="series" label="Série"
                                    placeholder="Ex: Série F"
                                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:input wire:model="issuer" label="Emitente"
                                    placeholder="IGCP / Estado Português"
                                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />

                                <flux:input wire:model="broker" label="Plataforma"
                                    placeholder="AforroNet, CTT..."
                                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                            </div>

                            {{-- TAXA + CÓDIGO --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <flux:input wire:model="interest_rate" type="number" step="0.001"
                                        label="Taxa Base Atual — Euribor 3M (%)"
                                        placeholder="Ex: 2.500"
                                        class="font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Máx. 2,5% · Mín. 0% · Prémio calculado automaticamente.
                                    </p>
                                </div>

                                <div class="space-y-1.5">
                                    <flux:input wire:model="symbol" label="Referência / Código"
                                        placeholder="Ex: CA-F ou CT-PC"
                                        class="uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Código interno da subscrição.
                                    </p>
                                </div>
                            </div>

                            {{-- BANNERS --}}
                            @if($product_type === 'CA')
                                <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-950/30 rounded-2xl border border-blue-100 dark:border-blue-900/30">
                                    <flux:icon name="information-circle" class="size-4 text-blue-500 shrink-0 mt-0.5" />
                                    <div class="text-[10px] text-blue-700 dark:text-blue-300 leading-relaxed space-y-1">
                                        <p><strong>Capitalização trimestral</strong> — juros somados ao saldo.</p>
                                        <p><strong>IRS 28%</strong> aplicado antes de capitalizar.</p>
                                        <p><strong>Prémio de permanência</strong> calculado automaticamente.</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-3 p-4 bg-indigo-50 dark:bg-indigo-950/30 rounded-2xl border border-indigo-100 dark:border-indigo-900/30">
                                    <flux:icon name="information-circle" class="size-4 text-indigo-500 shrink-0 mt-0.5" />
                                    <div class="text-[10px] text-indigo-700 dark:text-indigo-300 leading-relaxed space-y-1">
                                        <p><strong>Juro anual não capitalizável</strong>.</p>
                                        <p><strong>IRS 28%</strong> sobre o juro bruto.</p>
                                        <p>Juro líquido registado em <strong>Rendimentos Recebidos</strong>.</p>
                                    </div>
                                </div>
                            @endif

                        @else
                            {{-- CAMPOS NORMAIS PARA OUTROS TIPOS --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:input wire:model="symbol" label="Ticker / Símbolo"
                                    placeholder="Ex: VUAA.DE"
                                    class="uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />

                                <flux:input wire:model="isin" label="Código ISIN"
                                    placeholder="Ex: IE00BFMXXD54"
                                    class="uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                            </div>

                            <flux:input wire:model="name" label="Nome do Ativo"
                                placeholder="Ex: Vanguard S&P 500 UCITS ETF"
                                class="font-medium !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 shadow-inner" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    @if($type === 'Acao')
                                        <flux:input wire:model="exchange" label="Bolsa (Exchange)"
                                            placeholder="NYSE, NASDAQ..."
                                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    @elseif($type === 'Cripto')
                                        <flux:input wire:model="network" label="Rede (Blockchain)"
                                            placeholder="ERC-20, Solana..."
                                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    @else
                                        <flux:input wire:model="provider" label="Gestora / Provider"
                                            placeholder="Vanguard, iShares..."
                                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    @endif
                                </div>

                                <flux:input wire:model="broker" label="Corretora"
                                    placeholder="Ex: XTB, DEGIRO..."
                                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                            </div>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-indigo-100 dark:border-indigo-900/40 bg-indigo-50/40 dark:bg-indigo-950/20 p-4 space-y-2">
                        <label class="block text-[11px] font-black uppercase tracking-wider text-zinc-500">Conta bancária / Corretora *</label>
                        <select wire:model="bank_account_id" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-3 text-sm text-zinc-900 dark:text-white">
                            <option value="">Selecionar conta associada</option>
                            @foreach($bankAccounts as $bankAccount)
                                <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name ?: $bankAccount->name }} — {{ $bankAccount->name }}</option>
                            @endforeach
                        </select>
                        @error('bank_account_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- PASSO 3: DADOS DA TRANSAÇÃO --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                3
                            </div>
                            <flux:label class="text-[11px] font-black uppercase tracking-widest text-zinc-400">
                                Dados da Transação
                            </flux:label>
                        </div>

                        <div class="p-6 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-[2rem] border border-indigo-100 dark:border-indigo-900/30 space-y-5">

                            {{-- DATA --}}
                            <flux:input wire:model="operation_date" type="date"
                                label="Data de Subscrição"
                                class="font-black !bg-white dark:!bg-zinc-950" />
                            {{-- MODO UNITÁRIO / TOTAL --}}
                            @if($type !== 'Divida')

                                <div class="flex items-center gap-1 p-1 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                                    <button type="button" @click="mode = 'unit'"
                                        :class="mode === 'unit'
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                            : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'"
                                        class="flex-1 flex items-center justify-center gap-2 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        <flux:icon name="calculator" class="size-3.5" />
                                        Preço por unidade
                                    </button>

                                    <button type="button" @click="mode = 'total'"
                                        :class="mode === 'total'
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                            : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'"
                                        class="flex-1 flex items-center justify-center gap-2 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        <flux:icon name="banknotes" class="size-3.5" />
                                        Valor que investi
                                    </button>
                                </div>

                                {{-- MODO UNITÁRIO --}}
                                <div x-show="mode === 'unit'" x-transition class="space-y-4">

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <flux:input wire:model.live="quantity" type="number" step="0.00001"
                                            label="Quantidade de Títulos Comprados"
                                            placeholder="Ex: 4"
                                            class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Número exato de unidades adquiridas.
                                        </p>
                                    </div>

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <flux:input wire:model.live="average_price" type="number" step="0.00001"
                                            label="Preço de Compra por Unidade (€)"
                                            placeholder="Ex: 125,77"
                                            class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Valor de 1 título no momento da compra.
                                        </p>
                                    </div>
                                </div>

                                {{-- MODO TOTAL --}}
                                <div x-show="mode === 'total'" x-transition class="space-y-4">

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">

                                        <flux:input
                                            wire:model.live="total_amount"
                                            type="number"
                                            step="0.01"
                                            label="Valor Total que Investiste (€)"
                                            placeholder="Ex: 503,08"
                                            class="font-black !bg-zinc-50 dark:!bg-zinc-950"
                                        />

                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Montante total debitado na corretora antes de taxas.
                                        </p>
                                    </div>

                                    {{-- Preço por unidade para cálculo --}}
                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <flux:input wire:model.live="average_price" type="number" step="0.00001"
                                            label="Preço de Compra por Unidade (€)"
                                            placeholder="Ex: 125,77"
                                            class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Necessário para calcular a quantidade: Total ÷ Preço.
                                        </p>
                                    </div>

                                    {{-- Quantidade calculada --}}
                                    @if($total_amount && $average_price && (float)$average_price > 0)
                                        <div class="flex items-center justify-between px-4 py-3 bg-indigo-600/10 dark:bg-indigo-500/10 rounded-2xl border border-indigo-200 dark:border-indigo-800">
                                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                                                <flux:icon name="arrow-path" class="size-3.5" />
                                                <span class="text-[10px] font-black uppercase tracking-widest">Quantidade calculada</span>
                                            </div>
                                            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400 italic tracking-tighter">
                                                ≈ {{ number_format(((float)$total_amount - (float)($fees ?? 0)) / (float)$average_price, 4, ',', ' ') }} unidades
                                            </span>
                                        </div>
                                    @endif

                                </div>

                                {{-- TAXAS --}}
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                    <flux:input wire:model.live="fees" type="number" step="0.01"
                                        label="Taxas / Comissões da Corretora (€)"
                                        placeholder="Ex: 0,00"
                                        class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Custo da execução da ordem. <span class="text-emerald-500 font-bold">XTB, Trading212 e DEGIRO Free: normalmente 0 €.</span>
                                    </p>
                                </div>

                            @else
                                {{-- DÍVIDA PÚBLICA --}}
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                    <flux:input wire:model.live="quantity" type="number" step="1"
                                        label="Capital Investido (€) = N.º de Títulos"
                                        placeholder="Ex: 5000"
                                        class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Cada título vale exatamente <strong>1 €</strong>.
                                    </p>
                                </div>

                                {{-- Preview --}}
                                @if($quantity && (float)$quantity > 0)
                                    <div class="flex items-center justify-between px-4 py-3 bg-blue-600/10 dark:bg-blue-500/10 rounded-2xl border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                                            <flux:icon name="building-library" class="size-3.5" />
                                            <span class="text-[10px] font-black uppercase tracking-widest">Capital subscrito</span>
                                        </div>
                                        <span class="text-sm font-black text-blue-600 dark:text-blue-400 italic tracking-tighter">
                                            {{ number_format((float)$quantity, 0, ',', ' ') }} títulos · {{ number_format((float)$quantity, 2, ',', ' ') }} €
                                        </span>
                                    </div>

                                    {{-- Simulação --}}
                                    @if($interest_rate && (float)$interest_rate > 0)
                                        @php
                                            $yearsComplete  = $operation_date ? (int)\Carbon\Carbon::parse($operation_date)->diffInYears(now()) : 0;
                                            $loyaltyPreview = match(true) {
                                                $yearsComplete >= 5 => 1.25,
                                                $yearsComplete >= 4 => 1.00,
                                                $yearsComplete >= 3 => 0.75,
                                                $yearsComplete >= 2 => 0.50,
                                                $yearsComplete >= 1 => 0.25,
                                                default             => 0.00,
                                            };
                                            $totalRateGross   = (float)$interest_rate + $loyaltyPreview;
                                            $annualNetPreview = (float)$quantity * $totalRateGross / 100 * 0.72;
                                        @endphp

                                        <div class="p-4 bg-blue-50/50 dark:bg-blue-950/20 rounded-2xl border border-blue-100 dark:border-blue-900/30 space-y-3">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-blue-500 italic">Simulação Atual</p>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="text-center p-3 bg-white dark:bg-zinc-900 rounded-xl border border-blue-100 dark:border-blue-900/20">
                                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-wider">Taxa Total Bruta</p>
                                                    <p class="text-lg font-black text-blue-600 dark:text-blue-400 italic tracking-tighter mt-1">
                                                        {{ number_format($totalRateGross, 3, ',', '') }}%
                                                    </p>
                                                </div>

                                                <div class="text-center p-3 bg-white dark:bg-zinc-900 rounded-xl border border-blue-100 dark:border-blue-900/20">
                                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-wider">Juro Líquido / Ano</p>
                                                    <p class="text-lg font-black text-emerald-500 italic tracking-tighter mt-1">
                                                        +{{ number_format($annualNetPreview, 2, ',', ' ') }} €
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif

                        </div>
                    </div>
                    {{-- PASSO 4: RESUMO --}}
                    @if($quantity && $average_price && (float)$quantity > 0)
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                    4
                                </div>
                                <flux:label class="text-[11px] font-black uppercase tracking-widest text-zinc-400">
                                    Resumo
                                </flux:label>
                            </div>

                            <div class="p-6 bg-zinc-950 rounded-[2.5rem] flex items-center justify-between border border-zinc-800 shadow-2xl">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black uppercase text-zinc-500 tracking-[0.2em] italic">
                                        Investimento Total
                                    </p>

                                    <p class="text-white text-xs font-medium">
                                        <span class="text-indigo-400 font-black">
                                            {{ number_format((float)$quantity, 4, ',', ' ') }}
                                        </span> unidades

                                        <span class="text-zinc-500 mx-1">×</span>

                                        <span class="text-indigo-400 font-black">
                                            {{ number_format((float)$average_price, 2, ',', ' ') }} €
                                        </span>

                                        @if(!empty($fees) && (float)$fees > 0)
                                            <span class="text-zinc-500 mx-1">+</span>
                                            <span class="text-amber-400 font-black">
                                                {{ number_format((float)$fees, 2, ',', ' ') }} € taxas
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-2xl sm:text-3xl font-black text-white italic tracking-tighter">
                                        {{ number_format((float)$quantity * (float)$average_price + (float)($fees ?? 0), 2, ',', ' ') }} €
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- BOTÕES FINAIS (ESTILO PREMIUM) --}}
                <div class="shrink-0 p-5 sm:p-6 pt-4 flex gap-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <button
                        type="button"
                        @click="close()"
                        class="flex-1 uppercase font-black text-[10px] h-12 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="flex-[2] bg-indigo-600 h-12 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 text-white hover:bg-indigo-500 active:scale-95 transition-all disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Confirmar Posição</span>
                        <span wire:loading wire:target="save">A Processar...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
{{-- /MODAL --}}


