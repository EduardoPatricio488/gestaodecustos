<div class="space-y-10 pb-20">
    {{-- 1. HEADER CRM (ESTILO SaaS PREMIUM) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-brand-500/10 text-brand-600">
                        <flux:icon name="user-group" class="w-10 h-10" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Gestão de Clientes</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">CRM Intelligence</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Controlo de histórico comercial, retenção e saúde de portfólio</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="relative flex-1 min-w-[240px]">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Procurar entidade..." class="!bg-transparent border-none shadow-none" />
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:modal.trigger name="client-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Novo Cliente
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- 2. KPIs CRM (PORTFOLIO ANALYTICS COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Base de Clientes --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-brand-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-2xl text-zinc-500">
                    <flux:icon name="users" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Base de Dados</p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $totalClients }}
                </span>
                <span class="text-xs text-zinc-400 uppercase font-bold ml-2 tracking-widest italic">Entidades</span>
            </h3>
        </div>

        {{-- Leads em Negociação (Black Glass) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400">
                        <flux:icon name="bolt" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest">Pipeline</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Leads em Negociação</p>
                <h3 class="text-4xl font-black text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ $activeLeads }}
                    </span>
                </h3>
            </div>
        </div>

        {{-- Faturação Acumulada --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-emerald-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="banknotes" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Volume de Negócios (Total)</p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($clients->sum('total_revenue'), 2, ',', ' ') }} €
                </span>
            </h3>
        </div>
    </div>

    {{-- 3. GRID DE ENTIDADES (ESTILO SaaS DASHBOARD) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($clients as $client)
            <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-brand-500/30 transition-all duration-300 group overflow-hidden">
                {{-- Topo do Card: Identidade e Status --}}
                <div class="p-7 flex justify-between items-start">
                    <div class="flex items-center gap-5">
                        <div class="relative">
                            <img src="{{ $client->avatar_url }}" class="size-16 rounded-[1.5rem] shadow-lg border border-zinc-100 dark:border-zinc-800 object-cover bg-white">
                            <div class="absolute -bottom-1 -right-1 size-5 border-2 border-white dark:border-zinc-900 rounded-full
                                {{ $client->status === 'ativo' ? 'bg-emerald-500' : ($client->status === 'lead' ? 'bg-brand-500' : 'bg-zinc-400') }}">
                            </div>
                        </div>
                        <div>
                            <h4 class="font-black dark:text-white uppercase text-base tracking-tight leading-none">{{ $client->name }}</h4>
                            <div class="flex items-center gap-2 mt-2">
                                @php
                                    $statusStyle = match($client->status) {
                                        'ativo'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200',
                                        'lead'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200',
                                        default    => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border-zinc-200'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 font-black uppercase text-[8px] tracking-widest rounded-md border {{ $statusStyle }}">
                                    {{ $client->status }}
                                </span>
                                <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-tighter">NIF: {{ $client->tax_number ?? '---' }}</span>
                            </div>
                        </div>
                    </div>

                    <flux:dropdown>
                        <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" class="rounded-xl" />
                        <flux:menu class="min-w-[180px] p-2">
                            <flux:menu.item wire:click="edit({{ $client->id }})" icon="pencil-square" class="font-bold text-xs">Editar Ficha</flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item wire:click="delete({{ $client->id }})" wire:confirm="Apagar cliente e todo o histórico associado?" variant="danger" icon="trash" class="font-bold text-xs">Eliminar Entidade</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>

                {{-- Centro do Card: Métricas Financeiras --}}
                <div class="grid grid-cols-2 gap-px bg-zinc-100 dark:bg-zinc-800 border-y border-zinc-100 dark:border-zinc-800">
                    <div class="p-6 bg-white dark:bg-zinc-900 group-hover:bg-zinc-50 dark:group-hover:bg-zinc-950/50 transition-colors">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Lifetime Value (LTV)</p>
                        <p class="text-xl font-black text-emerald-600 tracking-tighter italic">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($client->total_revenue, 2, ',', ' ') }} €
                            </span>
                        </p>
                    </div>
                    <div class="p-6 bg-white dark:bg-zinc-900 group-hover:bg-zinc-50 dark:group-hover:bg-zinc-950/50 transition-colors border-l border-zinc-100 dark:border-zinc-800">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Saldo Devedor</p>
                        <p class="text-xl font-black {{ $client->pending_debt > 0 ? 'text-red-500' : 'text-zinc-300' }} tracking-tighter italic">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($client->pending_debt, 2, ',', ' ') }} €
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Rodapé do Card: Contactos Rápidos --}}
                <div class="px-7 py-4 bg-zinc-50/50 dark:bg-zinc-900/50 flex items-center justify-between">
                    <div class="flex gap-4">
                        @if($client->email)
                            <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
                                <flux:icon name="envelope" variant="micro" class="size-3" />
                                <span class="text-[10px] font-bold truncate max-w-[120px]">{{ $client->email }}</span>
                            </div>
                        @endif
                        @if($client->phone)
                            <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
                                <flux:icon name="phone" variant="micro" class="size-3" />
                                <span class="text-[10px] font-bold">{{ $client->phone }}</span>
                            </div>
                        @endif
                    </div>
                    <flux:button variant="ghost" size="xs" class="rounded-lg text-[9px] font-black uppercase tracking-widest text-brand-600">Ver Histórico →</flux:button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center">
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                        <flux:icon name="user-group" class="size-12 text-zinc-200 dark:text-zinc-700" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Diretório Vazio</p>
                        <p class="text-zinc-400 text-xs italic font-medium">Ainda não foram registados clientes no CRM.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 4. MODAL: FICHA EXECUTIVA DE CLIENTE (DESIGN CRM PRO) --}}
    <flux:modal name="client-modal" position="center" class="md:w-[650px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="user-group" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Editar Ficha de Cliente' : 'Nova Entidade Comercial' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Gere os dados de identidade e faturação do teu cliente.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- SECÇÃO: IDENTIDADE COMERCIAL E FISCAL --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 px-1">
                        <flux:icon name="identification" class="size-3 text-zinc-400" />
                        <p class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em]">Dados de Identidade</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome de Exibição</flux:label>
                            <flux:input
                                wire:model="name"
                                placeholder="Ex: Acme Portugal"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                            />
                        </div>
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome Fiscal (Legal)</flux:label>
                            <flux:input
                                wire:model="legal_name"
                                placeholder="Ex: Acme Corp, Lda"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Número de Identificação Fiscal (NIF)</flux:label>
                        <flux:input
                            wire:model="tax_number"
                            placeholder="500 000 000"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                </div>

                {{-- SECÇÃO: CONTACTOS E STATUS (PAINEL DE DESTAQUE) --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2">
                        <flux:icon name="envelope" class="size-3 text-brand-500" />
                        <p class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">Canais de Comunicação</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="email" label="Email Principal" icon="at-symbol" class="!bg-white dark:!bg-zinc-950 rounded-xl" />
                        <flux:input wire:model="phone" label="Telefone / Móvel" icon="phone" class="!bg-white dark:!bg-zinc-950 rounded-xl" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Estado no Funil Comercial</flux:label>
                        <flux:select wire:model="status" class="font-black uppercase text-xs !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
                            <option value="ativo">✅ Cliente Ativo / Recorrente</option>
                            <option value="lead">⚡ Lead em Negociação</option>
                            <option value="inativo">💤 Conta Inativa</option>
                        </flux:select>
                    </div>
                </div>

                {{-- NOTAS INTERNAS --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Histórico e Observações</flux:label>
                    <flux:textarea
                        wire:model="notes"
                        rows="2"
                        placeholder="Regista aqui detalhes sobre o cliente, acordos feitos ou preferências..."
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
                    {{ $editingId ? 'Atualizar Ficha' : 'Adicionar ao CRM' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Gestão de Relacionamento
        </p>
    </footer>
</div>
