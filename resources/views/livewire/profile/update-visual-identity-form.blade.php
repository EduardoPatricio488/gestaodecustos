<div class="relative p-8 md:p-12">
    {{-- Brilho de fundo que reage em tempo real --}}
   @if(!auth()->user()->isAdmin())
   <div class="absolute -right-20 -top-20 size-80 blur-[120px] rounded-full opacity-20 transition-all duration-1000 pointer-events-none"
         style="background-color: {{ $profile_color }}"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-center gap-12">

        {{-- COLUNA 1: PREVIEW GRANDE --}}
        <div class="flex flex-col items-center gap-6 shrink-0">
            <div class="size-48 rounded-[3rem] flex items-center justify-center text-8xl shadow-2xl transition-all duration-700 animate-in zoom-in duration-500"
                 style="background-color: {{ $profile_color }}15; border: 4px solid {{ $profile_color }}40; box-shadow: 0 20px 50px -12px {{ $profile_color }}30;">
                {{ $profile_emoji }}
            </div>
            <div class="text-center space-y-1">
                <p class="text-[10px] font-black uppercase text-zinc-500 tracking-[0.3em]">Identidade Ativa</p>
                <p class="text-xs font-bold text-zinc-400 italic">Visualizado no Dashboard</p>
            </div>
        </div>

        {{-- COLUNA 2: SELETORES --}}
        <div class="flex-1 w-full space-y-10">

            {{-- APENAS MOSTRA SE NÃO FOR ADMINISTRADOR --}}

    {{-- SELETOR DE EMOJIS --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-indigo-500 font-bold text-xs">01.</span>
            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-[0.2em]">Escolhe o teu Mood</p>
        </div>
        <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-10 gap-3">
            @foreach(['😀', '😎', '🤓', '🧑‍💻', '🤑', '🚀', '💎', '📈', '🧘‍♂️', '🦁', '🔥', '✨', '⚡', '🏆', '🎮', '🎧', '🍕', '🌍', '❤️', '👑'] as $e)
                <button
                    type="button"
                    wire:click="updateIdentity('emoji', '{{ $e }}')"
                    class="size-11 rounded-xl bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center text-xl hover:scale-110 active:scale-95 transition-all border-2 {{ $profile_emoji === $e ? 'border-brand-500 bg-white dark:bg-zinc-700 shadow-lg scale-110' : 'border-transparent opacity-60' }}"
                >
                    {{ $e }}
                </button>
            @endforeach
        </div>

        {{-- CAMPO DE EMOJI PERSONALIZADO --}}
        <div class="flex items-center gap-3 mt-4">
            <input
                type="text"
                wire:model="custom_emoji"
                wire:keydown.enter="applyCustomEmoji"
                placeholder="Cola ou digita qualquer emoji..."
                maxlength="8"
                class="flex-1 text-2xl text-center rounded-xl border-2 border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-4 py-2 focus:border-brand-500 focus:outline-none transition-all placeholder:text-sm placeholder:text-zinc-400 dark:text-white"
            />
            <button
                type="button"
                wire:click="applyCustomEmoji"
                class="shrink-0 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-xs font-black uppercase tracking-widest transition-all shadow-md"
            >
                Aplicar
            </button>
        </div>
    </div>


            {{-- SELETOR DE CORES --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-indigo-500 font-bold text-xs">02.</span>
                    <p class="text-[10px] font-black uppercase text-zinc-500 tracking-[0.2em]">Cor da tua Marca</p>
                </div>
                <div class="flex flex-wrap gap-4">
                    @foreach(['#10b981', '#6366f1', '#f59e0b', '#ef4444', '#0ea5e9', '#ec4899', '#a855f7', '#71717a', '#fb7185', '#2dd4bf'] as $c)
                        <button
                            type="button"
                            wire:click="updateIdentity('color', '{{ $c }}')"
                            class="size-10 rounded-full border-4 border-white dark:border-zinc-800 shadow-xl transition-all hover:scale-125 {{ $profile_color === $c ? 'ring-4 ring-brand-500/40 scale-125 z-10' : 'opacity-80' }}"
                            style="background-color: {{ $c }};"
                        ></button>
                    @endforeach
                </div>
            </div>

            {{-- FEEDBACK DE CARREGAMENTO --}}
            <div wire:loading wire:target="updateIdentity" class="absolute bottom-4 right-8 flex items-center gap-2 text-brand-500">
                <flux:icon name="arrow-path" class="size-3 animate-spin" />
                <span class="text-[9px] font-black uppercase tracking-widest">Sincronizando...</span>
            </div>
        </div>
    </div>
</div>
@endif
