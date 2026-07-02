<div class="space-y-10 pb-20">

    {{-- 1. HEADER DE VENDAS --}}
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
                    <h1 class="text-4xl font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        Faturação & Vendas
                    </h1>
                    <flux:badge variant="success" class="bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">
                        Sales Hub
                    </flux:badge>
                </div>
                <p class="text-sm text-zinc-500 italic mt-2">Monitorização de receitas emitidas e controlo de recebíveis</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">

            {{-- Botão Nova Fatura --}}
            <flux:modal.trigger name="add-invoice-modal">
                <button
    wire:click="openInvoiceModal"
    class="px-4 py-2 bg-brand-600 text-white rounded-xl font-black uppercase tracking-widest"
>
    Nova Fatura
</button>

            </flux:modal.trigger>

            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

            {{-- Filtro de Estado --}}
            <flux:select
                wire:model.live="statusFilter"
                class="w-40 text-[10px] font-black uppercase tracking-widest !bg-transparent border-none"
            >
                <option value="">Todos os estados</option>
                <option value="pendente">Pendentes</option>
                <option value="paga">Pagas</option>
            </flux:select>

            <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
        </div>
    </div>

    {{-- 2. KPI DASHBOARD --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Total Recebido --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-emerald-500/30 transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="check-circle" variant="outline" class="size-6" />
                </div>
                <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">
                    Liquidez Real
                </span>
            </div>

            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Total Recebido (Pagas)</p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                {{ number_format($totalBilled, 2, ',', ' ') }} €
            </h3>
        </div>

        {{-- Total Pendente --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-amber-500/30 transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-2xl text-amber-600">
                    <flux:icon name="clock" variant="outline" class="size-6" />
                </div>
                <span class="text-[9px] font-black text-amber-600 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">
                    Pendente
                </span>
            </div>

            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Contas a Receber</p>
            <h3 class="text-4xl font-black text-amber-600 tracking-tighter">
                {{ number_format($totalPending, 2, ',', ' ') }} €
            </h3>
        </div>

        {{-- IVA --}}
        <div class="relative bg-zinc-950 p-8 rounded-[2.5rem] border border-zinc-800 shadow-2xl overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10"></div>

            <div class="relative z-10">
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">IVA Total Acumulado</p>
                <h3 class="text-4xl font-black text-white tracking-tighter italic">
                    {{ number_format($vatToPay, 2, ',', ' ') }} €
                </h3>
                <p class="mt-4 text-[9px] text-zinc-600 font-bold uppercase tracking-widest">Reserva Fiscal Obrigatória</p>
            </div>
        </div>
    </div>

    {{-- 3. SALES LEDGER --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">

        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-900/30 flex justify-between items-center">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Pipeline de Receitas</h2>
                <p class="text-lg font-black uppercase italic tracking-tighter text-zinc-800 dark:text-zinc-200">Histórico de Faturação</p>
            </div>

            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm">
                {{ $invoices->total() }} Documentos
            </flux:badge>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                        <th class="p-6">Nº Documento / Cliente</th>
                        <th class="p-6 text-center">Estado</th>
                        <th class="p-6">Vencimento</th>
                        <th class="p-6 text-right px-10">Total</th>
                        <th class="p-6 w-10"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($invoices as $inv)
                        @php
                            $isOverdue = $inv->status === 'pendente' && \Carbon\Carbon::parse($inv->due_date)->isPast();
                        @endphp

                        <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all group/row">

                            {{-- Identificação --}}
                            <td class="p-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black uppercase dark:text-white">#{{ $inv->invoice_number }}</span>
                                    <span class="text-[10px] text-zinc-500 font-bold uppercase">{{ $inv->client_name }}</span>
                                </div>
                            </td>

                            {{-- Estado --}}
                            <td class="p-6 text-center">
                                @if($inv->status === 'paga')
                                    <span class="inline-flex px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-black uppercase text-[8px] rounded-xl">
                                        Liquidada
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 font-black uppercase text-[8px] rounded-xl">
                                        Pendente
                                    </span>
                                @endif
                            </td>

                            {{-- Vencimento --}}
                            <td class="p-6">
                                <span class="text-xs font-bold {{ $isOverdue ? 'text-red-500' : 'text-zinc-600 dark:text-zinc-400' }}">
                                    {{ \Carbon\Carbon::parse($inv->due_date)->format('d M, Y') }}
                                </span>

                                @if($isOverdue)
                                    <span class="text-[8px] font-black text-red-600 uppercase italic">Atraso detetado</span>
                                @endif
                            </td>

                            {{-- Valor --}}
                            <td class="p-6 text-right px-10">
                                <span class="text-xl font-black {{ $inv->status === 'paga' ? 'text-emerald-600' : 'text-zinc-900 dark:text-white' }} italic">
                                    {{ number_format($inv->total_amount, 2, ',', ' ') }} €
                                </span>
                                <span class="text-[8px] font-black text-zinc-400 uppercase opacity-70">
                                    Base: {{ number_format($inv->amount_excl_vat, 2, ',', ' ') }}€
                                </span>
                            </td>

                            {{-- Ações --}}
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
                                <div class="flex flex-col items-center gap-4">
                                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                                        <flux:icon name="newspaper" class="size-12 text-zinc-200 dark:text-zinc-700" />
                                    </div>
                                    <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">
                                        Sem Vendas Registadas
                                    </p>
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

    {{-- 4. MODAL DE EMISSÃO --}}
    @include('livewire.business.partials.invoice-modal')

    {{-- RODAPÉ --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Faturação e Vendas
        </p>
    </footer>

</div>
