<div class="space-y-10 pb-20">

    {{-- ================================================================== --}}
    {{-- HEADER · RESPONSIVO                                               --}}
    {{-- ================================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">

        {{-- Ícone + Título --}}
        <div class="flex items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full group-hover:bg-emerald-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-emerald-500/10">
                    <flux:icon name="arrow-trending-up" class="w-10 h-10 text-emerald-600" />
                </div>
            </div>

            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl sm:text-2xl sm:text-3xl md:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                        Gestão de Receitas
                    </h1>

                    <flux:badge
                        variant="success"
                        class="bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1"
                    >
                        Cash-In
                    </flux:badge>
                </div>

                <p class="text-sm text-zinc-500 font-medium italic mt-2">
                    Controlo estratégico de fluxos e rendimentos do grupo
                </p>
            </div>
        </div>

        {{-- Botões de ação --}}
        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">

            {{-- Botão: escolher tipo de fonte de rendimento --}}
            <div x-data="{ openSourceMenu: false }" class="relative">
                <flux:button
                    @click="openSourceMenu = !openSourceMenu"
                    variant="primary"
                    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                    <flux:icon name="calendar-days" class="size-4" />
                    Adicionar Rendimento
                    <flux:icon name="chevron-down" variant="micro" class="size-3 transition-transform" x-bind:class="openSourceMenu ? 'rotate-180' : ''" />
                </flux:button>

                <div x-show="openSourceMenu" x-cloak x-transition @click.outside="openSourceMenu = false"
                     class="absolute left-0 z-30 mt-2 w-72 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl overflow-hidden text-left">
                    <p class="px-4 pt-4 pb-2 text-[9px] font-black uppercase tracking-widest text-zinc-400">Para que fonte de rendimento é este registo?</p>
                    <div class="py-1">
                        @foreach([
                            ['event' => 'emprego', 'icon' => 'briefcase', 'label' => 'Salário / Contrato de Trabalho', 'color' => 'text-indigo-600'],
                            ['event' => 'renda', 'icon' => 'home', 'label' => 'Renda Imobiliária', 'color' => 'text-emerald-600'],
                            ['event' => 'freelance', 'icon' => 'computer-desktop', 'label' => 'Freelance / Trabalho Independente', 'color' => 'text-brand-600'],
                            ['event' => 'investimento', 'icon' => 'chart-bar-square', 'label' => 'Rendimento de Investimento', 'color' => 'text-amber-600'],
                            ['event' => 'reforma', 'icon' => 'building-library', 'label' => 'Reforma / Pensão', 'color' => 'text-sky-600'],
                            ['event' => 'bolsa', 'icon' => 'academic-cap', 'label' => 'Bolsa de Estudo', 'color' => 'text-fuchsia-600'],
                            ['event' => 'outro', 'icon' => 'ellipsis-horizontal-circle', 'label' => 'Outra Fonte', 'color' => 'text-zinc-500'],
                        ] as $source)
                            <button type="button"
                                @click="openSourceMenu = false"
                                wire:click="openIncomeSource('{{ $source['event'] }}')"
                                class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <flux:icon name="{{ $source['icon'] }}" variant="micro" class="size-4 {{ $source['color'] }}" />
                                <span class="text-xs font-bold text-zinc-700 dark:text-zinc-200">{{ $source['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

<div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

{{-- Botão receita extra --}}
<flux:button
    wire:click="openExtraModal"
    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20"
>
    Receita Extra
</flux:button>

{{-- EXPORTAÇÃO DO EXTRATO DE RECEITAS --}}
<div x-data="{ openStatementExport: false }" x-on:income-statement-download.window="window.location.href = $event.detail.url" class="relative">
    <flux:button
        type="button"
        variant="outline"
        @click="openStatementExport = !openStatementExport"
        class="rounded-2xl px-5 font-black uppercase tracking-widest"
    >
        <flux:icon name="arrow-down-tray" class="size-4" />
        Exportar PDF
        <flux:icon name="chevron-down" variant="micro" class="size-3 transition-transform" x-bind:class="openStatementExport ? 'rotate-180' : ''" />
    </flux:button>

    <div
        x-show="openStatementExport"
        x-cloak
        x-transition
        @click.outside="openStatementExport = false"
        class="absolute right-0 z-50 mt-3 w-80 max-w-[calc(100vw-2rem)] rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-2xl dark:border-zinc-700 dark:bg-zinc-900"
    >
        <div class="mb-3">
            <p class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">Exportar extrato</p>
            <p class="mt-1 text-[11px] leading-relaxed text-zinc-500">Descarrega um PDF com os movimentos, salários, serviços extra e detalhes do período.</p>
        </div>

        <div class="space-y-2">
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Mês específico</p>
            <div class="grid grid-cols-2 gap-2">
                <select wire:model="statementExportMonth" class="h-10 min-w-0 rounded-xl border border-zinc-200 bg-zinc-50 px-2 text-xs font-semibold text-zinc-800 outline-none focus:border-emerald-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    @foreach([
                        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                    ] as $monthNumber => $monthName)
                        <option value="{{ $monthNumber }}">{{ $monthName }}</option>
                    @endforeach
                </select>
                <select wire:model="statementExportYear" class="h-10 min-w-0 rounded-xl border border-zinc-200 bg-zinc-50 px-2 text-xs font-semibold text-zinc-800 outline-none focus:border-emerald-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    @foreach(range((int) now()->year, 2000) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <button
                type="button"
                wire:click="exportIncomeStatement('specific_month')"
                @click="openStatementExport = false"
                wire:loading.attr="disabled"
                wire:target="exportIncomeStatement"
                class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 disabled:opacity-50"
            >
                <flux:icon name="arrow-down-tray" class="size-4" />
                Descarregar PDF do mês
            </button>
        </div>

        <div class="my-3 border-t border-zinc-100 dark:border-zinc-800"></div>

        <div class="space-y-2">
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Ano específico</p>
            <select wire:model="statementExportYear" class="h-10 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 text-xs font-semibold text-zinc-800 outline-none focus:border-emerald-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                @foreach(range((int) now()->year, 2000) as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <button
                type="button"
                wire:click="exportIncomeStatement('specific_year')"
                @click="openStatementExport = false"
                wire:loading.attr="disabled"
                wire:target="exportIncomeStatement"
                class="flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 text-xs font-black uppercase tracking-widest text-emerald-700 transition hover:bg-emerald-100 disabled:opacity-50 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:hover:bg-emerald-950"
            >
                <flux:icon name="arrow-down-tray" class="size-4" />
                Descarregar PDF do ano
            </button>
        </div>

        <p wire:loading wire:target="exportIncomeStatement" class="mt-3 text-center text-[10px] font-semibold text-emerald-600">
            A preparar o extrato...
        </p>
    </div>
</div>






        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- PROJEÇÃO DE SALÁRIO E ALIMENTAÇÃO · CARTÕES DINÂMICOS --}}
    <section x-data="{ showSalaryHistory: false, showMealHistory: false }" class="space-y-4">
        <div class="flex items-end justify-between gap-3 px-1">
            <div>
                <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 dark:border-emerald-900/50 dark:bg-emerald-950/40">
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-50 motion-reduce:animate-none"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-300">Resumo mensal</span>
                </div>
                <h2 class="flex items-center gap-2 text-2xl font-black tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/20 transition duration-300 hover:rotate-[-6deg] hover:scale-110 motion-reduce:transform-none">
                        <flux:icon name="chart-bar" variant="micro" class="size-5" />
                    </span>
                    <span class="font-serif text-black dark:text-black">Total Projetado</span>
                </h2>
            </div>
            <span class="hidden items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-zinc-500 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:inline-flex">
                <flux:icon name="calendar-days" variant="micro" class="size-3.5 text-emerald-500" />
                {{ now()->translatedFormat('F Y') }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- CARTÃO BANCÁRIO: SALÁRIO LÍQUIDO --}}
            <article role="button" tabindex="0" aria-label="Consultar histórico do salário líquido" @click="showSalaryHistory = true" @keydown.enter.prevent="showSalaryHistory = true" @keydown.space.prevent="showSalaryHistory = true" class="group relative isolate min-h-56 cursor-pointer overflow-hidden rounded-[1.6rem] border border-indigo-300/20 bg-gradient-to-br from-[#18264a] via-[#111a32] to-[#080e1d] p-6 text-white shadow-2xl shadow-indigo-950/25 transition-all duration-500 hover:-translate-y-1 hover:border-indigo-300/60 hover:shadow-indigo-950/40 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-zinc-950 motion-reduce:transition-none sm:p-7">
                <div class="pointer-events-none absolute -right-12 -top-20 size-64 rounded-full border border-white/10 transition-transform duration-700 group-hover:rotate-12 group-hover:scale-110"></div>
                <div class="pointer-events-none absolute -right-2 -top-12 size-44 rounded-full border border-indigo-200/10"></div>
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-indigo-400/10 via-transparent to-white/5"></div>
                <div class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-1000 group-hover:translate-x-[450%]"></div>
                <div class="relative flex min-h-44 flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div><p class="text-[9px] font-black uppercase tracking-[0.28em] text-indigo-200/80">Finance Pro · Conta de rendimento</p><p class="mt-1 text-sm font-bold tracking-wide text-white">SALÁRIO LÍQUIDO</p></div>
                        <div class="flex items-center gap-2"><span class="text-[10px] font-black italic tracking-[0.18em] text-white/80">FP</span><div class="flex size-9 items-center justify-center rounded-xl border border-white/15 bg-white/10 backdrop-blur-sm"><flux:icon name="briefcase" variant="outline" class="size-5 text-indigo-100" /></div></div>
                    </div>
                    <div class="flex items-center gap-3 py-2">
                        <div class="relative h-9 w-12 overflow-hidden rounded-md border border-amber-100/40 bg-gradient-to-br from-amber-100 via-amber-300 to-amber-500 shadow-inner"><span class="absolute left-1/2 top-0 h-full w-px bg-amber-800/25"></span><span class="absolute left-0 top-1/2 h-px w-full bg-amber-800/25"></span><span class="absolute left-1/4 top-0 h-full w-px bg-amber-800/20"></span><span class="absolute right-1/4 top-0 h-full w-px bg-amber-800/20"></span></div>
                        <flux:icon name="wifi" variant="outline" class="size-5 rotate-90 text-white/65" /><span class="ml-auto text-[9px] font-bold uppercase tracking-[0.18em] text-indigo-100/65">Resumo mensal</span>
                    </div>
                    <div><p class="text-[9px] font-bold uppercase tracking-[0.2em] text-indigo-200/75">Valor projetado</p><p class="mt-1 text-3xl font-black tracking-tight tabular-nums text-white transition-transform duration-300 group-hover:translate-x-1 sm:text-4xl">{{ number_format($projectedSalaryNetTotal, 2, ',', ' ') }} <span class="text-xl text-indigo-200">€</span></p></div>
                    <div class="flex items-end justify-between gap-3 border-t border-white/15 pt-3">
                        <div class="min-w-0"><p class="text-[8px] font-bold uppercase tracking-[0.18em] text-indigo-200/65">Titular / contrato</p><p class="mt-1 truncate text-[11px] font-semibold text-white">@forelse($projectedSalaryContracts as $projectedContract){{ $loop->first ? '' : ' · ' }}{{ $projectedContract->description }}@empty Sem salário ativo registado @endforelse</p></div>
                        <div class="shrink-0 text-right"><p class="text-[8px] font-bold uppercase tracking-[0.18em] text-indigo-200/65">Projeção anual</p><p class="mt-1 text-xs font-bold tabular-nums text-white">{{ number_format($projectedSalaryAnnualTotal, 2, ',', ' ') }} €</p></div>
                    </div>
                </div>
            </article>

            {{-- CARTÃO BANCÁRIO: SUBSÍDIO DE ALIMENTAÇÃO --}}
            <article role="button" tabindex="0" aria-label="Consultar histórico do subsídio de alimentação" @click="showMealHistory = true" @keydown.enter.prevent="showMealHistory = true" @keydown.space.prevent="showMealHistory = true" class="group relative isolate min-h-56 cursor-pointer overflow-hidden rounded-[1.6rem] border border-emerald-300/20 bg-gradient-to-br from-[#075443] via-[#06382f] to-[#031e1a] p-6 text-white shadow-2xl shadow-emerald-950/25 transition-all duration-500 hover:-translate-y-1 hover:border-emerald-300/60 hover:shadow-emerald-950/40 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-zinc-950 motion-reduce:transition-none sm:p-7">
                <div class="pointer-events-none absolute -right-12 -top-20 size-64 rounded-full border border-white/10 transition-transform duration-700 group-hover:-rotate-12 group-hover:scale-110"></div>
                <div class="pointer-events-none absolute -right-2 -top-12 size-44 rounded-full border border-emerald-100/10"></div>
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-emerald-300/10 via-transparent to-white/5"></div>
                <div class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-1000 group-hover:translate-x-[450%]"></div>
                <div class="relative flex min-h-44 flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div><p class="text-[9px] font-black uppercase tracking-[0.28em] text-emerald-100/80">Finance Pro · Cartão benefício</p><p class="mt-1 text-sm font-bold tracking-wide text-white">SUBSÍDIO DE ALIMENTAÇÃO</p></div>
                        <div class="flex items-center gap-2"><span class="text-[10px] font-black italic tracking-[0.18em] text-white/80">FP</span><div class="flex size-9 items-center justify-center rounded-xl border border-white/15 bg-white/10 backdrop-blur-sm"><flux:icon name="cake" variant="outline" class="size-5 text-emerald-100" /></div></div>
                    </div>
                    <div class="flex items-center gap-3 py-2">
                        <div class="relative h-9 w-12 overflow-hidden rounded-md border border-amber-100/40 bg-gradient-to-br from-amber-100 via-amber-300 to-amber-500 shadow-inner"><span class="absolute left-1/2 top-0 h-full w-px bg-amber-800/25"></span><span class="absolute left-0 top-1/2 h-px w-full bg-amber-800/25"></span><span class="absolute left-1/4 top-0 h-full w-px bg-amber-800/20"></span><span class="absolute right-1/4 top-0 h-full w-px bg-amber-800/20"></span></div>
                        <flux:icon name="wifi" variant="outline" class="size-5 rotate-90 text-white/65" /><span class="ml-auto text-[9px] font-bold uppercase tracking-[0.18em] text-emerald-100/65">Benefício mensal</span>
                    </div>
                    <div><p class="text-[9px] font-bold uppercase tracking-[0.2em] text-emerald-100/75">Valor projetado</p><p class="mt-1 text-3xl font-black tracking-tight tabular-nums text-white transition-transform duration-300 group-hover:translate-x-1 sm:text-4xl">{{ number_format($projectedMealAllowanceTotal, 2, ',', ' ') }} <span class="text-xl text-emerald-200">€</span></p></div>
                    <div class="flex items-end justify-between gap-3 border-t border-white/15 pt-3">
                        <div class="min-w-0"><p class="text-[8px] font-bold uppercase tracking-[0.18em] text-emerald-100/65">Atribuído por</p><p class="mt-1 truncate text-[11px] font-semibold text-white">@forelse($projectedSalaryContracts as $projectedContract){{ $loop->first ? '' : ' · ' }}{{ $projectedContract->description }}@empty Sem subsídio configurado @endforelse</p></div>
                        <div class="shrink-0 text-right"><p class="text-[8px] font-bold uppercase tracking-[0.18em] text-emerald-100/65">Projeção anual</p><p class="mt-1 text-xs font-bold text-white">{{ number_format($projectedMealAllowanceTotal * 11, 2, ',', ' ') }} €</p></div>
                    </div>
                </div>
            </article>
        </div>


        {{-- MODAL: HISTÓRICO DO SALÁRIO --}}
        <div x-show="showSalaryHistory" x-cloak x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-950/80 p-3 backdrop-blur-sm sm:p-6" @click.self="showSalaryHistory = false" @keydown.escape.window="showSalaryHistory = false">
            <div x-show="showSalaryHistory" x-transition.scale class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900" @click.stop>
                <div class="flex items-center justify-between gap-4 border-b border-zinc-100 bg-gradient-to-r from-indigo-50 to-white p-5 dark:border-zinc-800 dark:from-indigo-950/40 dark:to-zinc-900 sm:p-6">
                    <div class="flex items-center gap-3">
                        <span class="flex size-11 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/20"><flux:icon name="briefcase" class="size-5" /></span>
                        <div><h3 class="text-lg font-black text-zinc-900 dark:text-white">Histórico do salário líquido</h3><p class="text-xs text-zinc-500">Contratos, pagamentos, serviços e prémios associados</p></div>
                    </div>
                    <button type="button" @click="showSalaryHistory = false" class="rounded-xl p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="Fechar"><flux:icon name="x-mark" class="size-5" /></button>
                </div>
                <div class="min-h-0 flex-1 space-y-6 overflow-y-auto p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-900/50 dark:bg-indigo-950/30"><p class="text-[10px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-300">Projeção mensal</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($projectedSalaryNetTotal, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">Salário líquido estimado, sem subsídio de alimentação</p></div>
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"><p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Projeção anual</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($projectedSalaryAnnualTotal, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">Projeção mensal multiplicada por 12</p></div>
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"><p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Registos de pagamento</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ $projectedSalaryHistoryPayments->count() }}</p><p class="mt-1 text-[10px] text-zinc-500">Pagamentos guardados no histórico</p></div>
                    </div>
                    <div class="rounded-2xl border border-indigo-100 p-4 dark:border-indigo-900/50">
                        <h4 class="text-xs font-black uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Como é calculado este cartão?</h4>
                        <div class="mt-3 space-y-2 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                            <p>O valor mensal soma o líquido estimado de cada contrato de trabalho ativo. Quando o subsídio de alimentação já está configurado separadamente, esse valor é retirado do montante do contrato para não ser contado duas vezes.</p>
                            <p>Acrescentam-se os serviços ou prémios do mês anterior que estejam marcados para integrar o salário deste mês. A estimativa anual apresentada no cartão corresponde ao valor mensal projetado × 12; não representa uma previsão de todos os subsídios anuais.</p>
                            <p><strong>Importante:</strong> uma projeção não significa que o dinheiro já tenha sido recebido. Para confirmar um recebimento, consulta o estado e a data de cada pagamento abaixo.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-zinc-500">Contratos considerados na projeção</h4>
                        @forelse($projectedSalaryContracts as $contract)
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex flex-wrap items-start justify-between gap-2"><div><p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $contract->description }}</p><p class="mt-1 text-[10px] text-zinc-500">{{ $contract->metadata['position'] ?? 'Função não indicada' }} · {{ $contract->metadata['contract_type'] ?? 'Tipo de contrato não indicado' }}</p></div><span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[9px] font-black uppercase text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Ativo</span></div>
                                <div class="mt-3 grid grid-cols-2 gap-3 text-xs sm:grid-cols-3"><div><p class="text-[10px] text-zinc-500">Valor configurado</p><p class="mt-1 font-bold text-zinc-900 dark:text-white">{{ number_format((float) $contract->amount, 2, ',', ' ') }} €</p></div><div><p class="text-[10px] text-zinc-500">Líquido considerado</p><p class="mt-1 font-bold text-indigo-600 dark:text-indigo-300">{{ number_format((float) $contract->projected_salary_net, 2, ',', ' ') }} €</p></div><div><p class="text-[10px] text-zinc-500">Início</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ $contract->metadata['start_date'] ?? 'Não indicado' }}</p></div></div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Não existem contratos ativos para calcular o salário projetado.</p>
                        @endforelse
                    </div>
                    <div class="space-y-3">
                        <div><h4 class="text-xs font-black uppercase tracking-wider text-zinc-500">Histórico de pagamentos salariais</h4><p class="mt-1 text-[10px] text-zinc-500">Inclui pagamentos pagos e pendentes registados na aplicação.</p></div>
                        @forelse($projectedSalaryHistoryPayments as $payment)
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex flex-wrap items-center justify-between gap-2"><div><p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $payment->recurringIncome?->description ?? 'Pagamento salarial' }}</p><p class="mt-1 text-[10px] text-zinc-500">Período: {{ str_pad((string) $payment->payment_month, 2, '0', STR_PAD_LEFT) }}/{{ $payment->payment_year }} · {{ $payment->paid_at?->format('d/m/Y') ?? 'Sem data de pagamento' }}</p></div><span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase {{ $payment->status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">{{ $payment->status === 'paid' ? 'Pago' : 'Pendente' }}</span></div>
                                <div class="mt-3 grid grid-cols-2 gap-3 text-xs sm:grid-cols-4"><div><p class="text-[10px] text-zinc-500">Salário bruto</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ number_format((float) $payment->salary_base_gross, 2, ',', ' ') }} €</p></div><div><p class="text-[10px] text-zinc-500">Segurança Social</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">− {{ number_format((float) $payment->social_security, 2, ',', ' ') }} €</p></div><div><p class="text-[10px] text-zinc-500">IRS</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">− {{ number_format((float) $payment->irs, 2, ',', ' ') }} €</p></div><div><p class="text-[10px] text-zinc-500">Líquido recebido</p><p class="mt-1 font-black text-emerald-600 dark:text-emerald-300">{{ number_format((float) $payment->net_received, 2, ',', ' ') }} €</p></div></div>
                                <p class="mt-3 text-[10px] text-zinc-500">Subsídio de alimentação: {{ number_format((float) $payment->meal_allowance, 2, ',', ' ') }} € · Outros complementos: {{ number_format((float) $payment->other_complements, 2, ',', ' ') }} € · Descontos adicionais: {{ number_format((float) $payment->other_deductions, 2, ',', ' ') }} €</p>
                                @if($payment->notes)<p class="mt-2 rounded-lg bg-zinc-50 p-2 text-[10px] text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Nota: {{ $payment->notes }}</p>@endif
                            </div>
                        @empty
                            <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Ainda não existem pagamentos salariais guardados. Os valores dos cartões são projeções dos contratos ativos.</p>
                        @endforelse
                    </div>
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-zinc-500">Serviços extra e prémios associados</h4>
                        @forelse($projectedSalaryHistoryExtras as $extra)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-zinc-200 p-3 dark:border-zinc-700"><div><p class="text-xs font-bold text-zinc-900 dark:text-white">{{ $extra->description }}</p><p class="mt-1 text-[10px] text-zinc-500">{{ $extra->received_at?->format('d/m/Y') ?? 'Sem data' }} · {{ $extra->added_to_salary ? 'Integrado no próximo salário' : 'Registado como entrada independente' }}</p></div><p class="text-sm font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format((float) ($extra->amount_converted ?? $extra->amount), 2, ',', ' ') }} €</p></div>
                        @empty
                            <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Não existem serviços extra ou prémios associados a estes contratos.</p>
                        @endforelse
                    </div>
                </div>
                <div class="border-t border-zinc-100 p-4 text-right dark:border-zinc-800"><button type="button" @click="showSalaryHistory = false" class="rounded-xl bg-zinc-900 px-5 py-2.5 text-xs font-black uppercase tracking-wider text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">Fechar histórico</button></div>
            </div>
        </div>

        {{-- MODAL: HISTÓRICO DO SUBSÍDIO DE ALIMENTAÇÃO --}}
        <div x-show="showMealHistory" x-cloak x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-950/80 p-3 backdrop-blur-sm sm:p-6" @click.self="showMealHistory = false" @keydown.escape.window="showMealHistory = false">
            <div x-show="showMealHistory" x-transition.scale class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900" @click.stop>
                <div class="flex items-center justify-between gap-4 border-b border-zinc-100 bg-gradient-to-r from-emerald-50 to-white p-5 dark:border-zinc-800 dark:from-emerald-950/40 dark:to-zinc-900 sm:p-6">
                    <div class="flex items-center gap-3"><span class="flex size-11 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/20"><flux:icon name="cake" class="size-5" /></span><div><h3 class="text-lg font-black text-zinc-900 dark:text-white">Histórico do subsídio de alimentação</h3><p class="text-xs text-zinc-500">Valores configurados e subsídios registados nos pagamentos</p></div></div>
                    <button type="button" @click="showMealHistory = false" class="rounded-xl p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="Fechar"><flux:icon name="x-mark" class="size-5" /></button>
                </div>
                <div class="min-h-0 flex-1 space-y-6 overflow-y-auto p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30"><p class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Projeção mensal</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($projectedMealAllowanceTotal, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">Soma dos valores mensais configurados</p></div>
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"><p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Projeção anual</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($projectedMealAllowanceTotal * 11, 2, ',', ' ') }} €</p><p class="mt-1 text-[10px] text-zinc-500">Estimativa mensal × 11 meses</p></div>
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50"><p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Registos salariais</p><p class="mt-1 text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ $projectedSalaryHistoryPayments->count() }}</p><p class="mt-1 text-[10px] text-zinc-500">Com detalhe do subsídio registado</p></div>
                    </div>
                    <div class="rounded-2xl border border-emerald-100 p-4 dark:border-emerald-900/50">
                        <h4 class="text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Como é calculado este cartão?</h4>
                        <div class="mt-3 space-y-2 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                            <p>O valor mensal é a soma do campo de subsídio de alimentação configurado em cada contrato ativo. Nos contratos MIND, por exemplo, o cálculo pode resultar do valor diário multiplicado pelos dias de trabalho elegíveis definidos no contrato.</p>
                            <p>A estimativa anual apresentada no cartão usa 11 meses, assumindo que o subsídio não é pago no mês de férias. É uma estimativa de configuração e não uma confirmação de pagamento.</p>
                            <p>Quando o destino é cartão de refeição, o subsídio é apresentado separadamente do salário transferido para a conta bancária. O valor efetivamente pago deve ser confirmado no histórico salarial e no respetivo registo.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-zinc-500">Configuração por contrato ativo</h4>
                        @forelse($projectedSalaryContracts as $contract)
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex flex-wrap items-start justify-between gap-2"><div><p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $contract->description }}</p><p class="mt-1 text-[10px] text-zinc-500">Destino: {{ ($contract->metadata['meal_allowance_destination'] ?? '') === 'meal_card' ? 'Cartão de refeição' : 'Dinheiro / transferência' }}</p></div><p class="text-sm font-black text-emerald-600 dark:text-emerald-300">{{ number_format((float) ($contract->metadata['calculated_sa'] ?? 0), 2, ',', ' ') }} € / mês</p></div>
                                <div class="mt-3 grid grid-cols-2 gap-3 text-xs sm:grid-cols-4"><div><p class="text-[10px] text-zinc-500">Valor diário</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ number_format((float) ($contract->metadata['meal_allowance'] ?? 0), 2, ',', ' ') }} €</p></div><div><p class="text-[10px] text-zinc-500">Dias configurados</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">{{ (int) ($contract->metadata['working_days'] ?? 0) }}</p></div><div><p class="text-[10px] text-zinc-500">Frequência</p><p class="mt-1 font-semibold text-zinc-800 dark:text-zinc-200">Mensal</p></div><div><p class="text-[10px] text-zinc-500">Estado</p><p class="mt-1 font-semibold text-emerald-600 dark:text-emerald-300">Ativo</p></div></div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Não existem subsídios configurados em contratos ativos.</p>
                        @endforelse
                    </div>
                    <div class="space-y-3">
                        <div><h4 class="text-xs font-black uppercase tracking-wider text-zinc-500">Histórico de subsídios registados</h4><p class="mt-1 text-[10px] text-zinc-500">Valores guardados nos registos mensais de salário, incluindo pagamentos pendentes.</p></div>
                        @forelse($projectedSalaryHistoryPayments as $payment)
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex flex-wrap items-center justify-between gap-2"><div><p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $payment->recurringIncome?->description ?? 'Subsídio de alimentação' }}</p><p class="mt-1 text-[10px] text-zinc-500">Período: {{ str_pad((string) $payment->payment_month, 2, '0', STR_PAD_LEFT) }}/{{ $payment->payment_year }} · {{ $payment->paid_at?->format('d/m/Y') ?? 'Sem data de pagamento' }}</p></div><span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase {{ $payment->status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">{{ $payment->status === 'paid' ? 'Pago' : 'Pendente' }}</span></div>
                                <div class="mt-3 flex flex-wrap items-end justify-between gap-3"><div><p class="text-[10px] text-zinc-500">Subsídio registado</p><p class="mt-1 text-lg font-black text-emerald-600 dark:text-emerald-300">{{ number_format((float) $payment->meal_allowance, 2, ',', ' ') }} €</p></div><p class="text-[10px] text-zinc-500">O estado indica se o pagamento salarial associado foi marcado como pago.</p></div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800">Ainda não existem registos mensais de pagamento com subsídio de alimentação.</p>
                        @endforelse
                    </div>
                </div>
                <div class="border-t border-zinc-100 p-4 text-right dark:border-zinc-800"><button type="button" @click="showMealHistory = false" class="rounded-xl bg-zinc-900 px-5 py-2.5 text-xs font-black uppercase tracking-wider text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">Fechar histórico</button></div>
            </div>
        </div>
    </section>

    {{-- CARDS DE ESTATÍSTICAS · ULTRA PREMIUM                           --}}
{{-- ================================================================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    {{-- RECEBIDO NO MÊS ANTERIOR · DESTAQUE PRINCIPAL --}}
    <div class="relative p-6 rounded-[2rem] bg-gradient-to-br from-emerald-50 via-white to-teal-50
                dark:from-emerald-950/70 dark:via-zinc-900 dark:to-teal-950/60
                border border-emerald-300 dark:border-emerald-800 shadow-lg shadow-emerald-900/10
                ring-1 ring-emerald-200/70 dark:ring-emerald-900/50 overflow-visible group z-20
                transition-all duration-300 hover:-translate-y-1 hover:border-emerald-400
                hover:shadow-xl hover:shadow-emerald-900/15">

        <div class="pointer-events-none absolute -top-10 -right-10 size-28 rounded-full bg-emerald-300/20 dark:bg-emerald-400/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
        <div class="pointer-events-none absolute left-0 top-5 bottom-5 w-1 rounded-r-full bg-gradient-to-b from-emerald-400 to-teal-500"></div>

        <div class="absolute top-5 right-5 flex size-10 items-center justify-center rounded-2xl
                    bg-emerald-600 text-white shadow-md shadow-emerald-700/25
                    ring-4 ring-emerald-100 dark:ring-emerald-900/70
                    transition-all duration-300 group-hover:rotate-6 group-hover:scale-110">
            <flux:icon name="wallet" class="size-5" />
        </div>

        <div class="relative">
            <div class="mb-2 flex items-center gap-2 pr-12">
                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-800 dark:text-emerald-300">
                    Este mês recebi
                </p>
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                    <button type="button" @click.stop="open = !open" :aria-expanded="open" aria-label="Como é calculado o valor recebido este mês" class="flex size-5 items-center justify-center rounded-full border border-emerald-400/60 bg-white/80 text-[10px] font-black text-emerald-700 shadow-sm transition hover:scale-110 hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-900 dark:text-emerald-200 dark:hover:bg-emerald-800">
                        i
                    </button>
                    <div x-show="open" x-cloak x-transition role="tooltip" class="absolute left-0 top-full z-40 mt-2 w-72 max-w-[calc(100vw-3rem)] rounded-2xl border border-zinc-700 bg-zinc-950 p-4 text-left normal-case shadow-2xl">
                        <p class="mb-3 text-[10px] font-black uppercase tracking-wider text-white">Composição do valor</p>
                        <div class="space-y-2 text-[10px]">
                            <div class="flex items-start justify-between gap-3">
                                <span class="text-zinc-300">Receitas registadas este mês</span>
                                <span class="shrink-0 font-bold tabular-nums text-white">{{ number_format($thisMonthIncomeTotal, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="text-zinc-300">Salário líquido + subsídio de alimentação</span>
                                <span class="shrink-0 font-bold tabular-nums text-white">{{ number_format($thisMonthPaidSalaryTotal + $thisMonthPendingSalaryTotal, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="text-zinc-300">Prémios e serviços extra</span>
                                <span class="shrink-0 font-bold tabular-nums text-white">{{ number_format($thisMonthSalaryExtras, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                        <div class="mt-3 border-t border-zinc-700 pt-3">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-300">Total apresentado</span>
                                <span class="text-xs font-black tabular-nums text-emerald-300">{{ number_format($thisMonthReceived, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                        <p class="mt-2 text-[9px] leading-relaxed text-zinc-400">Soma todos os valores a faturar. Nos contratos de trabalho, o salário só é incluído quando existe um contrato e está selecionada a opção «Já recebi este salário este mês».</p>
                    </div>
                </div>
            </div>

            <p class="text-4xl font-black text-emerald-700 dark:text-emerald-300 italic tracking-tight">
                {{ number_format($thisMonthReceived, 2, ',', ' ') }}€
            </p>

            <p class="text-[10px] text-emerald-700/70 dark:text-emerald-400 font-medium mt-1">
                Entradas e salários fixos de {{ now()->translatedFormat('F Y') }}
            </p>
        </div>
    </div>
{{-- ENTRADAS EXTRAS --}}
<div class="relative p-6 rounded-[2rem] bg-white/40 dark:bg-zinc-900/40
            backdrop-blur-xl border border-white/20 shadow-xl overflow-visible group relative z-10 hover:z-50">

    <div class="absolute inset-0 opacity-10">
        <flux:icon name="bolt" class="size-32 text-yellow-500/20 rotate-12" />
    </div>

    <div class="relative mb-2 flex items-center gap-2">
        <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Entradas Extra / Prémios</p>
        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
            <button type="button" @click.stop="open = !open" :aria-expanded="open" aria-label="Como é calculado o total de entradas extra e prémios" class="flex size-5 items-center justify-center rounded-full border border-zinc-300 bg-white/80 text-[10px] font-black text-zinc-600 shadow-sm transition hover:scale-110 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">i</button>
            <div x-show="open" x-cloak x-transition role="tooltip" class="absolute left-0 top-full z-40 mt-2 w-72 max-w-[calc(100vw-3rem)] rounded-2xl border border-zinc-700 bg-zinc-950 p-4 text-left normal-case shadow-2xl">
                <p class="text-[10px] leading-relaxed text-zinc-300">Soma os rendimentos variáveis registados, como receitas extra e prémios. Não corresponde a uma previsão de valores futuros.</p>
            </div>
        </div>
    </div>

    <p class="text-4xl font-black text-yellow-600 italic tracking-tight">
        {{ number_format($totalExtras, 0, ',', ' ') }}€
    </p>

    <p class="text-[10px] text-zinc-400 font-medium mt-1">
        Total de entradas variáveis
    </p>
</div>



    {{-- TOTAL ANUAL (SÓ FIXOS) --}}
    @php
        $totalYear = $fixedIncomes
            ->where('created_at', '>=', now()->startOfYear())
            ->sum('amount');
    @endphp

    <div class="relative p-6 rounded-[2rem] bg-white/40 dark:bg-zinc-900/40
                backdrop-blur-xl border border-white/20 shadow-xl overflow-visible group relative z-10 hover:z-50">

        <div class="absolute inset-0 opacity-10">
            <flux:icon name="banknotes" class="size-32 text-amber-500/20 rotate-12" />
        </div>

        <div class="relative mb-2 flex items-center gap-2">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Total {{ now()->year }} (Fixos)</p>
            <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                <button type="button" @click.stop="open = !open" :aria-expanded="open" aria-label="Como é calculado o total anual de rendimentos fixos" class="flex size-5 items-center justify-center rounded-full border border-zinc-300 bg-white/80 text-[10px] font-black text-zinc-600 shadow-sm transition hover:scale-110 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">i</button>
                <div x-show="open" x-cloak x-transition role="tooltip" class="absolute left-0 top-full z-40 mt-2 w-72 max-w-[calc(100vw-3rem)] rounded-2xl border border-zinc-700 bg-zinc-950 p-4 text-left normal-case shadow-2xl">
                    <p class="text-[10px] leading-relaxed text-zinc-300">Soma os valores dos rendimentos fixos criados desde 1 de janeiro do ano atual. É calculado com base nos valores registados e não inclui entradas variáveis.</p>
                </div>
            </div>
        </div>

        <p class="text-4xl font-black text-amber-600 italic tracking-tight">
            {{ number_format($totalYear, 0, ',', ' ') }}€
        </p>

        <p class="text-[10px] text-zinc-400 font-medium mt-1">
            Valores a faturar acumulados
        </p>
    </div>

    {{-- PROJEÇÃO ANUAL LÍQUIDA + SUBSÍDIO DE ALIMENTAÇÃO --}}
    <div class="relative p-6 rounded-[2rem] bg-white/40 dark:bg-zinc-900/40
                backdrop-blur-xl border border-white/20 shadow-xl overflow-visible group relative z-10 hover:z-50">

        <div class="absolute inset-0 opacity-10">
            <flux:icon name="trophy" class="size-32 text-emerald-500/20 rotate-12" />
        </div>

        <div class="relative mb-2 flex items-center gap-2">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Total anual estimado (Total líquido + Subsídio alimentação)</p>
            <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                <button type="button" @click.stop="open = !open" :aria-expanded="open" aria-label="Como é calculado o total ao fim de um ano" class="flex size-5 items-center justify-center rounded-full border border-zinc-300 bg-white/80 text-[10px] font-black text-zinc-600 shadow-sm transition hover:scale-110 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">i</button>
                <div x-show="open" x-cloak x-transition role="tooltip" class="absolute left-0 top-full z-40 mt-2 w-72 max-w-[calc(100vw-3rem)] rounded-2xl border border-zinc-700 bg-zinc-950 p-4 text-left normal-case shadow-2xl">
                    <p class="text-[10px] leading-relaxed text-zinc-300">Soma a estimativa líquida anual de todos os rendimentos recorrentes ativos do workspace. Os contratos de trabalho usam o cálculo salarial anual, incluindo os subsídios configurados; os restantes registos são anualizados de acordo com a periodicidade definida.</p>
                </div>
            </div>
        </div>

        <p class="text-4xl font-black text-emerald-600 italic tracking-tight">
            {{ number_format($salaryAnnualTotal, 2, ',', ' ') }}€
        </p>

        <p class="text-[10px] text-zinc-400 font-medium mt-1">
            Estimativa anual líquida de todos os registos ativos
        </p>
    </div>

</div>






































<div class="flex flex-col gap-8 w-full" x-data="{ activeIncomeTab: 'billing' }">
    <div class="flex justify-center">
        <div class="inline-flex flex-wrap items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-zinc-50 p-1.5 dark:border-zinc-800 dark:bg-zinc-950">
            <button type="button" @click="activeIncomeTab = 'billing'" :class="activeIncomeTab === 'billing' ? 'bg-white text-zinc-950 shadow-md ring-1 ring-zinc-200 dark:bg-zinc-800 dark:text-white dark:ring-zinc-700' : 'text-zinc-500 hover:bg-white/70 hover:text-zinc-950 dark:hover:bg-zinc-800/70 dark:hover:text-white'" class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-sm font-extrabold tracking-normal transition-all duration-200 sm:text-base">
                <flux:icon name="calendar-days" variant="micro" class="size-5" />
                Rendimentos Mensais
            </button>
            <button type="button" @click="activeIncomeTab = 'bank'" :class="activeIncomeTab === 'bank' ? 'bg-white text-zinc-950 shadow-md ring-1 ring-zinc-200 dark:bg-zinc-800 dark:text-white dark:ring-zinc-700' : 'text-zinc-500 hover:bg-white/70 hover:text-zinc-950 dark:hover:bg-zinc-800/70 dark:hover:text-white'" class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-sm font-extrabold tracking-normal transition-all duration-200 sm:text-base">
                <flux:icon name="building-library" variant="micro" class="size-5" />
                Transferências Bancárias
            </button>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- GRELHA FIXOS + EXTRAS · RESPONSIVA                               --}}
    {{-- ================================================================== -->


















{{-- ============================================================= --}}
{{-- RENDIMENTOS FIXOS · CARTÕES DE GESTÃO SALARIAL                  --}}
{{-- ============================================================= --}}
<div class="space-y-6">

    <div x-show="activeIncomeTab === 'billing'" x-cloak class="space-y-8">


    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center gap-1.5">
                <flux:tooltip toggleable content="Número de contratos salariais que se encontram atualmente ativos.">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Registos</p>
                </flux:tooltip>
            </div>
            <p class="mt-1 text-2xl font-black text-indigo-600">{{ $salaryActiveCount }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center gap-1.5">
                <flux:tooltip toggleable content="Soma dos valores líquidos mensais previstos para os contratos salariais ativos. É uma estimativa com base nos valores atualmente configurados.">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Total mensal líquido estimado</p>
                </flux:tooltip>
            </div>
            <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($salaryMonthlyTotal, 2, ',', ' ') }} €</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center gap-1.5">
                <flux:tooltip toggleable content="Previsão do rendimento líquido dos contratos salariais ativos para um ano completo. Tem em conta o número de pagamentos anuais definido em cada contrato, incluindo subsídios quando aplicável.">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Total ao fim de um ano (Total líquido de todos os registos)</p>
                </flux:tooltip>
            </div>
            <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($allRecordsAnnualNetTotal, 2, ',', ' ') }} €</p>
        </div>
    </div>



    <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
      @forelse($salaryContracts as $fixed)
        @php
            $fixedMeta = $fixed->metadata ?? [];
            $fixedStatus = $fixedMeta['contract_status'] ?? ($fixed->is_active ? 'active' : 'suspended');
            $statusLabels = ['active' => 'Ativo', 'ended' => 'Terminado', 'suspended' => 'Suspenso'];
            $contractLabels = ['sem_termo' => 'Sem termo', 'termo_certo' => 'Termo certo', 'termo_incerto' => 'Termo incerto', 'estagio' => 'Estágio', 'prestacao_servicos' => 'Prestação de serviços', 'outro' => 'Outro'];
            $mealTotal = (float) ($fixedMeta['calculated_sa'] ?? ((float) ($fixedMeta['meal_allowance'] ?? 0) * (int) ($fixedMeta['working_days'] ?? 0)));
        @endphp
        <button type="button" wire:click="openSalaryDetails({{ $fixed->id }})" class="group rounded-2xl border border-zinc-200 bg-white p-5 text-left shadow-sm transition hover:border-indigo-400 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-zinc-800 dark:bg-zinc-900">
            <span class="flex items-start justify-between gap-4">
                <span class="min-w-0">
                    <span class="block truncate text-base font-black uppercase tracking-tight text-zinc-900 dark:text-white">{{ $fixed->description }}</span>
                    <span class="mt-1 block text-xs text-zinc-500">{{ $fixedMeta['position'] ?? 'Cargo não indicado' }}{{ ! empty($fixedMeta['department']) ? ' · '.$fixedMeta['department'] : '' }}</span>
                </span>
                <span class="shrink-0 rounded-full px-2.5 py-1 text-[9px] font-black uppercase {{ $fixedStatus === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($fixedStatus === 'ended' ? 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300') }}">{{ $statusLabels[$fixedStatus] ?? 'Ativo' }}</span>
            </span>
            <span class="mt-4 grid grid-cols-2 gap-3">
                <span><span class="block text-[9px] font-bold uppercase text-zinc-400">Bruto mensal</span><span class="text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ number_format((float) ($fixedMeta['salary_gross'] ?? 0), 2, ',', ' ') }} €</span></span>
                <span><span class="block text-[9px] font-bold uppercase text-zinc-400">Líquido estimado</span><span class="text-sm font-bold text-emerald-600">{{ number_format((float) $fixed->amount, 2, ',', ' ') }} €</span></span>
                <span><span class="block text-[9px] font-bold uppercase text-zinc-400">Subsídio de alimentação</span><span class="text-sm font-bold text-amber-600">{{ number_format($mealTotal, 2, ',', ' ') }} €/mês</span></span>
                <span><span class="block text-[9px] font-bold uppercase text-zinc-400">Contrato · início</span><span class="text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $contractLabels[$fixedMeta['contract_type'] ?? ''] ?? 'Não indicado' }} · {{ ! empty($fixedMeta['start_date']) ? \Carbon\Carbon::parse($fixedMeta['start_date'])->format('d/m/Y') : '—' }}</span></span>
            </span>
            <span class="mt-4 flex items-center justify-between border-t border-zinc-100 pt-3 text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:border-zinc-800 dark:text-indigo-300">
                Ver detalhes completos
                <flux:icon name="arrow-right" variant="micro" class="size-4 transition group-hover:translate-x-1" />
            </span>
        </button>
      @empty
        <div class="rounded-2xl border-2 border-dashed border-zinc-200 p-10 text-center dark:border-zinc-800 xl:col-span-2">
            <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">Não foram encontrados salários com estes filtros.</p>
        </div>
      @endforelse
    </div>

<h2 class="text-2xl font-black dark:text-white uppercase italic tracking-tighter">Rendimentos por Categorias</h2>



{{-- ============================================================= --}}
{{-- 💼 GESTÃO DE CONTRATOS DE TRABALHO (EMPREGO)                  --}}
{{-- ============================================================= --}}
<div class="space-y-6 w-full mt-10">
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="briefcase" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Contratos de Trabalho (Salários)
            </h2>
        </div>

        {{-- Botão para abrir o modal de emprego --}}
        <button
            type="button"
            wire:click="newEmployment"
            class="flex items-center gap-2 px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg"
        >
            <flux:icon name="plus" variant="micro" class="size-3" />
            Novo Contrato
        </button>
    </div>

    <div class="space-y-4">
        @forelse($salaryContracts as $job)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-6 group transition-all hover:border-indigo-500/40 shadow-sm relative overflow-hidden w-full">

                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="flex items-start gap-5 flex-1">
                        {{-- Icon/Dia --}}
                        <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950 border border-indigo-100 dark:border-indigo-900 shadow-inner shrink-0">
                            <span class="text-[7px] font-black text-indigo-400 uppercase leading-none mb-1">RECEB.</span>
                            <span class="text-xl font-black text-indigo-600">{{ sprintf('%02d', $job->day_of_month) }}</span>
                        </div>

                        <div>
                            @php
                                $annualRaise = $job->metadata['annual_raise'] ?? [];
                                $annualRaiseLabel = ($annualRaise['type'] ?? null) === 'percentage'
                                    ? '+'.number_format((float) ($annualRaise['value'] ?? 0), 2, ',', ' ').'%/ano'
                                    : '+'.number_format((float) ($annualRaise['value'] ?? 0), 2, ',', ' ').' €/ano';
                            @endphp
                            <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                {{ $job->description }}
                                <span class="text-[9px] bg-indigo-500/10 text-indigo-500 px-2 py-0.5 rounded-lg border border-indigo-500/20 font-black">CONTRATO</span>
                                @if(! empty($annualRaise['enabled']))
                                    <span class="text-[9px] bg-emerald-500/10 text-emerald-700 px-2 py-0.5 rounded-lg border border-emerald-500/20 font-black dark:text-emerald-300">
                                        AUMENTO ANUAL {{ $annualRaiseLabel }}
                                    </span>
                                @endif
                            </p>
                             {{-- ADICIONA ESTA LINHA ABAIXO --}}
    <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
        Registado em:
        <span class="text-zinc-600 dark:text-zinc-300 font-black">
            {{ $job->created_at->translatedFormat('d M, Y - H:i') }}
        </span>
    </p>
                            <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                                {{ $job->notes ?: 'Vínculo laboral ativo' }}
                            </p>
                            <div class="mt-1">@include('livewire.partials.bank-source-badge', ['record' => $job])</div>
                            <button type="button" wire:click="openSalaryPaymentHistory({{ $job->id }})" class="mt-3 inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-[9px] font-black uppercase tracking-wider text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-900/50 dark:bg-indigo-950/30 dark:text-indigo-300 dark:hover:bg-indigo-950/60">
                                <flux:icon name="clock" variant="micro" class="size-3" />
                                Ver histórico de salários
                            </button>
                        </div>
                    </div>

                    @php
                        $contractExtras = $salaryExtraIncomes->where('salary_recurring_income_id', $job->id);
                        $salaryAddedThisMonth = $contractExtras
                            ->where('added_to_salary', true)
                            ->filter(fn ($extra) => $extra->received_at?->month === now()->month && $extra->received_at?->year === now()->year)
                            ->sum(fn ($extra) => (float) ($extra->amount_converted ?? $extra->amount));
                        $mealAllowanceMonthly = (float) ($job->metadata['calculated_sa']
                            ?? ((float) ($job->metadata['meal_allowance'] ?? 0) * (int) ($job->metadata['working_days'] ?? 22)));
                        $hasSeparateMealCard = ! empty($job->metadata['meal_card_income_id']);
                        $availableReal = (float) $job->amount + ($hasSeparateMealCard ? $mealAllowanceMonthly : 0);
                        $salaryBonus = $job->metadata['bonus'] ?? [];
                    @endphp
                    <div class="text-right">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Disponível Real (líquido + alimentação)</p>
                        <span class="text-3xl font-black text-emerald-600 tracking-tighter italic">
                            {{ number_format($availableReal, 2, ',', ' ') }}€
                        </span>
                        @if($mealAllowanceMonthly > 0)
                            <p class="mt-1 text-xs font-semibold text-amber-700 dark:text-amber-300">
                                Subsídio de alimentação: {{ number_format($mealAllowanceMonthly, 2, ',', ' ') }}€/mês{{ $hasSeparateMealCard ? ' (somado ao total)' : ' (incluído no líquido)' }}
                            </p>
                        @endif
                        @if($salaryAddedThisMonth > 0)
                            <p class="mt-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                Serviços extra a receber no próximo salário: +{{ number_format($salaryAddedThisMonth, 2, ',', ' ') }}€
                            </p>
                        @endif
                        @if(in_array($salaryBonus['type'] ?? null, ['one_off', 'monthly', 'annual'], true))
                            <p class="mt-1 text-xs font-semibold text-amber-700 dark:text-amber-300">
                                Prémio:
                                {{ number_format((float) ($salaryBonus['net'] ?? 0), 2, ',', ' ') }}€ líquidos
                                <span class="font-normal text-zinc-500">(bruto {{ number_format((float) ($salaryBonus['gross'] ?? 0), 2, ',', ' ') }}€)</span>
                            </p>
                        @endif
                        <div class="mt-3 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2 dark:border-emerald-900/40 dark:bg-emerald-950/30">
                            <p class="text-[8px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-300">Estimativa líquida em 1 ano</p>
                            <p class="mt-0.5 text-lg font-black tabular-nums text-emerald-700 dark:text-emerald-300">{{ number_format($job->annual_net_estimate, 2, ',', ' ') }}€</p>
                            <p class="text-[10px] font-medium text-emerald-700/80 dark:text-emerald-300/80">Inclui subsídios configurados e alimentação (11 meses)</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                    <p class="mb-3 text-[9px] font-black uppercase tracking-[0.18em] text-zinc-400">Resumo mensal do salário</p>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/30">
                            <div class="mb-3 flex items-center justify-between gap-2">                                <span class="text-[9px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-300">Salário Bruto</span>
                                <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                    <button type="button" @click.stop="open = !open" :aria-expanded="open" aria-label="Explicação do salário bruto" class="flex size-6 items-center justify-center rounded-full bg-white text-[11px] font-black text-indigo-500 shadow-sm hover:bg-indigo-100 dark:bg-zinc-900 dark:hover:bg-indigo-900">i</button>
                                    <span x-show="open" x-cloak role="tooltip" class="absolute right-0 top-full z-30 mt-2 w-56 rounded-xl bg-zinc-900 px-3 py-2 text-left text-[10px] font-medium normal-case leading-relaxed text-white shadow-xl">Valor do salário antes dos descontos de Segurança Social e IRS.</span>
                                </div>
                            </div>
                            <p class="text-xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($job->metadata['salary_gross'] ?? 0, 2, ',', ' ') }}€</p>
                            <p class="mt-1 text-[9px] font-bold uppercase text-zinc-400">Valor antes dos descontos</p>
                        </div>

                        <div class="rounded-2xl border border-rose-100 bg-rose-50/70 p-4 dark:border-rose-900/40 dark:bg-rose-950/20">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-300">Segurança Social · 11%</span>
                                <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                    <button type="button" @click.stop="open = !open" :aria-expanded="open" aria-label="Explicação da Segurança Social" class="flex size-6 items-center justify-center rounded-full bg-white text-[11px] font-black text-rose-500 shadow-sm hover:bg-rose-100 dark:bg-zinc-900 dark:hover:bg-rose-900">i</button>
                                    <span x-show="open" x-cloak role="tooltip" class="absolute right-0 top-full z-30 mt-2 w-56 rounded-xl bg-zinc-900 px-3 py-2 text-left text-[10px] font-medium normal-case leading-relaxed text-white shadow-xl">Contribuição do trabalhador para a Segurança Social, estimada à taxa de 11%.</span>
                                </div>
                            </div>
                            <p class="text-xl font-black tabular-nums text-rose-600 dark:text-rose-400">−{{ number_format($job->metadata['calculated_ss'] ?? (($job->metadata['salary_gross'] ?? 0) * 0.11), 2, ',', ' ') }}€</p>
                            <p class="mt-1 text-[9px] font-bold uppercase text-rose-500/70 dark:text-rose-300/70">Desconto para a Segurança Social</p>
                        </div>

                        <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-4 dark:border-amber-900/40 dark:bg-amber-950/20">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-300">Subs. Alimentação</span>
                                <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                    <button type="button" @click.stop="open = !open" :aria-expanded="open" aria-label="Explicação do subsídio de alimentação" class="flex size-6 items-center justify-center rounded-full bg-white text-[11px] font-black text-amber-600 shadow-sm hover:bg-amber-100 dark:bg-zinc-900 dark:hover:bg-amber-900">i</button>
                                    <span x-show="open" x-cloak role="tooltip" class="absolute right-0 top-full z-30 mt-2 w-56 rounded-xl bg-zinc-900 px-3 py-2 text-left text-[10px] font-medium normal-case leading-relaxed text-white shadow-xl">Estimativa mensal: valor diário do subsídio multiplicado pelos dias trabalhados.</span>
                                </div>
                            </div>
                            <p class="text-xl font-black tabular-nums text-emerald-600 dark:text-emerald-400">+{{ number_format($job->metadata['calculated_sa'] ?? 0, 2, ',', ' ') }}€</p>
                            <p class="mt-1 text-[9px] font-bold uppercase text-amber-700/70 dark:text-amber-300/70">Acresce ao valor líquido</p>
                        </div>

                        <div class="rounded-2xl border border-orange-100 bg-orange-50/70 p-4 dark:border-orange-900/40 dark:bg-orange-950/20">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-300">Retenção IRS</span>
                                <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                    <button type="button" @click.stop="open = !open" :aria-expanded="open" aria-label="Explicação do IRS" class="flex size-6 items-center justify-center rounded-full bg-white text-[11px] font-black text-orange-500 shadow-sm hover:bg-orange-100 dark:bg-zinc-900 dark:hover:bg-orange-900">i</button>
                                    <span x-show="open" x-cloak role="tooltip" class="absolute right-0 top-full z-30 mt-2 w-56 rounded-xl bg-zinc-900 px-3 py-2 text-left text-[10px] font-medium normal-case leading-relaxed text-white shadow-xl">Retenção mensal estimada antes de qualquer apuramento anual da isenção IRS Jovem.</span>
                                </div>
                            </div>
                            <p class="text-xl font-black tabular-nums text-orange-600 dark:text-orange-400">−{{ number_format($job->metadata['calculated_irs'] ?? 0, 2, ',', ' ') }}€</p>
                            <p class="mt-1 text-[9px] font-bold uppercase text-orange-500/70 dark:text-orange-300/70">Imposto retido estimado</p>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        <button type="button" wire:click="openSalaryDetails({{ $job->id }})" class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-[9px] font-black uppercase text-indigo-700 shadow-sm transition hover:bg-indigo-100 dark:border-indigo-900/50 dark:bg-indigo-950/30 dark:text-indigo-300">
                            Detalhes salariais
                        </button>
                        <button wire:click="openRaiseModal({{ $job->id }})" class="flex items-center gap-2 rounded-xl bg-emerald-500/10 px-3 py-2 text-[9px] font-black uppercase text-emerald-600 shadow-sm transition-all hover:bg-emerald-600 hover:text-white">
                            <flux:icon name="rocket-launch" variant="micro" class="size-3" />
                            Aumento
                        </button>
                        <button type="button" wire:click="editFixed({{ $job->id }})" class="inline-flex items-center gap-2 rounded-xl border-2 border-indigo-300 bg-indigo-600 px-4 py-2.5 text-[10px] font-black uppercase tracking-wide text-white shadow-md transition hover:border-indigo-500 hover:bg-indigo-700 dark:border-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <flux:icon name="pencil-square" variant="micro" class="size-4" />
                            Editar registo
                        </button>
                        <flux:button wire:click="deleteFixed({{ $job->id }})" wire:confirm="Apagar contrato?" variant="ghost" icon="trash" size="xs" color="red" />
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest">Nenhum contrato de trabalho registado</p>
            </div>
        @endforelse
    </div>
</div>

<section class="mt-10 space-y-4">
    <div class="flex items-center gap-3 px-2">
        <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-300">
            <flux:icon name="sparkles" variant="outline" class="size-4" />
        </div>
        <div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-500">Receitas extra e prémios · {{ now()->year }}</h2>
            <p class="mt-1 text-[10px] text-zinc-400">Gestão separada dos serviços realizados e dos prémios de cada contrato.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @foreach($salaryContracts as $job)
            @php
                $contractExtras = $salaryExtraIncomes->where('salary_recurring_income_id', $job->id);
                $contractServices = $contractExtras->filter(fn ($extra) => ($extra->salary_income_type ?? 'service') !== 'prize');
                $contractPrizes = $contractExtras->where('salary_income_type', 'prize');
                $contractServicesThisYear = $contractServices->filter(fn ($extra) => $extra->received_at?->year === now()->year);
                $contractServicesThisMonth = $contractServicesThisYear->filter(fn ($extra) => $extra->received_at?->month === now()->month);
                $contractPrizesThisYear = $contractPrizes->filter(fn ($extra) => $extra->received_at?->year === now()->year);
                $contractServicesTotal = $contractServicesThisYear->sum(fn ($extra) => (float) ($extra->amount_converted ?? $extra->amount));
                $contractServicesMonthTotal = $contractServicesThisMonth->sum(fn ($extra) => (float) ($extra->amount_converted ?? $extra->amount));
                $contractServicesMonthCount = $contractServicesThisMonth->sum(fn ($extra) => (int) ($extra->service_quantity ?? 1));
                $contractNextSalaryTotal = $contractServicesThisMonth->where('added_to_salary', true)->sum(fn ($extra) => (float) ($extra->amount_converted ?? $extra->amount));
                $contractPrizesTotal = $contractPrizesThisYear->sum(fn ($extra) => (float) ($extra->amount_converted ?? $extra->amount));
            @endphp
            <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-100">{{ $job->description }}</h3>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-emerald-200/70 bg-white/70 p-3 dark:border-emerald-900/40 dark:bg-zinc-900/50">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-300">Serviços extra · {{ now()->year }}</p>
                            <button type="button" wire:click="openExtraModal({{ $job->id }})" class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-100 px-2.5 py-1.5 text-[8px] font-black uppercase text-emerald-800 transition hover:bg-emerald-600 hover:text-white dark:border-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
                                <flux:icon name="plus" variant="micro" class="size-3" />
                                Adicionar serviço
                            </button>
                        </div>
                        <p class="mt-1 text-xl font-black tabular-nums text-emerald-700 dark:text-emerald-300">{{ number_format($contractServicesTotal, 2, ',', ' ') }} {{ $workspaceCurrency }}</p>
                        <p class="text-[10px] font-medium text-zinc-500">{{ $contractServicesThisYear->sum(fn ($extra) => (int) ($extra->service_quantity ?? 1)) }} serviço{{ $contractServicesThisYear->sum(fn ($extra) => (int) ($extra->service_quantity ?? 1)) === 1 ? '' : 's' }} realizado{{ $contractServicesThisYear->sum(fn ($extra) => (int) ($extra->service_quantity ?? 1)) === 1 ? '' : 's' }} este ano</p>
                        <p class="mt-1 text-[10px] font-semibold text-emerald-700 dark:text-emerald-300">Este mês: {{ $contractServicesMonthCount }} serviço{{ $contractServicesMonthCount === 1 ? '' : 's' }} · {{ number_format($contractServicesMonthTotal, 2, ',', ' ') }} {{ $workspaceCurrency }}</p>
                        @if($contractNextSalaryTotal > 0)
                            <p class="mt-1 text-[10px] font-bold text-indigo-700 dark:text-indigo-300">No salário de {{ now()->addMonth()->translatedFormat('F') }}: +{{ number_format($contractNextSalaryTotal, 2, ',', ' ') }} {{ $workspaceCurrency }}</p>
                        @endif
                        <button type="button" wire:click="openSalaryIncomeHistory({{ $job->id }}, 'service')" class="mt-3 rounded-lg border border-emerald-200 bg-white px-2.5 py-1.5 text-[8px] font-black uppercase text-emerald-700 transition hover:border-emerald-500 dark:border-emerald-900/50 dark:bg-zinc-900 dark:text-emerald-300">
                            Ver histórico ({{ $contractServices->count() }})
                        </button>
                    </div>
                    <div class="rounded-xl border border-amber-200/70 bg-white/70 p-3 dark:border-amber-900/40 dark:bg-zinc-900/50">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <p class="text-[9px] font-black uppercase tracking-widest text-amber-700 dark:text-amber-300">Prémios · {{ now()->year }}</p>
                            <button type="button" wire:click="editSalaryBonus({{ $job->id }})" class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-[8px] font-black uppercase text-amber-700 transition hover:border-amber-500 hover:bg-amber-500 hover:text-white dark:border-amber-900/40 dark:bg-amber-900/10 dark:text-amber-300">
                                <flux:icon name="gift" variant="micro" class="size-3" />
                                Configurar prémio
                            </button>
                        </div>
                        <p class="mt-1 text-xl font-black tabular-nums text-amber-700 dark:text-amber-300">{{ number_format($contractPrizesTotal, 2, ',', ' ') }} {{ $workspaceCurrency }}</p>
                        <p class="text-[10px] font-medium text-zinc-500">{{ $contractPrizesThisYear->count() }} prémio{{ $contractPrizesThisYear->count() === 1 ? '' : 's' }} recebido{{ $contractPrizesThisYear->count() === 1 ? '' : 's' }} este ano</p>
                        <p class="mt-3 text-[10px] text-zinc-500">Prémio configurado no contrato: {{ in_array($job->metadata['bonus']['type'] ?? null, ['one_off', 'monthly', 'annual'], true) ? number_format((float) ($job->metadata['bonus']['net'] ?? 0), 2, ',', ' ').' '.$workspaceCurrency.' líquidos' : 'Nenhum' }}</p>
                        <button type="button" wire:click="openSalaryIncomeHistory({{ $job->id }}, 'prize')" class="mt-3 rounded-lg border border-amber-200 bg-white px-2.5 py-1.5 text-[8px] font-black uppercase text-amber-700 transition hover:border-amber-500 dark:border-amber-900/50 dark:bg-zinc-900 dark:text-amber-300">
                            Ver histórico ({{ $contractPrizes->count() }})
                        </button>
                    </div>
                </div>
                <p class="mt-3 text-[10px] text-zinc-500 dark:text-zinc-400">Os serviços selecionados para o salário são pagos no mês seguinte à data de realização.</p>
            </article>
        @endforeach
        @if($salaryContracts->isEmpty())
            <p class="rounded-2xl border border-dashed border-zinc-300 p-6 text-center text-xs text-zinc-500 xl:col-span-2">Regista um contrato de trabalho para associar serviços extra ou prémios.</p>
        @endif
    </div>
</section>

@include('livewire.partials.unlinked-extra-incomes')

@if($salaryDetails)
    <style>
        body:has([aria-label="Detalhes do rendimento salarial"]) button[aria-label="Fechar modal"],
        body:has([aria-label="Detalhes do rendimento salarial"]) button[aria-label="Close modal"],
        body:has([aria-label="Detalhes do rendimento salarial"]) [data-flux-modal-close] {
            display: none !important;
        }
    </style>
    @php
        $detailMeta = $salaryDetails->metadata ?? [];
        $detailTabs = [
            'overview' => 'Visão geral',
            'professional' => 'Profissional',
            'composition' => 'Composição',
            'taxes' => 'Impostos',
            'benefits' => 'Benefícios',
            'payments' => 'Pagamentos',
            'documents' => 'Documentos',
            'notes' => 'Notas',
        ];
        $detailContractLabels = ['sem_termo' => 'Sem termo', 'termo_certo' => 'Termo certo', 'termo_incerto' => 'Termo incerto', 'estagio' => 'Estágio', 'prestacao_servicos' => 'Prestação de serviços', 'outro' => 'Outro'];
        $detailAnnualPayments = (int) ($detailMeta['annual_payments'] ?? 14);
        $detailMonthlyDuodecimos = (float) ($detailMeta['monthly_duodecimos'] ?? 0);
        $detailGross = (float) ($detailMeta['salary_gross'] ?? 0);
        $detailMealTotal = (float) ($detailMeta['calculated_sa'] ?? ((float) ($detailMeta['meal_allowance'] ?? 0) * (int) ($detailMeta['working_days'] ?? 0)));
        $detailStatus = $detailMeta['contract_status'] ?? ($salaryDetails->is_active ? 'active' : 'suspended');
        $detailStatusLabels = ['active' => 'Ativo', 'ended' => 'Terminado', 'suspended' => 'Suspenso'];
    @endphp
    <div class="fixed inset-0 z-[70] flex items-center justify-center bg-zinc-950/70 p-2 backdrop-blur-sm sm:p-5" role="dialog" aria-modal="true" aria-label="Detalhes do rendimento salarial">
        <section class="flex max-h-[96vh] w-full max-w-6xl flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">
            <header class="flex items-start gap-4 border-b border-zinc-100 p-5 dark:border-zinc-800 sm:p-7">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950">
                    <flux:icon name="briefcase" class="size-6" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="truncate text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-white">{{ $salaryDetails->description }}</h2>
                        <span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase {{ $detailStatus === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($detailStatus === 'ended' ? 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300') }}">{{ $detailStatusLabels[$detailStatus] ?? 'Ativo' }}</span>
                    </div>
                    <p class="mt-1 text-xs text-zinc-500">{{ $detailMeta['position'] ?? 'Cargo não indicado' }} · {{ $detailContractLabels[$detailMeta['contract_type'] ?? ''] ?? 'Tipo de contrato não indicado' }}</p>
                </div>
                <button type="button" wire:click="closeSalaryDetails" aria-label="Fechar detalhes" class="rounded-xl p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </header>

            <nav class="flex gap-2 overflow-x-auto border-b border-zinc-100 px-4 py-3 dark:border-zinc-800 sm:px-6" aria-label="Separadores do rendimento salarial">
                @foreach($detailTabs as $tabKey => $tabLabel)
                    <button type="button" wire:click="setSalaryDetailsTab('{{ $tabKey }}')" aria-current="{{ $salaryDetailsTab === $tabKey ? 'page' : 'false' }}"
                        class="shrink-0 rounded-xl px-3 py-2 text-[9px] font-black uppercase tracking-wide transition {{ $salaryDetailsTab === $tabKey ? 'bg-indigo-600 text-white' : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}">
                        {{ $tabLabel }}
                    </button>                @endforeach
            </nav>

            <div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
                @if($salaryDetailsTab === 'overview')
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl bg-indigo-50 p-4 dark:bg-indigo-950/40"><p class="text-[9px] font-black uppercase text-indigo-500">Bruto mensal</p><p class="mt-1 text-xl font-black text-indigo-700 dark:text-indigo-300">{{ number_format($detailGross, 2, ',', ' ') }} €</p></div>
                        <div class="rounded-2xl bg-emerald-50 p-4 dark:bg-emerald-950/30"><p class="text-[9px] font-black uppercase text-emerald-600">Líquido mensal estimado</p><p class="mt-1 text-xl font-black text-emerald-700 dark:text-emerald-300">{{ number_format((float) $salaryDetails->amount, 2, ',', ' ') }} €</p></div>
                        <div class="rounded-2xl bg-rose-50 p-4 dark:bg-rose-950/30"><p class="text-[9px] font-black uppercase text-rose-600">Segurança Social estimada</p><p class="mt-1 text-xl font-black text-rose-700 dark:text-rose-300">{{ number_format((float) ($salaryDetailsPayroll['social_security'] ?? 0), 2, ',', ' ') }} €</p></div>
                        <div class="rounded-2xl bg-amber-50 p-4 dark:bg-amber-950/30"><p class="text-[9px] font-black uppercase text-amber-600">Alimentação estimada</p><p class="mt-1 text-xl font-black text-amber-700 dark:text-amber-300">{{ number_format($detailMealTotal, 2, ',', ' ') }} €</p></div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <section class="rounded-2xl border border-zinc-200 p-5 dark:border-zinc-800">
                            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Resumo do contrato</h3>
                            <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                <div><dt class="text-[9px] font-bold uppercase text-zinc-400">Cargo</dt><dd class="mt-1 font-semibold text-zinc-800 dark:text-zinc-100">{{ $detailMeta['position'] ?? '—' }}</dd></div>
                                <div><dt class="text-[9px] font-bold uppercase text-zinc-400">Departamento</dt><dd class="mt-1 font-semibold text-zinc-800 dark:text-zinc-100">{{ $detailMeta['department'] ?? '—' }}</dd></div>
                                <div><dt class="text-[9px] font-bold uppercase text-zinc-400">Início</dt><dd class="mt-1 font-semibold text-zinc-800 dark:text-zinc-100">{{ ! empty($detailMeta['start_date']) ? \Carbon\Carbon::parse($detailMeta['start_date'])->format('d/m/Y') : '—' }}</dd></div>
                                <div><dt class="text-[9px] font-bold uppercase text-zinc-400">Dia habitual de pagamento</dt><dd class="mt-1 font-semibold text-zinc-800 dark:text-zinc-100">{{ $salaryDetails->day_of_month }}</dd></div>
                            </dl>
                        </section>
                        <section class="rounded-2xl border border-amber-200 bg-amber-50/60 p-5 dark:border-amber-900/40 dark:bg-amber-950/20">
                            <h3 class="text-xs font-black uppercase tracking-widest text-amber-700 dark:text-amber-300">Estimativa, não recibo oficial</h3>
                            <p class="mt-2 text-xs leading-5 text-zinc-600 dark:text-zinc-300">O cálculo usa as tabelas configuradas para o Continente em 2026. A região fiscal fica registada, mas Madeira e Açores ainda não têm tabelas próprias neste simulador. Confirma os valores no recibo e nas tabelas oficiais.</p>
                        </section>
                    </div>
                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <button type="button" wire:click="editFixed({{ $salaryDetails->id }})" class="rounded-xl bg-indigo-600 px-4 py-2 text-[10px] font-black uppercase text-white hover:bg-indigo-700">Editar contrato</button>
                    </div>
                @elseif($salaryDetailsTab === 'professional')
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach([
                            'Empresa' => $salaryDetails->description,
                            'Cargo / função' => $detailMeta['position'] ?? '—',
                            'Departamento' => $detailMeta['department'] ?? '—',
                            'Tipo de contrato' => $detailContractLabels[$detailMeta['contract_type'] ?? ''] ?? '—',
                            'Estado' => $detailStatusLabels[$detailStatus] ?? $detailStatus,
                            'Periodicidade' => ucfirst($salaryDetails->frequency ?? 'mensal'),
                            'Dia de pagamento' => $salaryDetails->day_of_month,
                            'Data de início' => ! empty($detailMeta['start_date']) ? \Carbon\Carbon::parse($detailMeta['start_date'])->format('d/m/Y') : '—',
                            'Data de fim' => ! empty($detailMeta['end_date']) ? \Carbon\Carbon::parse($detailMeta['end_date'])->format('d/m/Y') : '—',
                        ] as $label => $value)
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800"><p class="text-[9px] font-black uppercase text-zinc-400">{{ $label }}</p><p class="mt-1 text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $value }}</p></div>
                        @endforeach
                    </div>
                @elseif($salaryDetailsTab === 'composition')
                    <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <table class="w-full min-w-[32rem] text-sm">
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                <tr><th class="p-4 text-left text-xs font-semibold text-zinc-500">Salário base bruto mensal</th><td class="p-4 text-right font-bold">{{ number_format($detailGross, 2, ',', ' ') }} €</td></tr>
                                <tr><th class="p-4 text-left text-xs font-semibold text-zinc-500">Salário bruto anual estimado ({{ $detailAnnualPayments }} pagamentos)</th><td class="p-4 text-right font-bold">{{ number_format($detailGross * $detailAnnualPayments, 2, ',', ' ') }} €</td></tr>
                                <tr><th class="p-4 text-left text-xs font-semibold text-zinc-500">Subsídio de férias</th><td class="p-4 text-right font-bold">{{ number_format(($detailMeta['holiday_allowance_same_as_salary'] ?? true) ? $detailGross : (float) ($detailMeta['holiday_allowance_gross'] ?? 0), 2, ',', ' ') }} €</td></tr>
                                <tr><th class="p-4 text-left text-xs font-semibold text-zinc-500">Subsídio de Natal</th><td class="p-4 text-right font-bold">{{ number_format(($detailMeta['christmas_allowance_same_as_salary'] ?? true) ? $detailGross : (float) ($detailMeta['christmas_allowance_gross'] ?? 0), 2, ',', ' ') }} €</td></tr>
                                <tr><th class="p-4 text-left text-xs font-semibold text-zinc-500">Duodécimos mensais ({{ $detailMeta['duodecimos_percentage'] ?? (($detailMeta['allowances_in_duodecimos'] ?? false) ? 100 : 0) }}%)</th><td class="p-4 text-right font-bold">{{ number_format($detailMonthlyDuodecimos, 2, ',', ' ') }} €</td></tr>
                                <tr><th class="p-4 text-left text-xs font-semibold text-zinc-500">Outros complementos</th><td class="p-4 text-right font-bold">{{ number_format((float) ($detailMeta['other_complements'] ?? 0), 2, ',', ' ') }} €</td></tr>
                                <tr><th class="p-4 text-left text-xs font-semibold text-zinc-500">Prémio / horas extra / outros</th><td class="p-4 text-right font-bold">{{ number_format((float) ($detailMeta['productivity_bonus'] ?? 0) + (float) ($detailMeta['overtime'] ?? 0) + (float) ($detailMeta['other_income'] ?? 0), 2, ',', ' ') }} €</td></tr>
                            </tbody>
                        </table>
                    </div>
                @elseif($salaryDetailsTab === 'taxes')
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4 dark:border-rose-900/30 dark:bg-rose-950/20"><p class="text-[9px] font-black uppercase text-rose-600">Segurança Social · 11%</p><p class="mt-1 text-xl font-black text-rose-700 dark:text-rose-300">{{ number_format((float) ($salaryDetailsPayroll['social_security'] ?? 0), 2, ',', ' ') }} €/mês</p><p class="text-[10px] text-zinc-500">Incidência estimada: {{ number_format((float) ($salaryDetailsPayroll['taxable_remuneration'] ?? 0), 2, ',', ' ') }} € · anual: {{ number_format((float) ($salaryDetailsPayroll['social_security'] ?? 0) * $detailAnnualPayments, 2, ',', ' ') }} €</p></div>
                        <div class="rounded-2xl border border-orange-100 bg-orange-50 p-4 dark:border-orange-900/30 dark:bg-orange-950/20"><p class="text-[9px] font-black uppercase text-orange-600">Retenção IRS mensal (estimativa)</p><p class="mt-1 text-xl font-black text-orange-700 dark:text-orange-300">{{ number_format((float) ($salaryDetailsPayroll['irs'] ?? 0), 2, ',', ' ') }} €</p><p class="text-[10px] text-zinc-500">Tabela {{ $salaryDetailsPayroll['table'] ?? '—' }} · anual: {{ number_format((float) ($salaryDetailsPayroll['irs'] ?? 0) * $detailAnnualPayments, 2, ',', ' ') }} € · {{ ($detailMeta['withholding_status'] ?? 'normal') === 'exempt' ? 'sem retenção na fonte indicada' : 'retenção normal estimada' }}</p></div>
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800"><p class="text-[9px] font-black uppercase text-zinc-500">Situação fiscal</p><p class="mt-1 text-sm font-bold">{{ $detailMeta['civil_status'] ?? 'Não indicado' }} · {{ $detailMeta['dependents'] ?? 0 }} dependentes · {{ $detailMeta['tax_region'] ?? 'continente' }}</p></div>
                        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/30 dark:bg-indigo-950/20"><p class="text-[9px] font-black uppercase text-indigo-600">IRS Jovem · isenção separada</p>
                            @if($detailMeta['irs_jovem'] ?? false)
                                <p class="mt-1 text-sm font-bold">{{ $detailMeta['irs_jovem_year'] ?? 1 }}.º ano · {{ number_format((float) ($salaryDetailsPayroll['irs_jovem_rate'] ?? 0) * 100, 0) }}% de isenção legal aplicável</p>
                                <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">Rendimento elegível estimado: {{ number_format((float) ($salaryDetailsPayroll['irs_jovem_eligible_income'] ?? 0), 2, ',', ' ') }} €/mês. Parcela mensal potencialmente isenta: {{ number_format((float) ($salaryDetailsPayroll['irs_jovem_exempt_income'] ?? 0), 2, ',', ' ') }} €.</p>
                                <p class="mt-1 text-xs font-semibold text-indigo-700 dark:text-indigo-300">Poupança indicativa, não deduzida da retenção: ~{{ number_format((float) ($salaryDetailsPayroll['irs_jovem_estimated_savings'] ?? 0), 2, ',', ' ') }} €/mês (até ~{{ number_format((float) ($salaryDetailsPayroll['irs_jovem_estimated_savings'] ?? 0) * $detailAnnualPayments, 2, ',', ' ') }} €/ano estimados).</p>
                                <p class="text-[10px] text-zinc-500">Início do benefício: {{ $detailMeta['irs_jovem_start_year'] ?? 'não indicado' }}.</p>
                            @else
                                <p class="mt-1 text-sm text-zinc-500">Não configurado neste contrato.</p>
                            @endif
                        </div>
                    </div>
                    <p class="mt-4 rounded-xl bg-zinc-50 p-3 text-[10px] leading-5 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Fórmulas: Segurança Social estimada = remuneração contributiva × 11%; IRS = cálculo da tabela de retenção selecionada (Continente, 2026), dependentes e situação civil. Os valores anuais multiplicam a estimativa mensal pelo número de pagamentos configurado.</p>
                    <p class="mt-2 rounded-xl bg-amber-50 p-3 text-[10px] leading-5 text-amber-800 dark:bg-amber-950/30 dark:text-amber-200">IRS Jovem é uma isenção no apuramento do IRS e não uma redução aplicada à taxa de retenção na fonte. A poupança mostrada é apenas indicativa, baseada nas tabelas mensais; não substitui a liquidação anual.</p>
                @elseif($salaryDetailsTab === 'benefits')
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/30 dark:bg-amber-950/20"><p class="text-[9px] font-black uppercase text-amber-700">Alimentação</p><p class="mt-1 text-lg font-black">{{ number_format((float) ($detailMeta['meal_allowance'] ?? 0), 2, ',', ' ') }} €/dia</p><p class="text-xs text-zinc-500">{{ $detailMeta['working_days'] ?? 0 }} dias · {{ number_format($detailMealTotal, 2, ',', ' ') }} €/mês · {{ number_format($detailMealTotal * 11, 2, ',', ' ') }} €/ano estimado</p><p class="mt-1 text-[10px] text-zinc-500">Método: {{ $detailMeta['meal_allowance_destination'] ?? 'igual ao salário' }} · limite diário estimado de isenção: {{ number_format(($detailMeta['meal_allowance_destination'] ?? '') === 'meal_card' ? 10.455 : 6.15, 3, ',', ' ') }} €.</p><p class="text-[10px] text-zinc-500">Parcela mensal sujeita a tributação: {{ number_format((float) ($salaryDetailsPayroll['meal_taxable'] ?? 0), 2, ',', ' ') }} €.</p></div>
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800"><p class="text-[9px] font-black uppercase text-zinc-500">Subsídios de férias e Natal</p><p class="mt-1 text-sm font-bold">{{ ($detailMeta['duodecimos_percentage'] ?? (($detailMeta['allowances_in_duodecimos'] ?? false) ? 100 : 0)) }}% em duodécimos{{ (int) ($detailMeta['duodecimos_percentage'] ?? (($detailMeta['allowances_in_duodecimos'] ?? false) ? 100 : 0)) < 100 ? ' · remanescente pago nos meses correspondentes' : '' }}</p><p class="text-xs text-zinc-500">{{ number_format($detailMonthlyDuodecimos, 2, ',', ' ') }} €/mês em duodécimos</p></div>
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800"><p class="text-[9px] font-black uppercase text-zinc-500">Outros benefícios registados</p><p class="mt-1 text-sm font-bold">{{ $detailMeta['health_insurance'] ?? 'Sem seguro de saúde registado' }}</p><p class="text-xs text-zinc-500">Ajudas de custo: {{ number_format((float) ($detailMeta['expense_allowance'] ?? 0), 2, ',', ' ') }} €/mês</p><p class="mt-1 whitespace-pre-wrap text-xs text-zinc-500">{{ $detailMeta['additional_benefits'] ?? 'Sem benefícios adicionais registados.' }}</p><p class="mt-2 text-[10px] text-zinc-500">O tratamento fiscal/contributivo destes benefícios não é calculado automaticamente.</p></div>
                    </div>
                @elseif($salaryDetailsTab === 'payments')
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div><h3 class="text-sm font-black uppercase text-zinc-700 dark:text-zinc-200">Histórico de pagamentos</h3><p class="text-[10px] text-zinc-500">O sistema cria o registo mensal automaticamente e paga-o na data prevista. Podes editar os valores; o estado e a data são atualizados pelo sistema.</p></div>
                    </div>
                    @php
                        $paymentChart = $salaryPayments
                            ->sortBy(fn ($payment) => ((int) $payment->payment_year * 12) + (int) $payment->payment_month)
                            ->take(-12)
                            ->values();
                        $paymentChartMax = max(1, (float) $paymentChart->max('net_received'));
                    @endphp
                    @if($paymentChart->isNotEmpty())
                        <section class="mb-5 rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800">
                            <h4 class="mb-3 text-[9px] font-black uppercase tracking-widest text-zinc-500">Evolução do líquido recebido · últimos registos</h4>
                            <div class="flex h-36 items-end gap-2 overflow-x-auto border-b border-zinc-200 pb-2 dark:border-zinc-700">
                                @foreach($paymentChart as $paymentPoint)
                                    @php $barHeight = max(8, ((float) $paymentPoint->net_received / $paymentChartMax) * 100); @endphp
                                    <div class="flex h-full min-w-8 flex-1 flex-col items-center justify-end gap-1" title="{{ number_format((float) $paymentPoint->net_received, 2, ',', ' ') }} €">
                                        <span class="max-w-12 truncate text-[8px] font-bold text-zinc-500">{{ number_format((float) $paymentPoint->net_received, 0, ',', ' ') }}€</span>
                                        <span class="w-full max-w-10 rounded-t-md bg-indigo-500/80" style="height: {{ $barHeight }}%"></span>
                                        <span class="text-[8px] text-zinc-400">{{ \Carbon\Carbon::create($paymentPoint->payment_year, $paymentPoint->payment_month)->format('m/y') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                    @if($salaryPaymentForm)
                        <form wire:submit.prevent="saveSalaryPayment" class="mb-5 grid grid-cols-1 gap-3 rounded-2xl border border-indigo-200 bg-indigo-50/60 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/20 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach(['salary_base_gross' => 'Salário bruto', 'allowances_duodecimos' => 'Duodécimos', 'other_complements' => 'Complementos', 'social_security' => 'Segurança Social', 'irs' => 'IRS', 'other_deductions' => 'Outros descontos', 'meal_allowance' => 'Subsídio alimentação', 'net_received' => 'Líquido recebido'] as $field => $label)
                                <label class="text-[9px] font-bold uppercase text-zinc-500">{{ $label }} (€)<input type="number" min="0" step="0.01" wire:model="salaryPaymentForm.{{ $field }}" class="mt-1 h-10 w-full rounded-lg border-zinc-200 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-900"></label>
                            @endforeach
                            <label class="text-[9px] font-bold uppercase text-zinc-500 sm:col-span-2">Observações<input type="text" wire:model="salaryPaymentForm.notes" class="mt-1 h-10 w-full rounded-lg border-zinc-200 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-900"></label>
                            <div class="flex items-end gap-2"><button type="submit" class="h-10 rounded-lg bg-indigo-600 px-4 text-[9px] font-black uppercase text-white">Guardar</button><button type="button" wire:click="$set('salaryPaymentForm', [])" class="h-10 rounded-lg border border-zinc-300 px-4 text-[9px] font-black uppercase">Cancelar</button></div>
                        </form>
                    @endif
                    <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <table class="w-full min-w-[48rem] text-left text-xs">
                            <thead class="bg-zinc-50 text-[9px] uppercase text-zinc-500 dark:bg-zinc-800"><tr><th class="p-3">Mês</th><th class="p-3">Bruto</th><th class="p-3">SS</th><th class="p-3">IRS</th><th class="p-3">Alimentação</th><th class="p-3">Líquido</th><th class="p-3">Estado</th><th class="p-3">Ações</th></tr></thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @forelse($salaryPayments as $payment)
                                    <tr><td class="p-3 font-semibold">{{ \Carbon\Carbon::create($payment->payment_year, $payment->payment_month)->translatedFormat('F Y') }}<span class="block text-[9px] text-zinc-400">{{ $payment->paid_at?->format('d/m/Y') ?? 'Data prevista: '.$salaryDetails->day_of_month.'/'.str_pad((string) $payment->payment_month, 2, '0', STR_PAD_LEFT).'/'.$payment->payment_year }}</span>@if($payment->is_automatic)<span class="mt-1 inline-block rounded bg-indigo-50 px-1.5 py-0.5 text-[8px] font-black uppercase text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-300">Automático</span>@endif</td><td class="p-3">{{ number_format((float) $payment->salary_base_gross, 2, ',', ' ') }} €</td><td class="p-3">{{ number_format((float) $payment->social_security, 2, ',', ' ') }} €</td><td class="p-3">{{ number_format((float) $payment->irs, 2, ',', ' ') }} €</td><td class="p-3">{{ number_format((float) $payment->meal_allowance, 2, ',', ' ') }} €</td><td class="p-3 font-bold">{{ number_format((float) $payment->net_received, 2, ',', ' ') }} €</td><td class="p-3">{{ ['pending' => 'Pendente', 'paid' => 'Pago', 'late' => 'Atrasado'][$payment->status] ?? $payment->status }}</td><td class="p-3"><button type="button" wire:click="openSalaryPaymentForm({{ $payment->id }})" class="font-bold text-indigo-600">Editar valores</button></td></tr>
                                @empty
                                    <tr><td colspan="8" class="p-8 text-center text-zinc-500">Ainda não existem pagamentos registados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800"><p class="text-[9px] font-black uppercase text-zinc-500">Líquido estimado por mês</p><p class="mt-1 text-sm font-bold">{{ number_format((float) $salaryDetails->amount, 2, ',', ' ') }} €</p></div>
                        <div class="rounded-xl bg-emerald-50 p-3 dark:bg-emerald-950/30"><p class="text-[9px] font-black uppercase text-emerald-600">Líquido recebido registado em {{ now()->year }}</p><p class="mt-1 text-sm font-bold text-emerald-700 dark:text-emerald-300">{{ number_format((float) $salaryPayments->where('payment_year', now()->year)->sum('net_received'), 2, ',', ' ') }} €</p></div>
                    </div>
                @elseif($salaryDetailsTab === 'documents')
                    <form wire:submit.prevent="uploadSalaryDocument" class="mb-5 grid grid-cols-1 gap-3 rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800 sm:grid-cols-[1fr_auto_auto]">
                        <input type="file" wire:model="salaryDocumentFile" accept=".pdf,.jpg,.jpeg,.png,.webp" class="min-w-0 text-xs text-zinc-600 dark:text-zinc-300">
                        <select wire:model="salaryDocumentType" class="h-10 rounded-lg border-zinc-200 bg-white text-xs dark:border-zinc-700 dark:bg-zinc-900"><option value="contract">Contrato de trabalho</option><option value="payslip">Recibo de vencimento</option><option value="irs">Declaração de IRS</option><option value="payment_proof">Comprovativo de pagamento</option><option value="other">Outro documento</option></select>
                        <button type="submit" wire:loading.attr="disabled" wire:target="uploadSalaryDocument" class="h-10 rounded-lg bg-indigo-600 px-4 text-[9px] font-black uppercase text-white disabled:opacity-60"><span wire:loading.remove wire:target="uploadSalaryDocument">Carregar documento</span><span wire:loading wire:target="uploadSalaryDocument">A carregar…</span></button>
                        @error('salaryDocumentFile')<p class="text-xs text-rose-600 sm:col-span-3">{{ $message }}</p>@enderror
                        <p class="text-[10px] text-zinc-500 sm:col-span-3">PDF ou imagem, máximo 10 MB. Os ficheiros são guardados no disco privado.</p>
                    </form>
                    <div class="space-y-2">
                        @forelse($salaryDocuments as $document)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-zinc-200 p-3 dark:border-zinc-800">
                                <div class="min-w-0"><p class="truncate text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $document->original_name }}</p><p class="text-[9px] uppercase text-zinc-500">{{ ['contract' => 'Contrato', 'payslip' => 'Recibo', 'irs' => 'IRS', 'payment_proof' => 'Comprovativo', 'other' => 'Outro'][$document->document_type] ?? 'Documento' }} · {{ number_format($document->file_size / 1024, 0) }} KB</p></div>
                                <div class="flex gap-2"><a href="{{ route('salary-documents.preview', $document) }}" target="_blank" rel="noopener" class="rounded-lg border border-indigo-200 px-3 py-2 text-[9px] font-black uppercase text-indigo-700 dark:border-indigo-900 dark:text-indigo-300">Visualizar</a><button type="button" wire:click="downloadSalaryDocument({{ $document->id }})" class="rounded-lg border border-zinc-200 px-3 py-2 text-[9px] font-black uppercase text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">Descarregar</button><button type="button" wire:click="deleteSalaryDocument({{ $document->id }})" wire:confirm="Eliminar este documento?" class="rounded-lg border border-rose-200 px-3 py-2 text-[9px] font-black uppercase text-rose-600">Apagar</button></div>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-zinc-300 p-8 text-center text-sm text-zinc-500 dark:border-zinc-700">Ainda não existem documentos associados.</p>
                        @endforelse
                    </div>
                @elseif($salaryDetailsTab === 'notes')
                    <section class="rounded-2xl border border-zinc-200 p-5 dark:border-zinc-800">
                        <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Notas e observações do contrato</h3>
                        <p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-zinc-700 dark:text-zinc-200">{{ $salaryDetails->notes ?: 'Sem notas registadas.' }}</p>
                        <p class="mt-5 text-[10px] text-zinc-500">Para alterar as observações, utiliza “Editar contrato”. As observações por pagamento ficam no separador Histórico de pagamentos.</p>
                    </section>
                @endif
            </div>
        </section>
    </div>
@endif











{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: NOVO CONTRATO DE TRABALHO (COM AUDITORIA)             --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:modal-show-emprego.window="show()"
    x-on:modal-close-emprego.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 flex max-h-[88vh] w-full max-w-2xl flex-col overflow-hidden rounded-[2.5rem] border border-zinc-200 bg-white shadow-xl dark:border-zinc-800 dark:bg-zinc-900">

            <form wire:submit.prevent="saveEmployment" class="flex min-h-0 flex-col">
                {{-- HEADER --}}
                <div class="shrink-0 border-b border-zinc-100 p-5 dark:border-zinc-800 sm:p-6">
                    <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="briefcase" class="size-6" />
                    </div>                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white">{{ $editingFixedId ? 'Editar Salário' : 'Configurar Salário' }}</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Cálculo Automático de Impostos</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5 custom-scrollbar sm:p-6">

                    {{-- DESCRIÇÃO --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-indigo-600 z-10">Nome da Empresa</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Salário Mensal - Empresa X" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-indigo-500 transition-all">
                    </div>

                    <section class="space-y-3 rounded-3xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Dados profissionais e contrato</h3>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input type="text" wire:model="recPosition" placeholder="Cargo / função" class="h-11 rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                            <input type="text" wire:model="recDepartment" placeholder="Departamento" class="h-11 rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                            <select wire:model.live="recContractType" class="h-11 rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                <option value="sem_termo">Contrato sem termo</option><option value="termo_certo">Termo certo</option><option value="termo_incerto">Termo incerto</option><option value="estagio">Estágio</option><option value="prestacao_servicos">Prestação de serviços</option><option value="outro">Outro</option>
                            </select>
                            <select wire:model="recContractStatus" class="h-11 rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                <option value="active">Ativo</option><option value="ended">Terminado</option><option value="suspended">Suspenso</option>
                            </select>
                            <label class="text-[9px] font-bold uppercase text-zinc-500">Data de início<input type="date" wire:model="recStartDate" class="mt-1 h-11 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"></label>
                            @if($recContractType !== 'sem_termo')
                                <label class="text-[9px] font-bold uppercase text-zinc-500">Data de fim<input type="date" wire:model="recEndDate" class="mt-1 h-11 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"></label>
                            @endif
                            @unless($editingFixedId)
                                <label class="text-[9px] font-bold uppercase text-zinc-500">Estado da retenção<select wire:model.live="recWithholdingStatus" class="mt-1 h-11 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"><option value="normal">Retenção normal estimada</option><option value="exempt">Sem retenção na fonte</option></select></label>
                            @endunless
                        </div>
                        <p class="text-[10px] text-zinc-500">O dia habitual de pagamento é configurado abaixo. O cálculo fiscal disponível é uma estimativa para o Continente em 2026.</p>
                    </section>

                    {{-- VALORES BRUTO / LÍQUIDO --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Valor Bruto (€)</label>
                            <input type="number" step="0.01" wire:model.live="recSalaryGross" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Resultado Líquido (€)</label>
                            <input type="number" step="0.01" wire:model.live="recAmount" class="w-full h-14 bg-emerald-50 dark:bg-emerald-900/10 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none" readonly>
                        </div>
                    </div>
                    {{-- IRS JOVEM --}}
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/10 rounded-3xl border border-indigo-100 dark:border-indigo-900/30 space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="recIrsJovem" class="mt-0.5 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                            <span>
                                <span class="block text-sm font-black text-indigo-700 dark:text-indigo-300">Tenho IRS Jovem</span>
                                <span class="block text-[10px] text-zinc-500 mt-1">A retenção estimada continua a ser apresentada separadamente. A isenção não é aplicada como redução direta da taxa.</span>
                            </span>
                        </label>
                        @if($recIrsJovem)
                            <div>
                                <label class="text-[9px] font-black text-zinc-500 uppercase block mb-1">Ano de benefício</label>
                                <select wire:model.live="recIrsJovemYear" class="w-full h-11 bg-white dark:bg-zinc-800 border border-indigo-100 dark:border-indigo-900/30 rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                                    @for($year = 1; $year <= 10; $year++)
                                        <option value="{{ $year }}">{{ $year }}.º ano</option>
                                    @endfor
                                </select>
                            </div>
                            @if($recSalaryGross > 0)
                                <div class="rounded-xl border border-indigo-200 bg-white/80 p-3 dark:border-indigo-800 dark:bg-zinc-900/70">
                                    <p class="text-xs font-black text-indigo-700 dark:text-indigo-300">
                                        {{ number_format($calculatedIRSJovemRate * 100, 0) }}% de isenção no {{ $recIrsJovemYear }}.º ano
                                    </p>
                                    <p class="mt-1 text-[10px] text-zinc-600 dark:text-zinc-300">
                                        Remuneração mensal elegível isenta estimada: {{ number_format($calculatedIRSJovemExemptIncome, 2, ',', ' ') }} €.
                                    </p>
                                    <p class="mt-1 text-[10px] font-bold text-indigo-700 dark:text-indigo-300">
                                        IRS estimado após a isenção: {{ number_format((float) $calculatedIRS, 2, ',', ' ') }} €/mês.
                                        Líquido estimado com o benefício: ~{{ number_format((float) $recAmount, 2, ',', ' ') }} €/mês.
                                    </p>
                                    <p class="mt-1 text-[9px] text-zinc-500">A estimativa considera a parcela isenta e aplica a tabela de retenção apenas à remuneração não isenta. A taxa da tabela não é reduzida diretamente; o IRS Jovem continua sujeito ao apuramento anual.</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- SUBSÍDIOS DE FÉRIAS E NATAL --}}
                    <div class="p-4 bg-sky-50 dark:bg-sky-900/10 rounded-3xl border border-sky-100 dark:border-sky-900/30 space-y-3">
                        <div>
                            <p class="text-[10px] font-black uppercase text-sky-700 dark:text-sky-300 tracking-widest">Subsídios de Férias e de Natal</p>
                            <p class="text-[10px] text-zinc-500 mt-1">Define o valor bruto de cada subsídio e como é pago.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <label class="text-[9px] font-black text-zinc-500 uppercase block">Subsídio de férias</label>
                                <label class="flex items-center gap-2 text-xs font-bold text-zinc-700 dark:text-zinc-200 cursor-pointer">
                                    <input type="checkbox" wire:model.live="recHolidayAllowanceSameAsSalary" class="rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                                    Igual ao salário bruto
                                </label>
                                @unless($recHolidayAllowanceSameAsSalary)
                                    <input type="number" min="0" step="0.01" wire:model.live="recHolidayAllowanceGross" placeholder="Valor bruto (€)" class="w-full h-11 bg-white dark:bg-zinc-800 border border-sky-100 dark:border-sky-900/30 rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                                @endunless
                            </div>
                            <div class="space-y-3">
                                <label class="text-[9px] font-black text-zinc-500 uppercase block">Subsídio de Natal</label>
                                <label class="flex items-center gap-2 text-xs font-bold text-zinc-700 dark:text-zinc-200 cursor-pointer">
                                    <input type="checkbox" wire:model.live="recChristmasAllowanceSameAsSalary" class="rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                                    Igual ao salário bruto
                                </label>
                                @unless($recChristmasAllowanceSameAsSalary)
                                    <input type="number" min="0" step="0.01" wire:model.live="recChristmasAllowanceGross" placeholder="Valor bruto (€)" class="w-full h-11 bg-white dark:bg-zinc-800 border border-sky-100 dark:border-sky-900/30 rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                                @endunless
                            </div>
                        </div>

                        <label class="flex items-start gap-3 pt-4 border-t border-sky-100 dark:border-sky-900/30 cursor-pointer">
                            <input type="checkbox" wire:model.live="recAllowancesInDuodecimos" class="mt-0.5 rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                            <span>
                                <span class="block text-xs font-black text-zinc-700 dark:text-zinc-200">Receber em duodécimos</span>
                                <span class="block text-[10px] text-zinc-500 mt-1">Os dois subsídios são divididos por 12 e incluídos na estimativa mensal.</span>
                            </span>
                        </label>
                        @unless($recAllowancesInDuodecimos)
                            <p class="text-[10px] text-zinc-500">Recebidos por inteiro nos meses correspondentes; não incluídos na estimativa mensal.</p>
                        @endunless
                        @if($recAllowancesInDuodecimos)
                            @php
                                $monthlyDuodecimos = (
                                    ($recHolidayAllowanceSameAsSalary ? (float) $recSalaryGross : (float) $recHolidayAllowanceGross)
                                    + ($recChristmasAllowanceSameAsSalary ? (float) $recSalaryGross : (float) $recChristmasAllowanceGross)
                                ) / 12;
                            @endphp
                            <p class="text-[10px] font-bold text-sky-700 dark:text-sky-300">
                                Acréscimo bruto mensal estimado: + {{ number_format($monthlyDuodecimos, 2, ',', ' ') }} €
                            </p>
                        @endif
                    </div>

                    {{-- SUBSIDIO ALIMENTAÇÃO --}}
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-3xl border border-amber-100 dark:border-amber-900/30">
                        <p class="text-[10px] font-black uppercase text-amber-600 mb-4 ml-1 tracking-widest">Subsídio de Alimentação</p>
                        <p class="text-[10px] text-amber-800 dark:text-amber-200 mb-4">
                            A estimativa anual considera 11 meses: normalmente não há subsídio de alimentação durante o mês de férias. O valor por dia e os dias trabalhados são editáveis.
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">Valor por dia</label>
                                <input type="number" step="0.01" wire:model.live="recMealAllowance" class="w-full h-11 bg-white dark:bg-zinc-800 border-none rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                            </div>
                            <div class="relative">
                                <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">Dias trabalhados</label>
                                <input type="number" wire:model.live="recWorkingDays" class="w-full h-11 bg-white dark:bg-zinc-800 border-none rounded-xl px-4 font-bold text-sm text-center text-zinc-900 dark:text-white">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">Onde entra o subsídio de alimentação?</label>
                            <select wire:model.live="recMealDestination" class="w-full h-11 bg-white dark:bg-zinc-800 border border-amber-100 dark:border-amber-900/30 rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                                <option value="same">Mesmo destino do salário</option>
                                <option value="meal_card">Cartão de refeição separado</option>
                                <option value="cash">Dinheiro físico</option>
                                @foreach($this->bankAccounts->where('user_id', auth()->id()) as $acc)
                                    <option value="bank:{{ $acc->id }}">🏦 Para a conta - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[9px] text-zinc-500 mt-2">Escolhe a conta bancária ou indica se o subsídio é carregado num cartão à parte.</p>
                        </div>
                        <p class="text-[10px] font-bold text-amber-700 dark:text-amber-300 mt-3">
                            Estimativa anual ({{ number_format($recMealAllowance * $recWorkingDays, 2, ',', ' ') }} € × 11): {{ number_format($recMealAllowance * $recWorkingDays * 11, 2, ',', ' ') }} €
                        </p>
                    </div>

                    {{-- QUADRO DE AUDITORIA (IDENTICO AO ONBOARDING) --}}
                    @if($recSalaryGross > 0)
                    <div class="p-4 bg-zinc-950 text-white rounded-[2rem] border border-white/10 shadow-2xl space-y-3 animate-in fade-in zoom-in-95">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 italic">Cálculo de Salário</h3>
                            <span class="text-[9px] font-black bg-emerald-500/10 px-2 py-0.5 rounded text-emerald-400 uppercase">Estimativa 2026</span>
                        </div>
                        <div class="space-y-2 font-mono text-[11px]">
                            <div class="flex justify-between text-zinc-400">
                                <span>SEGURANÇA SOCIAL (11%)</span>
                                <span class="font-bold text-red-400">- {{ number_format($calculatedSS, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>{{ $recIrsJovem ? 'IRS JOVEM (RETENÇÃO ESTIMADA)' : 'IRS NORMAL (RETENÇÃO ESTIMADA)' }}</span>
                                <span class="font-bold text-red-400">- {{ number_format($calculatedIRS, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>SUBSÍDIO DE ALIMENTAÇÃO</span>
                                <span class="font-bold text-emerald-400">+ {{ number_format($calculatedSA, 2, ',', ' ') }} €</span>
                            </div>
                            @if($recAllowancesInDuodecimos)
                                <div class="flex justify-between text-zinc-400">
                                    <span>DUODÉCIMOS (BRUTO)</span>
                                    <span class="font-bold text-emerald-400">+ {{ number_format($monthlyDuodecimos, 2, ',', ' ') }} €</span>
                                </div>
                            @endif
                        </div>                    </div>
                    @endif

                    {{-- DIA DE PAGAMENTO + DESTINO --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-indigo-600 z-10">Onde entra este valor?</label>
                            <select wire:model.live="recSalaryDestination" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-indigo-500 transition-all">
                                <option value="cash">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts->where('user_id', auth()->id()) as $acc)
                                    <option value="bank:{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <label class="flex items-start gap-3 p-4 bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30 rounded-2xl cursor-pointer">
                        <input type="checkbox" wire:model="recSalaryReceivedThisMonth" class="mt-0.5 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                        <span>
                            <span class="block text-xs font-black text-indigo-700 dark:text-indigo-300">Já recebi este salário este mês</span>
                            <span class="block text-[10px] text-zinc-500 mt-1">Esta indicação fica associada ao mês atual.</span>
                        </span>
                    </label>
                    <div>
                        <label class="text-[9px] font-black uppercase text-zinc-500">Notas e observações do contrato</label>
                        <textarea wire:model="recNotes" rows="3" maxlength="5000" placeholder="Alterações contratuais, aumentos, funções ou notas pessoais" class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></textarea>
                        @error('recNotes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="shrink-0 border-t border-zinc-100 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mb-2 flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-2 dark:border-emerald-900/50 dark:bg-emerald-950/30">
                        <span class="text-xs font-black uppercase tracking-wide text-emerald-800 dark:text-emerald-300">Líquido final mensal</span>
                        <span class="text-2xl font-black tabular-nums text-emerald-700 dark:text-emerald-300">{{ number_format((float) $recAmount, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-12 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-indigo-500/20 transition-all">
                        Confirmar Contrato 💼
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if($showSalaryBonusModal)
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-init="setTimeout(() => show(), 50)"
    x-on:modal-show-salary-bonus.window="show()"
    x-on:modal-close-salary-bonus.window="close()"
    x-on:keydown.escape.window="close(); $wire.closeSalaryBonusModal()"
>
    <div x-show="open" x-cloak x-transition.opacity.duration.150ms @click="close(); $wire.closeSalaryBonusModal()" class="fixed inset-0 z-50 bg-zinc-950/60 backdrop-blur-sm"></div>
    <div x-show="open" x-cloak @click.self="close(); $wire.closeSalaryBonusModal()" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-3 sm:p-6">
        <div x-show="open" x-transition.scale.duration.150ms class="relative z-10 my-auto flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl shadow-zinc-950/20 dark:border-zinc-800 dark:bg-zinc-900" @click.stop>
            <form wire:submit.prevent="saveSalaryBonus" class="flex min-h-0 flex-col" autocomplete="off">
                <header class="flex items-start gap-4 border-b border-zinc-100 px-5 py-5 dark:border-zinc-800 sm:px-7">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-2xl dark:bg-amber-500/10">
                        🎁
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">Prémio do contrato</p>
                        <h2 class="mt-1 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $recDescription }}</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Configura o prémio sem alterar os restantes dados do salário.</p>
                    </div>
                    <button type="button" wire:click="closeSalaryBonusModal" @click="close()" aria-label="Fechar" class="rounded-xl p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </header>

                <div class="min-h-0 space-y-6 overflow-y-auto px-5 py-5 text-left sm:px-7 sm:py-6">
                    <section class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Valor do prémio</h3>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Indica um único valor bruto em euros. Com 0 €, o prémio atual é removido.</p>
                        </div>
                        <div>
                            <label for="salary-bonus-gross" class="mb-2 block text-sm font-semibold text-zinc-900 dark:text-white">Valor bruto</label>
                            <div class="relative">
                                <input id="salary-bonus-gross" type="number" min="0" step="0.01" inputmode="decimal" wire:model.live.debounce.300ms="recBonusGross" placeholder="0,00" class="h-14 w-full rounded-xl border border-zinc-300 bg-white px-4 pr-12 text-lg font-semibold text-zinc-900 outline-none transition placeholder:font-normal placeholder:text-zinc-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white">
                                <span class="absolute inset-y-0 right-3 flex items-center"><span class="rounded-lg bg-amber-100 px-3 py-1 text-lg font-black text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">€</span></span>
                            </div>
                            @error('recBonusGross')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </section>

                    <section class="space-y-3">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Descontos configuráveis</h3>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                <label class="flex items-center gap-3 text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                    <input type="checkbox" wire:model.live="recBonusDeductIrs" class="size-4 rounded border-zinc-300 text-amber-600 focus:ring-amber-500">
                                    Descontar IRS
                                </label>
                                <label for="salary-bonus-irs-rate" class="mb-2 mt-4 block text-xs font-medium text-zinc-500">Percentagem de IRS</label>
                                <div class="relative">
                                    <input id="salary-bonus-irs-rate" type="number" min="0" max="100" step="0.01" wire:model.live.debounce.300ms="recBonusIrsPercentage" class="h-11 w-full rounded-xl border border-zinc-300 bg-white px-3 pr-10 text-sm font-semibold text-zinc-900 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white">
                                    <span class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-zinc-400">%</span>
                                </div>
                                @error('recBonusIrsPercentage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                <p class="mt-2 text-xs text-zinc-500">Desconto calculado: {{ number_format($recBonusIrsAmount, 2, ',', ' ') }} €</p>
                            </div>
                            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                <label class="flex items-center gap-3 text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                    <input type="checkbox" wire:model.live="recBonusDeductSocialSecurity" class="size-4 rounded border-zinc-300 text-amber-600 focus:ring-amber-500">
                                    Descontar Segurança Social
                                </label>
                                <label for="salary-bonus-ss-rate" class="mb-2 mt-4 block text-xs font-medium text-zinc-500">Percentagem de Segurança Social</label>
                                <div class="relative">
                                    <input id="salary-bonus-ss-rate" type="number" min="0" max="100" step="0.01" wire:model.live.debounce.300ms="recBonusSocialSecurityPercentage" class="h-11 w-full rounded-xl border border-zinc-300 bg-white px-3 pr-10 text-sm font-semibold text-zinc-900 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white">
                                    <span class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-zinc-400">%</span>
                                </div>
                                @error('recBonusSocialSecurityPercentage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                <p class="mt-2 text-xs text-zinc-500">Desconto calculado: {{ number_format($recBonusSocialSecurityAmount, 2, ',', ' ') }} €</p>
                            </div>
                        </div>
                        <p class="text-xs leading-5 text-zinc-500 dark:text-zinc-400">As taxas são editáveis e aplicadas diretamente ao valor bruto do prémio. Esta é uma estimativa configurável, não substitui o cálculo oficial do recibo de vencimento.</p>
                    </section>

                    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="salary-bonus-date" class="mb-2 block text-sm font-semibold text-zinc-900 dark:text-white">Data de recebimento</label>
                            <input id="salary-bonus-date" type="date" wire:model="recBonusDate" class="h-12 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white">
                            @error('recBonusDate')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex flex-col justify-center rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-900/40 dark:bg-emerald-900/20">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Valor líquido estimado</p>
                            <p class="mt-1 text-2xl font-black tabular-nums text-emerald-700 dark:text-emerald-300">{{ number_format((float) $recBonusNet, 2, ',', ' ') }} €</p>
                            <p class="text-xs text-emerald-700/80 dark:text-emerald-300/80">Bruto menos IRS e Segurança Social selecionados</p>
                        </div>
                    </section>
                </div>

                <footer class="flex flex-col-reverse gap-2 border-t border-zinc-100 bg-zinc-50 px-5 py-4 dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row sm:justify-end sm:px-7">
                    <button type="button" wire:click="closeSalaryBonusModal" @click="close()" class="inline-flex h-11 items-center justify-center rounded-xl px-5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-200/70 dark:text-zinc-300 dark:hover:bg-zinc-800">Cancelar</button>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-amber-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500/20">Guardar prémio</button>
                </footer>
            </form>
        </div>
    </div>
</div>
@endif

@if($salaryIncomeHistoryContract)
<div
    x-data="{ open: true, close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); } }"
    x-init="document.documentElement.classList.add('overflow-hidden')"
    x-on:keydown.escape.window="close(); $wire.closeSalaryIncomeHistory()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close(); $wire.closeSalaryIncomeHistory()" class="fixed inset-0 z-[70] bg-zinc-950/70 backdrop-blur-sm"></div>
    <div x-show="open" x-cloak @click.self="close(); $wire.closeSalaryIncomeHistory()" class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto p-3 sm:p-6">
        <section class="relative z-10 my-auto max-h-[90vh] w-full max-w-2xl overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900" @click.stop>
            <header class="flex items-start justify-between gap-4 border-b border-zinc-100 px-5 py-5 dark:border-zinc-800 sm:px-7">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $salaryIncomeHistoryType === 'prize' ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-700 dark:text-emerald-400' }}">Histórico de {{ $salaryIncomeHistoryType === 'prize' ? 'prémios' : 'serviços extra' }}</p>
                    <h2 class="mt-1 text-xl font-bold text-zinc-900 dark:text-white">{{ $salaryIncomeHistoryContract->description }}</h2>
                    <p class="mt-2 text-xs font-semibold {{ $salaryIncomeHistoryType === 'prize' ? 'text-amber-700 dark:text-amber-300' : 'text-emerald-700 dark:text-emerald-300' }}">
                        Total: {{ number_format($salaryIncomeHistory->sum(fn ($extra) => (float) ($extra->amount_converted ?? $extra->amount)), 2, ',', ' ') }} {{ $workspaceCurrency }}
                        · {{ $salaryIncomeHistory->count() }} registo{{ $salaryIncomeHistory->count() === 1 ? '' : 's' }}
                    </p>
                </div>
                <button type="button" wire:click="closeSalaryIncomeHistory" @click="close()" class="rounded-xl p-2 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-label="Fechar histórico">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </header>
            <div class="max-h-[65vh] overflow-y-auto">
                @forelse($salaryIncomeHistory as $extra)
                    <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-5 py-4 last:border-b-0 dark:border-zinc-800 sm:px-7">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $extra->description }}</p>
                            <p class="mt-1 text-xs text-zinc-500">
                                {{ $extra->salary_income_type === 'prize' ? 'Prémio' : 'Serviço extra' }}
                                @if($extra->salary_income_type !== 'prize' && $extra->service_quantity)
                                    · {{ $extra->service_quantity }} × {{ number_format((float) $extra->service_unit_amount, 2, ',', ' ') }} {{ $extra->currency }}
                                @endif
                                · {{ $extra->received_at?->translatedFormat('d M Y') }}
                                @if($extra->salary_income_type !== 'prize' && $extra->added_to_salary)
                                    · será pago no próximo salário
                                @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <p class="text-sm font-bold tabular-nums text-emerald-600">+{{ number_format((float) ($extra->amount_converted ?? $extra->amount), 2, ',', ' ') }} {{ $workspaceCurrency }}</p>
                            @if($extra->salary_income_type !== 'prize')
                                <button type="button" wire:click="editSalaryExtraIncome({{ $extra->id }})" class="rounded-lg p-2 text-indigo-500 transition hover:bg-indigo-50 hover:text-indigo-700 dark:hover:bg-indigo-950/30" aria-label="Editar serviço extra">                                    <flux:icon name="pencil-square" class="size-4" />
                                </button>
                            @endif
                            @if($isOwner)
                                <button type="button" wire:click="deleteExtra({{ $extra->id }})" wire:confirm="Apagar esta entrada extra?" class="rounded-lg p-2 text-zinc-400 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30" aria-label="Apagar entrada extra">
                                    <flux:icon name="trash" class="size-4" />
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">Ainda não existem registos de {{ $salaryIncomeHistoryType === 'prize' ? 'prémios' : 'serviços extra' }} neste contrato.</p>
                        <p class="mt-1 text-xs text-zinc-500">Os registos desta categoria aparecerão aqui quando forem associados ao contrato.</p>
                    </div>
                @endforelse
            </div>
            <footer class="flex justify-end border-t border-zinc-100 px-5 py-4 dark:border-zinc-800 sm:px-7">
                <button type="button" wire:click="closeSalaryIncomeHistory" @click="close()" class="rounded-xl bg-zinc-100 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">Fechar</button>
            </footer>
        </section>
    </div>
</div>
@endif



{{-- ============================================================= --}}
{{-- RENDIMENTOS FIXOS COM FOCO EM DETALHE IMOBILIÁRIO             --}}
{{-- ============================================================= --}}
<div class="space-y-6 mt-10"> {{-- Adicionei um margin-top para separar --}}

    @php
        // FILTRO: Criamos uma lista isolada apenas para fonte imobiliária
        $rentalIncomes = $fixedIncomes->where('source', 'imobiliario');
    @endphp

    {{-- Título + BOTÃO DE AÇÃO --}}
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-500/10 rounded-lg text-blue-600">
                <flux:icon name="home-modern" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Rendas e exploração Imobiliária
            </h2>
        </div>

        {{-- BOTÃO QUE ABRE O MODAL --}}
        <div class="flex items-center gap-3">
            <flux:badge variant="neutral" class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase px-2 py-0.5 border-none">
                {{ $rentalIncomes->count() }} Ativos
            </flux:badge>

            <button
                @click="$dispatch('modal-show-renda')"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-blue-500/20"
            >
                <flux:icon name="plus" variant="micro" class="size-3" />
                Nova Renda
            </button>
        </div>
    </div>

    {{-- Lista de Cards (Filtrada) --}}
    <div class="space-y-4">
        @forelse($rentalIncomes as $fixed)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-0 group transition-all hover:border-blue-500/40 shadow-sm relative overflow-hidden">

                {{-- Linha de cor AZUL exclusiva para Imobiliário --}}
                <div class="h-1.5 w-full bg-blue-500"></div>

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">

                        {{-- Esquerda: Identificação --}}
                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-blue-50 dark:bg-zinc-950 border border-blue-100 dark:border-blue-900/50 shadow-inner shrink-0">
                                <span class="text-[7px] font-black text-blue-400 uppercase leading-none mb-1 text-center">RECOLHA</span>
                                <span class="text-xl font-black text-blue-600">
                                    {{ sprintf('%02d', $fixed->day_of_month) }}
                                </span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                    {{ $fixed->description }}
                                    <span class="text-[9px] bg-blue-500/10 text-blue-500 px-2 py-0.5 rounded-lg border border-blue-500/20 font-black tracking-widest">RENDAS</span>
                                </p>

                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                                    Registado em: <span class="text-zinc-600 dark:text-zinc-300">{{ $fixed->created_at->translatedFormat('d M, Y') }}</span>
                                    @if($fixed->notes) · <span class="italic opacity-70">"{{ $fixed->notes }}"</span> @endif
                                </p>
                                <div class="mt-1">@include('livewire.partials.bank-source-badge', ['record' => $fixed])</div>
                            </div>
                        </div>

                        {{-- Direita: Valor Líquido --}}
                        <div class="text-right shrink-0">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 italic">Rendimento Líquido</p>
                            <span class="text-3xl font-black text-blue-600 tracking-tighter italic">
                                {{ number_format($fixed->amount, 2, ',', ' ') }}€
                            </span>
                        </div>
                    </div>

                    {{-- ── ÁREA DE DETALHES ESPECÍFICOS (SE FOR IMOBILIÁRIO) ── --}}
                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-3 pt-6 border-t border-zinc-100 dark:border-zinc-800">

                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Renda Bruta</p>
                            <p class="text-xs font-black dark:text-white">
                                {{ number_format($fixed->metadata['rental_gross'] ?? $fixed->amount, 2, ',', ' ') }}€
                            </p>
                        </div>

                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-red-400 uppercase mb-1">Custos/Taxas</p>
                            <p class="text-xs font-black text-red-500">
                                -{{ number_format($fixed->metadata['rental_expenses'] ?? 0, 2, ',', ' ') }}€
                            </p>
                        </div>

                        <div class="p-3 bg-blue-500/5 rounded-xl border border-blue-500/10 col-span-2 sm:col-span-1">
                            <p class="text-[8px] font-black text-blue-500 uppercase mb-1 text-center">Gestão de Ativo</p>
                            <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 text-center">
                                @if(($fixed->metadata['property_type'] ?? '') === 'alojamento_local')
                                    🏨 Alojamento Local
                                @else
                                    🏠 Arrendamento Longo
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- AÇÕES NO HOVER --}}
                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-zinc-50 dark:border-zinc-800/50 pt-4">
                        <button wire:click="openRaiseModal({{ $fixed->id }})"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-500/10 text-blue-600 hover:bg-blue-600 hover:text-white transition-all font-black text-[9px] uppercase shadow-sm">
                            <flux:icon name="rocket-launch" variant="micro" class="size-3" />
                            Aumento Renda
                        </button>

                        <flux:button wire:click="editFixed({{ $fixed->id }})" variant="ghost" icon="pencil-square" size="xs" />
                        <flux:button wire:click="deleteFixed({{ $fixed->id }})" wire:confirm="Desejas mesmo eliminar este registo imobiliário?" variant="ghost" icon="trash" size="xs" color="red" />
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <flux:icon name="home-modern" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest italic">Nenhum ativo imobiliário ou renda registada</p>
            </div>
        @endforelse
    </div>
</div>


{{-- ============================================================= --}}
{{-- 💻 TRABALHO INDEPENDENTE / RECIBOS VERDES                     --}}
{{-- ============================================================= --}}
<div class="space-y-6 mt-10">
    @php
        $freelanceIncomes = $fixedIncomes->where('source', 'freelance');
    @endphp

    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-sky-500/10 rounded-lg text-sky-600">
                <flux:icon name="computer-desktop" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Trabalho Independente / Recibos Verdes
            </h2>
        </div>

        <div class="flex items-center gap-3">
            <flux:badge variant="neutral" class="bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 text-[10px] font-black uppercase px-2 py-0.5 border-none">
                {{ $freelanceIncomes->count() }} Ativos
            </flux:badge>

            <button
                wire:click="openFreelanceModal"
                class="flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-sky-500/20"
            >
                <flux:icon name="plus" variant="micro" class="size-3" />
                Novo Freelance
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($freelanceIncomes as $fixed)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-0 group transition-all hover:border-sky-500/40 shadow-sm relative overflow-hidden">
                <div class="h-1.5 w-full bg-sky-500"></div>

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex items-start gap-4 flex-1">                            <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-sky-50 dark:bg-zinc-950 border border-sky-100 dark:border-sky-900/50 shadow-inner shrink-0">
                                <span class="text-[7px] font-black text-sky-400 uppercase leading-none mb-1 text-center">RECEB.</span>
                                <span class="text-xl font-black text-sky-600">{{ sprintf('%02d', $fixed->day_of_month) }}</span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                    {{ $fixed->description }}
                                    <span class="text-[9px] bg-sky-500/10 text-sky-500 px-2 py-0.5 rounded-lg border border-sky-500/20 font-black tracking-widest">RECIBOS VERDES</span>
                                </p>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                                    {{ $fixed->metadata['freelance_activity'] ?? 'Atividade independente' }}
                                </p>
                                <div class="mt-1">@include('livewire.partials.bank-source-badge', ['record' => $fixed])</div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 italic">Rendimento Líquido</p>
                            <span class="text-3xl font-black text-sky-600 tracking-tighter italic">
                                {{ number_format($fixed->amount, 2, ',', ' ') }}€
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-3 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Faturação Média</p>
                            <p class="text-xs font-black dark:text-white">{{ number_format($fixed->metadata['freelance_gross'] ?? $fixed->amount, 2, ',', ' ') }}€</p>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-red-400 uppercase mb-1">Despesas</p>
                            <p class="text-xs font-black text-red-500">-{{ number_format($fixed->metadata['freelance_expenses'] ?? 0, 2, ',', ' ') }}€</p>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-red-400 uppercase mb-1">Retenção na Fonte</p>
                            <p class="text-xs font-black text-red-500">-{{ number_format($fixed->metadata['freelance_withholding'] ?? 0, 2, ',', ' ') }}€</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-zinc-50 dark:border-zinc-800/50 pt-4">
                        <button wire:click="openRaiseModal({{ $fixed->id }})"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-sky-500/10 text-sky-600 hover:bg-sky-600 hover:text-white transition-all font-black text-[9px] uppercase shadow-sm">
                            <flux:icon name="rocket-launch" variant="micro" class="size-3" />
                            Aumento
                        </button>
                        <flux:button wire:click="editFixed({{ $fixed->id }})" variant="ghost" icon="pencil-square" size="xs" />
                        <flux:button wire:click="deleteFixed({{ $fixed->id }})" wire:confirm="Apagar?" variant="ghost" icon="trash" size="xs" color="red" />
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <flux:icon name="computer-desktop" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest italic">Nenhum rendimento de freelance registado</p>
            </div>
        @endforelse
    </div>
</div>


{{-- ============================================================= --}}
{{-- 📈 RENDIMENTOS DE INVESTIMENTOS / DIVIDENDOS                  --}}
{{-- ============================================================= --}}
<div class="space-y-6 mt-10">
    @php
        $investmentIncomes = $fixedIncomes->where('source', 'investimento');
    @endphp

    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-violet-500/10 rounded-lg text-violet-600">
                <flux:icon name="chart-bar" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Rendimentos de Investimentos / Dividendos
            </h2>
        </div>

        <div class="flex items-center gap-3">
            <flux:badge variant="neutral" class="bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 text-[10px] font-black uppercase px-2 py-0.5 border-none">
                {{ $investmentIncomes->count() }} Ativos
            </flux:badge>

            <button
                wire:click="openInvestmentModal"
                class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-violet-500/20"
            >
                <flux:icon name="plus" variant="micro" class="size-3" />
                Novo Investimento
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($investmentIncomes as $fixed)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-0 group transition-all hover:border-violet-500/40 shadow-sm relative overflow-hidden">
                <div class="h-1.5 w-full bg-violet-500"></div>

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-violet-50 dark:bg-zinc-950 border border-violet-100 dark:border-violet-900/50 shadow-inner shrink-0">
                                <span class="text-[7px] font-black text-violet-400 uppercase leading-none mb-1 text-center">RECEB.</span>
                                <span class="text-xl font-black text-violet-600">{{ sprintf('%02d', $fixed->day_of_month) }}</span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                    {{ $fixed->description }}
                                    <span class="text-[9px] bg-violet-500/10 text-violet-500 px-2 py-0.5 rounded-lg border border-violet-500/20 font-black tracking-widest">INVESTIMENTO</span>
                                </p>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                                    {{ ucfirst($fixed->metadata['investment_type'] ?? 'dividendos') }}
                                    @if($fixed->metadata['investment_name'] ?? null) · <span class="italic text-zinc-400">"{{ $fixed->metadata['investment_name'] }}"</span> @endif
                                </p>
                                <div class="mt-1">@include('livewire.partials.bank-source-badge', ['record' => $fixed])</div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 italic">Rendimento Líquido</p>
                            <span class="text-3xl font-black text-violet-600 tracking-tighter italic">
                                {{ number_format($fixed->amount, 2, ',', ' ') }}€
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Rendimento Bruto</p>
                            <p class="text-xs font-black dark:text-white">{{ number_format($fixed->metadata['investment_amount'] ?? $fixed->amount, 2, ',', ' ') }}€</p>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-red-400 uppercase mb-1">Custos/Comissões</p>
                            <p class="text-xs font-black text-red-500">-{{ number_format($fixed->metadata['investment_expenses'] ?? 0, 2, ',', ' ') }}€</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-zinc-50 dark:border-zinc-800/50 pt-4">
                        <button wire:click="openRaiseModal({{ $fixed->id }})"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-violet-500/10 text-violet-600 hover:bg-violet-600 hover:text-white transition-all font-black text-[9px] uppercase shadow-sm">
                            <flux:icon name="rocket-launch" variant="micro" class="size-3" />
                            Aumento
                        </button>
                        <flux:button wire:click="editFixed({{ $fixed->id }})" variant="ghost" icon="pencil-square" size="xs" />
                        <flux:button wire:click="deleteFixed({{ $fixed->id }})" wire:confirm="Apagar?" variant="ghost" icon="trash" size="xs" color="red" />
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <flux:icon name="chart-bar" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest italic">Nenhum rendimento de investimento registado</p>
            </div>
        @endforelse
    </div>
</div>


{{-- ============================================================= --}}
{{-- 👴 REFORMA / PENSÃO DE VELHICE                                --}}
{{-- ============================================================= --}}
<div class="space-y-6 mt-10">
    @php
        $pensionIncomes = $fixedIncomes->where('source', 'reforma');
    @endphp

    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-rose-500/10 rounded-lg text-rose-600">
                <flux:icon name="user-circle" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Reforma / Pensão de Velhice
            </h2>
        </div>

        <div class="flex items-center gap-3">
            <flux:badge variant="neutral" class="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase px-2 py-0.5 border-none">
                {{ $pensionIncomes->count() }} Ativos
            </flux:badge>

            <button
                wire:click="openPensionModal"
                class="flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-rose-500/20"
            >
                <flux:icon name="plus" variant="micro" class="size-3" />
                Nova Pensão
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($pensionIncomes as $fixed)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-0 group transition-all hover:border-rose-500/40 shadow-sm relative overflow-hidden">
                <div class="h-1.5 w-full bg-rose-500"></div>

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-rose-50 dark:bg-zinc-950 border border-rose-100 dark:border-rose-900/50 shadow-inner shrink-0">
                                <span class="text-[7px] font-black text-rose-400 uppercase leading-none mb-1 text-center">RECEB.</span>
                                <span class="text-xl font-black text-rose-600">{{ sprintf('%02d', $fixed->day_of_month) }}</span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                    {{ $fixed->description }}
                                    <span class="text-[9px] bg-rose-500/10 text-rose-500 px-2 py-0.5 rounded-lg border border-rose-500/20 font-black tracking-widest">PENSÃO</span>
                                </p>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                                    {{ ucfirst($fixed->metadata['pension_type'] ?? 'velhice') }}
                                    @if($fixed->metadata['pension_entity'] ?? null) · <span class="italic text-zinc-400">{{ $fixed->metadata['pension_entity'] }}</span> @endif
                                </p>
                                <div class="mt-1">@include('livewire.partials.bank-source-badge', ['record' => $fixed])</div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 italic">Valor Líquido</p>
                            <span class="text-3xl font-black text-rose-600 tracking-tighter italic">
                                {{ number_format($fixed->amount, 2, ',', ' ') }}€
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Valor Bruto</p>
                            <p class="text-xs font-black dark:text-white">{{ number_format($fixed->metadata['pension_gross'] ?? $fixed->amount, 2, ',', ' ') }}€</p>
                        </div>
                        <div class="p-3 bg-rose-500/5 rounded-xl border border-rose-500/10">
                            <p class="text-[8px] font-black text-rose-500 uppercase mb-1">Entidade Pagadora</p>
                            <p class="text-[10px] font-bold text-rose-600 dark:text-rose-400">{{ $fixed->metadata['pension_entity'] ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-zinc-50 dark:border-zinc-800/50 pt-4">
                        <button wire:click="openRaiseModal({{ $fixed->id }})"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-500/10 text-rose-600 hover:bg-rose-600 hover:text-white transition-all font-black text-[9px] uppercase shadow-sm">
                            <flux:icon name="rocket-launch" variant="micro" class="size-3" />
                            Aumento
                        </button>
                        <flux:button wire:click="editFixed({{ $fixed->id }})" variant="ghost" icon="pencil-square" size="xs" />
                        <flux:button wire:click="deleteFixed({{ $fixed->id }})" wire:confirm="Apagar?" variant="ghost" icon="trash" size="xs" color="red" />
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <flux:icon name="user-circle" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest italic">Nenhuma pensão registada</p>
            </div>
        @endforelse
    </div>
</div>


{{-- ============================================================= --}}
{{-- 🎓 BOLSA DE ESTUDO / APOIO À FORMAÇÃO                         --}}
{{-- ============================================================= --}}
<div class="space-y-6 mt-10">
    @php
        $scholarshipIncomes = $fixedIncomes->where('source', 'bolsa');
    @endphp

    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-amber-500/10 rounded-lg text-amber-600">
                <flux:icon name="academic-cap" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Bolsa de Estudo / Apoio à Formação
            </h2>
        </div>

        <div class="flex items-center gap-3">
            <flux:badge variant="neutral" class="bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase px-2 py-0.5 border-none">
                {{ $scholarshipIncomes->count() }} Ativos
            </flux:badge>

            <button
                wire:click="openScholarshipModal"
                class="flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-amber-500/20"
            >
                <flux:icon name="plus" variant="micro" class="size-3" />
                Nova Bolsa
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($scholarshipIncomes as $fixed)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-0 group transition-all hover:border-amber-500/40 shadow-sm relative overflow-hidden">
                <div class="h-1.5 w-full bg-amber-500"></div>

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-amber-50 dark:bg-zinc-950 border border-amber-100 dark:border-amber-900/50 shadow-inner shrink-0">
                                <span class="text-[7px] font-black text-amber-400 uppercase leading-none mb-1 text-center">RECEB.</span>
                                <span class="text-xl font-black text-amber-600">{{ sprintf('%02d', $fixed->day_of_month) }}</span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                    {{ $fixed->description }}
                                    <span class="text-[9px] bg-amber-500/10 text-amber-500 px-2 py-0.5 rounded-lg border border-amber-500/20 font-black tracking-widest">BOLSA</span>
                                </p>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                                    {{ ucfirst($fixed->metadata['scholarship_type'] ?? 'estudo') }}
                                    @if($fixed->metadata['scholarship_entity'] ?? null) · <span class="italic text-zinc-400">{{ $fixed->metadata['scholarship_entity'] }}</span> @endif
                                    @if($fixed->metadata['scholarship_end_date'] ?? null) · <span class="text-zinc-400">até {{ \Carbon\Carbon::parse($fixed->metadata['scholarship_end_date'])->format('d/m/Y') }}</span> @endif
                                </p>
                                <div class="mt-1">@include('livewire.partials.bank-source-badge', ['record' => $fixed])</div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 italic">Valor</p>
                            <span class="text-3xl font-black text-amber-600 tracking-tighter italic">
                                {{ number_format($fixed->amount, 2, ',', ' ') }}€
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-zinc-50 dark:border-zinc-800/50 pt-4">
                        <button wire:click="openRaiseModal({{ $fixed->id }})"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-500/10 text-amber-600 hover:bg-amber-600 hover:text-white transition-all font-black text-[9px] uppercase shadow-sm">
                            <flux:icon name="rocket-launch" variant="micro" class="size-3" />
                            Aumento
                        </button>
                        <flux:button wire:click="editFixed({{ $fixed->id }})" variant="ghost" icon="pencil-square" size="xs" />
                        <flux:button wire:click="deleteFixed({{ $fixed->id }})" wire:confirm="Apagar?" variant="ghost" icon="trash" size="xs" color="red" />
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <flux:icon name="academic-cap" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest italic">Nenhuma bolsa registada</p>
            </div>
        @endforelse
    </div>
</div>


{{-- ============================================================= --}}
{{-- ✨ OUTRA FONTE DE RENDIMENTO                                  --}}
{{-- ============================================================= --}}
<div class="space-y-6 mt-10">
    @php
        $otherIncomes = $fixedIncomes->where('source', 'outro');
    @endphp

    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-zinc-200/60 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="sparkles" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Outra Fonte de Rendimento
            </h2>
        </div>

        <div class="flex items-center gap-3">
            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[10px] font-black uppercase px-2 py-0.5 border-none">
                {{ $otherIncomes->count() }} Ativos
            </flux:badge>

            <button
                wire:click="openOtherModal"
                class="flex items-center gap-2 px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg"
            >
                <flux:icon name="plus" variant="micro" class="size-3" />
                Nova Fonte
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($otherIncomes as $fixed)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-0 group transition-all hover:border-zinc-400/40 shadow-sm relative overflow-hidden">
                <div class="h-1.5 w-full bg-zinc-500"></div>

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 shadow-inner shrink-0">
                                <span class="text-[7px] font-black text-zinc-400 uppercase leading-none mb-1 text-center">RECEB.</span>
                                <span class="text-xl font-black text-zinc-700 dark:text-white">{{ sprintf('%02d', $fixed->day_of_month) }}</span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                    {{ $fixed->description }}
                                    <span class="text-[9px] bg-zinc-500/10 text-zinc-500 px-2 py-0.5 rounded-lg border border-zinc-500/20 font-black tracking-widest">OUTRO</span>
                                </p>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">                                    Frequência: {{ ucfirst($fixed->frequency ?? 'mensal') }}
                                </p>
                                <div class="mt-1">@include('livewire.partials.bank-source-badge', ['record' => $fixed])</div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 italic">Valor</p>
                            <span class="text-3xl font-black text-zinc-700 dark:text-white tracking-tighter italic">
                                {{ number_format($fixed->amount, 2, ',', ' ') }}€
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-zinc-50 dark:border-zinc-800/50 pt-4">
                        <button wire:click="openRaiseModal({{ $fixed->id }})"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-500/10 text-zinc-600 hover:bg-zinc-600 hover:text-white transition-all font-black text-[9px] uppercase shadow-sm">
                            <flux:icon name="rocket-launch" variant="micro" class="size-3" />
                            Aumento
                        </button>
                        <flux:button wire:click="editFixed({{ $fixed->id }})" variant="ghost" icon="pencil-square" size="xs" />
                        <flux:button wire:click="deleteFixed({{ $fixed->id }})" wire:confirm="Apagar?" variant="ghost" icon="trash" size="xs" color="red" />
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <flux:icon name="sparkles" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest italic">Nenhuma outra fonte registada</p>
            </div>
        @endforelse
    </div>
    </div>
    <div x-show="activeIncomeTab === 'bank'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Total recebido em contas</p>
                <p class="mt-2 text-2xl font-black text-emerald-600">{{ number_format($bankTransactionsTotal, 2, ',', ' ') }} €</p>
                <p class="mt-1 text-[10px] text-zinc-500">Soma dos movimentos bancários registados</p>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Entradas e transferências</p>
                <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-white">{{ $bankReceivedIncomes->count() }}</p>
                <p class="mt-1 text-[10px] text-zinc-500">Movimentos associados a uma conta</p>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Salários recebidos</p>
                <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-white">{{ $bankReceivedSalaryPayments->count() }}</p>
                <p class="mt-1 text-[10px] text-zinc-500">Pagamentos salariais confirmados</p>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-zinc-900 dark:text-white">Movimentos por conta bancária</h3>
                    <p class="mt-1 text-xs text-zinc-500">Transferências, MB WAY e outras entradas associadas às tuas contas.</p>
                </div>
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-[9px] font-black uppercase text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">Registos reais</span>
            </div>

            @if($bankReceivedIncomes->isEmpty() && $bankReceivedSalaryPayments->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-zinc-200 p-10 text-center dark:border-zinc-700">
                    <flux:icon name="building-library" class="mx-auto size-8 text-zinc-300 dark:text-zinc-600" />
                    <p class="mt-3 text-sm font-bold text-zinc-700 dark:text-zinc-200">Ainda não existem movimentos bancários registados.</p>
                    <p class="mt-1 text-xs text-zinc-500">Ao registares uma entrada ou salário, seleciona a conta bancária ou a conta MB WAY como destino.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left">
                        <thead><tr class="border-b border-zinc-100 text-[9px] font-black uppercase tracking-wider text-zinc-400 dark:border-zinc-800"><th class="px-3 py-3">Data</th><th class="px-3 py-3">Descrição</th><th class="px-3 py-3">Conta / método</th><th class="px-3 py-3 text-right">Valor recebido</th></tr></thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($bankReceivedIncomes as $movement)
                                <tr>
                                    <td class="px-3 py-3 text-xs text-zinc-500">{{ $movement->received_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-3 py-3"><p class="text-xs font-bold text-zinc-900 dark:text-white">{{ $movement->description }}</p><p class="mt-0.5 text-[9px] uppercase text-zinc-400">Entrada</p></td>
                                    <td class="px-3 py-3 text-xs font-semibold text-zinc-600 dark:text-zinc-300">{{ $movement->bankAccount?->name ?? 'Conta não identificada' }}</td>
                                    <td class="px-3 py-3 text-right text-sm font-black text-emerald-600">+{{ number_format((float) ($movement->amount_converted ?? $movement->amount), 2, ',', ' ') }} €</td>
                                </tr>
                            @endforeach
                            @foreach($bankReceivedSalaryPayments as $movement)
                                @php
                                    $movementMealCard = ($movement->recurringIncome?->metadata['meal_allowance_destination'] ?? null) === 'meal_card';
                                    $movementAmount = (float) $movement->bank_balance_amount > 0
                                        ? (float) $movement->bank_balance_amount
                                        : max(0, (float) $movement->net_received - ($movementMealCard ? (float) $movement->meal_allowance : 0));
                                @endphp
                                <tr>
                                    <td class="px-3 py-3 text-xs text-zinc-500">{{ $movement->paid_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-3 py-3"><p class="text-xs font-bold text-zinc-900 dark:text-white">{{ $movement->recurringIncome?->description ?? 'Salário' }}</p><p class="mt-0.5 text-[9px] uppercase text-zinc-400">Salário · {{ \Carbon\Carbon::create($movement->payment_year, $movement->payment_month)->translatedFormat('F Y') }}</p></td>
                                    <td class="px-3 py-3 text-xs font-semibold text-zinc-600 dark:text-zinc-300">{{ $movement->bankAccount?->name ?? 'Conta não identificada' }}</td>
                                    <td class="px-3 py-3 text-right text-sm font-black text-emerald-600">+{{ number_format($movementAmount, 2, ',', ' ') }} €</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


</div>


{{-- BOTÃO PARA ABRIR O MODAL (Adicionar ao cabeçalho da secção imobiliária) --}}
{{-- <button @click="$dispatch('modal-show-renda')" ... > --}}

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: NOVA RENDA / IMOBILIÁRIO                               --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:modal-show-renda.window="show()"
    x-on:modal-close-renda.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveRental" class="flex flex-col">
                {{-- HEADER --}}
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-blue-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="home-modern" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Rendimento Imobiliário</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Registo de Rendas e Exploração</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                {{-- BODY --}}
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    {{-- TIPO DE EXPLORAÇÃO (Estilo Wizard) --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-blue-600 z-10">Tipo de Gestão</label>
                        <select wire:model="recPropertyType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                            <option value="arrendamento">🏠 Arrendamento de Longa Duração</option>
                            <option value="alojamento_local">🏨 Alojamento Local (Turismo)</option>
                            <option value="outro">🏢 Comercial / Outro</option>
                        </select>
                    </div>

                    {{-- DESCRIÇÃO --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Identificação do Ativo</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Apartamento em Lisboa..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-blue-500 transition-all">
                    </div>

                    {{-- RENDIMENTO BRUTO VS DESPESAS --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Renda Bruta (€)</label>
                            <input type="number" step="0.01" wire:model.live="recRentalGross" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-emerald-500 transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-red-400 z-10">Despesas Totais (€)</label>
                            <input type="number" step="0.01" wire:model.live="recRentalExpenses" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-red-500 transition-all">
                        </div>
                    </div>

                    {{-- QUADRO DE RESULTADO (IDENTICO AO WIZARD) --}}
                    <div class="p-6 bg-blue-50 dark:bg-blue-900/10 rounded-3xl border border-blue-100 dark:border-blue-900/30 text-center animate-in fade-in duration-700">
                        <p class="text-[10px] font-black text-blue-700 dark:text-blue-300 uppercase tracking-wider">🏦 Rendimento estimado mensal</p>
                        <p class="text-4xl font-black text-blue-700 dark:text-blue-300 mt-2 tracking-tighter italic">
                            {{ number_format((float)$recAmount, 2, ',', ' ') }} €
                        </p>
                        <p class="text-[9px] text-blue-600/60 dark:text-blue-400/50 mt-1 uppercase font-bold tracking-widest">
                            Valor livre após custos operacionais
                        </p>
                    </div>

                    {{-- DIA DE RECOLHA + DESTINO --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia da Cobrança</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-blue-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-blue-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-blue-500/20 transition-all">
                        Ativar Rendimento 🏢
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TRABALHO INDEPENDENTE / RECIBOS VERDES                 --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:modal-show-freelance.window="show()"
    x-on:modal-close-freelance.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveFreelance" class="flex flex-col">
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-sky-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="computer-desktop" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Trabalho Independente</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Recibos Verdes / Prestação de Serviços</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Descrição</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Recibos Verdes - Cliente X" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-sky-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-sky-600 z-10">Atividade</label>
                            <input type="text" wire:model="recFreelanceActivity" placeholder="Ex: Designer, Programador..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-sky-500 transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Tipo de Rendimento</label>
                            <select wire:model="recFreelanceType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="prestacao_servicos">Prestação de Serviços</option>
                                <option value="vendas">Vendas</option>
                                <option value="consultoria">Consultoria</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Faturação Média (€)</label>
                            <input type="number" step="0.01" wire:model.live="recFreelanceGross" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none focus:border-emerald-500 transition-all">
                        </div>                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-red-400 z-10">Despesas Médias (€)</label>
                            <input type="number" step="0.01" wire:model.live="recFreelanceExpenses" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-red-500 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-red-400 z-10">Retenção na Fonte (€)</label>
                            <input type="number" step="0.01" wire:model.live="recFreelanceWithholding" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-red-500 transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Periodicidade</label>
                            <select wire:model="recFreelanceFrequency" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="mensal">Mensal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="anual">Anual</option>
                                <option value="variavel">Variável</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-6 bg-sky-50 dark:bg-sky-900/10 rounded-3xl border border-sky-100 dark:border-sky-900/30 text-center">
                        <p class="text-[10px] font-black text-sky-700 dark:text-sky-300 uppercase tracking-wider">💡 Rendimento estimado</p>
                        <p class="text-4xl font-black text-sky-700 dark:text-sky-300 mt-2 tracking-tighter italic">
                            {{ number_format((float)$recAmount, 2, ',', ' ') }} €
                        </p>
                        <p class="text-[9px] text-sky-600/60 dark:text-sky-400/50 mt-1 uppercase font-bold tracking-widest">
                            Faturação − despesas − retenção
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-sky-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-sky-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-sky-500/20 transition-all">
                        Confirmar Rendimento 💻
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: RENDIMENTOS DE INVESTIMENTOS / DIVIDENDOS              --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:modal-show-investimento.window="show()"
    x-on:modal-close-investimento.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveInvestment" class="flex flex-col">
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-violet-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="chart-bar" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Rendimento de Investimento</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Dividendos, Juros e Mais-Valias</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Descrição</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Dividendos - ETF S&P 500" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-violet-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-violet-600 z-10">Tipo de Rendimento</label>
                            <select wire:model="recInvestmentType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="dividendos">💰 Dividendos</option>
                                <option value="juros">🏦 Juros</option>
                                <option value="mais_valias">📊 Mais-valias</option>
                                <option value="fundos">📈 Fundos / ETFs</option>
                                <option value="cripto">₿ Criptoativos</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Investimento / Entidade</label>
                            <input type="text" wire:model="recInvestmentName" placeholder="Ex: ETF S&P 500, Banco X..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Rendimento (€)</label>
                            <input type="number" step="0.01" wire:model.live="recInvestmentAmount" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Periodicidade</label>
                            <select wire:model="recInvestmentFrequency" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="mensal">Mensal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="semestral">Semestral</option>
                                <option value="anual">Anual</option>
                                <option value="variavel">Variável</option>
                            </select>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-red-400 z-10">Custos / Comissões (€)</label>
                        <input type="number" step="0.01" wire:model.live="recInvestmentExpenses" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-red-500 transition-all">
                    </div>

                    <div class="p-6 bg-violet-50 dark:bg-violet-900/10 rounded-3xl border border-violet-100 dark:border-violet-900/30 text-center">
                        <p class="text-[10px] font-black text-violet-700 dark:text-violet-300 uppercase tracking-wider">📈 Rendimento líquido estimado</p>
                        <p class="text-4xl font-black text-violet-700 dark:text-violet-300 mt-2 tracking-tighter italic">
                            {{ number_format((float)$recAmount, 2, ',', ' ') }} €
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-violet-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-violet-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-violet-600 hover:bg-violet-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-violet-500/20 transition-all">
                        Confirmar Investimento 📈
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: REFORMA / PENSÃO DE VELHICE                            --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:modal-show-reforma.window="show()"
    x-on:modal-close-reforma.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="savePension" class="flex flex-col">
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-rose-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="user-circle" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Reforma / Pensão</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Registo de Pensão de Velhice ou Invalidez</p>                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Descrição</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Pensão de Velhice - Segurança Social" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-rose-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-rose-600 z-10">Tipo de Pensão</label>
                            <select wire:model="recPensionType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="velhice">👴 Pensão de Velhice</option>
                                <option value="invalidez">♿ Pensão de Invalidez</option>
                                <option value="sobrevivencia">❤️ Pensão de Sobrevivência</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Entidade Pagadora</label>
                            <input type="text" wire:model="recPensionEntity" placeholder="Ex: Segurança Social" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Valor Bruto (€)</label>
                            <input type="number" step="0.01" wire:model="recPensionGross" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Valor Líquido (€)</label>
                            <input type="number" step="0.01" wire:model.live="recPensionAmount" placeholder="0,00" class="w-full h-14 bg-emerald-50 dark:bg-emerald-900/10 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-rose-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-rose-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-rose-500/20 transition-all">
                        Confirmar Pensão 👴
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: BOLSA DE ESTUDO / APOIO À FORMAÇÃO                     --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:modal-show-bolsa.window="show()"
    x-on:modal-close-bolsa.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveScholarship" class="flex flex-col">
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-amber-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="academic-cap" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Bolsa de Estudo</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Apoio à Formação / Investigação</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Descrição</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Bolsa de Investigação - FCT" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-amber-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-amber-600 z-10">Tipo de Bolsa</label>
                            <select wire:model="recScholarshipType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="estudo">🎓 Bolsa de Estudo</option>
                                <option value="formacao">📚 Apoio à Formação</option>
                                <option value="investigacao">🔬 Bolsa de Investigação</option>
                                <option value="estagio">💼 Bolsa de Estágio</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Entidade</label>
                            <input type="text" wire:model="recScholarshipEntity" placeholder="Ex: Universidade, IEFP..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Valor (€)</label>
                            <input type="number" step="0.01" wire:model.live="recScholarshipAmount" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Periodicidade</label>
                            <select wire:model="recScholarshipFrequency" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="mensal">Mensal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="anual">Anual</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Data de Fim (opcional)</label>
                            <input type="date" wire:model="recScholarshipEndDate" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-center text-zinc-900 dark:text-white">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-amber-600 z-10">Onde entra este valor?</label>
                        <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-amber-500 transition-all">
                            <option value="">💵 Dinheiro Físico</option>
                            @foreach($this->bankAccounts as $acc)
                                <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-amber-500/20 transition-all">
                        Confirmar Bolsa 🎓
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: OUTRA FONTE DE RENDIMENTO                              --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:modal-show-outro.window="show()"
    x-on:modal-close-outro.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveOther" class="flex flex-col">
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-zinc-900 dark:bg-white rounded-2xl text-white dark:text-zinc-900 shadow-lg">
                        <flux:icon name="sparkles" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Outra Fonte</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Subsídio, Mesada, Comissão...</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-600 z-10">Nome da Fonte</label>
                        <input type="text" wire:model="recOtherSourceDetail" placeholder="Ex: Subsídio, Mesada, Pensão, Comissão..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-zinc-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Valor (€)</label>
                            <input type="number" step="0.01" wire:model.live="recOtherAmount" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Periodicidade</label>
                            <select wire:model="recOtherFrequency" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="mensal">Mensal</option>
                                <option value="semanal">Semanal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="anual">Anual</option>
                                <option value="variavel">Variável</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-zinc-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-zinc-900 dark:bg-white hover:opacity-90 text-white dark:text-zinc-900 rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl transition-all">
                        Confirmar Rendimento ✨
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Modal para atualizar um rendimento recorrente --}}
@if($showRaiseModal)
@php
    $raiseMeta = [
        'emprego'      => ['emoji' => '💼', 'title' => 'Atualizar salário', 'subtitle' => 'Altera o valor deste rendimento recorrente.', 'totalQ' => 'Novo valor líquido mensal', 'addQ' => 'Valor a acrescentar', 'confirm' => 'Guardar alteração'],
        'imobiliario'  => ['emoji' => '🏠', 'title' => 'Atualizar valor da renda', 'subtitle' => 'Altera o valor deste rendimento recorrente.', 'totalQ' => 'Novo valor mensal', 'addQ' => 'Valor a acrescentar', 'confirm' => 'Guardar alteração'],
        'freelance'    => ['emoji' => '💻', 'title' => 'Atualizar rendimento freelance', 'subtitle' => 'Altera o valor líquido deste rendimento.', 'totalQ' => 'Novo valor líquido', 'addQ' => 'Valor a acrescentar', 'confirm' => 'Guardar alteração'],
        'investimento' => ['emoji' => '📊', 'title' => 'Atualizar rendimento de investimento', 'subtitle' => 'Altera o valor deste rendimento recorrente.', 'totalQ' => 'Novo valor', 'addQ' => 'Valor a acrescentar', 'confirm' => 'Guardar alteração'],
        'reforma'      => ['emoji' => '👴', 'title' => 'Atualizar pensão', 'subtitle' => 'Altera o valor líquido desta pensão.', 'totalQ' => 'Novo valor líquido mensal', 'addQ' => 'Valor a acrescentar', 'confirm' => 'Guardar alteração'],
        'bolsa'        => ['emoji' => '🎓', 'title' => 'Atualizar bolsa', 'subtitle' => 'Altera o valor deste rendimento recorrente.', 'totalQ' => 'Novo valor', 'addQ' => 'Valor a acrescentar', 'confirm' => 'Guardar alteração'],
        'outro'        => ['emoji' => '✨', 'title' => 'Atualizar rendimento', 'subtitle' => 'Altera o valor deste rendimento recorrente.', 'totalQ' => 'Novo valor', 'addQ' => 'Valor a acrescentar', 'confirm' => 'Guardar alteração'],
    ];
    $rm = $raiseMeta[$raiseSourceType] ?? $raiseMeta['emprego'];
    $raiseModes = [
        ...($raiseSourceType === 'emprego' ? [
            'none' => ['title' => 'Sem aumento', 'description' => 'Mantém o salário atual e não agenda aumentos automáticos.', 'symbol' => '—'],
        ] : []),
        'total' => ['title' => 'Definir novo valor', 'description' => 'Substitui o valor atual.', 'symbol' => '€'],
        'addition' => ['title' => 'Aumento pontual', 'description' => 'Acrescenta ao valor atual uma única vez.', 'symbol' => '€'],
        'annual_fixed' => ['title' => 'Aumento anual fixo', 'description' => 'Acrescenta este valor todos os anos.', 'symbol' => '€'],
        'annual_percentage' => ['title' => 'Aumento anual percentual', 'description' => 'Aplica esta percentagem sobre o valor atualizado.', 'symbol' => '%'],
    ];
    $selectedRaiseMode = $raiseModes[$raiseMode] ?? $raiseModes['total'];
@endphp
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-init="setTimeout(() => show(), 50)"
    x-on:modal-show-upgrade.window="show()"
    x-on:modal-close-upgrade.window="close()"
    x-on:keydown.escape.window="close()"
>

    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.150ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/60 backdrop-blur-sm"
    ></div>

    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-3 sm:p-6"
    >
        <div
            x-show="open"
            x-transition.scale.duration.150ms
            class="relative z-10 my-auto flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl shadow-zinc-950/20 dark:border-zinc-800 dark:bg-zinc-900"
            @click.stop
        >

            <form wire:submit.prevent="applyRaise" class="flex min-h-0 flex-col" autocomplete="off">
                <header class="flex items-start gap-4 border-b border-zinc-100 px-5 py-5 dark:border-zinc-800 sm:px-7">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-2xl dark:bg-emerald-500/10">
                        {{ $rm['emoji'] }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Rendimento recorrente</p>
                        <h2 class="mt-1 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $rm['title'] }}</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $rm['subtitle'] }}</p>
                    </div>
                    <button type="button" wire:click="closeRaiseModal" @click="close()" aria-label="Fechar"
                        class="rounded-xl p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </header>

                <div class="min-h-0 space-y-6 overflow-y-auto px-5 py-5 text-left sm:px-7 sm:py-6">

                    <section>
                        <div class="mb-3">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Como queres atualizar?</h3>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Escolhe se a alteração é pontual ou automática.</p>
                        </div>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @foreach($raiseModes as $mode => $option)
                                <button type="button" wire:click="$set('raiseMode', '{{ $mode }}')" aria-pressed="{{ $raiseMode === $mode ? 'true' : 'false' }}"
                                    class="rounded-2xl border p-4 text-left transition focus:outline-none focus:ring-2 focus:ring-emerald-500/40 {{ $raiseMode === $mode ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-500 dark:bg-emerald-500/10' : 'border-zinc-200 bg-white hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600 dark:hover:bg-zinc-800' }}">
                                    <span class="flex items-center justify-between gap-3">
                                        <span class="text-sm font-semibold {{ $raiseMode === $mode ? 'text-emerald-800 dark:text-emerald-300' : 'text-zinc-800 dark:text-zinc-100' }}">{{ $option['title'] }}</span>
                                        <span class="flex size-5 shrink-0 items-center justify-center rounded-full border {{ $raiseMode === $mode ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-zinc-300 dark:border-zinc-600' }}">
                                            @if($raiseMode === $mode)<span class="text-xs">✓</span>@endif
                                        </span>
                                    </span>
                                    <span class="mt-1 block text-xs leading-5 text-zinc-500 dark:text-zinc-400">{{ $option['description'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </section>

                    @if($raiseMode !== 'none')
                    <section class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                        <label for="raise-value" class="mb-2 block text-sm font-semibold text-zinc-900 dark:text-white">
                            @if($raiseMode === 'total')
                                {{ $rm['totalQ'] }}
                            @elseif($raiseMode === 'addition')
                                {{ $rm['addQ'] }}
                            @elseif($raiseMode === 'annual_fixed')
                                Valor do aumento anual
                            @else
                                Percentagem de aumento anual
                            @endif
                        </label>
                        <div class="relative">
                            <input id="raise-value" type="number" min="0.01" step="0.01" inputmode="decimal" wire:model="raiseValue"
                                class="h-14 w-full rounded-xl border border-zinc-300 bg-white px-4 pr-12 text-lg font-semibold text-zinc-900 outline-none transition placeholder:font-normal placeholder:text-zinc-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                                placeholder="0,00" autofocus>
                            <span class="absolute inset-y-0 right-3 flex items-center">
                                @if(in_array($raiseMode, ['annual_fixed', 'annual_percentage'], true))
                                    <span class="rounded-lg bg-emerald-100 px-3 py-1 text-lg font-black text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">{{ $selectedRaiseMode['symbol'] }}</span>
                                @else
                                    <span class="text-sm font-semibold text-zinc-500">{{ $selectedRaiseMode['symbol'] }}</span>
                                @endif
                            </span>
                        </div>
                        @if($raiseMode === 'total')
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Este valor passa a ser o novo montante recorrente.</p>
                        @elseif($raiseMode === 'addition')
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Este montante será somado ao valor atual uma única vez.</p>
                        @elseif($raiseMode === 'annual_percentage')
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Usa uma percentagem entre 0,01% e 100%. Cada aplicação considera o valor mais recente.</p>
                        @else
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Este montante será somado ao valor atual em cada aplicação anual.</p>
                        @endif
                        @error('raiseValue')
                            <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </section>
                    @endif

                    @if(in_array($raiseMode, ['annual_fixed', 'annual_percentage'], true))
                        <section>
                            <label for="raise-next-date" class="mb-2 block text-sm font-semibold text-zinc-900 dark:text-white">Data do primeiro aumento</label>
                            <input id="raise-next-date" type="date" wire:model="raiseNextDate" min="{{ now()->toDateString() }}"
                                class="h-12 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white">
                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">O aumento será aplicado nesta data e repetido anualmente.</p>
                            @error('raiseNextDate')
                                <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </section>
                    @endif
                    @if($hasAnnualRaise && $raiseMode !== 'none')
                        <div class="flex flex-col gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/20 dark:bg-amber-500/10 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">Aumento anual ativo</p>
                                <p class="mt-1 text-xs text-amber-800/80 dark:text-amber-200/70">Podes atualizar a configuração acima ou desativar a aplicação automática.</p>
                            </div>
                            <button type="button" wire:click="cancelAnnualRaise" wire:confirm="Queres desativar o aumento anual automático?"
                                class="shrink-0 rounded-xl border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100 dark:border-amber-500/30 dark:bg-zinc-900 dark:text-amber-200 dark:hover:bg-amber-500/10">
                                Desativar
                            </button>
                        </div>
                    @endif
                </div>

                <footer class="flex flex-col-reverse gap-2 border-t border-zinc-100 bg-zinc-50 px-5 py-4 dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row sm:justify-end sm:px-7">
                    <button type="button" wire:click="closeRaiseModal" @click="close()"
                        class="inline-flex h-11 items-center justify-center rounded-xl px-5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-200/70 dark:text-zinc-300 dark:hover:bg-zinc-800">
                        Voltar
                    </button>
                    <button type="submit"
                        class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                        {{ $raiseMode === 'none' ? 'Guardar sem aumento' : $rm['confirm'] }}
                    </button>
                </footer>

            </form>
        </div>
    </div>
</div>
@endif






















    </div>@if($showExtraModal)

<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:modal-show-receita-extra.window="show()"
    x-on:modal-close-receita-extra.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveExtra" class="flex flex-col" autocomplete="off">
                {{-- HEADER --}}
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="sparkles" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Receita Extra</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Regista uma entrada ou serviço extra</p>
                    </div>
                    <button type="button" @click="close(); $wire.closeExtraModal()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                {{-- BODY --}}
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    {{-- DESCRIÇÃO --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-emerald-600 z-10">{{ $extraSalaryId ? 'Serviço extra realizado' : 'Descrição' }}</label>
                        <input type="text" wire:model="description" placeholder="{{ $extraSalaryId ? 'Ex: Serviço técnico, turno extra...' : 'Ex: Serviço, venda...' }}" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-emerald-500 transition-all">
                    </div>

                    {{-- VALOR + MOEDA + DATA --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @if($extraSalaryId)
                            <div class="relative">
                                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Valor por serviço ({{ strtoupper($currency) }})</label>
                                <input type="number" min="0.01" step="0.01" wire:model.live="serviceUnitAmount" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-emerald-500 transition-all">
                                @error('serviceUnitAmount')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="relative">
                                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Serviços feitos este mês</label>
                                <input type="number" min="1" max="1000" step="1" wire:model.live="serviceQuantity" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-emerald-500 transition-all">
                                @error('serviceQuantity')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                            </div>
                        @else
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Valor ({{ strtoupper($currency) }})</label>
                            <input type="number" step="0.01" wire:model="amount" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-emerald-500 transition-all">
                        </div>
                        @endif
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Data</label>
                            <input type="date" wire:model="received_at" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none">
                        </div>
                        @if($extraSalaryId)
                            <div class="relative">
                                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Total do serviço ({{ strtoupper($currency) }})</label>
                                <input type="number" wire:model="amount" readonly class="w-full h-14 bg-emerald-50 dark:bg-emerald-950/30 border-2 border-emerald-200 dark:border-emerald-900 rounded-2xl px-5 text-lg font-black text-emerald-700 dark:text-emerald-300 outline-none">
                                @error('amount')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                            </div>
                        @endif
                    </div>

                    @if($salaryContractOptions->isNotEmpty() && ! $extraSalaryId)
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-emerald-600 z-10">Associar ao contrato</label>
                            <select wire:model.live="extraSalaryId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="">Sem contrato associado</option>
                                @foreach($salaryContractOptions as $salaryContract)
                                    <option value="{{ $salaryContract->id }}">{{ $salaryContract->description }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-zinc-500">A entrada aparece no total anual e no histórico do contrato escolhido.</p>
                        </div>
                    @endif

                    {{-- IMPOSTO ESTIMADO --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-amber-600 z-10">Imposto Estimado (%)</label>
                        <div class="relative">
                        <input type="number" step="0.1" min="0" max="100" wire:model="tax_estimate" placeholder="Ex: 25 (IRS, IVA...)" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl pl-5 pr-16 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-amber-500 transition-all">
                        <span class="absolute right-4 top-1/2 flex size-9 -translate-y-1/2 items-center justify-center rounded-xl border-2 border-amber-300 bg-amber-100 text-lg font-black text-amber-700 shadow-sm dark:border-amber-500/60 dark:bg-amber-500/20 dark:text-amber-300">%</span>
                        </div>
                        @if($tax_estimate && $amount)
                            <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold mt-1.5">
                                Imposto estimado: ~{{ number_format($amount * $tax_estimate / 100, 2, ',', '.') }} {{ strtoupper($currency) }}
                                · Líquido: ~{{ number_format($amount - ($amount * $tax_estimate / 100), 2, ',', '.') }} {{ strtoupper($currency) }}
                            </p>
                        @endif
                    </div>

                    {{-- NOTAS --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-zinc-400 z-10">Notas (opcional)</label>
                        <textarea wire:model="notes" rows="2" placeholder="Observações, cliente, referência..." class="w-full bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 py-4 text-sm font-medium text-zinc-900 dark:text-white resize-none outline-none focus:border-emerald-500 transition-all"></textarea>
                    </div>

                    {{-- DESTINO DO DINHEIRO --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-emerald-600 z-10">Onde entra este valor?</label>
                        <select wire:model="extraDestination" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-emerald-500 transition-all">
                            @if($extraSalaryId)
                                <option value="salary">💼 Adicionar ao salário do próximo mês</option>
                            @endif
                            <option value="cash">💵 Dinheiro Físico</option>
                            @foreach($this->bankAccounts as $acc)
                                @if((int) $acc->user_id === (int) auth()->id())
                                    <option value="bank:{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if($extraSalaryId && $extraDestination === 'salary')
                            <p class="mt-2 text-xs font-medium text-emerald-700 dark:text-emerald-300">O total calculado (valor por serviço × número de serviços) será somado ao próximo salário na data prevista, sem alterar o salário base do contrato.</p>
                        @endif
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close(); $wire.closeExtraModal()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button
                        wire:click="saveExtra"
                        @click="close()"
                        wire:loading.attr="disabled"
                        wire:target="saveExtra"
                        class="flex-[2] h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-emerald-500/20 transition-all disabled:opacity-60">
                        Confirmar Ganho ✨
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: SALÁRIO FIXO · PREMIUM + FLUIDO --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div    x-data="{
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
    x-on:modal-show-salario.window="show()"
    x-on:modal-close-salario.window="close()"
    x-on:keydown.escape.window="close()"
>

    {{-- BACKDROP PREMIUM --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.120ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"
    ></div>

    {{-- WRAPPER --}}
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
    >

        {{-- PAINEL PREMIUM --}}
        <div
            x-show="open"
            x-transition.scale.duration.120ms
            class="relative z-10 w-full max-w-2xl rounded-[2rem] shadow-xl overflow-hidden transform-gpu
                   bg-emerald-500/10 backdrop-blur-sm border border-emerald-500/20"
            @click.stop
        >

            <form wire:submit.prevent="{{ $editingFixedId ? 'updateFixed' : 'saveFixed' }}" class="flex max-h-[86vh] flex-col" autocomplete="off">

                {{-- HEADER --}}
                <div class="shrink-0 p-6 pb-4 flex items-center gap-4 border-b border-white/10 bg-white/10 backdrop-blur-sm">
                    <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-md shadow-emerald-500/20">
                        <flux:icon name="calendar-days" class="size-5" />
                    </div>

                    <div class="flex-1 min-w-0">
                        <h2 class="font-black uppercase italic tracking-tight leading-none text-white">
                            {{ $editingFixedId ? 'Editar Salário' : 'Configurar Salário' }}
                        </h2>
                        <p class="text-[10px] text-emerald-300 font-black uppercase tracking-widest mt-1.5 italic">
                            Rendimento que se repete automaticamente
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="close()"
                        class="rounded-full p-2 hover:bg-white/10 text-zinc-300 hover:text-white transition-colors"
                    >
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                {{-- BODY --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">
{{-- SELETOR DE VÍNCULO EMPRESARIAL --}}
<div class="relative mb-3 text-left">
    <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
        Vincular a uma Empresa
    </label>
    <div class="relative">
        <select
            wire:model.live="recWorkspaceId"
            class="w-full bg-white/10 dark:bg-zinc-900/40 border border-white/10 rounded-2xl py-4 px-5
                   text-sm font-bold text-white outline-none appearance-none transition-all focus:ring-2 focus:ring-emerald-500/30"
        >
            <option value="" class="bg-white text-zinc-900">Entrada Manual (Individual)</option>

            @foreach($collabBusinesses as $biz)
                {{-- Estilo inline para garantir que as opções são legíveis --}}
                <option value="{{ $biz->id }}" class="bg-white text-zinc-900">
                    🏢 {{ $biz->name }}
                </option>
            @endforeach
        </select>

        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-300/50">
            <flux:icon name="chevron-down" variant="micro" class="size-4" />
        </div>
    </div>
    <p class="text-[8px] text-zinc-400 uppercase font-black mt-2 ml-1 tracking-[0.2em]">
        Selecione a empresa onde trabalha para importar os dados do contrato
    </p>
</div>

                    {{-- Identificação --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Identificação
                        </label>
                        <input
                            type="text"
                            wire:model="recDescription"
                            placeholder="Ex: Salário Mensal - Empresa X"
                            class="w-full bg-white/10 dark:bg-zinc-900/20 border @error('recDescription') border-red-500/60 @else border-white/10 @enderror rounded-2xl py-4 px-5
                                   text-sm font-bold text-white placeholder-white/40 outline-none transition-all focus:ring-2 focus:ring-emerald-500/30"
                        >
                        @error('recDescription')
                            <p class="text-red-400 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Valor + Dia --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Valor Líquido (€)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="recAmount"
                                placeholder="0,00"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border @error('recAmount') border-red-500/60 @else border-white/10 @enderror rounded-2xl py-4 px-5
                                       text-lg font-black text-emerald-400 placeholder-white/40 outline-none transition-all focus:ring-2 focus:ring-emerald-500/30"
                            >
                            @error('recAmount')
                                <p class="text-red-400 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Dia de Recebimento
                            </label>
                            <input
                                type="number"
                                min="1"
                                max="31"
                                wire:model="recDay"
                                placeholder="Ex: 25"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border @error('recDay') border-red-500/60 @else border-white/10 @enderror rounded-2xl py-4 px-5
                                       text-lg font-black text-white text-center outline-none transition-all focus:ring-2 focus:ring-emerald-500/30"
                            >
                            @error('recDay')
                                <p class="text-red-400 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Fonte + Frequência --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Fonte
                            </label>
                            <select
                                wire:model="recSource"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none transition-all focus:ring-2 focus:ring-emerald-500/30 appearance-none"
                            >
                                <option value="emprego">💼 Emprego</option>
                                <option value="freelance">💻 Freelance</option>
                                <option value="investimento">📈 Investimento</option>
                                <option value="outro">✨ Outro</option>
                            </select>
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Frequência
                            </label>
                            <select
                                wire:model="recFrequency"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none transition-all focus:ring-2 focus:ring-emerald-500/30 appearance-none"
                            >
                                <option value="semanal">📅 Semanal</option>
                                <option value="mensal">🔁 Mensal</option>
                                <option value="anual">📆 Anual</option>
                            </select>
                        </div>

                    </div>

                    {{-- Onde entra este valor --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Onde entra este valor?
                        </label>
                        <select
                            wire:model="recBankAccountId"                            class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                   text-sm font-bold text-white outline-none transition-all focus:ring-2 focus:ring-emerald-500/30 appearance-none"
                        >
                            <option value="" class="bg-white text-zinc-900">💵 Dinheiro Físico</option>
                            @foreach($this->bankAccounts as $acc)
                                <option value="{{ $acc->id }}" class="bg-white text-zinc-900">🏦 Banco — {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Imposto estimado --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Imposto Estimado (%)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                max="100"
                                wire:model="recTaxEstimate"
                                placeholder="Ex: 28.5 (IRS)"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white placeholder-white/40 outline-none transition-all focus:ring-2 focus:ring-amber-500/30"
                            >
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-emerald-300 font-black text-sm">%</span>
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Notas (opcional)
                        </label>
                        <textarea
                            wire:model="recNotes"
                            rows="2"
                            placeholder="Empresa, contrato, observações..."
                            class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                   text-sm font-medium text-white placeholder-white/40 resize-none outline-none transition-all focus:ring-2 focus:ring-emerald-500/30"
                        ></textarea>
                    </div>

                </div>
{{-- FOOTER DO MODAL --}}
                <div class="shrink-0 p-6 pt-4 flex flex-col sm:flex-row gap-3 border-t border-white/10 bg-white/10 backdrop-blur-sm">
                    <button
                        type="button"
                        @click="close(); $wire.set('editingFixedId', null)"
                        class="w-full h-14 rounded-2xl text-zinc-300 hover:text-white hover:bg-white/10
                               font-bold uppercase text-xs tracking-widest transition-all active:scale-95"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="saveFixed, updateFixed"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20
                               transition-all text-xs active:scale-95 disabled:opacity-60"
                    >
                        {{-- Texto muda quando está a processar --}}
                        <span wire:loading.remove wire:target="saveFixed, updateFixed">
                            {{ $editingFixedId ? 'Guardar Alterações' : 'Confirmar Salário' }}
                        </span>

                        <span wire:loading wire:target="saveFixed, updateFixed" class="flex items-center justify-center gap-2">
                            <div class="size-3 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                            A processar...
                        </span>
                    </button>
                </div>

            </form> {{-- FECHA O FORM --}}
        </div> {{-- FECHA O PAINEL --}}
    </div> {{-- FECHA O WRAPPER --}}
</div> {{-- FECHA O x-data DO MODAL --}}

    {{-- ================================================================== --}}
    {{-- FOOTER DA PÁGINA                                                 --}}
    {{-- ================================================================== --}}
    <footer class="pt-16 sm:pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-16 sm:mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Receitas e Fluxo
        </p>
    </footer>

</div> {{-- FECHA A DIV RAIZ DO FICHEIRO --}}
