<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Externo - {{ config('app.name') }}</title>
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
    @php
        $isAuthPage = request()->routeIs('login', 'register');
    @endphp

    <div class="min-h-screen flex flex-col items-center px-4 sm:px-6 py-8 md:py-12 relative overflow-hidden text-center">

        {{-- FUNDO PREMIUM PARA AUTENTICAÇÃO --}}
        @if($isAuthPage)
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[700px] h-[420px] bg-brand-500/10 dark:bg-brand-500/15 blur-[120px] rounded-full"></div>
                <div class="absolute top-1/2 -left-48 w-80 h-80 bg-emerald-500/5 dark:bg-emerald-500/10 blur-[100px] rounded-full"></div>
                <div class="absolute bottom-0 -right-40 w-96 h-96 bg-brand-500/5 dark:bg-brand-500/10 blur-[110px] rounded-full"></div>
                <div class="absolute inset-0 opacity-[0.025] dark:opacity-[0.04]" style="background-image: linear-gradient(to right, currentColor 1px, transparent 1px), linear-gradient(to bottom, currentColor 1px, transparent 1px); background-size: 32px 32px;"></div>
            </div>
        @else
            {{-- Brilho de Fundo --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-64 bg-emerald-500/10 blur-[100px] rounded-full pointer-events-none"></div>
        @endif

        {{-- TITULO / LOGO --}}
        <div class="flex justify-center mb-8 md:mb-10 relative z-10">
            <a href="/" wire:navigate class="group flex items-center gap-3 font-black text-2xl tracking-tighter">
                <span class="flex size-10 items-center justify-center rounded-xl bg-brand-600 shadow-lg shadow-brand-500/20 group-hover:scale-105 group-hover:shadow-brand-500/30 transition-all duration-200">
                    <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <span class="dark:text-white text-zinc-900 uppercase italic group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">Gestão de Custos</span>
            </a>
        </div>

        <div class="w-full {{ request()->routeIs('client.portal', 'supplier.dashboard', 'bank.dashboard', 'careers.apply', 'careers.portal') ? 'max-w-[1200px]' : 'max-w-[440px]' }} relative z-10 mx-auto">
            @if($isAuthPage)
                <div class="relative overflow-hidden rounded-[2rem] border border-white/70 dark:border-zinc-800/80 bg-white/90 dark:bg-zinc-900/90 shadow-2xl shadow-zinc-900/10 dark:shadow-black/30 backdrop-blur-xl p-6 sm:p-8">
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-brand-500/60 to-transparent"></div>
                    <div class="absolute -top-20 -right-20 size-40 rounded-full bg-brand-500/5 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -left-20 size-40 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none"></div>
                    <div class="relative">
                        {{ $slot }}
                    </div>
                </div>
            @else
                {{ $slot }}
            @endif

            <p class="mt-8 text-center text-[9px] text-zinc-500 font-black uppercase tracking-[0.3em] opacity-40">
                &copy; {{ date('Y') }} — Hub de Gestão Inteligente
            </p>
        </div>
    </div>
    @livewireScripts
    @fluxScripts
</body>
</html>