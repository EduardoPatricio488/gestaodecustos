<div
    x-data="{
        cartOpen: false,
        bouncing: false,
        showPlus: false,
        pulseRing: false,
        playAddAnimation() {
            this.bouncing = true;
            this.showPlus = true;
            this.pulseRing = true;
            setTimeout(() => this.bouncing = false, 650);
            setTimeout(() => this.showPlus = false, 950);
            setTimeout(() => this.pulseRing = false, 900);
        }
    }"
    x-on:cart-item-added.window="playAddAnimation()"
    @open-cart.window="cartOpen = true"
    class="fixed bottom-24 right-6 z-[108]"
>
    <style>
        @keyframes store-cart-pop {
            0%, 100% { transform: scale(1) rotate(0deg); }
            25% { transform: scale(1.18) rotate(-10deg); }
            50% { transform: scale(1.1) rotate(8deg); }
            75% { transform: scale(1.14) rotate(-4deg); }
        }
        @keyframes store-cart-ring {
            0% { transform: scale(1); opacity: 0.55; }
            100% { transform: scale(1.65); opacity: 0; }
        }
        @keyframes store-cart-plus-fly {
            0% { opacity: 1; transform: translate(-50%, 0) scale(1); }
            100% { opacity: 0; transform: translate(-50%, -2.75rem) scale(1.15); }
        }
        .store-cart-pop { animation: store-cart-pop 0.65s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .store-cart-ring { animation: store-cart-ring 0.85s ease-out forwards; }
        .store-cart-plus-fly { animation: store-cart-plus-fly 0.9s ease-out forwards; }
    </style>

    {{-- +1 ao adicionar --}}
    <span x-show="showPlus" x-cloak class="absolute -top-2 left-1/2 -translate-x-1/2 text-sm font-black text-emerald-600 drop-shadow-sm pointer-events-none store-cart-plus-fly">+1</span>

    {{-- BOTÃO FLUTUANTE (FAB) --}}
    <button type="button" @click="cartOpen = true" :class="bouncing ? 'store-cart-pop' : ''"
        class="relative pointer-events-auto flex items-center justify-center size-16 rounded-full bg-emerald-600 text-white shadow-[0_12px_40px_rgba(5,150,105,0.45)] border-4 border-white dark:border-zinc-900 hover:bg-emerald-500 hover:scale-110 active:scale-95 transition-all duration-200 group outline-none">

        <span x-show="pulseRing" x-cloak class="absolute inset-0 rounded-full border-2 border-emerald-400 store-cart-ring pointer-events-none"></span>

        <flux:icon name="shopping-cart" class="size-7" />

        @if($count > 0)
            <span class="absolute -top-1 -right-1 min-w-6 h-6 px-1.5 flex items-center justify-center bg-white text-emerald-700 text-[10px] font-black rounded-full border-2 border-emerald-600 shadow-md">
                {{ $count > 9 ? '9+' : $count }}
            </span>
        @endif
    </button>

    {{-- ABA LATERAL (DRAWER) --}}
    <template x-teleport="body">
        <div x-show="cartOpen" x-cloak class="fixed inset-0 z-[1000]">
            <!-- Backdrop -->
            <div x-show="cartOpen" x-transition.opacity @click="cartOpen = false" class="absolute inset-0 bg-zinc-950/40 backdrop-blur-sm"></div>

            <!-- Painel -->
            <div x-show="cartOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="fixed right-0 top-0 h-screen w-full max-w-sm bg-white dark:bg-zinc-900 shadow-2xl flex flex-col border-l border-zinc-200 dark:border-zinc-800">

                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50 dark:bg-zinc-950">
                    <div class="flex items-center gap-3">
                        <flux:icon name="shopping-cart" class="size-5 text-emerald-600" />
                        <h2 class="text-sm font-black uppercase tracking-widest text-zinc-900 dark:text-white leading-none">O Teu Carrinho</h2>
                    </div>
                    <button @click="cartOpen = false" class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-full transition-colors">
                        <flux:icon name="x-mark" variant="micro" class="size-4" />
                    </button>
                </div>

                <!-- Lista de Produtos -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar text-left">
    @forelse($cartItems as $product)
        <div class="flex gap-4 items-center animate-in fade-in slide-in-from-right-4 duration-300" wire:key="drawer-item-{{ $product->id }}">
            {{-- Mostra o ícone/imagem do produto --}}
            <div class="size-16 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-3xl shadow-inner shrink-0">
                {{ $product->image ?? '📦' }}
            </div>

            <div class="flex-1 min-w-0">
                {{-- MUDANÇA AQUI: Usamos $product->title para ler da BD --}}
                <h4 class="text-[11px] font-black uppercase text-zinc-800 dark:text-white truncate">
                    {{ $product->title }}
                </h4>

                {{-- MUDANÇA AQUI: Usamos $product->price para ler da BD --}}
                <p class="text-[10px] text-emerald-600 font-bold mt-0.5 uppercase">
                    {{ number_format($product->price, 2, ',', '.') }} €
                </p>

                {{-- O botão de remover agora envia o ID real do produto --}}
                <button wire:click="removeFromCart({{ $product->id }})" class="text-[8px] font-black text-red-500 uppercase hover:underline">
                    Remover
                </button>
            </div>
        </div>
    @empty
        <div class="h-full flex flex-col items-center justify-center text-center opacity-30 py-20">
            <flux:icon name="shopping-cart" class="size-12 mb-4" />
            <p class="text-[10px] font-black uppercase tracking-widest">O carrinho está vazio</p>
        </div>
    @endforelse
</div>

                <!-- Footer do Carrinho -->
                @if(count($cartItems) > 0)
                    <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 space-y-4">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black uppercase text-zinc-400">Total:</span>
                            <span class="text-2xl font-black italic text-zinc-900 dark:text-white">
                                {{ number_format($cartTotal, 2, ',', '.') }} €
                            </span>
                        </div>

                        <div class="grid gap-2">
                            <a href="{{ route('store.checkout') }}" wire:navigate class="w-full h-14 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl flex items-center justify-center gap-3 font-black uppercase text-[11px] tracking-[0.2em] shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                                Finalizar Compra
                            </a>
                            <a href="{{ route('store.cart') }}" wire:navigate class="w-full py-3 text-center text-[9px] font-black uppercase text-zinc-400 hover:text-zinc-600 transition-colors">
                                Editar no Carrinho Completo
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </template>
</div>
