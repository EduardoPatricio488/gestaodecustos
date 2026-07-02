<div class="space-y-8 pb-20">
    {{-- HEADER --}}
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 px-2">
        <div class="flex items-center gap-5">
            <div class="p-5 bg-emerald-600 rounded-[2rem] shadow-xl shadow-emerald-500/20">
                <flux:icon name="shopping-bag" class="size-10 text-white" />
            </div>
            <div>
                <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Gestão da Loja</h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Produtos, vendas, tendências e personalização do marketplace</p>
            </div>
        </div>
        <a href="{{ route('hub.store') }}" wire:navigate target="_blank"
           class="px-5 py-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-emerald-500 transition-colors">
            Ver Loja Pública →
        </a>
    </header>

    {{-- NAVEGAÇÃO INTERNA --}}
    <div class="flex flex-wrap gap-2 p-1.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm">
        @foreach([
            'overview' => ['icon' => 'chart-bar', 'label' => 'Estatísticas'],
            'products' => ['icon' => 'cube', 'label' => 'Produtos'],
            'purchases' => ['icon' => 'receipt-percent', 'label' => 'Compras'],
            'tabs' => ['icon' => 'squares-2x2', 'label' => 'Abas da Loja'],
        ] as $key => $meta)
            <button wire:click="setSection('{{ $key }}')"
                @class([
                    'flex items-center gap-2 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all',
                    'bg-emerald-600 text-white shadow-lg' => $section === $key,
                    'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800' => $section !== $key,
                ])>
                <flux:icon :name="$meta['icon']" class="size-4" />
                {{ $meta['label'] }}
            </button>
        @endforeach
    </div>

    {{-- ═══ ESTATÍSTICAS ═══ --}}
    @if($section === 'overview')
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
            <div class="p-6 bg-zinc-950 text-white rounded-[2rem] border border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400 mb-2">Receita Total</p>
                <p class="text-4xl font-black italic">{{ number_format($stats['total_revenue'], 2, ',', ' ') }} €</p>
                <p class="text-[9px] text-zinc-500 mt-2 uppercase">{{ $stats['total_purchases'] }} transações</p>
            </div>
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Este Mês</p>
                <p class="text-4xl font-black italic dark:text-white">{{ number_format($stats['revenue_month'], 2, ',', ' ') }} €</p>
                <p class="text-[9px] text-zinc-500 mt-2 uppercase">{{ $stats['purchases_month'] }} compras</p>
            </div>
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Hoje</p>
                <p class="text-4xl font-black italic dark:text-white">{{ $stats['purchases_today'] }}</p>
                <p class="text-[9px] text-zinc-500 mt-2 uppercase">{{ number_format($stats['revenue_today'], 2, ',', ' ') }} €</p>
            </div>
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Compradores Únicos</p>
                <p class="text-4xl font-black italic dark:text-white">{{ $stats['unique_buyers'] }}</p>
                <p class="text-[9px] text-zinc-500 mt-2 uppercase">Ticket médio {{ number_format($stats['avg_order'], 2, ',', ' ') }} €</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Tendência 14 dias --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8">
                <h2 class="text-lg font-black uppercase italic mb-6">Tendência (14 dias)</h2>
                <div class="flex items-end gap-1.5 h-40">
                    @foreach($trend as $point)
                        <div class="flex-1 flex flex-col items-center gap-1 group">
                            <span class="text-[8px] font-bold text-zinc-400 opacity-0 group-hover:opacity-100 transition-opacity">{{ $point['count'] }}</span>
                            <div class="w-full bg-emerald-500/80 rounded-t-md transition-all group-hover:bg-emerald-500"
                                 style="height: {{ max(4, ($point['count'] / $maxTrend) * 100) }}%"
                                 title="{{ $point['label'] }}: {{ $point['count'] }} vendas"></div>
                            <span class="text-[7px] font-black text-zinc-400 uppercase">{{ $point['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Por tipo (compras reais) --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8">
                <h2 class="text-lg font-black uppercase italic mb-1">Vendas por Categoria</h2>
                <p class="text-[10px] text-zinc-400 uppercase tracking-widest mb-6">Compras reais registadas na plataforma</p>
                <div class="space-y-4">
                    @forelse($salesByType as $row)
                        @php $typeLabel = $productTypes[$row->type] ?? ucfirst($row->type); @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold uppercase text-[10px]">{{ $typeLabel }}</span>
                                <span class="font-black text-emerald-600">{{ $row->total_sales }} vendas</span>
                            </div>
                            <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full"
                                     style="width: {{ $salesByType->max('total_sales') > 0 ? ($row->total_sales / $salesByType->max('total_sales')) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-400 text-sm italic">Sem dados de vendas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Top produtos --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden">
            <div class="p-6 border-b border-zinc-100 dark:border-zinc-800">
                <h2 class="text-lg font-black uppercase italic">Produtos Mais Vendidos</h2>
                <p class="text-[10px] text-zinc-400 uppercase tracking-widest mt-1">Por compras reais na plataforma</p>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach($topProducts as $i => $product)
                    <div class="flex items-center gap-4 p-5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <span class="text-2xl font-black text-zinc-300 w-8">#{{ $i + 1 }}</span>
                        <span class="text-3xl">{{ $product->image }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-black uppercase text-sm truncate">{{ $product->title }}</p>
                            <p class="text-[10px] text-zinc-500 uppercase">{{ $product->category_label }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-emerald-600">{{ $product->real_sales }} {{ $product->real_sales === 1 ? 'venda' : 'vendas' }}</p>
                            @if($product->rating_count > 0)
                                <p class="text-[10px] text-amber-500">★ {{ number_format($product->rating_avg, 1) }}</p>
                            @endif
                        </div>
                        <p class="font-black text-lg">{{ number_format($product->price, 2, ',', ' ') }} €</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══ PRODUTOS ═══ --}}
    @if($section === 'products')
        <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
            <input wire:model.live.debounce.300ms="productSearch" type="search" placeholder="Pesquisar produtos..."
                   class="w-full sm:w-80 px-4 py-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-sm" />
            <flux:button wire:click="startCreate" variant="primary" icon="plus" class="rounded-2xl font-black uppercase text-[10px] bg-emerald-600">
                Novo Produto
            </flux:button>
        </div>

        @if($editingProductId !== null)
            <div class="bg-white dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-[2.5rem] p-8 space-y-6">
                <h2 class="text-xl font-black uppercase italic">{{ $editingProductId ? 'Editar Produto' : 'Novo Produto' }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Título</label>
                        <input wire:model="title" class="w-full h-12 px-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950" />
                        @error('title') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Slug (URL)</label>
                        <input wire:model="slug" placeholder="auto se vazio" class="w-full h-12 px-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Tipo / Aba</label>
                        <select wire:model="type" class="w-full h-12 px-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950">
                            @foreach($productTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Preço (€)</label>
                        <input wire:model="price" type="number" step="0.01" min="0" class="w-full h-12 px-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950" />
                        @error('price') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Emoji / Ícone</label>
                        <input wire:model="image" maxlength="20" class="w-full h-12 px-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-2xl" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Badge</label>
                        <input wire:model="badge" placeholder="Ex: Novo, Mais Vendido" class="w-full h-12 px-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950" />
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Descrição curta</label>
                        <textarea wire:model="description" rows="2" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950"></textarea>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Conteúdo longo (opcional)</label>
                        <textarea wire:model="long_content" rows="3" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950"></textarea>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-zinc-400">Funcionalidades (uma por linha)</label>
                        <textarea wire:model="featuresRaw" rows="4" placeholder="📈 Previsões de saldo&#10;🔔 Alertas automáticos" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 font-mono text-sm"></textarea>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_featured" class="rounded text-emerald-600" />
                            <span class="text-sm font-bold">Destaque na loja</span>
                        </label>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-zinc-400">Pontos XP</label>
                            <input wire:model="points_reward" type="number" min="0" class="w-24 h-10 px-3 rounded-xl border border-zinc-200 dark:border-zinc-800" />
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button wire:click="cancelEdit" variant="ghost" class="rounded-xl uppercase font-black text-[10px]">Cancelar</flux:button>
                    <flux:button wire:click="saveProduct" variant="primary" class="rounded-xl uppercase font-black text-[10px] bg-emerald-600">Guardar Produto</flux:button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4">
            @forelse($products as $product)
                <div wire:key="admin-product-{{ $product->id }}" class="flex flex-col sm:flex-row sm:items-center gap-4 p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem]">
                    <span class="text-4xl">{{ $product->image }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-black uppercase">{{ $product->title }}</p>
                            @if($product->is_featured)
                                <span class="text-[8px] font-black bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full uppercase">Destaque</span>
                            @endif
                            @if($product->badge)
                                <span class="text-[8px] font-black bg-brand-100 text-brand-700 px-2 py-0.5 rounded-full uppercase">{{ $product->badge }}</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-zinc-500 uppercase mt-1">{{ $product->category_label }} · {{ $product->sales_count }} vendas · ★ {{ number_format($product->rating_avg, 1) }}</p>
                    </div>
                    <p class="text-xl font-black">{{ number_format($product->price, 2, ',', ' ') }} €</p>
                    <div class="flex gap-2">
                        <button wire:click="toggleFeatured({{ $product->id }})" class="p-2 rounded-xl border border-zinc-200 hover:bg-amber-50" title="Destaque">
                            <flux:icon name="star" class="size-4 {{ $product->is_featured ? 'text-amber-500' : 'text-zinc-400' }}" />
                        </button>
                        <button wire:click="startEdit({{ $product->id }})" class="p-2 rounded-xl border border-zinc-200 hover:bg-zinc-50">
                            <flux:icon name="pencil" class="size-4 text-zinc-500" />
                        </button>
                        <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Eliminar '{{ $product->title }}'?" class="p-2 rounded-xl border border-red-200 hover:bg-red-50">
                            <flux:icon name="trash" class="size-4 text-red-500" />
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-center py-16 text-zinc-400 font-bold uppercase text-sm">Nenhum produto encontrado.</p>
            @endforelse
        </div>
    @endif

    {{-- ═══ COMPRAS ═══ --}}
    @if($section === 'purchases')
        <div class="flex gap-3">
            <input wire:model.live.debounce.300ms="purchaseSearch" type="search" placeholder="Pesquisar por utilizador ou produto..."
                   class="flex-1 max-w-md px-4 py-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-sm" />
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/50 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                        <tr>
                            <th class="text-left p-4">Data</th>
                            <th class="text-left p-4">Utilizador</th>
                            <th class="text-left p-4">Produto</th>
                            <th class="text-left p-4">Pagamento</th>
                            <th class="text-right p-4">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($purchases as $purchase)
                            <tr wire:key="purchase-{{ $purchase->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="p-4 whitespace-nowrap text-zinc-500">{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-4">
                                    <p class="font-bold">{{ $purchase->user?->name ?? '—' }}</p>
                                    <p class="text-[10px] text-zinc-400">{{ $purchase->user?->email }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $purchase->product?->image }}</span>
                                        <span class="font-medium">{{ $purchase->product?->title ?? 'Produto removido' }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-[10px] font-black uppercase px-2 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800">{{ $purchase->payment_method }}</span>
                                    @if($purchase->coupon_code)
                                        <span class="text-[9px] text-emerald-600 block mt-1">{{ $purchase->coupon_code }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right font-black text-emerald-600">{{ number_format($purchase->amount_paid, 2, ',', ' ') }} €</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-12 text-center text-zinc-400 italic">Sem compras registadas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($purchases->hasPages())
                <div class="p-4 border-t border-zinc-100 dark:border-zinc-800">{{ $purchases->links() }}</div>
            @endif
        </div>
    @endif

    {{-- ═══ ABAS DA LOJA ═══ --}}
    @if($section === 'tabs')
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between gap-4">
                <div>
                    <h2 class="text-xl font-black uppercase italic">Personalizar Abas da Loja</h2>
                    <p class="text-sm text-zinc-500 mt-1">Define quais categorias aparecem na loja pública e a ordem de exibição.</p>
                </div>
                <div class="flex gap-2">
                    <flux:button wire:click="resetStoreTabs" variant="ghost" class="rounded-xl text-[10px] font-black uppercase">Repor Padrão</flux:button>
                    <flux:button wire:click="saveStoreTabs" variant="primary" class="rounded-xl text-[10px] font-black uppercase bg-emerald-600">Guardar Abas</flux:button>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($storeTabs as $index => $tab)
                    <div wire:key="store-tab-{{ $tab['key'] }}" class="flex flex-wrap items-center gap-3 p-4 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                        <div class="flex flex-col gap-1">
                            <button type="button" wire:click="moveTabUp({{ $index }})" class="p-1 text-zinc-400 hover:text-zinc-700" @if($index === 0) disabled @endif>▲</button>
                            <button type="button" wire:click="moveTabDown({{ $index }})" class="p-1 text-zinc-400 hover:text-zinc-700" @if($index === count($storeTabs) - 1) disabled @endif>▼</button>
                        </div>
                        <input wire:model="storeTabs.{{ $index }}.label" class="flex-1 min-w-[120px] h-10 px-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 font-bold" />
                        <code class="text-[10px] font-mono text-zinc-400 px-2">{{ $tab['key'] }}</code>
                        <label class="flex items-center gap-2 cursor-pointer ml-auto">
                            <input type="checkbox" wire:model="storeTabs.{{ $index }}.visible" class="rounded text-emerald-600" />
                            <span class="text-[10px] font-black uppercase">Visível</span>
                        </label>
                    </div>
                @endforeach
            </div>

            <p class="text-[10px] text-zinc-400 italic">A aba «Todos» mostra todos os produtos exceto planos. A aba «Planos» lista subscrições mensais.</p>
        </div>
    @endif
</div>
