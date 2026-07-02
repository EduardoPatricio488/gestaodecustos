<div class="space-y-8 pb-20">

    {{-- HEADER --}}
    <header class="relative overflow-hidden bg-zinc-100 rounded-[3rem] p-10 border border-zinc-300 shadow-xl">
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
            <div class="text-left">
                <flux:badge variant="neutral" class="bg-zinc-200 border-zinc-300 text-zinc-700 font-black uppercase tracking-widest text-[9px] mb-4">
                    Finance Hub PRO Store
                </flux:badge>
                <h1 class="text-4xl font-black text-zinc-900 uppercase italic tracking-tighter leading-none">
                    Extensões PRO & IA Financeira
                </h1>
                <p class="text-zinc-600 font-medium mt-4 max-w-xl">
                    Pesquisa, compara e adquire extensões premium com confiança.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">

                <a href="{{ route('store.wishlist') }}" wire:navigate class="px-4 py-2.5 bg-white border border-zinc-300 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-50 flex items-center gap-2">
                    <flux:icon name="heart" class="size-4" /> Favoritos
                </a>
                <a href="{{ route('store.compare') }}" wire:navigate class="px-4 py-2.5 bg-white border border-zinc-300 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-50 flex items-center gap-2">
                    <flux:icon name="scale" class="size-4" /> Comparar
                    @if($compareCount > 0)
                        <span class="bg-brand-500 text-white text-[9px] px-1.5 py-0.5 rounded-full">{{ $compareCount }}</span>
                    @endif
                </a>
                <a href="{{ route('store.cart') }}" wire:navigate class="relative px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 flex items-center gap-2 shadow-lg">
                    <flux:icon name="shopping-cart" class="size-4" /> Carrinho
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 min-w-5 h-5 px-1 flex items-center justify-center bg-white text-emerald-700 text-[9px] font-black rounded-full border-2 border-emerald-600">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    {{-- ACESSO RÁPIDO AO INVENTÁRIO --}}
    <a href="{{ route('hub.inventory') }}" wire:navigate
       class="group flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-6 bg-gradient-to-r from-emerald-600 to-emerald-500 rounded-3xl text-white shadow-xl hover:shadow-2xl hover:from-emerald-500 hover:to-emerald-400 transition-all">
        <div class="flex items-center gap-4">
            <div class="size-14 bg-white/20 rounded-2xl flex items-center justify-center group-hover:scale-105 transition-transform">
                <flux:icon name="archive-box" class="size-7" />
            </div>
            <div class="text-left">
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-emerald-100">Os teus produtos</p>
                <h2 class="text-xl font-black uppercase italic tracking-tight">O Meu Inventário</h2>
                <p class="text-sm text-emerald-50 mt-1">
                    @if($inventoryCount > 0)
                        Tens {{ $inventoryCount }} {{ $inventoryCount === 1 ? 'recurso ativo' : 'recursos ativos' }} — descarrega, ativa e gere tudo aqui.
                    @else
                        Ainda não tens produtos. Após comprar, aparecem aqui para download imediato.
                    @endif
                </p>
            </div>
        </div>
        <span class="shrink-0 px-6 py-3 bg-white text-emerald-700 rounded-2xl text-[10px] font-black uppercase tracking-widest group-hover:bg-emerald-50 transition-colors">
            Abrir Inventário →
        </span>
    </a>

    {{-- PESQUISA & FILTROS --}}
    <div class="bg-white border border-zinc-200 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <flux:icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-zinc-400" />
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Pesquisar produtos..."
                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-zinc-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none text-sm" />
            </div>
            <div class="flex flex-wrap gap-3">
                <input wire:model.live.debounce.500ms="priceMin" type="number" step="0.01" min="0" placeholder="Min €"
                       class="w-24 px-3 py-3 rounded-xl border border-zinc-200 text-sm" />
                <input wire:model.live.debounce.500ms="priceMax" type="number" step="0.01" min="0" placeholder="Max €"
                       class="w-24 px-3 py-3 rounded-xl border border-zinc-200 text-sm" />
                <select wire:model.live="sortBy" class="px-4 py-3 rounded-xl border border-zinc-200 text-sm font-bold">
                    <option value="popular">Mais Popular</option>
                    <option value="rating">Melhor Avaliado</option>
                    <option value="price_asc">Preço ↑</option>
                    <option value="price_desc">Preço ↓</option>
                    <option value="newest">Mais Recente</option>
                </select>
                <label class="flex items-center gap-2 px-4 py-3 rounded-xl border border-zinc-200 text-sm font-bold cursor-pointer">
                    <input wire:model.live="onlyFeatured" type="checkbox" class="rounded" />
                    Destaques
                </label>
                <button wire:click="clearFilters" class="px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:bg-zinc-100">
                    Limpar
                </button>
            </div>
        </div>

        {{-- TABS --}}
        @php $storeTabs = app(\App\Services\StoreTabsService::class)->visible(); @endphp
        <div class="flex overflow-x-auto gap-1 p-1 bg-zinc-100 rounded-2xl">
            @foreach($storeTabs as $tab)
                <button wire:click="setTab('{{ $tab['key'] }}')"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all
                               {{ $activeTab === $tab['key'] ? 'bg-zinc-800 text-white' : 'text-zinc-600 hover:bg-zinc-200' }}">
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- BUNDLES --}}
    @if($bundles->isNotEmpty() && $activeTab === 'all' && !$search)
        <section class="space-y-4">
            <h2 class="text-lg font-black uppercase tracking-tight">Packs & Bundles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($bundles as $bundle)
                    <div class="bg-gradient-to-br from-brand-50 to-white border border-brand-200 rounded-3xl p-6 flex gap-4">
                        <div class="text-5xl">{{ $bundle->image }}</div>
                        <div class="flex-1">
                            @if($bundle->badge)<span class="text-[9px] font-black text-brand-600 uppercase">{{ $bundle->badge }}</span>@endif
                            <h3 class="font-black uppercase">{{ $bundle->title }}</h3>
                            <p class="text-xs text-zinc-600 mt-1">{{ $bundle->description }}</p>
                            <p class="text-xs text-emerald-600 font-bold mt-2">Poupa {{ $bundle->savings_percent }}%</p>
                            <div class="flex items-center gap-3 mt-4">
                                <span class="text-xl font-black">{{ number_format($bundle->price, 2, ',', '.') }} €</span>
                                <span class="text-xs text-zinc-400 line-through">{{ number_format($bundle->individualTotal(), 2, ',', '.') }} €</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- PRODUTOS --}}
    @if($products->isEmpty())
        <div class="py-20 text-center bg-zinc-50 border-2 border-dashed border-zinc-200 rounded-[3rem]">
            <flux:icon name="magnifying-glass" class="size-12 mx-auto mb-4 text-zinc-300" />
            <h3 class="text-xl font-black text-zinc-400 uppercase">Nenhum produto encontrado</h3>
            <button wire:click="clearFilters" class="mt-4 text-sm font-bold text-brand-600">Limpar filtros</button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($products as $product)
                <div class="group relative bg-zinc-50 border border-zinc-200 rounded-[2.5rem] overflow-hidden flex flex-col hover:border-brand-500 hover:shadow-xl transition-all" wire:key="product-{{ $product->id }}">
                    @if($product->badge)
                        <div class="absolute m-6 z-10">
                            <span class="px-3 py-1 bg-brand-600 text-white text-[9px] font-black uppercase rounded-full">{{ $product->badge }}</span>
                        </div>
                    @endif

                    <div class="h-44 bg-gradient-to-br from-zinc-100 to-zinc-200 flex items-center justify-center text-6xl relative">
                        <span class="group-hover:scale-110 transition-transform">{{ $product->image }}</span>
                        <div class="absolute top-4 right-4 flex gap-2">
                            <button wire:click="toggleWishlist({{ $product->id }})"
                                    class="p-2 bg-white/90 rounded-xl shadow hover:scale-110 transition-transform"
                                    title="Favoritos">
                                <flux:icon name="heart" class="size-4 {{ in_array($product->id, $wishlistIds) ? 'text-red-500 fill-red-500' : 'text-zinc-400' }}" />
                            </button>
                            <button wire:click="addToCompare({{ $product->id }})"
                                    class="p-2 bg-white/90 rounded-xl shadow hover:scale-110 transition-transform"
                                    title="Comparar">
                                <flux:icon name="scale" class="size-4 text-zinc-400" />
                            </button>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-1">
                        <span class="text-[9px] font-black text-brand-600 uppercase tracking-widest">{{ $product->category_label }}</span>
                        <h3 class="text-lg font-black uppercase leading-tight mt-1">{{ $product->title }}</h3>
                        <p class="text-xs text-zinc-600 mt-2 line-clamp-2">{{ $product->description }}</p>

                        @if($product->rating_count > 0)
                            <div class="flex items-center gap-2 mt-3">
                                <span class="text-amber-500 text-sm">★ {{ number_format($product->rating_avg, 1) }}</span>
                                <span class="text-[10px] text-zinc-400">({{ $product->rating_count }} reviews)</span>
                            </div>
                        @endif

                        <div class="mt-auto pt-4 flex items-center justify-between border-t border-zinc-200 mt-4">
                            <span class="text-2xl font-black italic">{{ number_format($product->price, 2, ',', '.') }} €</span>
                            <div class="flex flex-col gap-1.5">
                                <a href="{{ route('store.product.show', $product) }}" wire:navigate
                                   class="text-center px-3 py-2 bg-white border border-zinc-200 rounded-xl text-[9px] font-black uppercase">Ver</a>
                                <button wire:click="addToCart({{ $product->id }})"
                                        class="px-3 py-2 bg-zinc-200 rounded-xl text-[9px] font-black uppercase">+ Carrinho</button>
                                <button wire:click="buyNow({{ $product->id }})"
                                        class="px-3 py-2 bg-brand-600 text-white rounded-xl text-[9px] font-black uppercase">Comprar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- PLANOS --}}
    @if($activeTab === 'all' && $planProducts->isNotEmpty() && !$search)
        <section class="space-y-4">
            <h2 class="text-lg font-black uppercase">Planos PRO</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($planProducts as $index => $plan)
                    <div @class(['border rounded-3xl p-6', $index === 1 ? 'bg-zinc-200 border-zinc-400 shadow-lg' : 'bg-zinc-50 border-zinc-200'])>
                        <h3 class="font-black uppercase">{{ $plan->title }}</h3>
                        <p class="text-xs text-zinc-600 mt-2">{{ $plan->description }}</p>
                        <p class="text-2xl font-black mt-4">{{ number_format($plan->price, 2, ',', '.') }} €/mês</p>
                        <div class="flex gap-2 mt-4">
                            <button wire:click="addToCart({{ $plan->id }})" class="flex-1 py-2 bg-white border rounded-xl text-[9px] font-black uppercase">+ Carrinho</button>
                            <button wire:click="buyNow({{ $plan->id }})" class="flex-1 py-2 bg-zinc-900 text-white rounded-xl text-[9px] font-black uppercase">Subscrever</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</div>
