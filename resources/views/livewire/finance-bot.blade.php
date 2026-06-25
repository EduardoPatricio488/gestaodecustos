<div
    x-data="{
        open: @entangle('isOpen'),
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            });
        }
    }"
    x-init="scrollToBottom()"
    x-on:message-sent.window="scrollToBottom()"
>

    {{-- BOTÃO FLUTUANTE --}}
    <button
        wire:click="toggleChat"
        class="fixed bottom-6 right-6 z-[100] size-14 rounded-full bg-brand-600 text-white shadow-2xl shadow-brand-500/40 flex items-center justify-center hover:scale-110 active:scale-95 transition-all group"
    >
        <flux:icon name="sparkles" x-show="!open" class="size-6 group-hover:rotate-12 transition-transform" />
        <flux:icon name="x-mark" x-show="open" x-cloak class="size-6" />
        <div x-show="!open" class="absolute -top-1 -right-1 size-4 bg-emerald-400 border-2 border-white dark:border-zinc-950 rounded-full"></div>
    </button>

    {{-- JANELA DE CHAT --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95"
        class="fixed bottom-24 right-6 z-[100]
               w-[400px] max-w-[calc(100vw-2rem)]
               h-[640px] max-h-[calc(100vh-7rem)]
               bg-white dark:bg-zinc-950 rounded-[2.5rem]
               border border-zinc-200 dark:border-zinc-800 shadow-2xl
               flex flex-col overflow-hidden"
    >

        {{-- HEADER --}}
        <div class="p-6 bg-zinc-950 text-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-2xl bg-brand-500 flex items-center justify-center shadow-lg shadow-brand-500/20">
                    <flux:icon name="sparkles" class="size-5 text-white" />
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest leading-none">Finance Pilot</h3>
                    <p class="text-[9px] text-emerald-400 font-bold uppercase mt-1 animate-pulse">Online agora</p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <button wire:click="handleAction('menu:root')" title="Menu principal"
                        class="text-zinc-500 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/5">
                    <flux:icon name="squares-2x2" class="size-4" />
                </button>

                <button @click="open = false"
                        class="text-zinc-500 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/5">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>
        </div>

        {{-- MENSAGENS --}}
        <div
            x-ref="messagesContainer"
            class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar
                   bg-zinc-50/50 dark:bg-zinc-900/20"
        >
            @foreach($messages as $msg)
                <div wire:key="msg-{{ $msg['id'] }}"
                     class="flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">

                    @php
                        $rendered = e($msg['content']);
                        $rendered = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $rendered);
                        $rendered = nl2br($rendered);
                    @endphp

                    <div class="max-w-[88%] p-4 rounded-2xl text-sm leading-relaxed
                        {{ $msg['role'] === 'user'
                            ? 'bg-brand-600 text-white rounded-tr-none'
                            : 'bg-white dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-tl-none font-medium'
                        }}">
                        {!! $rendered !!}
                    </div>

                    {{-- BOTÕES DE OPÇÃO --}}
                    @if(!empty($msg['options']))
                        <div class="flex flex-wrap gap-2 mt-4 justify-end max-w-[95%]">
                            @foreach($msg['options'] as $opt)
                                <button
                                    wire:click="handleAction('{{ $opt['action'] }}')"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800
                                           rounded-xl text-[10px] font-black uppercase tracking-widest
                                           hover:border-brand-500 hover:text-brand-600 transition-all shadow-sm disabled:opacity-40"
                                >
                                    {{ $opt['label'] }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- A aguardar resposta --}}
            @if($awaiting)
                <div class="flex justify-start">
                    <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-500/10
                                border border-amber-200 dark:border-amber-500/20 rounded-xl">
                        <flux:icon name="pencil" class="size-3.5 text-amber-600" />
                        <span class="text-[9px] font-black uppercase tracking-widest text-amber-600">
                            A aguardar a tua resposta...
                        </span>
                    </div>
                </div>
            @endif

            {{-- "A escrever..." --}}
            <div wire:loading wire:target="sendMessage,handleAction" class="flex items-start">
                <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                            rounded-2xl rounded-tl-none px-4 py-3 flex items-center gap-1">
                    <span class="size-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="size-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="size-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>
        </div>

        {{-- INPUT --}}
        <form wire:submit.prevent="sendMessage"
              class="p-4 bg-white dark:bg-zinc-950 border-t dark:border-zinc-800 flex-shrink-0">

            <div class="relative flex items-center">
                <input
                    wire:model="userInput"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                    placeholder="{{ $awaiting ? 'Escreve a tua resposta...' : 'Escreve uma pergunta...' }}"
                    autocomplete="off"
                    class="w-full bg-zinc-100 dark:bg-zinc-900 border-none rounded-2xl py-3 pl-4 pr-12
                           text-sm font-medium focus:ring-2 focus:ring-brand-500 transition-all disabled:opacity-60"
                >

                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage"
                        class="absolute right-2 p-2 text-brand-600 disabled:opacity-40 hover:scale-110 transition-transform">
                    <flux:icon name="paper-airplane" variant="solid" class="size-5" />
                </button>
            </div>

        </form>
    </div>
</div>
