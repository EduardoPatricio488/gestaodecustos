<div class="space-y-10 pb-20">
    {{-- 1. HEADER DE INVESTIMENTOS (ESTILO SaaS PREMIUM) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
        <div class="flex items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-0 bg-indigo-500/20 blur-2xl rounded-full group-hover:bg-indigo-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-indigo-500/10">
                    <flux:icon name="chart-bar-square" class="w-10 h-10 text-indigo-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Investimentos</h1>
                    <flux:badge variant="neutral" class="bg-indigo-500/10 text-indigo-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Portefólio Ativo</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Monitorização de ativos e performance de capital em tempo real</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:modal.trigger name="add-investment">
                <flux:button variant="primary" icon="plus" class="bg-indigo-600 hover:bg-indigo-700 rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20">
                    Novo Ativo
                </flux:button>
            </flux:modal.trigger>
            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
            <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
        </div>
    </div>

    {{-- 2. MARKET TICKER (ESTILO TERMINAL FINANCEIRO) --}}
    <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar">
        @foreach($marketData as $ticker => $data)
            <div class="glass-card flex-shrink-0 min-w-[180px] p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[1.8rem] shadow-sm hover:border-indigo-500/30 transition-all group">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em] group-hover:text-indigo-500 transition-colors">{{ $ticker }}</span>
                        <span class="text-[8px] text-zinc-500 font-bold uppercase truncate max-w-[80px]">{{ $data['name'] }}</span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] font-black {{ $data['change'] >= 0 ? 'text-emerald-500' : 'text-red-500' }} flex items-center gap-1">
                            {{ $data['change'] >= 0 ? '▲' : '▼' }} {{ number_format(abs($data['change']), 2) }}%
                        </span>
                        <div class="size-1 rounded-full {{ $data['change'] >= 0 ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }} mt-1"></div>
                    </div>
                </div>
                <p class="text-xl font-black dark:text-white tracking-tighter italic">
                    {{ number_format($data['price'], 2, ',', ' ') }} <span class="text-xs">€</span>
                </p>
            </div>
        @endforeach
    </div>

    {{-- 3. CENTRO DE PERFORMANCE DO PORTEFÓLIO --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- CARD PRINCIPAL: PATRIMÓNIO (ESTILO BLACK GLASS) --}}
        <div class="stat-card bg-zinc-950 text-white p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 lg:col-span-2 group">
            {{-- Efeito de Glow de Fundo --}}
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 blur-[100px] rounded-full -mr-20 -mt-20 group-hover:bg-indigo-500/20 transition-all duration-1000"></div>

            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-500/20 rounded-lg">
                            <flux:icon name="chart-bar-square" class="size-5 text-indigo-400" />
                        </div>
                        <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Avaliação do Portefólio</h2>
                    </div>
                    <p class="text-6xl sm:text-7xl font-black tracking-tighter italic">
                        {{ number_format($currentValue, 2, ',', ' ') }} <span class="text-3xl">€</span>
                    </p>
                </div>

                <div class="mt-12 flex flex-wrap items-end justify-between gap-6">
                    <div class="flex gap-4">
                        <div class="px-6 py-3 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Custo de Aquisição</p>
                            <p class="text-xl font-black text-zinc-200">{{ number_format($totalInvested, 2, ',', ' ') }} €</p>
                        </div>

                        <div class="px-6 py-3 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Lucro / Prejuízo</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-black {{ $profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit, 2, ',', ' ') }} €
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-4xl font-black {{ $profit >= 0 ? 'text-emerald-400' : 'text-red-400' }} tracking-tighter">
                            {{ $totalInvested > 0 ? round(($profit/$totalInvested)*100, 1) : 0 }}%
                        </span>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mt-1">Rentabilidade Total</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD SECUNDÁRIO: ESTRATÉGIA E RISCO --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group">
            <div class="text-center space-y-4">
                <div class="relative inline-block">
                    <div class="absolute inset-0 bg-indigo-500/20 blur-xl rounded-full animate-pulse"></div>
                    <div class="relative p-5 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.8rem] text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                        <flux:icon name="shield-check" variant="outline" class="size-8" />
                    </div>
                </div>

                <div>
                    <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-zinc-400">Perfil de Alocação</h3>
                    <p class="text-2xl font-black dark:text-white uppercase italic tracking-tighter mt-1">Diversificada</p>
                </div>
            </div>

            <div class="space-y-4 mt-8">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-black text-zinc-500 uppercase">Ativos em Carteira</span>
                        <span class="text-sm font-black dark:text-white">{{ $myAssets->count() }}</span>
                    </div>
                    <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500" style="width: {{ min($myAssets->count() * 10, 100) }}%"></div>
                    </div>
                </div>

                <p class="text-[10px] text-zinc-400 font-medium italic text-center leading-relaxed">
                    A tua carteira está distribuída por {{ $myAssets->count() }} frentes de capital. Considera rebalancear se um ativo exceder 25% do total.
                </p>
            </div>
        </div>
    </div>

    {{-- 4. LISTAGEM DETALHADA DE ATIVOS (ESTILO LEDGER) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-900/30">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Custódia de Ativos</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Posições em Carteira</p>
            </div>
            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm">{{ $myAssets->count() }} Ativos Ativos</flux:badge>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                        <th class="p-6">Ativo / Ticker</th>
                        <th class="p-6 text-center">Posição (Qtd)</th>
                        <th class="p-6 text-right">Preço Médio</th>
                        <th class="p-6 text-right">Cotação Atual</th>
                        <th class="p-6 text-right px-10">Performance Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($myAssets as $asset)
                        @php
                            $assetValue = $asset->quantity * $asset->current_price;
                            $assetCost = $asset->quantity * $asset->average_price;
                            $assetProfit = $assetValue - $assetCost;
                            $assetPerc = $assetCost > 0 ? ($assetProfit / $assetCost) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all duration-300 group/row">
                            {{-- IDENTIFICAÇÃO --}}
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-white dark:bg-zinc-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black text-xs shadow-sm border border-zinc-100 dark:border-zinc-700 group-hover/row:scale-110 transition-transform">
                                        {{ $asset->symbol }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black dark:text-white uppercase tracking-tight leading-none">{{ $asset->name }}</p>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-500 tracking-widest">{{ $asset->type }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- QUANTIDADE --}}
                            <td class="p-6 text-center">
                                <span class="text-sm font-black dark:text-zinc-200">{{ number_format($asset->quantity, 4, ',', ' ') }}</span>
                                <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-tighter">unidades</p>
                            </td>

                            {{-- PREÇO MÉDIO --}}
                            <td class="p-6 text-right">
                                <span class="text-xs font-bold text-zinc-500">{{ number_format($asset->average_price, 2, ',', ' ') }} €</span>
                            </td>

                            {{-- COTAÇÃO --}}
                            <td class="p-6 text-right">
                                <span class="text-sm font-black dark:text-white">{{ number_format($asset->current_price, 2, ',', ' ') }} €</span>
                            </td>

                            {{-- PERFORMANCE --}}
                            <td class="p-6 text-right px-10">
                                <div class="flex flex-col items-end">
                                    <span class="text-lg font-black {{ $assetProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }} tracking-tighter">
                                        {{ number_format($assetValue, 2, ',', ' ') }} €
                                    </span>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-black {{ $assetProfit >= 0 ? 'text-emerald-600/70' : 'text-red-600/70' }} uppercase">
                                            {{ $assetProfit >= 0 ? '+' : '' }}{{ number_format($assetProfit, 2, ',', ' ') }} €
                                        </span>
                                        <span class="text-[9px] font-bold {{ $assetProfit >= 0 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }} px-1.5 py-0.5 rounded-md">
                                            {{ number_format($assetPerc, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-24 text-center">
                                <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner max-w-xs mx-auto">
                                    <flux:icon name="magnifying-glass" class="size-10 text-zinc-200 dark:text-zinc-700 mx-auto mb-4" />
                                    <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Cofre Vazio</p>
                                    <p class="text-zinc-400 text-xs italic mt-1">Ainda não registaste investimentos.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 5. MODAL: REGISTAR NOVO ATIVO (DESIGN SaaS PROFISSIONAL) --}}
    <flux:modal name="add-investment" position="center" class="md:w-[550px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">
            <div class="absolute top-6 right-6">
                <flux:modal.close><flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" /></flux:modal.close>
            </div>

            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-500/20">
                    <flux:icon name="plus" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Registar Ativo</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Adiciona uma nova posição ao teu portefólio global.</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Ticker / Símbolo</flux:label>
                        <flux:input wire:model="symbol" placeholder="Ex: BTC ou AAPL" class="font-black !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner uppercase" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Tipo de Ativo</flux:label>
                        <flux:select wire:model="type" class="font-black uppercase text-xs !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner">
                            <option value="Ação">Ação</option>
                            <option value="Cripto">Cripto</option>
                            <option value="ETF">ETF</option>
                            <option value="Fundo">Fundo</option>
                        </flux:select>
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome da Empresa ou Moeda</flux:label>
                    <flux:input wire:model="name" placeholder="Ex: Apple Inc. ou Bitcoin" class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Quantidade</flux:label>
                            <flux:input wire:model="quantity" type="number" step="0.0001" class="font-black !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm" />
                        </div>
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Preço Médio (€)</flux:label>
                            <flux:input wire:model="average_price" type="number" step="0.01" class="font-black !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <flux:modal.close><flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Cancelar</flux:button></flux:modal.close>
                <flux:button wire:click="save" variant="primary" class="flex-[2] bg-indigo-600 hover:bg-indigo-700 border-none font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 h-14 rounded-2xl">
                    Guardar no Portefólio
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Gestão de Ativos
        </p>
    </footer>
</div>
