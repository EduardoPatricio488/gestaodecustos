<div
    x-data="{
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
    class="fixed bottom-24 right-6 z-[108] pointer-events-none"
    aria-hidden="false"
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
    <span
        x-show="showPlus"
        x-cloak
        x-transition:enter="store-cart-plus-fly"
        class="absolute -top-2 left-1/2 -translate-x-1/2 text-sm font-black text-emerald-600 drop-shadow-sm pointer-events-none store-cart-plus-fly"
    >+1</span>

    <a
        href="{{ route('store.cart') }}"
        wire:navigate
        title="Ver carrinho ({{ $count }} {{ $count === 1 ? 'item' : 'itens' }})"
        :class="bouncing ? 'store-cart-pop' : ''"
        class="relative pointer-events-auto flex items-center justify-center size-16 rounded-full bg-emerald-600 text-white shadow-[0_12px_40px_rgba(5,150,105,0.45)] border-4 border-white dark:border-zinc-900 hover:bg-emerald-500 hover:scale-105 active:scale-95 transition-colors duration-200 group"
    >
        {{-- Anel de pulso ao adicionar --}}
        <span
            x-show="pulseRing"
            x-cloak
            class="absolute inset-0 rounded-full border-2 border-emerald-400 store-cart-ring pointer-events-none"
        ></span>

        <flux:icon name="shopping-cart" class="size-7 group-hover:scale-110 transition-transform duration-300" />

        @if($count > 0)
            <span
                wire:key="cart-badge-{{ $count }}"
                x-bind:class="bouncing ? 'store-cart-pop' : ''"
                class="absolute -top-1 -right-1 min-w-6 h-6 px-1.5 flex items-center justify-center bg-white text-emerald-700 text-[10px] font-black rounded-full border-2 border-emerald-600 shadow-md"
            >
                {{ $count > 9 ? '9+' : $count }}
            </span>
        @else
            <span class="absolute -top-0.5 -right-0.5 size-3 bg-amber-400 border-2 border-white dark:border-zinc-900 rounded-full"></span>
        @endif
    </a>
</div>
