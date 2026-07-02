<div class="max-w-5xl mx-auto py-10 space-y-10">

    <div class="flex items-center justify-between">
        <a href="{{ route('hub.store') }}" wire:navigate class="text-[10px] font-black uppercase text-zinc-500 flex items-center gap-2">
            <flux:icon name="arrow-left" class="size-4" /> Voltar à Loja
        </a>
        <div class="flex gap-2">
            <button wire:click="toggleWishlist({{ $product->id }})" class="p-2.5 rounded-xl border {{ $inWishlist ? 'bg-red-50 border-red-200 text-red-500' : 'bg-white border-zinc-200' }}">
                <flux:icon name="heart" class="size-5" />
            </button>
            <button wire:click="addToCompare({{ $product->id }})" class="p-2.5 rounded-xl border bg-white border-zinc-200">
                <flux:icon name="scale" class="size-5" />
            </button>
            <a href="{{ route('store.cart') }}" wire:navigate class="p-2.5 rounded-xl border bg-white border-zinc-200 relative">
                <flux:icon name="shopping-cart" class="size-5" />
                @if($cartCount > 0)<span class="absolute -top-1 -right-1 size-4 bg-brand-500 text-white text-[8px] font-black rounded-full flex items-center justify-center">{{ $cartCount }}</span>@endif
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-xl">
                <span class="text-[10px] font-black text-brand-600 uppercase tracking-widest">{{ $product->category_label }}</span>
                <h1 class="text-3xl font-black uppercase italic mt-2">{{ $product->title }}</h1>

                @if($product->rating_count > 0)
                    <div class="flex items-center gap-2 mt-3">
                        <span class="text-amber-500 font-black">★ {{ number_format($product->rating_avg, 1) }}</span>
                        <span class="text-sm text-zinc-500">({{ $product->rating_count }} avaliações)</span>
                    </div>
                @endif

                <p class="text-zinc-700 mt-4 leading-relaxed">{{ $product->description }}</p>

                @if($product->long_content)
                    <div class="mt-6 prose prose-sm max-w-none text-zinc-600">{!! nl2br(e($product->long_content)) !!}</div>
                @endif

                <div class="mt-4 p-4 bg-brand-50 border border-brand-100 rounded-2xl">
                    <p class="text-[10px] font-black uppercase text-brand-600 mb-1">Recomendação IA</p>
                    <p class="text-sm text-brand-900">{{ $aiExplanation }}</p>
                </div>

                @if(!$alreadyOwned)
                    <div class="mt-8 flex flex-wrap gap-3">
                        <button wire:click="addToCart({{ $product->id }})" class="px-6 py-3 bg-zinc-100 border rounded-2xl font-black uppercase text-[10px]">+ Carrinho</button>
                        <button wire:click="buyNow({{ $product->id }})" class="px-8 py-3 bg-emerald-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-lg">Comprar Agora</button>
                    </div>
                @else
                    <div class="mt-8 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                        <flux:icon name="check-circle" class="text-emerald-600" />
                        <span class="font-bold text-emerald-700">Já tens este produto</span>
                        <flux:button href="{{ route('hub.inventory') }}" wire:navigate size="sm" class="ml-auto">Inventário</flux:button>
                    </div>
                @endif
            </div>

            @if($product->type === 'course')
                @include('livewire.store.partials.course-content')
            @elseif($product->type === 'guide')
                @include('livewire.store.partials.guide-content')
            @else
                @if($product->video_url)
                    <div class="bg-zinc-900 rounded-3xl overflow-hidden aspect-video">
                        <iframe src="{{ $product->video_url }}" class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                    </div>
                @endif

                @if($product->screenshots)
                    <div class="bg-white border rounded-3xl p-8">
                        <h2 class="text-lg font-black uppercase mb-4">Screenshots / Demo</h2>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($product->screenshots as $shot)
                                <div class="aspect-video bg-zinc-100 rounded-2xl flex items-center justify-center text-sm text-zinc-500 p-4 text-center">{{ $shot }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            @if($product->roadmap)
                <div class="bg-white border rounded-3xl p-8">
                    <h2 class="text-lg font-black uppercase mb-4">Roadmap</h2>
                    <div class="space-y-4">
                        @foreach($product->roadmap as $item)
                            <div class="flex gap-4">
                                <div class="w-20 text-[10px] font-black uppercase text-zinc-400">{{ $item['date'] ?? '' }}</div>
                                <div class="flex-1 pb-4 border-l-2 border-brand-200 pl-4">
                                    <p class="font-bold text-sm">{{ $item['title'] ?? '' }}</p>
                                    <p class="text-xs text-zinc-500">{{ $item['desc'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($product->faq)
                <div class="bg-white border rounded-3xl p-8" x-data="{ open: null }">
                    <h2 class="text-lg font-black uppercase mb-4">FAQ</h2>
                    @foreach($product->faq as $i => $faq)
                        <div class="border-b border-zinc-100 py-3">
                            <button @click="open = open === {{ $i }} ? null : {{ $i }}" class="w-full text-left font-bold text-sm flex justify-between">
                                {{ $faq['q'] ?? '' }}
                                <span x-text="open === {{ $i }} ? '−' : '+'"></span>
                            </button>
                            <p x-show="open === {{ $i }}" x-collapse class="text-sm text-zinc-600 mt-2">{{ $faq['a'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-zinc-100 rounded-3xl p-6 text-center sticky top-4">
                <div class="text-7xl mb-4">{{ $product->image }}</div>
                <p class="text-3xl font-black italic">{{ number_format($product->price, 2, ',', '.') }} €</p>
                @if($product->points_reward > 0)
                    <p class="text-xs text-amber-600 font-bold mt-2">+{{ $product->points_reward }} pontos</p>
                @endif
            </div>

            @if($product->features)
                <div class="bg-white border rounded-3xl p-6">
                    <h3 class="font-black uppercase text-sm mb-3">Incluído</h3>
                    @foreach($product->features as $feature)
                        <p class="text-sm text-zinc-600 py-1">{{ $feature }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- REVIEWS --}}
    <div class="bg-white border rounded-3xl p-8">
        <h2 class="text-xl font-black uppercase mb-6">Avaliações ({{ $reviews->count() }})</h2>

        @if($alreadyOwned)
            <div class="mb-8 p-6 bg-zinc-50 rounded-2xl space-y-4">
                <p class="text-sm font-bold">Deixa a tua avaliação</p>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button wire:click="$set('reviewRating', {{ $i }})" class="text-2xl {{ $reviewRating >= $i ? 'text-amber-400' : 'text-zinc-300' }}">★</button>
                    @endfor
                </div>
                <textarea wire:model="reviewComment" rows="3" placeholder="Comentário (opcional)" class="w-full px-4 py-3 border rounded-xl text-sm"></textarea>
                <flux:button wire:click="submitReview" size="sm" class="rounded-xl font-black uppercase text-[10px]">Publicar</flux:button>
            </div>
        @endif

        <div class="space-y-4">
            @forelse($reviews as $review)
                <div class="p-4 border rounded-2xl">
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-sm">{{ $review->user->name }}</span>
                        <span class="text-amber-400 text-sm">{{ str_repeat('★', $review->rating) }}</span>
                        <span class="text-[10px] text-zinc-400">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    @if($review->comment)<p class="text-sm text-zinc-600 mt-2">{{ $review->comment }}</p>@endif
                </div>
            @empty
                <p class="text-sm text-zinc-400 text-center py-8">Ainda sem avaliações. Sê o primeiro!</p>
            @endforelse
        </div>
    </div>

    @if($relatedProducts->isNotEmpty())
        <div>
            <h2 class="text-lg font-black uppercase mb-4">Produtos relacionados</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('store.product.show', $related) }}" wire:navigate class="p-4 bg-white border rounded-2xl hover:border-brand-500 transition-all">
                        <span class="text-3xl">{{ $related->image }}</span>
                        <p class="font-black text-sm uppercase mt-2">{{ $related->title }}</p>
                        <p class="text-brand-600 font-black">{{ number_format($related->price, 2, ',', '.') }} €</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
