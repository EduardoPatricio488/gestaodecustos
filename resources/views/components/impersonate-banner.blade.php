{{-- INDICADOR DE MODO VISUALIZAÇÃO CENTRADO (SUBSTITUIR A BARRA AMBER) --}}
@if(session()->has('impersonator_id'))
    <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[999] w-auto animate-in fade-in slide-in-from-bottom-10 duration-700">
        <div class="relative group">
            <!-- Aura de brilho atrás do botão -->
            <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-amber-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>

            <div class="relative flex items-center gap-4 px-6 py-3 bg-zinc-900 dark:bg-zinc-800 border border-white/10 rounded-full shadow-2xl backdrop-blur-md">

                <div class="flex items-center gap-3">
                    {{-- Ícone de Alerta Animado --}}
                    <div class="size-8 rounded-full bg-red-600 flex items-center justify-center shadow-lg animate-pulse">
                        <flux:icon name="eye" variant="micro" class="size-4 text-white" />
                    </div>

                    <div class="flex flex-col items-start leading-none pr-4 border-r border-white/10 text-left">
                        <span class="text-[8px] font-black uppercase tracking-[0.2em] text-red-500">Modo Suporte</span>
                        <span class="text-[10px] font-black uppercase tracking-tight text-white mt-0.5">
                            {{ auth()->user()->name }}
                        </span>
                    </div>

                    {{-- Botão de Sair --}}
                    <a href="{{ route('admin.stop-impersonating') }}"
                       class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-white transition-all no-underline flex items-center gap-2 group/btn">
                        <span>Voltar ao Admin</span>
                        <flux:icon name="arrow-right-start-on-rectangle" variant="micro" class="size-3 group-hover/btn:translate-x-1 transition-transform" />
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
