<div
    x-data="{ chatOpen: false, newTicketOpen: false }"
    x-on:open-chat-modal.window="chatOpen = true"
    x-on:close-chat-modal.window="chatOpen = false"
    x-on:ticket-created.window="newTicketOpen = false"
    x-on:keydown.escape.window="chatOpen = false; newTicketOpen = false;"
    class="space-y-10 pb-24"
>

    {{-- 1. HEADER DINÂMICO --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2 w-full">

            <div class="flex items-center gap-6 flex-wrap md:flex-nowrap w-full md:w-auto">
                <div class="relative group shrink-0">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full transition-all duration-700 shadow-brand-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="{{ $isBusinessMode ? 'building-office-2' : 'question-mark-circle' }}"
                                   class="w-10 h-10 text-brand-600" />
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-3xl sm:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                            {{ $isBusinessMode ? 'Suporte Técnico LASO' : 'Centro de Apoio Pessoal' }}
                        </h1>

                        <flux:badge variant="neutral"
                            class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">
                            {{ $isBusinessMode ? 'Empresa Ativa' : 'Service Desk' }}
                        </flux:badge>
                    </div>

                    <p class="text-sm text-zinc-500 font-medium italic mt-2 leading-relaxed">
                        Linha direta para <span class="text-brand-600 font-bold uppercase tracking-tighter">Resolução de Problemas e Consultoria</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem]
                        border border-zinc-200 dark:border-zinc-800 shadow-sm w-full md:w-auto justify-between md:justify-start">

                <flux:button type="button" @click="newTicketOpen = true" variant="primary" icon="plus"
                    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg bg-brand-600 shadow-brand-500/20 border-none text-white whitespace-nowrap">
                    Novo Ticket
                </flux:button>

                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

                <flux:button href="{{ $isBusinessMode ? route('hub.business.dashboard') : route('dashboard') }}"
                             variant="ghost" icon="arrow-left" wire:navigate title="Voltar"
                             class="rounded-xl whitespace-nowrap" />
            </div>

        </header>
    </div>

    {{-- 2. KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

        <div class="glass-card p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Total de Pedidos</p>
            <h3 class="text-3xl font-black dark:text-white tracking-tighter italic">{{ $myTickets->count() }}</h3>
            <p class="mt-2 text-[9px] font-bold text-zinc-500 uppercase tracking-widest italic">Histórico no Contexto</p>
        </div>

        <div class="glass-card p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm">
            <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">A Aguardar Resposta</p>
            <h3 class="text-3xl font-black text-amber-600 tracking-tighter italic">
                {{ $myTickets->where('status', 'open')->count() }}
            </h3>
            <div class="mt-2 h-1 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 animate-pulse w-full"></div>
            </div>
        </div>

        <div class="glass-card p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm">
            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-1">Casos Resolvidos</p>
            <h3 class="text-3xl font-black text-emerald-600 tracking-tighter italic">
                {{ $myTickets->where('status', 'closed')->count() }}
            </h3>
            <p class="mt-2 text-[9px] font-bold text-emerald-500 uppercase tracking-widest italic">Eficiência de Gestão</p>
        </div>

    </div>

    {{-- 3. LISTA DE TICKETS --}}
    <div class="space-y-4">
        <div class="flex items-center gap-3 px-2">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="queue-list" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                {{ $isBusinessMode ? 'Histórico Técnico LASO' : 'Pedidos Suporte Pessoal' }}
            </h2>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @forelse($myTickets as $ticket)
                @php
                    $isOpen = $ticket->status === 'open';
                    $isHigh = $ticket->priority === 'high';
                @endphp

                <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] overflow-hidden transition-all duration-300 hover:border-brand-500/30 group shadow-sm">

                    <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-6">

                                                {{-- IDENTIFICAÇÃO E ASSUNTO --}}
                        <div class="flex items-center gap-5 flex-1 w-full">
                            <div class="relative shrink-0">
                                <div class="size-12 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 flex items-center justify-center">
                                    <flux:icon name="ticket" variant="outline" class="size-6 {{ $isHigh ? 'text-red-500' : 'text-zinc-400' }}" />
                                </div>

                                @if($isOpen)
                                    <div class="absolute -top-1 -right-1 size-3 bg-brand-500 rounded-full border-2 border-white dark:border-zinc-900 animate-pulse"></div>
                                @endif
                            </div>

                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h4 class="font-black dark:text-white uppercase text-sm tracking-tight group-hover:text-brand-600 transition-colors truncate">
                                        {{ $ticket->subject }}
                                    </h4>

                                    <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-[0.2em] rounded-md border border-zinc-200 dark:border-zinc-700">
                                        ID #{{ $ticket->id }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $isHigh ? 'text-red-500' : 'text-zinc-400' }}">
                                        {{ $isHigh ? 'Prioridade Crítica' : 'Prioridade Normal' }}
                                    </span>

                                    <span class="text-zinc-200 dark:text-zinc-800 text-xs">|</span>

                                    <span class="text-[10px] text-zinc-500 font-bold uppercase italic">
                                        {{ $ticket->messages_count }} interações
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ESTADO E AÇÕES --}}
                        <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end border-t md:border-none border-zinc-50 dark:border-zinc-800 pt-4 md:pt-0">

                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border transition-all
                                {{ !$isOpen
                                    ? 'bg-zinc-50 text-zinc-400 border-zinc-100 dark:bg-zinc-800 dark:border-zinc-700'
                                    : 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20' }}">

                                <div class="size-1.5 rounded-full {{ !$isOpen ? 'bg-zinc-300' : 'bg-emerald-500 animate-pulse' }}"></div>
                                {{ !$isOpen ? 'Resolvido' : 'Em Análise' }}
                            </span>

                            <div class="flex items-center gap-2">

                                <flux:button wire:click="viewConversation({{ $ticket->id }})" variant="filled"
                                    class="bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-black uppercase text-[10px] px-6 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 transition-all shadow-sm">
                                    Entrar no Chat
                                </flux:button>

                                @if($isOpen)
                                    <flux:button wire:click="closeTicket({{ $ticket->id }})"
                                        wire:confirm="Confirmas que o problema foi resolvido?"
                                        variant="ghost" icon="check-circle" color="emerald"
                                        class="rounded-xl" title="Fechar Caso" />
                                @endif

                            </div>
                        </div>

                    </div>
                </div>

            @empty
                <div class="py-24 text-center glass-card rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20">
                    <flux:icon name="sparkles" class="size-12 text-zinc-300 mx-auto mb-4" />
                    <p class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px]">Tudo em ordem</p>
                    <p class="text-zinc-400 text-xs italic mt-1 font-medium">Não foram encontrados pedidos registados neste contexto.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 4. MODAL DE CONVERSA --}}
    <div x-show="chatOpen" x-cloak
        x-transition:enter="transition-opacity ease-out duration-75"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-75"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="chatOpen = false"
        class="fixed inset-0 z-50 bg-black/40">
    </div>

    <div x-show="chatOpen" x-cloak @click.self="chatOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4">

        <div @click.stop x-show="chatOpen"
            x-transition:enter="transition ease-out duration-100 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full md:w-[600px] max-h-[90vh] flex flex-col">

            @if($activeTicket)
                <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 flex flex-col h-[700px] shadow-2xl border border-zinc-200 dark:border-zinc-800">

                                        {{-- Cabeçalho da Conversa --}}
                    <div class="border-b dark:border-zinc-800 pb-6 flex items-center gap-4">
                        <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                            <flux:icon name="chat-bubble-left-right" class="size-6" />
                        </div>

                        <div class="min-w-0">
                            <flux:heading size="xl"
                                class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none truncate">
                                {{ $activeTicket->subject }}
                            </flux:heading>

                            <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest mt-2 flex items-center gap-2">
                                <span class="text-brand-500">Protocolo #{{ $activeTicket->id }}</span>
                                <span class="text-zinc-300 dark:text-zinc-700">|</span>
                                <span>{{ $activeTicket->status === 'closed' ? 'Caso Encerrado' : 'Canal Aberto' }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Área de Mensagens --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-6 custom-scrollbar rounded-2xl bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-100 dark:border-zinc-800/50 shadow-inner">

                        @foreach($activeTicket->messages as $msg)
                            <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }} animate-fade-in">

                                <div class="max-w-[85%] flex flex-col {{ $msg->user_id === auth()->id() ? 'items-end' : 'items-start' }}">

                                    <div class="p-4 rounded-[1.4rem] text-sm shadow-sm transition-all
                                        {{ $msg->is_admin_reply
                                            ? 'bg-zinc-900 text-brand-400 dark:bg-white dark:text-zinc-950 rounded-tl-none font-bold'
                                            : 'bg-brand-600 text-white rounded-tr-none shadow-brand-600/10 font-medium' }}">
                                        {{ $msg->message }}
                                    </div>

                                    <span class="text-[8px] mt-2 font-black uppercase text-zinc-400 tracking-widest italic">
                                        {{ $msg->is_admin_reply ? 'Equipa de Suporte' : 'Tu' }} · {{ $msg->created_at->format('H:i') }}
                                    </span>

                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- Campo de Resposta --}}
                    @if($activeTicket->status !== 'closed')
                        <div class="pt-4 border-t dark:border-zinc-800">
                            <form wire:submit.prevent="sendReply" class="relative group">

                                <div class="flex items-center gap-3 p-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-inner focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">

                                    <flux:input
                                        wire:model="replyMessage"
                                        placeholder="Escrever mensagem..."
                                        class="flex-1 !bg-transparent border-none shadow-none font-medium h-12"
                                    />

                                    <flux:button type="submit" variant="primary"
                                        class="rounded-xl shadow-lg h-10 w-12 flex items-center justify-center bg-brand-600 border-none">
                                        <flux:icon name="paper-airplane" variant="solid" class="size-4" />
                                    </flux:button>

                                </div>

                            </form>
                        </div>
                    @else
                        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 text-center">
                            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest italic">
                                Este ticket foi resolvido e o canal de chat arquivado.
                            </p>
                        </div>
                    @endif

                </div>
            @endif

        </div>
    </div>

    {{-- 5. MODAL: NOVO TICKET --}}
    <div x-show="newTicketOpen" x-cloak
        x-transition:enter="transition-opacity ease-out duration-75"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-75"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="newTicketOpen = false"
        class="fixed inset-0 z-50 bg-black/40">
    </div>

    <div x-show="newTicketOpen" x-cloak @click.self="newTicketOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4">

        <div @click.stop x-show="newTicketOpen"
            x-transition:enter="transition ease-out duration-100 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full md:w-[500px] max-h-[90vh] overflow-y-auto custom-scrollbar">

            <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">

                <div class="text-center space-y-2">
                    <div class="inline-flex p-3 bg-brand-500/10 rounded-2xl mb-2 text-brand-600">
                        <flux:icon name="plus" class="size-6" />
                    </div>

                    <flux:heading size="xl"
                        class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $isBusinessMode ? 'Ticket de Suporte Técnico' : 'Abrir Pedido Pessoal' }}
                    </flux:heading>

                    <p class="text-xs text-zinc-400 font-medium italic px-4">
                        {{ $isBusinessMode
                            ? 'O seu pedido será associado à conta corporativa da ' . auth()->user()->currentWorkspace->name
                            : 'Explique o seu problema técnico para que possamos ajudar na sua conta pessoal.' }}
                    </p>
                </div>

                <form wire:submit="openTicket" class="space-y-6">

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                            Assunto do Pedido
                        </flux:label>

                        <flux:input wire:model="subject"
                            placeholder="Ex: Erro ao emitir fatura..."
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                            Nível de Urgência
                        </flux:label>

                        <flux:select wire:model="priority"
                            class="font-black uppercase text-xs !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner">
                            <option value="low">🟢 Baixa Prioridade (Dúvida)</option>
                            <option value="normal">🟡 Prioridade Normal (Melhoria)</option>
                            <option value="high">🔴 Alta Prioridade (Erro Crítico)</option>
                        </flux:select>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                            Descrição Detalhada
                        </flux:label>

                        <flux:textarea wire:model="message" rows="3"
                            placeholder="Forneça o máximo de detalhes possível..."
                            class="rounded-2xl shadow-inner border-none !bg-zinc-50 dark:!bg-zinc-900 text-sm p-4" />
                    </div>

                    <div class="flex gap-4 pt-4">
                        <flux:button type="button" @click="newTicketOpen = false" variant="ghost"
                            class="flex-1 font-black uppercase text-[10px] text-zinc-400">
                            Descartar
                        </flux:button>

                        <flux:button type="submit" variant="primary"
                            class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl bg-brand-600 border-none">
                            Enviar Pedido
                        </flux:button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- 6. FOOTER --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ auth()->user()->currentWorkspace->name ?? config('app.name') }}
            · Suporte Técnico {{ $isBusinessMode ? 'Enterprise' : 'Pessoal' }}
        </p>
    </footer>

    {{-- 7. ESTILOS --}}
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
        [x-cloak] { display: none !important; }
    </style>

</div>
