{{-- GUIA: texto completo + PDF --}}
<div class="space-y-6">
    <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
        <flux:icon name="document-text" class="size-6 text-amber-600" />
        <div>
            <p class="text-[10px] font-black uppercase text-amber-700">Formato do guia</p>
            <p class="text-sm text-amber-900">Leitura online + download PDF</p>
        </div>
    </div>

    <div class="bg-white border rounded-3xl p-8">
        <h2 class="text-lg font-black uppercase mb-6">Conteúdo do guia</h2>

        @if($alreadyOwned)
            <div class="prose prose-sm max-w-none text-zinc-700 leading-relaxed whitespace-pre-line">{{ $product->long_content }}</div>

            @if($ownedPurchase)
                <div class="mt-8 p-6 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="size-12 bg-emerald-600 rounded-xl flex items-center justify-center text-white text-2xl">📄</div>
                        <div>
                            <p class="font-black uppercase text-sm">Versão PDF</p>
                            <p class="text-xs text-zinc-500">Formato imprimível e offline</p>
                        </div>
                    </div>
                    <a href="{{ route('store.download.request', $ownedPurchase) }}"
                       class="px-6 py-3 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 shadow-lg">
                        Descarregar PDF
                    </a>
                </div>
            @endif
        @else
            <div class="relative">
                <div class="prose prose-sm max-w-none text-zinc-600 leading-relaxed whitespace-pre-line line-clamp-[12]">
                    {{ $product->long_content }}
                </div>
                <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-white to-transparent"></div>
            </div>
            <div class="mt-4 p-4 bg-zinc-50 border border-dashed border-zinc-300 rounded-2xl text-center">
                <flux:icon name="lock-closed" class="size-6 mx-auto text-zinc-400 mb-2" />
                <p class="text-sm font-bold text-zinc-500">Compra o guia para ler o texto completo e descarregar o PDF</p>
            </div>
        @endif
    </div>

    @if($product->features)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($product->features as $feature)
                <div class="p-4 bg-zinc-50 border rounded-2xl text-sm font-medium text-center">{{ $feature }}</div>
            @endforeach
        </div>
    @endif
</div>
