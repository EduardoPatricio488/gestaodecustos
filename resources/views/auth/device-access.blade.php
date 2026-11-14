<!DOCTYPE html>
<html lang="pt">
<head>
    <style>
        @keyframes fp-launch-orbit { from { transform: rotate(0deg) translateX(30px) rotate(0deg); } to { transform: rotate(360deg) translateX(30px) rotate(-360deg); } }
        @keyframes fp-launch-rise { from { transform: translateY(18px) scale(.9); opacity:0; } to { transform: translateY(0) scale(1); opacity:1; } }
        @keyframes fp-launch-pulse { 0%,100% { opacity:.4; transform:scale(.94); } 50% { opacity:1; transform:scale(1.06); } }
        .fp-launch-orbit { animation:fp-launch-orbit 2.2s linear infinite; }
        .fp-launch-rise { animation:fp-launch-rise .7s cubic-bezier(.2,.8,.2,1) both; }
        .fp-launch-pulse { animation:fp-launch-pulse 1.4s ease-in-out infinite; }
        @media(prefers-reduced-motion:reduce){.fp-launch-orbit,.fp-launch-rise,.fp-launch-pulse{animation:none!important}}
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#070b13">
    <title>Escolher acesso — Finance Pro AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-zinc-950 text-white antialiased">
    <div id="fp-launch-splash" class="fixed inset-0 z-50 flex items-center justify-center overflow-hidden bg-[#070b13] transition-opacity duration-500">
        <div class="absolute inset-0" style="background:radial-gradient(circle at 50% 45%,rgba(16,185,129,.24),transparent 44%),radial-gradient(circle at 15% 85%,rgba(59,130,246,.15),transparent 35%)"></div>
        <div class="fp-launch-orbit absolute left-1/2 top-[calc(50%-30px)] size-3 rounded-full bg-emerald-300 shadow-[0_0_24px_8px_rgba(52,211,153,.55)]"></div>
        <div class="fp-launch-rise relative z-10 text-center">
            <div class="fp-launch-pulse mx-auto mb-7 flex size-24 items-center justify-center rounded-[2rem] border border-emerald-300/30 bg-emerald-400/10 shadow-[0_0_70px_rgba(16,185,129,.22)]">
                <svg viewBox="0 0 64 64" class="size-14" fill="none" aria-hidden="true"><path d="M10 46V32h10v14M27 46V20h10v26M44 46V10h10v36" stroke="#6ee7b7" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/><path d="m9 24 15-9 12 4 18-12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[.38em] text-emerald-300">FINANCE PRO AI</p>
            <h1 class="mt-3 text-3xl font-black">As tuas finanças.</h1>
            <p class="mt-2 text-sm text-zinc-400">Tudo em movimento. Tudo sob controlo.</p>
            <div class="mx-auto mt-8 h-1 w-36 overflow-hidden rounded-full bg-white/10"><div class="h-full w-1/2 animate-pulse rounded-full bg-emerald-300"></div></div>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const splash = document.getElementById('fp-launch-splash');
            window.setTimeout(() => {
                splash.classList.add('opacity-0');
                window.setTimeout(() => splash.remove(), 520);
            }, 1450);
        });
    </script>
    <main class="flex min-h-screen items-center justify-center px-5 py-10">
        <section class="w-full max-w-md">
            @php
                $hour = now()->hour;
                $greeting = $hour >= 5 && $hour < 12
                    ? 'Bom dia'
                    : ($hour >= 12 && $hour < 20 ? 'Boa tarde' : 'Boa noite');
            @endphp
            <div class="mb-8 text-center">
                <p class="mb-3 text-lg font-semibold text-zinc-200">{{ $greeting }}, {{ auth()->user()->name }}</p>
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
