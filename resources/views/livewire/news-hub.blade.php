<div class="space-y-8 pb-20">
    <x-page-header title="Notícias 24h" description="Acompanha as últimas notícias de Portugal e do mundo, publicadas nas últimas 24 horas.">
        <x-slot:actions>
            <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate>Voltar à dashboard</flux:button>
            <flux:button wire:click="refreshNews" variant="primary" icon="arrow-path">Atualizar notícias</flux:button>
        </x-slot:actions>
    </x-page-header>

    <section class="relative overflow-hidden rounded-[2rem] border border-sky-300/20 bg-gradient-to-br from-zinc-950 via-slate-950 to-blue-950 p-6 sm:p-8 text-white shadow-xl shadow-sky-950/10">
        <div class="pointer-events-none absolute -right-16 -top-20 size-64 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl border border-sky-300/25 bg-sky-400/10 text-sky-300">
                    <flux:icon name="newspaper" class="size-7" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-black uppercase tracking-wider">Central de Notícias</h2>
                        <span class="rounded-md border border-emerald-300/25 bg-emerald-400/10 px-2 py-1 text-[9px] font-black tracking-widest text-emerald-300">ÚLTIMAS 24H</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-300">Informação recente, organizada num só lugar.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                <span class="size-2.5 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,0.8)]"></span>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Notícias disponíveis</p>
                    <p class="text-2xl font-black tabular-nums">{{ $totalNews }}</p>
                </div>
            </div>
        </div>
    </section>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="$set('regionFilter', 'ALL')" class="rounded-xl px-4 py-2.5 text-xs font-black uppercase tracking-wider transition {{ $regionFilter === 'ALL' ? 'bg-sky-600 text-white shadow-lg shadow-sky-600/20' : 'border border-zinc-200 bg-white text-zinc-500 hover:border-sky-300 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300' }}">Todas</button>
            <button type="button" wire:click="$set('regionFilter', 'PT')" class="rounded-xl px-4 py-2.5 text-xs font-black uppercase tracking-wider transition {{ $regionFilter === 'PT' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'border border-zinc-200 bg-white text-zinc-500 hover:border-emerald-300 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300' }}">Portugal</button>
            <button type="button" wire:click="$set('regionFilter', 'INT')" class="rounded-xl px-4 py-2.5 text-xs font-black uppercase tracking-wider transition {{ $regionFilter === 'INT' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'border border-zinc-200 bg-white text-zinc-500 hover:border-blue-300 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300' }}">Mundo</button>
        </div>

        <div class="relative w-full lg:max-w-sm">
            <flux:icon name="magnifying-glass" class="absolute left-4 top-1/2 z-10 size-4 -translate-y-1/2 text-zinc-400" />
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Pesquisar notícias ou fontes..." class="h-12 w-full rounded-2xl border border-zinc-200 bg-white pl-11 pr-4 text-sm font-semibold text-zinc-800 outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white" />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @forelse($articles as $article)
            <article wire:key="news-{{ $article['id'] }}" class="group flex h-full flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-sky-300 hover:shadow-xl hover:shadow-sky-950/5 dark:border-zinc-800 dark:bg-zinc-900">
                @if(!empty($article['image']))
                    <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="relative block h-48 overflow-hidden bg-zinc-100 dark:bg-zinc-800 sm:h-56">
                        <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" loading="lazy" referrerpolicy="no-referrer" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"></div>
                    </a>
                @endif

                <div class="flex flex-1 flex-col p-5">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-[9px] font-black uppercase tracking-widest {{ $article['region'] === 'PT' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-sky-50 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300' }}">
                            <span class="size-1.5 rounded-full {{ $article['region'] === 'PT' ? 'bg-emerald-500' : 'bg-sky-500' }}"></span>
                            {{ $article['region'] === 'PT' ? 'Portugal' : 'Mundo' }}
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wide text-zinc-400">{{ $article['published_label'] }}</span>
                    </div>

                    <h3 class="text-base font-extrabold leading-snug text-zinc-900 transition-colors group-hover:text-sky-700 dark:text-white dark:group-hover:text-sky-300 sm:text-lg">
                        {{ $article['title'] }}
                    </h3>

                    @if(!empty($article['summary']))
                        <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $article['summary'] }}</p>
                    @endif

                    <div class="mt-auto flex items-center justify-between gap-3 border-t border-zinc-100 pt-4 mt-5 dark:border-zinc-800">
                        <span class="truncate text-xs font-bold text-zinc-500 dark:text-zinc-400">{{ $article['source'] ?: 'Fonte noticiosa' }}</span>
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-sky-50 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-sky-700 transition hover:bg-sky-600 hover:text-white dark:bg-sky-400/10 dark:text-sky-300 dark:hover:bg-sky-500 dark:hover:text-white">
                            Ler notícia <flux:icon name="arrow-up-right" class="size-3.5" />
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-3xl border border-dashed border-zinc-300 bg-white px-6 py-16 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <flux:icon name="newspaper" class="mx-auto mb-4 size-10 text-zinc-300 dark:text-zinc-600" />
                <h3 class="text-lg font-black text-zinc-700 dark:text-zinc-200">Não foram encontradas notícias</h3>
                <p class="mt-2 text-sm text-zinc-500">Experimenta alterar a pesquisa ou atualizar a lista.</p>
            </div>
        @endforelse
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-400">
        <span>A lista apresenta notícias publicadas nas últimas 24 horas, quando disponíveis na fonte RSS.</span>
        <span>Fonte: Google News RSS</span>
    </div>
</div>
