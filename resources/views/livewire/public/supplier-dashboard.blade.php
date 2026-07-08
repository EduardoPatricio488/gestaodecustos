<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 p-6 md:p-12 text-left">
    {{-- Contentor de Largura Total --}}
    <div class="max-w-[1400px] mx-auto space-y-12">

        {{-- 1. HEADER: BRANDING --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden text-left w-full">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/5 blur-[100px] rounded-full -mr-32 -mt-32"></div>

            <div class="flex items-center gap-6 relative z-10">
                <div class="size-20 rounded-2xl bg-brand-600 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-brand-500/20 uppercase italic shrink-0">
                    {{ substr($supplier->name, 0, 1) }}
                </div>
                <div class="text-left">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2 py-0.5 bg-brand-500/10 text-brand-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-brand-500/20">Fornecedor Verificado</span>
                        <h2 class="text-xs font-black text-zinc-400 uppercase tracking-widest">{{ $workspace->name }}</h2>
                    </div>
                    <h1 class="text-4xl font-black dark:text-white tracking-tighter italic leading-none">Painel de Parceiro: {{ $supplier->name }}</h1>
                </div>
            </div>

            <a href="{{ route('supplier.portal') }}" class="px-6 py-3 bg-zinc-900 dark:bg-zinc-800 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all shadow-xl z-10">
                Sair do Portal
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- COLUNA ESQUERDA: OPERACIONAL (8 Colunas) --}}
            <div class="lg:col-span-8 space-y-12">

                {{-- BANNER DE SUBMISSÃO --}}
                <div class="bg-brand-600 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-xl shadow-brand-500/20">
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 text-left">
                        <div class="flex items-center gap-5">
                            <div class="p-4 bg-white/10 rounded-2xl shadow-inner"><flux:icon name="cloud-arrow-up" class="size-8 text-white" /></div>
                            <div class="text-left">
                                <h3 class="font-black uppercase text-lg tracking-tight leading-none">Submissão Digital de Faturas</h3>
                                <p class="text-xs text-brand-100 opacity-90 font-medium mt-2">Envie os seus documentos para processamento imediato pela nossa contabilidade.</p>
                            </div>
                        </div>
                        <flux:modal.trigger name="upload-invoice-modal">
                            <button class="px-10 py-4 bg-white text-brand-600 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-brand-50 transition-all shadow-lg shrink-0">Submeter Agora</button>
                        </flux:modal.trigger>
                    </div>
                </div>

                {{-- HISTÓRICO DE PAGAMENTOS --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 text-left px-2">
                        <flux:icon name="banknotes" class="size-5 text-zinc-400" />
                        <h3 class="font-black dark:text-white uppercase text-sm tracking-widest italic">Histórico de Movimentos</h3>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden w-full">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-zinc-50/50 dark:bg-zinc-950/50 text-[9px] font-black uppercase text-zinc-400 tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                                    <th class="p-6">Data</th>
                                    <th class="p-6">Referência / Documento</th>
                                    <th class="p-6 text-right px-10">Valor Liquidado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                                @forelse($history as $item)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-colors">
                                        <td class="p-6">
                                            <span class="text-sm font-black dark:text-white uppercase">{{ \Carbon\Carbon::parse($item->spent_at)->translatedFormat('d M, Y') }}</span>
                                        </td>
                                        <td class="p-6 italic text-zinc-500 text-sm">"{{ $item->title }}"</td>
                                        <td class="p-6 text-right px-10 font-black text-lg dark:text-white">{{ number_format($item->amount, 2, ',', ' ') }}€</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="p-20 text-center text-zinc-400 uppercase text-[10px] font-black italic">Sem movimentos processados no sistema.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- COLUNA DIREITA: ADMIN & CHAT (4 Colunas) --}}
            <div class="lg:col-span-4 space-y-10 text-left">

                {{-- MENSAGENS --}}
                <div class="space-y-4">
                    <h3 class="font-black dark:text-white uppercase text-[11px] tracking-widest px-2 flex items-center gap-2">
                        <flux:icon name="chat-bubble-left-right" class="size-4" /> Centro de Mensagens
                    </h3>
                    <div class="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm text-left">
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse($tickets as $ticket)
                                <button wire:click="setActiveTicket({{ $ticket->id }})" class="w-full p-6 flex flex-col gap-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all text-left group">
                                    <div class="flex justify-between items-start">
                                        <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500">{{ $ticket->status }}</span>
                                        <span class="text-[9px] text-zinc-400 font-bold uppercase">{{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs font-black dark:text-white uppercase group-hover:text-brand-600 transition-colors">{{ str_replace('[FORNECEDOR] ', '', $ticket->subject) }}</p>
                                </button>
                            @empty
                                <div class="p-10 text-center text-zinc-400 text-[10px] font-black uppercase italic opacity-50">Sem conversas ativas.</div>
                            @endforelse
                        </div>
                        <div class="p-4 bg-zinc-50/50 dark:bg-zinc-950/50 border-t border-zinc-100 dark:border-zinc-800">
                            <flux:modal.trigger name="support-modal">
                                <button class="w-full py-3 bg-zinc-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-brand-600 transition-all">Novo Pedido / Ticket</button>
                            </flux:modal.trigger>
                        </div>
                    </div>
                </div>

                {{-- FICHA TÉCNICA --}}
                <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] space-y-6 shadow-sm text-left">
                    <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b pb-4">Dados Contratuais</h4>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[9px] font-black text-zinc-400 uppercase">NIF Fiscal</p>
                            <p class="text-sm font-mono font-bold dark:text-white">{{ $supplier->tax_number }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-400 uppercase">Acordo Comercial</p>
                            <p class="text-sm font-black text-brand-600 italic uppercase">{{ $supplier->payment_terms ?? 'Pronto Pagamento' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-400 uppercase">Morada Registada</p>
                            <p class="text-[11px] text-zinc-500 italic font-medium leading-relaxed">"{{ $supplier->address ?? 'N/D' }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CHAT --}}
    <flux:modal name="view-ticket-modal" class="md:w-[600px] !p-0 rounded-[2.5rem] overflow-hidden text-left" wire:ignore.self>
        <div class="flex flex-col h-[600px] bg-white dark:bg-zinc-950">
            <div class="p-6 border-b dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-900/50">
                <div class="text-left"><h2 class="text-sm font-black uppercase">Canal de Comunicação</h2></div>
                <flux:modal.close><flux:button variant="ghost" icon="x-mark" size="sm" /></flux:modal.close>
            </div>
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                @foreach($activeMessages as $msg)
                    <div class="flex {{ $msg->is_admin_reply ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[85%] {{ $msg->is_admin_reply ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white rounded-t-2xl rounded-r-2xl' : 'bg-brand-600 text-white rounded-t-2xl rounded-l-2xl' }} p-4 shadow-sm">
                            <p class="text-sm font-medium leading-relaxed text-left">{{ $msg->message }}</p>
                            <p class="text-[8px] mt-2 font-black uppercase opacity-60">{{ $msg->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="p-6 border-t dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <form wire:submit.prevent="sendReply" class="flex gap-3">
                    <input wire:model="replyMessage" type="text" placeholder="Escreva a resposta..." class="flex-1 bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl px-4 text-sm h-12" />
                    <flux:button type="submit" variant="primary" class="!bg-emerald-600 h-12 rounded-xl font-black uppercase text-[10px] px-8 text-white">Enviar</flux:button>
                </form>
            </div>
        </div>
    </flux:modal>

    {{-- MODAL UPLOAD --}}
    <flux:modal name="upload-invoice-modal" class="md:w-[500px] !p-10 rounded-[2.5rem] text-left" wire:ignore.self>
        <h2 class="text-2xl font-black uppercase italic tracking-tighter">Submeter Documento</h2>
        <form wire:submit.prevent="submitInvoice" class="space-y-6 mt-8">
            <flux:input wire:model="invoice_amount" label="Valor Total (€)" type="number" step="0.01" />
            <input type="file" wire:model="invoice_doc" class="w-full text-xs" />
            <flux:button type="submit" variant="primary" class="w-full h-14 rounded-2xl font-black uppercase !bg-brand-600 text-white shadow-lg shadow-brand-500/20">Confirmar Envio</flux:button>
        </form>
    </flux:modal>

    {{-- MODAL NOVO TICKET --}}
    <flux:modal name="support-modal" class="md:w-[500px] !p-10 rounded-[2.5rem] text-left" wire:ignore.self>
        <h2 class="text-2xl font-black uppercase italic tracking-tighter">Novo Pedido de Assistência</h2>
        <form wire:submit.prevent="sendTicket" class="space-y-6 mt-8">
            <flux:input wire:model="subject" label="Assunto" placeholder="Ex: Erro no pagamento ou alteração de dados..." />
            <flux:textarea wire:model="message" label="Mensagem" rows="5" placeholder="Explique detalhadamente o seu pedido..." />
            <flux:button type="submit" variant="primary" class="w-full h-14 rounded-2xl font-black uppercase !bg-brand-600 text-white shadow-lg shadow-brand-500/20">Iniciar Chat</flux:button>
        </form>
    </flux:modal>
</div>
