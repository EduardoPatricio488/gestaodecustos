<div class="space-y-10 pb-20 p-4 lg:p-10">

    {{-- 1. HEADER COM BREADCRUMB --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
        <div>
            <div class="flex items-center gap-2 text-zinc-400 text-[10px] font-black uppercase tracking-widest mb-2">
                <a href="{{ route('categories') }}" wire:navigate class="hover:text-brand-500 transition-colors">Cofre de Categorias</a>
                <flux:icon name="chevron-right" class="size-2" />
                <span class="text-zinc-500 italic">{{ $category->name }}</span>
            </div>
            <h1 class="text-4xl font-black dark:text-white uppercase italic tracking-tighter leading-none">Arquitetura do Hub</h1>
            <p class="text-xs text-zinc-500 font-medium uppercase tracking-widest mt-2">Personaliza os atributos de registo para este módulo</p>
        </div>
        <flux:button :href="route('categories')" variant="ghost" icon="arrow-left" wire:navigate class="rounded-xl font-bold uppercase text-[10px]">Voltar ao Painel</flux:button>
    </div>

    @if (session('ok'))
        <flux:callout color="green" icon="check-circle" class="shadow-lg border-emerald-500/20 animate-in fade-in zoom-in duration-300">
            {{ session('ok') }}
        </flux:callout>
    @endif

    <div class="grid gap-10 lg:grid-cols-12 items-start">

        {{-- COLUNA ESQUERDA: CONFIGURADOR (5/12) --}}
        <div class="lg:col-span-5 space-y-8">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden">
                <div class="absolute -top-10 -right-10 size-40 bg-brand-500/5 blur-3xl rounded-full"></div>

                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-700 dark:text-zinc-300 italic mb-8 flex items-center gap-3">
                    <div class="p-2 bg-brand-500/10 rounded-lg text-brand-600"><flux:icon name="plus" class="size-4" /></div>
                    Novo Atributo Inteligente
                </h2>

                <form wire:submit="saveField" class="space-y-6 relative z-10">
                    {{-- Nome do Campo --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Identificação</flux:label>
                        <flux:input wire:model="label" placeholder="Ex: Quilometragem, Entidade, Local..." class="h-14 font-bold rounded-2xl shadow-inner border-none !bg-zinc-50 dark:!bg-zinc-950 focus:ring-2 focus:ring-brand-500/20" />
                        @error('label') <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Tipo de Dado --}}
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Tipo de Dado</flux:label>
                            <flux:select wire:model.live="type" class="h-14 !rounded-2xl font-bold border-none !bg-zinc-50 dark:!bg-zinc-950 shadow-inner">
                                @foreach($types as $val => $display) <option value="{{ $val }}">{{ $display }}</option> @endforeach
                            </flux:select>
                        </div>
                        {{-- Placeholder --}}
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Dica Visual</flux:label>
                            <flux:input wire:model="placeholder" placeholder="Ex: Digite aqui..." class="h-14 rounded-2xl border-none !bg-zinc-50 dark:!bg-zinc-950 shadow-inner" />
                        </div>
                    </div>

                    {{-- Opções (Só aparece se for Select) --}}
                    @if ($type === 'select')
                        <div class="space-y-2 animate-in fade-in slide-in-from-top-2 duration-300">
                            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Opções da Lista</flux:label>
                            <flux:textarea wire:model="optionsRaw" placeholder="Separadas por vírgula (ex: Urgente, Normal, Baixa)" rows="2" class="!rounded-2xl italic shadow-inner border-none !bg-zinc-50 dark:!bg-zinc-950" />
                            <p class="text-[9px] text-zinc-500 mt-1 font-bold uppercase px-2 italic">O sistema criará o menu de escolha automaticamente.</p>
                        </div>
                    @endif

                    {{-- CHECKBOX ROBUSTO ( AlpineJS + @entangle ) --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Configuração</flux:label>
                        <div
                            x-data="{ check: @entangle('required') }"
                            @click="check = !check"
                            class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition-all duration-200"
                            :class="check ? 'bg-brand-500/10 border-brand-500/50 shadow-lg shadow-brand-500/5' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800'"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="size-6 rounded-lg border-2 flex items-center justify-center transition-all"
                                    :class="check ? 'bg-brand-600 border-brand-600 shadow-[0_0_10px_rgba(59,130,246,0.5)]' : 'bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700'"
                                >
                                    <template x-if="check">
                                        <flux:icon name="check" class="size-4 text-white" />
                                    </template>
                                </div>
                                <div>
                                    <span class="block text-xs font-black uppercase tracking-tight" :class="check ? 'text-brand-600' : 'text-zinc-500'">Campo Obrigatório</span>
                                    <span class="block text-[9px] text-zinc-400 font-bold uppercase italic">Validar preenchimento no formulário</span>
                                </div>
                            </div>
                            <input type="checkbox" wire:model="required" class="hidden">
                        </div>
                    </div>

                 <flux:button
    type="submit"
    variant="primary"
    wire:target="saveField"
    class="w-full h-16 bg-brand-600 hover:bg-brand-700 rounded-[1.5rem] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 transition-all hover:scale-[1.02]"
>
    <span wire:loading.remove wire:target="saveField">
        {{ $editingFieldId ? 'Atualizar Atributo' : 'Acoplar Atributo' }}
    </span>
    <span wire:loading wire:target="saveField" class="flex items-center gap-2">
        <flux:icon name="arrow-path" class="size-4 animate-spin" />
        A processar...
    </span>
</flux:button>
                </form>
            </div>

            {{-- PRESETS INTELIGENTES --}}
            <div class="bg-zinc-950 rounded-[2.5rem] p-8 border border-zinc-800 shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <h3 class="text-[10px] font-black text-brand-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                    <flux:icon name="sparkles" class="size-3" />
                    Sugestões de Arquitetura
                </h3>
                <div class="grid grid-cols-2 gap-3 relative z-10">
                    <button type="button" wire:click="usePreset('Localização', 'text')" class="p-4 text-left bg-white/5 hover:bg-white/10 rounded-2xl border border-white/5 transition-all group/btn">
                        <span class="block text-[11px] font-black text-white uppercase tracking-tighter group-hover/btn:text-brand-400 transition-colors">📍 Local</span>
                        <span class="text-[9px] text-zinc-500 font-bold uppercase">Texto Simples</span>
                    </button>
                    <button type="button" wire:click="usePreset('Prioridade', 'select', 'Urgente, Normal, Opcional')" class="p-4 text-left bg-white/5 hover:bg-white/10 rounded-2xl border border-white/5 transition-all group/btn">
                        <span class="block text-[11px] font-black text-white uppercase tracking-tighter group-hover/btn:text-brand-400 transition-colors">⚡ Prioridade</span>
                        <span class="text-[9px] text-zinc-500 font-bold uppercase">Seleção</span>
                    </button>
                    <button type="button" wire:click="usePreset('Vencimento', 'date')" class="p-4 text-left bg-white/5 hover:bg-white/10 rounded-2xl border border-white/5 transition-all group/btn">
                        <span class="block text-[11px] font-black text-white uppercase tracking-tighter group-hover/btn:text-brand-400 transition-colors">🗓️ Data Limite</span>
                        <span class="text-[9px] text-zinc-500 font-bold uppercase">Calendário</span>
                    </button>
                    <button type="button" wire:click="usePreset('Validado', 'checkbox')" class="p-4 text-left bg-white/5 hover:bg-white/10 rounded-2xl border border-white/5 transition-all group/btn">
                        <span class="block text-[11px] font-black text-white uppercase tracking-tighter group-hover/btn:text-brand-400 transition-colors">✓ Validação</span>
                        <span class="text-[9px] text-zinc-500 font-bold uppercase">Checkmark</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA: LISTA DE CAMPOS (7/12) --}}
<div class="lg:col-span-7 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm flex flex-col h-full">
    <div class="px-8 py-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20 flex items-center justify-between">
        <div>
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400 italic">Estrutura Ativa</h2>
            <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter leading-none">Hierarquia de Atributos</p>
        </div>
        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase border-none px-4 py-1.5 shadow-sm">{{ count($fields) }} Campos</flux:badge>
    </div>

    <div class="p-6 flex-1 overflow-y-auto custom-scrollbar">
        {{-- LISTA COM SORTABLE.JS --}}
        <div
            x-data
            x-init="new Sortable($el, {
                animation: 200,
                handle: '.field-drag',
                ghostClass: 'opacity-10',
                onEnd: (evt) => {
                    let items = Array.from(evt.to.children).map((el, idx) => ({ value: el.dataset.id, order: idx + 1 }));
                    $wire.updateOrder(items);
                }
            })"
            class="space-y-4"
        >
            @forelse ($fields as $f)
                <div data-id="{{ $f->id }}" wire:key="field-row-{{ $f->id }}"
                     class="group flex items-center justify-between p-5 bg-zinc-50 dark:bg-zinc-950/40 rounded-[1.5rem] border border-zinc-100 dark:border-zinc-800/60 hover:border-brand-500/30 hover:bg-white dark:hover:bg-zinc-900 transition-all duration-300 {{ $editingFieldId == $f->id ? 'ring-2 ring-brand-500 bg-brand-500/5' : '' }}">

                    <div class="flex items-center gap-6 min-w-0">
                        {{-- Drag Handle --}}
                        <div class="field-drag cursor-grab active:cursor-grabbing p-2 text-zinc-300 dark:text-zinc-700 hover:text-brand-500 transition-colors shrink-0">
                            <flux:icon name="bars-2" variant="mini" class="size-6" />
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-3">
                                <span class="font-black dark:text-white uppercase text-base truncate tracking-tight">{{ $f->label }}</span>
                                @if ($f->required)
                                    <span class="text-[8px] font-black bg-red-500/10 text-red-500 border border-red-500/20 px-2 py-0.5 rounded-md">OBRIGATÓRIO</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="text-[10px] font-black uppercase text-brand-600 bg-brand-50 dark:bg-brand-500/10 px-2.5 py-0.5 rounded-lg border border-brand-500/10">
                                    {{ $types[$f->type] }}
                                </span>
                                @if($f->type === 'select')
                                    <span class="text-[9px] text-zinc-400 font-bold uppercase truncate italic">— {{ count($f->options) }} opções</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- AÇÕES (EDITAR E ELIMINAR) --}}
                    <div class="shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        {{-- BOTÃO EDITAR --}}
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            wire:click="editField({{ $f->id }})"
                            class="text-zinc-500 hover:text-brand-500 rounded-xl h-10 w-10 flex items-center justify-center"
                            title="Editar Atributo"
                        />

                        {{-- BOTÃO ELIMINAR --}}
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            wire:click="removeField({{ $f->id }})"
                            wire:confirm="Eliminar este atributo?"
                            class="text-red-500 rounded-xl hover:bg-red-50 h-10 w-10 flex items-center justify-center"
                        />
                    </div>
                </div>
                    @empty
                        <div class="py-24 text-center space-y-6 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[3rem]">
                            <div class="relative inline-block">
                                <flux:icon name="clipboard-document-list" class="size-16 text-zinc-200 dark:text-zinc-800 opacity-40 mx-auto" />
                                <div class="absolute inset-0 bg-brand-500/10 blur-2xl rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-zinc-400 font-black uppercase tracking-[0.2em] text-[11px] italic">Arquitetura de Dados Vazia</p>
                                <p class="text-zinc-500 text-[9px] font-bold uppercase mt-1">Adiciona campos para personalizar o formulário deste Hub</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-8 bg-zinc-50/50 dark:bg-zinc-950/50 border-t border-zinc-100 dark:border-zinc-800">
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] italic text-center">
                    As alterações na estrutura são aplicadas em tempo real a todos os novos registos.
                </p>
            </div>
        </div>

    </div>

    {{-- CARREGAMENTO DA BIBLIOTECA SORTABLE --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
        .field-drag { touch-action: none; }
        {{-- Estilo para o item quando está a ser arrastado --}}
        .sortable-chosen { background: rgba(59, 130, 246, 0.05) !important; border-color: rgba(59, 130, 246, 0.3) !important; }
    </style>
</div>
