<div id="fp-account-loading" class="fixed inset-0 z-[999999] min-h-screen h-[100dvh] w-screen flex-col items-center justify-center overflow-hidden bg-[#070b13] px-6 text-center text-white" role="status" aria-live="polite" @if ($livewire ?? false) wire:loading.flex wire:target="login" @endif>
    <style>
        @keyframes fp-account-progress { 0% { transform: translateX(-110%); } 100% { transform: translateX(410%); } }
        @keyframes fp-account-spin { to { transform: rotate(360deg); } }
        #fp-account-loading { display:none; }
        #fp-account-loading.is-visible { display:flex; }
        #fp-account-loading .fp-spinner { animation:fp-account-spin 1.2s linear infinite; }
        #fp-account-loading .fp-progress { animation:fp-account-progress 1.2s ease-in-out infinite; }
        html[data-theme="light"] #fp-account-loading { background:#f4f7fb; color:#18181b; }
        html[data-theme="light"] #fp-account-loading .fp-loading-copy { color:#52525b; }
        @media(prefers-reduced-motion:reduce){#fp-account-loading *{animation:none!important}}
    </style>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_45%,rgba(16,185,129,.18),transparent_38%)]"></div>
    <div class="relative flex size-28 items-center justify-center">
        <div class="absolute inset-0 rounded-full border border-emerald-400/20"></div>
        <div class="fp-spinner absolute inset-2 rounded-full border-2 border-transparent border-t-emerald-300"></div>
        <div class="absolute inset-5 rounded-full border-2 border-transparent border-b-cyan-300" style="animation:fp-account-spin 1.6s linear infinite reverse"></div>
        <div class="flex size-12 items-center justify-center rounded-2xl bg-emerald-400/15 text-emerald-300 shadow-[0_0_35px_rgba(16,185,129,.2)]">
            <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 18V9h4v9M10 18V5h4v13M16 18V2h4v16" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
    </div>
    <p class="relative mt-8 text-xs font-black uppercase tracking-[.35em] text-emerald-300">Finance Pro AI</p>
    <h2 class="relative mt-3 text-2xl font-black tracking-tight">A preparar a tua conta</h2>
    <p class="fp-loading-copy relative mt-2 max-w-xs text-sm leading-relaxed text-zinc-400">Estamos a carregar os teus dados e a preparar o teu espaço de trabalho.</p>
    <div class="relative mt-8 h-1 w-48 overflow-hidden rounded-full bg-white/10">
        <div class="fp-progress h-full w-1/3 rounded-full bg-gradient-to-r from-emerald-400 to-cyan-300"></div>
    </div>
</div>
