<div class="max-w-4xl mx-auto py-10 space-y-8">
    <header class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black uppercase italic">Favoritos</h1>
            <p class="text-sm text-zinc-500">{{ $items->count() }} produtos guardados</p>
        </div>
        <flux:button href="{{ route('hub.store') }}" wire:navigate variant="ghost" icon="arrow-left" class="rounded-xl uppercase font-black text-[10px]">Loja</flux:button>
    </header>

    @forelse($items as $item)
        <div class="flex items-center gap-6 p-6 bg-white border rounded-3xl" wire:key="wish-{{ $item->id }}">
            <span class="text-4xl">{{ $item->product->image }}</span>
            <div class="flex-1">
                <p class="text-[9px] font-black text-brand-600 uppercase">{{ $item->product->category_label }}</p>
                <h3 class="font-black uppercase">{{ $item->product->title }}</h3>
                <p class="text-lg font-black mt-1">{{ number_format($item->product->price, 2, ',', '.') }} €</p>
            </div>
            <div class="flex flex-col gap-2">
                <a href="{{ route('store.product.show', $item->product) }}" wire:navigate class="px-4 py-2 bg-zinc-100 rounded-xl text-[9px] font-black uppercase text-center">Ver</a>
                <button wire:click="addToCart({{ $item->product->id }})" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-[9px] font-black uppercase">+ Carrinho</button>
                <button wire:click="removeFromWishlist({{ $item->product->id }})" class="px-4 py-2 text-red-500 text-[9px] font-black uppercase">Remover</button>
            </div>
        </div>
    @empty
        <div class="py-20 text-center border-2 border-dashed rounded-3xl">
            <flux:icon name="heart" class="size-12 mx-auto mb-4 text-zinc-300" />
            <p class="font-black text-zinc-400 uppercase">Sem favoritos</p>
            <flux:button href="{{ route('hub.store') }}" wire:navigate class="mt-6 rounded-xl uppercase font-black text-[10px]">Explorar Loja</flux:button>
        </div>
    @endforelse
</div>
