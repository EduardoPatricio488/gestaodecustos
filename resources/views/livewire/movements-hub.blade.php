<div class="min-h-screen space-y-6 pb-16">
    <section class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-20 size-72 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="relative flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div class="flex items-center gap-4">
                <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-lg"><flux:icon name="arrows-right-left" class="size-7" /></div>
                <div><h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white sm:text-3xl">Todos os movimentos</h1><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Receitas, despesas, assinaturas e transferências num único histórico.</p></div>
            </div>
            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 self-start rounded-xl border border-zinc-200 px-4 py-2.5 text-sm font-bold text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300"><flux:icon name="arrow-left" class="size-4" /> Dashboard</a>
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
                        @if(!str_starts_with($movement['key'], 'subscription-payment-'))
                            <div class="mt-4 flex flex-wrap justify-end gap-2">
                                <button type="button" wire:click="editMovement('{{ $movement['type'] }}', {{ $movement['record_id'] }})" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-300">
                                    <flux:icon name="pencil-square" class="size-4" /> Editar informações
                                </button>
                                @if(!empty($movement['record_url']))
                                    <a href="{{ $movement['record_url'] }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-zinc-800 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-zinc-700 dark:bg-zinc-700">
                                        <flux:icon name="arrow-top-right-on-square" class="size-4" /> Abrir módulo
                                    </a>
                                @endif
                            </div>
                        @endif

                        @if($editingId && $editingType === $movement['type'] && (int) $editingId === (int) ($movement['record_id'] ?? 0))
                            <form wire:submit="saveMovement" class="mt-4 space-y-4 rounded-2xl border border-blue-200 bg-white p-4 dark:border-blue-900 dark:bg-zinc-900">
                                <div class="flex items-center justify-between gap-3">
                                    <h4 class="text-sm font-black text-zinc-900 dark:text-white">Editar registo</h4>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Alterações guardadas na conta</span>
                                </div>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    @if($editingType === 'subscription')
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Nome<input wire:model="editingTitle" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">@error('editingTitle')<span class="text-rose-500">{{ $message }}</span>@enderror</label>
                                    @elseif($editingType === 'expense')
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Título<input wire:model="editingTitle" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                    @endif
                                    @if($editingType !== 'subscription')
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Descrição<input wire:model="editingDescription" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                    @endif
                                    <label class="space-y-1 text-xs font-bold text-zinc-500">Valor<input type="number" step="0.01" min="0.01" wire:model="editingAmount" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">@error('editingAmount')<span class="text-rose-500">{{ $message }}</span>@enderror</label>
                                    <label class="space-y-1 text-xs font-bold text-zinc-500">Data<input type="date" wire:model="editingDate" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">@error('editingDate')<span class="text-rose-500">{{ $message }}</span>@enderror</label>
                                    @if($editingType !== 'subscription')
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Moeda<input maxlength="3" wire:model="editingCurrency" class="w-full rounded-xl border-zinc-200 text-sm uppercase dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                    @endif
                                    @if(in_array($editingType, ['expense','income'], true))
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Categoria
                                            <select wire:model="editingCategoryId" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Sem categoria</option>@foreach($editCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
                                        </label>
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Conta bancária
                                            <select wire:model="editingAccountId" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Sem conta associada</option>@foreach($editAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select>
                                        </label>
                                    @endif
                                    @if($editingType === 'expense')
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Valor pago<input type="number" min="0" step="0.01" wire:model="editingAmountPaid" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">@error('editingAmountPaid')<span class="text-rose-500">{{ $message }}</span>@enderror</label>
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">IVA<input type="number" min="0" step="0.01" wire:model="editingVatAmount" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                        <label class="flex items-center gap-2 self-end rounded-xl border border-zinc-200 p-3 text-xs font-bold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300"><input type="checkbox" wire:model="editingCompanyPaid" class="rounded border-zinc-300"> Despesa da empresa</label>
                                    @elseif($editingType === 'income')
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Origem<input wire:model="editingSource" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Tipo de receita<input wire:model="editingIncomeType" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Frequência<input wire:model="editingFrequency" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                    @elseif($editingType === 'transfer')
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Direção<select wire:model="editingDirection" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="out">Saída</option><option value="in">Entrada</option></select></label>
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Categoria<input wire:model="editingTransferCategory" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Destinatário<input wire:model="editingRecipient" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Método<input wire:model="editingTransferMethod" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Estado<input wire:model="editingStatus" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                    @elseif($editingType === 'subscription')
                                        <label class="space-y-1 text-xs font-bold text-zinc-500">Periodicidade<input wire:model="editingCycle" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                                        <label class="flex items-center gap-2 self-end rounded-xl border border-zinc-200 p-3 text-xs font-bold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300"><input type="checkbox" wire:model="editingCompanyPaid" class="rounded border-zinc-300"> Paga pela empresa</label>
                                        <label class="flex items-center gap-2 self-end rounded-xl border border-zinc-200 p-3 text-xs font-bold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300"><input type="checkbox" wire:model="editingActive" class="rounded border-zinc-300"> Assinatura ativa</label>
                                    @endif
                                    @if(in_array($editingType, ['income','transfer','subscription'], true))
                                        <label class="space-y-1 text-xs font-bold text-zinc-500 sm:col-span-2">Notas<textarea wire:model="editingNotes" rows="2" class="w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></textarea></label>
                                    @endif
                                </div>
                                <div class="flex flex-wrap justify-end gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                                    <button type="button" wire:click="cancelEditMovement" class="rounded-xl border border-zinc-200 px-4 py-2.5 text-xs font-bold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">Cancelar</button>
                                    <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-700 disabled:opacity-60"><flux:icon name="check" class="size-4" /> Guardar alterações</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="px-5 py-14 text-center"><div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800"><flux:icon name="inbox" class="size-7" /></div><h3 class="mt-4 text-sm font-black text-zinc-900 dark:text-white">Não foram encontrados movimentos</h3><p class="mt-1 text-xs text-zinc-500">Experimenta alterar os filtros ou o período selecionado.</p></div>
            @endforelse
        </div>
        <p class="text-[11px] text-zinc-400">As assinaturas representam compromissos recorrentes e não são somadas novamente às despesas pagas.</p>
    </section>
</div>
