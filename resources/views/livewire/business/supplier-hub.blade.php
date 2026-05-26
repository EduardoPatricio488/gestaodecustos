<div class="space-y-10 pb-20">
    {{-- 1. HEADER DE FORNECEDORES (ESTILO SaaS PREMIUM) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-brand-500/10 text-brand-600">
                        <flux:icon name="user-group" class="w-10 h-10" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Fornecedores & Parceiros</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">Supply Chain</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Gestão estratégica de entidades, contratos e volume de transações</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="relative flex-1 min-w-[200px]">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Procurar parceiro..." class="!bg-transparent border-none shadow-none" />
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:modal.trigger name="supplier-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Novo Parceiro
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- 2. KPIs DE COMPRAS (ANALYTICS COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Parceiros Ativos --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-brand-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-2xl text-zinc-500">
                    <flux:icon name="building-storefront" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Entidades Registadas</p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $totalActiveSuppliers }}
                </span>
                <span class="text-xs text-zinc-400 uppercase font-bold ml-2 tracking-widest italic">Parceiros</span>
            </h3>
        </div>

        {{-- Maior Fornecedor (Black Glass) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400 shadow-inner">
                        <flux:icon name="trophy" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest italic text-zinc-400">Top Spend</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Maior Volume de Negócio</p>
                <h3 class="text-2xl font-black text-white tracking-tighter uppercase truncate mb-2">{{ $topSupplier->name ?? '---' }}</h3>
                <p class="text-sm font-black text-brand-500 italic">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                        {{ number_format($topSupplier->total_spent ?? 0, 2, ',', ' ') }} €
                    </span>
                </p>
            </div>
        </div>

        {{-- Média de Gasto --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-emerald-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="chart-pie" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Média de Gasto por Entidade</p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($totalActiveSuppliers > 0 ? ($suppliers->sum('total_spent') / $totalActiveSuppliers) : 0, 2, ',', ' ') }} €
                </span>
            </h3>
            <p class="mt-4 text-[9px] text-zinc-400 font-bold uppercase tracking-widest italic">Impacto Médio no OpEx</p>
        </div>
    </div>

    {{-- 3. GRELHA DE PARCEIROS (ESTILO SaaS AUDIT) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($suppliers as $supplier)
            <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-brand-500/30 transition-all duration-300 group overflow-hidden flex flex-col h-full">

                {{-- Topo do Card: Identidade --}}
                <div class="p-7 flex justify-between items-start">
                    <div class="flex items-center gap-5">
                        <div class="size-16 rounded-[1.5rem] bg-brand-500/10 text-brand-600 flex items-center justify-center font-black text-2xl shadow-inner border border-brand-500/20 uppercase">
                            {{ substr($supplier->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-black dark:text-white uppercase text-base tracking-tight leading-none group-hover:text-brand-600 transition-colors">{{ $supplier->name }}</h4>
                            <span class="inline-flex mt-2 px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-widest rounded-md border border-zinc-200 dark:border-zinc-700">
                                {{ $supplier->industry ?? 'Entidade / Fornecedor' }}
                            </span>
                        </div>
                    </div>

                    <flux:dropdown>
                        <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" class="rounded-xl opacity-0 group-hover:opacity-100 transition-opacity" />
                        <flux:menu class="min-w-[180px] p-2">
                            <flux:menu.item wire:click="edit({{ $supplier->id }})" icon="pencil-square" class="font-bold text-xs uppercase">Editar Ficha</flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item wire:click="delete({{ $supplier->id }})" wire:confirm="Eliminar fornecedor e histórico?" variant="danger" icon="trash" class="font-bold text-xs uppercase text-red-500">Remover Parceiro</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>

                {{-- Meio do Card: Contactos e Condições --}}
                <div class="px-7 space-y-3 mb-8">
                    @if($supplier->email)
                        <div class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 group/info">
                            <flux:icon name="envelope" variant="micro" class="size-3.5 group-hover/info:text-brand-500 transition-colors" />
                            <span :class="privacyMode ? 'blur-sm select-none' : ''" class="text-[11px] font-bold transition-all duration-500 truncate">
                                {{ $supplier->email }}
                            </span>
                        </div>
                    @endif

                    @if($supplier->payment_terms)
                        <div class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 group/info">
                            <flux:icon name="credit-card" variant="micro" class="size-3.5 group-hover/info:text-brand-500 transition-colors" />
                            <span class="text-[10px] font-black uppercase tracking-tight">Acordo: {{ $supplier->payment_terms }}</span>
                        </div>
                    @endif
                </div>

                {{-- Rodapé do Card: Performance Financeira (Ledger Style) --}}
                <div class="mt-auto p-7 bg-zinc-50/50 dark:bg-zinc-800/40 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-end">
                    <div>
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Total Transacionado</p>
                        <h3 class="text-xl font-black dark:text-zinc-100 tracking-tighter italic leading-none">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($supplier->total_spent, 2, ',', ' ') }}
                            </span> €
                        </h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Volume</p>
                        <span class="inline-flex px-2 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg text-[10px] font-black dark:text-white shadow-sm">
                            {{ $supplier->bills_count }} Docs
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center">
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                        <flux:icon name="user-group" class="size-12 text-zinc-200 dark:text-zinc-700" />
                    </div>
                    <div class="space-y-1 text-center">
                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Cadeia de Abastecimento Vazia</p>
                        <p class="text-zinc-400 text-xs italic font-medium">Ainda não registaste nenhum parceiro de negócio.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 4. MODAL: FICHA EXECUTIVA DE FORNECEDOR (DESIGN SaaS PRO) --}}
    <flux:modal name="supplier-modal" position="center" class="md:w-[650px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-900 dark:bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="user-group" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Editar Ficha de Parceiro' : 'Novo Registo de Entidade' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Gere os dados de faturação e condições comerciais do fornecedor.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- SECÇÃO: IDENTIDADE COMERCIAL E FISCAL --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 px-1">
                        <flux:icon name="identification" class="size-3 text-zinc-400" />
                        <p class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em]">Identificação de Entidade</p>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome da Marca / Comercial</flux:label>
                        <flux:input
                            wire:model="name"
                            placeholder="Ex: Staples Portugal"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Designação Social (Fiscal)</flux:label>
                            <flux:input
                                wire:model="legal_name"
                                placeholder="Ex: Staples Office Supplies, S.A."
                                class="font-bold !bg-white dark:!bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl h-12 shadow-sm"
                            />
                        </div>
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">NIF / VAT Number</flux:label>
                            <flux:input
                                wire:model="tax_number"
                                placeholder="500 000 000"
                                class="font-black !bg-white dark:!bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl h-12 shadow-sm"
                            />
                        </div>
                    </div>
                </div>

                {{-- SECÇÃO: LOGÍSTICA E PAGAMENTOS (PAINEL DE DESTAQUE) --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2">
                        <flux:icon name="credit-card" class="size-3 text-brand-500" />
                        <p class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">Condições Comerciais</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="email" label="Email para Envio de Encomendas" icon="envelope" class="!bg-white dark:!bg-zinc-950 rounded-xl" />
                        <flux:input wire:model="phone" label="Contacto Telefónico" icon="phone" class="!bg-white dark:!bg-zinc-950 rounded-xl" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Acordo de Pagamento (Prazo)</flux:label>
                        <flux:select wire:model="payment_terms" class="font-black uppercase text-xs !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
                            <option value="">Escolha uma condição...</option>
                            <option value="Pronto Pagamento">⚡ Pronto Pagamento</option>
                            <option value="8 Dias">📅 8 Dias Líquidos</option>
                            <option value="30 Dias">📅 30 Dias (Padrão)</option>
                            <option value="60 Dias">📅 60 Dias (Crédito)</option>
                        </flux:select>
                    </div>
                </div>

                {{-- MORADA E SEDE --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Sede / Morada Fiscal</flux:label>
                    <flux:textarea
                        wire:model="address"
                        rows="2"
                        placeholder="Introduz a morada completa para registo contabilístico..."
                        class="rounded-2xl shadow-sm border-none !bg-zinc-50 dark:!bg-zinc-900 text-sm p-4"
                    />
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Ficha' : 'Validar Parceiro' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Gestão de Supply Chain
        </p>
    </footer>
</div>
