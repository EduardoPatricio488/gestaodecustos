<x-guest-layout>
    {{-- EFEITO DE FUNDO --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[5%] -right-[5%] size-64 bg-emerald-500/5 blur-[80px] rounded-full"></div>
    </div>

    <div class="mx-auto max-w-[360px] relative z-10 space-y-6 text-center">
        {{-- HEADER DE SEGURANÇA --}}
        <div class="space-y-4">
            <div class="inline-flex relative">
                <div class="absolute inset-0 bg-emerald-500/15 blur-xl rounded-full"></div>
                <div class="relative p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl text-emerald-600">
                    <flux:icon name="shield-check" variant="solid" class="size-7" />
                </div>
            </div>

            <div class="space-y-1">
                <h1 class="text-2xl font-black dark:text-white uppercase tracking-tighter italic">Ativar Conta</h1>
                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest italic leading-tight">
                    Enviámos um código de segurança <br> para o teu endereço de email.
                </p>
            </div>
        </div>

        {{-- CARD DO FORMULÁRIO --}}
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl">
            <form method="POST" action="{{ route('verification.verify-code') }}" class="space-y-6">
                @csrf
                <div class="space-y-3">
                    <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em]">Código de 6 Dígitos</flux:label>
                    <input
                        name="code"
                        type="text"
                        maxlength="6"
                        placeholder="000000"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="w-full text-center text-3xl tracking-[0.5em] font-black bg-zinc-50 dark:bg-zinc-950 border-none rounded-2xl h-16 shadow-inner text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <flux:button type="submit" variant="primary" class="w-full h-12 rounded-xl font-black uppercase tracking-widest shadow-lg bg-emerald-600 border-none hover:bg-emerald-500 transition-all active:scale-95 text-white">
                    Validar Protocolo 🟢
                </flux:button>
            </form>
        </div>

        {{-- MENSAGEM DE STATUS --}}
        @if (session('status'))
            <div class="text-[10px] font-black uppercase text-emerald-500 tracking-widest italic">
                ✓ {{ session('status') }}
            </div>
        @endif

        {{-- OPÇÕES DE REENVIO E SAÍDA --}}
        <div class="space-y-4 animate-fade-in">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="text-[10px] font-black uppercase text-zinc-400 hover:text-emerald-600 transition-colors tracking-widest italic">
                    Não recebi o código. Reenviar agora.
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-[9px] font-bold text-red-500/70 uppercase tracking-tighter hover:text-red-500 transition-colors">
                    Cancelar e Sair do Terminal
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

<style>
    /* Garante que o input de código foca corretamente */
    input::placeholder {
        letter-spacing: normal;
        font-weight: normal;
        opacity: 0.3;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
</style>
