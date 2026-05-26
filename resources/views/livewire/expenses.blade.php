<div class="space-y-6">
    <x-page-header title="Histórico de Despesas" description="Consulta todos os gastos do espaço: {{ auth()->user()->currentWorkspace->name }}">
        <x-slot:actions>
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-brand-500/10 px-4 py-2 text-right border border-brand-500/20 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-widest text-brand-700 dark:text-brand-400">Total do Mês</p>
                    <p class="text-lg font-black tabular-nums text-brand-800 dark:text-brand-300">
                        {{ number_format($monthTotal, 2, ',', ' ') }} €
                    </p>
                </div>


            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- MENSAGEM DE SUCESSO --}}
   <div class="space-y-6">
    {{-- MENSAGEM DE SUCESSO E XP --}}
   @if (session('ok'))
    <div class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl shadow-lg font-bold animate-bounce">
        {{ session('ok') }}
    </div>
@endif

    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] overflow-hidden shadow-sm">
        {{-- BARRA DE FILTROS --}}
        <div class="flex flex-col gap-4 border-b border-zinc-100 p-6 sm:flex-row sm:items-center dark:border-zinc-800">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Pesquisar por descrição…"
                class="flex-1"
            />

            <flux:select wire:model.live="filterCategory" class="sm:w-64" placeholder="Filtrar por Categoria">
                <option value="">Todas as categorias</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </flux:select>
        </div>

        {{-- LISTA DE GASTOS --}}
        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
            @forelse ($expenses as $e)
                <div class="group flex items-center gap-5 px-6 py-5 transition hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40" wire:key="expense-{{ $e->id }}">

                    {{-- ÍCONE COM COR DA CATEGORIA --}}
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl text-xs font-black text-white shadow-sm"
                        style="background: {{ $e->category?->color ?? '#71717a' }}">
                        {{ strtoupper(mb_substr($e->category?->name ?? '?', 0, 1)) }}
                    </span>

                    {{-- DESCRIÇÃO E AUTOR --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="truncate font-bold text-zinc-900 dark:text-white uppercase tracking-tight text-sm">
                                {{ $e->description ?: ($e->category?->name ?? 'Despesa') }}
                            </p>

                            {{-- BADGE DO UTILIZADOR (Só aparece se a conta for partilhada) --}}
                            @if($isShared)
                                <div title="Registado por {{ $e->user->name }}" class="flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                    <div class="w-3 h-3 rounded-full bg-brand-500 flex items-center justify-center text-[6px] text-white font-black">
                                        {{ substr($e->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-[9px] font-black uppercase text-zinc-500 tracking-tighter">{{ explode(' ', $e->user->name)[0] }}</span>
                                </div>
                            @endif
                        </div>

                        <p class="mt-1 text-[11px] font-medium text-zinc-500">
                            {{ $e->spent_at->translatedFormat('d M Y') }}
                            @if ($e->category)
                                · <span class="font-bold uppercase" style="color: {{ $e->category->color }}">{{ $e->category->name }}</span>
                            @endif
                        </p>
                    </div>

                    {{-- VALOR --}}
                    <div class="text-right">
                        <span class="shrink-0 text-lg font-black tabular-nums text-red-500">
                            −{{ number_format($e->amount, 2, ',', ' ') }} €
                        </span>
                    </div>

                    {{-- ACÇÕES --}}
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:button size="xs" variant="ghost" icon="pencil" wire:click="edit({{ $e->id }})" />
                        <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $e->id }})" wire:confirm="Eliminar registo?" color="red" />
                    </div>
                </div>
            @empty
                <div class="py-20 text-center flex flex-col items-center justify-center space-y-3">
                    <flux:icon name="banknotes" class="w-12 h-12 text-zinc-200" />
                    <p class="text-zinc-500 font-medium italic">Nenhuma despesa registada com estes critérios.</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINAÇÃO --}}
        <div class="p-6 bg-zinc-50/50 dark:bg-zinc-800/10 border-t border-zinc-100 dark:border-zinc-800">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
