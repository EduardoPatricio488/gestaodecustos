<div class="min-h-[70vh] flex items-center justify-center py-8 sm:py-12">
    <div class="w-full max-w-5xl space-y-8">
        <div class="relative overflow-hidden rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl">
            <div class="absolute inset-0 pointer-events-none bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.16),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(59,130,246,0.10),transparent_35%)]"></div>
            <div class="relative p-8 sm:p-12">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em]">
                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                            Portal do Candidato
                        </div>
                        <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-zinc-900 dark:text-white">Olá, {{ auth()->user()->name }} 👋</h1>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 max-w-xl">O teu perfil de candidato está ativo. Consulta oportunidades e acompanha as tuas candidaturas num só lugar.</p>
                    </div>

                    <div class="shrink-0 size-20 rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-xl shadow-emerald-500/20">
                        <flux:icon name="briefcase" class="size-9" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-10">
                    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/50 p-5">
                        <flux:icon name="user-circle" class="size-5 text-emerald-500" />
                        <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-zinc-400">Perfil</p>
                        <p class="mt-1 text-sm font-bold text-zinc-900 dark:text-white">Perfil de candidato</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/50 p-5">
                        <flux:icon name="briefcase" class="size-5 text-blue-500" />
                        <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-zinc-400">Oportunidades</p>
                        <p class="mt-1 text-sm font-bold text-zinc-900 dark:text-white">Vagas disponíveis</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/50 p-5">
                        <flux:icon name="clipboard-document-check" class="size-5 text-violet-500" />
                        <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-zinc-400">Candidaturas</p>
                        <p class="mt-1 text-sm font-bold text-zinc-900 dark:text-white">Acompanha o teu progresso</p>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-700 p-6 text-center">
                    <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300">O teu espaço de carreira está pronto.</p>
                    <p class="mt-1 text-xs text-zinc-500">As funcionalidades de perfil, vagas e candidaturas serão apresentadas aqui.</p>
                </div>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="/" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 text-xs font-black uppercase tracking-wider hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all">
                        <flux:icon name="arrow-left" class="size-4" />
                        Início
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-black uppercase tracking-wider hover:bg-red-500/15 transition-all">
                            <flux:icon name="arrow-right-start-on-rectangle" class="size-4" />
                            Terminar sessão
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
