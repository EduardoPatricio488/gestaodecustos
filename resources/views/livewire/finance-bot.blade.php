<div
x-data="{
    listening:false,
    open: @entangle('isOpen'),
    scrollToBottom() {
        this.$nextTick(() => {
            const el = this.$refs.messagesContainer;
            if (el) el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
        });
    },
    startVoice() {
        const rec = new(window.SpeechRecognition || window.webkitSpeechRecognition)();
        rec.lang = 'pt-PT';
        rec.onresult = e => $wire.set('userInput', e.results[0][0].transcript);
        rec.start();
        this.listening = true;
        rec.onend = () => this.listening = false;
    }
}"
x-init="scrollToBottom(); $watch('open', value => value && scrollToBottom())"
x-on:message-sent.window="scrollToBottom()"
class="relative"
>

    {{-- BOTÃO FLUTUANTE --}}
    <button
        wire:click="toggleChat"
        class="fixed bottom-6 right-6 z-[110] size-16 rounded-full bg-emerald-500 text-white shadow-[0_10px_40px_rgba(16,185,129,0.4)] flex items-center justify-center hover:scale-110 active:scale-95 transition-all group border-4 border-white dark:border-zinc-900"
    >
        <flux:icon name="sparkles" x-show="!open" class="size-7 text-white group-hover:rotate-12 transition-transform" />
        <flux:icon name="x-mark" x-show="open" x-cloak class="size-7 text-white" />

        <div x-show="!open" class="absolute top-0 right-0 size-4 bg-emerald-400 border-2 border-white dark:border-zinc-900 rounded-full">
            <span class="absolute inset-0 rounded-full bg-emerald-400 animate-ping"></span>
        </div>
    </button>

    {{-- JANELA DE CHAT --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95 blur-sm"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100 blur-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100 blur-0"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95 blur-sm"
        class="fixed bottom-24 right-6 z-[110]
               w-[480px] max-w-[calc(100vw-2rem)]
               h-[750px] max-h-[calc(100vh-6rem)]
               bg-white dark:bg-zinc-950 rounded-[3rem]
               border border-zinc-200 dark:border-zinc-800 shadow-[0_30px_90px_rgba(0,0,0,0.4)]
               flex flex-col overflow-hidden text-left"
    >

        {{-- HEADER --}}
        <div class="p-7 bg-zinc-950 text-white flex items-center justify-between flex-shrink-0 border-b border-white/5 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 size-24 bg-brand-500/10 blur-2xl rounded-full"></div>

            <div class="flex items-center gap-4 relative z-10 text-left">
                <div class="size-11 rounded-2xl bg-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                    <flux:icon name="sparkles" class="size-5 text-white" />
                </div>
                <div class="text-left">
                    <h3 class="text-sm font-black uppercase tracking-widest leading-none italic">Finance Pilot</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="size-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        <p class="text-[9px] text-zinc-400 font-black uppercase tracking-widest">IA Estratégica Ativa</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1 relative z-10">
                <button wire:click="handleAction('menu:root')" title="Menu principal"
                        class="text-zinc-500 hover:text-white transition-colors p-2 rounded-xl hover:bg-white/5">
                    <flux:icon name="squares-2x2" class="size-4" />
                </button>
                <button @click="open = false"
                        class="text-zinc-500 hover:text-white transition-colors p-2 rounded-xl hover:bg-white/5">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>
        </div>

        {{-- MENSAGENS --}}
        <div
            x-ref="messagesContainer"
            x-on:input="scrollToBottom()"
            class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar
                   bg-zinc-50/30 dark:bg-zinc-900/10 text-left"
            style="background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.02) 1px, transparent 0); background-size: 32px 32px;"
        >
            @foreach($messages as $msg)
                <div wire:key="msg-{{ $msg['id'] }}"
                     class="flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }} animate-in fade-in slide-in-from-bottom-2 duration-300">

                    @php
                        $rendered = e($msg['content']);
                        $rendered = preg_replace('/\*\*(.*?)\*\*/s', '<strong class="font-black">$1</strong>', $rendered);
                        $rendered = nl2br($rendered);
                    @endphp

                    <div class="max-w-[85%] p-5 rounded-[2rem] text-sm leading-relaxed shadow-sm text-left
                        {{ $msg['role'] === 'user'
                            ? 'bg-emerald-600 text-white rounded-tr-none'
                            : 'bg-white dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-tl-none font-medium'
                        }}">
                        {!! $rendered !!}
                    </div>

                    @if(!empty($msg['options']))
                        <div class="flex flex-wrap gap-2 mt-4 max-w-full">
                            @foreach($msg['options'] as $opt)
                                <button
                                    wire:click="handleAction('{{ $opt['action'] }}')"
                                    class="px-5 py-2.5 bg-white dark:bg-zinc-900 border-2 border-zinc-100 dark:border-zinc-800
                                           rounded-2xl text-[10px] font-black uppercase tracking-widest
                                           hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm active:scale-95"
                                >
                                    {{ $opt['label'] }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- TYPING --}}
            @if($isTyping)
                <div class="flex items-start animate-in fade-in duration-300">
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                                rounded-3xl rounded-tl-none px-6 py-4 flex items-center gap-2 shadow-sm">
                        <div class="flex gap-1.5">
                            <span class="size-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full animate-bounce"></span>
                            <span class="size-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="size-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-2">A analisar...</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- INPUT --}}
        <div class="p-6 bg-white dark:bg-zinc-950 border-t dark:border-zinc-800 flex-shrink-0">

            {{-- HINTS --}}
            <div class="relative mb-5 pb-1">
                <div x-ref="hints" class="flex gap-2 overflow-x-auto no-scrollbar whitespace-nowrap px-8">
                    @foreach([
                        'Resumo de hoje',
                        'Quanto gastei?',
                        'Minhas metas',
                        'Dicas de poupança',
                        'Subscrições',
                        'Investimentos',
                        'Metas anuais',
                        'Relatório semanal',
                        'Alertas',
                        'Tendências'
                    ] as $hint)
                        <button
                           x-on:click="$wire.set('userInput', '{{ $hint }}'); $wire.call('sendMessage')"

                            class="inline-block px-4 py-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-500 hover:text-emerald-600 hover:border-emerald-500 transition-all shadow-sm active:scale-95"
                        >
                            {{ $hint }}
                        </button>
                    @endforeach
                </div>

                {{-- SETAS --}}
                <button
                    x-on:click="$refs.hints.scrollBy({ left: -150, behavior: 'smooth' })"
                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm rounded-full size-7 flex items-center justify-center hover:scale-110 transition"
                >
                    <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button
                    x-on:click="$refs.hints.scrollBy({ left: 150, behavior: 'smooth' })"
                    class="absolute right-0 top-1/2 -translate-y-1/2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm rounded-full size-7 flex items-center justify-center hover:scale-110 transition"
                >
                    <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent class="relative group">
    <div class="absolute inset-0 bg-emerald-500/5 blur-xl rounded-[2rem] opacity-0 group-focus-within:opacity-100 transition-opacity"></div>

    <div class="relative flex items-center bg-zinc-100 dark:bg-zinc-900 border-2 border-transparent focus-within:border-emerald-500/30 rounded-[2rem] p-2 transition-all shadow-inner">

        <input
    wire:model.defer="userInput"
    wire:keydown.enter.prevent="sendMessage"
    wire:loading.attr="disabled"
    wire:target="sendMessage"
    placeholder="Pergunta ao Pilot..."
    autocomplete="off"
    class="flex-1 bg-transparent border-none py-3 pl-4 pr-2
           text-sm font-medium focus:ring-0 text-zinc-800 dark:text-white
           placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
/>

        {{-- ENVIAR --}}
        <button
            type="button"
            wire:click="sendMessage"
            wire:loading.attr="disabled"
            wire:target="sendMessage"
            class="size-12 flex items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-500 hover:scale-105 active:scale-95 transition-all disabled:opacity-40"
        >
            <flux:icon name="paper-airplane" variant="solid" class="size-5" />
        </button>

        {{-- MICROFONE --}}
        <button
            type="button"
            @click="startVoice()"
            :class="listening ? 'bg-emerald-500 text-white' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300'"
            class="size-12 flex items-center justify-center rounded-2xl hover:scale-105 transition"
        >
            🎤
        </button>

    </div>
</form>

            <div class="mt-6 flex items-center justify-center gap-3 opacity-30">
                <div class="h-px bg-zinc-300 dark:bg-zinc-800 flex-1"></div>
                <p class="text-[8px] text-zinc-400 font-black uppercase tracking-[0.4em]">
                    Neural Engine v2.4
                </p>
                <div class="h-px bg-zinc-300 dark:bg-zinc-800 flex-1"></div>
            </div>
        </div>
    </div>

    {{-- ESTILOS --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.08); border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
