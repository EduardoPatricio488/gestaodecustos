<div class="space-y-8 pb-24">
    {{-- ── BARRA SUPERIOR: STATUS IA E XP (DNA DO FINANCE PRO) ── --}}
    <div class="p-2 sm:p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[1.5rem] sm:rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between w-full px-2 sm:px-4 gap-4">
            {{-- LADO ESQUERDO: PROGRESSÃO --}}
            <div class="flex items-center gap-8 justify-between sm:justify-start w-full sm:w-auto">
                <div class="hidden md:block border-r border-zinc-100 dark:border-zinc-800 pr-10">
                    <div class="flex items-center gap-3 mb-1.5">
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] italic">Consola de Auditoria</span>
                        <span class="text-[9px] font-black bg-brand-600 text-white px-2.5 py-0.5 rounded-full shadow-lg">NÍVEL {{ auth()->user()->level }}</span>
                    </div>
                    <div class="w-56 h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-inner">
                        <div class="h-full bg-brand-500 shadow-[0_0_12px_rgba(59,130,246,0.5)] transition-all duration-1000"
                             style="width: {{ (auth()->user()->xp % 1000) / 10 }}%"></div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="p-3 bg-zinc-950 rounded-2xl border border-zinc-800 shadow-xl">
                        <flux:icon name="banknotes" class="size-6 text-brand-400" />
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Histórico Central</h1>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1 italic">{{ auth()->user()->currentWorkspace->name }}</p>
                    </div>
                </div>
            </div>

            {{-- LADO DIREITO: ACÇÕES --}}
            <div class="flex items-center gap-4 w-full sm:w-auto">
                 <a href="{{ route('expenses.create') }}" wire:navigate
                    class="flex items-center justify-center gap-2 w-full sm:w-auto px-6 h-12 rounded-xl sm:rounded-2xl bg-brand-600 text-white font-black uppercase text-[10px] sm:text-xs shadow-lg shadow-brand-600/20 hover:scale-[1.02] transition-all">
                    <flux:icon name="plus" class="size-4" />
                    Nova Despesa
                </a>
            </div>
        </div>
    </div>

    {{-- ── WIDGETS DE PERFORMANCE OPERACIONAL ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 px-2">
        {{-- Total Mensal --}}
        <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Volume de Saídas</p>
            <p class="text-3xl font-black text-zinc-900 dark:text-white italic tracking-tighter">{{ number_format($monthTotal, 2, ',', '.') }}€</p>
            <div class="absolute -right-2 -bottom-2 opacity-10">
                <flux:icon name="chart-bar" class="size-16" />
            </div>
        </div>

        {{-- Ticket Médio --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Média p/ Transação</p>
            <p class="text-3xl font-black text-zinc-900 dark:text-white italic tracking-tighter">
                {{ $expenses->count() > 0 ? number_format($monthTotal / $expenses->total(), 2, ',', '.') : '0,00' }}€
            </p>
            <p class="text-[8px] text-zinc-500 font-bold mt-1 uppercase tracking-tighter italic">Auditado em {{ $expenses->total() }} registos</p>
        </div>

        {{-- Origem de Capital --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Status de Liquidez</p>
            <div class="flex items-center gap-2">
                <span class="text-lg font-black text-emerald-500 italic uppercase tracking-tighter italic leading-none">Estável</span>
                <div class="size-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_#10b981]"></div>
            </div>
            <p class="text-[8px] text-zinc-500 font-bold mt-1 uppercase tracking-tighter italic">Fluxo sob controlo da IA</p>
        </div>

        {{-- Hub Mais Ativo --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Foco de Consumo</p>
            @php
                $topHub = $expenses->groupBy('category_id')->sortByDesc(fn($group) => $group->sum('amount'))->first();
            @endphp
            <p class="text-lg font-black text-brand-600 italic tracking-tighter uppercase truncate leading-none">
                {{ $topHub?->first()->category?->name ?? '---' }}
            </p>
            <p class="text-[8px] text-zinc-500 font-bold mt-1 uppercase">Hub com maior pressão financeira</p>
        </div>
    </div>

    {{-- ── LISTAGEM DE MOVIMENTOS (ESTILO AUDITORIA) ── --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden mx-2">

        {{-- FILTROS --}}
        <div class="p-6 bg-zinc-50/50 dark:bg-zinc-950/20 border-b border-zinc-100 dark:border-zinc-800 flex flex-col md:flex-row gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Pesquisar protocolo, descrição ou valor..."
                class="flex-1 !rounded-2xl border-none shadow-inner bg-zinc-100 dark:bg-zinc-800"
            />

            <div class="flex gap-2">
                <flux:select wire:model.live="filterCategory" class="w-full md:w-64" placeholder="Todos os Hubs">
                    <option value="">Todos os Hubs</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        {{-- LISTA --}}
        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
            @forelse ($expenses as $e)
                <div class="group flex flex-col sm:flex-row items-start sm:items-center gap-5 px-6 py-6 transition-all hover:bg-zinc-50/80 dark:hover:bg-brand-500/5" wire:key="expense-{{ $e->id }}">

                    {{-- DATA --}}
                    <div class="flex sm:flex-col items-center gap-2 sm:gap-0 min-w-[55px] text-center">
                        <span class="text-xl font-black text-zinc-900 dark:text-white tracking-tighter leading-none italic">{{ $e->spent_at->format('d') }}</span>
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">{{ $e->spent_at->translatedFormat('M') }}</span>
                    </div>

                    {{-- HUB --}}
                    <div class="relative">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-[1.2rem] text-sm font-black text-white shadow-lg"
                            style="background: {{ $e->category?->color ?? '#71717a' }}; box-shadow: 0 8px 15px -5px {{ $e->category?->color ?? '#71717a' }}66;">
                            <flux:icon name="{{ $e->category?->icon ?? 'tag' }}" class="size-5" />
                        </span>
                    </div>

                    {{-- DETALHES --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="truncate text-sm font-black text-zinc-900 dark:text-white uppercase tracking-tight italic">
                                {{ $e->description ?: ($e->category?->name ?? 'Protocolo de Gasto') }}
                            </p>

                            @if($e->subcategory)
                                <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-[8px] font-black text-zinc-500 uppercase tracking-widest border border-zinc-200 dark:border-zinc-700">
                                    {{ $e->subcategory }}
                                </span>
                            @endif

                            {{-- TAGS DE METADADOS INTELIGENTES --}}
                            @if($e->metadata)
                                @foreach(collect($e->metadata)->take(2) as $key => $val)
                                    @if($val && !is_array($val))
                                        <span class="text-[8px] font-bold text-zinc-400 italic">#{{ $val }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="mt-1.5 flex items-center gap-3">
                             <p class="text-[9px] font-black uppercase tracking-tight" style="color: {{ $e->category?->color }}">
                                {{ $e->category?->name ?? 'Geral' }}
                            </p>

                            {{-- CONTA BANCÁRIA --}}
                            @if($e->bankAccount)
                                <div class="flex items-center gap-1.5 opacity-60">
                                    <flux:icon name="credit-card" class="size-3 text-zinc-400" />
                                    <span class="text-[9px] font-bold text-zinc-500 uppercase">{{ $e->bankAccount->name }}</span>
                                </div>
                            @endif

                            @if($e->receipt_path)
                                <flux:icon name="paper-clip" class="size-3 text-brand-500" />
                            @endif
                        </div>
                    </div>

                    {{-- VALOR E ACÇÕES --}}
                    <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-none pt-4 sm:pt-0">
                        <div class="text-right">
                            <span class="text-xl font-black tabular-nums text-red-500 italic tracking-tight">
                                −{{ number_format($e->amount, 2, ',', ' ') }}€
                            </span>
                            @if($e->vat_amount > 0)
                                <p class="text-[8px] font-bold text-zinc-400 uppercase italic">IVA Incl: {{ number_format($e->vat_amount, 2) }}€</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 sm:opacity-0 group-hover:opacity-100 transition-all">
                            <button wire:click="edit({{ $e->id }})" class="p-2 rounded-xl text-zinc-400 hover:text-brand-500 hover:bg-brand-500/10 transition-colors">
                                <flux:icon name="pencil-square" variant="mini" class="size-4" />
                            </button>
                            <button wire:click="delete({{ $e->id }})" wire:confirm="Eliminar definitivo?" class="p-2 rounded-xl text-zinc-400 hover:text-red-500 hover:bg-red-500/10 transition-colors">
                                <flux:icon name="trash" variant="mini" class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-32 text-center">
                    <flux:icon name="magnifying-glass" class="size-12 text-zinc-200 mx-auto mb-4" />
                    <p class="text-zinc-500 font-black uppercase tracking-widest text-xs italic">Nenhum registo detetado</p>
                </div>
            @endforelse
        </div>

        <div class="p-6 bg-zinc-50/50 dark:bg-zinc-950/20 border-t border-zinc-100 dark:border-zinc-800">
            {{ $expenses->links() }}
        </div>
    </div>

    {{-- FOOTER --}}
    <footer class="pt-10 pb-20 text-center opacity-30">
        <p class="text-[8px] font-black text-zinc-400 uppercase tracking-[0.4em]">Audit Cloud Protocol • v4.0 • {{ date('Y') }}</p>
    </footer>
</div>
