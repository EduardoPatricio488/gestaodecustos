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
                        <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                            @if(in_array($movement['edit_type'] ?? '', ['expense', 'income', 'transfer', 'subscription'], true))
                                <button type="button" wire:click="editMovement('{{ $movement['edit_type'] }}', {{ $movement['record_id'] }})" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 px-3 py-2 text-[11px] font-bold text-zinc-600 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-blue-950/30">
                                    <flux:icon name="pencil" class="size-3.5" /> Editar
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

    @if($editingId)
        <div class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-zinc-950/60 p-4" wire:key="movement-editor-modal">
            <div class="my-6 w-full max-w-3xl rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900 sm:p-6" @click.stop>
                <div class="mb-5 flex items-start justify-between gap-3">
                    <div><h2 class="text-lg font-black text-zinc-900 dark:text-white">Editar movimento</h2><p class="mt-1 text-xs text-zinc-500">Altera os campos e guarda. Também podes converter o tipo de movimento.</p></div>
                    <button type="button" wire:click="cancelEditMovement" class="rounded-lg p-2 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-label="Fechar"><flux:icon name="x-mark" class="size-5" /></button>
                </div>
                <form wire:submit="saveMovement" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @if(in_array($editingType, ['expense','subscription']))
                            <label class="text-xs font-bold text-zinc-500">Nome / título<input wire:model="editingTitle" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                        @endif
                        <label class="text-xs font-bold text-zinc-500">Descrição<input wire:model="editingDescription" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                        <label class="text-xs font-bold text-zinc-500">Valor<input type="number" min="0.01" step="0.01" wire:model="editingAmount" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                        <label class="text-xs font-bold text-zinc-500">Data<input type="date" wire:model="editingDate" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                        <label class="text-xs font-bold text-zinc-500">Moeda<input maxlength="3" wire:model="editingCurrency" class="mt-1 w-full rounded-xl border-zinc-200 text-sm uppercase dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                        @if(in_array($editingType, ['expense','income','subscription']))
                            <label class="text-xs font-bold text-zinc-500">Categoria<select wire:model="editingCategoryId" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Sem categoria</option>@foreach($editCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label>
                        @endif
                        @if(in_array($editingType, ['expense','income']))
                            <label class="text-xs font-bold text-zinc-500">Conta bancária<select wire:model="editingAccountId" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Sem conta</option>@foreach($editAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></label>
                        @endif
                        @if($editingType === 'transfer')
                            <label class="text-xs font-bold text-zinc-500">Direção<select wire:model="editingDirection" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="out">Saída</option><option value="in">Entrada</option></select></label>
                            <label class="text-xs font-bold text-zinc-500">Destinatário<input wire:model="editingRecipient" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                        @endif
                        @if($editingType === 'subscription')
                            <label class="text-xs font-bold text-zinc-500">Periodicidade<select wire:model="editingCycle" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="monthly">Mensal</option><option value="yearly">Anual</option><option value="weekly">Semanal</option><option value="quarterly">Trimestral</option></select></label>
                        @endif
                        @if($editingType === 'income')
                            <label class="text-xs font-bold text-zinc-500">Origem<input wire:model="editingSource" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                            <label class="text-xs font-bold text-zinc-500">Tipo de receita<input wire:model="editingIncomeType" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></label>
                        @endif
                    </div>
                    <div class="flex flex-wrap justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        <button type="button" wire:click="cancelEditMovement" class="rounded-xl border border-zinc-200 px-4 py-2.5 text-xs font-bold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">Cancelar</button>
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-700" wire:loading.attr="disabled" wire:target="saveMovement">Guardar alterações</button>
                    </div>
                </form>
                @if(in_array($editingType, ['expense','income','transfer','subscription'], true))
                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50/70 p-4 dark:border-amber-900/50 dark:bg-amber-950/20">
                        <h3 class="text-xs font-black uppercase tracking-wide text-amber-800 dark:text-amber-300">Alterar tipo de movimento</h3>
                        <p class="mt-1 text-xs text-amber-800/80 dark:text-amber-200/80">O movimento atual será substituído pelo novo tipo; não ficarão dois registos.</p>
                        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <label class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Novo tipo<select wire:model="conversionType" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="expense">Despesa</option><option value="income">Receita</option><option value="transfer">Transferência</option><option value="subscription">Assinatura</option></select></label>
                            @if(in_array($conversionType, ['expense','income'], true))
                                <label class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Categoria (opcional)<select wire:model="editingCategoryId" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Sem categoria</option>@foreach($editCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label>
                                <label class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Conta bancária (opcional)<select wire:model="editingAccountId" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Sem conta</option>@foreach($editAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></label>
                            @endif
                            @if($conversionType === 'transfer')
                                <label class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Conta de origem<select wire:model="editingFromAccountId" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Selecionar conta</option>@foreach($editAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></label>
                                <label class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Conta de destino<select wire:model="editingToAccountId" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Selecionar conta</option>@foreach($editAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></label>
                            @endif
                            @if($conversionType === 'subscription')
                                <label class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Categoria da assinatura<select wire:model="editingCategoryId" class="mt-1 w-full rounded-xl border-zinc-200 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"><option value="">Selecionar categoria</option>@foreach($editCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label>
                            @endif
                        </div>
                        @error('conversionType')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        @error('editingFromAccountId')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        @error('editingToAccountId')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        @error('editingCategoryId')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        <div class="mt-3 flex justify-end"><button type="button" wire:click="convertMovement" wire:confirm="Tens a certeza? O registo atual será apagado e substituído pelo novo tipo." wire:loading.attr="disabled" wire:target="convertMovement" class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-black text-white hover:bg-amber-700">Converter movimento</button></div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
