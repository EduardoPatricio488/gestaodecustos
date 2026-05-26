<div class="space-y-10 pb-20">
    {{-- 1. HEADER BANCÁRIO (ESTILO SaaS PREMIUM) --}}
    <div class="relative">
        {{-- Glow decorativo de fundo --}}
        <div class="absolute -top-10 left-0 size-64 {{ $isBusinessMode ? 'bg-brand-500/5' : 'bg-brand-500/10' }} blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 {{ $isBusinessMode ? 'bg-brand-500/20' : 'bg-brand-500/30' }} blur-2xl rounded-full group-hover:scale-125 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="building-library" class="w-10 h-10 {{ $isBusinessMode ? 'text-zinc-800 dark:text-brand-400' : 'text-brand-600' }}" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">{{ $modeTitle }}</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Tesouraria Ativa</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">
                        {{ $isBusinessMode ? 'Controlo estratégico de capitais e cashflow empresarial' : 'Gestão consolidada de contas e ativos pessoais' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="relative flex-1 min-w-[200px]">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Procurar conta..." class="!bg-transparent border-none shadow-none" />
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:modal.trigger name="bank-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Nova Conta
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- 2. KPIs DE LIQUIDEZ (CENTRO DE CAIXA COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Liquidez Disponível (Black Glass ou Brand) --}}
        <div class="stat-card p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between h-52 transition-all hover:scale-[1.02] {{ $isBusinessMode ? 'bg-zinc-950 text-white' : 'bg-brand-600 text-white border-none' }}">
            <div class="absolute top-0 right-0 w-32 h-32 {{ $isBusinessMode ? 'bg-brand-500/10' : 'bg-white/10' }} blur-3xl rounded-full -mr-10 -mt-10"></div>

            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] {{ $isBusinessMode ? 'text-brand-400' : 'text-white/60' }}">Cash-on-Hand</p>
                <h3 class="text-5xl font-black mt-2 tracking-tighter italic leading-none">
                    <span :class="privacyMode ? 'blur-xl select-none' : ''" class="transition-all duration-700 inline-block">
                        {{ number_format($totalLiquidez, 2, ',', ' ') }}
                    </span> <span class="text-2xl">€</span>
                </h3>
            </div>
            <div class="relative z-10 flex items-center gap-2">
                <div class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                <p class="text-[9px] font-black uppercase tracking-widest opacity-60">Total em disponibilidades</p>
            </div>
            <flux:icon name="banknotes" class="absolute -right-6 -bottom-6 size-32 text-white/5 rotate-12" />
        </div>

        {{-- Dívida de Cartões --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between h-52 group hover:border-red-500/30 transition-all">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Passivo Circulante</p>
                <h3 class="text-4xl font-black text-red-500 tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($totalDividaCartao, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
            <p class="text-[9px] font-black text-zinc-400 uppercase italic opacity-60 tracking-widest">Utilização de crédito</p>
        </div>

        {{-- Património Líquido --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between h-52 group hover:border-emerald-500/30 transition-all">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Net Cashflow Real</p>
                <h3 class="text-4xl font-black dark:text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($netCash, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
            <flux:badge variant="success" class="w-fit font-black text-[9px] uppercase tracking-tighter bg-emerald-500/10 text-emerald-600 border-none px-4 py-1 italic">
                Saldo Consolidado
            </flux:badge>
        </div>
    </div>
    {{-- 3. GRELHA DE CONTAS (ESTILO DIGITAL WALLET) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($accounts as $account)
            <div class="relative group">
                {{-- Efeito de Sombra Colorida no Hover --}}
                <div class="absolute inset-0 rounded-[2.5rem] opacity-0 group-hover:opacity-20 transition-opacity duration-500 blur-xl" style="background-color: {{ $account->color }}"></div>

                <div class="glass-card relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden transition-all duration-500 group-hover:-translate-y-1 group-hover:shadow-xl flex flex-col h-64">

                    {{-- Barra de Cor Superior (Identidade do Banco) --}}
                    <div class="h-2 w-full" style="background-color: {{ $account->color }}"></div>

                    <div class="p-8 flex flex-col justify-between h-full">
                        {{-- Topo do Cartão: Banco e Tipo --}}
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-4">
                                <div class="size-12 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-inner" style="background-color: {{ $account->color }}">
                                    <flux:icon name="{{ $account->getIcon() }}" variant="mini" class="size-6" />
                                </div>
                                <div>
                                    <h4 class="font-black dark:text-white uppercase text-sm tracking-tight leading-none">{{ $account->name }}</h4>
                                    <span class="inline-flex mt-1.5 px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-[0.15em] rounded-md border border-zinc-200 dark:border-zinc-700">
                                        {{ $account->type }}
                                    </span>
                                </div>
                            </div>

                            <flux:dropdown>
                                <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" class="rounded-xl opacity-0 group-hover:opacity-100 transition-opacity" />
                                <flux:menu class="min-w-[180px] p-2">
                                    <flux:menu.item wire:click="edit({{ $account->id }})" icon="pencil-square" class="font-bold text-xs uppercase">Configurar Conta</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item wire:click="delete({{ $account->id }})" wire:confirm="Eliminar conta e todo o histórico associado?" variant="danger" icon="trash" class="font-bold text-xs uppercase text-red-500">Remover do Sistema</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>

                        {{-- Meio do Cartão: Chip Visual (Estético) --}}
                        <div class="flex items-center gap-1 opacity-20 dark:opacity-10">
                             <div class="w-8 h-6 bg-zinc-400 rounded-md"></div>
                             <div class="w-4 h-6 bg-zinc-400 rounded-md"></div>
                        </div>

                        {{-- Fundo do Cartão: Saldo --}}
                        <div>
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Saldo Disponível</p>
                            <h3 class="text-4xl font-black dark:text-white tracking-tighter italic leading-none">
                                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                    {{ number_format($account->current_balance, 2, ',', ' ') }}
                                </span> <span class="text-xl">€</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center">
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                        <flux:icon name="building-library" class="size-12 text-zinc-200 dark:text-zinc-700" />
                    </div>
                    <div class="space-y-1 text-center">
                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Cofre Inativo</p>
                        <p class="text-zinc-400 text-xs italic font-medium">Não foram encontradas contas no contexto {{ $isBusinessMode ? 'empresarial' : 'pessoal' }}.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    {{-- 4. MODAL: CONFIGURAÇÃO DE CONTA (DESIGN DIGITAL WALLET) --}}
    <flux:modal name="bank-modal" position="center" class="md:w-[550px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-2xl text-white shadow-lg shadow-brand-500/20 {{ $isBusinessMode ? 'bg-zinc-900' : 'bg-brand-600' }}">
                    <flux:icon name="building-library" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Configurar Conta' : 'Nova Conta Digital' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Contexto Atual: <span class="text-brand-600 font-bold uppercase">{{ $isBusinessMode ? 'Empresa' : 'Pessoal' }}</span></p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Identificação da Conta --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome do Banco ou Identificação</flux:label>
                    <flux:input
                        wire:model="name"
                        placeholder="Ex: Santander À Ordem, Revolut Business..."
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                    />
                </div>

                {{-- Dados Técnicos (Painel de Destaque) --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Categoria de Conta</flux:label>
                            <flux:select wire:model="type" class="font-black uppercase text-[10px] !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
                                <option value="corrente">💳 Conta Corrente</option>
                                <option value="poupanca">📈 Poupança / Investimento</option>
                                <option value="cash">💵 Dinheiro Vivo</option>
                                <option value="credito">🔥 Cartão de Crédito</option>
                            </flux:select>
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Saldo Inicial (€)</flux:label>
                            <flux:input
                                wire:model="balance"
                                type="number"
                                step="0.01"
                                class="font-black text-lg text-brand-600 !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                                placeholder="0,00"
                            />
                        </div>
                    </div>

                    {{-- Seletor de Identidade Visual --}}
                    <div class="space-y-3 pt-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Cor de Identidade no Sistema</flux:label>
                        <div class="flex items-center gap-4 p-3 bg-white dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                            <input type="color" wire:model="color" class="size-10 rounded-lg border-none cursor-pointer bg-transparent">
                            <div class="flex flex-col">
                                <span class="text-xs font-mono font-black text-zinc-800 dark:text-zinc-200 uppercase">{{ $color }}</span>
                                <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-tight">Esta cor definirá o aspeto do seu cartão</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Conta' : 'Ativar Conta no Sistema' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Tesouraria Digital
        </p>
    </footer>
</div>
