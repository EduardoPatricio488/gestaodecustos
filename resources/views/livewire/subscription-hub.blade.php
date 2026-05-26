<div class="space-y-10 pb-20">
    {{-- 1. HEADER PREMIUM --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full transition-all duration-700 shadow-brand-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="credit-card" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Assinaturas</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Custos Fixos</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Monitorização de <span class="text-brand-600 font-bold uppercase tracking-tighter">Débitos Diretos e Recorrência</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="add-sub-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Nova Assinatura
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </header>
    </div>

    {{-- 2. KPIs DE PERFORMANCE FINANCEIRA --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Mensal --}}
        <div class="stat-card bg-zinc-950 text-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 group">
            <div class="absolute -right-10 -top-10 size-40 bg-brand-500/10 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-400 mb-2">Total Mensal Fixo</p>
                <h3 class="text-5xl font-black tracking-tighter italic text-white">{{ number_format($totalMonthly, 2, ',', ' ') }} <small class="text-xl not-italic ml-1">€</small></h3>
                <div class="mt-6 h-1 w-full bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-brand-500 shadow-[0_0_15px_#3b82f6]" style="width: 70%"></div>
                </div>
            </div>
            <flux:icon name="banknotes" class="absolute -right-4 -bottom-4 size-24 text-white/5 -rotate-12" />
        </div>

        {{-- Próximos 30 dias --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Previsão 30 Dias</p>
                <h3 class="text-4xl font-black text-orange-500 tracking-tighter italic">{{ number_format($upcoming, 2, ',', ' ') }} €</h3>
            </div>
            <p class="mt-4 text-[9px] font-bold text-zinc-500 uppercase tracking-widest italic flex items-center gap-2">
                <span class="size-2 bg-orange-500 rounded-full animate-ping"></span>
                Pagamentos agendados
            </p>
        </div>

        {{-- Média Diária --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Custo Médio Diário</p>
                <h3 class="text-4xl font-black dark:text-white tracking-tighter italic">{{ number_format($totalMonthly / 30, 2, ',', ' ') }} €</h3>
            </div>
            <p class="mt-4 text-[9px] font-black text-emerald-600 uppercase italic">Eficiência de Recorrência</p>
        </div>
    </div>

    {{-- 3. PIPELINE DE CUSTOS RECORRENTES (LISTA) --}}
    <div class="space-y-6">
        <div class="flex items-center gap-3 px-2">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="queue-list" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Contratos & Subscrições Ativas</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($subscriptions as $sub)
                <div class="glass-card p-6 flex justify-between items-center bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] shadow-sm hover:border-brand-500/40 transition-all duration-300 group">
                    <div class="flex items-center gap-5">
                        {{-- Indicador de Dia de Cobrança --}}
                        <div class="relative shrink-0">
                            <div class="size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 flex flex-col items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                                <span class="text-[8px] font-black text-zinc-400 uppercase leading-none mb-1">Dia</span>
                                <span class="text-xl font-black text-brand-600 leading-none tracking-tighter">{{ str_pad($sub->billing_day, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <h4 class="font-black dark:text-white uppercase text-sm tracking-tight group-hover:text-brand-600 transition-colors">
                                {{ $sub->name }}
                            </h4>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-[0.2em] rounded-md border border-zinc-200 dark:border-zinc-700">
                                    {{ $sub->category->name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-right space-y-2">
                        <p class="text-2xl font-black dark:text-white tracking-tighter italic">
                            {{ number_format($sub->amount, 2, ',', ' ') }} <small class="text-xs">€</small>
                        </p>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" class="rounded-xl opacity-0 group-hover:opacity-100 transition-opacity" />

                            <flux:menu>
                                <flux:menu.item wire:click="delete({{ $sub->id }})" wire:confirm="Tens a certeza que desejas cancelar esta monitorização?" variant="danger" icon="trash">
                                    Remover Assinatura
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            @endforeach

            @if($subscriptions->isEmpty())
                <div class="lg:col-span-2 py-20 text-center glass-card rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                    <flux:icon name="credit-card" class="size-12 text-zinc-300 mx-auto mb-4" />
                    <p class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px]">Sem débitos registados</p>
                </div>
            @endif
        </div>
    </div>
    {{-- 4. MODAL: CONFIGURAR NOVA ASSINATURA (DESIGN SaaS PRO) --}}
    <flux:modal name="add-sub-modal" position="center" class="md:w-[550px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            <div class="text-center space-y-2">
                <div class="inline-flex p-3 bg-brand-500/10 rounded-2xl mb-2 text-brand-600">
                    <flux:icon name="plus" class="size-6" />
                </div>
                <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">Configurar Assinatura</flux:heading>
                <p class="text-xs text-zinc-400 font-medium italic">Defina um custo fixo recorrente para projeção de cashflow.</p>
            </div>

            <div class="space-y-6">
                {{-- Nome da Assinatura --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Identificação do Gasto</flux:label>
                    <flux:input
                        wire:model="name"
                        placeholder="Ex: Netflix, Renda Escritório, Ginásio..."
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                    />
                </div>

                <div class="grid grid-cols-2 gap-6">
                    {{-- Valor Mensal --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Valor Mensal (€)</flux:label>
                        <flux:input
                            wire:model="amount"
                            type="number"
                            step="0.01"
                            placeholder="0,00"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner text-brand-600"
                        />
                    </div>
                    {{-- Dia de Cobrança --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Dia de Débito</flux:label>
                        <flux:input
                            wire:model="billing_day"
                            type="number"
                            min="1"
                            max="31"
                            placeholder="DD"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                </div>

                {{-- Categoria --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Categoria de Custo</flux:label>
                    <flux:select wire:model="category_id" class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner">
                        <option value="">Selecione o centro de custo...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            {{-- Acções --}}
            <div class="flex gap-4 pt-4">
                <flux:button
                    x-on:click="$dispatch('modal-close', { name: 'add-sub-modal' })"
                    variant="ghost"
                    class="flex-1 font-black uppercase text-[10px] text-zinc-400 hover:text-zinc-600 h-14 rounded-2xl"
                >
                    Descartar
                </flux:button>

                <flux:button
                    wire:click="save"
                    variant="primary"
                    class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl bg-brand-600 border-none text-white"
                >
                    Ativar Assinatura
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Sistema de Monitorização de Recorrência
        </p>
    </footer>
</div> {{-- FECHO DA DIV RAIZ --}}
