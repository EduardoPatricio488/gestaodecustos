<div class="space-y-10 pb-20">
    {{-- 1. HEADER DE VENDAS (ESTILO SaaS PREMIUM) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
        <div class="flex items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full group-hover:bg-emerald-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-emerald-500/10 text-emerald-600">
                    <flux:icon name="presentation-chart-line" class="w-10 h-10" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Faturação & Vendas</h1>
                    <flux:badge variant="success" class="bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Sales Hub</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Monitorização de receitas emitidas e controlo de recebíveis</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:modal.trigger name="add-invoice-modal">
                <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                    Nova Fatura
                </flux:button>
            </flux:modal.trigger>

            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
            <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
        </div>
    </div>

    {{-- 2. KPI DASHBOARD (CASHFLOW INTELLIGENCE COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Recebido (Pagas) --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-emerald-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="check-circle" variant="outline" class="size-6" />
                </div>
                <span class="text-[9px] font-black text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg uppercase tracking-widest italic text-emerald-600">Liquidez Real</span>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Total Recebido (Pagas)</p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($totalBilled, 2, ',', ' ') }} €
                </span>
            </h3>
        </div>

        {{-- A Receber (Pendentes) --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-amber-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-2xl text-amber-600">
                    <flux:icon name="clock" variant="outline" class="size-6" />
                </div>
                <span class="text-[9px] font-black text-amber-600 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">Pendente</span>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Contas a Receber</p>
            <h3 class="text-4xl font-black text-amber-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($totalPending, 2, ',', ' ') }} €
                </span>
            </h3>
        </div>

        {{-- IVA Acumulado (Vault Style) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400">
                        <flux:icon name="receipt-percent" variant="outline" class="size-6" />
                    </div>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">IVA Total Acumulado</p>
                <h3 class="text-4xl font-black text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($vatToPay, 2, ',', ' ') }} €
                    </span>
                </h3>
                <p class="mt-4 text-[9px] text-zinc-600 font-bold uppercase tracking-widest">Reserva Fiscal Obrigatória</p>
            </div>
        </div>
    </div>

    {{-- 3. SALES LEDGER (ESTILO SaaS LEDGER) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-900/30">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Pipeline de Receitas</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter text-zinc-800 dark:text-zinc-200">Histórico de Faturação</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm">{{ $invoices->total() }} Documentos</flux:badge>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-widest">
                        <th class="p-6">Nº Documento / Cliente</th>
                        <th class="p-6 text-center">Estado do Fluxo</th>
                        <th class="p-6">Data de Vencimento</th>
                        <th class="p-6 text-right px-10">Montante Total</th>
                        <th class="p-6 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($invoices as $inv)
                        @php
                            $isOverdue = $inv->status === 'pendente' && \Carbon\Carbon::parse($inv->due_date)->isPast();
                        @endphp
                        <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row">
                            {{-- COLUNA IDENTIFICAÇÃO --}}
                            <td class="p-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black dark:text-white uppercase tracking-tight">#{{ $inv->invoice_number }}</span>
                                    <span class="text-[10px] text-zinc-500 font-bold uppercase mt-0.5">{{ $inv->client_name }}</span>
                                </div>
                            </td>

                            {{-- COLUNA ESTADO --}}
                            <td class="p-6 text-center">
                                @if($inv->status === 'paga')
                                    <span class="inline-flex px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-black uppercase text-[8px] tracking-widest rounded-xl border border-emerald-200 dark:border-emerald-800">
                                        Liquidada
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 font-black uppercase text-[8px] tracking-widest rounded-xl border border-amber-200 dark:border-amber-800">
                                        Pendente
                                    </span>
                                @endif
                            </td>

                            {{-- COLUNA VENCIMENTO --}}
                            <td class="p-6">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold {{ $isOverdue ? 'text-red-500' : 'text-zinc-600 dark:text-zinc-400' }}">
                                        {{ \Carbon\Carbon::parse($inv->due_date)->format('d M, Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <span class="text-[8px] font-black text-red-600 uppercase italic">Atraso detetado</span>
                                    @endif
                                </div>
                            </td>

                            {{-- COLUNA VALOR --}}
                            <td class="p-6 text-right px-10">
                                <div class="flex flex-col items-end">
                                    <span class="text-xl font-black {{ $inv->status === 'paga' ? 'text-emerald-600' : 'text-zinc-900 dark:text-white' }} tracking-tighter italic">
                                        <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                            {{ number_format($inv->total_amount, 2, ',', ' ') }} €
                                        </span>
                                    </span>
                                    <span class="text-[8px] font-black text-zinc-400 uppercase opacity-70">
                                        Base: <span :class="privacyMode ? 'blur-sm' : ''">{{ number_format($inv->amount_excl_vat, 2, ',', ' ') }}€</span>
                                    </span>
                                </div>
                            </td>

                            {{-- AÇÕES --}}
                            <td class="p-6 text-right pr-8">
                                <div class="flex items-center gap-2 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                    @if($inv->status === 'pendente')
                                        <flux:button wire:click="markAsPaid({{ $inv->id }})" variant="ghost" icon="check" size="xs" color="emerald" class="rounded-lg" />
                                    @endif
                                    <flux:button wire:click="delete({{ $inv->id }})" wire:confirm="Eliminar registo de venda?" variant="ghost" icon="trash" size="xs" color="red" class="rounded-lg" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-24 text-center">
                                <div class="flex flex-col items-center justify-center gap-4">
                                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                                        <flux:icon name="newspaper" class="size-12 text-zinc-200 dark:text-zinc-700" />
                                    </div>
                                    <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Sem Vendas Registadas</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/20">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    {{-- 4. MODAL: EMISSÃO DE REGISTO DE VENDA (DESIGN EXECUTIVO) --}}
    <flux:modal name="add-invoice-modal" position="center" class="md:w-[600px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-lg shadow-emerald-500/20">
                    <flux:icon name="presentation-chart-line" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Emitir Registo de Venda</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Regista a faturação emitida para controlo de tesouraria.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Linha 1: Cliente e Número --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Identificação do Cliente</flux:label>
                        <flux:input
                            wire:model="client_name"
                            placeholder="Ex: Acme Portugal, Lda"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nº de Documento / Referência</flux:label>
                        <flux:input
                            wire:model="invoice_number"
                            placeholder="Ex: FT 2026/001"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner uppercase"
                        />
                    </div>
                </div>

                {{-- Linha 2: Valor Base e Vencimento --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Montante Base (S/ IVA)</flux:label>
                        <flux:input
                            wire:model.live="amount_excl_vat"
                            type="number"
                            step="0.01"
                            class="font-black text-2xl text-zinc-900 dark:text-white !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-16 shadow-inner"
                            placeholder="0,00"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Data Limite de Pagamento</flux:label>
                        <flux:input wire:model="due_date" type="date" class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-16 shadow-inner" />
                    </div>
                </div>

                {{-- ÁREA DE CÁLCULO E PREVIEW (ESTILO RECEIPT) --}}
                <div class="p-6 bg-zinc-900 rounded-[2rem] border border-zinc-800 space-y-4 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <flux:icon name="receipt-percent" class="size-20 text-white" />
                    </div>

                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-[0.2em] mb-4">Resumo da Transação</p>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-zinc-400">
                                <span class="text-xs font-bold uppercase">IVA Estimado (23%)</span>
                                <span class="text-sm font-black">{{ number_format($vat_amount, 2, ',', ' ') }} €</span>
                            </div>

                            <div class="pt-4 border-t border-white/5 flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] font-black uppercase text-emerald-500 tracking-widest leading-none">Total Bruto</p>
                                    <p class="text-[8px] text-zinc-500 font-medium italic mt-1 uppercase">Valor a receber do cliente</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-4xl font-black text-white tracking-tighter italic">
                                        {{ number_format($total_amount, 2, ',', ' ') }} €
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] bg-emerald-600 hover:bg-emerald-500 border-none font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20 h-14 rounded-2xl">
                    Confirmar e Emitir
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Faturação e Vendas
        </p>
    </footer>
</div>
