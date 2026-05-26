<div class="relative" x-data="{ open: false }">
    {{-- BOTÃO DO SINO --}}
    <button
        @click="open = !open"
        @click.outside="open = false"
        type="button"
        class="relative p-2 rounded-xl transition-all hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400"
    >
        <flux:icon name="bell" class="size-5" />

        @if($unreadCount > 0)
            <span class="absolute top-2 right-2 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border-2 border-white dark:border-zinc-950"></span>
            </span>
        @endif
    </button>

    {{-- MENU DE NOTIFICAÇÕES (ALPINIZED) --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-cloak
        class="absolute right-0 mt-2 w-80 md:w-96 bg-white dark:bg-zinc-900 rounded-[2rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 z-[500] overflow-hidden"
    >
        <header class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 flex justify-between items-center">
            <h3 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Alertas do Sistema</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[9px] font-black uppercase text-brand-600 hover:text-brand-500 transition-colors">
                    Ler Todas
                </button>
            @endif
        </header>

        <div class="max-h-96 overflow-y-auto no-scrollbar divide-y divide-zinc-50 dark:divide-zinc-800/50">
            @forelse($notifications as $notification)
                <div
                    wire:click="readAndNavigate({{ $notification->id }})"
                    @click="open = false"
                    class="p-4 flex gap-4 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition duration-150 relative
                    {{ is_null($notification->read_at) ? 'bg-brand-500/5' : '' }}"
                >
                    @if(is_null($notification->read_at))
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-brand-500 rounded-r-full"></div>
                    @endif

                    <div class="flex-shrink-0 mt-1">
                        <div class="p-2 rounded-xl bg-{{ $notification->color }}-500/10 text-{{ $notification->color }}-600">
                            <flux:icon name="{{ $notification->icon }}" variant="outline" class="size-4" />
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold dark:text-white uppercase tracking-tight leading-tight mb-1">
                            {{ $notification->title }}
                        </p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed truncate">
                            {{ $notification->message }}
                        </p>
                        <p class="text-[9px] font-bold text-zinc-400 mt-2 uppercase">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center space-y-3">
                    <flux:icon name="check-badge" class="size-10 text-zinc-200 dark:text-zinc-800 mx-auto" />
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 text-zinc-500">Nenhuma notificação</p>
                </div>
            @endforelse
        </div>

        @if($notifications->count() > 0)
            <footer class="p-3 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-100 dark:border-zinc-800">
                <button wire:click="clearHistory" class="w-full py-2 text-[9px] font-black uppercase tracking-widest text-zinc-400 hover:text-red-500 transition-colors">
                    Limpar Histórico
                </button>
            </footer>
        @endif
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
