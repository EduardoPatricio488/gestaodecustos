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

            {{-- Botão salário fixo --}}

<flux:button
    @click="$dispatch('modal-show-salario')"
    variant="primary"
    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
    <flux:icon name="calendar-days" class="size-4" />
    Configurar Salário
</flux:button>

<div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

{{-- Botão receita extra --}}
<flux:button
    wire:click="openExtraModal"
    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20"
>
    Receita Extra
</flux:button>






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
{{-- CARDS DE ESTATÍSTICAS · ULTRA PREMIUM                           --}}
{{-- ================================================================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    {{-- MÉDIA MENSAL --}}
    <div class="relative p-6 rounded-[2rem] bg-white/40 dark:bg-zinc-900/40
                backdrop-blur-xl border border-white/20 shadow-xl overflow-hidden group">

        <div class="absolute inset-0 opacity-10">
            <flux:icon name="chart-bar" class="size-32 text-emerald-500/20 rotate-12" />
        </div>

        <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500 mb-2">
            Média Mensal
        </p>

        <p class="text-4xl font-black text-emerald-600 italic tracking-tight">
            {{ number_format($avgMonthly, 0, ',', ' ') }}€
        </p>

        <p class="text-[10px] text-zinc-400 font-medium mt-1">
            Últimos 6 meses
        </p>
    </div>
{{-- ENTRADAS EXTRAS --}}
<div class="relative p-6 rounded-[2rem] bg-white/40 dark:bg-zinc-900/40
            backdrop-blur-xl border border-white/20 shadow-xl overflow-hidden group">

    <div class="absolute inset-0 opacity-10">
        <flux:icon name="bolt" class="size-32 text-yellow-500/20 rotate-12" />
    </div>

    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500 mb-2">
        Entradas Extras
    </p>

    <p class="text-4xl font-black text-yellow-600 italic tracking-tight">
        {{ number_format($totalExtras, 0, ',', ' ') }}€
    </p>

    <p class="text-[10px] text-zinc-400 font-medium mt-1">
        Total de Receitas Variáveis
    </p>
</div>



    {{-- TOTAL ANUAL (SÓ FIXOS) --}}
    @php
        $totalYear = $fixedIncomes
            ->where('created_at', '>=', now()->startOfYear())
            ->sum('amount');
    @endphp

    <div class="relative p-6 rounded-[2rem] bg-white/40 dark:bg-zinc-900/40
                backdrop-blur-xl border border-white/20 shadow-xl overflow-hidden group">

        <div class="absolute inset-0 opacity-10">
            <flux:icon name="banknotes" class="size-32 text-amber-500/20 rotate-12" />
        </div>

        <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500 mb-2">
            Total {{ now()->year }} (Fixos)
        </p>

        <p class="text-4xl font-black text-amber-600 italic tracking-tight">
            {{ number_format($totalYear, 0, ',', ' ') }}€
        </p>

        <p class="text-[10px] text-zinc-400 font-medium mt-1">
            Rendimentos Fixos acumulados
        </p>
    </div>

    {{-- MELHOR FONTE (6 CATEGORIAS REAIS) --}}
    @php
        $sourceNames = [
            'independente' => 'Trabalho Independente / Recibos Verdes',
            'investimentos' => 'Investimentos / Dividendos',
            'imobiliario' => 'Rendas e Exploração Imobiliária',
            'reforma' => 'Reforma / Pensão de Velhice',
            'bolsa' => 'Bolsa de Estudo / Apoio à Formação',
            'outro' => 'Outra Fonte',
        ];

        $sourceEmojis = [
            'independente' => '💼',
            'investimentos' => '📈',
            'imobiliario' => '🏠',
            'reforma' => '🧓',
            'bolsa' => '🎓',
            'outro' => '✨',
        ];

        // Agrupar rendimentos fixos por fonte e somar
        $bestSourceKey = $fixedIncomes
            ->groupBy('source')
            ->map(fn($items) => $items->sum('amount'))
            ->sortDesc()
            ->keys()
            ->first();

        $bestSourceValue = $fixedIncomes
            ->where('source', $bestSourceKey)
            ->sum('amount');
    @endphp

    <div class="relative p-6 rounded-[2rem] bg-white/40 dark:bg-zinc-900/40
                backdrop-blur-xl border border-white/20 shadow-xl overflow-hidden group">

        <div class="absolute inset-0 opacity-10">
            <flux:icon name="trophy" class="size-32 text-emerald-500/20 rotate-12" />
        </div>

        <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500 mb-2">
            Melhor Fonte
        </p>

        <p class="text-4xl font-black text-emerald-600 italic tracking-tight flex items-center gap-2">
            {{ $sourceEmojis[$bestSourceKey] ?? '✨' }}
            {{ number_format($bestSourceValue, 0, ',', ' ') }}€
        </p>

        <p class="text-[10px] text-zinc-400 font-medium mt-1">
            {{ $sourceNames[$bestSourceKey] ?? 'N/A' }}
        </p>
    </div>

</div>






































<h2 class="text-2xl font-black dark:text-white uppercase italic tracking-tighter">Todos os Rendimentos</h2>






    {{-- ================================================================== --}}
    {{-- GRELHA FIXOS + EXTRAS · RESPONSIVA                               --}}
    {{-- ================================================================== --}}
   <div class="flex flex-col gap-12 w-full">



















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
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-0 group transition-all hover:border-emerald-500/40 shadow-sm relative overflow-hidden w-full">

        {{-- Linha de Cor --}}
        <div class="h-1.5 w-full {{ $fixed->source === 'emprego' ? 'bg-indigo-500' : ($fixed->source === 'imobiliario' ? 'bg-blue-500' : 'bg-emerald-500') }}"></div>

        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                {{-- Esquerda --}}
                <div class="flex items-start gap-5 flex-1">
                    <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 shadow-inner shrink-0">
                        <span class="text-[7px] font-black text-zinc-400 uppercase leading-none mb-1">DIA</span>
                        <span class="text-xl font-black text-zinc-800 dark:text-white">{{ sprintf('%02d', $fixed->day_of_month) }}</span>
                    </div>

                    <div>
                        <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                            {{ $fixed->description }}
                           @if($fixed->source === 'emprego')
            <span class="text-[9px] bg-indigo-500/10 text-indigo-500 px-2 py-0.5 rounded-lg border border-indigo-500/20 font-black tracking-widest">
                CONTRATO
            </span>
        @elseif($fixed->source === 'imobiliario')
            <span class="text-[9px] bg-blue-500/10 text-blue-500 px-2 py-0.5 rounded-lg border border-blue-500/20 font-black tracking-widest">
                RENDAS
            </span>
        @endif
                        </p>
                        <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest mt-1.5">
                            Frequência: {{ ucfirst($fixed->frequency ?? 'mensal') }}
                            @if($fixed->notes) · <span class="italic text-zinc-400">"{{ $fixed->notes }}"</span> @endif
                        </p>
                    </div>
                </div>

                {{-- Direita --}}
                <div class="text-right">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 italic">Disponível Líquido</p>
                    <span class="text-3xl font-black text-emerald-600 tracking-tighter italic">
                        {{ number_format($fixed->amount, 2, ',', ' ') }}€
                    </span>
                </div>
            </div>

            {{-- ── DETALHES DE CONTRATO (SE FOR EMPREGO) ── --}}
            @if($fixed->source === 'emprego' && $fixed->metadata)
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-zinc-100 dark:border-zinc-800 animate-in fade-in">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Salário Bruto</p>
                        <p class="text-xs font-black dark:text-white">{{ number_format($fixed->metadata['salary_gross'] ?? 0, 2, ',', ' ') }}€</p>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <p class="text-[8px] font-black text-red-400 uppercase mb-1">Descontos Est.</p>
                        @php
                            $ss = ($fixed->metadata['salary_gross'] ?? 0) * 0.11;
                            $irs = ($fixed->amount - ($fixed->metadata['calculated_sa'] ?? 0)) - (($fixed->metadata['salary_gross'] ?? 0) - $ss);
                        @endphp
                        <p class="text-xs font-black text-red-500">-{{ number_format(abs($ss + $irs), 2, ',', ' ') }}€</p>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <p class="text-[8px] font-black text-emerald-500 uppercase mb-1">Subs. Almoço</p>
                        <p class="text-xs font-black text-emerald-600">+{{ number_format($fixed->metadata['calculated_sa'] ?? 0, 2, ',', ' ') }}€</p>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button wire:click="openRaiseModal({{ $fixed->id }})" class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all">
                            <flux:icon name="rocket-launch" variant="micro" class="size-4" />
                        </button>
                        <flux:button wire:click="editFixed({{ $fixed->id }})" variant="ghost" icon="pencil-square" size="xs" />
                        <flux:button wire:click="deleteFixed({{ $fixed->id }})" wire:confirm="Apagar?" variant="ghost" icon="trash" size="xs" color="red" />
                    </div>
                </div>
            @else
                {{-- Outras fontes: Botões normais --}}
                <div class="mt-4 flex items-center justify-end gap-2">
                    <button wire:click="openRaiseModal({{ $fixed->id }})" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all font-black text-[9px] uppercase shadow-sm">
                        <flux:icon name="rocket-launch" variant="micro" class="size-3" /> Aumento
                    </button>
                    <flux:button wire:click="editFixed({{ $fixed->id }})" variant="ghost" icon="pencil-square" size="xs" />
                    <flux:button wire:click="deleteFixed({{ $fixed->id }})" variant="ghost" icon="trash" size="xs" color="red" />
                </div>
            @endif
        </div>
    </div>
@empty
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <flux:icon name="clock" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest">Sem salários configurados</p>
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
            @click="$dispatch('modal-show-emprego')"
            class="flex items-center gap-2 px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg"
        >
            <flux:icon name="plus" variant="micro" class="size-3" />
            Novo Contrato
        </button>
    </div>

    <div class="space-y-4">
        @php
            $employmentIncomes = $fixedIncomes->where('source', 'emprego');
        @endphp

        @forelse($employmentIncomes as $job)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] p-6 group transition-all hover:border-indigo-500/40 shadow-sm relative overflow-hidden w-full">

                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="flex items-start gap-5 flex-1">
                        {{-- Icon/Dia --}}
                        <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950 border border-indigo-100 dark:border-indigo-900 shadow-inner shrink-0">
                            <span class="text-[7px] font-black text-indigo-400 uppercase leading-none mb-1">RECEB.</span>
                            <span class="text-xl font-black text-indigo-600">{{ sprintf('%02d', $job->day_of_month) }}</span>
                        </div>

                        <div>
                            <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                {{ $job->description }}
                                <span class="text-[9px] bg-indigo-500/10 text-indigo-500 px-2 py-0.5 rounded-lg border border-indigo-500/20 font-black">CONTRATO</span>
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
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Disponível Real (Líquido)</p>
                        <span class="text-3xl font-black text-emerald-600 tracking-tighter italic">
                            {{ number_format($job->amount, 2, ',', ' ') }}€
                        </span>
                    </div>
                </div>

                {{-- BREAKDOWN BRUTO VS LÍQUIDO --}}
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Salário Bruto</p>
                        <p class="text-xs font-black dark:text-white">{{ number_format($job->metadata['salary_gross'] ?? 0, 2, ',', ' ') }}€</p>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <p class="text-[8px] font-black text-red-400 uppercase mb-1">Segurança Social (11%)</p>
                        <p class="text-xs font-black text-red-500">-{{ number_format(($job->metadata['salary_gross'] ?? 0) * 0.11, 2, ',', ' ') }}€</p>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Subs. Alimentação</p>
                        <p class="text-xs font-black text-emerald-600">+{{ number_format($job->metadata['calculated_sa'] ?? 0, 2, ',', ' ') }}€</p>
                    </div>

                   <div class="flex items-center justify-end gap-2">


    <button wire:click="openRaiseModal({{ $fixed->id }})"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all font-black text-[9px] uppercase shadow-sm">
                            <flux:icon name="rocket-launch" variant="micro" class="size-3" />
                            Aumento
                        </button>                 {{-- Chamar o método editFixed que agora deteta o tipo e abre o modal certo --}}
    <flux:button
        wire:click="editFixed({{ $job->id }})"
        variant="ghost"
        icon="pencil-square"
        size="xs"
    />

    <flux:button
        wire:click="deleteFixed({{ $job->id }})"
        wire:confirm="Apagar contrato?"
        variant="ghost"
        icon="trash"
        size="xs"
        color="red"
    />
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
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveEmployment" class="flex flex-col">
                {{-- HEADER --}}
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="briefcase" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white">Configurar Salário</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Cálculo Automático de Impostos</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                {{-- BODY --}}
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar">

                    {{-- DESCRIÇÃO --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-indigo-600 z-10">Descrição do Vínculo</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Salário Mensal - Empresa X" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-indigo-500 transition-all">
                    </div>

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

                    {{-- SUBSIDIO ALIMENTAÇÃO --}}
                    <div class="p-6 bg-amber-50 dark:bg-amber-900/10 rounded-3xl border border-amber-100 dark:border-amber-900/30">
                        <p class="text-[10px] font-black uppercase text-amber-600 mb-4 ml-1 tracking-widest">Subsídio de Alimentação</p>
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
                    </div>

                    {{-- QUADRO DE AUDITORIA (IDENTICO AO ONBOARDING) --}}
                    @if($recSalaryGross > 0)
                    <div class="p-6 bg-zinc-950 text-white rounded-[2rem] border border-white/10 shadow-2xl space-y-4 animate-in fade-in zoom-in-95">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 italic">Audit de Descontos</h3>
                            <span class="text-[9px] font-black bg-emerald-500/10 px-2 py-0.5 rounded text-emerald-400 uppercase">Calculado por IA</span>
                        </div>
                        <div class="space-y-2 font-mono text-[11px]">
                            <div class="flex justify-between text-zinc-400">
                                <span>SEGURANÇA SOCIAL (11%)</span>
                                <span class="font-bold text-red-400">- {{ number_format($calculatedSS, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>RETENÇÃO IRS (ESTIMADA)</span>
                                <span class="font-bold text-red-400">- {{ number_format($calculatedIRS, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>TOTAL SUBSÍDIOS</span>
                                <span class="font-bold text-emerald-400">+ {{ number_format($calculatedSA, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="pt-3 border-t border-dashed border-white/10 flex justify-between items-center">
                                <span class="text-zinc-500 font-black uppercase text-[9px]">Líquido Final:</span>
                                <span class="text-lg font-black text-white italic">{{ number_format($recAmount, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- DIA DE PAGAMENTO --}}
                    <div class="relative w-40">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                        <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-indigo-500/20 transition-all">
                        Confirmar Contrato 💼
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>









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
                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-sky-50 dark:bg-zinc-950 border border-sky-100 dark:border-sky-900/50 shadow-inner shrink-0">
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
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex items-start gap-4 flex-1">
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
                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                                    Frequência: {{ ucfirst($fixed->frequency ?? 'mensal') }}
                                </p>
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

                    {{-- DIA DE RECOLHA --}}
                    <div class="relative w-40">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia da Cobrança</label>
                        <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
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
                        </div>
                        <div class="relative">
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

                    <div class="relative w-40">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                        <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
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

                    <div class="relative w-40">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                        <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
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
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Registo de Pensão de Velhice ou Invalidez</p>
                    </div>
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

                    <div class="relative w-40">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                        <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
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

                    <div class="relative w-40">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                        <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
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


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL TÁTICO: ATUALIZAÇÃO DE AUMENTO (PREMIUM) --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@if($showRaiseModal)
@php
    $raiseMeta = [
        'emprego'      => ['emoji' => '🚀', 'title' => 'Upgrade de Salário!', 'subtitle' => 'Parabéns pelo teu progresso financeiro', 'noun' => 'Extra', 'totalQ' => 'Qual o novo valor líquido do salário?', 'addQ' => 'Quanto vais receber a mais no final do mês?', 'confirm' => 'Confirmar Upgrade', 'bounce' => true],
        'imobiliario'  => ['emoji' => '📈', 'title' => 'Atualizar Valor da Renda', 'subtitle' => 'Ajuste estratégico de rendimentos imobiliários', 'noun' => 'Aumento', 'totalQ' => 'Qual o novo valor da renda mensal?', 'addQ' => 'Qual o valor do aumento da renda?', 'confirm' => 'Confirmar Ajuste', 'bounce' => false],
        'freelance'    => ['emoji' => '💻', 'title' => 'Atualizar Rendimento Freelance', 'subtitle' => 'Ajuste do valor líquido dos recibos verdes', 'noun' => 'Aumento', 'totalQ' => 'Qual o novo valor líquido?', 'addQ' => 'Qual o valor a somar ao rendimento?', 'confirm' => 'Confirmar Ajuste', 'bounce' => false],
        'investimento' => ['emoji' => '📊', 'title' => 'Atualizar Rendimento de Investimento', 'subtitle' => 'Ajuste de dividendos, juros ou mais-valias', 'noun' => 'Aumento', 'totalQ' => 'Qual o novo valor líquido?', 'addQ' => 'Qual o valor a somar ao rendimento?', 'confirm' => 'Confirmar Ajuste', 'bounce' => false],
        'reforma'      => ['emoji' => '👴', 'title' => 'Atualizar Pensão', 'subtitle' => 'Ajuste do valor líquido da reforma/pensão', 'noun' => 'Aumento', 'totalQ' => 'Qual o novo valor líquido?', 'addQ' => 'Qual o valor a somar à pensão?', 'confirm' => 'Confirmar Ajuste', 'bounce' => false],
        'bolsa'        => ['emoji' => '🎓', 'title' => 'Atualizar Bolsa', 'subtitle' => 'Ajuste do valor da bolsa de estudo', 'noun' => 'Aumento', 'totalQ' => 'Qual o novo valor da bolsa?', 'addQ' => 'Qual o valor a somar à bolsa?', 'confirm' => 'Confirmar Ajuste', 'bounce' => false],
        'outro'        => ['emoji' => '✨', 'title' => 'Atualizar Rendimento', 'subtitle' => 'Ajuste do valor desta fonte de rendimento', 'noun' => 'Aumento', 'totalQ' => 'Qual o novo valor?', 'addQ' => 'Qual o valor a somar?', 'confirm' => 'Confirmar Ajuste', 'bounce' => false],
    ];
    $rm = $raiseMeta[$raiseSourceType] ?? $raiseMeta['emprego'];
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

    {{-- BACKDROP PREMIUM --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.120ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/80"
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

           <form wire:submit.prevent="applyRaise" class="flex max-h-[86vh] flex-col" autocomplete="off">

                {{-- HEADER DINÂMICO (Contexto Rendas vs Salário) --}}
                <div class="text-center mb-8 pt-8 px-6">
                    <div class="size-20 bg-emerald-500/20 rounded-[2rem] flex items-center justify-center mx-auto mb-4 border border-emerald-500/30">
                        <span class="text-4xl {{ $rm['bounce'] ? 'animate-bounce' : '' }}">
                            {{ $rm['emoji'] }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-black uppercase italic tracking-tighter text-white leading-none">
                        {{ $rm['title'] }}
                    </h2>

                    <p class="text-[10px] text-emerald-300 font-bold uppercase tracking-widest mt-2 px-10">
                        {{ $rm['subtitle'] }}
                    </p>
                </div>

                {{-- CORPO --}}
                <div class="space-y-6 px-6 pb-8 text-left">

                    {{-- Selector de Modo --}}
                    <div class="flex bg-white/10 dark:bg-zinc-800/40 p-1 rounded-2xl border border-white/10 backdrop-blur-sm">
                        <button type="button" wire:click="$set('raiseMode', 'total')"
                            class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl transition-all
                            {{ $raiseMode === 'total'
                                ? 'bg-white/20 dark:bg-zinc-600 text-emerald-400 shadow-md'
                                : 'text-zinc-400' }}">
                            Definir Novo Total
                        </button>

                        <button type="button" wire:click="$set('raiseMode', 'addition')"
                            class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl transition-all
                            {{ $raiseMode === 'addition'
                                ? 'bg-white/20 dark:bg-zinc-600 text-emerald-400 shadow-md'
                                : 'text-zinc-400' }}">
                            Somar {{ $rm['noun'] }}
                        </button>
                    </div>

                    {{-- Input com Label Dinâmica --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-emerald-300 ml-1">
                            {{ $raiseMode === 'total' ? $rm['totalQ'] : $rm['addQ'] }}
                        </label>

                        <div class="relative">
                            <input
                                type="number"
                                step="0.01"
                                wire:model="raiseValue"
                                class="w-full h-20 bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-[1.5rem]
                                       px-8 text-3xl font-black text-emerald-400 shadow-inner backdrop-blur-sm
                                       focus:ring-4 focus:ring-emerald-500/20 transition-all text-center"
                                placeholder="0,00"
                                autofocus
                            >
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-emerald-300 font-black text-xl">€</span>
                        </div>
                    </div>

                    {{-- BOTÕES --}}
                    <div class="flex gap-4 pt-2">
                         <button type="button" wire:click="closeRaiseModal"
                            @click="close()"
                            class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px]
                                   text-zinc-400 hover:text-white transition-colors">
                            Cancelar
                        </button>

                        <button type="submit"
                            class="flex-[2] h-14 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase
                                   text-[11px] tracking-widest shadow-xl shadow-emerald-500/30 transition-all
                                   hover:scale-[1.02] active:scale-95">
                            {{ $rm['confirm'] }} 💪
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endif






















<br>
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














    </div>@if($showExtraModal)

<div
    x-data="{
        open: false,
        show() {
            requestAnimationFrame(() => {
                this.open = true;
                document.documentElement.classList.add('overflow-hidden');
            });
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        }
    }"
    x-on:modal-show-receita-extra.window="show()"
    x-on:modal-close-receita-extra.window="close()"
    x-on:keydown.escape.window="close()"
>

    {{-- BACKDROP — instantâneo e suave --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.60ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/70 backdrop-blur-md will-change-opacity will-change-transform"
    ></div>

    {{-- WRAPPER --}}
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
    >

        {{-- PAINEL — animação POP ultra fluida --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-90 transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.92] translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-70 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.92] translate-y-2"
            class="relative z-10 w-full max-w-2xl rounded-[2rem] shadow-xl overflow-hidden
                   bg-emerald-500/10 backdrop-blur-xl border border-emerald-500/20
                   will-change-transform will-change-opacity"
            @click.stop
        >

            <form wire:submit.prevent="saveExtra" class="flex max-h-[86vh] flex-col" autocomplete="off">

                {{-- HEADER --}}
                <div class="shrink-0 p-6 pb-4 flex items-center gap-4 border-b border-white/10 bg-white/10 backdrop-blur-sm">
                    <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-md shadow-emerald-500/20
                                transition-transform duration-150 group-hover:scale-105">
                        <flux:icon name="sparkles" class="size-5" />
                    </div>

                    <div class="flex-1 min-w-0">
                        <h2 class="font-black uppercase italic tracking-tight leading-none text-white">
                            Receita Extra
                        </h2>
                        <p class="text-[10px] text-emerald-300 font-black uppercase tracking-widest mt-1.5 italic">
                            Regista uma entrada pontual ou recorrente
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="close(); $wire.closeExtraModal()"
                         class="rounded-full p-2 hover:bg-white/10 text-zinc-300 hover:text-white transition-all">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                {{-- BODY — animações internas + scroll suave --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6
                            transition-all duration-150 ease-out will-change-scroll">

                    {{-- Descrição --}}
                    <div class="relative transition-all duration-150 ease-out">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                       text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Origem do Ganho
                        </label>
                        <input
                            type="text"
                            wire:model="description"
                            placeholder="Ex: Freelance, Venda, Bónus..."
                            class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                   text-sm font-bold text-white placeholder-white/40 outline-none
                                   transition-all duration-150 ease-out
                                   focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                        >
                    </div>

                    {{-- Valor + Data --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Valor ({{ strtoupper($currency) }})
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="amount"
                                placeholder="0,00"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-lg font-black text-emerald-400 placeholder-white/40 outline-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                        </div>

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Moeda
                            </label>
                            <select
                                wire:model="currency"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none appearance-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                                @foreach($currencyOptions as $code => $label)
                                    <option value="{{ $code }}">{{ $code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Data
                            </label>
                            <input
                                type="date"
                                wire:model="received_at"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                        </div>

                    </div>

                    {{-- Fonte + Frequência --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Fonte
                            </label>
                            <select
                                wire:model="source"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none appearance-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                                <option value="emprego">💼 Emprego</option>
                                <option value="freelance">💻 Freelance</option>
                                <option value="investimento">📈 Investimento</option>
                                <option value="outro">✨ Outro</option>
                            </select>
                        </div>

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Frequência
                            </label>
                            <select
                                wire:model="frequency"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none appearance-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                                <option value="pontual">📌 Pontual</option>
                                <option value="semanal">📅 Semanal</option>
                                <option value="mensal">🔁 Mensal</option>
                                <option value="anual">📆 Anual</option>
                            </select>
                        </div>

                    </div>

                    {{-- Imposto estimado --}}
                    <div class="relative transition-all duration-150 ease-out">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                       text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
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
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white placeholder-white/40 outline-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-amber-500/40 focus:bg-white/20"
                            >
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-emerald-300 font-black text-sm">%</span>
                        </div>

                        @if($tax_estimate && $amount)
                            <p class="text-[10px] text-amber-400 font-bold mt-1.5 transition-all duration-150 ease-out">
                                Imposto estimado: ~{{ number_format($amount * $tax_estimate / 100, 2, ',', '.') }} {{ strtoupper($currency) }}
                                · Líquido: ~{{ number_format($amount - ($amount * $tax_estimate / 100), 2, ',', '.') }} {{ strtoupper($currency) }}
                            </p>
                        @endif
                    </div>

                    {{-- Notas --}}
                    <div class="relative transition-all duration-150 ease-out">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                       text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Notas (opcional)
                        </label>
                        <textarea
                            wire:model="notes"
                            rows="2"
                            placeholder="Observações, cliente, referência..."
                            class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                   text-sm font-medium text-white placeholder-white/40 resize-none outline-none
                                   transition-all duration-150 ease-out
                                   focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                        ></textarea>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="shrink-0 p-6 pt-4 flex flex-col sm:flex-row gap-3 border-t border-white/10 bg-white/10 backdrop-blur-sm">
                    <button
                        type="button"
                        @click="close(); $wire.closeExtraModal()"
                        class="w-full h-14 rounded-2xl text-zinc-300 hover:text-white hover:bg-white/10
                               font-bold uppercase text-xs tracking-widest transition-all duration-150 active:scale-95"
                    >
                        Cancelar
                    </button>

                    <button
                        wire:click="saveExtra"
                        @click="close()"
                        wire:loading.attr="disabled"
                        wire:target="saveExtra"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20
                               transition-all duration-150 active:scale-95 disabled:opacity-60"
                    >
                        Confirmar Ganho
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
<div class="relative mb-8 text-left">
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
