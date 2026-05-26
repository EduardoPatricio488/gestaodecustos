<div class="space-y-10 pb-20">
    {{-- 1. HEADER COMERCIAL (ESTILO PREMIUM SaaS) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-indigo-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-brand-500/10">
                        <flux:icon name="newspaper" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Propostas & Orçamentos</h1>
                        <flux:badge variant="neutral" class="bg-indigo-500/10 text-indigo-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Pipeline Comercial</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Gestão ativa de oportunidades e funil de vendas estratégico</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="proposal-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Novo Orçamento
                    </flux:button>
                </flux:modal.trigger>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
            </div>
        </div>
    </div>

    {{-- 2. KPIs COMERCIAIS (PIPELINE INTELLIGENCE COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Pipeline Total (Black Glass) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400">
                        <flux:icon name="currency-dollar" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest">Valor Bruto</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Pipeline em Negociação</p>
                <h3 class="text-4xl font-black text-white tracking-tighter">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($totalValue, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
        </div>

        {{-- Taxa de Conversão --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:border-emerald-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="arrow-path" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Eficiência de Conversão</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">{{ round($conversionRate) }}%</h3>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest italic">adjudicado</span>
            </div>
            <div class="mt-4 h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]" style="width: {{ $conversionRate }}%"></div>
            </div>
        </div>

        {{-- Orçamentos Ativos --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:border-brand-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-2xl text-zinc-500">
                    <flux:icon name="clock" variant="outline" class="size-6" />
                </div>
                <flux:badge variant="neutral" size="sm" class="font-black text-[9px]">{{ $proposals->where('status', 'enviada')->count() }} Ativos</flux:badge>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Aguardar Resposta</p>
            <h3 class="text-4xl font-black dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $proposals->where('status', 'enviada')->count() }}
                </span>
            </h3>
            <p class="mt-4 text-[9px] text-zinc-500 font-medium italic">Oportunidades em fase de decisão.</p>
        </div>
    </div>

    {{-- 3. BARRA DE FILTRAGEM INTELIGENTE --}}
    <div class="glass-card p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl flex flex-col md:flex-row gap-4 shadow-sm items-center">
        <div class="relative flex-1 w-full">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Procurar referência, título ou cliente..." class="!bg-transparent border-none shadow-none" />
        </div>
        <div class="h-8 w-px bg-zinc-100 dark:bg-zinc-800 hidden md:block"></div>
        <flux:select wire:model.live="clientFilter" class="w-full md:w-72 font-bold uppercase text-[10px]" placeholder="Todos os Clientes">
            <option value="">Filtrar por Entidade</option>
            @foreach($clients as $client) <option value="{{ $client->id }}">{{ $client->name }}</option> @endforeach
        </flux:select>
    </div>

    {{-- 4. SALES PIPELINE LEDGER (TABELA SaaS) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-900/30">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Oportunidades Comerciais</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Histórico de Orçamentos</p>
            </div>
            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none">{{ $proposals->count() }} Registos</flux:badge>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-widest">
                        <th class="p-6">Referência / Título</th>
                        <th class="p-6">Entidade / Cliente</th>
                        <th class="p-6 text-center">Estado Comercial</th>
                        <th class="p-6 text-right px-10">Valor Proposto</th>
                        <th class="p-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($proposals as $prop)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row">
                            {{-- REF E TÍTULO --}}
                            <td class="p-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black dark:text-white uppercase tracking-tight">#{{ $prop->proposal_number }}</span>
                                    <span class="text-[10px] text-zinc-500 font-bold uppercase mt-0.5 max-w-[200px] truncate">{{ $prop->title }}</span>
                                </div>
                            </td>

                            {{-- CLIENTE --}}
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div class="size-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                                        <img src="{{ $prop->client->avatar_url }}" class="size-full object-cover">
                                    </div>
                                    <span class="text-xs font-black dark:text-zinc-300 uppercase tracking-tight">{{ $prop->client->name }}</span>
                                </div>
                            </td>

                            {{-- ESTADO COM BADGES SaaS --}}
                            <td class="p-6 text-center">
                                @php
                                    $statusStyle = match($prop->status) {
                                        'aceite'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'enviada'  => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                        'recusada' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800',
                                        default    => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700'
                                    };
                                @endphp
                                <span class="inline-flex px-3 py-1 font-black uppercase text-[8px] tracking-widest rounded-xl border {{ $statusStyle }}">
                                    {{ $prop->status }}
                                </span>
                            </td>

                            {{-- VALOR COM PRIVACIDADE --}}
                            <td class="p-6 text-right px-10">
                                <div class="flex flex-col items-end">
                                    <span class="text-lg font-black dark:text-white tracking-tighter italic">
                                        <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                            {{ number_format($prop->amount, 2, ',', ' ') }} €
                                        </span>
                                    </span>
                                    <span class="text-[8px] font-black text-zinc-400 uppercase opacity-70">Valor Base</span>
                                </div>
                            </td>

                            {{-- AÇÕES E CONVERSÃO --}}
                            <td class="p-6 text-right pr-8">
                                <div class="flex items-center justify-end gap-3">
                                    @if($prop->status === 'aceite')
                                        <flux:button wire:click="convertToInvoice({{ $prop->id }})" variant="filled" size="xs" icon="document-check" class="bg-emerald-600 hover:bg-emerald-500 text-white font-black uppercase text-[9px] px-4 py-1.5 rounded-xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-105">
                                            Faturar Agora
                                        </flux:button>
                                    @endif

                                    <flux:dropdown>
                                        <flux:button variant="ghost" icon="ellipsis-vertical" size="sm" class="rounded-xl" />
                                        <flux:menu class="min-w-[200px] p-2">
                                            <flux:menu.item wire:click="edit({{ $prop->id }})" icon="pencil-square" class="font-bold text-xs">Editar Dados</flux:menu.item>
                                            <flux:menu.item wire:click="updateStatus({{ $prop->id }}, 'enviada')" icon="paper-airplane" class="font-bold text-xs">Enviada ao Cliente</flux:menu.item>
                                            <flux:menu.item wire:click="updateStatus({{ $prop->id }}, 'aceite')" icon="check" class="text-emerald-600 font-bold text-xs">Marcar Adjudicada</flux:menu.item>
                                            <flux:menu.item wire:click="updateStatus({{ $prop->id }}, 'recusada')" icon="x-mark" class="text-red-500 font-bold text-xs">Marcar Perdida</flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item wire:click="delete({{ $prop->id }})" wire:confirm="Eliminar oportunidade?" variant="danger" icon="trash" class="font-bold text-xs">Eliminar do Funil</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-24 text-center">
                                <div class="flex flex-col items-center justify-center gap-4">
                                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                                        <flux:icon name="document-magnifying-glass" class="size-12 text-zinc-200 dark:text-zinc-700" />
                                    </div>
                                    <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Funil de Vendas Limpo</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 5. MODAL: GESTÃO DE OPORTUNIDADE (DESIGN EXECUTIVO) --}}
    <flux:modal name="proposal-modal" position="center" class="md:w-[650px] !p-0 overflow-visible">
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
                    <flux:icon name="newspaper" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Editar Orçamento' : 'Nova Oportunidade' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Prepara uma proposta profissional para apresentar ao teu cliente.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Linha 1: Título e Referência --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Título do Projeto / Proposta</flux:label>
                        <flux:input
                            wire:model="title"
                            placeholder="Ex: Consultoria Estratégica Q3"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Referência</flux:label>
                        <flux:input
                            wire:model="proposal_number"
                            placeholder="ORC-001"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner uppercase"
                        />
                    </div>
                </div>

                {{-- Linha 2: Cliente e Estado --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Entidade / Cliente</flux:label>
                        <flux:select wire:model="client_id" class="font-bold uppercase text-[10px] !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner">
                            <option value="">Selecionar Cliente...</option>
                            @foreach($clients as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                        </flux:select>
                    </div>
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Fase da Negociação</flux:label>
                        <flux:select wire:model="status" class="font-black uppercase text-[10px] !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner">
                            <option value="rascunho">📝 Rascunho Interno</option>
                            <option value="enviada">📩 Enviada ao Cliente</option>
                            <option value="aceite">✅ Adjudicada / Aceite</option>
                            <option value="recusada">❌ Recusada / Perdida</option>
                        </flux:select>
                    </div>
                </div>

                {{-- Área de Valor e Validade (Painel de Destaque) --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Valor Base Estimado</flux:label>
                            <flux:input
                                wire:model="amount"
                                type="number"
                                step="0.01"
                                icon="banknotes"
                                class="font-black text-2xl text-brand-600 !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-14 shadow-sm"
                                placeholder="0,00"
                            />
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Válido até</flux:label>
                            <flux:input wire:model="valid_until" type="date" class="font-bold !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-14 shadow-sm" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Notas e Condições Especiais</flux:label>
                        <flux:textarea
                            wire:model="notes"
                            rows="3"
                            placeholder="Ex: Condições de pagamento, prazos de entrega, observações técnicas..."
                            class="rounded-2xl shadow-sm border-none !bg-white dark:!bg-zinc-950 text-sm"
                        />
                    </div>
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Proposta' : 'Gravar Oportunidade' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Gestão Comercial
        </p>
    </footer>
</div>
