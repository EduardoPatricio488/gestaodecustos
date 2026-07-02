<div class="space-y-10 pb-20 text-left">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter leading-none">O Meu Inventário</h1>
            <p class="text-sm text-zinc-500 mt-2">{{ $totalItems }} recursos • {{ number_format($totalSpent, 2, ',', '.') }} € investidos</p>
        </div>
        <flux:button href="{{ route('hub.store') }}" wire:navigate icon="plus" variant="primary" class="rounded-2xl uppercase font-black text-[10px] tracking-widest">
            Explorar Loja
        </flux:button>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($items as $item)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden flex flex-col shadow-sm">
                <div class="p-8 pb-4 flex justify-between items-start">
                    <div class="size-16 bg-zinc-50 rounded-2xl flex items-center justify-center text-4xl border">
                        {{ $item->product->image }}
                    </div>
                    <flux:badge variant="neutral" class="uppercase font-black text-[8px]">{{ $item->product->type }}</flux:badge>
                </div>

                <div class="px-8 pb-8 flex-1">
                    <h3 class="text-lg font-black uppercase leading-tight mb-2">{{ $item->product->title }}</h3>
                    <p class="text-xs text-zinc-500 mb-1">Adquirido em {{ $item->created_at->format('d/m/Y') }}</p>
                    <p class="text-xs text-zinc-500 mb-4">{{ number_format($item->amount_paid, 2, ',', '.') }} € • {{ $item->payment_method }}</p>

                    @if($item->license)
                        <p class="text-[9px] font-mono text-zinc-400 mb-4 truncate" title="{{ $item->license->license_key }}">
                            Licença: {{ $item->license->license_key }}
                        </p>
                    @endif

                    @if($item->product->type === 'course')
                        <flux:button href="{{ route('store.product.show', $item->product) }}" wire:navigate variant="primary" class="w-full rounded-xl uppercase font-black text-[10px]">
                            Aceder ao Curso
                        </flux:button>
                        @if($item->product->download_path)
                            <a href="{{ route('store.download.request', $item) }}"
                               class="mt-2 flex items-center justify-center gap-2 w-full py-2.5 bg-zinc-100 text-zinc-800 rounded-xl uppercase font-black text-[9px] tracking-widest hover:bg-zinc-200">
                                <flux:icon name="document-arrow-down" class="size-4" />
                                PDF Workbook
                            </a>
                        @endif
                    @elseif($item->product->type === 'guide')
                        <flux:button href="{{ route('store.product.show', $item->product) }}" wire:navigate variant="ghost" class="w-full rounded-xl uppercase font-black text-[10px]">
                            Ler Guia Online
                        </flux:button>
                        <a href="{{ route('store.download.request', $item) }}"
                           class="mt-2 flex items-center justify-center gap-2 w-full py-3 bg-emerald-600 text-white rounded-xl uppercase font-black text-[10px] tracking-widest hover:bg-emerald-500">
                            <flux:icon name="document-arrow-down" class="size-4" />
                            Descarregar PDF
                        </a>
                    @elseif($item->product->type === 'ia' || $item->product->type === 'widget')
                        <flux:button href="{{ route('dashboard') }}" wire:navigate variant="ghost" class="w-full rounded-xl uppercase font-black text-[10px]">
                            Ver no Dashboard
                        </flux:button>
                    @else
                        <a href="{{ route('store.download.request', $item) }}"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-zinc-900 text-white rounded-xl uppercase font-black text-[10px] tracking-widest hover:bg-zinc-700 transition-all">
                            <flux:icon name="cloud-arrow-down" class="size-4" />
                            Descarregar
                        </a>
                    @endif
                </div>

                <div class="px-8 py-4 bg-zinc-50/50 border-t flex items-center gap-2">
                    <div class="size-1.5 rounded-full bg-emerald-500"></div>
                    <span class="text-[9px] font-black uppercase text-zinc-400">Recurso Ativo</span>
                    @if($item->license)
                        <span class="text-[9px] text-zinc-400 ml-auto">{{ $item->license->download_count }} downloads</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 py-32 text-center bg-zinc-50 border-2 border-dashed border-zinc-200 rounded-[3rem]">
                <flux:icon name="archive-box" class="size-12 mx-auto mb-4 text-zinc-300" />
                <h3 class="text-xl font-black text-zinc-400 uppercase">Inventário Vazio</h3>
                <flux:button href="{{ route('hub.store') }}" wire:navigate variant="ghost" class="mt-8 uppercase font-black text-[10px]">Visitar Loja</flux:button>
            </div>
        @endforelse
    </div>
</div>
