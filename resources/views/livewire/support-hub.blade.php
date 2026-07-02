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

                <flux:button type="button" @click="$dispatch('open-ticket-modal')" variant="primary" icon="plus"
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












    {{-- 4. MODAL DE CONVERSA (SUPER FLUID LIQUID GLASS) --}}
<div x-show="chatOpen" x-cloak
    x-transition.opacity.duration.45ms
    @click="chatOpen = false"
    class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm will-change-transform will-change-opacity">
</div>

<div x-show="chatOpen" x-cloak @click.self="chatOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 transform-gpu">

    <div @click.stop x-show="chatOpen"
        x-transition:enter="transition transform-gpu ease-out duration-60"
        x-transition:enter-start="opacity-0 scale-[0.97] translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition transform-gpu ease-in duration-50"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-[0.97] translate-y-1"
        class="relative w-full max-w-[600px] h-[700px] flex flex-col
               rounded-[2rem] overflow-hidden
               bg-white/10 dark:bg-white/10
               backdrop-blur-lg border border-white/20
               shadow-[0_2px_8px_-1px_rgba(0,0,0,0.25)]
               will-change-transform will-change-opacity">

        @if($activeTicket)
            <div class="relative flex flex-col h-full p-10 space-y-8 transform-gpu">

                {{-- HEADER --}}
                <div class="border-b border-white/10 pb-6 flex items-center gap-4 transform-gpu">
                    <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-md shadow-brand-500/20">
                        <flux:icon name="chat-bubble-left-right" class="size-6" />
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-3xl font-black uppercase italic tracking-tight text-white truncate">
                            {{ $activeTicket->subject }}
                        </h2>

                        <p class="text-[10px] text-zinc-200 font-black uppercase tracking-widest mt-2 flex items-center gap-2">
                            <span class="text-brand-400">Protocolo #{{ $activeTicket->id }}</span>
                            <span class="text-zinc-500">|</span>
                            <span>{{ $activeTicket->status === 'closed' ? 'Caso Encerrado' : 'Canal Aberto' }}</span>
                        </p>
                    </div>
                </div>

                {{-- MENSAGENS --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-6 custom-scrollbar
                            rounded-2xl bg-white/5 border border-white/10 shadow-inner
                            transform-gpu will-change-transform">

                    @foreach($activeTicket->messages as $msg)
                        <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">

                            <div class="max-w-[85%] flex flex-col {{ $msg->user_id === auth()->id() ? 'items-end' : 'items-start' }} transform-gpu">

                                {{-- BUBBLE COM CONTRASTE --}}
                                <div class="p-4 rounded-[1.4rem] text-sm shadow-md transform-gpu will-change-transform
                                    {{ $msg->is_admin_reply
                                        ? 'bg-white/95 text-zinc-900 rounded-tl-none'
                                        : 'bg-brand-600 text-white rounded-tr-none shadow-brand-600/20' }}">
                                    {{ $msg->message }}
                                </div>

                                <span class="text-[8px] mt-2 font-black uppercase text-zinc-300 tracking-widest italic">
                                    {{ $msg->is_admin_reply ? 'Equipa de Suporte' : 'Tu' }} · {{ $msg->created_at->format('H:i') }}
                                </span>

                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- CAMPO DE RESPOSTA --}}
                @if($activeTicket->status !== 'closed')
                    <div class="pt-4 border-t border-white/10 transform-gpu">
                        <form wire:submit.prevent="sendReply" class="relative group">

                            <div class="flex items-center gap-3 p-2
                                        bg-white/10 border border-white/20
                                        rounded-2xl shadow-inner backdrop-blur-md
                                        focus-within:ring-2 focus-within:ring-brand-500/30
                                        transform-gpu will-change-transform transition-all">

                                <flux:input
                                    wire:model="replyMessage"
                                    placeholder="Escrever mensagem..."
                                    class="flex-1 !bg-transparent border-none shadow-none font-medium h-12 !text-white placeholder-white"
                                />

                                <flux:button type="submit" variant="primary"
                                    class="rounded-xl shadow-md h-10 w-12 flex items-center justify-center bg-brand-600 border-none">
                                    <flux:icon name="paper-airplane" variant="solid" class="size-4" />
                                </flux:button>

                            </div>

                        </form>
                    </div>
                @else
                    <div class="p-4 bg-white/10 rounded-2xl border border-white/20 text-center transform-gpu">
                        <p class="text-[10px] font-black uppercase text-zinc-200 tracking-widest italic">
                            Este ticket foi resolvido e o canal de chat arquivado.
                        </p>
                    </div>
                @endif

            </div>
        @endif

    </div>
</div>












    {{-- MODAL: NOVO TICKET (LIQUID GLASS PREMIUM) --}}
<div
    x-data="{ open: false }"
    x-on:open-ticket-modal.window="open = true"
    x-on:close-ticket-modal.window="open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
>

    {{-- BACKDROP --}}
    <div
        x-show="open"
        x-transition.opacity.duration.120ms
        @click="open = false"
        class="fixed inset-0 z-50 bg-zinc-950/80"
    ></div>

    {{-- WRAPPER --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="open = false"
    >

        {{-- PAINEL LIQUID GLASS --}}
        <div
            @click.stop
            x-transition.scale.duration.120ms
            class="relative w-full max-w-lg rounded-[2rem] overflow-hidden
                   backdrop-blur-xl border border-white/20
                   shadow-[0_6px_22px_-4px_rgba(0,0,0,0.45)]
                   bg-white/10 dark:bg-white/10 p-10 space-y-8"
        >

            {{-- HEADER --}}
            <div class="space-y-3 text-center">
                <div class="inline-flex p-3 rounded-2xl mx-auto bg-brand-500/20 text-brand-400">
                    <flux:icon name="plus" class="size-6" />
                </div>

                <h2 class="text-3xl font-black uppercase italic tracking-tight text-white">
                    {{ $isBusinessMode ? 'Ticket de Suporte Técnico' : 'Abrir Pedido Pessoal' }}
                </h2>

                <p class="text-[10px] text-zinc-200 font-semibold uppercase tracking-[0.25em]">
                    {{ $isBusinessMode
                        ? 'O seu pedido será associado à conta corporativa da ' . auth()->user()->currentWorkspace->name
                        : 'Explique o seu problema técnico para que possamos ajudar na sua conta pessoal.' }}
                </p>
            </div>

            {{-- FORM --}}
            <form wire:submit.prevent="openTicket" class="space-y-6">

                {{-- ASSUNTO --}}
                <div>
                    <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">
                        Assunto do Pedido
                    </flux:label>

                    <flux:input wire:model="subject"
                        placeholder="Ex: Erro ao emitir fatura..."
                        class="rounded-2xl bg-white/10 border-white/10 text-white font-black placeholder-zinc-400 h-14" />
                </div>

                {{-- PRIORIDADE --}}
                <div>
                    <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">
                        Nível de Urgência
                    </flux:label>

                    <flux:select wire:model="priority"
    class="rounded-2xl bg-white/10 border-white/10 !text-white font-black uppercase text-xs h-14
           placeholder-white focus:placeholder-white">
    <option value="low" class="text-black">🟢 Baixa Prioridade (Dúvida)</option>
    <option value="normal" class="text-black">🟡 Prioridade Normal (Melhoria)</option>
    <option value="high" class="text-black">🔴 Alta Prioridade (Erro Crítico)</option>
</flux:select>


                </div>

                {{-- DESCRIÇÃO --}}
                <div>
                    <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">
                        Descrição Detalhada
                    </flux:label>

                  <flux:textarea wire:model="message" rows="3"
    placeholder="Forneça o máximo de detalhes possível..."
    class="rounded-2xl bg-white/10 border-white/10 !text-white placeholder-white p-4" />


                </div>

                {{-- AÇÕES --}}
                <div class="flex gap-3 pt-4">

                    {{-- CANCELAR --}}
                    <button type="button" @click="open = false"
                        class="flex-1 px-4 py-3 text-[10px] font-black uppercase tracking-widest
                               text-zinc-300 hover:text-white transition-colors duration-100">
                        Descartar
                    </button>

                    {{-- ENVIAR --}}
                    <button type="submit"
                        class="flex-[2] h-12 rounded-2xl font-black uppercase tracking-widest
                               text-white transition-all duration-120 shadow-md
                               bg-brand-600 hover:bg-brand-500 shadow-brand-500/40">
                        Enviar Pedido
                    </button>

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
