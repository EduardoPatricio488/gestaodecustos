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
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Monitorização de incidências e <span class="text-red-600 font-bold uppercase">Moderação Social</span></p>
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

    {{-- 2. KPIs DE ATENDIMENTO (3 COLUNAS) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- KPI 1: Crítico / Urgente --}}
        <div class="stat-card bg-zinc-950 text-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 group">
            <div class="absolute -right-10 -top-10 size-40 bg-red-500/10 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-red-500 mb-2">Crítico / Urgente</p>
                <h3 class="text-5xl font-black tracking-tighter italic text-white">
                    {{ \App\Models\SupportTicket::where('priority', 'high')->where('status', 'open')->count() }}
                </h3>
                <p class="mt-4 text-[9px] font-bold text-zinc-500 uppercase tracking-widest italic">Tickets prioritários</p>
            </div>
            <flux:icon name="fire" class="absolute -right-4 -bottom-4 size-24 text-white/5 -rotate-12" />
        </div>

        {{-- KPI 2: NOVO - DENÚNCIAS SOCIAIS --}}
        <div class="stat-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] shadow-sm relative overflow-hidden border border-zinc-200 dark:border-zinc-800 group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500 mb-2">Denúncias Social</p>
                <h3 class="text-5xl font-black tracking-tighter italic dark:text-white">
                    {{ \App\Models\SocialReport::where('status', 'pending')->count() }}
                </h3>
                <p class="mt-4 text-[9px] font-bold text-zinc-400 uppercase tracking-widest italic">Aguardando moderação</p>
            </div>
            <flux:icon name="megaphone" class="absolute -right-4 -bottom-4 size-24 text-zinc-100 dark:text-zinc-800/30 -rotate-12" />
        </div>

        {{-- KPI 3: Tickets Resolvidos --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group relative overflow-hidden">
            <div class="absolute -right-10 -top-10 size-40 bg-emerald-500/5 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Total Resolvido</p>
                <h3 class="text-5xl font-black text-emerald-600 dark:text-emerald-500 tracking-tighter italic">
                    {{ \App\Models\SupportTicket::where('status', 'closed')->count() }}
                </h3>
                <p class="mt-4 text-[9px] font-black text-emerald-600 uppercase italic">Performance Global</p>
            </div>
            <flux:icon name="check-badge" class="absolute -right-4 -bottom-4 size-20 text-zinc-100 dark:text-zinc-800/30 -rotate-12" />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 h-[800px]">

        {{-- 3. FILA DE ATENDIMENTO (COLUNA ESQUERDA) --}}
        <div class="lg:col-span-4 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm">

            {{-- SELETOR DE MODO (TICKETS VS DENÚNCIAS) --}}
            <div class="p-4 bg-zinc-50 dark:bg-zinc-950/20 border-b dark:border-zinc-800 flex gap-2">
                <button wire:click="$set('activeTab', 'tickets')"
                    class="flex-1 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2
                    {{ $activeTab === 'tickets' ? 'bg-zinc-900 text-white shadow-lg' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                    <flux:icon name="chat-bubble-left-right" class="size-3" /> Tickets
                </button>
                <button wire:click="$set('activeTab', 'denuncias')"
                    class="flex-1 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2
                    {{ $activeTab === 'denuncias' ? 'bg-red-600 text-white shadow-lg' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                    <flux:icon name="megaphone" class="size-3" /> Denúncias
                </button>
            </div>

            <div class="p-5 border-b dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Procurar..." icon="magnifying-glass" class="!bg-white dark:!bg-zinc-900 border-none shadow-sm rounded-xl" />
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-zinc-100 dark:divide-zinc-800">
                @if($activeTab === 'tickets')
                    @forelse($tickets as $ticket)
                        <button wire:click="selectTicket({{ $ticket->id }})"
                            class="w-full text-left p-6 transition-all hover:bg-zinc-50 dark:hover:bg-brand-500/5 relative group
                            {{ $activeTicketId == $ticket->id ? 'bg-brand-50/50 dark:bg-brand-500/10' : '' }}">
                            @if($activeTicketId == $ticket->id) <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-brand-500 rounded-r-full"></div> @endif
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 {{ $ticket->workspace_id ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600' }} text-[8px] font-black uppercase rounded border shadow-sm">
                                        {{ $ticket->workspace_id ? '🏢 EMPRESARIAL' : '👤 PESSOAL' }}
                                    </span>
                                    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">#{{ $ticket->id }}</span>
                                </div>
                                <flux:badge :variant="$ticket->priority === 'high' ? 'danger' : 'neutral'" size="sm" class="text-[8px] font-black uppercase border-none">{{ $ticket->priority }}</flux:badge>
                            </div>
                            <h4 class="text-sm font-black text-zinc-900 dark:text-white leading-tight mb-3 truncate">{{ $ticket->subject }}</h4>
                            <div class="flex items-center gap-3">
                                <div class="size-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[9px] font-black uppercase text-zinc-500 border dark:border-zinc-700 shadow-inner">{{ substr($ticket->user->name, 0, 1) }}</div>
                                <span class="text-[10px] font-black dark:text-zinc-300 leading-none">{{ explode(' ', $ticket->user->name)[0] }}</span>
                                <span class="text-[9px] text-zinc-400 ml-auto font-medium italic">{{ $ticket->updated_at->diffForHumans(null, true) }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="p-20 text-center text-zinc-400 italic text-xs font-black opacity-30">Sem tickets</div>
                    @endforelse
                @else
                    {{-- LISTA DE DENÚNCIAS --}}
                    @forelse($reports as $report)
                        <button wire:click="selectReport({{ $report->id }})"
                            class="w-full text-left p-6 transition-all hover:bg-red-50/50 dark:hover:bg-red-500/5 relative group
                            {{ $selectedReportId == $report->id ? 'bg-red-50 dark:bg-red-500/10' : '' }}">
                            @if($selectedReportId == $report->id) <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-red-600 rounded-r-full"></div> @endif
                            <div class="flex justify-between items-start mb-3">
                                <span class="px-2 py-0.5 bg-red-500/10 text-red-600 text-[8px] font-black uppercase rounded border border-red-500/20">🚨 DENÚNCIA SOCIAL</span>
                                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">#{{ $report->id }}</span>
                            </div>
                            <h4 class="text-sm font-black text-zinc-900 dark:text-white leading-tight mb-2 truncate">Post de {{ $report->social_post->user->name }}</h4>
                            <p class="text-[10px] text-zinc-500 italic line-clamp-1">"{{ $report->reason }}"</p>
                        </button>
                    @empty
                        <div class="p-20 text-center text-zinc-400 italic text-xs font-black opacity-30">Sem denúncias</div>
                    @endforelse
                @endif
            </div>
        </div>

        {{-- 4. JANELA DE ACÇÃO (LG:COL-SPAN-8) --}}
        <div class="lg:col-span-8 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-xl overflow-hidden relative">

            {{-- MODO TICKETS (TEU CÓDIGO ORIGINAL) --}}
            @if($activeTab === 'tickets' && $this->activeTicket)
                <header class="p-8 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20 backdrop-blur-md">
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <h2 class="text-xl font-black dark:text-white uppercase italic leading-none tracking-tighter">{{ $this->activeTicket->subject }}</h2>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3">
                                <p class="text-xs text-zinc-500 font-medium flex items-center gap-2"><flux:icon name="user" variant="micro" class="size-3" />{{ $this->activeTicket->user->name }}</p>
                                <span class="text-zinc-300 dark:text-zinc-800">|</span>
                                <p class="text-xs text-brand-600 font-bold uppercase tracking-widest text-[10px]">{{ $this->activeTicket->workspace?->name ?? 'Suporte Pessoal' }}</p>
                                <span class="text-zinc-300 dark:text-zinc-800">|</span>
                                <p class="text-xs text-zinc-400 font-medium italic truncate max-w-[150px]">{{ $this->activeTicket->user->email }}</p>
                            </div>
                        </div>
                        @if($this->activeTicket->status !== 'closed')
                            <flux:button wire:click="closeTicket({{ $this->activeTicket->id }})" variant="ghost" size="sm" icon="check-circle" color="emerald" class="rounded-xl font-black uppercase text-[9px] tracking-widest">Resolver Caso</flux:button>
                        @else
                            <flux:badge variant="neutral" class="uppercase font-black text-[9px] px-3 border-none bg-zinc-100 dark:bg-zinc-800">Arquivado</flux:badge>
                        @endif
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto p-8 space-y-8 bg-zinc-50/20 dark:bg-zinc-950/10 custom-scrollbar" style="background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.03) 1px, transparent 0); background-size: 24px 24px;">
                    @foreach($this->activeTicket->messages as $msg)
                        <div class="flex {{ $msg->is_admin_reply ? 'justify-end' : 'justify-start' }} animate-fade-in">
                            <div class="max-w-[80%] flex flex-col {{ $msg->is_admin_reply ? 'items-end' : 'items-start' }}">
                                <div class="p-5 rounded-[1.6rem] text-sm leading-relaxed shadow-sm transition-all
                                    {{ $msg->is_admin_reply
                                        ? 'bg-zinc-900 text-brand-400 dark:bg-white dark:text-zinc-950 rounded-tr-none border border-zinc-800 font-bold'
                                        : 'bg-white dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-700 rounded-tl-none font-medium' }}">
                                    {{ $msg->message }}
                                </div>
                                <span class="text-[8px] mt-2 font-black uppercase text-zinc-400 tracking-widest italic">{{ $msg->is_admin_reply ? 'Tu (Suporte Oficial)' : $this->activeTicket->user->name }} · {{ $msg->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($this->activeTicket->status !== 'closed')
                    <footer class="p-8 bg-white dark:bg-zinc-900 border-t dark:border-zinc-800">
                        <form wire:submit.prevent="sendReply" class="space-y-4">
                            <div class="relative group">
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-inner focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                                    <textarea wire:model="replyMessage" rows="3" placeholder="Instruções para o utilizador..." class="w-full p-4 bg-transparent border-none focus:ring-0 text-sm dark:text-white resize-none no-scrollbar placeholder:text-zinc-400 font-medium"></textarea>
                                    <div class="flex justify-end p-2"><flux:button type="submit" variant="primary" icon="paper-airplane" class="rounded-2xl px-10 h-12 font-black uppercase tracking-widest shadow-xl shadow-brand-500/20">Transmitir Resposta</flux:button></div>
                                </div>
                            </div>
                        </form>
                    </footer>
                @endif

            {{-- MODO DENÚNCIAS (NOVO) --}}
            @elseif($activeTab === 'denuncias' && $this->selectedReport)
                <header class="p-8 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20">
                    <h2 class="text-xl font-black text-red-600 uppercase italic tracking-tighter">Moderação de Conteúdo</h2>
                </header>
                <div class="flex-1 overflow-y-auto p-8 space-y-8 bg-zinc-50/20 custom-scrollbar">
                    {{-- Preview do Post --}}
                    <div class="max-w-md mx-auto bg-white dark:bg-zinc-800 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-700 shadow-2xl overflow-hidden">
                        <div class="p-6 border-b dark:border-zinc-700 flex items-center gap-3">
                            <flux:avatar src="{{ $this->selectedReport->social_post->user->avatarUrl() }}" class="size-10" />
                            <span class="font-black text-sm text-zinc-900 dark:text-white">{{ $this->selectedReport->social_post->user->name }}</span>
                        </div>
                        <div class="p-8 text-sm text-zinc-800 dark:text-zinc-200">{{ $this->selectedReport->social_post->content }}</div>
                        @if($this->selectedReport->social_post->media_path)
                            <div class="px-8 pb-8"><img src="{{ Storage::url($this->selectedReport->social_post->media_path) }}" class="w-full h-64 object-cover rounded-2xl"></div>
                        @endif
                    </div>
                    {{-- Motivo da Denúncia --}}
                    <div class="max-w-md mx-auto p-6 bg-red-500/5 border border-red-500/20 rounded-3xl">
                        <h5 class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-2">Relatório da Denúncia:</h5>
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">"{{ $this->selectedReport->reason }}"</p>
                        <p class="text-[8px] font-black text-zinc-400 mt-4 uppercase">Reportado por: {{ $this->selectedReport->user->name }}</p>
                    </div>
                </div>
                <footer class="p-8 bg-white dark:bg-zinc-900 border-t flex gap-4">
                    <flux:button wire:click="ignoreReport({{ $this->selectedReport->id }})" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px]">Ignorar</flux:button>
                    <flux:button wire:click="deletePost({{ $this->selectedReport->id }})" variant="danger" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px]">Apagar Post</flux:button>
                </footer>

            @else
                {{-- ESTADO VAZIO --}}
                <div class="flex-1 flex flex-col items-center justify-center text-center p-20 opacity-30">
                    <flux:icon name="chat-bubble-left-right" class="size-16 text-zinc-400 mb-6" />
                    <h3 class="text-xl font-black text-zinc-400 uppercase tracking-tighter italic">Gestor de Suporte</h3>
                </div>
            @endif
        </div>
    </div>

    {{-- ESTILOS TÉCNICOS --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.3s ease-out forwards; }
    </style>
</div>
