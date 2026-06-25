<div style="--cat-color: {{ $categoryColor }};">
    {{-- CSS ADICIONAL PARA RESPONSIVIDADE E ANIMAÇÕES (IGUAL AO HUB) --}}
    <style>
        @media (max-width: 640px) {
            .custom-scrollbar::-webkit-scrollbar { width: 0px; }
            [class*="rounded-[2.5rem]"] { border-radius: 1.5rem !important; }
            [class*="rounded-[3rem]"] { border-radius: 2rem !important; }
        }
        @keyframes scan {
            0% { top: 0%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan-line {
            position: absolute; left: 0; width: 100%; height: 3px; z-index: 40;
            background: linear-gradient(to right, transparent, #10b981, transparent);
            box-shadow: 0 0 20px #10b981; animation: scan 2.5s infinite ease-in-out;
        }
        [x-cloak] { display: none !important; }
    </style>

    <div class="mx-auto max-w-6xl space-y-8 pb-24 px-4 sm:px-0"
        x-data="{
            scannerOpen: false,
            reviewOpen: false,
            scannerPreview: null,
        }"
        x-on:scan-completed.window="scannerOpen = false; setTimeout(() => reviewOpen = true, 250);"
        x-on:open-review-modal.window="scannerOpen = false; setTimeout(() => reviewOpen = true, 250);"
        x-on:keydown.escape.window="scannerOpen = false; reviewOpen = false;"
    >
        {{-- ── HEADER DINÂMICO ── --}}
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10 pt-4 sm:pt-6">
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl sm:rounded-[2rem] shadow-2xl">
                    <flux:icon name="banknotes" class="size-8 sm:size-10 text-brand-600" />
                </div>
                <div>
                    <div class="relative inline-block">
                        <h1 class="text-2xl sm:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                            {{ $expense ? 'Editar Registo' : 'Novo Registo' }}
                        </h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[8px] sm:text-[9px] font-black uppercase tracking-widest border-none px-2 py-0.5">Terminal Gestão</flux:badge>
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-500 font-medium italic mt-1 sm:mt-2">Lançamento Estratégico de Custos</p>
                </div>
            </div>

           <div class="flex items-center gap-3 w-full sm:w-auto bg-white dark:bg-zinc-900 p-2 sm:p-3 rounded-2xl sm:rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">

    {{-- BOTÃO SCANNER --}}
    <button type="button" @click="scannerPreview = null; scannerOpen = true"
        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 h-12 rounded-xl sm:rounded-2xl text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-500/10 font-black uppercase text-[10px] sm:text-sm transition-all border border-transparent hover:border-brand-500/20">
        <flux:icon name="sparkles" class="size-4 animate-pulse" />
        Scanner IA
    </button>

    {{-- SEPARADOR --}}
    <div class="hidden sm:block h-8 w-px bg-zinc-200 dark:bg-zinc-800"></div>

    {{-- BOTÃO SAIR DESTACADO --}}
    <a href="{{ route('expenses') }}" wire:navigate
        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 h-12 rounded-xl sm:rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-black uppercase text-[10px] sm:text-sm transition-all hover:bg-red-500 hover:text-white hover:shadow-lg hover:shadow-red-500/20 group">
        <flux:icon name="arrow-left" class="size-4 group-hover:-translate-x-1 transition-transform" />
        Sair
    </a>
</div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- ── COLUNA ESQUERDA: SELETOR DE HUB (4/12) ── --}}
            <div class="lg:col-span-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-6 shadow-sm h-fit">
                <div class="flex items-center gap-2 mb-6 px-2">
                    <div class="size-2 rounded-full bg-brand-500"></div>
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-zinc-400 italic">Selecionar Hub de Destino</h2>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach($categories as $cat)
                        <button type="button" wire:click="$set('category_id', {{ $cat->id }})"
                            @class([
                                'flex flex-col items-center justify-center p-5 rounded-[1.8rem] border-2 transition-all gap-3 group relative overflow-hidden',
                                'bg-brand-500/5 border-brand-500 shadow-lg' => $category_id == $cat->id,
                                'bg-zinc-50 dark:bg-zinc-950 border-transparent opacity-60 hover:opacity-100 hover:bg-zinc-100 dark:hover:bg-zinc-800' => $category_id != $cat->id,
                            ])>
                            <flux:icon name="{{ $cat->icon ?? 'tag' }}" class="size-7 transition-transform group-hover:scale-110"
                                style="color: {{ $category_id == $cat->id ? $cat->color : '#9ca3af' }}" />
                            <span class="text-[9px] font-black uppercase text-center leading-none {{ $category_id == $cat->id ? 'text-zinc-900 dark:text-white' : 'text-zinc-500' }}">
                                {{ $cat->name }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ── COLUNA DIREITA: FORMULÁRIO (8/12) ── --}}
            <div class="lg:col-span-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-2xl overflow-hidden">
                <div class="h-1.5 w-full transition-colors duration-500" style="background-color: var(--cat-color);"></div>

                <form wire:submit.prevent="save" class="p-6 sm:p-10 space-y-8" autocomplete="off">

                    {{-- Valor e Data --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-1">Valor da Transação</span>
                            <div class="relative">
                                <input wire:model="amount" type="number" step="0.01" placeholder="0.00"
                                    class="w-full h-16 rounded-2xl border-0 bg-zinc-50 dark:bg-zinc-950 px-6 text-2xl font-black text-brand-600 shadow-inner outline-none ring-0 focus:ring-2 focus:ring-brand-500/20 transition-all">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 font-black text-zinc-400">€</span>
                            </div>
                        </label>
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Data do Registo</span>
                            <input wire:model="spent_at" type="date"
                                class="w-full h-16 rounded-2xl border-0 bg-zinc-50 dark:bg-zinc-950 px-6 text-sm font-bold shadow-inner outline-none ring-0 focus:ring-2 focus:ring-brand-500/20 transition-all dark:text-white">
                        </label>
                    </div>

                    {{-- Subcategoria --}}
                    <label class="block space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-1">Classificação do Gasto</span>
                        <select wire:model="subcategory" class="w-full h-14 rounded-2xl border-0 bg-zinc-50 dark:bg-zinc-950 px-6 text-sm font-bold shadow-inner focus:ring-2 focus:ring-brand-500/20 dark:text-white transition-all">
                            <option value="">Selecionar tipo...</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub }}">{{ $sub }}</option>
                            @endforeach
                        </select>
                    </label>

                    {{-- Campos Dinâmicos (Hub Específico) --}}
                    @if(isset($categoryFields[$activeSlug]))
                    <div class="rounded-3xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 p-6 sm:p-8 space-y-6 animate-in fade-in slide-in-from-top-2 duration-500">
                        <div class="flex items-center gap-3 border-b border-zinc-200/50 dark:border-zinc-800 pb-4">
                            <flux:icon name="{{ $categoryFields[$activeSlug]['icon'] ?? 'tag' }}" class="size-4" style="color: var(--cat-color);" />
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Informações de Contexto</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($categoryFields[$activeSlug]['fields'] as $field)
                                <label class="block space-y-2">
                                    <span class="text-[10px] font-bold uppercase text-zinc-400 ml-1">{{ $field['label'] }}</span>
                                    @if($field['type'] === 'select')
                                        <select wire:model="meta.{{ $field['name'] }}" class="w-full h-12 rounded-xl border-0 bg-white dark:bg-zinc-900 px-4 text-sm font-bold shadow-sm focus:ring-2 focus:ring-brand-500/20 transition-all">
                                            <option value="">Escolher...</option>
                                            @foreach($field['options'] as $opt) <option value="{{ $opt }}">{{ $opt }}</option> @endforeach
                                        </select>
                                    @else
                                        <input wire:model="meta.{{ $field['name'] }}" type="{{ $field['type'] }}" class="w-full h-12 rounded-xl border-0 bg-white dark:bg-zinc-900 px-4 text-sm font-bold shadow-sm focus:ring-2 focus:ring-brand-500/20 transition-all">
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Notas --}}
                    <label class="block space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-1">Notas / Observações</span>
                        <textarea wire:model="description" rows="3" placeholder="Detalhes adicionais..."
                            class="w-full resize-none rounded-[2rem] border-0 bg-zinc-50 dark:bg-zinc-950 px-6 py-4 text-sm font-medium shadow-inner outline-none focus:ring-2 focus:ring-brand-500/20 transition-all"></textarea>
                    </label>

                    <button type="submit"
                        class="w-full h-16 rounded-3xl font-black uppercase tracking-widest text-white transition-all active:scale-95 shadow-2xl"
                        style="background-color: var(--cat-color); box-shadow: 0 15px 30px -10px color-mix(in srgb, var(--cat-color), transparent 50%);">
                        <span wire:loading.remove wire:target="save">{{ $expense ? 'Atualizar Registo' : 'Confirmar Lançamento' }}</span>
                        <span wire:loading wire:target="save">A Guardar...</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- MODAL 1 — SCANNER IA (CLONE CATEGORYHUB)                      --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div x-show="scannerOpen" x-cloak class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm" @click="scannerOpen = false"></div>
        <div x-show="scannerOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click.stop class="relative w-full max-w-lg bg-zinc-950 rounded-[2.5rem] border border-white/10 shadow-2xl overflow-hidden">

                <div wire:loading wire:target="scanReceiptWithAI" class="absolute inset-0 bg-zinc-950/95 z-50 flex flex-col items-center justify-center gap-6">
                    <div class="size-20 border-4 border-brand-500/20 border-t-brand-500 rounded-full animate-spin"></div>
                    <p class="text-brand-400 font-black uppercase tracking-[0.4em] text-[10px]">IA Vision a processar...</p>
                </div>

                <div class="p-10 space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-brand-500/10 rounded-2xl text-brand-400"><flux:icon name="sparkles" class="size-6" /></div>
                            <h2 class="text-xl font-black text-white uppercase italic tracking-tighter">Scanner Vision IA</h2>
                        </div>
                        <button type="button" @click="scannerOpen = false" class="text-zinc-500 hover:text-white"><flux:icon name="x-mark" /></button>
                    </div>

                    @if($scanError)
                        <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-4 text-xs text-red-400 font-medium">{{ $scanError }}</div>
                    @endif

                    <div class="relative bg-zinc-900/50 rounded-[2.5rem] p-10 border-2 border-dashed border-zinc-800 text-center hover:border-brand-500/50 transition-all group">
                        <input type="file" id="receiptInput" accept="image/*" wire:model="receipt" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                        @if ($receipt && !$errors->has('receipt'))
                            <div class="relative inline-block">
                                <img src="{{ $receipt->temporaryUrl() }}" class="size-44 object-cover rounded-3xl border-4 border-brand-500/50 shadow-2xl">
                                <div class="animate-scan-line"></div>
                            </div>
                        @else
                            <flux:icon name="camera" class="size-16 text-zinc-800 mx-auto mb-4 group-hover:text-brand-500" />
                            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Carregar Fatura / Recibo</p>
                        @endif
                    </div>

                    <div class="flex gap-4">
                        <button type="button" @click="scannerOpen = false" class="flex-1 h-14 text-zinc-500 font-bold uppercase text-xs tracking-widest">Cancelar</button>
                        <button type="button" wire:click="scanReceiptWithAI" wire:loading.attr="disabled" class="flex-[2] h-14 rounded-2xl bg-brand-600 text-white font-black uppercase shadow-xl text-sm transition-all hover:bg-brand-500">
                            Iniciar Extração
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- MODAL 2 — REVISÃO IA (CLONE CATEGORYHUB)                      --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div x-show="reviewOpen" x-cloak class="fixed inset-0 z-50 bg-black/60 backdrop-blur-md" @click="reviewOpen = false"></div>
        <div x-show="reviewOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click.stop class="relative w-full max-w-2xl bg-zinc-950 rounded-[2.5rem] shadow-2xl border border-white/10 max-h-[90vh] flex flex-col overflow-hidden">
                <div class="p-8 border-b border-white/5 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20"><flux:icon name="sparkles" class="size-6 text-emerald-400" /></div>
                        <h2 class="text-xl font-black text-white uppercase italic tracking-tighter">Dados Extraídos</h2>
                    </div>
                    <button type="button" @click="reviewOpen = false" class="text-zinc-500 hover:text-white transition-colors"><flux:icon name="x-mark" /></button>
                </div>

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    @if(!empty($scannedData))
                        <div class="bg-white/5 border border-white/10 rounded-[2rem] p-8 text-center">
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.3em] mb-2">Total Extraído</p>
                            <p class="text-6xl font-black text-white tracking-tighter italic">{{ number_format($scannedData['amount'] ?? 0, 2, ',', '.') }}€</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2 bg-white/5 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                                <flux:icon name="building-storefront" class="size-5 text-brand-400" />
                                <div><p class="text-[8px] font-black text-zinc-500 uppercase">Estabelecimento</p><p class="text-sm font-black text-white">{{ $scannedData['store'] ?: 'Não detetado' }}</p></div>
                            </div>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                                <flux:icon name="calendar" class="size-5 text-zinc-400" />
                                <div><p class="text-[8px] font-black text-zinc-500 uppercase">Data Detetada</p><p class="text-sm font-black text-white">{{ $scannedData['date'] }}</p></div>
                            </div>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-4 flex items-center gap-4">
                                <flux:icon name="tag" class="size-5 text-zinc-400" />
                                <div><p class="text-[8px] font-black text-zinc-500 uppercase">Sugestão de Categoria</p><p class="text-sm font-black text-brand-400">{{ $scannedData['subcategory'] ?? 'Geral' }}</p></div>
                            </div>
                        </div>

                        @if(!empty($scannedData['items']))
                        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                            <div class="px-4 py-2 bg-white/5 border-b border-white/5 font-black text-[8px] text-zinc-400 uppercase">Lista de Itens</div>
                            <div class="divide-y divide-white/5 max-h-40 overflow-y-auto custom-scrollbar">
                                @foreach($scannedData['items'] as $item)
                                    <div class="px-4 py-3 flex justify-between text-xs">
                                        <span class="text-zinc-300 font-medium">{{ $item['name'] }}</span>
                                        <span class="font-black text-white">{{ number_format($item['price'] ?? 0, 2) }}€</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endif
                </div>

                <div class="p-8 pt-4 flex gap-4 shrink-0">
                    <button type="button" @click="reviewOpen = false" class="flex-1 h-14 rounded-2xl font-bold text-xs text-zinc-500 border border-white/5 uppercase tracking-widest">Ajustar</button>
                    <button type="button" @click="reviewOpen = false" class="flex-[2] h-14 rounded-2xl bg-brand-600 text-white font-black uppercase text-sm shadow-xl hover:bg-brand-500 transition-all">Confirmar e Preencher</button>
                </div>
            </div>
        </div>

    </div>{{-- /FIM DO WRAPPER x-data --}}
</div>{{-- /FIM DO WRAPPER DE COR --}}
