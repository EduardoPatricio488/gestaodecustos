<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center p-4 lg:p-10">
    <div class="w-full max-w-4xl">
        {{-- CABEÇALHO --}}
        <div class="mb-6 flex items-center justify-between gap-4 px-2">
            <div>
                <div class="flex items-center gap-2 text-zinc-400 text-[10px] font-black uppercase tracking-widest mb-2">
                    <a href="{{ route('expenses') }}" wire:navigate class="hover:text-brand-500 transition-colors">Despesas</a>
                    <flux:icon name="chevron-right" class="size-2" />
                    <span class="text-zinc-500 italic">{{ $isEditing ? 'Editar' : 'Nova despesa' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black dark:text-white uppercase italic tracking-tighter leading-none">
                    {{ $isEditing ? 'Editar despesa' : 'Registar despesa' }}
                </h1>
                <p class="text-xs text-zinc-500 font-medium uppercase tracking-widest mt-2">Formulário adaptado à categoria selecionada</p>
            </div>
            <flux:button wire:click="cancel" variant="ghost" icon="x-mark" class="rounded-xl font-bold uppercase text-[10px]">Fechar</flux:button>
        </div>

        {{-- MODAL PRINCIPAL --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-2xl overflow-hidden">
            {{-- IDENTIDADE DA CATEGORIA --}}
            <div class="relative overflow-hidden p-6 sm:p-8 bg-zinc-950">
                <div class="absolute -right-20 -top-20 size-56 rounded-full blur-3xl opacity-20" style="background: {{ $category?->color ?? '#6366f1' }}"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    <div class="flex items-center gap-4">
                        <div class="flex size-16 items-center justify-center rounded-2xl shadow-xl" style="background: {{ $category?->color ?? '#6366f1' }}; color: white;">
                            <flux:icon name="{{ $category?->icon ?? 'tag' }}" class="size-8" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-brand-400">Categoria ativa</p>
                            <h2 class="mt-1 text-xl sm:text-2xl font-black uppercase italic tracking-tight text-white">
                                {{ $category?->name ?? 'Selecionar categoria' }}
                            </h2>
                            @if($category)
                                <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mt-1">{{ $category->fields->count() }} atributos personalizados</p>
                            @endif
                        </div>
                    </div>

                    @if(!$isEditing)
                        <flux:button type="button" variant="ghost" icon="adjustments-horizontal" x-data x-on:click="$dispatch('change-expense-category')" class="rounded-xl text-zinc-300 hover:text-white hover:bg-white/10 uppercase text-[10px] font-black">
                            Alterar categoria
                        </flux:button>
                    @endif
                </div>
            </div>

            @if(!$category)
                {{-- ESCOLHA DE CATEGORIA --}}
                <div class="p-6 sm:p-10">
                    <div class="mb-6">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-700 dark:text-zinc-300 italic">Escolhe um Hub</h3>
                        <p class="text-xs text-zinc-500 mt-1">Os campos da despesa serão definidos automaticamente pela categoria.</p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($categories as $item)
                            <button type="button" wire:click="setCategory({{ $item->id }})" class="group min-h-28 flex flex-col items-center justify-center gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/40 p-4 hover:border-brand-500/50 hover:bg-brand-500/5 transition-all">
                                <span class="flex size-12 items-center justify-center rounded-2xl text-white shadow-md group-hover:scale-110 transition-transform" style="background: {{ $item->color ?? '#71717a' }}">
                                    <flux:icon name="{{ $item->icon ?? 'tag' }}" class="size-5" />
                                </span>
                                <span class="text-[10px] font-black uppercase tracking-tight text-zinc-800 dark:text-zinc-100">{{ $item->name }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                <form wire:submit="save" class="p-6 sm:p-10 space-y-8">
                    {{-- DADOS BASE --}}
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="p-2 bg-brand-500/10 rounded-lg text-brand-600"><flux:icon name="banknotes" class="size-4" /></div>
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-700 dark:text-zinc-300 italic">Dados da transação</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Valor (€) *</flux:label>
                                <flux:input wire:model="amount" type="number" step="0.01" min="0.01" placeholder="0,00" icon="currency-euro" class="h-14 rounded-2xl font-black text-xl !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />
                                @error('amount') <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Data *</flux:label>
                                <flux:input wire:model="spent_at" type="date" class="h-14 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />
                                @error('spent_at') <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Descrição</flux:label>
                                <flux:input wire:model="description" placeholder="Ex: Almoço de equipa, combustível, compra..." class="h-14 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />
                                @error('description') <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ATRIBUTOS DA CATEGORIA --}}
                    @if($category->fields->isNotEmpty())
                        <div class="border-t border-zinc-100 dark:border-zinc-800 pt-8">
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-500"><flux:icon name="adjustments-horizontal" class="size-4" /></div>
                                    <div>
                                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-700 dark:text-zinc-300 italic">Atributos de {{ $category->name }}</h3>
                                        <p class="text-[9px] text-zinc-400 uppercase tracking-widest mt-1">Campos definidos na arquitetura da categoria</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black uppercase text-zinc-400 bg-zinc-50 dark:bg-zinc-950 px-3 py-1 rounded-full border border-zinc-100 dark:border-zinc-800">{{ $category->fields->count() }} campos</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                @foreach($category->fields as $field)
                                    <div class="space-y-2 {{ $field->type === 'text' ? 'md:col-span-2' : '' }}">
                                        <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">
                                            {{ $field->label }} @if($field->required)<span class="text-red-500">*</span>@endif
                                        </flux:label>

                                        @switch($field->type)
                                            @case('select')
                                                <flux:select wire:model="fieldValues.{{ $field->key }}" class="h-14 !rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner">
                                                    <option value="">Selecionar...</option>
                                                    @foreach($field->options ?? [] as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </flux:select>
                                                @break
                                            @case('number')
                                                <flux:input wire:model="fieldValues.{{ $field->key }}" type="number" step="any" placeholder="{{ $field->placeholder ?? '' }}" class="h-14 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />
                                                @break
                                            @case('date')
                                                <flux:input wire:model="fieldValues.{{ $field->key }}" type="date" class="h-14 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />
                                                @break
                                            @case('checkbox')
                                                <label class="flex h-14 items-center justify-between px-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 cursor-pointer hover:border-brand-500/40 transition">
                                                    <span class="text-xs font-black uppercase text-zinc-600 dark:text-zinc-300">{{ $field->placeholder ?: 'Confirmar' }}</span>
                                                    <input type="checkbox" wire:model="fieldValues.{{ $field->key }}" class="size-5 rounded accent-brand-600">
                                                </label>
                                                @break
                                            @default
                                                <flux:input wire:model="fieldValues.{{ $field->key }}" type="text" placeholder="{{ $field->placeholder ?? 'Introduzir informação...' }}" class="h-14 rounded-2xl font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />
                                        @endswitch

                                        @error('fieldValues.'.$field->key) <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 p-6 text-center">
                            <flux:icon name="sparkles" class="size-6 text-zinc-300 mx-auto mb-2" />
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Esta categoria não tem atributos personalizados</p>
                        </div>
                    @endif

                    {{-- AÇÕES --}}
                    <div class="flex flex-col-reverse sm:flex-row gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button type="button" variant="ghost" wire:click="cancel" class="flex-1 h-14 rounded-2xl font-black uppercase tracking-widest text-[10px]">Cancelar</flux:button>
                        <flux:button type="submit" variant="primary" wire:target="save" class="flex-1 h-14 rounded-2xl bg-brand-600 hover:bg-brand-700 font-black uppercase tracking-widest text-[10px] shadow-xl shadow-brand-500/20">
                            <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Guardar alterações' : 'Registar despesa' }}</span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2"><flux:icon name="arrow-path" class="size-4 animate-spin" /> A processar...</span>
                        </flux:button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- MODAL PARA TROCAR CATEGORIA --}}
    @if(!$isEditing)
        <div x-data="{ open: false }" x-on:change-expense-category.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div x-show="open" x-transition.opacity x-on:click="open = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div x-show="open" x-transition class="relative w-full max-w-2xl max-h-[85vh] overflow-hidden rounded-[2rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl">
                <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-brand-500">Alterar Hub</p>
                        <h2 class="mt-1 text-xl font-black italic tracking-tight dark:text-white">Escolhe outra categoria</h2>
                    </div>
                    <button type="button" x-on:click="open = false" class="p-2 rounded-xl text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800"><flux:icon name="x-mark" class="size-5" /></button>
                </div>
                <div class="max-h-[65vh] overflow-y-auto p-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($categories as $item)
                        <button type="button" wire:click="setCategory({{ $item->id }})" x-on:click="open = false" class="group min-h-28 flex flex-col items-center justify-center gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/40 p-4 hover:border-brand-500/50 hover:bg-brand-500/5 transition-all">
                            <span class="flex size-12 items-center justify-center rounded-2xl text-white shadow-md group-hover:scale-110 transition-transform" style="background: {{ $item->color ?? '#71717a' }}"><flux:icon name="{{ $item->icon ?? 'tag' }}" class="size-5" /></span>
                            <span class="text-[10px] font-black uppercase tracking-tight dark:text-zinc-100">{{ $item->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
