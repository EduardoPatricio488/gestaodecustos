<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
      @include('partials.head')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Externo - Finance Pro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
    <div class="min-h-screen flex flex-col items-center px-6 pt-12 md:pt-20 relative overflow-hidden">

        {{-- Brilho de Fundo --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-64 bg-emerald-500/10 blur-[100px] rounded-full pointer-events-none"></div>

        {{-- TITULO / LOGO --}}
        <div class="flex justify-center mb-10 relative z-10">
            <a href="/" wire:navigate class="flex items-center gap-3 font-black text-2xl tracking-tighter">
                <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-600 shadow-lg shadow-emerald-500/20">
                    <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <span class="dark:text-white text-zinc-900 uppercase italic">Gestão de Custos</span>
            </a>
        </div>

        {{-- ✅ ATUALIZADO: Adicionada a rota 'careers.portal' à lista de largura total --}}
        <div class="w-full {{ request()->routeIs('client.portal', 'supplier.dashboard', 'bank.dashboard', 'careers.apply', 'careers.portal') ? 'max-w-[1400px]' : 'max-w-[440px]' }} relative z-10">
            {{ $slot }}

            <p class="mt-8 text-center text-[9px] text-zinc-500 font-black uppercase tracking-[0.3em] opacity-40">
                &copy; {{ date('Y') }} — Hub de Gestão Inteligente
            </p>
        </div>
    </div>
    @livewireScripts
    @fluxScripts
</body>
</html>
