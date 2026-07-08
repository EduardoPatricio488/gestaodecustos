<div class="space-y-12 pb-20">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="p-5 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-[2rem] shadow-xl text-white text-center">
                <flux:icon name="clipboard-document-check" class="w-10 h-10" />
            </div>
            <div>
                <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                    Aprovação de Despesas
                </h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">
                    Validação oficial de reembolsos e gastos de equipa
                </p>
            </div>
        </div>
    </div>

    {{-- DASHBOARD DE TESOURARIA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- CARD 1 --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">
                    Total Pendente de Revisão
                </p>
                <h3 class="text-4xl font-black text-amber-500 tracking-tighter">
                    {{ number_format($stats['total_pending'], 2, ',', ' ') }}€
                </h3>
            </div>
            <flux:icon name="clock" class="size-12 text-zinc-200 dark:text-zinc-700" />
        </div>

        {{-- CARD 2 --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">
                    Aprovado este Mês
                </p>
                <h3 class="text-4xl font-black text-emerald-500 tracking-tighter">
                    {{ number_format($stats['total_approved_month'], 2, ',', ' ') }}€
                </h3>
            </div>
            <flux:icon name="check-circle" class="size-12 text-zinc-200 dark:text-zinc-700" />
        </div>

    </div>

    {{-- FILA DE TRABALHO --}}
    <div class="space-y-6">

        <h3 class="text-[11px] font-black uppercase tracking-[0.3em] text-zinc-400 flex items-center gap-3">
            <span class="size-2 rounded-full bg-amber-500 animate-pulse"></span>
            Aguardam Decisão do CEO
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            @forelse($pending as $item)
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm flex flex-col md:flex-row justify-between gap-6 hover:border-brand-500/50 transition-all">

                    {{-- INFO PRINCIPAL --}}
                    <div class="flex gap-4">
                        <div class="size-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 font-black">
                            {{ $item->user->initials() }}
                        </div>

                        <div>
                            <p class="font-black dark:text-white uppercase tracking-tight">
                                {{ $item->description }}
                            </p>

                            <p class="text-[10px] font-bold text-zinc-400 uppercase">
                                {{ $item->user->name }} · {{ $item->spent_at->format('d/m/Y') }}
                            </p>

                            {{-- TAGS --}}
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if($item->project)
                                    <span class="text-[8px] font-black bg-brand-50 dark:bg-brand-900/30 px-2 py-0.5 rounded text-brand-600 uppercase border border-brand-100 dark:border-brand-800">
                                        PRJ: {{ $item->project->name }}
                                    </span>
                                @endif

                                @if($item->task)
                                    <span class="text-[8px] font-black bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded text-amber-600 uppercase border border-amber-100 dark:border-amber-800">
                                        TRF: {{ $item->task->title }}
                                    </span>
                                @endif

                                <span class="text-[8px] font-black bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded text-zinc-500 uppercase">
                                    {{ $item->category?->name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- VALOR + AÇÕES --}}
                    <div class="flex items-center gap-4 border-t md:border-t-0 md:border-l border-zinc-100 dark:border-zinc-800 pt-4 md:pt-0 md:pl-6">

                        <span class="text-2xl font-black dark:text-white tabular-nums">
                            {{ number_format($item->amount, 2, ',', ' ') }}€
                        </span>

                        <div class="flex gap-2">
                            <flux:button wire:click="approve({{ $item->id }})"
                                         variant="primary"
                                         size="sm"
                                         class="!bg-emerald-600 rounded-xl font-black uppercase text-[9px]">
                                Aprovar
                            </flux:button>

                            <flux:button wire:click="reject({{ $item->id }})"
                                         variant="ghost"
                                         size="sm"
                                         class="text-red-500 rounded-xl font-black uppercase text-[9px]">
                                Recusar
                            </flux:button>
                        </div>

                    </div>

                </div>
            @empty

                {{-- EMPTY STATE --}}
                <div class="col-span-full py-12 text-center bg-zinc-50 dark:bg-zinc-900/50 rounded-[2.5rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                    <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                        Nenhuma despesa para aprovar.
                    </p>
                </div>

            @endforelse

        </div>
    </div>

    {{-- HISTÓRICO --}}
    @if($history->isNotEmpty())
        <div class="space-y-6 opacity-60 hover:opacity-100 transition-opacity">

            <h3 class="text-[11px] font-black uppercase tracking-[0.3em] text-zinc-400">
                Decisões Recentes
            </h3>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm">

                <flux:table>
                    <flux:table.rows>

                        @foreach($history as $item)
                            <flux:table.row class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-all">

                                {{-- STATUS --}}
                                <flux:table.cell class="pl-8 w-32">
                                    <span class="text-[8px] font-black uppercase px-2 py-1 rounded-md
                                        {{ strtolower($item->status) === 'aprovado'
                                            ? 'bg-emerald-500/10 text-emerald-600'
                                            : 'bg-red-500/10 text-red-600' }}">
                                        {{ $item->status }}
                                    </span>
                                </flux:table.cell>

                                {{-- DESCRIÇÃO --}}
                                <flux:table.cell>
                                    <p class="text-sm font-black dark:text-white uppercase">
                                        {{ $item->description }}
                                    </p>
                                    <p class="text-[9px] text-zinc-400 uppercase font-bold">
                                        {{ $item->user->name }}
                                    </p>
                                </flux:table.cell>

                                {{-- VALOR --}}
                                <flux:table.cell class="text-right pr-8">
                                    <span class="font-black dark:text-white">
                                        {{ number_format($item->amount, 2, ',', ' ') }}€
                                    </span>
                                </flux:table.cell>

                            </flux:table.row>
                        @endforeach

                    </flux:table.rows>
                </flux:table>

            </div>
        </div>
    @endif

</div>
