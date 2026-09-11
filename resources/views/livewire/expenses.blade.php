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
                        <div class="h-full bg-brand-500 shadow-[0_0_12px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: {{ (auth()->user()->xp % 1000) / 10 }}%"></div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="p-3 bg-zinc-950 rounded-2xl border border-zinc-800 shadow-xl">
                        <flux:icon name="banknotes" class="size-6 text-brand-400" />
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Todas as Despesas</h1>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1 italic">{{ auth()->user()->currentWorkspace->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── WIDGETS DE PERFORMANCE OPERACIONAL ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 px-2">
        {{-- Total Mensal --}}
        <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Volume de Saídas</p>
            <p class="text-3xl font-black text-zinc-900 dark:text-white italic tracking-tighter">{{ number_format($monthTotal, 2, ',', '.') }}€</p>
            <div class="absolute -right-2 -bottom-2 opacity-10"><flux:icon name="chart-bar" class="size-16" /></div>
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

            <div class="flex flex-col sm:flex-row gap-2">
                @if($canEdit)
                    <flux:button variant="primary" icon="plus" x-on:click="$dispatch('open-expense-category-picker')">
                        Nova despesa
                    </flux:button>
                @endif

                <flux:select wire:model.live="filterCategory" class="w-full md:w-64">
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
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-[1.2rem] text-sm font-black text-white shadow-lg" style="background: {{ $e->category?->color ?? '#71717a' }}; box-shadow: 0 8px 15px -5px {{ $e->category?->color ?? '#71717a' }}66;">
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
                                <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-[8px] font-black text-zinc-500 uppercase tracking-widest border border-zinc-200 dark:border-zinc-700">{{ $e->subcategory }}</span>
                            @endif

                            @if($e->metadata)
                                @foreach(collect($e->metadata)->take(2) as $key => $val)
                                    @if($val && !is_array($val))
                                        <span class="text-[8px] font-bold text-zinc-400 italic">#{{ $val }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="mt-1.5 flex items-center gap-3">
                            <p class="text-[9px] font-black uppercase tracking-tight" style="color: {{ $e->category?->color }}">{{ $e->category?->name ?? 'Geral' }}</p>

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
                            <span class="text-xl font-black tabular-nums text-red-500 italic tracking-tight">−{{ number_format($e->amount, 2, ',', ' ') }}€</span>
                            @if($e->vat_amount > 0)
                                <p class="text-[8px] font-bold text-zinc-400 uppercase italic">IVA Incl: {{ number_format($e->vat_amount, 2) }}€</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 sm:opacity-0 group-hover:opacity-100 transition-all">
                            @if($e->category?->slug)
                                <a href="{{ route('hub.category', $e->category->slug) }}" wire:navigate class="p-2 rounded-xl text-zinc-400 hover:text-brand-500 hover:bg-brand-500/10 transition-colors" title="Ver no Hub {{ $e->category->name }}">
                                    <flux:icon name="arrow-top-right-on-square" variant="mini" class="size-4" />
                                </a>
                            @endif
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

    {{-- SELETOR DE CATEGORIA PARA NOVA DESPESA --}}
    <div x-data="{ open: false }" x-on:open-expense-category-picker.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" aria-modal="true" role="dialog">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/60 backdrop-blur-sm" x-on:click="open = false"></div>

        <div x-show="open" x-transition class="relative w-full max-w-2xl max-h-[85vh] overflow-hidden rounded-[2rem] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl">
            <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-100 dark:border-zinc-800">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-brand-500">Novo movimento</p>
                    <h2 class="mt-1 text-xl font-black italic tracking-tight text-zinc-900 dark:text-white">Escolhe uma categoria</h2>
                    <p class="mt-1 text-xs font-medium text-zinc-500">Seleciona onde queres registar a nova despesa.</p>
                </div>
                <button type="button" x-on:click="open = false" class="p-2 rounded-xl text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-700 dark:hover:text-white transition">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>

            <div class="max-h-[60vh] overflow-y-auto p-6">
                @if($categories->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($categories as $category)
                            <button type="button" wire:click="selectCategory({{ $category->id }})" wire:loading.attr="disabled" x-on:click="open = false" class="group flex min-h-28 flex-col items-center justify-center gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/40 p-4 text-center transition-all hover:-translate-y-0.5 hover:border-brand-500/50 hover:bg-brand-500/5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-brand-500/40">
                                <span class="flex size-12 items-center justify-center rounded-2xl text-white shadow-md transition-transform group-hover:scale-110" style="background: {{ $category->color ?? '#71717a' }};">
                                    <flux:icon name="{{ $category->icon ?? 'tag' }}" class="size-5" />
                                </span>
                                <span class="text-xs font-black uppercase tracking-tight text-zinc-800 dark:text-zinc-100">{{ $category->name }}</span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center">
                        <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800"><flux:icon name="tag" class="size-6 text-zinc-400" /></div>
                        <h3 class="mt-4 text-sm font-black uppercase tracking-tight text-zinc-900 dark:text-white">Sem categorias disponíveis</h3>
                        <p class="mt-1 text-xs text-zinc-500">Cria primeiro uma categoria para poderes registar despesas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <footer class="pt-10 pb-20 text-center opacity-30">
        <p class="text-[8px] font-black text-zinc-400 uppercase tracking-[0.4em]">Audit Cloud Protocol • v4.0 • {{ date('Y') }}</p>
    </footer>
</div>
