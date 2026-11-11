<div class="min-h-screen space-y-6 pb-16">
    <section class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-20 size-72 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="relative flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div class="flex items-center gap-4">
                <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-lg"><flux:icon name="arrows-right-left" class="size-7" /></div>
                <div><h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white sm:text-3xl">Todos os movimentos</h1><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Receitas, despesas, assinaturas e transferências num único histórico.</p></div>
            </div>
            <div class="flex flex-wrap items-center gap-2 self-start">
                @if(! $readOnly)
                <a href="{{ route('hub.import') }}" wire:navigate.hover class="group inline-flex items-center gap-2 rounded-xl border border-emerald-500 bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-600/25 transition duration-200 hover:-translate-y-0.5 hover:from-emerald-500 hover:to-teal-500 hover:shadow-xl hover:shadow-emerald-600/30 focus:outline-none focus:ring-4 focus:ring-emerald-500/30">
                    <span class="flex size-8 items-center justify-center rounded-lg bg-white/15 transition group-hover:scale-110"><flux:icon name="arrow-up-tray" class="size-4" /></span>
                    <span class="text-left"><span class="block text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-100">Importação</span><span class="block">Importar extrato</span></span>
                    <flux:icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" />
                </a>
                <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 px-4 py-2.5 text-sm font-bold text-zinc-600 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"><flux:icon name="arrow-left" class="size-4" /> Dashboard</a>
                @endif
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <article class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900"><p class="text-xs font-bold uppercase tracking-wider text-zinc-400">Movimentos encontrados</p><p class="mt-2 text-2xl font-black text-zinc-900 dark:text-white">{{ number_format($movementCount, 0, ',', '.') }}</p></article>
        <article class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/20"><p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Entradas</p><p class="mt-2 text-2xl font-black text-emerald-700 dark:text-emerald-400">{{ number_format($incomeTotal, 2, ',', '.') }} €</p></article>
        <article class="rounded-2xl border border-rose-200 bg-rose-50/60 p-4 dark:border-rose-900/50 dark:bg-rose-950/20"><p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400">Saídas e compromissos</p><p class="mt-2 text-2xl font-black text-rose-700 dark:text-rose-400">{{ number_format($expenseTotal, 2, ',', '.') }} €</p></article>
    </section>

    <section class="space-y-4 rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:p-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div><h2 class="text-lg font-black text-zinc-900 dark:text-white">Histórico completo</h2><p class="text-xs text-zinc-500">Filtra por tipo, período ou pesquisa.</p></div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <a href="{{ route('export.movements.pdf') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                    <flux:icon name="document-arrow-down" class="size-4" />
                    Exportar PDF
                </a>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:w-auto">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Pesquisar movimentos..." class="min-w-0 rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                <select wire:model.live="period" class="rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="all">Todo o período</option><option value="month">Este mês</option><option value="year">Este ano</option></select>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach([['all','Todos'],['income','Receitas'],['expense','Despesas'],['subscription','Assinaturas'],['transfer','Transferências']] as [$key,$label])
                <button type="button" wire:click="$set('type', '{{ $key }}')" class="rounded-full border px-4 py-2 text-xs font-bold transition {{ $type === $key ? 'border-blue-600 bg-blue-600 text-white' : 'border-zinc-200 bg-white text-zinc-600 hover:border-blue-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-300' }}">{{ $label }}</button>
            @endforeach
            <button type="button" wire:click="clearFilters" class="rounded-full px-3 py-2 text-xs font-bold text-zinc-400 hover:text-blue-600">Limpar filtros</button>
        </div>
        <div class="overflow-hidden rounded-2xl border border-zinc-100 dark:border-zinc-800">
            @forelse($movements as $movement)
                <article wire:key="{{ $movement['key'] }}" x-data="{ open: false }" class="border-b border-zinc-100 last:border-0 dark:border-zinc-800">
                    <div class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $movement['direction'] === 'in' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30' : ($movement['direction'] === 'company' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30' : 'bg-rose-100 text-rose-600 dark:bg-rose-900/30') }}">
                                <flux:icon name="{{ $movement['direction'] === 'company' ? 'clock' : ($movement['type'] === 'income' ? 'arrow-down-left' : ($movement['type'] === 'expense' ? 'arrow-up-right' : ($movement['type'] === 'subscription' ? 'clock' : 'arrows-right-left'))) }}" class="size-5" />
                            </div>
                            <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><p class="truncate text-sm font-bold text-zinc-900 dark:text-white">{{ $movement['title'] }}</p><span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[9px] font-black uppercase text-zinc-500 dark:bg-zinc-800">{{ $movement['type_label'] }}</span></div><p class="mt-1 truncate text-xs text-zinc-500">{{ $movement['detail'] }} · {{ $movement['date']?->format('d/m/Y') ?? '—' }}</p></div>
                        </div>
                        <div class="flex items-center justify-between gap-4 sm:justify-end">
                            <span class="text-[10px] font-semibold text-zinc-400">{{ $movement['status'] }}</span>
                            <p class="text-base font-black tabular-nums {{ $movement['direction'] === 'in' ? 'text-emerald-600' : ($movement['direction'] === 'company' ? 'text-blue-600' : 'text-rose-600') }}">{{ $movement['direction'] === 'in' ? '+' : ($movement['direction'] === 'company' ? '' : '−') }}{{ number_format($movement['amount'], 2, ',', '.') }} €</p>
                            <button type="button" @click="open = !open" :aria-expanded="open.toString()" class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl border border-zinc-200 text-zinc-500 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 dark:border-zinc-700 dark:hover:bg-blue-950/30" aria-label="Abrir detalhes do movimento" title="Ver todas as informações">
                                <flux:icon name="chevron-down" class="size-4 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                            </button>
                        </div>
                    </div>
                    <div x-cloak x-show="open" x-transition.opacity.duration.200ms class="border-t border-zinc-100 bg-zinc-50/70 px-4 py-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <div class="mb-3 flex items-center gap-2 text-xs font-black uppercase tracking-wider text-zinc-500"><flux:icon name="document-text" class="size-4" /> Informações do registo</div>
                        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach(($movement['details'] ?? []) as $detailLabel => $detailValue)
                                @if($detailValue !== null && $detailValue !== '')
                                    <div class="min-w-0 rounded-xl border border-zinc-200/80 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                                        <dt class="text-[10px] font-bold uppercase tracking-wide text-zinc-400">{{ $detailLabel }}</dt>
                                        <dd class="mt-1 break-words whitespace-pre-wrap text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $detailValue }}</dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                        <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                            @if(! $readOnly && in_array($movement['edit_type'] ?? '', ['expense', 'income', 'transfer', 'subscription'], true))
                                <button type="button" wire:click="editMovement('{{ $movement['edit_type'] }}', {{ $movement['record_id'] }})" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 px-3 py-2 text-[11px] font-bold text-zinc-600 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-blue-950/30">
                                    <flux:icon name="pencil" class="size-3.5" /> Editar informações
                                </button>
                                <button type="button" wire:click="openConversionModal('{{ $movement['edit_type'] }}', {{ $movement['record_id'] }})" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-3 py-2 text-[11px] font-bold text-white transition hover:bg-amber-700">
                                    <flux:icon name="arrow-path" class="size-3.5" /> Mudar movimento
                                </button>
                            @endif
                        @if(!empty($movement['record_url']))
                                <a href="{{ $movement['record_url'] }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-blue-700">
                                    <flux:icon name="arrow-top-right-on-square" class="size-4" />
                                    Abrir registo
                                </a>
                        @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-5 py-14 text-center"><div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800"><flux:icon name="inbox" class="size-7" /></div><h3 class="mt-4 text-sm font-black text-zinc-900 dark:text-white">Não foram encontrados movimentos</h3><p class="mt-1 text-xs text-zinc-500">Experimenta alterar os filtros ou o período selecionado.</p></div>
            @endforelse
        </div>
        <p class="text-[11px] text-zinc-400">As assinaturas representam compromissos recorrentes e não são somadas novamente às despesas pagas.</p>
    </section>

    @if(! $readOnly)
    @if($showEditModal && $editingId)
        <div class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden bg-zinc-950/70 p-2 backdrop-blur-sm sm:p-4" wire:key="movement-editor-modal" @click.self="$wire.cancelEditMovement()" x-on:keydown.escape.window="$wire.cancelEditMovement()">
            <div class="flex min-h-0 w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900" style="height: min(760px, calc(100dvh - 32px)); max-height: calc(100dvh - 32px); min-height: 0; display: flex; flex-direction: column;" @click.stop>
                <div class="flex shrink-0 items-start justify-between gap-3 border-b border-zinc-100 px-4 py-4 dark:border-zinc-800 sm:px-6 sm:py-5">
                    <div><h2 class="text-lg font-black text-zinc-900 dark:text-white">Editar movimento</h2><p class="mt-1 text-xs text-zinc-500">Altera os campos e guarda as informações do registo.</p></div>
                    <button type="button" wire:click="cancelEditMovement" class="rounded-lg p-2 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-label="Fechar"><flux:icon name="x-mark" class="size-5" /></button>
                </div>
                <div class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto overscroll-y-contain px-4 pb-5 sm:px-6" style="height: 0; min-height: 0; flex: 1 1 0%; overflow-y: auto; overflow-x: hidden; overscroll-behavior: contain; scrollbar-width: thin; -webkit-overflow-scrolling: touch;" x-init="$el.scrollTop = 0">
                <form wire:submit="saveMovement" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label class="text-xs font-bold text-zinc-500">Nome / título<input wire:model="editingTitle" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"></label>
                        <label class="text-xs font-bold text-zinc-500">Descrição<input wire:model="editingDescription" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"></label>
                        <label class="text-xs font-bold text-zinc-500">Valor<input type="number" min="0.01" step="0.01" wire:model="editingAmount" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"></label>
                        <label class="text-xs font-bold text-zinc-500">Data<input type="date" wire:model="editingDate" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"></label>
                        <label class="text-xs font-bold text-zinc-500">Moeda<input maxlength="3" wire:model="editingCurrency" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm uppercase shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"></label>
                        <label class="text-xs font-bold text-zinc-500">Estado<div class="mt-1.5 flex h-[42px] w-full items-center rounded-xl border-2 border-emerald-200 bg-emerald-50 px-3 text-sm font-bold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300"><flux:icon name="check-circle" class="mr-2 size-4" />Completo</div></label>
                        @if($editingType === 'expense')
                            <label class="text-xs font-bold text-zinc-500">Em falta (€)<input type="number" min="0" step="0.01" wire:model="editingOutstandingAmount" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"><span class="mt-1 block text-[10px] font-normal text-zinc-400">O valor pago será recalculado automaticamente.</span>@error('editingOutstandingAmount')<span class="mt-1 block text-[10px] text-rose-600">{{ $message }}</span>@enderror</label>
                            <label class="text-xs font-bold text-zinc-500">IVA (€)<input type="number" min="0" step="0.01" wire:model="editingVatAmount" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white">@error('editingVatAmount')<span class="mt-1 block text-[10px] text-rose-600">{{ $message }}</span>@enderror</label>
                            <label class="text-xs font-bold text-zinc-500 sm:col-span-2">Informações de importação<textarea rows="4" wire:model="editingMetadata" placeholder="Banco: Santander&#10;Número da importação: 7&#10;Tipo de ficheiro de origem: PDF" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"></textarea><span class="mt-1 block text-[10px] font-normal text-zinc-400">Informações apresentadas em texto simples. Deixa vazio para remover os dados de importação.</span>@error('editingMetadata')<span class="mt-1 block text-[10px] text-rose-600">{{ $message }}</span>@enderror</label>
                        @endif
                        @if(in_array($editingType, ['expense','income','subscription']))
                            <label class="text-xs font-bold text-zinc-500">Categoria<select wire:model="editingCategoryId" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"><option value="">Sem categoria</option>@foreach($editCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label>
                        @endif
                        @if(in_array($editingType, ['expense','income']))
                            <label class="text-xs font-bold text-zinc-500">Conta bancária<select wire:model="editingAccountId" class="mt-1.5 w-full rounded-xl border-2 border-zinc-300 bg-white px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-600 dark:bg-zinc-950 dark:text-white"><option value="">Sem conta</option>@foreach($editAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></label>
                        @endif
                    </div>
                    <div class="flex flex-wrap justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        <button type="button" wire:click="cancelEditMovement" class="rounded-xl border border-zinc-200 px-4 py-2.5 text-xs font-bold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">Cancelar</button>
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-700" wire:loading.attr="disabled" wire:target="saveMovement">Guardar alterações</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    @endif

    @if($showConversionModal)
        @php
            $movementTypeLabels = ['expense' => 'Despesa', 'income' => 'Receita', 'transfer' => 'Transferência', 'subscription' => 'Assinatura'];
            $currentMovementLabel = $movementTypeLabels[$editingType] ?? 'Movimento';
            $selectedMovementLabel = $movementTypeLabels[$conversionType] ?? 'Novo movimento';
        @endphp
        <div class="fixed inset-0 z-[120] flex items-center justify-center overflow-hidden overscroll-none p-3 sm:p-4" wire:key="movement-conversion-modal" wire:click.self="closeConversionModal" x-on:keydown.escape.window="$wire.closeConversionModal()">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeConversionModal"></div>
            <div class="relative flex h-[calc(100dvh-1.5rem)] max-h-[calc(100dvh-1.5rem)] min-h-0 w-full max-w-3xl flex-col overflow-hidden rounded-[2rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl" wire:click.stop>
                <div class="sticky top-0 z-30 flex shrink-0 flex-col gap-4 p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-amber-600">Conversão de movimento</p>
                            <h2 class="mt-1 text-xl font-black text-zinc-900 dark:text-white">Mudar tipo de movimento</h2>
                            <p class="mt-1 text-xs text-zinc-500">O formulário adapta-se ao tipo escolhido e reaproveita os dados compatíveis.</p>
                        </div>
                        <button type="button" wire:click="closeConversionModal" class="size-10 shrink-0 rounded-xl flex items-center justify-center text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-label="Fechar"><flux:icon name="x-mark" class="size-5" /></button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-900/50 dark:bg-blue-950/20">
                            <p class="text-[9px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-300">Tipo atual</p>
                            <p class="mt-1 text-sm font-black text-blue-900 dark:text-blue-100">{{ $currentMovementLabel }}</p>
                        </div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                            Novo tipo de movimento
                            <select wire:model.live="conversionType" class="mt-1.5 w-full rounded-xl border-2 border-zinc-200 bg-white px-3 py-3 text-sm font-bold text-zinc-900 shadow-sm transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                                <option value="">Selecionar tipo...</option>
                                <option value="expense">Despesa</option>
                                <option value="income">Receita</option>
                                <option value="transfer">Transferência</option>
                                <option value="subscription">Assinatura</option>
                            </select>
                        </label>
                    </div>
                    @error('conversionType')<p class="text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                </div>

                @if($conversionType === 'expense')
                    <form wire:submit="convertMovement" class="max-h-[calc(94vh-190px)] overflow-y-auto p-6 sm:p-8 space-y-7">
                        <div class="relative overflow-hidden rounded-[1.5rem] bg-zinc-950 px-5 py-5 text-white">
                            <div class="absolute -right-12 -top-16 size-44 rounded-full bg-rose-500/20 blur-3xl"></div>
                            <div class="relative flex items-center gap-4">
                                <div class="flex size-12 items-center justify-center rounded-2xl bg-rose-600 text-white shadow-xl"><flux:icon name="banknotes" class="size-6" /></div>
                                <div><p class="text-[9px] font-black uppercase tracking-[0.25em] text-rose-300">Nova despesa</p><h3 class="mt-1 text-lg font-black uppercase italic tracking-tight">Dados da despesa</h3></div>
                            </div>
                        </div>
                        <section>
                            <div class="mb-4 flex items-center gap-3"><div class="rounded-lg bg-brand-500/10 p-2 text-brand-600"><flux:icon name="banknotes" class="size-4" /></div><h4 class="text-[10px] font-black uppercase tracking-[0.2em] italic text-zinc-700 dark:text-zinc-300">Informação principal</h4></div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <label class="space-y-2 text-[9px] font-black uppercase tracking-widest text-zinc-400">Valor (€) *
                                    <input wire:model="editingAmount" type="number" min="0.01" step="0.01" class="h-13 w-full rounded-2xl border-0 bg-zinc-50 text-lg font-black text-zinc-900 shadow-inner dark:bg-zinc-950 dark:text-white">
                                    @error('editingAmount')<span class="text-[9px] text-rose-500">{{ $message }}</span>@enderror
                                </label>
                                <label class="space-y-2 text-[9px] font-black uppercase tracking-widest text-zinc-400">Data *
                                    <input wire:model="editingDate" type="date" class="h-13 w-full rounded-2xl border-0 bg-zinc-50 font-bold text-zinc-900 shadow-inner dark:bg-zinc-950 dark:text-white">
                                    @error('editingDate')<span class="text-[9px] text-rose-500">{{ $message }}</span>@enderror
                                </label>
                                <label class="space-y-2 text-[9px] font-black uppercase tracking-widest text-zinc-400 sm:col-span-2">Descrição
                                    <input wire:model="editingDescription" placeholder="Ex.: supermercado, combustível, jantar..." class="h-13 w-full rounded-2xl border-0 bg-zinc-50 font-bold text-zinc-900 shadow-inner dark:bg-zinc-950 dark:text-white">
                                    @error('editingDescription')<span class="text-[9px] text-rose-500">{{ $message }}</span>@enderror
                                </label>
                                <label class="space-y-2 text-[9px] font-black uppercase tracking-widest text-zinc-400">Categoria *
                                    <select wire:model.live="editingCategoryId" class="h-13 w-full rounded-2xl border-0 bg-zinc-50 font-bold text-zinc-900 shadow-inner dark:bg-zinc-950 dark:text-white">
                                        <option value="">Selecionar categoria...</option>
                                        @foreach($editCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                                    </select>
                                    @error('editingCategoryId')<span class="text-[9px] text-rose-500">{{ $message }}</span>@enderror
                                </label>
                            </div>
                        </section>
                        @if($conversionExpenseCategory && $conversionExpenseCategory->fields->isNotEmpty())
                            <section class="border-t border-zinc-100 pt-7 dark:border-zinc-800">
                                <div class="mb-4 flex items-center gap-3"><div class="rounded-lg bg-indigo-500/10 p-2 text-indigo-500"><flux:icon name="adjustments-horizontal" class="size-4" /></div><div><h4 class="text-[10px] font-black uppercase tracking-[0.2em] italic text-zinc-700 dark:text-zinc-300">Campos de {{ $conversionExpenseCategory->name }}</h4><p class="mt-1 text-[8px] uppercase tracking-widest text-zinc-400">Campos específicos desta categoria</p></div></div>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    @foreach($conversionExpenseCategory->fields as $field)
                                        <div class="space-y-2 {{ $field->type === 'text' ? 'sm:col-span-2' : '' }}">
                                            <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400">{{ $field->label }} @if($field->required)<span class="text-rose-500">*</span>@endif</label>
                                            @switch($field->type)
                                                @case('select')
                                                    <select wire:model="conversionFieldValues.{{ $field->key }}" class="h-13 w-full rounded-2xl border-0 bg-zinc-50 font-bold text-zinc-900 shadow-inner dark:bg-zinc-950 dark:text-white"><option value="">Selecionar...</option>@foreach($field->options ?? [] as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach</select>
                                                    @break
                                                @case('number')<input wire:model="conversionFieldValues.{{ $field->key }}" type="number" step="any" placeholder="{{ $field->placeholder ?? '' }}" class="h-13 w-full rounded-2xl border-0 bg-zinc-50 font-bold shadow-inner dark:bg-zinc-950 dark:text-white">@break
                                                @case('date')<input wire:model="conversionFieldValues.{{ $field->key }}" type="date" class="h-13 w-full rounded-2xl border-0 bg-zinc-50 font-bold shadow-inner dark:bg-zinc-950 dark:text-white">@break
                                                @case('checkbox')<label class="flex h-13 items-center justify-between rounded-2xl border border-zinc-200 bg-zinc-50 px-5 dark:border-zinc-800 dark:bg-zinc-950"><span class="text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300">{{ $field->placeholder ?: 'Confirmar' }}</span><input type="checkbox" wire:model="conversionFieldValues.{{ $field->key }}" class="size-5 rounded accent-brand-600"></label>@break
                                                @default<input wire:model="conversionFieldValues.{{ $field->key }}" type="text" placeholder="{{ $field->placeholder ?? 'Introduzir informação...' }}" class="h-13 w-full rounded-2xl border-0 bg-zinc-50 font-bold shadow-inner dark:bg-zinc-950 dark:text-white">
                                            @endswitch
                                            @error('conversionFieldValues.'.$field->key)<span class="text-[9px] text-rose-500">{{ $message }}</span>@enderror
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endif
                        <div class="flex flex-col-reverse gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800 sm:flex-row">
                            <button type="button" wire:click="closeConversionModal" class="h-13 flex-1 rounded-2xl font-black uppercase tracking-widest text-[9px] text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800">Cancelar</button>
                            <button type="submit" wire:confirm="Tens a certeza? O movimento atual será substituído por uma despesa." wire:loading.attr="disabled" class="h-13 flex-1 rounded-2xl bg-rose-600 text-[9px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-rose-700">Converter em despesa</button>
                        </div>
                    </form>
                @elseif($conversionType === 'income')
                    <form wire:submit="convertMovement" class="max-h-[calc(94vh-190px)] overflow-y-auto p-6 sm:p-8 space-y-6" autocomplete="off">
                        <div class="flex items-center gap-4 border-b border-zinc-100 pb-5 dark:border-zinc-800">
                            <div class="rounded-2xl bg-emerald-600 p-3 text-white shadow-lg"><flux:icon name="sparkles" class="size-6" /></div>
                            <div><p class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Nova receita</p><h3 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white">Receita Extra</h3><p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Regista uma entrada ou serviço extra</p></div>
                        </div>
                        <label class="relative block"><span class="absolute -top-2.5 left-4 z-10 bg-white px-2 text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:bg-zinc-900">Descrição</span><input wire:model="editingDescription" placeholder="Ex.: Serviço, venda..." class="h-14 w-full rounded-2xl border-2 border-zinc-100 bg-zinc-50 px-5 text-sm font-bold text-zinc-900 focus:border-emerald-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">@error('editingDescription')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="relative block"><span class="absolute -top-2.5 left-4 z-10 bg-white px-2 text-[10px] font-black uppercase text-emerald-600 dark:bg-zinc-900">Valor ({{ strtoupper($editingCurrency) }})</span><input wire:model="editingAmount" type="number" min="0.01" step="0.01" class="h-14 w-full rounded-2xl border-2 border-zinc-100 bg-zinc-50 px-5 text-lg font-black text-zinc-900 focus:border-emerald-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">@error('editingAmount')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="relative block"><span class="absolute -top-2.5 left-4 z-10 bg-white px-2 text-[10px] font-black uppercase text-zinc-400 dark:bg-zinc-900">Data</span><input wire:model="editingDate" type="date" class="h-14 w-full rounded-2xl border-2 border-zinc-100 bg-zinc-50 px-5 text-sm font-bold text-zinc-900 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">@error('editingDate')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                        </div>
                        <label class="relative block"><span class="absolute -top-2.5 left-4 z-10 bg-white px-2 text-[10px] font-black uppercase tracking-widest text-amber-600 dark:bg-zinc-900">Imposto estimado (%)</span><input wire:model="editingIncomeTaxEstimate" type="number" min="0" max="100" step="0.1" placeholder="Ex.: 25" class="h-14 w-full rounded-2xl border-2 border-zinc-100 bg-zinc-50 px-5 text-sm font-bold text-zinc-900 focus:border-amber-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">@error('editingIncomeTaxEstimate')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                        <label class="relative block"><span class="absolute -top-2.5 left-4 z-10 bg-white px-2 text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:bg-zinc-900">Notas (opcional)</span><textarea wire:model="editingNotes" rows="2" placeholder="Observações, cliente, referência..." class="w-full rounded-2xl border-2 border-zinc-100 bg-zinc-50 px-5 py-4 text-sm font-medium text-zinc-900 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"></textarea></label>
                        <label class="relative block"><span class="absolute -top-2.5 left-4 z-10 bg-white px-2 text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:bg-zinc-900">Onde entra este valor?</span><select wire:model.live="editingIncomeDestination" class="h-14 w-full rounded-2xl border-2 border-zinc-100 bg-zinc-50 px-5 text-sm font-bold text-zinc-900 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"><option value="cash">Dinheiro físico</option>@foreach($conversionIncomeAccounts as $account)<option value="bank:{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></label>
                        <div class="flex flex-col-reverse gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800 sm:flex-row"><button type="button" wire:click="closeConversionModal" class="h-14 flex-1 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50">Cancelar</button><button type="submit" wire:confirm="Tens a certeza? O movimento atual será substituído por uma receita." wire:loading.attr="disabled" class="h-14 flex-[2] rounded-2xl bg-emerald-600 text-xs font-black uppercase tracking-widest text-white shadow-xl hover:bg-emerald-700">Converter em receita</button></div>
                    </form>
                @elseif($conversionType === 'transfer')
                    <form wire:submit="convertMovement" class="max-h-[calc(94vh-190px)] overflow-y-auto p-5 sm:p-6 space-y-4">
                        <div class="flex items-center gap-3 border-b border-zinc-100 pb-4 dark:border-zinc-800"><div class="rounded-xl bg-blue-600 p-3 text-white"><flux:icon name="arrows-right-left" class="size-5" /></div><div><p class="text-[9px] font-black uppercase tracking-widest text-blue-600">Nova transferência</p><h3 class="text-sm font-black text-zinc-900 dark:text-white">Registar Transferência</h3></div></div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Conta origem *<select wire:model="editingFromAccountId" class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"><option value="">— Selecionar —</option>@foreach($editAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select>@error('editingFromAccountId')<span class="mt-1 block text-[10px] text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Conta destino *<select wire:model="editingToAccountId" class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"><option value="">— Selecionar —</option>@foreach($editAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select>@error('editingToAccountId')<span class="mt-1 block text-[10px] text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Valor *<input wire:model="editingAmount" type="number" min="0.01" step="0.01" class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">@error('editingAmount')<span class="mt-1 block text-[10px] text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Data *<input wire:model="editingDate" type="date" class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">@error('editingDate')<span class="mt-1 block text-[10px] text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 sm:col-span-2">Descrição<input wire:model="editingDescription" placeholder="Ex.: Transferência para poupança" class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></label>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 sm:col-span-2">Estado<select wire:model="editingTransferStatus" class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"><option value="completed">Concluída</option><option value="pending">Pendente</option><option value="failed">Falhada</option></select>@error('editingTransferStatus')<span class="mt-1 block text-[10px] text-rose-500">{{ $message }}</span>@enderror</label>
                        </div>
                        <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800"><button type="button" wire:click="closeConversionModal" class="rounded-xl border border-zinc-200 px-4 py-2.5 text-xs font-bold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">Cancelar</button><button type="submit" wire:confirm="Tens a certeza? O movimento atual será substituído por uma transferência." wire:loading.attr="disabled" class="rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-black text-white hover:bg-blue-700">Converter em transferência</button></div>
                    </form>
                @elseif($conversionType === 'subscription')
                    <form wire:submit="convertMovement" class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto overscroll-contain p-4 sm:p-5 space-y-4" style="scrollbar-width: thin; -webkit-overflow-scrolling: touch;">
                        <div class="flex items-center gap-4 border-b border-white/10 pb-5"><div class="rounded-2xl bg-brand-600/30 p-3 text-brand-300 shadow"><flux:icon name="credit-card" class="size-5" /></div><div><p class="text-[9px] font-black uppercase tracking-widest text-brand-300">Nova assinatura</p><h3 class="text-xl font-black uppercase italic tracking-tight text-zinc-900 dark:text-white">Configuração completa de custo recorrente</h3></div></div>
                        <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Nome da assinatura</span><input wire:model="editingTitle" type="text" placeholder="Ex.: Netflix, Spotify, Renda..." class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-bold text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white">@error('editingTitle')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Valor</span><input wire:model="editingAmount" type="number" min="0.01" step="0.01" class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-lg font-black text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white">@error('editingAmount')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Dia de débito</span><input wire:model="editingSubscriptionBillingDay" type="number" min="1" max="31" class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-black text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white">@error('editingSubscriptionBillingDay')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Categoria</span><select wire:model="editingCategoryId" class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-bold text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white"><option value="">Selecionar tipo de serviço...</option>@foreach($editCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>@error('editingCategoryId')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Ciclo</span><select wire:model="editingCycle" class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-bold text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white"><option value="monthly">Mensal</option><option value="quarterly">Trimestral</option><option value="semiannual">Semestral</option><option value="annual">Anual</option></select>@error('editingCycle')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Pagamento</span><select wire:model="editingSubscriptionPaymentMethod" class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-bold text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white"><option value="card">Cartão</option><option value="direct_debit">Débito direto</option><option value="bank_transfer">Transferência</option><option value="paypal">PayPal</option><option value="cash">Numerário</option></select>@error('editingSubscriptionPaymentMethod')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Estado</span><select wire:model="editingSubscriptionStatus" class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-bold text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white"><option value="active">Ativa</option><option value="paused">Pausada</option><option value="cancelled">Cancelada</option></select>@error('editingSubscriptionStatus')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Início</span><input wire:model="editingSubscriptionStartedAt" type="date" class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-bold text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white">@error('editingSubscriptionStartedAt')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                            <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Renovação</span><input wire:model="editingSubscriptionRenewalDate" type="date" class="h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-bold text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white">@error('editingSubscriptionRenewalDate')<span class="text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                        </div>
                        <label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Notas</span><textarea wire:model="editingNotes" rows="3" placeholder="Plano, contrato, promoção..." class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm font-bold text-zinc-900 dark:border-white/10 dark:bg-zinc-900/40 dark:text-white"></textarea></label>
                        <div class="space-y-3 rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-white/10 dark:bg-zinc-900/40"><label class="flex items-center justify-between gap-4"><span><span class="block text-[10px] font-black uppercase tracking-widest text-brand-500">Lembrete de cobrança</span><span class="mt-1 block text-xs text-zinc-500">Avisar antes da renovação ou débito.</span></span><input type="checkbox" wire:model="editingNotifyBeforeBilling" class="rounded border-zinc-300 text-brand-500 focus:ring-brand-500"></label><label class="block space-y-2"><span class="text-[10px] font-black uppercase tracking-widest text-brand-500">Dias antes</span><input wire:model="editingNotifyDaysBefore" type="number" min="1" max="30" class="h-12 w-full rounded-2xl border border-zinc-200 bg-white px-4 text-sm font-black text-zinc-900 dark:border-white/10 dark:bg-zinc-950 dark:text-white"></label></div>
                        <div class="flex flex-col-reverse gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800 sm:flex-row"><button type="button" wire:click="closeConversionModal" class="h-14 flex-1 rounded-2xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:bg-zinc-50">Cancelar</button><button type="submit" wire:confirm="Tens a certeza? O movimento atual será substituído por uma assinatura." wire:loading.attr="disabled" class="h-14 flex-[2] rounded-2xl bg-brand-600 text-xs font-black uppercase tracking-widest text-white shadow-xl hover:bg-brand-500">Converter em assinatura</button></div>
                    </form>
                @else
                    <div class="p-8 text-center"><flux:icon name="arrow-path" class="mx-auto size-8 text-zinc-300" /><p class="mt-3 text-sm font-bold text-zinc-500">Seleciona o novo tipo para carregar o formulário correspondente.</p></div>
                @endif
            </div>
        </div>
    @endif
    @endif
</div>
