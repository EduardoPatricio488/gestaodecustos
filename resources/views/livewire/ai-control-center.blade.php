<div class="space-y-8">
    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-2 text-brand-500">
            <flux:icon name="sparkles" class="size-5" />
            <span class="text-xs font-black uppercase tracking-[0.3em]">Finance Pro AI</span>
        </div>
        <h1 class="text-3xl font-black tracking-tight dark:text-white">AI Intelligence Center</h1>
        <p class="text-sm text-zinc-500">Insights, ações, memória e saúde financeira num único centro de controlo.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Saúde financeira</p>
            <div class="mt-3 flex items-end gap-3">
                <span class="text-5xl font-black dark:text-white">{{ $health['score'] }}</span>
                <span class="pb-2 text-sm font-bold text-zinc-500">/100</span>
            </div>
            <p class="mt-2 text-sm font-bold text-brand-500">{{ $health['label'] }}</p>
            @if(isset($health['savings_rate']))
                <p class="mt-3 text-xs text-zinc-500">Taxa de poupança: {{ $health['savings_rate'] }}%</p>
            @endif
            @if(isset($health['margin']))
                <p class="mt-3 text-xs text-zinc-500">Margem: {{ $health['margin'] }}%</p>
            @endif
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Insights ativos</p>
            <p class="mt-3 text-5xl font-black dark:text-white">{{ $insights->count() }}</p>
            <p class="mt-2 text-xs text-zinc-500">Com deduplicação e prioridade.</p>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Ações AI</p>
            <p class="mt-3 text-5xl font-black dark:text-white">{{ $actions->count() }}</p>
            <p class="mt-2 text-xs text-zinc-500">Registo de auditoria e confirmações.</p>
        </div>
    </div>

    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black dark:text-white">Insight Center</h2>
            <span class="text-xs font-bold text-zinc-400">{{ $insights->whereNull('read_at')->count() }} por ler</span>
        </div>

        @forelse($insights as $insight)
            <article class="rounded-3xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-zinc-100 px-2 py-1 text-[9px] font-black uppercase tracking-widest dark:bg-zinc-800">{{ $insight->priority }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $insight->category }}</span>
                        </div>
                        <h3 class="mt-3 text-lg font-black dark:text-white">{{ $insight->title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-500">{{ $insight->message }}</p>
                        <p class="mt-3 text-[10px] text-zinc-400">Confiança {{ $insight->confidence }}% · Score {{ $insight->score }}/100 · Fonte: {{ $insight->source }}</p>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        @if($insight->read_at === null)
                            <flux:button wire:click="markRead({{ $insight->id }})" size="sm" variant="ghost">Marcar lido</flux:button>
                        @endif
                        <flux:button wire:click="feedback({{ $insight->id }}, 'useful')" size="sm" variant="ghost">Útil</flux:button>
                        <flux:button wire:click="feedback({{ $insight->id }}, 'not_useful')" size="sm" variant="ghost">Não útil</flux:button>
                        <flux:button wire:click="feedback({{ $insight->id }}, 'more')" size="sm" variant="ghost">Mais disto</flux:button>
                        <flux:button wire:click="dismiss({{ $insight->id }})" size="sm" variant="ghost">Dispensar</flux:button>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
                <flux:icon name="sparkles" class="mx-auto size-8 text-zinc-400" />
                <p class="mt-3 font-bold dark:text-white">Ainda não existem insights ativos.</p>
                <p class="mt-1 text-sm text-zinc-500">O AI Observer irá criar insights quando existirem sinais suficientes.</p>
            </div>
        @endforelse
    </section>

    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black dark:text-white">Action Center</h2>
            <span class="text-xs text-zinc-400">Últimas ações AI</span>
        </div>
        <div class="overflow-hidden rounded-3xl border border-zinc-200 dark:border-zinc-800">
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($actions as $action)
                    <div class="flex flex-col gap-2 bg-white p-5 dark:bg-zinc-900 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-bold dark:text-white">{{ $action->tool_name }}</p>
                            <p class="text-xs text-zinc-500">{{ $action->status }} · {{ $action->action_type }}</p>
                        </div>
                        <span class="text-xs text-zinc-400">{{ $action->created_at?->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="bg-white p-8 text-center text-sm text-zinc-500 dark:bg-zinc-900">Ainda não existem ações AI.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-black dark:text-white">Memória da IA</h2>
                <p class="mt-1 text-sm text-zinc-500">A memória é separada do histórico e pode ser apagada pelo utilizador.</p>
            </div>
            @if($memories->isNotEmpty())
                <flux:button wire:click="clearMemories" wire:confirm="Queres apagar toda a memória guardada pela IA?" variant="danger" size="sm">Apagar memória</flux:button>
            @endif
        </div>

        <div class="mt-5 space-y-2">
            @forelse($memories as $memory)
                <div class="flex items-center justify-between gap-4 rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-950">
                    <div class="min-w-0">
                        <p class="text-xs font-black uppercase tracking-widest text-zinc-400">{{ $memory->type }} · {{ $memory->key }}</p>
                        <p class="mt-1 truncate text-sm font-medium dark:text-zinc-200">{{ $memory->value }}</p>
                    </div>
                    <flux:button wire:click="forgetMemory({{ $memory->id }})" variant="ghost" size="sm">Apagar</flux:button>
                </div>
            @empty
                <p class="py-4 text-sm text-zinc-500">A IA ainda não guardou memórias permanentes.</p>
            @endforelse
        </div>
    </section>
</div>
