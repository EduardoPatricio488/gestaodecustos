<div class="space-y-8 pb-20">
    {{-- 1. HEADER DE CONTROLO NEURAL (ESTILO PREMIUM SaaS) --}}
    <div class="relative">
        {{-- Glow decorativo --}}
        <div class="absolute -top-10 left-0 size-72 bg-brand-500/10 blur-[120px] rounded-full pointer-events-none animate-pulse"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:scale-125 transition-all duration-1000"></div>
                    <div class="relative p-5 bg-zinc-900 border border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="chat-bubble-left-right" class="w-10 h-10 text-brand-400" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Gestor de Suporte</h1>
                        <flux:badge variant="neutral" class="bg-brand-500/10 text-brand-400 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Admin Console</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Monitorização e resolução de incidências em <span class="text-brand-600 font-bold uppercase">Tempo Real</span></p>
                </div>
            </div>

            {{-- FILTRO DE ESTADO ESTILIZADO --}}
            <div class="flex items-center gap-2 bg-white dark:bg-zinc-900 p-1.5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                @foreach(['open' => 'Abertos', 'pending' => 'Pendentes', 'closed' => 'Fechados'] as $val => $label)
                    <button wire:click="$set('statusFilter', '{{ $val }}')"
                        class="px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                        {{ $statusFilter === $val ? 'bg-zinc-900 dark:bg-zinc-800 text-white shadow-lg' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800/50' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </header>
    </div>

    {{-- 2. KPIs DE ATENDIMENTO (DASHBOARD ANALYTICS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6"> {{-- Mudado para 2 colunas --}}

        {{-- KPI 1: Crítico / Urgente (Ação Imediata) --}}
        <div class="stat-card bg-zinc-950 text-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 group">
            <div class="absolute -right-10 -top-10 size-40 bg-red-500/10 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-red-500 mb-2">Crítico / Urgente</p>
                {{-- Conta todos os tickets High e Open na BD --}}
                <h3 class="text-5xl font-black tracking-tighter italic text-white">
                    {{ \App\Models\SupportTicket::where('priority', 'high')->where('status', 'open')->count() }}
                </h3>
                <p class="mt-4 text-[9px] font-bold text-zinc-500 uppercase tracking-widest italic">Tickets prioritários aguardando</p>
            </div>
            <flux:icon name="fire" class="absolute -right-4 -bottom-4 size-24 text-white/5 -rotate-12" />
        </div>

        {{-- KPI 2: Tickets Resolvidos (Global) --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group relative overflow-hidden">
            <div class="absolute -right-10 -top-10 size-40 bg-emerald-500/5 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Total de Tickets Resolvidos</p>
                {{-- Esta contagem ignora os filtros de pesquisa ou estado da lista --}}
                <h3 class="text-5xl font-black text-emerald-600 dark:text-emerald-500 tracking-tighter italic">
                    {{ \App\Models\SupportTicket::where('status', 'closed')->count() }}
                </h3>
                <div class="flex items-center gap-2 mt-4">
                    <div class="size-1.5 rounded-full bg-emerald-500"></div>
                    <p class="text-[9px] font-black text-emerald-600 uppercase italic">Performance Vitalícia do Sistema</p>
                </div>
            </div>
            <flux:icon name="check-badge" class="absolute -right-4 -bottom-4 size-20 text-zinc-100 dark:text-zinc-800/30 -rotate-12" />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 h-[750px]">

        {{-- 3. FILA DE ATENDIMENTO (COLUNA ESQUERDA) --}}
        <div class="lg:col-span-4 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm">
            <div class="p-5 border-b dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Procurar user ou empresa..." icon="magnifying-glass" class="!bg-white dark:!bg-zinc-900 border-none shadow-sm rounded-xl" />
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($tickets as $ticket)
                    <button
                        wire:click="selectTicket({{ $ticket->id }})"
                        class="w-full text-left p-6 transition-all hover:bg-zinc-50 dark:hover:bg-brand-500/5 relative group
                        {{ $activeTicketId == $ticket->id ? 'bg-brand-50/50 dark:bg-brand-500/10' : '' }}"
                    >
                        @if($activeTicketId == $ticket->id)
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-brand-500 rounded-r-full"></div>
                        @endif

                        <div class="flex justify-between items-start mb-3">
                            {{-- BADGE DE CONTEXTO: EMPRESA VS PESSOAL --}}
                            <div class="flex items-center gap-2">
    {{-- Se o ticket tem um workspace_id, ele é um ticket de empresa --}}
    @if($ticket->workspace_id)
        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[8px] font-black uppercase rounded border border-emerald-500/20 shadow-sm">
            🏢 EMPRESARIAL
        </span>
    @else
        <span class="px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[8px] font-black uppercase rounded border border-blue-500/20 shadow-sm">
            👤 PESSOAL
        </span>
    @endif
    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">#{{ $ticket->id }}</span>
</div>
                            <flux:badge :variant="$ticket->priority === 'high' ? 'danger' : 'neutral'" size="sm" class="text-[8px] font-black uppercase border-none">{{ $ticket->priority }}</flux:badge>
                        </div>

                        <h4 class="text-sm font-black text-zinc-900 dark:text-white leading-tight mb-3 group-hover:text-brand-600 transition-colors truncate">
                            {{ $ticket->subject }}
                        </h4>

                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[9px] font-black uppercase text-zinc-500 border dark:border-zinc-700 shadow-inner">
                                {{ substr($ticket->user->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
    <span class="text-[10px] font-black dark:text-zinc-300 leading-none">{{ explode(' ', $ticket->user->name)[0] }}</span>
    <span class="text-[9px] font-bold text-zinc-400 uppercase mt-0.5 italic truncate max-w-[140px]">
        {{ $ticket->workspace?->name ?? 'Conta Pessoal' }}
    </span>
</div>
                            <span class="text-[9px] text-zinc-400 ml-auto font-medium italic">{{ $ticket->updated_at->diffForHumans(null, true) }}</span>
                        </div>
                    </button>
                @empty
                    <div class="p-20 text-center text-zinc-400 italic text-xs uppercase font-black tracking-widest opacity-30">Vazio</div>
                @endforelse
            </div>

            @if($tickets->hasPages())
                <div class="p-4 border-t dark:border-zinc-800 bg-zinc-50/50">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

        {{-- 4. JANELA DE RESPOSTA (ESTILO TERMINAL DE SUPORTE) --}}
        <div class="lg:col-span-8 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-xl overflow-hidden relative">
            @if($activeTicket)
                {{-- Cabeçalho do Chat Detalhado --}}
                <header class="p-8 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20 backdrop-blur-md">
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <h2 class="text-xl font-black dark:text-white uppercase italic leading-none tracking-tighter">{{ $activeTicket->subject }}</h2>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3">
                                <p class="text-xs text-zinc-500 font-medium flex items-center gap-2">
                                    <flux:icon name="user" variant="micro" class="size-3" />
                                    {{ $activeTicket->user->name }}
                                </p>
                                <span class="text-zinc-300 dark:text-zinc-800">|</span>
                               <p class="text-xs text-brand-600 font-bold uppercase tracking-widest text-[10px]">
    {{ $activeTicket->workspace?->name ?? 'Suporte Pessoal' }}
</p>
                                <span class="text-zinc-300 dark:text-zinc-800">|</span>
                                <p class="text-xs text-zinc-400 font-medium italic truncate max-w-[150px]">{{ $activeTicket->user->email }}</p>
                            </div>
                        </div>

                        @if($activeTicket->status !== 'closed')
                            <flux:button wire:click="closeTicket({{ $activeTicket->id }})" variant="ghost" size="sm" icon="check-circle" color="emerald" class="rounded-xl font-black uppercase text-[9px] tracking-widest">Resolver Caso</flux:button>
                        @else
                            <flux:badge variant="neutral" class="uppercase font-black text-[9px] px-3 border-none bg-zinc-100 dark:bg-zinc-800">Arquivado</flux:badge>
                        @endif
                    </div>
                </header>

                {{-- Corpo das Mensagens (Scroll com Grid Pattern) --}}
                <div class="flex-1 overflow-y-auto p-8 space-y-8 bg-zinc-50/20 dark:bg-zinc-950/10 custom-scrollbar"
                     style="background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.03) 1px, transparent 0); background-size: 24px 24px;">

                    @foreach($activeTicket->messages as $msg)
                        <div class="flex {{ $msg->is_admin_reply ? 'justify-end' : 'justify-start' }} animate-fade-in">
                            <div class="max-w-[80%] flex flex-col {{ $msg->is_admin_reply ? 'items-end' : 'items-start' }}">
                                <div class="p-5 rounded-[1.6rem] text-sm leading-relaxed shadow-sm transition-all
                                    {{ $msg->is_admin_reply
                                        ? 'bg-zinc-900 text-brand-400 dark:bg-white dark:text-zinc-950 rounded-tr-none border border-zinc-800 font-bold'
                                        : 'bg-white dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-700 rounded-tl-none font-medium' }}">
                                    {{ $msg->message }}
                                </div>
                                <span class="text-[8px] mt-2 font-black uppercase text-zinc-400 tracking-widest italic">
                                    {{ $msg->is_admin_reply ? 'Tu (Suporte Oficial)' : $activeTicket->user->name }} · {{ $msg->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Painel de Resposta --}}
                @if($activeTicket->status !== 'closed')
                    <footer class="p-8 bg-white dark:bg-zinc-900 border-t dark:border-zinc-800">
                        <form wire:submit.prevent="sendReply" class="space-y-4">
                            <div class="relative group">
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-inner focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                                    <textarea
                                        wire:model="replyMessage"
                                        rows="3"
                                        placeholder="Instruções para o utilizador..."
                                        class="w-full p-4 bg-transparent border-none focus:ring-0 text-sm dark:text-white resize-none no-scrollbar placeholder:text-zinc-400 font-medium"
                                    ></textarea>

                                    <div class="flex justify-end p-2">
                                        <flux:button type="submit" variant="primary" icon="paper-airplane" class="rounded-2xl px-10 h-12 font-black uppercase tracking-widest shadow-xl shadow-brand-500/20">
                                            Transmitir Resposta
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[8px] text-zinc-400 font-black uppercase tracking-[0.3em] text-center">Protocolo de comunicação segura via {{ config('app.name') }} IA</p>
                        </form>
                    </footer>
                @else
                    <div class="p-8 bg-zinc-100 dark:bg-zinc-800/50 text-center border-t dark:border-zinc-800">
                        <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest italic">Este protocolo foi resolvido e encerrado pelo administrador.</p>
                    </div>
                @endif
            @else
                {{-- ESTADO VAZIO --}}
                <div class="flex-1 flex flex-col items-center justify-center text-center p-20 opacity-30">
                    <div class="p-8 bg-zinc-100 dark:bg-zinc-800 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-700 mb-6">
                        <flux:icon name="chat-bubble-left-right" class="size-16 text-zinc-400" />
                    </div>
                    <h3 class="text-xl font-black text-zinc-400 uppercase tracking-tighter italic">Gestor de Suporte</h3>
                    <p class="text-sm text-zinc-500 max-w-xs mt-2 font-medium">Seleciona um protocolo na fila lateral para processar a incidência.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ESTILOS TÉCNICOS --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.3s ease-out forwards; }
    </style>
</div>
