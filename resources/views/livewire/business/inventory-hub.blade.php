<div class="space-y-10 pb-20">
    {{-- 1. HEADER LOGÍSTICO (ESTILO SaaS PREMIUM) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-[0_12px_40px_rgba(15,23,42,0.25)] text-brand-600">
                        <flux:icon name="archive-box" class="w-10 h-10" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">
                            Stock & Inventário
                        </h1>
                        <flux:badge
                            variant="neutral"
                            class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">
                            Logística Ativa
                        </flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">
                        Controlo de mercadorias, valor de armazém e reposição
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
                <div class="relative flex-1 min-w-[240px]">
                    <flux:input
                        wire:model.live="search"
                        icon="magnifying-glass"
                        placeholder="Procurar artigo..."
                        class="!bg-transparent border-none shadow-none"
                    />
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:modal.trigger name="product-modal">
                    <flux:button
                        variant="primary"
                        icon="plus"
                        class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20 hover:shadow-[0_0_25px_rgba(99,102,241,0.35)] transition-all">
                        Novo Artigo
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- 2. KPIs DE INVENTÁRIO (COM PRIVACIDADE) --}}
    @php
        $totalCost = $products->sum(fn($p) => $p->unit_cost * $p->stock_quantity);
        $margin = $totalInventoryValue > 0 ? (($totalInventoryValue - $totalCost) / $totalInventoryValue) * 100 : 0;
        $marginColor = $margin < 20 ? 'text-red-500' : ($margin < 40 ? 'text-amber-500' : 'text-emerald-500');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Valor de Venda (Black Glass) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-[0_16px_45px_rgba(0,0,0,0.45)] border border-zinc-800 group hover:scale-[1.02] transition-all">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400">
                        <flux:icon name="currency-dollar" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black text-white/60 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">
                        Ativos Brutos
                    </span>
                </div>
                <p class="text-[12px] font-black text-zinc-400 uppercase tracking-[0.25em] mb-1">
                    Valor Estimado de Venda
                </p>
                <h3 class="text-4xl font-black text-white tracking-tighter">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($totalInventoryValue, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
        </div>

        {{-- Alertas de Stock --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-[0_8px_30px_rgba(0,0,0,0.08)] group transition-all {{ $lowStockCount > 0 ? 'hover:border-red-500/40 hover:shadow-[0_0_25px_rgba(239,68,68,0.35)]' : 'hover:border-emerald-500/40 hover:shadow-[0_0_25px_rgba(16,185,129,0.35)]' }}">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 {{ $lowStockCount > 0 ? 'bg-red-50 dark:bg-red-500/10 text-red-600' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600' }} rounded-2xl">
                    <flux:icon
                        name="{{ $lowStockCount > 0 ? 'exclamation-triangle' : 'check-badge' }}"
                        variant="outline"
                        class="size-6"
                    />
                </div>
            </div>
            <p class="text-[12px] font-black text-zinc-400 uppercase tracking-[0.25em] mb-1">
                Reposição Necessária
            </p>
            <h3 class="text-4xl font-black {{ $lowStockCount > 0 ? 'text-red-500' : 'text-zinc-900 dark:text-white' }} tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $lowStockCount }}
                </span>
                <span class="text-xs text-zinc-400 uppercase font-bold ml-2 tracking-widest italic">
                    Artigos
                </span>
            </h3>
        </div>

        {{-- Margem Média --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all hover:border-emerald-500/40 hover:shadow-[0_0_25px_rgba(16,185,129,0.35)]">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="bolt" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[12px] font-black text-zinc-400 uppercase tracking-[0.25em] mb-1">
                Margem Média Bruta
            </p>
            <h3 class="text-4xl font-black {{ $marginColor }} tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ round($margin) }}%
                </span>
            </h3>
            <div class="mt-4 h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div
                    class="h-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]"
                    style="width: {{ max(min($margin, 100), 0) }}%">
                </div>
            </div>
        </div>
    </div>

    {{-- 3. INVENTORY LEDGER (ESTILO SaaS LEDGER) COM PRIVACIDADE --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-[0_12px_40px_rgba(0,0,0,0.12)] overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-900/30">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.25em] text-zinc-400 mb-1">
                    Mapa de Mercadorias
                </h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter text-zinc-800 dark:text-zinc-200">
                    Custódia de Artigos em Armazém
                </p>
            </div>
            <div class="flex items-center gap-3">
                <flux:badge
                    variant="neutral"
                    class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm">
                    {{ $products->count() }} Referências
                </flux:badge>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-widest">
                        <th class="p-6">Artigo / Identificação (SKU)</th>
                        <th class="p-6 text-center">Nível de Stock</th>
                        <th class="p-6 text-right">Custo Unit.</th>
                        <th class="p-6 text-right">Preço Venda</th>
                        <th class="p-6 text-right px-10">Valor em Ativo</th>
                        <th class="p-6 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($products as $product)
                        @php
                            $isLowStock = $product->stock_quantity <= $product->min_stock_alert;
                            $productMargin = $product->unit_price > 0
                                ? (($product->unit_price - $product->unit_cost) / $product->unit_price) * 100
                                : 0;
                            $rotation = $isLowStock ? 'Alta' : 'Normal';
                        @endphp
                        <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row hover:scale-[1.01]">
                            {{-- COLUNA IDENTIFICAÇÃO --}}
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-sm">
                                        <flux:icon name="archive-box" variant="outline" class="size-5 text-zinc-500" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black dark:text-white uppercase tracking-tight">
                                            {{ $product->name }}
                                        </span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase mt-0.5">
                                            {{ $product->sku ?? 'S/ SKU' }}
                                        </span>
                                        <span class="text-[9px] text-zinc-400 font-semibold uppercase mt-0.5">
                                            Rotatividade: {{ $rotation }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- COLUNA STOCK (COM BLUR) --}}
                            <td class="p-6 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span
                                        class="inline-flex px-3 py-1 font-black uppercase text-[9px] tracking-widest rounded-xl border transition-all duration-500
                                            {{ $isLowStock
                                                ? 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800'
                                                : 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800' }}">
                                        <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                            {{ $product->stock_quantity }} UNID.
                                        </span>
                                    </span>
                                </div>
                            </td>

                            {{-- COLUNA CUSTO (COM BLUR) --}}
                            <td class="p-6 text-right">
                                <span
                                    :class="privacyMode ? 'blur-sm select-none' : ''"
                                    class="text-xs font-bold text-zinc-500 transition-all duration-500">
                                    {{ number_format($product->unit_cost, 2, ',', ' ') }}€
                                </span>
                            </td>

                            {{-- COLUNA PREÇO (COM BLUR) --}}
                            <td class="p-6 text-right">
                                <div class="flex flex-col items-end gap-1">
                                    <span
                                        :class="privacyMode ? 'blur-sm select-none' : ''"
                                        class="text-sm font-bold dark:text-zinc-200 transition-all duration-500">
                                        {{ number_format($product->unit_price, 2, ',', ' ') }}€
                                    </span>
                                    <span class="text-[9px] text-zinc-400 font-semibold uppercase">
                                        Margem: {{ round($productMargin) }}%
                                    </span>
                                </div>
                            </td>

                            {{-- COLUNA VALOR TOTAL (COM BLUR) --}}
                            <td class="p-6 text-right px-10">
                                <div class="flex flex-col items-end">
                                    <span class="text-lg font-black text-brand-600 dark:text-brand-400 tracking-tighter italic">
                                        <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                            {{ number_format($product->stock_quantity * $product->unit_price, 2, ',', ' ') }} €
                                        </span>
                                    </span>
                                    <span class="text-[8px] font-black text-zinc-400 uppercase opacity-70">
                                        Potencial de Venda
                                    </span>
                                </div>
                            </td>

                            {{-- AÇÕES --}}
                            <td class="p-6 text-right pr-8">
                                <flux:tooltip text="Editar artigo">
                                    <flux:button
                                        wire:click="edit({{ $product->id }})"
                                        variant="ghost"
                                        icon="pencil-square"
                                        size="sm"
                                        class="opacity-0 group-hover/row:opacity-100 transition-opacity rounded-xl"
                                    />
                                </flux:tooltip>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-24 text-center text-zinc-400 italic font-medium">
                                Sem artigos registados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. MODAL: GESTÃO DE ARTIGO (DESIGN SaaS PRO) --}}
    <flux:modal name="product-modal" position="center" class="md:w-[600px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-[0_20px_60px_rgba(0,0,0,0.35)] border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-900 dark:bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="archive-box" class="size-6" />
                </div>
                <div>
                    <flux:heading
                        size="xl"
                        class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Editar Ficha de Artigo' : 'Novo Registo de Stock' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">
                        Configura os parâmetros de inventário e margens comerciais.
                    </p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Linha 1: Identificação --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                            Designação do Produto
                        </flux:label>
                        <flux:input
                            wire:model="name"
                            placeholder="Ex: Monitor UltraWide 34''"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                            SKU / Referência
                        </flux:label>
                        <flux:input
                            wire:model="sku"
                            placeholder="Ex: MON-UW34-OP01"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner uppercase"
                        />
                    </div>
                </div>

                {{-- Painel de Níveis de Stock --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2 px-1">
                        <flux:icon name="exclamation-triangle" class="size-3 text-brand-500" />
                        <p class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">
                            Parâmetros de Reposição
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2 text-center">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                                Unidades em Stock
                            </flux:label>
                            <flux:input
                                wire:model="stock_quantity"
                                type="number"
                                class="font-black text-xl text-center text-zinc-900 dark:text-white !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                            />
                        </div>

                        <div class="space-y-2 text-center">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                                Ponto de Alerta (Mín.)
                            </flux:label>
                            <flux:input
                                wire:model="min_stock_alert"
                                type="number"
                                class="font-black text-xl text-center text-red-500 !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                            />
                        </div>
                    </div>
                </div>

                {{-- Engenharia de Preços --}}
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                            Custo de Compra (€)
                        </flux:label>
                        <flux:input
                            wire:model="unit_cost"
                            type="number"
                            step="0.01"
                            class="font-black text-lg text-red-500 !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                            placeholder="0,00"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                            Preço de Venda (€)
                        </flux:label>
                        <flux:input
                            wire:model="unit_price"
                            type="number"
                            step="0.01"
                            class="font-black text-lg text-emerald-600 !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                            placeholder="0,00"
                        />
                    </div>
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button
                        variant="ghost"
                        class="flex-1 font-black uppercase text-[10px] text-zinc-400">
                        Descartar
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    wire:click="save"
                    variant="primary"
                    class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Inventário' : 'Gravar Artigo' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Controlo Logístico
        </p>
    </footer>
</div>
