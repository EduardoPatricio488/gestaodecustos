<div class="max-w-3xl mx-auto py-10 space-y-8 text-left">

  <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h1 class="text-3xl font-black uppercase italic tracking-tighter">Carrinho</h1>
      <p class="text-sm text-zinc-500 mt-1">{{ $count }} {{ $count === 1 ? 'item' : 'itens' }} no carrinho</p>
    </div>
    <flux:button href="{{ route('hub.store') }}" wire:navigate variant="ghost" icon="arrow-left" class="rounded-xl uppercase font-black text-[10px] tracking-widest">
      Continuar a Comprar
    </flux:button>
  </header>

  @if($items->isEmpty())
    <div class="py-24 text-center bg-zinc-50 border-2 border-dashed border-zinc-200 rounded-[3rem]">
      <flux:icon name="shopping-cart" class="size-14 mx-auto mb-4 text-zinc-300" />
      <h3 class="text-xl font-black text-zinc-400 uppercase tracking-widest">Carrinho Vazio</h3>
      <p class="text-xs text-zinc-500 mt-2 mb-8">Ainda não adicionaste nenhum produto.</p>
      <flux:button href="{{ route('hub.store') }}" wire:navigate variant="primary" class="rounded-2xl uppercase font-black text-[10px] tracking-widest">
        Explorar Loja
      </flux:button>
    </div>
  @else
    <div class="space-y-4">
      @foreach($items as $item)
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-sm" wire:key="cart-item-{{ $item['product']->id }}">
          <div class="text-5xl shrink-0">{{ $item['product']->image }}</div>

          <div class="flex-1 min-w-0">
            <p class="text-[9px] font-black text-brand-600 uppercase tracking-widest">{{ $item['product']->category_label }}</p>
            <h3 class="text-lg font-black uppercase truncate">{{ $item['product']->title }}</h3>
            <p class="text-sm text-zinc-500 mt-1">{{ number_format($item['product']->price, 2, ',', '.') }} € cada</p>
          </div>

          <div class="flex items-center gap-3">
            <div class="flex items-center border border-zinc-200 rounded-xl overflow-hidden">
              <button wire:click="updateQuantity({{ $item['product']->id }}, {{ $item['quantity'] - 1 }})"
                      class="px-3 py-2 text-zinc-600 hover:bg-zinc-100 transition-colors font-bold">−</button>
              <span class="px-4 py-2 text-sm font-black min-w-10 text-center">{{ $item['quantity'] }}</span>
              <button wire:click="updateQuantity({{ $item['product']->id }}, {{ $item['quantity'] + 1 }})"
                      class="px-3 py-2 text-zinc-600 hover:bg-zinc-100 transition-colors font-bold">+</button>
            </div>

            <p class="text-lg font-black min-w-24 text-right">{{ number_format($item['subtotal'], 2, ',', '.') }} €</p>

            <button wire:click="removeItem({{ $item['product']->id }})"
                    class="p-2 text-zinc-400 hover:text-red-500 transition-colors"
                    title="Remover">
              <flux:icon name="trash" class="size-5" />
            </button>
          </div>
        </div>
      @endforeach
    </div>

    <div class="bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 space-y-6">
      <div class="flex items-center justify-between">
        <span class="text-sm font-bold text-zinc-500 uppercase tracking-widest">Total</span>
        <span class="text-3xl font-black italic">{{ number_format($total, 2, ',', '.') }} €</span>
      </div>

      @if(isset($crossSell) && $crossSell->isNotEmpty())
        <div class="p-4 bg-brand-50 border border-brand-200 rounded-2xl">
          <p class="text-[10px] font-black uppercase text-brand-700 mb-2">Também poderás gostar</p>
          <div class="flex flex-wrap gap-2">
            @foreach($crossSell as $product)
              <button wire:click="addToCart({{ $product->id }})" class="px-3 py-2 bg-white border rounded-xl text-[9px] font-bold">
                + {{ $product->title }}
              </button>
            @endforeach
          </div>
        </div>
      @endif

      <div class="flex flex-col sm:flex-row gap-3">
        <button wire:click="clearCart"
                class="flex-1 h-12 rounded-2xl border border-zinc-300 text-zinc-600 font-black uppercase text-[10px] tracking-widest hover:bg-zinc-200 transition-all">
          Esvaziar Carrinho
        </button>

        <flux:button href="{{ route('store.checkout') }}" wire:navigate variant="primary" class="flex-1 h-12 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-xl shadow-brand-500/20">
          Finalizar Compra
        </flux:button>
      </div>
    </div>
  @endif

</div>
