<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Gestão de Custos') }}</title>

        <!-- Scripts -->
        <script>
            // Deteção de tema imediata para evitar flash branco
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
        <div class="min-h-screen flex flex-col justify-center items-center px-6 py-12 relative overflow-hidden">

            {{-- Decoração de Fundo (Glow) --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-64 bg-brand-500/10 blur-[100px] rounded-full pointer-events-none"></div>

            <div class="w-full max-w-[440px] relative z-10">
                {{-- Logo --}}
                <div class="flex justify-center mb-10">
                    <a href="/" wire:navigate class="flex items-center gap-3 font-black text-2xl tracking-tighter">
                        <span class="flex size-12 items-center justify-center rounded-2xl bg-brand-600 shadow-xl shadow-brand-500/20">
                            <svg class="size-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <span class="dark:text-white">Gestão de Custos</span>
                    </a>
                </div>

                {{-- Card Principal --}}
                <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl backdrop-blur-md">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <p class="mt-8 text-center text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em]">
                    &copy; {{ date('Y') }} — Finanças Inteligentes
                </p>
            </div>
        </div>

        @livewireScripts
        @fluxScripts
    </body>
</html>
