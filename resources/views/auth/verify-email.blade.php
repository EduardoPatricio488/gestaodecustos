<!DOCTYPE html>
<html lang="pt" class="h-full bg-zinc-50 dark:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Conta | Finance Pro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased font-sans">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">

        <div class="w-full max-w-md space-y-8">

            {{-- HEADER / LOGO --}}
            <div class="text-center">
                <div class="inline-flex p-4 bg-emerald-500/10 rounded-[2rem] mb-6 shadow-inner">
                    <flux:icon name="shield-check" class="size-10 text-emerald-600" />
                </div>
                <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                    Verificar Conta
                </h1>
                <p class="text-zinc-500 font-medium mt-4 text-sm leading-relaxed px-8">
                    Enviámos um código de segurança para o teu e-mail. Introduz os 6 dígitos abaixo para ativar o teu acesso.
                </p>
            </div>

            {{-- FORMULÁRIO DE CÓDIGO --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-10 shadow-2xl transition-all">

                <form action="{{ route('verification.verify-code') }}" method="POST" class="space-y-8">
                    @csrf

                    <div class="space-y-4 text-left">
                        <label class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.3em] ml-1">
                            Código de Segurança
                        </label>

                        <input
                            type="text"
                            name="code"
                            maxlength="6"
                            placeholder="000000"
                            autofocus
                            class="w-full h-20 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-[1.5rem]
                                   text-center text-4xl font-black tracking-[0.5em] text-emerald-600 outline-none
                                   focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-inner"
                        >

                        @error('code')
                            <p class="text-red-500 text-xs font-bold text-center mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full h-16 rounded-2xl font-black uppercase tracking-widest bg-emerald-600 hover:bg-emerald-700 text-white border-none shadow-xl shadow-emerald-500/20 text-sm transition-all active:scale-95">
                        Ativar Acesso 🟢
                    </button>
                </form>

                {{-- REENVIAR CÓDIGO --}}
                <div class="mt-10 pt-8 border-t border-zinc-100 dark:border-zinc-800">
                    <form action="{{ route('verification.send') }}" method="POST" class="text-center">
    @csrf
    <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 hover:text-emerald-500 transition-colors">
        Reenviar Código de Segurança
    </button>
</form>
                </div>
            </div>

            {{-- BOTÃO DE SAÍDA --}}
            <div class="text-center">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-red-500 transition-colors">
                        🚪 Sair da Conta
                    </button>
                </form>
            </div>

        </div>

        {{-- RODAPÉ --}}
        <p class="mt-12 text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em] opacity-50">
            Finance Pro · Protocolo de Segurança Ativo
        </p>
    </div>
</body>
</html>
