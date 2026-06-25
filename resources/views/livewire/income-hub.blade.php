{{-- ====================================================================== --}}
{{--  LAYOUT RESPONSIVO · MOBILE-FIRST · OTIMIZADO · COMENTADO              --}}
{{--  Mantém TODO o teu design original, apenas fluido e adaptado           --}}
{{-- ====================================================================== --}}

<div
    class="space-y-10 pb-20 px-4 sm:px-6 lg:px-8"
    x-data="{ openExtra: false, openFixed: false }"
    x-on:open-extra-modal.window="openExtra = true"
    x-on:open-fixed-modal.window="openFixed = true"
    x-on:close-fixed-modal.window="openFixed = false"
    x-on:keydown.escape.window="openExtra = false; openFixed = false"
>

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
                    <h1 class="text-3xl sm:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
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

            {{-- Botão salário fixo --}}
            <button
                @click="openFixed = true"
                class="flex items-center gap-2 px-4 h-11 rounded-2xl text-zinc-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 font-black uppercase text-sm transition-colors"
            >
                <flux:icon name="calendar-days" class="size-4" />
                Configurar Salário
            </button>

            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

            {{-- Botão receita extra --}}
            <button
                @click="openExtra = true"
                class="flex items-center gap-2 px-6 h-11 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase text-sm shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02]"
            >
                <flux:icon name="plus" class="size-4" />
                Receita Extra
            </button>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- CARD TOTAL MENSAL · RESPONSIVO                                    --}}
    {{-- ================================================================== --}}
    <div class="relative overflow-hidden bg-emerald-600 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl border-none">

        {{-- Fundo decorativo --}}
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 50 Q 25 40 50 50 T 100 50" fill="none" stroke="white" stroke-width="0.5"/>
                <path d="M0 30 Q 25 20 50 30 T 100 30" fill="none" stroke="white" stroke-width="0.5"/>
                <path d="M0 70 Q 25 60 50 70 T 100 70" fill="none" stroke="white" stroke-width="0.5"/>
            </svg>
        </div>

        {{-- Conteúdo --}}
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-8">

            <div class="text-center lg:text-left space-y-2">
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-100 opacity-80">
                    Total Projetado para {{ now()->translatedFormat('F') }}
                </h3>

                <div class="flex items-baseline justify-center lg:justify-start gap-4">
                    <span class="text-6xl sm:text-7xl font-black text-white tracking-tighter italic">
                        {{ number_format($totalMonthly, 2, ',', ' ') }}
                        <span class="text-3xl">€</span>
                    </span>
                </div>

                @if($taxEstimated > 0)
                    <p class="text-[10px] text-emerald-100/70 font-bold uppercase tracking-widest">
                        Imposto estimado: ~{{ number_format($taxEstimated, 2, ',', ' ') }}€
                        · Líquido: ~{{ number_format($totalMonthly - $taxEstimated, 2, ',', ' ') }}€
                    </p>
                @endif
            </div>

            <div class="hidden lg:block">
                <flux:icon name="banknotes" class="size-32 text-white/10 rotate-12" />
            </div>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- CARDS DE ESTATÍSTICAS · GRID RESPONSIVO                          --}}
    {{-- ================================================================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Média mensal --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Média Mensal</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tighter italic">
                {{ number_format($avgMonthly, 0, ',', ' ') }}€
            </p>
            <p class="text-[10px] text-zinc-400 font-medium mt-1">Últimos 6 meses</p>
        </div>

        {{-- Melhor mês --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Melhor Mês</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tighter italic">
                {{ number_format($bestMonth['total'], 0, ',', ' ') }}€
            </p>
            <p class="text-[10px] text-zinc-400 font-medium mt-1">{{ $bestMonth['label'] }}</p>
        </div>

        {{-- Total anual --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Total {{ now()->year }}</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tighter italic">
                {{ number_format($totalYear, 0, ',', ' ') }}€
            </p>
            <p class="text-[10px] text-zinc-400 font-medium mt-1">Entradas este ano</p>
        </div>

        {{-- Por fonte --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-3">Por Fonte</p>

            <div class="space-y-1.5">
                @foreach(['emprego'=>'💼','freelance'=>'💻','investimento'=>'📈','outro'=>'✨'] as $src => $emoji)
                    @if(isset($bySource[$src]) && $bySource[$src] > 0)
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-500">{{ $emoji }} {{ ucfirst($src) }}</span>
                            <span class="text-[10px] font-black text-emerald-600">
                                {{ number_format($bySource[$src], 0, ',', ' ') }}€
                            </span>
                        </div>
                    @endif
                @endforeach

                @if($bySource->isEmpty())
                    <p class="text-[10px] text-zinc-400 italic">Sem dados ainda</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- GRÁFICO 6 MESES · RESPONSIVO                                     --}}
    {{-- ================================================================== --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm">

        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-6">
            Evolução dos Últimos 6 Meses
        </p>

        @php $maxVal = $monthlyTotals->max('total') ?: 1; @endphp

        <div class="flex items-end gap-3 h-32">

            @foreach($monthlyTotals as $month)
                @php
                    $height = $maxVal > 0 ? max(4, ($month['total'] / $maxVal) * 100) : 4;
                @endphp

                <div class="flex-1 flex flex-col items-center gap-2">
                    <span class="text-[9px] font-black text-zinc-400">
                        @if($month['total'] > 0)
                            {{ number_format($month['total'], 0) }}€
                        @endif
                    </span>

                    <div
                        class="w-full rounded-t-xl transition-all
                        {{ $month['label'] === now()->translatedFormat('M')
                            ? 'bg-emerald-600'
                            : 'bg-emerald-200 dark:bg-emerald-900/40'
                        }}"
                        style="height: {{ $height }}%"
                    ></div>

                    <span class="text-[9px] font-black uppercase text-zinc-400">
                        {{ $month['label'] }}
                    </span>
                </div>
            @endforeach

        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- GRELHA FIXOS + EXTRAS · RESPONSIVA                               --}}
    {{-- ================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">



{{-- ============================================================= --}}
{{-- RENDIMENTOS FIXOS COM GESTÃO DE AUMENTO                        --}}
{{-- ============================================================= --}}
<div class="space-y-6">

    {{-- Título --}}
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="calendar-days" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Rendimentos Fixos
            </h2>
        </div>

        <flux:badge
            variant="neutral"
            class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-2 py-0.5 border-none"
        >
            {{ $fixedIncomes->count() }} Ativos
        </flux:badge>
    </div>

    {{-- Lista de Cards --}}
    <div class="space-y-4">
        @forelse($fixedIncomes as $fixed)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 flex justify-between items-start group transition-all hover:border-emerald-500/40 shadow-sm relative overflow-hidden">

                {{-- Brilho de fundo no hover --}}
                <div class="absolute -right-10 -top-10 size-24 bg-emerald-500/5 blur-2xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>

                {{-- Conteúdo Esquerda --}}
                <div class="flex items-start gap-5 relative z-10">
                    {{-- Tile do Dia --}}
                    <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 shadow-inner shrink-0">
                        <span class="text-[8px] font-black text-zinc-400 uppercase leading-none mb-1">DIA</span>
                        <span class="text-xl font-black text-emerald-600 leading-none">
                            {{ sprintf('%02d', $fixed->day_of_month) }}
                        </span>
                    </div>

                    {{-- Info do Rendimento --}}
                    <div>
                        <p class="text-sm font-black dark:text-white uppercase tracking-tight">
                            {{ $fixed->description }}
                        </p>

                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest">
                                {{ ucfirst($fixed->frequency ?? 'mensal') }}
                            </p>

                            @if($fixed->source)
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-500">
                                    {{ ['emprego'=>'💼','freelance'=>'💻','investimento'=>'📈','outro'=>'✨'][$fixed->source] ?? '' }}
                                    {{ ucfirst($fixed->source) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Valor + Ações Rápidas --}}
                <div class="flex items-center gap-3 shrink-0 relative z-10">
                    <span class="text-xl font-black text-emerald-600 tracking-tighter italic mr-2">
                        {{ number_format($fixed->amount, 2, ',', ' ') }}€
                    </span>

                    {{-- BOTÃO: RECEBI UM AUMENTO 🚀 --}}
                    <button
                        wire:click="openRaiseModal({{ $fixed->id }})"
                        class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all opacity-0 group-hover:opacity-100 shadow-sm"
                        title="Recebi um aumento"
                    >
                        <flux:icon name="rocket-launch" variant="micro" class="size-4" />
                    </button>

                    <flux:button
                        wire:click="editFixed({{ $fixed->id }})"
                        variant="ghost"
                        icon="pencil-square"
                        size="xs"
                        class="opacity-0 group-hover:opacity-100 transition-opacity"
                    />

                    <flux:button
                        wire:click="deleteFixed({{ $fixed->id }})"
                        wire:confirm="Eliminar rendimento fixo?"
                        variant="ghost"
                        icon="trash"
                        size="xs"
                        color="red"
                        class="opacity-0 group-hover:opacity-100 transition-opacity"
                    />
                </div>
            </div>
        @empty
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <flux:icon name="clock" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest">Sem salários configurados</p>
            </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL TÁTICO: ATUALIZAÇÃO DE AUMENTO                           --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($showRaiseModal)
    <div class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 shadow-2xl border border-zinc-200 dark:border-zinc-800 animate-in zoom-in-95 duration-200">

            <div class="text-center mb-8">
                <div class="size-20 bg-emerald-500/10 rounded-[2rem] flex items-center justify-center mx-auto mb-4 border border-emerald-500/20">
                    <span class="text-4xl animate-bounce">🚀</span>
                </div>
                <h2 class="text-2xl font-black uppercase italic tracking-tighter dark:text-white leading-none">Upgrade de Salário!</h2>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-2">Parabéns pelo teu progresso financeiro</p>
            </div>

            <div class="space-y-6">
                {{-- Selector de Modo: Total ou Soma --}}
                <div class="flex bg-zinc-100 dark:bg-zinc-800 p-1 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                    <button wire:click="$set('raiseMode', 'total')" class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl transition-all {{ $raiseMode === 'total' ? 'bg-white dark:bg-zinc-600 shadow-md text-emerald-600' : 'text-zinc-500' }}">Definir Novo Total</button>
                    <button wire:click="$set('raiseMode', 'addition')" class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl transition-all {{ $raiseMode === 'addition' ? 'bg-white dark:bg-zinc-600 shadow-md text-emerald-600' : 'text-zinc-500' }}">Somar Aumento</button>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-zinc-400 ml-1">
                        {{ $raiseMode === 'total' ? 'Qual o novo valor líquido?' : 'Quanto vais receber a mais?' }}
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            step="0.01"
                            wire:model="raiseValue"
                            class="w-full h-20 bg-zinc-50 dark:bg-zinc-950 border-none rounded-[1.5rem] px-8 text-3xl font-black text-emerald-600 shadow-inner focus:ring-4 focus:ring-emerald-500/10 transition-all text-center"
                            placeholder="0,00"
                            autofocus
                        >
                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-xl">€</span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button wire:click="$set('showRaiseModal', false)" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:text-zinc-600 transition-colors">Cancelar</button>
                    <button wire:click="applyRaise" class="flex-[2] h-14 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-xl shadow-emerald-500/30 transition-all hover:scale-[1.02] active:scale-95">
                        Confirmar Upgrade 💪
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>c

























        {{-- ============================================================= --}}
        {{-- ENTRADAS EXTRAS                                               --}}
        {{-- ============================================================= --}}
        <div class="space-y-6">

            {{-- Título --}}
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="sparkles" variant="outline" class="size-4" />
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                        Entradas Extras ({{ now()->translatedFormat('M') }})
                    </h2>
                </div>

                <flux:badge
                    variant="success"
                    class="bg-emerald-500/10 text-emerald-600 text-[10px] font-black uppercase px-2 py-0.5 border-none"
                >
                    {{ $extraIncomes->count() }} Lançamentos
                </flux:badge>
            </div>

            {{-- Tabela --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">

                <div class="overflow-x-auto">
                                       <table class="w-full text-left border-collapse">
                        <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                            <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                                <th class="p-5">Data</th>
                                <th class="p-5">Descrição</th>
                                <th class="p-5">Fonte</th>
                                <th class="p-5 text-right px-8">Valor</th>
                                <th class="p-5 w-10"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            @forelse($extraIncomes as $extra)
                                <tr class="hover:bg-emerald-50/30 dark:hover:bg-emerald-500/5 transition-all group">
                                    <td class="p-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black dark:text-white leading-none">
                                                {{ \Carbon\Carbon::parse($extra->received_at)->format('d') }}
                                            </span>
                                            <span class="text-[9px] font-black text-zinc-400 uppercase mt-1">
                                                {{ \Carbon\Carbon::parse($extra->received_at)->translatedFormat('M') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="p-5">
                                        <p class="text-sm font-bold dark:text-white uppercase tracking-tight">
                                            {{ $extra->description }}
                                        </p>

                                        @if($extra->notes)
                                            <p class="text-[10px] text-zinc-400 italic mt-0.5 truncate max-w-[140px] sm:max-w-[180px]">
                                                {{ $extra->notes }}
                                            </p>
                                        @endif

                                        @if($extra->tax_estimate)
                                            <p class="text-[9px] text-amber-500 font-bold mt-0.5">
                                                ~{{ $extra->tax_estimate }}% imposto
                                            </p>
                                        @endif
                                    </td>

                                    <td class="p-5">
                                        @php
                                            $sourceMap = [
                                                'emprego'     => ['icon' => '💼', 'color' => 'text-blue-600'],
                                                'freelance'   => ['icon' => '💻', 'color' => 'text-purple-600'],
                                                'investimento'=> ['icon' => '📈', 'color' => 'text-emerald-600'],
                                                'outro'       => ['icon' => '✨', 'color' => 'text-zinc-500'],
                                            ];
                                        @endphp

                                        <span class="text-[10px] font-black uppercase px-2 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                                            {{ $sourceMap[$extra->source ?? 'outro']['icon'] ?? '✨' }}
                                            {{ ucfirst($extra->source ?? 'outro') }}
                                        </span>
                                    </td>

                                    <td class="p-5 text-right px-8">
                                        <span class="text-lg font-black text-emerald-600 tracking-tighter">
                                            +{{ number_format($extra->amount, 2, ',', ' ') }}€
                                        </span>
                                    </td>

                                    <td class="p-5">
                                        <flux:button
                                            wire:click="deleteExtra({{ $extra->id }})"
                                            variant="ghost"
                                            icon="trash"
                                            size="xs"
                                            color="red"
                                            class="opacity-0 group-hover:opacity-100 transition-opacity"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 sm:p-20 text-center">
                                        <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest">
                                            Sem ganhos extra este mês
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- MODAL: RECEITA EXTRA · MOBILE-FIRST                              --}}
    {{-- ================================================================== --}}
    <div
        x-show="openExtra"
        x-cloak
        x-transition:enter="transition-opacity ease-out duration-75"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="openExtra = false"
        class="fixed inset-0 z-50 bg-zinc-950/50"
    ></div>

    <div
        x-show="openExtra"
        x-cloak
        @click.self="openExtra = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
    >
        <div
            @click.stop
            x-show="openExtra"
            x-transition:enter="transition ease-out duration-75 transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.99] translate-y-0.5"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.99] translate-y-0.5"
            class="relative z-10 w-full max-w-lg bg-white dark:bg-zinc-950 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden transform-gpu"
        >
            {{-- Loading overlay --}}
            <div
                wire:loading
                wire:target="saveExtra"
                class="absolute inset-0 bg-white/90 dark:bg-zinc-950/90 z-50 flex items-center justify-center"
            >
                <div class="size-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
            </div>

            <div class="flex flex-col max-h-[86vh]">

                {{-- HEADER --}}
                <div class="shrink-0 p-5 sm:p-6 pb-4 flex items-center gap-4 border-b border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-md shadow-emerald-500/20">
                        <flux:icon name="sparkles" class="size-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                            Receita Extra
                        </h2>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                            Regista uma entrada pontual ou recorrente
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="openExtra = false"
                        class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors"
                    >
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                {{-- BODY --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-5">

                    {{-- Descrição --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                            Origem do Ganho
                        </label>
                        <input
                            type="text"
                            wire:model="description"
                            placeholder="Ex: Freelance, Venda, Bónus..."
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all"
                        >
                        @error('description')
                            <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Valor + Data --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                                Valor (€)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="amount"
                                placeholder="0,00"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-lg font-black text-emerald-600 outline-none transition-all"
                            >
                            @error('amount')
                                <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                                Data
                            </label>
                            <input
                                type="date"
                                wire:model="received_at"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all"
                            >
                        </div>
                    </div>

                    {{-- Fonte + Frequência --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                                Fonte
                            </label>
                            <select
                                wire:model="source"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all appearance-none"
                            >
                                <option value="emprego">💼 Emprego</option>
                                <option value="freelance">💻 Freelance</option>
                                <option value="investimento">📈 Investimento</option>
                                <option value="outro">✨ Outro</option>
                            </select>
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                                Frequência
                            </label>
                            <select
                                wire:model="frequency"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all appearance-none"
                            >
                                <option value="pontual">📌 Pontual</option>
                                <option value="semanal">📅 Semanal</option>
                                <option value="mensal">🔁 Mensal</option>
                                <option value="anual">📆 Anual</option>
                            </select>
                        </div>
                    </div>

                    {{-- Imposto estimado --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                            Imposto Estimado (%)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                max="100"
                                wire:model="tax_estimate"
                                placeholder="Ex: 25 (IRS, IVA...)"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-amber-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all"
                            >
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-sm">%</span>
                        </div>
                        @if($tax_estimate && $amount)
                            <p class="text-[10px] text-amber-500 font-bold mt-1.5">
                                Imposto estimado: ~{{ number_format($amount * $tax_estimate / 100, 2, ',', '.') }}€
                                · Líquido: ~{{ number_format($amount - ($amount * $tax_estimate / 100), 2, ',', '.') }}€
                            </p>
                        @endif
                    </div>

                    {{-- Notas --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                            Notas (opcional)
                        </label>
                        <textarea
                            wire:model="notes"
                            rows="2"
                            placeholder="Observações, cliente, referência..."
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-medium dark:text-white resize-none outline-none transition-all placeholder:text-zinc-400"
                        ></textarea>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="shrink-0 p-5 sm:p-6 pt-4 flex flex-col sm:flex-row gap-3 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <button
                        type="button"
                        @click="openExtra = false"
                        class="w-full h-14 rounded-2xl text-zinc-500 hover:text-zinc-800 dark:hover:text-white
                               hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold uppercase text-xs tracking-widest
                               transition-all active:scale-95"
                    >
                        Cancelar
                    </button>
                    <button
                        wire:click="saveExtra"
                        @click="openExtra = false"
                        wire:loading.attr="disabled"
                        wire:target="saveExtra"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20
                               transition-all text-xs active:scale-95 disabled:opacity-60"
                    >
                        Confirmar Ganho
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- MODAL: SALÁRIO FIXO · MOBILE-FIRST                               --}}
    {{-- ================================================================== --}}
    <div
        x-show="openFixed"
        x-cloak
        x-transition:enter="transition-opacity ease-out duration-75"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="openFixed = false"
        class="fixed inset-0 z-50 bg-zinc-950/50"
    ></div>

    <div
        x-show="openFixed"
        x-cloak
        @click.self="openFixed = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
    >
        <div
            @click.stop
            x-show="openFixed"
            x-transition:enter="transition ease-out duration-75 transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.99] translate-y-0.5"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.99] translate-y-0.5"
            class="relative z-10 w-full max-w-lg bg-white dark:bg-zinc-950 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden transform-gpu"
        >
            {{-- Loading --}}
            <div
                wire:loading
                wire:target="saveFixed,updateFixed"
                class="absolute inset-0 bg-white/90 dark:bg-zinc-950/90 z-50 flex items-center justify-center"
            >
                <div class="size-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
            </div>

            <div class="flex flex-col max-h-[86vh]">

                {{-- HEADER --}}
                <div class="shrink-0 p-5 sm:p-6 pb-4 flex items-center gap-4 border-b border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-md shadow-emerald-500/20">
                        <flux:icon name="calendar-days" class="size-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                            {{ $editingFixedId ? 'Editar Salário' : 'Configurar Salário' }}
                        </h2>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                            Rendimento que se repete automaticamente
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="openFixed = false"
                        class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors"
                    >
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                {{-- BODY --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-5">

                    {{-- Descrição --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                            Identificação
                        </label>
                        <input
                            type="text"
                            wire:model="recDescription"
                            placeholder="Ex: Salário Mensal - Empresa X"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all"
                        >
                        @error('recDescription')
                            <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Valor + Dia --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                                Valor Líquido (€)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="recAmount"
                                placeholder="0,00"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-lg font-black text-emerald-600 outline-none transition-all"
                            >
                            @error('recAmount')
                                <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                                Dia de Recebimento
                            </label>
                            <input
                                type="number"
                                min="1"
                                max="31"
                                wire:model="recDay"
                                placeholder="Ex: 25"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-lg font-black text-center dark:text-white outline-none transition-all"
                            >
                            @error('recDay')
                                <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Fonte + Frequência --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                                Fonte
                            </label>
                            <select
                                wire:model="recSource"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all appearance-none"
                            >
                                <option value="emprego">💼 Emprego</option>
                                <option value="freelance">💻 Freelance</option>
                                <option value="investimento">📈 Investimento</option>
                                <option value="outro">✨ Outro</option>
                            </select>
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                                Frequência
                            </label>
                            <select
                                wire:model="recFrequency"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all appearance-none"
                            >
                                <option value="semanal">📅 Semanal</option>
                                <option value="mensal">🔁 Mensal</option>
                                <option value="anual">📆 Anual</option>
                            </select>
                        </div>
                    </div>

                    {{-- Imposto estimado --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
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
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-amber-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all"
                            >
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-sm">%</span>
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                            Notas (opcional)
                        </label>
                        <textarea
                            wire:model="recNotes"
                            rows="2"
                            placeholder="Empresa, contrato, observações..."
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-medium dark:text-white resize-none outline-none transition-all placeholder:text-zinc-400"
                        ></textarea>
                    </div>

                </div>
{{-- MODAL: ATUALIZAÇÃO DE RENDIMENTO (AUMENTO) --}}
    @if($showRaiseModal)
    <div class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-md">
        <div class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            <div class="text-center mb-8">
                <div class="size-16 bg-emerald-500/10 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-emerald-500/20">
                    <span class="text-3xl animate-bounce">🚀</span>
                </div>
                <h2 class="text-xl font-black uppercase italic tracking-tighter dark:text-white">Parabéns pelo Aumento!</h2>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1 text-center">Atualiza o teu rendimento mensal</p>
            </div>

            <div class="space-y-6">
                {{-- Selector de Modo --}}
                <div class="flex bg-zinc-100 dark:bg-zinc-800 p-1 rounded-2xl">
                    <button wire:click="$set('raiseMode', 'total')" class="flex-1 py-2 text-[10px] font-black uppercase rounded-xl transition-all {{ $raiseMode === 'total' ? 'bg-white dark:bg-zinc-700 shadow-sm text-emerald-600' : 'text-zinc-500' }}">Novo Total</button>
                    <button wire:click="$set('raiseMode', 'addition')" class="flex-1 py-2 text-[10px] font-black uppercase rounded-xl transition-all {{ $raiseMode === 'addition' ? 'bg-white dark:bg-zinc-700 shadow-sm text-emerald-600' : 'text-zinc-500' }}">Valor do Aumento</button>
                </div>

                <div class="space-y-2">
                    <flux:label class="text-[9px] font-black uppercase text-zinc-400 ml-1">
                        {{ $raiseMode === 'total' ? 'Introduz o novo salário líquido' : 'Quanto vais receber a mais?' }}
                    </flux:label>
                    <div class="relative">
                        <input
                            type="number"
                            step="0.01"
                            wire:model="raiseValue"
                            class="w-full h-16 bg-zinc-50 dark:bg-zinc-950 border-none rounded-2xl px-6 text-2xl font-black text-emerald-600 shadow-inner focus:ring-2 focus:ring-emerald-500/20"
                            placeholder="0,00"
                            autofocus
                        >
                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-zinc-400 font-black">€</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showRaiseModal', false)" class="flex-1 h-12 rounded-xl text-[10px] font-black uppercase text-zinc-400">Cancelar</button>
                    <button wire:click="applyRaise" class="flex-[2] h-12 bg-emerald-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all">
                        Atualizar Salário
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
                {{-- FOOTER --}}
                <div class="shrink-0 p-5 sm:p-6 pt-4 flex flex-col sm:flex-row gap-3 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <button
                        type="button"
                        @click="openFixed = false"
                        wire:click="$set('editingFixedId', null)"
                        class="w-full h-14 rounded-2xl text-zinc-500 hover:text-zinc-800 dark:hover:text-white
                               hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold uppercase text-xs tracking-widest
                               transition-all active:scale-95"
                    >
                        Cancelar
                    </button>
                    @if($editingFixedId)
                    <button
                        wire:click="updateFixed"
                        wire:loading.attr="disabled"
                        wire:target="updateFixed"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20
                               transition-all text-xs active:scale-95 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="updateFixed">Atualizar Salário</span>
                        <span wire:loading wire:target="updateFixed">A guardar...</span>
                    </button>
                    @else
                    <button
                        wire:click="saveFixed"
                        @click="openFixed = false"
                        wire:loading.attr="disabled"
                        wire:target="saveFixed"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20
                               transition-all text-xs active:scale-95 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="saveFixed">Guardar Salário</span>
                        <span wire:loading wire:target="saveFixed">A guardar...</span>
                    </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- FOOTER                                                           --}}
    {{-- ================================================================== --}}
    <footer class="pt-16 sm:pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-16 sm:mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Receitas e Fluxo
        </p>
    </footer>
</div>
