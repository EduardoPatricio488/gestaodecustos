<!DOCTYPE html>
<html lang="pt" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manutenção | {{ config('app.name') }}</title>

    <!-- Fontes e Estilos (Reaproveitando o teu Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .glow {
            position: absolute;
            width: 100%;
            max-width: 600px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, rgba(0, 0, 0, 0) 70%);
            filter: blur(80px);
            z-index: -1;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased flex items-center justify-center min-h-screen p-6 overflow-hidden">

    <!-- Efeito de luz de fundo -->
    <div class="glow"></div>

    <div class="relative z-10 max-w-lg w-full text-center space-y-10">

        <!-- Ícone Animado -->
        <div class="flex justify-center">
            <div class="relative">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-8 bg-zinc-900/50 border border-zinc-800 rounded-[3rem] shadow-2xl backdrop-blur-xl">
                    <svg class="w-16 h-16 text-brand-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11 4a2 2 0 114 0v1a2 2 0 002 2 2 2 0 110 4 2 2 0 00-2 2 2 2 0 11-4 0 2 2 0 00-2-2 2 2 0 110-4 2 2 0 002-2V4z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 14v7m-3-3h6" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Texto Principal -->
        <div class="space-y-4">
            <h1 class="text-5xl font-black text-white tracking-tighter uppercase italic italic">
                Pausa para <span class="text-brand-500">Afinações</span>
            </h1>
            <p class="text-zinc-400 text-lg font-medium leading-relaxed">
                Estamos a atualizar a tua plataforma de gestão de custos para garantir a melhor performance. Voltamos num piscar de olhos.
            </p>
        </div>

        <!-- Barra de Estado Discreta -->
        <div class="pt-10 flex flex-col items-center gap-4">
            <div class="inline-flex items-center gap-3 px-5 py-2 bg-zinc-900 border border-zinc-800 rounded-full">
                <div class="w-2 h-2 rounded-full bg-brand-500 animate-ping"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-300">Atualização em curso</span>
            </div>

            <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">
                Tempo estimado: 15 minutos
            </p>
        </div>

    </div>

    <!-- Footer Discreto -->
    <div class="absolute bottom-10 text-center w-full">
        <p class="text-[9px] text-zinc-700 font-black uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} {{ config('app.name') }} · Gestão de Custos Inteligente
        </p>
    </div>

</body>
</html>
