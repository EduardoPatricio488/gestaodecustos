<div
    x-data="{ chatOpen: @entangle('open').live }"
    x-on:open-chat-with.window="chatOpen = true; if ($event.detail.userId) $wire.openDirectChatWith($event.detail.userId)"
    class="relative"
>

@once
<style>
[x-cloak]{display:none!important}

.fc-chat-panel{
    position:fixed;inset:0;z-index:200;
    display:flex;align-items:center;justify-content:center;
    padding:1rem;
    background:rgba(0,0,0,.35);
}

.fc-chat-window{
    position:relative;
    width:100%;max-width:26rem;height:min(680px, 90vh);
    background:#fff;
    border:1px solid rgba(16,185,129,.1);
    border-radius:1.75rem;
    box-shadow:0 24px 60px -10px rgba(0,0,0,.2);
    display:flex;flex-direction:column;overflow:hidden;
}

.fc-chat-input{
    background:rgba(0,0,0,.04);
    border:1.5px solid rgba(0,0,0,.07);
    border-radius:.875rem;
    outline:none;
    font-size:.75rem;
    color:#0f172a;
    transition:border-color .18s,box-shadow .18s,background .18s;
}
.fc-chat-input:focus{
    background:#fff;
    border-color:#10b981;
    box-shadow:0 0 0 3px rgba(16,185,129,.08);
}

.fc-chat-scroll::-webkit-scrollbar{width:3px}
.fc-chat-scroll::-webkit-scrollbar-thumb{background:#10b981;border-radius:10px}
.fc-chat-scroll::-webkit-scrollbar-track{background:transparent}

.fc-msg-mine{
    background:linear-gradient(135deg,#059669,#10b981);
    color:#fff;
    border-radius:1.25rem 1.25rem 4px 1.25rem;
    box-shadow:0 4px 12px -2px rgba(16,185,129,.3);
}
.fc-msg-other{
    background:#fff;
    color:#0f172a;
    border:1px solid rgba(0,0,0,.06);
    border-radius:1.25rem 1.25rem 1.25rem 4px;
    box-shadow:0 2px 8px -2px rgba(0,0,0,.06);
}

.fc-unread-dot{
    min-width:.6rem;height:.6rem;
    background:#10b981;border-radius:999px;
    flex-shrink:0;
}

@keyframes fc-chat-in{
    from{opacity:0;transform:scale(.95) translateY(8px)}
    to{opacity:1;transform:scale(1) translateY(0)}
}
.fc-chat-anim{animation:fc-chat-in .2s ease both}
</style>
@endonce

{{-- BOTÃO LAUNCHER --}}
@if($showLauncher)
<button @click="chatOpen = true" type="button"
    class="relative size-10 rounded-[.875rem] bg-white border border-zinc-100 text-zinc-500 hover:text-emerald-600 hover:border-emerald-200 transition-all shadow-sm flex items-center justify-center"
    aria-label="Mensagens"
>
    <flux:icon name="chat-bubble-left-right" class="size-4.5" />
    @if($this->totalUnreadCount > 0)
        <span class="absolute -top-1 -right-1 min-w-[1.1rem] h-[1.1rem] px-1 bg-emerald-500 text-white text-[8px] font-black rounded-full flex items-center justify-center border-2 border-white shadow">
            {{ $this->totalUnreadCount > 9 ? '9+' : $this->totalUnreadCount }}
        </span>
    @endif
</button>
@endif

{{-- JANELA DO CHAT --}}
<div x-show="chatOpen" x-cloak class="fc-chat-panel"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-end="opacity-0"
>
    <div @click="chatOpen = false" class="absolute inset-0"></div>

    <div @click.stop class="fc-chat-window fc-chat-anim">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-100 shrink-0 bg-white">
            @if($this->activeConversation)
                @php $other = $this->activeConversation->is_group ? null : $this->activeConversation->otherUser(auth()->id()); @endphp
                <button wire:click="backToList" class="flex items-center gap-2.5 min-w-0 group">
                    <flux:icon name="chevron-left" class="size-4 text-emerald-600 shrink-0 group-hover:-translate-x-0.5 transition-transform" />
                    <flux:avatar
                        src="{{ $other?->avatarUrl() }}"
                        initials="{{ $this->activeConversation->is_group ? '#' : ($other?->initials() ?? '?') }}"
                        class="size-9 rounded-xl border border-black/5 shrink-0"
                    />
                    <div class="text-left min-w-0">
                        <p class="text-sm font-black text-zinc-900 truncate">
                            {{ $this->activeConversation->is_group ? $this->activeConversation->name : ($other?->name ?? 'Conversa') }}
                        </p>
                        <p class="text-[9px] font-bold text-emerald-500 uppercase">ativo agora</p>
                    </div>
                </button>
            @else
                <div>
                    <p class="text-sm font-black text-zinc-900 uppercase tracking-tight">Mensagens</p>
                    <p class="text-[9px] font-bold text-emerald-500 uppercase mt-0.5">Finance Connect</p>
                </div>
            @endif

            <div class="flex items-center gap-1.5 shrink-0">
                @if(!$this->activeConversation)
                    <button wire:click="$set('showNewChat', true)"
                        class="size-8 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-colors"
                        title="Nova conversa">
                        <flux:icon name="pencil-square" class="size-4" />
                    </button>
                @endif
                <button @click="chatOpen = false"
                    class="size-8 rounded-xl bg-zinc-50 text-zinc-400 hover:text-zinc-900 flex items-center justify-center transition-colors">
                    <flux:icon name="x-mark" class="size-4" />
                </button>
            </div>
        </div>

        {{-- CONTEÚDO --}}
        <div class="flex-1 overflow-hidden flex flex-col bg-zinc-50/40">

            @if($showNewChat)
                {{-- NOVO CHAT: Pesquisa de utilizadores --}}
                <div class="p-4 space-y-3 flex-1 overflow-y-auto fc-chat-scroll">
                    <div class="relative">
                        <flux:icon name="magnifying-glass" class="size-3.5 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <input type="text" wire:model.live.debounce.300ms="userSearch"
                            placeholder="Procurar pessoas..."
                            class="fc-chat-input w-full py-2.5 pl-9 pr-4">
                    </div>

                    <div class="space-y-1">
                        @forelse($this->searchResults as $user)
                            <button wire:click="startDirectChat({{ $user->id }})"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white hover:shadow-sm transition-all border border-transparent hover:border-zinc-100 text-left">
                                <flux:avatar src="{{ $user->avatarUrl() }}" class="size-9 rounded-xl border border-black/5 shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-black text-zinc-900 truncate">{{ $user->name }}</p>
                                    <p class="text-[9px] text-zinc-400 font-bold">@{{ $user->username }}</p>
                                </div>
                                <flux:icon name="chat-bubble-left" class="size-4 text-emerald-500 shrink-0" />
                            </button>
                        @empty
                            @if(trim($userSearch) !== '')
                                <p class="text-center text-[10px] text-zinc-400 font-bold uppercase tracking-widest py-8">Sem resultados</p>
                            @else
                                <p class="text-center text-[10px] text-zinc-400 font-bold uppercase tracking-widest py-8">Escreve um nome para pesquisar</p>
                            @endif
                        @endforelse
                    </div>
                </div>

            @elseif($this->activeConversation)
                {{-- MENSAGENS --}}
                <div
                    class="flex-1 overflow-y-auto fc-chat-scroll p-4 space-y-2.5"
                    x-init="$el.scrollTop = $el.scrollHeight"
                    x-on:livewire:navigated.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
                    x-on:message-sent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
                >
                    @forelse($this->conversationMessages as $message)
                        @php $isMine = $message->user_id === auth()->id(); @endphp
                        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} items-end gap-2">
                            @if(!$isMine)
                                <flux:avatar src="{{ $message->user->avatarUrl() }}" class="size-6 rounded-lg border border-black/5 shrink-0 mb-0.5" />
                            @endif
                            <div class="{{ $isMine ? 'fc-msg-mine' : 'fc-msg-other' }} px-4 py-2.5 max-w-[80%]">
                                <p class="text-sm font-medium leading-relaxed break-words">{{ $message->content }}</p>
                                <div class="flex items-center {{ $isMine ? 'justify-end' : 'justify-start' }} gap-1 mt-1">
                                    <span class="text-[8px] font-bold {{ $isMine ? 'text-white/60' : 'text-zinc-400' }} uppercase">{{ $message->created_at->format('H:i') }}</span>
                                    @if($isMine)
                                        <flux:icon name="check" class="size-2.5 text-white/60" />
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full py-16 text-center">
                            <div class="size-12 rounded-2xl bg-emerald-50 flex items-center justify-center mb-3">
                                <flux:icon name="chat-bubble-left-right" class="size-6 text-emerald-400" />
                            </div>
                            <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Diz olá! 👋</p>
                        </div>
                    @endforelse
                </div>

                {{-- INPUT DE MENSAGEM --}}
                <form wire:submit.prevent="sendMessage"
                    class="p-3 bg-white border-t border-zinc-100 flex items-center gap-2 shrink-0">
                    <input wire:model="newMessage"
                        placeholder="Escreve uma mensagem..."
                        class="fc-chat-input flex-1 py-2.5 px-4">
                    <button type="submit" wire:loading.attr="disabled"
                        class="size-9 rounded-xl flex items-center justify-center shrink-0 transition-all
                            bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow hover:filter hover:brightness-110 active:scale-95 disabled:opacity-50">
                        <flux:icon name="paper-airplane" class="size-4 text-white" />
                    </button>
                </form>

            @else
                {{-- LISTA DE CONVERSAS --}}
                <div class="flex-1 overflow-y-auto fc-chat-scroll p-2">
                    @forelse($this->conversations as $conversation)
                        @php
                            $other = $conversation->is_group ? null : $conversation->otherUser(auth()->id());
                            $lastMsg = $conversation->messages->first();
                            $pivot = $conversation->users->firstWhere('id', auth()->id())?->pivot;
                            $isUnread = $lastMsg && $lastMsg->user_id !== auth()->id()
                                && (!$pivot?->last_read_at || $lastMsg->created_at->gt($pivot->last_read_at));
                        @endphp
                        <button wire:click="openConversation({{ $conversation->id }})"
                            class="w-full flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white hover:shadow-sm transition-all text-left group border border-transparent hover:border-zinc-100 {{ $isUnread ? 'bg-emerald-50/50' : '' }}">
                            <div class="relative shrink-0">
                                <flux:avatar src="{{ $other?->avatarUrl() }}" class="size-10 rounded-xl border border-black/5 shadow-sm" />
                                @if($other && method_exists($other, 'isOnline') && $other->isOnline())
                                    <div class="absolute -bottom-0.5 -right-0.5 size-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2 mb-0.5">
                                    <p class="text-sm font-black text-zinc-900 truncate">
                                        {{ $conversation->is_group ? $conversation->name : ($other?->name ?? 'Utilizador') }}
                                    </p>
                                    @if($lastMsg)
                                        <span class="text-[8px] text-zinc-400 font-bold uppercase shrink-0">{{ $lastMsg->created_at->diffForHumans(null, true) }}</span>
                                    @endif
                                </div>
                                <p class="text-xs truncate {{ $isUnread ? 'font-black text-zinc-900' : 'font-medium text-zinc-500' }}">
                                    {{ $lastMsg?->content ?? 'Conversa iniciada' }}
                                </p>
                            </div>
                            @if($isUnread)
                                <div class="fc-unread-dot"></div>
                            @endif
                        </button>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                            <div class="size-14 rounded-2xl bg-zinc-100 flex items-center justify-center mb-3">
                                <flux:icon name="chat-bubble-left-right" class="size-7 text-zinc-300" />
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-[.25em] text-zinc-400">Sem conversas ainda</p>
                            <button wire:click="$set('showNewChat', true)"
                                class="mt-4 px-5 py-2.5 rounded-xl bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-100 transition-colors">
                                Iniciar Conversa
                            </button>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>

</div>
