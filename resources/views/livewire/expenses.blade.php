<div class="space-y-8 pb-24">
    <div class="p-2 sm:p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[1.5rem] sm:rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between w-full px-2 sm:px-4 gap-4">
            <div class="flex items-center gap-8 justify-between sm:justify-start w-full sm:w-auto">
                <div class="hidden md:block border-r border-zinc-100 dark:border-zinc-800 pr-10"><div class="flex items-center gap-3 mb-1.5"><span class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] italic">Consola de Auditoria</span><span class="text-[9px] font-black bg-brand-600 text-white px-2.5 py-0.5 rounded-full shadow-lg">NÍVEL {{ auth()->user()->level }}</span></div><div class="w-56 h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-inner"><div class="h-full bg-brand-500 transition-all duration-1000" style="width: {{ (auth()->user()->xp % 1000) / 10 }}%"></div></div></div>
                <div class="flex items-center gap-4 min-w-0"><div class="p-3 bg-zinc-950 rounded-2xl border border-zinc-800 shadow-xl shrink-0"><flux:icon name="banknotes" class="size-6 text-brand-400" /></div><div class="min-w-0"><h1 class="text-xl sm:text-2xl font-black dark:text-white uppercase tracking-tighter italic leading-none truncate">Todas as Despesas</h1><p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1 italic truncate">{{ auth()->user()->currentWorkspace->name }}</p></div></div>
            </div>
            @if($canEdit)<flux:button variant="primary" icon="plus" x-on:click="$dispatch('open-expense-category-picker')" class="shrink-0 rounded-xl font-black uppercase tracking-widest">Nova despesa</flux:button>@endif
        </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 px-2">
        <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm"><p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Volume de Saídas</p><p class="text-3xl font-black text-zinc-900 dark:text-white italic tracking-tighter">{{ number_format($monthTotal, 2, ',', '.') }}€</p><div class="absolute -right-2 -bottom-2 opacity-10"><flux:icon name="chart-bar" class="size-16" /></div></div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm"><p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Média p/ Transação</p><p class="text-3xl font-black text-zinc-900 dark:text-white italic tracking-tighter">{{ $expenses->count() > 0 ? number_format($monthTotal / $expenses->total(), 2, ',', '.') : '0,00' }}€</p><p class="text-[8px] text-zinc-500 font-bold mt-1 uppercase tracking-tighter italic">Auditado em {{ $expenses->total() }} registos</p></div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm"><p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Status de Liquidez</p><div class="flex items-center gap-2"><span class="text-lg font-black text-emerald-500 italic uppercase tracking-tighter italic leading-none">Estável</span><div class="size-2 rounded-full bg-emerald-500 animate-pulse"></div></div><p class="text-[8px] text-zinc-500 font-bold mt-1 uppercase tracking-tighter italic">Fluxo sob controlo da IA</p></div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm"><p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Foco de Consumo</p>@php $topHub = $expenses->groupBy('category_id')->sortByDesc(fn($group) => $group->sum('amount'))->first(); @endphp<p class="text-lg font-black text-brand-600 italic tracking-tighter uppercase truncate leading-none">{{ $topHub?->first()->category?->name ?? '---' }}</p><p class="text-[8px] text-zinc-500 font-bold mt-1 uppercase">Hub com maior pressão financeira</p></div>
    </div>
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden mx-2">
        <div class="p-6 bg-zinc-50/50 dark:bg-zinc-950/20 border-b border-zinc-100 dark:border-zinc-800 flex flex-col md:flex-row gap-4"><flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Pesquisar protocolo, descrição ou valor..." class="flex-1 !rounded-2xl border-none shadow-inner bg-zinc-100 dark:bg-zinc-800" /><div class="flex flex-col sm:flex-row gap-2"><flux:select wire:model.live="filterCategory" class="w-full md:w-64"><option value="">Todos os Hubs</option>@foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</flux:select></div></div>
        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
            @forelse ($expenses as $e)
                <div class="group flex flex-col sm:flex-row items-start sm:items-center gap-5 px-6 py-6 transition-all hover:bg-zinc-50/80 dark:hover:bg-brand-500/5" wire:key="expense-{{ $e->id }}"><div class="flex sm:flex-col items-center gap-2 sm:gap-0 min-w-[55px] text-center"><span class="text-xl font-black text-zinc-900 dark:text-white tracking-tighter leading-none italic">{{ $e->spent_at->format('d') }}</span><span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">{{ $e->spent_at->translatedFormat('M') }}</span></div><div class="relative"><span class="flex size-12 shrink-0 items-center justify-center rounded-[1.2rem] text-sm font-black text-white shadow-lg" style="background: {{ $e->category?->color ?? '#71717a' }};"><flux:icon name="{{ $e->category?->icon ?? 'tag' }}" class="size-5" /></span></div><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><p class="truncate text-sm font-black text-zinc-900 dark:text-white uppercase tracking-tight italic">{{ $e->description ?: ($e->category?->name ?? 'Protocolo de Gasto') }}</p>@if($e->subcategory)<span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-[8px] font-black text-zinc-500 uppercase tracking-widest border border-zinc-200 dark:border-zinc-700">{{ $e->subcategory }}</span>@endif</div><div class="mt-1.5 flex items-center gap-3"><p class="text-[9px] font-black uppercase tracking-tight" style="color: {{ $e->category?->color }}">{{ $e->category?->name ?? 'Geral' }}</p>@if($e->bankAccount)<span class="text-[9px] font-bold text-zinc-500 uppercase">{{ $e->bankAccount->name }}</span>@endif @if($e->receipt_path)<flux:icon name="paper-clip" class="size-3 text-brand-500" />@endif</div></div><div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-none pt-4 sm:pt-0"><div class="text-right"><span class="text-xl font-black tabular-nums text-red-500 italic tracking-tight">−{{ number_format($e->amount, 2, ',', ' ') }}€</span>@if($e->vat_amount > 0)<p class="text-[8px] font-bold text-zinc-400 uppercase italic">IVA Incl: {{ number_format($e->vat_amount, 2) }}€</p>@endif</div><div class="flex items-center gap-1 sm:opacity-0 group-hover:opacity-100 transition-all">@if($e->category?->slug)<a href="{{ route('hub.category', $e->category->slug) }}" wire:navigate class="p-2 rounded-xl text-zinc-400 hover:text-brand-500 hover:bg-brand-500/10 transition-colors"><flux:icon name="arrow-top-right-on-square" variant="mini" class="size-4" /></a>@endif<button wire:click="delete({{ $e->id }})" wire:confirm="Eliminar definitivo?" class="p-2 rounded-xl text-zinc-400 hover:text-red-500 hover:bg-red-500/10 transition-colors"><flux:icon name="trash" variant="mini" class="size-4" /></button></div></div></div>
            @empty<div class="py-32 text-center"><flux:icon name="magnifying-glass" class="size-12 text-zinc-200 mx-auto mb-4" /><p class="text-zinc-500 font-black uppercase tracking-widest text-xs italic">Nenhum registo detetado</p></div>@endforelse
        </div>
        <div class="p-6 bg-zinc-50/50 dark:bg-zinc-950/20 border-t border-zinc-100 dark:border-zinc-800">{{ $expenses->links() }}</div>
    </div>
    <div
        x-data="{ open: false, activeTab: 'category', scanSecondsRemaining: 45, scanTimer: null, startInvoiceTimer() { this.scanSecondsRemaining = 45; if (this.scanTimer) clearInterval(this.scanTimer); this.scanTimer = setInterval(() => { if (this.scanSecondsRemaining > 0) this.scanSecondsRemaining--; else clearInterval(this.scanTimer); }, 1000); } }"
        x-on:open-expense-category-picker.window="open = true; activeTab = 'scanner'"
        x-on:keydown.escape.window="open = false"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        aria-modal="true"
        role="dialog"
    >
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/60 backdrop-blur-sm" x-on:click="open = false"></div>

        <div x-show="open" x-transition class="relative flex max-h-[88vh] w-full max-w-2xl flex-col overflow-hidden rounded-[2rem] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl">
            <div class="border-b border-zinc-100 px-6 py-5 dark:border-zinc-800">
                <div class="min-w-0">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-brand-500">Novo movimento</p>
                    <h2 class="mt-1 text-xl font-black italic tracking-tight text-zinc-900 dark:text-white">Nova despesa</h2>
                    <p class="mt-1 text-xs font-medium text-zinc-500">Digitaliza uma fatura com IA ou escolhe manualmente a categoria.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 p-3 sm:px-6 border-b border-zinc-100 dark:border-zinc-800">
                <button type="button" x-on:click="activeTab = 'scanner'" x-bind:class="activeTab === 'scanner' ? 'bg-brand-600 text-white shadow-lg' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500'" class="flex items-center justify-center gap-2 rounded-xl px-3 py-3 text-[10px] sm:text-xs font-black uppercase tracking-wider transition-all">
                    <flux:icon name="sparkles" class="size-4" />
                    Scanner de fatura
                </button>
                <button type="button" x-on:click="activeTab = 'category'" x-bind:class="activeTab === 'category' ? 'bg-brand-600 text-white shadow-lg' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500'" class="flex items-center justify-center gap-2 rounded-xl px-3 py-3 text-[10px] sm:text-xs font-black uppercase tracking-wider transition-all">
                    <flux:icon name="tag" class="size-4" />
                    Escolher categoria
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-4 sm:p-6">
                <div x-show="activeTab === 'category'">
                    <p class="mb-4 text-xs font-medium text-zinc-500">Seleciona a categoria onde queres registar a despesa.</p>
                    @if($categories->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($categories as $category)
                                <button type="button" wire:click="selectCategory({{ $category->id }})" wire:loading.attr="disabled" x-on:click="open = false" class="group flex min-h-28 flex-col items-center justify-center gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/40 p-4 text-center transition-all hover:-translate-y-0.5 hover:border-brand-500/50 hover:shadow-lg">
                                    <span class="flex size-12 items-center justify-center rounded-2xl text-white shadow-md" style="background: {{ $category->color ?? '#71717a' }};">
                                        <flux:icon name="{{ $category->icon ?? 'tag' }}" class="size-5" />
                                    </span>
                                    <span class="text-xs font-black uppercase tracking-tight text-zinc-800 dark:text-zinc-100">{{ $category->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <h3 class="mt-4 text-base font-black text-zinc-900 dark:text-white">Sem categorias disponíveis</h3>
                            <p class="mt-1 text-sm text-zinc-500">Cria uma categoria primeiro para registar uma despesa.</p>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'scanner'" x-cloak class="space-y-5" @if($isScanningInvoice) wire:poll.5s="checkInvoiceScan" @endif>
                    @if($scannerError)
                        <div class="flex items-start gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-500">
                            <flux:icon name="exclamation-triangle" class="size-4 shrink-0" />
                            <span>{{ $scannerError }}</span>
                        </div>
                    @endif

                    <div class="rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50/70 dark:bg-zinc-950/40 p-4 sm:p-5 text-center">
                        <input type="file" wire:model="scannerReceipt" accept=".pdf,image/jpeg,image/png,image/webp" class="block w-full text-xs text-zinc-500 file:mr-3 file:rounded-full file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-[10px] file:font-black file:text-white hover:file:bg-brand-700" />
                        @if($scannerReceipt)
                            <div class="mt-4 flex flex-col items-center gap-2">
                                @if(str_starts_with($scannerReceipt->getMimeType() ?? '', 'image/'))
                                    <img src="{{ $scannerReceipt->temporaryUrl() }}" alt="Pré-visualização da fatura" class="max-h-44 rounded-xl border border-zinc-200 dark:border-zinc-700 object-contain shadow-lg" />
                                @else
                                    <div class="rounded-xl bg-red-50 px-6 py-4 text-xs font-black text-red-600 dark:bg-red-950/30">PDF carregado</div>
                                @endif
                                <span class="text-[10px] font-bold text-emerald-600">Fatura carregada com sucesso</span>
                            </div>
                        @else
                            <p class="mt-3 text-xs font-bold text-zinc-500">Seleciona uma fotografia, imagem ou PDF da fatura</p>
                            <p class="mt-1 text-[10px] text-zinc-400">PDF, JPG, PNG ou WEBP · Máximo 20 MB</p>
                        @endif
                        <div wire:loading wire:target="scannerReceipt" class="mt-3 text-xs font-bold text-brand-500">A carregar imagem...</div>
                    </div>
                    @if($isScanningInvoice)
                        <div class="rounded-xl border border-brand-500/20 bg-brand-500/5 px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <flux:icon name="arrow-path" class="size-4 animate-spin text-brand-500" />
                                <p class="text-xs font-bold text-brand-600 dark:text-brand-400">A analisar a tua fatura...</p>
                            </div>
                            <p class="mt-1 text-[11px] text-zinc-500" x-text="scanSecondsRemaining > 0 ? 'Estimativa: cerca de ' + scanSecondsRemaining + ' segundos restantes' : 'A análise está a demorar mais do que o previsto. Continua em curso...'"></p>
                            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800">
                                <div class="h-full w-full origin-left rounded-full bg-brand-500" style="animation: invoice-scan-progress 45s linear forwards;"></div>
                            </div>
                            <p class="mt-2 text-[10px] text-zinc-400">Podes aguardar — não feches esta janela.</p>
                        </div>
                    @endif
                    <div class="flex flex-col-reverse sm:flex-row gap-3">
                        <button type="button" x-on:click="open = false" class="flex-1 rounded-xl border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-xs font-black uppercase tracking-wider text-zinc-500">Cancelar</button>
                        <button type="button" wire:click="scanInvoiceAndDetectCategory" x-on:click="startInvoiceTimer()" wire:loading.attr="disabled" wire:target="scannerReceipt, scanInvoiceAndDetectCategory" @disabled(! $scannerReceipt || $isScanningInvoice) class="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-brand-600/20 transition-all hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50">
                            @if($isScanningInvoice)
                                <flux:icon name="arrow-path" class="size-4 animate-spin" /> A analisar fatura...
                            @else
                                <span wire:loading.remove wire:target="scanInvoiceAndDetectCategory" class="inline-flex items-center gap-2">
                                    <flux:icon name="sparkles" class="size-4" /> Analisar fatura
                                </span>
                                <span wire:loading.inline-flex wire:target="scanInvoiceAndDetectCategory" class="items-center gap-2">
                                    <flux:icon name="arrow-path" class="size-4 animate-spin" /> A analisar fatura...
                                </span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>