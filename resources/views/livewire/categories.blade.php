<div class="space-y-10 pb-20 p-4 lg:p-10">
    {{-- 1. HEADER DA PÁGINA --}}
    <div class="px-2">
        <h1 class="text-3xl font-black dark:text-white uppercase italic tracking-tighter leading-none">Gestão de Categorias</h1>
        <p class="text-xs text-zinc-500 font-medium uppercase tracking-widest mt-2 px-1">Personaliza e expande o teu terminal financeiro</p>
    </div>

    {{-- 2. PAINEL DE CONFIGURAÇÃO UNIFICADO --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

        {{-- COLUNA DA ESQUERDA: CAMPOS DE DADOS (5/12) --}}
        <div class="lg:col-span-5 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 bg-indigo-600/10 rounded-lg text-indigo-600">
                    <flux:icon name="plus" variant="mini" class="size-4" />
                </div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-700 dark:text-zinc-300 italic">Configurar Novo Hub</h2>
            </div>

            <form wire:submit="save" class="flex-1 flex flex-col justify-between space-y-6">
                <div class="space-y-5">
                    {{-- Nome --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Nome do Hub</flux:label>
                        <flux:input wire:model="name" placeholder="Ex: Ginásio, Pets..." class="h-14 font-bold rounded-2xl !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner" />
                        @error('name') <p class="text-red-500 text-[10px] font-bold uppercase px-2 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Orçamento --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Plafond Mensal (€)</flux:label>
                        <flux:input wire:model="budget_limit" type="number" placeholder="0.00" icon="banknotes" class="h-14 font-black !bg-zinc-50 dark:!bg-zinc-950 border-none shadow-inner text-brand-600" />
                    </div>

                    {{-- Cor --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 italic">Cor de Identificação</flux:label>
                        <div class="flex items-center gap-4 bg-zinc-50 dark:bg-zinc-950 p-2.5 rounded-2xl shadow-inner border border-zinc-100 dark:border-zinc-800/50">
                            <input type="color" wire:model.live="color" class="h-10 w-16 rounded-xl cursor-pointer border-none bg-transparent shrink-0">
                            <div class="h-3 flex-1 rounded-full border border-black/5" style="background-color: {{ $color }}"></div>
                            <span class="text-[10px] font-black uppercase text-zinc-500 pr-2">{{ $color }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <flux:button type="submit" variant="primary" class="w-full bg-indigo-600 hover:bg-indigo-700 rounded-2xl font-black uppercase tracking-[0.1em] shadow-lg shadow-indigo-500/20 h-14 transition-all hover:scale-[1.02]">
                        Ativar Categoria
                    </flux:button>
                </div>
            </form>
        </div>

        {{-- COLUNA DA DIREITA: SELETOR DE ÍCONE VISUAL (7/12) --}}
        <div class="lg:col-span-7 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm">
            <div class="flex items-center justify-between mb-8 px-1">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="swatch" variant="mini" class="size-4" />
                    </div>
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-700 dark:text-zinc-300 italic">Identidade Visual</h2>
                </div>
                <span class="text-[9px] font-black uppercase text-zinc-400 bg-zinc-50 dark:bg-zinc-950 px-3 py-1 rounded-full border border-zinc-100 dark:border-zinc-800">
                    {{ count($availableIcons) }} Opções disponíveis
                </span>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar pr-2">
                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3 p-1">
                    @foreach($availableIcons as $iconOption)
                        <button
                            type="button"
                            wire:click="$set('icon', '{{ $iconOption }}')"
                            wire:key="icon-select-{{ $iconOption }}"
                            @class([
                                'flex items-center justify-center size-12 rounded-2xl transition-all duration-200 group/icon relative',
                                'bg-indigo-600 text-white shadow-xl shadow-indigo-500/40 scale-110 z-10 ring-4 ring-white dark:ring-zinc-900' => $icon === $iconOption,
                                'bg-zinc-50 dark:bg-zinc-950 text-zinc-400 hover:text-indigo-500 border border-zinc-100 dark:border-zinc-800 hover:border-indigo-200 dark:hover:border-indigo-900' => $icon !== $iconOption
                            ])
                            title="{{ $iconOption }}"
                        >
                            @if($icon === $iconOption)
                                <div class="absolute -top-1 -right-1 size-4 bg-emerald-500 rounded-full flex items-center justify-center border-2 border-white dark:border-zinc-900">
                                    <flux:icon name="check" variant="mini" class="size-2.5 text-white" />
                                </div>
                            @endif
                            <flux:icon name="{{ $iconOption }}" class="size-6 transition-transform group-hover/icon:scale-110" />
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 3. LISTAGEM DE CATEGORIAS (COFRE) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden mt-10">
        {{-- Cabeçalho do Cofre --}}
        <div class="px-8 py-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-zinc-400 mb-1 italic">Cofre de Categorias</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter leading-none">Hubs Ativos no Sistema</p>
            </div>
            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-4 py-1 border-none shadow-sm">{{ $categories->count() }} Ativos</flux:badge>
        </div>

        {{-- Corpo do Cofre --}}
        <div class="p-4 sm:p-8 relative"> {{-- Removida a duplicação de divs aqui --}}
            {{-- Overlay de Carregamento --}}
            <div wire:loading wire:target="updateOrder" class="absolute inset-0 z-50 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-[2px] flex items-center justify-center rounded-[2rem]">
                <flux:icon name="arrow-path" class="size-8 text-indigo-500 animate-spin" />
            </div>

            {{-- Container de Arrastar (w-full para ocupar tudo) --}}
            <div
                id="sortable-list"
                x-data
                x-init="
                    new Sortable($el, {
                        animation: 150,
                        ghostClass: 'opacity-10',
                        handle: '.drag-handle',
                        onEnd: (evt) => {
                            let items = Array.from(evt.to.children).map((el, index) => {
                                return { value: el.getAttribute('data-id'), order: index + 1 }
                            });
                            $wire.updateOrder(items);
                        }
                    });
                "
                class="grid grid-cols-1 gap-4 w-full"
            >
                @forelse ($categories as $c)
                    <div
                        data-id="{{ $c->id }}"
                        wire:key="cat-row-{{ $c->id }}"
                        class="group w-full rounded-[2rem] border border-zinc-100 dark:border-zinc-800/50 bg-white dark:bg-zinc-900/50 transition-all duration-300"
                    >
                        @if ($editingId === $c->id)
    {{-- MODO EDIÇÃO INLINE EXPANDIDO --}}
    <div class="p-6 bg-zinc-50 dark:bg-zinc-950 rounded-[2rem] border border-indigo-500/30 shadow-inner space-y-6 animate-in fade-in zoom-in-95 duration-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:input wire:model="editName" label="Nome do Hub" class="h-12 rounded-xl" />
            <flux:input wire:model="editBudgetLimit" type="number" label="Plafond Mensal (€)" placeholder="0.00" class="h-12 rounded-xl font-black text-emerald-600" />
            <flux:input wire:model="editColor" type="color" label="Cor" class="h-12 rounded-xl cursor-pointer" />
        </div>

        <div class="space-y-3">
            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Alterar Ícone Identificador</flux:label>
            <div class="flex flex-wrap gap-2 p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 max-h-40 overflow-y-auto custom-scrollbar">
                @foreach($availableIcons as $iconOption)
                    <button type="button" wire:click="$set('editIcon', '{{ $iconOption }}')"
                        class="flex items-center justify-center size-10 rounded-xl border-2 transition-all
                            {{ $editIcon === $iconOption ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600' : 'border-transparent text-zinc-400 hover:bg-zinc-100' }}">
                        <flux:icon name="{{ $iconOption }}" class="size-5" />
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t border-zinc-200 dark:border-zinc-800">
            <flux:button size="sm" variant="ghost" wire:click="cancelEdit" class="rounded-xl uppercase font-black text-[10px]">Cancelar</flux:button>
            <flux:button size="sm" variant="primary" icon="check" wire:click="update" class="bg-indigo-600 rounded-xl uppercase font-black text-[10px] px-8 h-10">Guardar Alterações</flux:button>
        </div>
    </div>

                        @else
                            {{-- MODO VISUALIZAÇÃO --}}
                            <div class="flex items-center justify-between p-4 md:p-6 rounded-[1.8rem] hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all duration-300 w-full">
                                <div class="flex items-center gap-6 flex-1 min-w-0">
                                    {{-- Handle para pegar e arrastar --}}
                                    <div class="drag-handle cursor-grab active:cursor-grabbing p-2 text-zinc-300 dark:text-zinc-700 hover:text-indigo-500 transition-colors shrink-0">
                                        <flux:icon name="bars-2" variant="mini" class="size-6" />
                                    </div>

                                    {{-- Ícone --}}
                                    <span class="flex items-center justify-center size-14 rounded-2xl shadow-sm border border-black/5 dark:border-white/5 shrink-0"
                                          style="background: {{ $c->color }}1A; color: {{ $c->color }}">
                                        <flux:icon name="{{ $c->icon ?? 'tag' }}" class="size-7" />
                                    </span>

                                    {{-- Texto --}}
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="font-black uppercase tracking-tight dark:text-white text-lg truncate">{{ $c->name }}</span>
                                            @if ($c->is_fixed)
                                                <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded italic shrink-0">Sistema</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 mt-1">
                                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest truncate">
                                                {{ $c->budget_limit ? number_format($c->budget_limit, 0).'€' : 'Sem limite' }}
                                            </p>
                                            <span class="text-zinc-300 dark:text-zinc-700 shrink-0">|</span>
                                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest truncate">
                                                {{ $c->expenses_count ?? 0 }} registos este mês
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Ações --}}
                                <div class="flex items-center gap-3 shrink-0 ml-4">
                                    <flux:button size="sm" variant="ghost" icon="adjustments-horizontal" :href="route('categories.fields', $c->id)" wire:navigate class="rounded-xl text-zinc-500 hover:text-indigo-500" />
                                    <flux:button size="sm" variant="ghost" icon="pencil" wire:click="startEdit({{ $c->id }})" class="rounded-xl text-zinc-500 hover:text-indigo-500" />
                                    @unless ($c->is_fixed)
                                        <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete({{ $c->id }})" wire:confirm="Eliminar Hub '{{ $c->name }}'?" class="text-red-500 rounded-xl hover:bg-red-50" />
                                    @endunless
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-20 text-center">
                        <flux:icon name="swatch" class="size-12 text-zinc-200 mx-auto mb-4 opacity-20" />
                        <p class="text-zinc-400 font-black uppercase tracking-widest text-xs italic px-10">Nenhum Hub personalizado encontrado.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- CARREGAMENTO DA BIBLIOTECA SORTABLE --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
        .drag-handle { touch-action: none; }
    </style>
</div>
