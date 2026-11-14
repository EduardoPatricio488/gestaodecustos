<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#070b13">
    <title>Escolher acesso — Finance Pro AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-zinc-950 text-white antialiased">
    <main class="flex min-h-screen items-center justify-center px-5 py-10">
        <section class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-5 flex size-16 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-300">
                    <svg viewBox="0 0 24 24" class="size-9" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M4 18V9h4v9M10 18V5h4v13M16 18V2h4v16" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <p class="text-xs font-black uppercase tracking-[.3em] text-emerald-300">Finance Pro AI</p>
                <h1 class="mt-3 text-2xl font-black tracking-tight">Como queres entrar?</h1>
                <p class="mt-2 text-sm text-zinc-400">Escolhe a experiência que pretendes utilizar neste dispositivo.</p>
            </div>

            <div class="space-y-3">
                <form method="POST" action="{{ route('device.mode') }}">
                    @csrf
                    <input type="hidden" name="mode" value="mobile">
                    <button type="submit" class="flex w-full items-center gap-4 rounded-2xl border border-emerald-400/50 bg-emerald-500/10 p-5 text-left transition hover:bg-emerald-500/15 active:scale-[.98]">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-zinc-950">
                            <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="6" y="2" width="12" height="20" rx="2"/><path d="M10 18h4"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-base font-black">Apenas registos rápidos</span>
                            <span class="mt-1 block text-xs leading-relaxed text-zinc-400">Abre a versão mobile, simples e rápida para registar movimentos.</span>
                        </span>
                        <span class="text-2xl text-emerald-300">›</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('device.mode') }}">
                    @csrf
                    <input type="hidden" name="mode" value="full">
                    <button type="submit" class="flex w-full items-center gap-4 rounded-2xl border border-white/10 bg-white/[.04] p-5 text-left transition hover:bg-white/[.08] active:scale-[.98]">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-zinc-700 text-white">
                            <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-base font-black">Site completo</span>
                            <span class="mt-1 block text-xs leading-relaxed text-zinc-400">Acede ao dashboard e a todas as funcionalidades.</span>
                        </span>
                        <span class="text-2xl text-zinc-400">›</span>
                    </button>
                </form>
            </div>
        </section>
    </main>
    @livewireScripts
</body>
</html>
