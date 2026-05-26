{{-- Só aparece se existir um ID de administrador guardado na sessão --}}
@if(session()->has('impersonator_id'))
    <div class="bg-amber-600 text-white py-2 px-4 flex flex-col sm:flex-row justify-between items-center relative z-[100] shadow-2xl border-b border-amber-700 animate-in fade-in duration-500">

        {{-- Lado Esquerdo: Mensagem de Aviso --}}
        <div class="flex items-center gap-3 mb-2 sm:mb-0">
            {{-- Ícone Animado de Olho/Monitorização --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 animate-pulse text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>

            <p class="text-sm font-bold uppercase tracking-wider leading-none">
                Modo Suporte Ativo: <span class="text-amber-100 italic">Vedo a conta de</span> <b>{{ auth()->user()->name }}</b>
            </p>
        </div>

        {{-- Lado Direito: Botão para Voltar --}}
        <a href="{{ route('admin.stop-impersonating') }}"
           class="bg-white text-amber-700 px-4 py-1.5 rounded-xl text-[10px] font-black hover:bg-amber-50 hover:scale-105 active:scale-95 transition-all uppercase no-underline shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
            </svg>
            Sair e Voltar ao Admin
        </a>
    </div>
@endif
