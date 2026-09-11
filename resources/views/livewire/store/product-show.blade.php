<div class="max-w-6xl mx-auto py-6 sm:py-10 px-4 sm:px-6 space-y-8 pb-20">
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('hub.store') }}" wire:navigate class="text-[10px] font-black uppercase text-zinc-500 flex items-center gap-2 hover:text-zinc-900 dark:hover:text-white">
            <flux:icon name="arrow-left" class="size-4" /> Voltar à Loja
        </a>
        <div class="flex gap-2">
            <button wire:click="toggleWishlist({{ $product->id }})" aria-label="Adicionar aos favoritos" class="p-2.5 rounded-xl border dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <flux:icon name="heart" class="size-5" />
            </button>
            <button wire:click="addToCompare({{ $product->id }})" aria-label="Comparar produto" class="p-2.5 rounded-xl border dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <flux:icon name="scale" class="size-5" />
            </button>
            <a href="{{ route('store.cart') }}" wire:navigate aria-label="Carrinho" class="p-2.5 rounded-xl border dark:border-zinc-700 bg-white dark:bg-zinc-900 relative">
                <flux:icon name="shopping-cart" class="size-5" />
                @if($cartCount > 0)<span class="absolute -top-1 -right-1 size-4 bg-brand-500 text-white text-[8px] font-black rounded-full flex items-center justify-center">{{ $cartCount }}</span>@endif
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <main class="lg:col-span-2 space-y-6">
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 sm:p-8 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="text-[10px] font-black text-brand-600 uppercase tracking-widest">{{ $product->category_label }}</span>
                    <span class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase">{{ $product->audience_label }}</span>
                    @if($product->badge)<span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[9px] font-black uppercase">{{ $product->badge }}</span>@endif
                </div>
                <h1 class="text-3xl sm:text-4xl font-black uppercase italic tracking-tight">{{ $product->title }}</h1>
                <p class="text-sm text-zinc-500 mt-2">{{ $product->highlight }}</p>

                @if($product->rating_count > 0)
                    <div class="flex items-center gap-2 mt-4"><span class="text-amber-500 font-black">★ {{ number_format($product->rating_avg, 1) }}</span><span class="text-sm text-zinc-500">({{ $product->rating_count }} avaliações)</span></div>
                @endif

                <p class="text-zinc-700 dark:text-zinc-300 mt-6 leading-relaxed text-[15px]">{{ $product->description }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6">
                    @foreach([
                        ['O que é?', $product->category_label],
                        ['Onde aparece?', $product->integration_location ?: 'Dentro do Finance Pro AI, conforme a integração do produto.'],
                        ['Para quem é?', $product->audience_label],
                        ['Compatibilidade', is_array($product->compatibility) && count($product->compatibility) ? implode(' • ', $product->compatibility) : 'Finance Pro AI'],
                        ['O que recebo?', $product->delivery_label],
                        ['Depois da compra?', $product->delivery_type === 'download' ? 'Download imediato no inventário.' : 'Acesso ativado no teu inventário.'],
                    ] as [$label, $value])
                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 bg-zinc-50/70 dark:bg-zinc-950/40">
                            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">{{ $label }}</p>
                            <p class="text-sm font-semibold mt-1">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>

                @if($product->long_content)
                    <div class="mt-7 pt-7 border-t border-zinc-100 dark:border-zinc-800 prose prose-sm max-w-none text-zinc-600 dark:text-zinc-300">{!! nl2br(e($product->long_content)) !!}</div>
                @endif

                @if($product->objectives)
                    <div class="mt-7 pt-7 border-t border-zinc-100 dark:border-zinc-800">
                        <h2 class="text-lg font-black uppercase mb-4">O que vais conseguir fazer</h2>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach($product->objectives as $objective)
                                <div class="flex gap-2 text-sm"><flux:icon name="check-circle" class="size-5 text-emerald-500 shrink-0" /><span>{{ $objective }}</span></div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($product->features)
                    <div class="mt-7 pt-7 border-t border-zinc-100 dark:border-zinc-800">
                        <h2 class="text-lg font-black uppercase mb-4">Incluído</h2>
                        <div class="grid sm:grid-cols-2 gap-2">
                            @foreach($product->features as $feature)<div class="text-sm text-zinc-600 dark:text-zinc-300 py-1">✓ {{ $feature }}</div>@endforeach
                        </div>
                    </div>
                @endif

                @if($product->entitlements->isNotEmpty())
                    <div class="mt-7 pt-7 border-t border-zinc-100 dark:border-zinc-800">
                        <h2 class="text-lg font-black uppercase mb-3">Funcionalidade ativada</h2>
                        @foreach($product->entitlements->where('is_active', true) as $entitlement)
                            <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40">
                                <div><p class="font-black text-sm">{{ $entitlement->label ?: $entitlement->key }}</p><p class="text-xs text-zinc-500">{{ $entitlement->description ?: 'Acesso integrado no Finance Pro AI.' }}</p></div>
                                @if($alreadyOwned && $entitlement->route && \Illuminate\Support\Facades\Route::has($entitlement->route))
                                    <flux:button href="{{ route($entitlement->route) }}" wire:navigate size="sm" variant="primary">Abrir</flux:button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif($product->integration_route && \Illuminate\Support\Facades\Route::has($product->integration_route))
                    <div class="mt-7 pt-7 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40">
                            <div><p class="font-black text-sm">{{ $product->integration_label ?: 'Funcionalidade integrada' }}</p><p class="text-xs text-zinc-500">Disponível dentro da aplicação após a compra.</p></div>
                            @if($alreadyOwned)<flux:button href="{{ route($product->integration_route) }}" wire:navigate size="sm" variant="primary">Abrir funcionalidade</flux:button>@endif
                        </div>
                    </div>
                @endif

                @if(!$alreadyOwned)
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button wire:click="addToCart({{ $product->id }})" class="px-6 py-3 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl font-black uppercase text-[10px]">+ Carrinho</button>
                        <button wire:click="buyNow({{ $product->id }})" class="px-8 py-3 bg-emerald-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-lg">Comprar Agora</button>
                    </div>
                @else
                    <div class="mt-8 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900 rounded-2xl">
                        <flux:icon name="check-circle" class="text-emerald-600" />
                        <span class="font-bold text-emerald-700 dark:text-emerald-300">Já tens este produto no teu inventário.</span>
                        <flux:button href="{{ route('hub.inventory') }}" wire:navigate size="sm" class="ml-auto">Abrir inventário</flux:button>
                    </div>
                @endif
            </section>

            @if($product->type === 'course') @include('livewire.store.partials.course-content')
            @elseif($product->type === 'guide') @include('livewire.store.partials.guide-content')
            @endif

            @if($product->video_url)
                <section class="bg-zinc-950 rounded-3xl overflow-hidden aspect-video"><iframe src="{{ $product->video_url }}" class="w-full h-full" allowfullscreen loading="lazy" title="Demonstração de {{ $product->title }}"></iframe></section>
            @endif

            @if($product->screenshots)
                <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 sm:p-8">
                    <h2 class="text-lg font-black uppercase mb-4">Veja como funciona</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($product->screenshots as $shot)
                            @if(filter_var($shot, FILTER_VALIDATE_URL))
                                <img src="{{ $shot }}" alt="Screenshot de {{ $product->title }}" loading="lazy" class="w-full aspect-video object-cover rounded-2xl border border-zinc-200 dark:border-zinc-800">
                            @else
                                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-sm text-zinc-500 p-4 text-center">{{ $shot }}</div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            @if($product->roadmap)
                <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 sm:p-8">
                    <h2 class="text-lg font-black uppercase mb-4">Roadmap</h2>
                    <div class="space-y-4">@foreach($product->roadmap as $item)<div class="flex gap-4"><div class="w-20 text-[10px] font-black uppercase text-zinc-400">{{ $item['date'] ?? '' }}</div><div class="flex-1 pb-4 border-l-2 border-brand-200 pl-4"><p class="font-bold text-sm">{{ $item['title'] ?? '' }}</p><p class="text-xs text-zinc-500">{{ $item['desc'] ?? '' }}</p></div></div>@endforeach</div>
                </section>
            @endif

            @if($product->faq)
                <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 sm:p-8" x-data="{ open: null }">
                    <h2 class="text-lg font-black uppercase mb-4">FAQ</h2>
                    @foreach($product->faq as $i => $faq)<div class="border-b border-zinc-100 dark:border-zinc-800 py-3"><button @click="open = open === {{ $i }} ? null : {{ $i }}" class="w-full text-left font-bold text-sm flex justify-between">{{ $faq['q'] ?? '' }}<span x-text="open === {{ $i }} ? '−' : '+'"></span></button><p x-show="open === {{ $i }}" x-collapse class="text-sm text-zinc-600 dark:text-zinc-300 mt-2">{{ $faq['a'] ?? '' }}</p></div>@endforeach
                </section>
            @endif

            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 sm:p-8">
                <h2 class="text-xl font-black uppercase mb-6">Avaliações ({{ $reviews->count() }})</h2>
                @if($alreadyOwned)
                    <div class="mb-8 p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl space-y-4">
                        <p class="text-sm font-bold">Deixa a tua avaliação</p>
                        <div class="flex gap-2">@for($i = 1; $i <= 5; $i++)<button wire:click="$set('reviewRating', {{ $i }})" class="text-2xl {{ $reviewRating >= $i ? 'text-amber-400' : 'text-zinc-300' }}">★</button>@endfor</div>
                        <textarea wire:model="reviewComment" rows="3" placeholder="Comentário (opcional)" class="w-full px-4 py-3 border rounded-xl text-sm bg-white dark:bg-zinc-900"></textarea>
                        <flux:button wire:click="submitReview" size="sm" class="rounded-xl font-black uppercase text-[10px]">Publicar</flux:button>
                    </div>
                @endif
                <div class="space-y-4">@forelse($reviews as $review)<div class="p-4 border border-zinc-200 dark:border-zinc-800 rounded-2xl"><div class="flex flex-wrap items-center gap-3"><span class="font-bold text-sm">{{ $review->user->name }}</span><span class="text-amber-400 text-sm">{{ str_repeat('★', $review->rating) }}</span><span class="text-[10px] text-zinc-400">{{ $review->created_at->diffForHumans() }}</span></div>@if($review->comment)<p class="text-sm text-zinc-600 dark:text-zinc-300 mt-2">{{ $review->comment }}</p>@endif</div>@empty<p class="text-sm text-zinc-400 text-center py-8">Ainda sem avaliações. Sê o primeiro!</p>@endforelse</div>
            </section>
        </main>

        <aside class="space-y-5">
            <div class="bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 text-center lg:sticky lg:top-4">
                <div class="text-7xl mb-4">{{ $product->image }}</div>
                <p class="text-3xl font-black italic">{{ number_format($product->price, 2, ',', '.') }} €</p>
                @if($product->points_reward > 0)<p class="text-xs text-amber-600 font-bold mt-2">+{{ $product->points_reward }} pontos</p>@endif
                <div class="mt-5 pt-5 border-t border-zinc-200 dark:border-zinc-700 text-left space-y-3 text-xs"><div class="flex justify-between"><span class="text-zinc-500">Tipo</span><strong>{{ $product->category_label }}</strong></div><div class="flex justify-between"><span class="text-zinc-500">Público</span><strong>{{ $product->audience_label }}</strong></div><div class="flex justify-between"><span class="text-zinc-500">Entrega</span><strong>{{ $product->delivery_label }}</strong></div></div>
                @if($alreadyOwned)<flux:button href="{{ route('hub.inventory') }}" wire:navigate variant="primary" class="w-full mt-5 rounded-xl uppercase font-black text-[10px]">Abrir inventário</flux:button>@endif
            </div>
        </aside>
    </div>

    @if($relatedProducts->isNotEmpty())
        <section><h2 class="text-lg font-black uppercase mb-4">Também podes gostar</h2><div class="grid grid-cols-1 md:grid-cols-3 gap-4">@foreach($relatedProducts as $related)<a href="{{ route('store.product.show', $related) }}" wire:navigate class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl hover:border-brand-500 transition-all"><span class="text-3xl">{{ $related->image }}</span><p class="font-black text-sm uppercase mt-2">{{ $related->title }}</p><p class="text-brand-600 font-black">{{ number_format($related->price, 2, ',', '.') }} €</p></a>@endforeach</div></section>
    @endif
</div>
