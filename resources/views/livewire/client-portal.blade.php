<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-8 lg:px-8">
        <div class="space-y-6">
            {{-- HEADER --}}
            <header class="relative overflow-hidden rounded-[2rem] border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="absolute -right-24 -top-24 size-72 rounded-full bg-emerald-500/10 blur-3xl"></div>
                <div class="relative flex flex-col gap-6 p-5 sm:p-7 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex min-w-0 items-center gap-4 sm:gap-5">
                        @if($workspace->logo_url)
                            <img src="{{ $workspace->logo_url }}" alt="{{ $workspace->name }}" class="size-16 shrink-0 rounded-2xl border border-zinc-200 object-cover shadow-sm sm:size-20 dark:border-zinc-700">
                        @else
                            <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-2xl font-black text-white shadow-lg shadow-emerald-600/20 sm:size-20 sm:text-3xl">
                                {{ strtoupper(substr($workspace->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <span class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Portal do Cliente</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $workspace->name }}</span>
                            </div>
                            <h1 class="truncate text-2xl font-black tracking-tight sm:text-3xl">Olá, {{ $client->name }}</h1>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Acompanha projetos, propostas, faturação e suporte num só lugar.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        @if(!empty($companyTaxNumber))
                            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800/70">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">NIF da empresa</p>
                                <p class="mt-1 text-sm font-black tracking-wide">{{ $companyTaxNumber }}</p>
                            </div>
                        @endif
                        <a href="/" class="inline-flex h-12 items-center justify-center rounded-2xl bg-zinc-950 px-5 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-emerald-600 dark:bg-white dark:text-zinc-950 dark:hover:bg-emerald-500 dark:hover:text-white">
                            Sair
                        </a>
                    </div>
                </div>
            </header>

            {{-- KPI --}}
            <section class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Projetos</span>
                        <flux:icon name="briefcase" class="size-4 text-zinc-400" />
                    </div>
                    <p class="mt-3 text-2xl font-black">{{ $portalStats['projects'] }}</p>
                    <p class="mt-1 text-[10px] text-zinc-400">Em acompanhamento</p>
                </div>
                <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Tarefas</span>
                        <flux:icon name="check-circle" class="size-4 text-zinc-400" />
                    </div>
                    <p class="mt-3 text-2xl font-black">{{ $portalStats['openTasks'] }}</p>
                    <p class="mt-1 text-[10px] text-zinc-400">Por concluir</p>
                </div>
                <div class="rounded-2xl border border-indigo-200 bg-white p-4 shadow-sm dark:border-indigo-900/50 dark:bg-zinc-900 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Propostas</span>
                        <flux:icon name="document-text" class="size-4 text-indigo-500" />
                    </div>
                    <p class="mt-3 text-2xl font-black text-indigo-600">{{ $portalStats['pendingProposals'] }}</p>
                    <p class="mt-1 text-[10px] text-zinc-400">A aguardar decisão</p>
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm dark:border-emerald-900/50 dark:bg-zinc-900 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Suporte</span>
                        <flux:icon name="chat-bubble-left-right" class="size-4 text-emerald-600" />
                    </div>
                    <p class="mt-3 text-2xl font-black text-emerald-600">{{ $portalStats['openTickets'] }}</p>
                    <p class="mt-1 text-[10px] text-zinc-400">Pedidos em aberto</p>
                </div>
                <div class="col-span-2 rounded-2xl bg-zinc-950 p-4 shadow-sm dark:bg-black sm:p-5 lg:col-span-1">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Faturação</span>
                        <flux:icon name="banknotes" class="size-4 text-zinc-500" />
                    </div>
                    <p class="mt-3 text-xl font-black tracking-tight text-white">{{ number_format($portalStats['invoiceTotal'], 2, ',', ' ') }} €</p>
                    <p class="mt-1 text-[10px] text-zinc-500">Total registado</p>
                </div>
            </section>

            {{-- PROPOSTAS PENDENTES --}}
            @if($proposals->count() > 0)
                <section class="overflow-hidden rounded-[2rem] border border-indigo-200 bg-indigo-600 shadow-lg shadow-indigo-600/10 dark:border-indigo-900/60">
                    <div class="flex flex-col gap-5 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-white/15">
                                <flux:icon name="document-text" class="size-5 text-white" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-100">Ação necessária</p>
                                <h2 class="mt-1 text-lg font-black text-white">Tens {{ $proposals->count() }} proposta(s) para analisar</h2>
                                <p class="mt-1 text-sm text-indigo-100">Revê as condições e responde diretamente a partir do portal.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($proposals as $proposal)
                                <button type="button" wire:click="approveProposal({{ $proposal->id }})" wire:loading.attr="disabled" class="inline-flex h-11 items-center justify-center rounded-xl bg-white px-5 text-[10px] font-black uppercase tracking-widest text-indigo-600 transition hover:bg-indigo-50 disabled:opacity-60">Aceitar</button>
                                <button type="button" wire:click="declineProposal({{ $proposal->id }})" wire:loading.attr="disabled" class="inline-flex h-11 items-center justify-center rounded-xl bg-indigo-700 px-5 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-indigo-800 disabled:opacity-60">Recusar</button>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                {{-- PROJETOS --}}
                <section class="space-y-4 xl:col-span-8">
                    <div class="flex items-end justify-between gap-4 px-1">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Trabalho</p>
                            <h2 class="mt-1 text-xl font-black tracking-tight">Os teus projetos</h2>
                        </div>
                        <span class="text-xs font-medium text-zinc-400">{{ $projects->count() }} total</span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @forelse($projects as $project)
                            @php($progress = max(0, min(100, (float) ($project->progress ?? 0))))
                            <article class="group rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-emerald-800">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-lg font-black tracking-tight">{{ $project->name }}</h3>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $project->tasks_count }} tarefa(s) por concluir</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-zinc-100 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ $project->status }}</span>
                                </div>
                                <div class="mt-7">
                                    <div class="mb-2 flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                                        <span class="text-zinc-400">Progresso</span>
                                        <span class="text-emerald-600">{{ number_format($progress, 0) }}%</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-[1.75rem] border border-dashed border-zinc-300 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
                                <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                                    <flux:icon name="briefcase" class="size-5 text-zinc-400" />
                                </div>
                                <h3 class="mt-4 text-sm font-black">Ainda não existem projetos</h3>
                                <p class="mt-1 text-xs text-zinc-400">Quando a empresa criar um projeto, este aparecerá aqui.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- SUPORTE --}}
                <aside class="space-y-4 xl:col-span-4">
                    <div class="flex items-end justify-between gap-4 px-1">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Comunicação</p>
                            <h2 class="mt-1 text-xl font-black tracking-tight">Suporte</h2>
                        </div>
                        <flux:modal.trigger name="support-modal">
                            <button type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-[10px] font-black uppercase tracking-widest text-white shadow-sm transition hover:bg-emerald-500">
                                <flux:icon name="plus" class="size-3.5" /> Novo pedido
                            </button>
                        </flux:modal.trigger>
                    </div>

                    <div class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse($tickets as $ticket)
                                <button type="button" wire:click="setActiveTicket({{ $ticket->id }})" class="block w-full p-5 text-left transition hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ $ticket->status }}</span>
                                        <span class="text-[10px] font-medium text-zinc-400">{{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-3 truncate text-sm font-black">{{ str_replace('[PORTAL] ', '', $ticket->subject) }}</p>
                                    <p class="mt-1 truncate text-xs text-zinc-400">{{ $ticket->messages->last()->message ?? 'Sem mensagens' }}</p>
                                    <span class="mt-3 inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-widest text-emerald-600">Abrir conversa <span aria-hidden="true">→</span></span>
                                </button>
                            @empty
                                <div class="p-8 text-center">
                                    <div class="mx-auto flex size-11 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="chat-bubble-left-right" class="size-5 text-zinc-400" />
                                    </div>
                                    <p class="mt-3 text-sm font-black">Tudo tranquilo</p>
                                    <p class="mt-1 text-xs text-zinc-400">Ainda não tens pedidos de suporte.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </aside>

                {{-- ATIVIDADE --}}
                <section class="xl:col-span-8 rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-xl bg-emerald-500/10">
                            <flux:icon name="bolt" class="size-5 text-emerald-600" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Atualizações</p>
                            <h2 class="text-lg font-black tracking-tight">Atividade recente</h2>
                        </div>
                    </div>
                    <div class="mt-6">
                        @forelse($recentActivity as $act)
                            <div class="flex items-start gap-4 border-b border-zinc-100 py-4 last:border-0 dark:border-zinc-800">
                                <div class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-500/10">
                                    <span class="size-2 rounded-full bg-emerald-500"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold">{{ $act->title }}</p>
                                    <div class="mt-1 flex flex-wrap gap-x-2 gap-y-1 text-[10px] text-zinc-400">
                                        <span>{{ $act->project->name }}</span>
                                        <span>•</span>
                                        <span>Concluído {{ $act->completed_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-zinc-50 p-7 text-center dark:bg-zinc-800/50">
                                <p class="text-sm font-bold">Ainda não há atividade recente.</p>
                                <p class="mt-1 text-xs text-zinc-400">As atualizações de tarefas concluídas aparecerão aqui.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- FATURAÇÃO --}}
                <section class="xl:col-span-4 rounded-[1.75rem] border border-zinc-200 bg-white p-5 shadow-sm sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                            <flux:icon name="document-text" class="size-5 text-zinc-500" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Financeiro</p>
                            <h2 class="text-lg font-black tracking-tight">Faturação</h2>
                        </div>
                    </div>
                    <div class="mt-5 space-y-2">
                        @forelse($invoices as $invoice)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-zinc-100 p-3 dark:border-zinc-800">
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-black">#{{ $invoice->number }}</p>
                                    <p class="mt-1 text-[10px] text-zinc-400">{{ $invoice->created_at->format('d/m/Y') }}</p>
                                </div>
                                <p class="shrink-0 text-sm font-black">{{ number_format($invoice->total, 2, ',', ' ') }} €</p>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-zinc-50 p-7 text-center dark:bg-zinc-800/50">
                                <p class="text-sm font-bold">Sem faturas</p>
                                <p class="mt-1 text-xs text-zinc-400">As faturas emitidas serão apresentadas aqui.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- FOOTER CONTACT --}}
            <section class="relative overflow-hidden rounded-[2rem] bg-zinc-950 p-6 text-white sm:p-8 dark:bg-black">
                <div class="absolute -right-16 -top-20 size-64 rounded-full bg-emerald-500/15 blur-3xl"></div>
                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Precisas de ajuda?</p>
                        <h2 class="mt-2 text-xl font-black tracking-tight sm:text-2xl">Fala diretamente com a equipa.</h2>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-400">Envia uma mensagem através do portal e acompanha toda a conversa sem sair desta área.</p>
                    </div>
                    <flux:modal.trigger name="support-modal">
                        <button type="button" class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-emerald-500">
                            <flux:icon name="chat-bubble-left-right" class="size-4" /> Contactar equipa
                        </button>
                    </flux:modal.trigger>
                </div>
            </section>
        </div>
    </div>

    {{-- CONVERSA --}}
    <flux:modal name="view-ticket-modal" class="w-full max-w-2xl !p-0 overflow-hidden rounded-[2rem]">
        <div class="flex h-[min(680px,90vh)] flex-col bg-white dark:bg-zinc-950">
            <div class="relative border-b border-zinc-100 bg-white px-5 py-5 dark:border-zinc-800 dark:bg-zinc-900 sm:px-6">
                <flux:modal.close>
                    <button type="button" aria-label="Fechar" class="absolute left-4 top-4 flex size-9 items-center justify-center rounded-xl text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </flux:modal.close>
                <div class="px-10 text-center">
                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Suporte</p>
                    <h2 class="mt-1 text-base font-black">Conversa com a equipa</h2>
                    <p class="mt-1 text-[10px] text-zinc-400">As tuas mensagens ficam associadas ao pedido de suporte.</p>
                </div>
            </div>

            <div class="flex-1 space-y-5 overflow-y-auto bg-zinc-50/70 p-5 dark:bg-zinc-950 sm:p-6">
                @forelse($activeMessages as $msg)
                    <div class="flex {{ $msg->is_from_client ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[88%] rounded-2xl px-4 py-3 {{ $msg->is_from_client ? 'rounded-br-md bg-emerald-600 text-white' : 'rounded-bl-md border border-zinc-200 bg-white text-zinc-800 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100' }}">
                            <p class="text-sm leading-relaxed">{{ $msg->message }}</p>
                            <p class="mt-2 text-[9px] font-bold uppercase opacity-60">{{ $msg->created_at->format('d/m H:i') }} · {{ $msg->is_from_client ? 'Tu' : 'Empresa' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex h-full items-center justify-center text-center">
                        <div>
                            <flux:icon name="chat-bubble-left-right" class="mx-auto size-8 text-zinc-300 dark:text-zinc-700" />
                            <p class="mt-3 text-sm font-bold">Ainda não há mensagens.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-zinc-100 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:p-5">
                @if($activeTicketId)
                    <form wire:submit.prevent="sendReply" class="flex gap-2">
                        <input wire:model="replyMessage" type="text" maxlength="5000" autocomplete="off" placeholder="Escreve uma resposta..." class="h-12 min-w-0 flex-1 rounded-xl border border-zinc-200 bg-zinc-50 px-4 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        <button type="submit" wire:loading.attr="disabled" class="h-12 rounded-xl bg-emerald-600 px-5 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-emerald-500 disabled:opacity-60">Enviar</button>
                    </form>
                    @error('replyMessage')
                        <p class="mt-2 text-xs font-medium text-red-500">{{ $message }}</p>
                    @enderror
                @endif
            </div>
        </div>
    </flux:modal>

    {{-- NOVO PEDIDO --}}
    <flux:modal name="support-modal" class="w-full max-w-xl !p-0 overflow-hidden rounded-[2rem]">
        <div class="bg-white p-6 dark:bg-zinc-950 sm:p-8">
            <div class="relative pb-6 text-center">
                <flux:modal.close>
                    <button type="button" aria-label="Fechar" class="absolute left-0 top-0 flex size-9 items-center justify-center rounded-xl text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </flux:modal.close>
                <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-emerald-500/10">
                    <flux:icon name="chat-bubble-left-right" class="size-6 text-emerald-600" />
                </div>
                <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-emerald-600">Novo pedido</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight">Como podemos ajudar?</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-zinc-500 dark:text-zinc-400">Envia os detalhes e a equipa da {{ $workspace->name }} responderá através deste portal.</p>
            </div>

            <form wire:submit.prevent="sendTicket" class="space-y-5">
                <flux:input wire:model="subject" label="Assunto" maxlength="150" placeholder="Ex.: Dúvida sobre projeto ou fatura" />
                <flux:textarea wire:model="message" label="Mensagem" maxlength="5000" rows="6" placeholder="Explica o que precisas de saber ou resolver..." />
                <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                    <flux:modal.close>
                        <button type="button" class="h-11 rounded-xl px-5 text-[10px] font-black uppercase tracking-widest text-zinc-500 transition hover:bg-zinc-100 dark:hover:bg-zinc-800">Cancelar</button>
                    </flux:modal.close>
                    <button type="submit" wire:loading.attr="disabled" class="h-11 rounded-xl bg-emerald-600 px-6 text-[10px] font-black uppercase tracking-widest text-white shadow-sm transition hover:bg-emerald-500 disabled:opacity-60">Enviar pedido</button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
