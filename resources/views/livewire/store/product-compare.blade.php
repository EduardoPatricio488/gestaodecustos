<div class="max-w-6xl mx-auto py-10 space-y-8">
    <header class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black uppercase italic">Comparar Produtos</h1>
            <p class="text-sm text-zinc-500">Até 4 produtos lado a lado</p>
        </div>
        @if($products->isNotEmpty())
            <button wire:click="clear" class="text-[10px] font-black uppercase text-red-500">Limpar tudo</button>
        @endif
    </header>

    @if($products->isEmpty())
        <div class="py-20 text-center border-2 border-dashed rounded-3xl">
            <flux:icon name="scale" class="size-12 mx-auto mb-4 text-zinc-300" />
            <p class="font-black text-zinc-400 uppercase">Nenhum produto para comparar</p>
            <flux:button href="{{ route('hub.store') }}" wire:navigate class="mt-6 rounded-xl uppercase font-black text-[10px]">Adicionar da Loja</flux:button>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="p-4 text-left text-[10px] font-black uppercase text-zinc-400"></th>
                        @foreach($products as $product)
                            <th class="p-4 text-center min-w-48" wire:key="compare-{{ $product->id }}">
                                <div class="text-4xl mb-2">{{ $product->image }}</div>
                                <p class="font-black uppercase text-sm">{{ $product->title }}</p>
                                <button wire:click="remove({{ $product->id }})" class="text-[9px] text-red-500 mt-2">Remover</button>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr class="border-t"><td class="p-4 font-bold">Preço</td>
                        @foreach($products as $p)<td class="p-4 text-center font-black">{{ number_format($p->price, 2, ',', '.') }} €</td>@endforeach
                    </tr>
                    <tr class="border-t"><td class="p-4 font-bold">Categoria</td>
                        @foreach($products as $p)<td class="p-4 text-center">{{ $p->category_label }}</td>@endforeach
                    </tr>
                    <tr class="border-t"><td class="p-4 font-bold">Avaliação</td>
                        @foreach($products as $p)<td class="p-4 text-center">{{ $p->rating_count > 0 ? '★ '.number_format($p->rating_avg, 1) : '—' }}</td>@endforeach
                    </tr>
                    <tr class="border-t"><td class="p-4 font-bold">Vendas</td>
                        @foreach($products as $p)<td class="p-4 text-center">{{ $p->sales_count }}</td>@endforeach
                    </tr>
                    <tr class="border-t"><td class="p-4 font-bold">Descrição</td>
                        @foreach($products as $p)<td class="p-4 text-center text-xs text-zinc-600">{{ \Illuminate\Support\Str::limit($p->description, 80) }}</td>@endforeach
                    </tr>
                    <tr class="border-t"><td class="p-4"></td>
                        @foreach($products as $p)
                            <td class="p-4 text-center">
                                <button wire:click="addToCart({{ $p->id }})" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-[9px] font-black uppercase">+ Carrinho</button>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
