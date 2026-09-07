<div
    class="fixed inset-0 z-[999] flex items-center justify-center p-4"
    x-data="{ open: @entangle('show') }"
    x-show="open"
    x-cloak
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Painel --}}
    <div class="relative w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden"
         style="max-height: 90vh;">

        {{-- Barra de progresso no topo --}}
        <div class="h-1.5 bg-zinc-100 dark:bg-zinc-800">
            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-700 ease-out rounded-full"
                 style="width: {{ (($step - 1) / ($totalSteps - 1)) * 100 }}%"></div>
        </div>

        <div class="overflow-y-auto custom-scrollbar" style="max-height: calc(90vh - 6px)">

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 1: BOAS-VINDAS                --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 1)
            <div class="p-10 space-y-8">
                <div class="text-center space-y-4">
                    <div class="size-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-[2rem] flex items-center justify-center mx-auto shadow-2xl shadow-indigo-500/30">
                        <flux:icon name="sparkles" class="size-10 text-white" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-black italic tracking-tighter dark:text-white">
                            Bem-vindo, {{ auth()->user()->name }}! 👋
                        </h1>
                        <p class="text-zinc-500 font-medium mt-2">Vamos configurar o teu espaço financeiro em menos de 2 minutos.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/30 rounded-2xl p-5">
                        <flux:icon name="chart-bar-square" class="size-7 text-indigo-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Dashboard Financeiro</p>
                        <p class="text-xs text-zinc-500 mt-1">Visão completa das tuas receitas, despesas e investimentos.</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl p-5">
                        <flux:icon name="arrow-trending-up" class="size-7 text-emerald-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Controlo de Receitas</p>
                        <p class="text-xs text-zinc-500 mt-1">Regista salários, rendimentos extra e acompanha tudo.</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 rounded-2xl p-5">
                        <flux:icon name="sparkles" class="size-7 text-amber-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Scanner IA de Faturas</p>
                        <p class="text-xs text-zinc-500 mt-1">Fotografa qualquer fatura e a IA extrai os dados automaticamente.</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/30 rounded-2xl p-5">
                        <flux:icon name="globe-alt" class="size-7 text-purple-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Finance Connect</p>
                        <p class="text-xs text-zinc-500 mt-1">Rede social financeira — partilha conquistas e segue amigos.</p>
                    </div>
                    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800/30 rounded-2xl p-5">
                        <flux:icon name="trophy" class="size-7 text-rose-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Metas & Gamificação</p>
                        <p class="text-xs text-zinc-500 mt-1">Define objetivos, ganha XP e sobe de nível financeiro.</p>
                    </div>
                    <div class="bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800/30 rounded-2xl p-5">
                        <flux:icon name="building-office" class="size-7 text-sky-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Área Empresarial</p>
                        <p class="text-xs text-zinc-500 mt-1">Faturação, clientes, P&L e gestão completa de negócio.</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Passo 1 de {{ $totalSteps }}</p>
                    <button wire:click="nextStep"
                        class="flex items-center gap-2 px-8 h-12 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-lg shadow-indigo-500/20 transition-all hover:scale-[1.02]">
                        Começar
                        <flux:icon name="arrow-right" class="size-4" />
                    </button>
                </div>
            </div>
            @endif










{{-- ─────────────────────────────────── --}}
{{-- PASSO 2: FONTE DE RENDIMENTO        --}}
{{-- ─────────────────────────────────── --}}
@if($step === 2)
<div class="p-10 space-y-8">

    {{-- HEADER --}}
    <div class="flex items-center gap-4">
        <div class="size-14 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
            <flux:icon name="banknotes" class="size-7 text-white" />
        </div>

        <div>
            <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">
                @switch($salarySource)
                    @case('emprego')
                        Quais são os dados do teu salário?
                        @break
                    @case('freelance')
                        Quais são os dados do teu trabalho independente?
                        @break
                    @case('investimento')
                        Quais são os teus rendimentos de investimento?
                        @break
                    @case('imobiliario')
                        Quais são os teus rendimentos imobiliários?
                        @break
                    @case('reforma')
                        Quais são os dados da tua pensão?
                        @break
                    @case('bolsa')
                        Quais são os dados da tua bolsa?
                        @break
                    @default
                        Vamos configurar o teu rendimento
                @endswitch
            </h2>

            <p class="text-sm text-zinc-500 mt-0.5">
                @switch($salarySource)
                    @case('emprego')
                        Regista o teu salário para calcular automaticamente o teu rendimento líquido.
                        @break
                    @case('freelance')
                        Indica quanto recebes habitualmente através do teu trabalho independente.
                        @break
                    @case('investimento')
                        Regista dividendos, juros ou outros rendimentos provenientes dos teus investimentos.
                        @break
                    @case('imobiliario')
                        Adiciona os rendimentos que recebes através de imóveis ou exploração imobiliária.
                        @break
                    @case('reforma')
                        Regista o valor da tua reforma ou pensão.
                        @break
                    @case('bolsa')
                        Indica o valor e a entidade responsável pela tua bolsa ou apoio à formação.
                        @break
                    @default
                        Adiciona qualquer outra fonte de rendimento que tenhas.
                @endswitch
            </p>
        </div>
    </div>


    {{-- FONTE DE RENDIMENTO --}}
    <div class="space-y-5">

        <div class="relative">
            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                Fonte de Rendimento
            </label>

            <select
                wire:model.live="salarySource"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all appearance-none cursor-pointer"
            >
                <option value="emprego">💼 Emprego (Contrato de trabalho)</option>
                <option value="freelance">💻 Trabalho Independente / Recibos Verdes</option>
                <option value="investimento">📈 Rendimentos de Investimentos / Dividendos</option>
                <option value="imobiliario">🏠 Rendas e Exploração Imobiliária</option>
                <option value="reforma">👴 Reforma / Pensão de Velhice</option>
                <option value="bolsa">🎓 Bolsa de Estudo / Apoio à Formação</option>
                <option value="outro">✨ Outra Fonte</option>
            </select>
        </div>


       {{-- ═══════════════════════════════════ --}}
{{-- 💼 EMPREGO                          --}}
{{-- ═══════════════════════════════════ --}}
@if($salarySource === 'emprego')

    {{-- DESCRIÇÃO --}}
    <div class="relative">
        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
            Descrição
        </label>

        <input
            type="text"
            wire:model="salaryDescription"
            placeholder="Ex: Salário Mensal - Empresa X"
            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all"
        >
    </div>


    {{-- BRUTO / LÍQUIDO --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- VALOR BRUTO --}}
        <div class="relative group">

            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                Valor Bruto (€)
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                wire:model.live="salaryGross"
                placeholder="0,00"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-indigo-500 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500 outline-none transition-all"
            >

        </div>


        {{-- VALOR LÍQUIDO --}}
        <div class="relative group">

            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                Valor Líquido (€)
            </label>

            <input
                type="number"
                step="0.01"
                wire:model.live="salaryAmount"
                placeholder="0,00"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600 outline-none transition-all"
            >

        </div>

    </div>


    {{-- ═══════════════════════════════════ --}}
    {{-- 🍽️ SUBSÍDIO DE ALIMENTAÇÃO         --}}
    {{-- ═══════════════════════════════════ --}}

    <div class="mt-2 p-5 bg-amber-50 dark:bg-amber-500/10 rounded-[1.5rem] border border-amber-200 dark:border-amber-500/20">

        <div class="flex items-center gap-2 mb-4">

            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                <span class="text-lg">🍽️</span>
            </div>

            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-amber-700 dark:text-amber-300">
                    Subsídio de Alimentação
                </h3>

                <p class="text-[10px] text-amber-600/70 dark:text-amber-400/70 mt-0.5">
                    Personaliza o valor recebido por dia
                </p>
            </div>

        </div>


        {{-- VALOR / DIAS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- VALOR POR DIA --}}
            <div class="relative">

                <label class="absolute left-4 -top-2.5 px-2 bg-amber-50 dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400 z-10">
                    Valor por dia
                </label>

                <div class="relative">

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model.live="mealAllowance"
                        placeholder="0,00"
                        class="w-full bg-white dark:bg-zinc-900 border-2 border-amber-200 dark:border-amber-500/20 focus:border-amber-500 rounded-2xl py-4 px-5 pr-16 text-xl font-black text-zinc-700 dark:text-white outline-none transition-all"
                    >

                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-zinc-400">
                        €/dia
                    </span>

                </div>

            </div>


            {{-- DIAS TRABALHADOS --}}
            <div class="relative">

                <label class="absolute left-4 -top-2.5 px-2 bg-amber-50 dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400 z-10">
                    Dias trabalhados
                </label>

                <div class="relative">

                    <input
                        type="number"
                        min="1"
                        max="31"
                        wire:model.live="workingDays"
                        placeholder="22"
                        class="w-full bg-white dark:bg-zinc-900 border-2 border-amber-200 dark:border-amber-500/20 focus:border-amber-500 rounded-2xl py-4 px-5 pr-16 text-xl font-black text-zinc-700 dark:text-white outline-none transition-all"
                    >

                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-zinc-400">
                        dias
                    </span>

                </div>

            </div>

        </div>


        {{-- TOTAL MENSAL --}}
        <div class="mt-4 bg-white/70 dark:bg-zinc-900/70 rounded-2xl p-4 border border-amber-200/70 dark:border-amber-500/10">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">
                        Subsídio mensal estimado
                    </p>

                    <p class="text-[10px] text-zinc-400 mt-1">
                        {{ number_format((float) $mealAllowance, 2, ',', ' ') }} €
                        ×
                        {{ $workingDays }} dias
                    </p>

                </div>

                <p class="text-xl font-black text-amber-700 dark:text-amber-300">
                    {{ number_format(
                        (float) $mealAllowance * (int) $workingDays,
                        2,
                        ',',
                        ' '
                    ) }} €
                </p>

            </div>

        </div>

    </div>


    {{-- ═══════════════════════════════════ --}}
    {{-- AUDIT                              --}}
    {{-- ═══════════════════════════════════ --}}

    @if($salaryGross > 0 && $salaryAmount > 0)

        <div class="p-6 bg-zinc-950 text-white rounded-[2rem] border border-white/10 shadow-2xl space-y-4">

            <div class="flex items-center justify-between border-b border-white/10 pb-3">

                <div class="flex items-center gap-2">

                    <flux:icon
                        name="calculator"
                        class="size-4 text-emerald-400"
                    />

                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 italic">
                        Breakdown de Descontos
                    </h3>

                </div>

                <span class="text-[9px] font-black bg-white/5 px-2 py-0.5 rounded uppercase text-zinc-400">
                    Audit Automático
                </span>

            </div>


            <div class="space-y-2 font-mono text-[11px]">

                {{-- SEGURANÇA SOCIAL --}}
                <div class="flex justify-between text-zinc-400">

                    <span>
                        SEGURANÇA SOCIAL
                    </span>

                    <span class="font-bold text-red-400">
                        - {{ number_format(
                            $calculatedSS,
                            2,
                            ',',
                            ' '
                        ) }} €
                    </span>

                </div>


                {{-- IRS --}}
                <div class="flex justify-between text-zinc-400">

                    <span>
                        RETENÇÃO IRS
                    </span>

                    <span class="font-bold text-red-400">
                        - {{ number_format(
                            $calculatedIRS,
                            2,
                            ',',
                            ' '
                        ) }} €
                    </span>

                </div>


                {{-- SUBSÍDIO --}}
                <div class="flex justify-between text-zinc-400">

                    <span>
                        SUBSÍDIO ALIMENTAÇÃO
                    </span>

                    <span class="font-bold text-emerald-400">
                        + {{ number_format(
                            $calculatedSA,
                            2,
                            ',',
                            ' '
                        ) }} €
                    </span>

                </div>


                {{-- TOTAL --}}
                <div class="pt-3 border-t border-dashed border-white/10 flex justify-between items-center">

                    <span class="text-zinc-500 font-black uppercase text-[9px]">
                        Líquido estimado:
                    </span>

                    <span class="text-lg font-black text-white italic">
                        {{ number_format(
                            $salaryAmount,
                            2,
                            ',',
                            ' '
                        ) }} €
                    </span>

                </div>

            </div>

        </div>




    {{-- ═══════════════════════════════════ --}}
    {{-- DIA DE RECEBIMENTO                 --}}
    {{-- ═══════════════════════════════════ --}}

    <div class="relative w-40">

        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
            Dia de Recebimento
        </label>

        <input
            type="number"
            min="1"
            max="31"
            wire:model.live="salaryDay"
            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-xl font-black text-center dark:text-white outline-none transition-all"
        >

    </div>

@endif


        {{-- ═══════════════════════════════════ --}}
        {{-- 💻 FREELANCE                         --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'freelance')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Atividade
                    </label>

                    <input
                        type="text"
                        wire:model="freelanceActivity"
                        placeholder="Ex: Designer, Programador..."
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none focus:border-emerald-500"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Tipo de rendimento
                    </label>

                    <select
                        wire:model="freelanceType"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                    >
                        <option value="prestacao_servicos">Prestação de Serviços</option>
                        <option value="vendas">Vendas</option>
                        <option value="consultoria">Consultoria</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Faturação média (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="freelanceGross"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Despesas médias (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="freelanceExpenses"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500"
                    >
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Retenção na fonte (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="freelanceWithholding"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Periodicidade
                    </label>

                    <select wire:model="freelanceFrequency"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="mensal">Mensal</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="anual">Anual</option>
                        <option value="variavel">Variável</option>
                    </select>
                </div>

            </div>

            <div class="p-5 bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800/30 rounded-2xl">

                <p class="text-xs font-black text-sky-700 dark:text-sky-300 uppercase tracking-wider">
                    💡 Rendimento estimado
                </p>

                <p class="text-2xl font-black text-sky-700 dark:text-sky-300 mt-1">
{{ number_format(max(0, (float)$freelanceGross - (float)$freelanceExpenses - (float)$freelanceWithholding), 2, ',', ' ') }} €                </p>

                <p class="text-[10px] text-sky-600/70 mt-1">
                    Faturação − despesas − retenção
                </p>

            </div>


        {{-- ═══════════════════════════════════ --}}
        {{-- 📈 INVESTIMENTOS                     --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'investimento')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Tipo de Rendimento
                    </label>

                    <select wire:model="investmentType"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="dividendos">💰 Dividendos</option>
                        <option value="juros">🏦 Juros</option>
                        <option value="mais_valias">📊 Mais-valias</option>
                        <option value="fundos">📈 Fundos / ETFs</option>
                        <option value="cripto">₿ Criptoativos</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Investimento / Entidade
                    </label>

                    <input
                        type="text"
                        wire:model="investmentName"
                        placeholder="Ex: ETF S&P 500, Banco X..."
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                    >
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Rendimento (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="investmentAmount"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Periodicidade
                    </label>

                    <select wire:model="investmentFrequency"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="mensal">Mensal</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="semestral">Semestral</option>
                        <option value="anual">Anual</option>
                        <option value="variavel">Variável</option>
                    </select>
                </div>

            </div>

            <div class="relative">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                    Custos / Comissões (€)
                </label>

                <input
                    type="number"
                    step="0.01"
                    wire:model="investmentExpenses"
                    placeholder="0,00"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500"
                >
            </div>

{{-- 🏠 RENDIMENTOS IMOBILIÁRIOS --}}
@elseif($salarySource === 'imobiliario')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- IDENTIFICAÇÃO --}}
        <div class="relative sm:col-span-2">
            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">Identificação</label>
            <input type="text" wire:model="propertyDescription" placeholder="Ex: Apartamento Lisboa" class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none" autocomplete="off">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- RENDIMENTO (CAIXA 1) --}}
        <div class="relative group">
            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">Rendimento (€)</label>
            <input
                type="number"
                step="0.01"
                wire:model.live="rentalGross"
                placeholder="0,00"
                class="w-full h-14 bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600 outline-none"
                autocomplete="off"
            >
        </div>

        {{-- DESPESAS (CAIXA 2) --}}
        <div class="relative group">
            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">Despesas (€)</label>
            <input
                type="number"
                step="0.01"
                wire:model.live="rentalExpenses" {{-- GARANTE QUE É ESTE MODELO --}}
                placeholder="0,00"
                class="w-full h-14 bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-indigo-500 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500 outline-none"
                autocomplete="off"
            >
        </div>
    </div>

    {{-- QUADRO DE RESULTADO (APENAS LEITURA) --}}
    <div class="p-5 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 rounded-2xl">
        <p class="text-xs font-black text-amber-700 dark:text-amber-300 uppercase tracking-wider">🏠 Rendimento Líquido Estimado</p>
        <p class="text-3xl font-black text-amber-700 dark:text-amber-300 mt-1 tabular-nums">
            {{ number_format($this->salaryAmount, 2, ',', ' ') }} €
        </p>
    </div>


        {{-- ═══════════════════════════════════ --}}
        {{-- 👴 REFORMA                           --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'reforma')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Tipo de Pensão
                    </label>

                    <select wire:model="pensionType"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="velhice">👴 Pensão de Velhice</option>
                        <option value="invalidez">♿ Pensão de Invalidez</option>
                        <option value="sobrevivencia">❤️ Pensão de Sobrevivência</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Entidade Pagadora
                    </label>

                    <input
                        type="text"
                        wire:model="pensionEntity"
                        placeholder="Ex: Segurança Social"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                    >
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Valor Bruto (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="pensionGross"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Valor Líquido (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="pensionAmount"
                        placeholder="0,00"
                        class="w-full bg-white dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

            </div>


        {{-- ═══════════════════════════════════ --}}
        {{-- 🎓 BOLSA                             --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'bolsa')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Tipo de Bolsa
                    </label>

                    <select wire:model="scholarshipType"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="estudo">🎓 Bolsa de Estudo</option>
                        <option value="formacao">📚 Apoio à Formação</option>
                        <option value="investigacao">🔬 Bolsa de Investigação</option>
                        <option value="estagio">💼 Bolsa de Estágio</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Entidade
                    </label>

                    <input
                        type="text"
                        wire:model="scholarshipEntity"
                        placeholder="Ex: Universidade, IEFP..."
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                    >
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Valor (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="scholarshipAmount"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Periodicidade
                    </label>

                    <select wire:model="scholarshipFrequency"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="mensal">Mensal</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>

            </div>

            <div class="relative">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                    Data de fim (opcional)
                </label>

                <input
                    type="date"
                    wire:model="scholarshipEndDate"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                >
            </div>


        {{-- ═══════════════════════════════════ --}}
        {{-- ✨ OUTRO                             --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'outro')

            <div class="relative">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                    Nome da Fonte
                </label>

                <input
                    type="text"
                    wire:model="otherSourceDetail"
                    placeholder="Ex: Subsídio, Mesada, Pensão, Comissão..."
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Valor (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="otherAmount"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Periodicidade
                    </label>

                    <select wire:model="otherFrequency"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="mensal">Mensal</option>
                        <option value="semanal">Semanal</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="anual">Anual</option>
                        <option value="variavel">Variável</option>
                    </select>
                </div>

            </div>

            <div class="relative w-40">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                    Dia de Recebimento
                </label>

                <input
                    type="number"
                    min="1"
                    max="31"
                    wire:model="salaryDay"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-center dark:text-white"
                >
            </div>

        @endif

    </div>


    {{-- AVISO DE PRIVACIDADE --}}
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl p-4 flex items-start gap-3">

        <flux:icon name="shield-check" class="size-5 text-emerald-500 shrink-0 mt-0.5" />

        <p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
            Os teus dados são privados e só tu (e quem convidares para o teu workspace) os pode ver.
        </p>

    </div>


    {{-- FOOTER --}}
    <div class="flex items-center justify-between pt-2">
    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
        Passo {{ $step }} de {{ $totalSteps }}
    </p>

    <div class="flex items-center gap-3">
        {{-- BOTÃO VOLTAR --}}
        <button wire:click="previousStep"
            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors flex items-center gap-2">
            <flux:icon name="arrow-left" class="size-3" />
            Voltar
        </button>

        <button wire:click="skipStep"
            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors">
            Saltar
        </button>

        <button wire:click="nextStep"
            class="flex items-center gap-2 px-8 h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02]">
            Guardar e Continuar
            <flux:icon name="arrow-right" class="size-4" />
        </button>
    </div>
</div>

</div>
@endif



















            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 3: WORKSPACE                  --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 3)
            <div class="p-10 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
                        <flux:icon name="user-group" class="size-7 text-white" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">O teu espaço financeiro</h2>
                        <p class="text-sm text-zinc-500 mt-0.5">Dá um nome ao teu workspace — pode ser só para ti ou partilhado com família/sócios.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-indigo-600 z-10">Nome do Workspace</label>
                        <input type="text" wire:model="workspaceName"
                            placeholder="Ex: Família Silva, As Minhas Finanças, Startup XYZ..."
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-indigo-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button type="button" wire:click="$set('workspaceName', 'As Minhas Finanças')"
                            class="p-4 bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 hover:border-indigo-400 rounded-2xl text-left transition-all group">
                            <p class="text-lg mb-1">👤</p>
                            <p class="text-xs font-black dark:text-white group-hover:text-indigo-600 transition-colors">Pessoal</p>
                            <p class="text-[10px] text-zinc-400">Só para mim</p>
                        </button>
                        <button type="button" wire:click="$set('workspaceName', 'Família')"
                            class="p-4 bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 hover:border-indigo-400 rounded-2xl text-left transition-all group">
                            <p class="text-lg mb-1">👨‍👩‍👧</p>
                            <p class="text-xs font-black dark:text-white group-hover:text-indigo-600 transition-colors">Familiar</p>
                            <p class="text-[10px] text-zinc-400">Partilhado com família</p>
                        </button>
                        <button type="button" wire:click="$set('workspaceName', 'O Meu Negócio')"
                            class="p-4 bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 hover:border-indigo-400 rounded-2xl text-left transition-all group">
                            <p class="text-lg mb-1">🏢</p>
                            <p class="text-xs font-black dark:text-white group-hover:text-indigo-600 transition-colors">Empresarial</p>
                            <p class="text-[10px] text-zinc-400">Para o meu negócio</p>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
        Passo {{ $step }} de {{ $totalSteps }}
    </p>

    <div class="flex items-center gap-3">
        {{-- BOTÃO VOLTAR --}}
        <button wire:click="previousStep"
            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors flex items-center gap-2">
            <flux:icon name="arrow-left" class="size-3" />
            Voltar
        </button>

        <button wire:click="skipStep"
            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors">
            Saltar
        </button>

        <button wire:click="nextStep"
            class="flex items-center gap-2 px-8 h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02]">
            Continuar
            <flux:icon name="arrow-right" class="size-4" />
        </button>
    </div>
</div>
            </div>
            @endif

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 4: CATEGORIA                  --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 4)
            <div class="p-10 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 bg-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/20 shrink-0">
                        <flux:icon name="tag" class="size-7 text-white" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">Primeira categoria de despesa</h2>
                        <p class="text-sm text-zinc-500 mt-0.5">Cria uma categoria para organizar os teus gastos. Podes adicionar mais depois.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach([
                            ['name' => 'Alimentação', 'color' => '#f97316', 'emoji' => '🛒'],
                            ['name' => 'Transporte', 'color' => '#3b82f6', 'emoji' => '🚗'],
                            ['name' => 'Saúde', 'color' => '#ef4444', 'emoji' => '❤️'],
                            ['name' => 'Lazer', 'color' => '#8b5cf6', 'emoji' => '🎉'],
                            ['name' => 'Educação', 'color' => '#10b981', 'emoji' => '📚'],
                            ['name' => 'Casa', 'color' => '#0ea5e9', 'emoji' => '🏠'],
                        ] as $suggestion)
                            <button type="button"
                                wire:click="$set('categoryName', '{{ $suggestion['name'] }}'); $set('categoryColor', '{{ $suggestion['color'] }}')"
                                class="p-3 bg-zinc-50 dark:bg-zinc-900 border-2 {{ $categoryName === $suggestion['name'] ? 'border-purple-500' : 'border-zinc-200 dark:border-zinc-800' }} hover:border-purple-400 rounded-2xl text-center transition-all">
                                <p class="text-xl mb-1">{{ $suggestion['emoji'] }}</p>
                                <p class="text-xs font-black dark:text-white">{{ $suggestion['name'] }}</p>
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="h-px flex-1 bg-zinc-100 dark:bg-zinc-800"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">ou cria a tua</span>
                        <div class="h-px flex-1 bg-zinc-100 dark:bg-zinc-800"></div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="relative flex-1">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-purple-600 z-10">Nome da Categoria</label>
                            <input type="text" wire:model="categoryName" placeholder="Ex: Subscriçõess, Pets, Viagens..."
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-purple-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                        </div>
                        <div class="relative shrink-0">
                            <label class="absolute left-1/2 -translate-x-1/2 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10 whitespace-nowrap">Cor</label>
                            <input type="color" wire:model="categoryColor"
                                class="size-16 rounded-2xl border-2 border-zinc-200 dark:border-zinc-800 cursor-pointer p-1 bg-zinc-50 dark:bg-zinc-900">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
        Passo {{ $step }} de {{ $totalSteps }}
    </p>

    <div class="flex items-center gap-3">
        {{-- BOTÃO VOLTAR --}}
        <button wire:click="previousStep"
            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors flex items-center gap-2">
            <flux:icon name="arrow-left" class="size-3" />
            Voltar
        </button>

        <button wire:click="skipStep"
            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors">
            Saltar
        </button>

        <button wire:click="nextStep"
            class="flex items-center gap-2 px-8 h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02]">
            Continuar
            <flux:icon name="arrow-right" class="size-4" />
        </button>
    </div>
</div>
            </div>
            @endif

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 5: CONCLUÍDO                  --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 5)
            <div class="p-10 space-y-8 text-center">
                <div class="space-y-4">
                    <div class="size-24 bg-gradient-to-br from-emerald-400 to-indigo-600 rounded-[2rem] flex items-center justify-center mx-auto shadow-2xl shadow-indigo-500/30 animate-bounce">
                        <flux:icon name="check-circle" class="size-12 text-white" />
                    </div>
                    <div>
                        <h2 class="text-3xl font-black italic tracking-tighter dark:text-white">Tudo pronto! 🎉</h2>
                        <p class="text-zinc-500 font-medium mt-2">O teu espaço financeiro está configurado. Agora é hora de explorar!</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-left">
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="p-5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/30 rounded-2xl hover:border-indigo-400 transition-all group">
                        <flux:icon name="squares-2x2" class="size-6 text-indigo-500 mb-2" />
                        <p class="text-sm font-black dark:text-white group-hover:text-indigo-600 transition-colors">Dashboard</p>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Ver resumo financeiro</p>
                    </a>
                    <a href="{{ route('hub.incomes') }}" wire:navigate
                        class="p-5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl hover:border-emerald-400 transition-all group">
                        <flux:icon name="arrow-trending-up" class="size-6 text-emerald-500 mb-2" />
                        <p class="text-sm font-black dark:text-white group-hover:text-emerald-600 transition-colors">Receitas</p>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Gerir rendimentos</p>
                    </a>
                    <a href="{{ route('social.hub') }}" wire:navigate
                        class="p-5 bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/30 rounded-2xl hover:border-purple-400 transition-all group">
                        <flux:icon name="globe-alt" class="size-6 text-purple-500 mb-2" />
                        <p class="text-sm font-black dark:text-white group-hover:text-purple-600 transition-colors">Finance Connect</p>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Rede social financeira</p>
                    </a>
                </div>

               <div class="pt-4 space-y-4">
    {{-- BOTÃO PRINCIPAL --}}
    <button wire:click="completeOnboarding"
        class="w-full h-14 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black uppercase tracking-widest text-sm rounded-2xl shadow-2xl shadow-indigo-500/20 transition-all hover:scale-[1.02]">
        🚀 Entrar na Plataforma
    </button>

    {{-- BOTÃO VOLTAR (PEQUENO E POR BAIXO) --}}
    <div class="text-center">
        <button wire:click="previousStep"
            class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-zinc-600 transition-colors flex items-center justify-center gap-2 mx-auto">
            <flux:icon name="arrow-left" class="size-3" />
            Corrigir dados anteriores
        </button>
    </div>
</div>
            </div>
            @endif

        </div>
    </div>
</div>
