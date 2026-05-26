<!DOCTYPE html>
<html lang="pt" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registos Fechados | {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .glow-blue {
            position: absolute;
            width: 100%;
            max-width: 600px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, rgba(0, 0, 0, 0) 70%);
            filter: blur(80px);
            z-index: -1;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased flex items-center justify-center min-h-screen p-6">

    <div class="glow-blue text-center flex items-center justify-center"></div>

    <div class="relative z-10 max-w-lg w-full text-center space-y-10">

        <!-- Ícone -->
        <div class="flex justify-center">
            <div class="relative p-8 bg-zinc-900/50 border border-zinc-800 rounded-[3rem] shadow-2xl backdrop-blur-xl">
                <flux:icon name="user-plus" variant="outline" class="size-16 text-brand-500 opacity-50" />
                <div class="absolute -top-2 -right-2 bg-red-500 text-white p-2 rounded-full shadow-lg">
                    <flux:icon name="x-mark" variant="micro" class="size-5" />
                </div>
            </div>
        </div>

        <!-- Texto Principal -->
        <div class="space-y-4">
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic italic">
                Registos <span class="text-brand-500">Suspensos</span>
            </h1>
            <p class="text-zinc-400 text-lg font-medium leading-relaxed">
                De momento, não estamos a aceitar novas contas. <br>
                A plataforma está limitada a utilizadores convidados ou já registados.
            </p>
        </div>

        <!-- Ações -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/" class="w-full sm:w-auto px-8 py-3 bg-zinc-900 border border-zinc-800 rounded-2xl text-sm font-bold hover:bg-zinc-800 transition-all">
                Voltar ao Início
            </a>

            <a href="/login" class="w-full sm:w-auto px-8 py-3 bg-brand-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-brand-500/20 hover:bg-brand-500 transition-all">
                Já tenho conta (Login)
            </a>
        </div>

        <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em]">
            Equipa de Administração — {{ config('app.name') }}
        </p>

    </div>

</body>
</html>
