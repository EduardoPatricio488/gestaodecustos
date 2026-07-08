<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 p-6 md:p-12 text-left">
    <div class="max-w-[1400px] mx-auto space-y-12">

        {{-- 1. HEADER: BRANDING --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full -mr-32 -mt-32"></div>

            <div class="flex items-center gap-6 relative z-10 text-left">
                @if($workspace->logo_url)
                    <img src="{{ $workspace->logo_url }}" class="size-20 rounded-2xl object-cover shadow-lg border border-zinc-100 dark:border-zinc-800">
                @else
                    <div class="size-20 rounded-2xl bg-emerald-600 flex items-center justify-center text-white text-3xl font-black shadow-lg">
                        {{ substr($workspace->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-emerald-500/20">Parceiro Oficial</span>
                        <h2 class="text-xs font-black text-zinc-400 uppercase tracking-widest">{{ $workspace->name }}</h2>
                    </div>
                    <h1 class="text-4xl font-black dark:text-white tracking-tighter italic leading-none">Área Exclusiva: {{ $client->name }}</h1>
                </div>
            </div>

            <a href="/" class="px-6 py-3 bg-zinc-900 dark:bg-zinc-800 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all active:scale-95 shadow-xl shadow-black/10 z-10">
                Sair do Portal
            </a>
        </div>

        {{-- 2. ALERTA DE PROPOSTAS --}}
        @if($proposals->count() > 0)
            <div class="bg-indigo-600 rounded-[2rem] p-6 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl shadow-indigo-500/20 animate-in fade-in slide-in-from-top-4 duration-700 text-left">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/10 rounded-2xl"><flux:icon name="document-text" class="size-6 text-white" /></div>
                    <div>
                        <p class="font-black uppercase text-sm tracking-tight text-white">Nova Proposta para Revisão</p>
                        <p class="text-xs text-indigo-100 opacity-80 font-medium">Analisámos os teus requisitos e enviámos uma nova proposta. Analisa e aprova online.</p>
                    </div>
                </div>
                <button class="px-8 py-3 bg-white text-indigo-600 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-50 transition-all shadow-lg shrink-0">Ver Proposta</button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- COLUNA ESQUERDA: OPERACIONAL (8 Colunas) --}}
            <div class="lg:col-span-8 space-y-12">

                {{-- PROJETOS --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 text-left">
                        <flux:icon name="briefcase" class="size-5 text-zinc-400" />
                        <h3 class="font-black dark:text-white uppercase text-sm tracking-widest italic">Planos de Trabalho Ativos</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                        @forelse($projects as $project)
                            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-emerald-500/30 transition-all group">
                                <div class="flex justify-between items-start mb-8">
                                    <div>
                                        <h4 class="font-black text-2xl dark:text-white uppercase tracking-tight group-hover:text-emerald-500 transition-colors leading-tight">{{ $project->name }}</h4>
                                        <p class="text-[10px] text-zinc-400 font-bold uppercase mt-1 italic">{{ $project->tasks_count }} tarefas por concluir</p>
                                    </div>
                                    <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase rounded-lg">{{ $project->status }}</span>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex justify-between text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                                        <span>Execução</span>
                                        <span class="text-emerald-600">{{ $project->progress ?? 0 }}%</span>
                                    </div>
                                    <div class="h-3 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden p-0.5 border border-zinc-200 dark:border-zinc-700">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 shadow-[0_0_12px_rgba(16,185,129,0.4)]" style="width: {{ $project->progress ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-16 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[3rem]">
                                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest italic opacity-50 text-left">Sem projetos em curso.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- DIÁRIO DE PRODUÇÃO (TIMELINE) --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3 px-2 text-left">
                        <flux:icon name="bolt" class="size-5 text-emerald-500" />
                        <h3 class="font-black dark:text-white uppercase text-sm tracking-widest italic">Diário de Produção</h3>
                    </div>

                    <div class="relative pl-8 space-y-8 before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-zinc-200 dark:before:bg-zinc-800">
                        @forelse($recentActivity as $act)
                            <div class="relative text-left">
                                <div class="absolute -left-[27px] top-1 size-3.5 rounded-full bg-emerald-500 border-4 border-zinc-50 dark:border-zinc-950 shadow-sm"></div>
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $act->title }}</p>
                                        <p class="text-[10px] text-zinc-400 font-bold uppercase italic">Concluído {{ $act->completed_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="text-[9px] font-black text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-3 py-1.5 rounded uppercase w-fit shadow-sm">{{ $act->project->name }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-zinc-400 italic text-left pl-2">Updates automáticos aparecerão assim que as tarefas forem concluídas.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- COLUNA DIREITA: ADMIN & MENSAGENS (4 Colunas) --}}
            <div class="lg:col-span-4 space-y-12 text-left">

                {{-- CENTRO DE MENSAGENS (COM HISTÓRICO) --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h3 class="font-black dark:text-white uppercase text-[11px] tracking-widest flex items-center gap-2">
                            <flux:icon name="chat-bubble-left-right" class="size-4" /> Centro de Mensagens
                        </h3>
                        <flux:modal.trigger name="support-modal">
                            <button class="text-[9px] font-black text-emerald-600 uppercase hover:underline">+ Novo Pedido</button>
                        </flux:modal.trigger>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse($tickets as $ticket)
                                <button wire:click="setActiveTicket({{ $ticket->id }})" class="w-full p-6 flex flex-col gap-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all text-left group">
                                    <div class="flex justify-between items-start">
                                        <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border border-zinc-200 dark:border-zinc-700">
                                            {{ $ticket->status }}
                                        </span>
                                        <span class="text-[9px] text-zinc-400 font-bold uppercase">{{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs font-black dark:text-white uppercase tracking-tight group-hover:text-emerald-600 transition-colors">
                                        {{ str_replace('[PORTAL] ', '', $ticket->subject) }}
                                    </p>
                                    <p class="text-[10px] text-zinc-400 truncate">{{ $ticket->messages->last()->message ?? 'Sem mensagens' }}</p>
                                </button>
                            @empty
                                <div class="p-10 text-center text-zinc-400 text-[10px] font-black uppercase tracking-widest italic opacity-50">Sem conversas ativas.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- FATURAS --}}
                <div class="space-y-4">
                    <h3 class="font-black dark:text-white uppercase text-[11px] tracking-widest px-2 flex items-center gap-2">
                        <flux:icon name="folder" class="size-4" /> Arquivo de Faturação
                    </h3>
                    <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse($invoices as $invoice)
                                <div class="p-6 flex justify-between items-center hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all text-left">
                                    <div>
                                        <p class="text-xs font-black dark:text-white uppercase">#{{ $invoice->number }}</p>
                                        <p class="text-[10px] text-zinc-400 font-bold mt-1">{{ $invoice->created_at->format('d M, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-black text-lg dark:text-white tracking-tighter">{{ number_format($invoice->total, 2, ',', ' ') }}€</p>
                                        <button class="text-[9px] text-emerald-600 font-black uppercase hover:underline flex items-center gap-1 ml-auto">
                                            <flux:icon name="arrow-down-tray" variant="micro" class="size-3" /> PDF
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 text-center text-zinc-400 text-[10px] font-black uppercase tracking-widest italic opacity-50">Sem faturas emitidas.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- WIDGET CEO --}}
                <div class="p-8 bg-zinc-950 border border-zinc-800 rounded-[2.5rem] text-left relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 size-40 bg-emerald-500/10 blur-3xl rounded-full group-hover:bg-emerald-500/20 transition-all duration-700"></div>
                    <flux:icon name="chat-bubble-left-right" variant="solid" class="size-12 mb-6 text-white" />
                    <h4 class="font-black text-white uppercase tracking-tight text-xl leading-none">CEO Acesso Direto</h4>
                    <p class="text-xs text-zinc-400 mt-4 leading-relaxed font-medium">Precisas de assistência técnica ou comercial? Abre um ticket e fala diretamente com a nossa administração.</p>
                    <div class="mt-8">
                        <flux:modal.trigger name="support-modal">
                            <button class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                                Contactar Gestor
                            </button>
                        </flux:modal.trigger>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL 1: CHAT DE HISTÓRICO --}}
    <flux:modal name="view-ticket-modal" class="md:w-[600px] !p-0 rounded-[2.5rem] overflow-hidden">
        <div class="flex flex-col h-[600px] bg-white dark:bg-zinc-950">
            <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-900/50">
                <div class="text-left leading-tight">
                    <h2 class="text-sm font-black dark:text-white uppercase tracking-widest">Conversa com Administração</h2>
                    <p class="text-[9px] text-zinc-500 font-bold uppercase mt-1">Canal Seguro & Criptografado</p>
                </div>
                <flux:modal.close><flux:button variant="ghost" icon="x-mark" size="sm" /></flux:modal.close>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-zinc-50/20 dark:bg-transparent">
                @foreach($activeMessages as $msg)
                    <div class="flex {{ $msg->is_from_client ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] {{ $msg->is_from_client ? 'bg-emerald-600 text-white rounded-t-2xl rounded-l-2xl shadow-emerald-500/10' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white rounded-t-2xl rounded-r-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm' }} p-4">
                            <p class="text-sm font-medium leading-relaxed text-left">{{ $msg->message }}</p>
                            <p class="text-[8px] mt-2 font-black uppercase opacity-60 {{ $msg->is_from_client ? 'text-right' : 'text-left' }}">
                                {{ $msg->created_at->format('H:i') }} · {{ $msg->is_from_client ? 'Tu' : 'Empresa Parceira' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <form wire:submit.prevent="sendReply" class="flex gap-3">
                    <input wire:model="replyMessage" type="text" placeholder="Escreve a tua resposta..." class="flex-1 bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl px-4 text-sm focus:ring-2 focus:ring-emerald-500/20 dark:text-white h-12" />
                    <flux:button type="submit" variant="primary" class="!bg-emerald-600 h-12 rounded-xl font-black uppercase text-[10px] px-8">Enviar</flux:button>
                </form>
            </div>
        </div>
    </flux:modal>

    {{-- MODAL 2: NOVO TICKET --}}
    <flux:modal name="support-modal" class="md:w-[500px] !p-0 rounded-[2.5rem] overflow-hidden">
        <div class="p-10 space-y-8 bg-white dark:bg-zinc-950 text-left">
            <div>
                <h2 class="text-2xl font-black dark:text-white uppercase italic tracking-tighter leading-none">Inicia uma Conversa</h2>
                <p class="text-xs text-zinc-400 font-medium italic mt-2">Diz-nos o que precisas e a administração da {{ $workspace->name }} responderá em breve.</p>
            </div>

            <form wire:submit.prevent="sendTicket" class="space-y-6">
                <div class="space-y-4">
                    <flux:input wire:model="subject" label="Assunto" placeholder="Ex: Dúvida sobre planeamento ou fatura..." class="font-bold" />
                    <flux:textarea wire:model="message" label="Mensagem Detalhada" placeholder="Explica aqui o teu pedido..." rows="6" class="rounded-xl" />
                </div>
                <div class="pt-2 flex gap-4">
                    <flux:modal.close class="flex-1"><flux:button variant="ghost" class="w-full font-black uppercase text-[10px] text-zinc-400">Abortar</flux:button></flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-[2] !bg-emerald-600 font-black h-12 rounded-xl uppercase text-[10px] tracking-widest shadow-lg shadow-emerald-500/20">Iniciar Chat</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

</div>
