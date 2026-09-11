<div x-data="{ categoryModal: {{ $category ? 'false' : 'true' }}, formModal: {{ $category ? 'true' : 'false' }} }" class="min-h-[calc(100vh-8rem)]">
    {{-- Página deliberadamente vazia: o registo acontece através dos modais, como nas páginas de categorias. --}}

    {{-- MODAL 1: ESCOLHA DA CATEGORIA --}}
    <div x-show="categoryModal" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <div x-show="categoryModal" x-transition.opacity class="absolute inset-0 bg-black/70 backdrop-blur-md" x-on:click="categoryModal = false; $wire.cancel()"></div>
        <div x-show="categoryModal" x-transition class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[2rem] sm:rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl">
            <div class="flex items-center justify-between gap-4 px-6 py-5 sm:px-8 border-b border-zinc-100 dark:border-zinc-800">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-brand-500">Nova despesa</p>
                    <h1 class="mt-1 text-xl sm:text-2xl font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Escolhe uma categoria</h1>
                    <p class="mt-1 text-xs text-zinc-500">Os campos do próximo passo serão definidos pela categoria.</p>
                </div>
                <button type="button" x-on:click="categoryModal = false; $wire.cancel()" class="shrink-0 p-2.5 rounded-xl text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 dark:hover:text-white dark:hover:bg-zinc-800 transition"><flux:icon name="x-mark" class="size-5" /></button>
            </div>

            <div class="max-h-[70vh] overflow-y-auto p-6 sm:p-8">
                @if($categories->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($categories as $item)
                            <button type="button" wire:click="setCategory({{ $item->id }})" x-on:click="categoryModal = false; formModal = true" wire:loading.attr="disabled" class="group min-h-32 sm:min-h-36 flex flex-col items-center justify-center gap-3 rounded-2xl sm:rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/40 p-4 hover:-translate-y-0.5 hover:border-brand-500/50 hover:bg-brand-500/5 hover:shadow-xl transition-all focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                                <span class="flex size-14 items-center justify-center rounded-2xl text-white shadow-lg group-hover:scale-110 transition-transform" style="background: {{ $item->color ?? '#71717a' }}"><flux:icon name="{{ $item->icon ?? 'tag' }}" class="size-6" /></span>
                                <span class="text-[10px] sm:text-xs font-black uppercase tracking-tight text-zinc-800 dark:text-zinc-100 text-center">{{ $item->name }}</span>
                                @if($item->fields->isNotEmpty())<span class="text-[8px] font-bold uppercase tracking-widest text-zinc-400">{{ $item->fields->count() }} campos</span>@endif
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="py-16 text-center"><flux:icon name="tag" class="size-10 text-zinc-300 mx-auto mb-4" /><p class="text-xs font-black uppercase tracking-widest text-zinc-500">Não existem categorias disponíveis.</p></div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL 2: FORMULÁRIO DINÂMICO DA CATEGORIA --}}
    @if($category)
        <div x-show="formModal" x-cloak class="fixed inset-0 z-[71] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            <div x-show="formModal" x-transition.opacity class="absolute inset-0 bg-black/70 backdrop-blur-md" x-on:click="formModal = false; $wire.cancel()"></div>
            <div x-show="formModal" x-transition class="relative w-full max-w-3xl max-h-[92vh] overflow-hidden rounded-[2rem] sm:rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl">
                <div class="relative overflow-hidden bg-zinc-950 px-6 py-5 sm:px-8 sm:py-6">
                    <div class="absolute -right-16 -top-20 size-52 rounded-full blur-3xl opacity-20" style="background: {{ $category->color ?? '#6366f1' }}"></div>
                    <div class="relative flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex size-12 sm:size-14 shrink-0 items-center justify-center rounded-2xl shadow-xl" style="background: {{ $category->color ?? '#6366f1' }}; color: white"><flux:icon name="{{ $category->icon ?? 'tag' }}" class="size-6 sm:size-7" /></div>
                            <div class="min-w-0"><p class="text-[9px] font-black uppercase tracking-[0.25em] text-brand-400">Nova despesa · {{ $category->name }}</p><h2 class="mt-1 text-lg sm:text-xl font-black uppercase italic tracking-tight text-white truncate">Dados da despesa</h2></div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$isEditing)<button type="button" x-on:click="formModal = false; categoryModal = true" class="p-2 rounded-xl text-zinc-400 hover:text-white hover:bg-white/10 transition" title="Alterar categoria"><flux:icon name="arrow-path" class="size-5" /></button>@endif
                            <button type="button" x-on:click="formModal = false; $wire.cancel()" class="p-2 rounded-xl text-zinc-400 hover:text-white hover:bg-white/10 transition"><flux:icon name="x-mark" class="size-5" /></button>
                        </div>
                    </div>
                </div>

                <form wire:submit="save" class="max-h-[calc(92vh-110px)] overflow-y-auto p-6 sm:p-8 space-y-7">
                    {{-- CAMPOS BASE --}}
                    <section>
                        <div class="flex items-center gap-3 mb-4"><div class="p-2 rounded-lg bg-brand-500/10 text-brand-600"><flux:icon name="banknotes" class="size-4" /></div><h3 class="text-[10px] font-black uppercase tracking-[0.2em] italic text-zinc-700 dark:text-zinc-300">Informação principal</h3></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2"><flux:label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 ml-1">Valor (€) *</flux:label><flux:input wire:model="amount" type="number" step="0.01" min="0.01" placeholder="0,00" icon="currency-euro" class="h-13 rounded-2xl font-black text-lg !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />@error('amount')<p class="text-[9px] font-bold uppercase text-red-500 px-2">{{ $message }}</p>@enderror</div>
                            <div class="space-y-2"><flux:label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 ml-1">Data *</flux:label><flux:input wire:model="spent_at" type="date" class="h-13 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />@error('spent_at')<p class="text-[9px] font-bold uppercase text-red-500 px-2">{{ $message }}</p>@enderror</div>
                            <div class="sm:col-span-2 space-y-2"><flux:label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 ml-1">Descrição</flux:label><flux:input wire:model="description" placeholder="Ex.: supermercado, combustível, jantar..." class="h-13 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />@error('description')<p class="text-[9px] font-bold uppercase text-red-500 px-2">{{ $message }}</p>@enderror</div>
                        </div>
                    </section>

                    {{-- CAMPOS ESPECÍFICOS --}}
                    @if($category->fields->isNotEmpty())
                        <section class="border-t border-zinc-100 dark:border-zinc-800 pt-7">
                            <div class="flex items-center justify-between gap-3 mb-4"><div class="flex items-center gap-3"><div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-500"><flux:icon name="adjustments-horizontal" class="size-4" /></div><div><h3 class="text-[10px] font-black uppercase tracking-[0.2em] italic text-zinc-700 dark:text-zinc-300">Campos de {{ $category->name }}</h3><p class="text-[8px] text-zinc-400 uppercase tracking-widest mt-1">Configurados nesta categoria</p></div></div><span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-50 dark:bg-zinc-950 px-2.5 py-1 rounded-full border border-zinc-100 dark:border-zinc-800">{{ $category->fields->count() }} campos</span></div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($category->fields as $field)
                                    <div class="space-y-2 {{ $field->type === 'text' ? 'sm:col-span-2' : '' }}">
                                        <flux:label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 ml-1">{{ $field->label }} @if($field->required)<span class="text-red-500">*</span>@endif</flux:label>
                                        @switch($field->type)
                                            @case('select')<flux:select wire:model="fieldValues.{{ $field->key }}" class="h-13 !rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner"><option value="">Selecionar...</option>@foreach($field->options ?? [] as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach</flux:select>@break
                                            @case('number')<flux:input wire:model="fieldValues.{{ $field->key }}" type="number" step="any" placeholder="{{ $field->placeholder ?? '' }}" class="h-13 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />@break
                                            @case('date')<flux:input wire:model="fieldValues.{{ $field->key }}" type="date" class="h-13 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />@break
                                            @case('checkbox')<label class="flex h-13 items-center justify-between px-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 cursor-pointer"><span class="text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300">{{ $field->placeholder ?: 'Confirmar' }}</span><input type="checkbox" wire:model="fieldValues.{{ $field->key }}" class="size-5 rounded accent-brand-600"></label>@break
                                            @default<flux:input wire:model="fieldValues.{{ $field->key }}" type="text" placeholder="{{ $field->placeholder ?? 'Introduzir informação...' }}" class="h-13 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />
                                        @endswitch
                                        @error('fieldValues.'.$field->key)<p class="text-[9px] font-bold uppercase text-red-500 px-2">{{ $message }}</p>@enderror
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <div class="flex flex-col-reverse sm:flex-row gap-3 pt-5 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button type="button" variant="ghost" x-on:click="formModal = false; $wire.cancel()" class="flex-1 h-13 rounded-2xl font-black uppercase tracking-widest text-[9px]">Cancelar</flux:button>
                        <flux:button type="submit" variant="primary" wire:target="save" class="flex-1 h-13 rounded-2xl font-black uppercase tracking-widest text-[9px] shadow-xl shadow-brand-500/20"><span wire:loading.remove wire:target="save">{{ $isEditing ? 'Guardar alterações' : 'Registar despesa' }}</span><span wire:loading wire:target="save" class="flex items-center justify-center gap-2"><flux:icon name="arrow-path" class="size-4 animate-spin" /> A processar...</span></flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>