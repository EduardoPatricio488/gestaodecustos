<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
        {{-- Header --}}
        <header class="relative overflow-hidden rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <div class="absolute -right-24 -top-24 size-72 rounded-full bg-brand-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4 sm:gap-5">
                    <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-brand-600 text-2xl font-black uppercase italic text-white shadow-lg shadow-brand-500/20 sm:size-20 sm:text-3xl">
                        {{ substr($supplier->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <span class="rounded-full border border-brand-500/20 bg-brand-500/10 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-brand-600 dark:text-brand-400">Fornecedor verificado</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $workspace->name }}</span>
                        </div>
                        <h1 class="truncate text-2xl font-black tracking-tight sm:text-3xl">Olá, {{ $supplier->name }}</h1>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Bem-vindo ao seu espaço de parceiro.</p>
                    </div>
                </div>
                <a href="{{ route('supplier.portal') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-zinc-900 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-brand-600 dark:bg-zinc-800">
                    <flux:icon name="arrow-left-start-on-rectangle" class="size-4" />
                    Sair do portal
                </a>
            </div>
        </header>

        {{-- KPI --}}
        <section class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800"><flux:icon name="arrows-right-left" class="size-5" /></div>
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Movimentos</p>
                <p class="mt-1 text-2xl font-black">{{ $portalStats['movements'] }}</p>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 flex size-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600"><flux:icon name="banknotes" class="size-5" /></div>
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Total liquidado</p>
                <p class="mt-1 text-xl font-black text-emerald-600">{{ number_format($portalStats['totalPaid'], 2, ',', ' ') }} €</p>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 flex size-10 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600"><flux:icon name="chat-bubble-left-right" class="size-5" /></div>
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Pedidos em aberto</p>
                <p class="mt-1 text-2xl font-black">{{ $portalStats['openTickets'] }}</p>
            </div>
            <div class="rounded-2xl bg-zinc-950 p-5 shadow-sm dark:border dark:border-zinc-800">
                <div class="mb-4 flex size-10 items-center justify-center rounded-xl bg-white/10 text-brand-400"><flux:icon name="clock" class="size-5" /></div>
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Último movimento</p>
                <p class="mt-1 truncate text-lg font-black text-white">{{ $portalStats['lastMovement'] ? number_format($portalStats['lastMovement']->amount, 2, ',', ' ').' €' : 'Sem movimentos' }}</p>
            </div>
        </section>

        {{-- Ações rápidas --}}
        <section class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="relative overflow-hidden rounded-[2rem] bg-brand-600 p-6 text-white shadow-xl shadow-brand-500/10 sm:p-8">
                <div class="absolute -right-10 -top-10 size-40 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="mb-3 flex size-11 items-center justify-center rounded-xl bg-white/15"><flux:icon name="document-arrow-up" class="size-6" /></div>
                        <h2 class="text-lg font-black uppercase tracking-tight">Submeter uma fatura</h2>
                        <p class="mt-1 max-w-md text-sm text-brand-100">Envie um documento para a empresa tratar digitalmente.</p>
                    </div>
                    <flux:modal.trigger name="upload-invoice-modal">
                        <button class="rounded-xl bg-white px-5 py-3 text-[10px] font-black uppercase tracking-widest text-brand-600 shadow-lg transition hover:bg-brand-50">Submeter fatura</button>
                    </flux:modal.trigger>
                </div>
            </div>
            <div class="rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="mb-3 flex size-11 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon name="chat-bubble-left-right" class="size-6" /></div>
                        <h2 class="text-lg font-black uppercase tracking-tight">Precisa de ajuda?</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Abra um pedido e fale diretamente com a equipa.</p>
                    </div>
                    <flux:modal.trigger name="support-modal">
                        <button class="rounded-xl bg-zinc-900 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-brand-600 dark:bg-zinc-800">Novo pedido</button>
                    </flux:modal.trigger>
                </div>
            </div>
        </section>

        <div class="mt-8 grid gap-8 lg:grid-cols-12">
            {{-- Movimentos --}}
            <section class="lg:col-span-8">
                <div class="mb-4 flex items-center justify-between px-1">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-widest">Histórico financeiro</h2>
                        <p class="mt-1 text-xs text-zinc-400">Movimentos associados à sua conta de fornecedor.</p>
                    </div>
                    <span class="rounded-full bg-zinc-100 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-zinc-500 dark:bg-zinc-800">{{ $portalStats['movements'] }} registos</span>
                </div>
                <div class="overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[620px] text-left">
                            <thead class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950/50">
                                <tr class="text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                    <th class="px-6 py-4">Data</th>
                                    <th class="px-6 py-4">Documento</th>
                                    <th class="px-6 py-4 text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @forelse($history as $item)
                                    <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-6 py-5 text-sm font-bold">{{ \Carbon\Carbon::parse($item->spent_at)->translatedFormat('d M Y') }}</td>
                                        <td class="px-6 py-5">
                                            <p class="max-w-sm truncate text-sm font-semibold text-zinc-600 dark:text-zinc-300">{{ $item->title }}</p>
                                        </td>
                                        <td class="px-6 py-5 text-right text-sm font-black">{{ number_format($item->amount, 2, ',', ' ') }} €</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-6 py-16 text-center"><div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800"><flux:icon name="document-text" class="size-6" /></div><p class="mt-3 text-xs font-black uppercase tracking-widest text-zinc-400">Ainda não existem movimentos</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- Suporte --}}
            <aside class="space-y-6 lg:col-span-4">
                <div>
                    <div class="mb-4 px-1">
                        <h2 class="text-sm font-black uppercase tracking-widest">Comunicação</h2>
                        <p class="mt-1 text-xs text-zinc-400">Acompanhe os seus pedidos.</p>
                    </div>
                    <div class="overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        @forelse($tickets as $ticket)
                            <button wire:click="setActiveTicket({{ $ticket->id }})" class="w-full border-b border-zinc-100 p-5 text-left transition last:border-0 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/60">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full bg-zinc-100 px-2 py-1 text-[8px] font-black uppercase tracking-widest text-zinc-500 dark:bg-zinc-800">{{ $ticket->status }}</span>
                                    <span class="text-[9px] font-bold text-zinc-400">{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-3 truncate text-xs font-black uppercase">{{ str_replace('[FORNECEDOR] ', '', $ticket->subject) }}</p>
                            </button>
                        @empty
                            <div class="p-10 text-center"><flux:icon name="chat-bubble-left-right" class="mx-auto size-7 text-zinc-300" /><p class="mt-3 text-[9px] font-black uppercase tracking-widest text-zinc-400">Sem pedidos de suporte</p></div>
                        @endforelse
                        <div class="border-t border-zinc-100 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                            <flux:modal.trigger name="support-modal"><button class="w-full rounded-xl bg-zinc-900 py-3 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-brand-600 dark:bg-zinc-800">Abrir pedido</button></flux:modal.trigger>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h3 class="border-b border-zinc-100 pb-4 text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:border-zinc-800">Dados do fornecedor</h3>
                    <div class="space-y-5 pt-5">
                        <div><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">NIF</p><p class="mt-1 font-mono text-sm font-bold">{{ preg_replace('/^(\d{3})(\d{3})(\d{3})$/', '$1 $2 $3', preg_replace('/\D+/', '', (string) $supplier->tax_number)) ?: 'Não registado' }}</p></div>
                        <div><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Condições de pagamento</p><p class="mt-1 text-sm font-black text-brand-600">{{ $supplier->payment_terms ?? 'Pronto Pagamento' }}</p></div>
                        <div><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Morada</p><p class="mt-1 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $supplier->address ?? 'Não especificada' }}</p></div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    {{-- Chat --}}
    <flux:modal name="view-ticket-modal" class="w-full max-w-2xl !p-0 overflow-hidden rounded-[2rem]" wire:ignore.self>
        <div class="flex h-[620px] flex-col bg-white dark:bg-zinc-950">
            <div class="flex items-center justify-between border-b border-zinc-100 bg-zinc-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-900/80">
                <div><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Comunicação</p><h2 class="mt-1 text-lg font-black">Conversa com a empresa</h2></div>
                <flux:modal.close><flux:button variant="ghost" icon="x-mark" size="sm" /></flux:modal.close>
            </div>
            <div class="flex-1 space-y-5 overflow-y-auto p-5 sm:p-6">
                @foreach($activeMessages as $msg)
                    <div class="flex {{ $msg->is_admin_reply ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[85%] rounded-2xl p-4 {{ $msg->is_admin_reply ? 'rounded-tl-md bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-100' : 'rounded-tr-md bg-brand-600 text-white' }}">
                            <p class="text-sm leading-relaxed">{{ $msg->message }}</p>
                            <p class="mt-2 text-[8px] font-black uppercase opacity-60">{{ $msg->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-zinc-100 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                <form wire:submit.prevent="sendReply" class="flex gap-2">
                    <input wire:model="replyMessage" type="text" placeholder="Escreva uma mensagem..." class="h-12 min-w-0 flex-1 rounded-xl border-0 bg-zinc-100 px-4 text-sm outline-none ring-brand-500 focus:ring-2 dark:bg-zinc-800" />
                    <flux:button type="submit" variant="primary" class="h-12 rounded-xl px-5 font-black uppercase text-[10px]">Enviar</flux:button>
                </form>
            </div>
        </div>
    </flux:modal>

    {{-- Fatura --}}
    <flux:modal name="upload-invoice-modal" class="w-full max-w-lg rounded-[2rem]" wire:ignore.self>
        <div class="p-2">
            <div class="mb-7"><div class="mb-3 flex size-11 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600"><flux:icon name="document-arrow-up" class="size-6" /></div><h2 class="text-2xl font-black tracking-tight">Submeter fatura</h2><p class="mt-1 text-sm text-zinc-500">Envie o documento para processamento.</p></div>
            <form wire:submit.prevent="submitInvoice" class="space-y-5">
                <flux:input wire:model="invoice_amount" label="Valor total (€)" type="number" step="0.01" />
                <input type="file" wire:model="invoice_doc" accept=".pdf,.jpg,.jpeg,.png" class="block w-full rounded-xl border border-zinc-200 bg-zinc-50 p-3 text-xs dark:border-zinc-700 dark:bg-zinc-800" />
                <flux:button type="submit" variant="primary" class="h-12 w-full rounded-xl font-black uppercase text-[10px]">Confirmar envio</flux:button>
            </form>
        </div>
    </flux:modal>

    {{-- Novo pedido --}}
    <flux:modal name="support-modal" class="w-full max-w-lg rounded-[2rem]" wire:ignore.self>
        <div class="p-2">
            <div class="mb-7"><div class="mb-3 flex size-11 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600"><flux:icon name="chat-bubble-left-right" class="size-6" /></div><h2 class="text-2xl font-black tracking-tight">Novo pedido</h2><p class="mt-1 text-sm text-zinc-500">Envie uma mensagem à equipa.</p></div>
            <form wire:submit.prevent="sendTicket" class="space-y-5">
                <flux:input wire:model="subject" label="Assunto" placeholder="Ex.: Pagamento, documento ou dados" />
                <flux:textarea wire:model="message" label="Mensagem" rows="5" placeholder="Descreva o que precisa..." />
                <flux:button type="submit" variant="primary" class="h-12 w-full rounded-xl font-black uppercase text-[10px]">Enviar pedido</flux:button>
            </form>
        </div>
    </flux:modal>
</div>
