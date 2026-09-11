<div class="space-y-8 pb-20 text-left">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
        <div>
            <span class="text-[10px] font-black uppercase tracking-widest text-brand-600">A tua biblioteca</span>
            <h1 class="text-3xl sm:text-4xl font-black uppercase italic tracking-tight mt-1">Os meus produtos & funcionalidades</h1>
            <p class="text-sm text-zinc-500 mt-2">{{ $totalItems }} produtos ativos • {{ number_format($totalSpent, 2, ',', '.') }} € investidos</p>
        </div>
        <flux:button href="{{ route('hub.store') }}" wire:navigate icon="plus" variant="primary" class="rounded-2xl uppercase font-black text-[10px] tracking-widest">Explorar Loja</flux:button>
    </header>

    @if($entitlements->isNotEmpty())
        <section class="bg-zinc-950 text-white rounded-[2rem] p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Acesso integrado</p><h2 class="text-xl font-black uppercase">Funcionalidades que já tens</h2></div>
                <flux:icon name="bolt" class="size-6 text-emerald-400" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($entitlements as $feature)
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                        <div class="flex items-center gap-3"><span class="text-2xl">{{ $feature->image ?: '⚡' }}</span><div class="min-w-0"><p class="font-black text-sm truncate">{{ $feature->integration_label ?: $feature->title }}</p><p class="text-[10px] text-zinc-400">{{ $feature->integration_location ?: 'Finance Pro AI' }}</p></div></div>
                        @if($feature->integration_route && \Illuminate\Support\Facades\Route::has($feature->integration_route))
                            <flux:button href="{{ route($feature->integration_route) }}" wire:navigate size="sm" variant="primary" class="w-full mt-3">Abrir funcionalidade</flux:button>
                        @else
                            <p class="text-[10px] text-zinc-500 mt-3">Ativada automaticamente na aplicação.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($items as $item)
            @php($product = $item->product)
            <article class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] overflow-hidden flex flex-col shadow-sm">
                <div class="p-6 flex items-start justify-between gap-4">
                    <div class="size-14 bg-zinc-50 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-3xl border border-zinc-200 dark:border-zinc-700">{{ $product->image ?: '📦' }}</div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[8px] font-black uppercase">Ativo</span>
                </div>
                <div class="px-6 pb-6 flex-1">
                    <div class="text-[9px] font-black uppercase tracking-widest text-brand-600">{{ $product->category_label }}</div>
                    <h3 class="text-lg font-black uppercase leading-tight mt-1">{{ $product->title }}</h3>
                    <p class="text-xs text-zinc-500 mt-2">{{ $product->highlight }}</p>
                    <div class="grid grid-cols-2 gap-2 mt-5 text-[10px]"><div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800"><span class="text-zinc-400 block">Acesso</span><strong>{{ $product->delivery_label }}</strong></div><div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800"><span class="text-zinc-400 block">Adquirido</span><strong>{{ $item->created_at->format('d/m/Y') }}</strong></div></div>
                    @if($item->license)<p class="text-[9px] font-mono text-zinc-400 mt-4 truncate" title="{{ $item->license->license_key }}">Licença: {{ $item->license->license_key }}</p>@endif
                </div>
                <div class="px-6 pb-6 space-y-2">
                    @if($product->type === 'course')
                        <flux:button href="{{ route('store.product.show', $product) }}" wire:navigate variant="primary" class="w-full rounded-xl uppercase font-black text-[10px]">Começar curso</flux:button>
                    @elseif($product->type === 'guide')
                        <flux:button href="{{ route('store.product.show', $product) }}" wire:navigate variant="primary" class="w-full rounded-xl uppercase font-black text-[10px]">Ler guia</flux:button>
                    @elseif($product->integration_route && \Illuminate\Support\Facades\Route::has($product->integration_route))
                        <flux:button href="{{ route($product->integration_route) }}" wire:navigate variant="primary" class="w-full rounded-xl uppercase font-black text-[10px]">Abrir funcionalidade</flux:button>
                    @else
                        <flux:button href="{{ route('store.product.show', $product) }}" wire:navigate variant="ghost" class="w-full rounded-xl uppercase font-black text-[10px]">Ver produto</flux:button>
                    @endif

                    @if($product->download_path)
                        <a href="{{ route('store.download.request', $item) }}" class="flex items-center justify-center gap-2 w-full py-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-xl uppercase font-black text-[9px] tracking-widest hover:bg-zinc-200 dark:hover:bg-zinc-700"><flux:icon name="document-arrow-down" class="size-4" /> Descarregar</a>
                    @endif
                </div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 py-24 text-center bg-zinc-50 dark:bg-zinc-900 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                <flux:icon name="archive-box" class="size-12 mx-auto mb-4 text-zinc-300" />
                <h3 class="text-xl font-black text-zinc-400 uppercase">Ainda não tens produtos</h3>
                <p class="text-sm text-zinc-500 mt-2">Compra um recurso na Store e ele aparece aqui automaticamente.</p>
                <flux:button href="{{ route('hub.store') }}" wire:navigate variant="ghost" class="mt-6 uppercase font-black text-[10px]">Visitar Loja</flux:button>
            </div>
        @endforelse
    </div>
</div>
