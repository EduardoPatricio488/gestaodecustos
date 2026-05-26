<div
    x-data="{ isOpen: @entangle('isOpen') }"
    x-show="isOpen"
    x-on:open-global-search.window="isOpen = true; $nextTick(() => $refs.searchInput.focus())"
    x-on:keydown.window.escape="isOpen = false"
    class="relative z-[999]"
    style="display: none;"
>
    <div x-show="isOpen" x-transition.opacity class="fixed inset-0 bg-zinc-950/40 backdrop-blur-md" @click="isOpen = false"></div>

    <div class="fixed inset-0 overflow-y-auto p-4 md:p-20 pointer-events-none">
        <div
            x-show="isOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="mx-auto max-w-2xl transform overflow-hidden rounded-[2.5rem] bg-white dark:bg-zinc-900 shadow-2xl pointer-events-auto border border-zinc-200 dark:border-zinc-800"
        >
            {{-- Barra de Pesquisa com Contexto --}}
            <div class="relative group border-b dark:border-zinc-800">
                <flux:icon name="magnifying-glass" class="absolute left-6 top-6 size-6 text-zinc-400 group-focus-within:text-brand-500" />
                <input
                    x-ref="searchInput"
                    wire:model.live.debounce.100ms="search"
                    type="text"
                    class="h-20 w-full border-0 bg-transparent pl-16 pr-32 text-zinc-900 dark:text-white focus:ring-0 text-xl font-bold uppercase tracking-tight"
                    placeholder="Pesquisar..."
                >
                {{-- INDICADOR DE CONTEXTO --}}
                <div class="absolute right-6 top-7">
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $isBusinessMode ? 'bg-zinc-900 text-brand-400 border-brand-500/30' : 'bg-brand-500 text-white border-transparent' }}">
                        {{ $isBusinessMode ? 'Modo Empresa' : 'Modo Pessoal' }}
                    </span>
                </div>
            </div>

            {{-- Resultados --}}
            <div class="max-h-[60vh] overflow-y-auto p-4 no-scrollbar">
                @if(strlen($search) >= 2)
                    @forelse($groupedResults as $category => $items)
                        <div class="mb-6 last:mb-0">
                            <h3 class="px-4 text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-3">{{ $category }}</h3>
                            <div class="space-y-1">
                                @foreach($items as $item)
                                    <a href="{{ $item['url'] }}" wire:navigate @click="isOpen = false"
                                       class="flex items-center gap-4 p-4 rounded-[1.5rem] hover:bg-brand-500 group transition-all">
                                        <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-white/20">
                                            <flux:icon :name="$item['icon']" class="size-5 text-zinc-500 dark:text-zinc-400 group-hover:text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-black dark:text-white group-hover:text-white uppercase truncate">{{ $item['title'] }}</p>
                                            <p class="text-[10px] font-bold text-zinc-500 group-hover:text-white/70 uppercase truncate">{{ $item['sub'] ?? '' }}</p>
                                        </div>
                                        <flux:icon name="chevron-right" class="size-4 text-zinc-300 group-hover:text-white" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center text-zinc-500 italic">Sem resultados neste contexto.</div>
                    @endforelse
                @else
                    <div class="py-12 text-center">
                        <flux:icon name="sparkles" class="size-8 text-zinc-200 mx-auto mb-3" />
                        <p class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.3em]">
                            Pesquisa inteligente em {{ $isBusinessMode ? 'negócios' : 'finanças pessoais' }}
                        </p>
                    </div>
                @endif
            </div>

            <footer class="p-4 bg-zinc-50 dark:bg-zinc-800/50 border-t dark:border-zinc-800 flex justify-between items-center text-[9px] font-black uppercase text-zinc-400">
                <div class="flex gap-4 italic opacity-50">
                    <span>⏎ Selecionar</span>
                    <span>ESC Fechar</span>
                </div>
                <span class="text-brand-600">Spotlight Contextual</span>
            </footer>
        </div>
    </div>
</div>
