<div class="flex h-[calc(100vh-10rem)] gap-6 pb-6" x-data="{
    scrollToBottom() {
        $nextTick(() => {
            const container = $refs.chatContainer;
            if (container) { container.scrollTop = container.scrollHeight; }
        });
    }
}" x-init="scrollToBottom()" x-on:message-sent.window="scrollToBottom()">

    {{-- 1. BARRA LATERAL DE CANAIS (ESTILO BLACK GLASS) --}}
    <aside class="w-72 flex flex-col bg-zinc-950 border border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-2xl relative group">
        {{-- Efeito de Glow IA ao fundo --}}
        <div class="absolute -top-24 -left-24 size-48 bg-brand-500/10 blur-[80px] rounded-full opacity-50"></div>

        <div class="p-8 border-b border-white/5 relative z-10">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-brand-500/20 rounded-xl">
                    <flux:icon name="hashtag" class="size-4 text-brand-400" />
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Canais de Equipa</h3>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-2 no-scrollbar relative z-10">
            {{-- Canal Geral --}}
            <button wire:click="selectChannel(null)"
                class="w-full flex items-center justify-between px-5 py-4 rounded-[1.5rem] transition-all duration-300 group/item
                {{ is_null($activeProjectId) ? 'bg-brand-600 text-white shadow-xl shadow-brand-600/20' : 'text-zinc-500 hover:bg-white/5' }}">
                <div class="flex items-center gap-3">
                    <flux:icon name="chat-bubble-left-right" variant="{{ is_null($activeProjectId) ? 'solid' : 'outline' }}" class="size-4" />
                    <span class="font-black uppercase text-[11px] tracking-widest">Mural Geral</span>
                </div>
                @if(is_null($activeProjectId))
                    <div class="size-1.5 rounded-full bg-white animate-pulse"></div>
                @endif
            </button>

            <div class="pt-8 pb-3 px-5 flex items-center justify-between">
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-600">Projetos Ativos</span>
                <div class="h-px flex-1 bg-white/5 ml-4"></div>
            </div>

            {{-- Canais por Projeto --}}
            @foreach($channels as $channel)
                <button wire:click="selectChannel({{ $channel->id }})"
                    class="w-full flex items-center justify-between px-5 py-4 rounded-[1.5rem] transition-all duration-300 group/item
                    {{ $activeProjectId == $channel->id ? 'bg-white text-zinc-950 shadow-xl' : 'text-zinc-500 hover:bg-white/5' }}">
                    <div class="flex items-center gap-3">
                        <div class="size-2 rounded-full {{ $activeProjectId == $channel->id ? 'bg-brand-500' : 'bg-zinc-700 group-hover/item:bg-zinc-500' }} transition-colors"></div>
                        <span class="font-black uppercase text-[10px] tracking-tight truncate max-w-[140px]">{{ $channel->name }}</span>
                    </div>
                    @if($activeProjectId == $channel->id)
                         <flux:icon name="chevron-right" variant="micro" class="size-3" />
                    @endif
                </button>
            @endforeach
        </nav>

        {{-- Footer da Sidebar (User Status) --}}
        <div class="p-6 bg-white/5 border-t border-white/5">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="size-10 rounded-2xl bg-brand-600 flex items-center justify-center text-white font-black text-xs shadow-lg uppercase">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="absolute -bottom-1 -right-1 size-3.5 bg-emerald-500 border-4 border-zinc-950 rounded-full"></div>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black text-white uppercase tracking-tight">{{ explode(' ', auth()->user()->name)[0] }}</span>
                    <span class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest italic">Online</span>
                </div>
            </div>
        </div>
    </aside>

    {{-- 2. JANELA DE CHAT PRINCIPAL (ESTILO HIGH-FIDELITY) --}}
    <main class="flex-1 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden relative">

        {{-- Cabeçalho do Chat (Estilo Glass) --}}
        <header class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-white/50 dark:bg-zinc-900/50 backdrop-blur-md relative z-10">
            <div class="flex items-center gap-4">
                <div class="p-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-2xl text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    <flux:icon name="{{ is_null($activeProjectId) ? 'chat-bubble-left-right' : 'briefcase' }}" variant="outline" class="size-5" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-black dark:text-white uppercase text-base tracking-tight italic">{{ $activeChannelName }}</h2>
                        <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    </div>
                    <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest mt-0.5">Canal de Transmissão Direta</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:block">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Procurar no histórico..." class="w-64 !bg-zinc-50 dark:!bg-zinc-800/50 border-none rounded-xl" size="sm" />
                </div>
                <flux:button variant="ghost" icon="ellipsis-vertical" class="rounded-xl" />
            </div>
        </header>

        {{-- Contentor de Mensagens (Efeito de Profundidade) --}}
        <div x-ref="chatContainer"
             class="flex-1 overflow-y-auto p-8 space-y-8 no-scrollbar bg-zinc-50/30 dark:bg-zinc-950/20"
             style="background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.03) 1px, transparent 0); background-size: 24px 24px;">

            @forelse($messages as $msg)
                <div class="flex gap-4 {{ $msg->isFromAuthUser() ? 'flex-row-reverse' : '' }} group animate-fade-in">
                    {{-- Avatar Premium --}}
                    <div class="flex-shrink-0 mt-1">
                        <div class="size-10 rounded-2xl flex items-center justify-center font-black text-xs shadow-md border-2
                            {{ $msg->isFromAuthUser()
                                ? 'bg-zinc-900 border-zinc-800 text-brand-400'
                                : 'bg-white dark:bg-zinc-800 border-zinc-100 dark:border-zinc-700 text-zinc-500' }}">
                            {{ substr($msg->user->name, 0, 1) }}
                        </div>
                    </div>

                    {{-- Balão de Mensagem --}}
                    <div class="max-w-[65%] space-y-1.5">
                        <div class="flex items-center gap-3 {{ $msg->isFromAuthUser() ? 'flex-row-reverse text-right' : '' }}">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400">{{ $msg->user->name }}</span>
                            <span class="text-[9px] font-bold text-zinc-300 dark:text-zinc-600 uppercase">{{ $msg->sent_at }}</span>
                        </div>

                        <div class="relative p-5 rounded-[1.8rem] text-sm leading-relaxed shadow-sm transition-all group-hover:shadow-md
                            {{ $msg->isFromAuthUser()
                                ? 'bg-brand-600 text-white rounded-tr-none font-medium'
                                : 'bg-white dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-700 rounded-tl-none' }}">
                            {{ $msg->content }}

                            {{-- Checkmark discreto para as minhas mensagens --}}
                            @if($msg->isFromAuthUser())
                                <div class="absolute bottom-2 right-3 opacity-40">
                                    <flux:icon name="check" variant="micro" class="size-3" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-center space-y-4 opacity-40">
                    <div class="p-6 bg-zinc-100 dark:bg-zinc-800 rounded-[2.5rem] border-2 border-dashed border-zinc-200 dark:border-zinc-700">
                        <flux:icon name="chat-bubble-bottom-center-text" class="size-12 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black dark:text-white uppercase tracking-widest">Início da Conversa</p>
                        <p class="text-xs text-zinc-500 mt-1 italic">As mensagens enviadas aqui são encriptadas e seguras.</p>
                    </div>
                </div>
            @endforelse
        </div>
        {{-- 3. CAMPO DE ESCRITA (ESTILO SaaS COMMAND) --}}
        <footer class="p-6 bg-white dark:bg-zinc-900 border-t border-zinc-100 dark:border-zinc-800 relative z-10">
            <div class="relative group">
                {{-- Painel de Input com Efeito de Sombra Interna --}}
                <div class="flex items-end gap-3 p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-inner focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">

                    {{-- Botão de Anexo (Estético/Futuro) --}}
                    <button class="p-3 text-zinc-400 hover:text-brand-500 transition-colors">
                        <flux:icon name="plus" variant="micro" class="size-5" />
                    </button>

                    <textarea
                        wire:model="content"
                        rows="1"
                        placeholder="Escreve uma mensagem para a equipa..."
                        class="flex-1 py-3 bg-transparent border-none focus:ring-0 text-sm dark:text-white resize-none no-scrollbar placeholder:text-zinc-400 font-medium"
                        x-on:keydown.enter.prevent="if($event.target.value.trim() !== '') { $wire.sendMessage().then(() => scrollToBottom()) }"
                    ></textarea>

                    {{-- Botão de Envio de Alto Impacto --}}
                    <button
                        wire:click="sendMessage"
                        @click="scrollToBottom()"
                        class="p-4 bg-brand-600 text-white rounded-[1.4rem] shadow-lg shadow-brand-500/20 hover:bg-brand-500 hover:scale-105 active:scale-95 transition-all group/send"
                    >
                        <flux:icon name="paper-airplane" variant="solid" class="size-5 group-hover/send:translate-x-0.5 group-hover/send:-translate-y-0.5 transition-transform" />
                    </button>
                </div>
            </div>

            {{-- Dica de Atalho Estilizada --}}
            <div class="flex justify-center items-center gap-3 mt-4">
                <div class="h-px w-8 bg-zinc-100 dark:bg-zinc-800"></div>
                <p class="text-[9px] text-zinc-400 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                    Prime <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded text-zinc-500 italic">ENTER</span> para transmitir
                </p>
                <div class="h-px w-8 bg-zinc-100 dark:border-zinc-800"></div>
            </div>
        </footer>
    </main>
</div>

{{-- 4. ESTILOS DE INTERFACE (SCROLL & ANIMAÇÕES) --}}
<style>
    /* Animação de entrada das mensagens */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeInUp 0.4s ease-out forwards;
    }

    /* Scrollbar ultra-fina e discreta para a janela de chat */
    #chat-window::-webkit-scrollbar {
        width: 4px;
    }

    #chat-window::-webkit-scrollbar-track {
        background: transparent;
    }

    #chat-window::-webkit-scrollbar-thumb {
        background: #e4e4e7; /* zinc-200 */
        border-radius: 10px;
    }

    .dark #chat-window::-webkit-scrollbar-thumb {
        background: #27272a; /* zinc-800 */
    }

    #chat-window::-webkit-scrollbar-thumb:hover {
        background: #3b82f6; /* brand-500 */
    }

    /* Utilitários */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Garantir que o campo de texto cresce mas mantém limites */
    textarea {
        min-height: 44px;
        max-height: 200px;
    }
</style>
