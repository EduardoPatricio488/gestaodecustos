<div class="min-h-screen space-y-8 pb-16">
    <section class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-20 size-72 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="relative flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
            <div class="flex items-start gap-4">
                <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20"><flux:icon name="arrows-right-left" class="size-7" /></div>
                <div>
                    <div class="flex flex-wrap items-center gap-2"><h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white sm:text-3xl">Transferências Bancárias</h1><flux:badge color="blue">Movimentos</flux:badge></div>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">Centraliza os movimentos associados às tuas contas: transferências entre bancos, recebimentos, pagamentos e operações identificadas como MB WAY.</p>
                </div>
            </div>

        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900"><p class="text-xs font-bold uppercase tracking-wider text-zinc-400">Movimentos registados</p><p class="mt-2 text-3xl font-black text-zinc-950 dark:text-white">{{ number_format($movementCount, 0, ',', '.') }}</p><p class="mt-1 text-xs text-zinc-400">Movimentos associados a contas</p></div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-900/50 dark:bg-emerald-950/20"><p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Entradas</p><p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-400">{{ number_format($totalIn, 2, ',', '.') }} €</p><p class="mt-1 text-xs text-emerald-700/70">Receitas e recebimentos</p></div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50/70 p-5 dark:border-rose-900/50 dark:bg-rose-950/20"><p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400">Saídas</p><p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-400">{{ number_format($totalOut, 2, ',', '.') }} €</p><p class="mt-1 text-xs text-rose-700/70">Pagamentos e despesas</p></div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-5 dark:border-blue-900/50 dark:bg-blue-950/20"><p class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Entre contas</p><p class="mt-2 text-3xl font-black text-blue-700 dark:text-blue-400">{{ number_format($totalTransfers, 2, ',', '.') }} €</p><p class="mt-1 text-xs text-blue-700/70">Transferências internas</p></div>
    </section>

    <section class="space-y-4" x-data="{ filter: 'all', query: '' }">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-lg font-black text-zinc-900 dark:text-white">Histórico de movimentos</h2><p class="text-xs text-zinc-500">Consulta e filtra as operações registadas nas tuas contas.</p></div><div class="relative w-full sm:max-w-xs"><flux:icon name="magnifying-glass" class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400" /><input x-model="query" type="search" placeholder="Pesquisar movimento, conta..." class="w-full rounded-xl border border-zinc-200 bg-white py-2.5 pl-9 pr-3 text-sm text-zinc-800 outline-none focus:border-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"></div></div>
        <div class="flex flex-wrap gap-2">
            @foreach([['all','Todos'],['transfer','Transferências'],['mbway','MB WAY'],['income','Entradas'],['expense','Saídas']] as [$key,$label])
                <button type="button" @click="filter='{{ $key }}'" :class="filter==='{{ $key }}' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-900'" class="rounded-full border border-zinc-200 px-4 py-2 text-xs font-bold dark:border-zinc-700">{{ $label }}</button>
            @endforeach
        </div>
        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            @if($movements->isEmpty())
                <div class="px-6 py-16 text-center"><div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-500 dark:bg-blue-950/40"><flux:icon name="arrows-right-left" class="size-8" /></div><h3 class="mt-4 text-base font-black text-zinc-900 dark:text-white">Ainda não existem movimentos bancários</h3><p class="mx-auto mt-2 max-w-lg text-sm leading-relaxed text-zinc-500">Quando registares transferências entre contas, receitas ou despesas associadas a uma conta bancária, os movimentos aparecem aqui. Os movimentos MB WAY são identificados quando o método ou a descrição do registo o indicar.</p><a href="{{ route('hub.banco') }}" wire:navigate class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700"><flux:icon name="building-library" class="size-4" /> Abrir gestão bancária</a></div>
            @else
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($movements as $movement)
                        @php $filterType = $movement['kind'] === 'mbway' ? 'mbway' : ($movement['kind'] === 'transfer' ? 'transfer' : $movement['direction']); @endphp
                        <article x-show="(filter === 'all' || filter === '{{ $filterType }}') && @js(mb_strtolower($movement['label'].' '.$movement['account'].' '.$movement['counterpart'].' '.$movement['channel'])).includes(query.toLowerCase())" class="flex flex-col gap-3 px-4 py-4 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/40 sm:flex-row sm:items-center sm:px-5">
                            <div class="flex min-w-0 flex-1 items-center gap-3"><div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $movement['kind'] === 'mbway' ? 'bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-300' : ($movement['direction'] === 'in' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300' : ($movement['direction'] === 'out' ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-300' : 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300')) }}"><flux:icon name="{{ $movement['kind'] === 'mbway' ? 'device-phone-mobile' : ($movement['direction'] === 'in' ? 'arrow-down-left' : ($movement['direction'] === 'out' ? 'arrow-up-right' : 'arrows-right-left')) }}" class="size-5" /></div><div class="min-w-0"><p class="truncate text-sm font-bold text-zinc-900 dark:text-white">{{ $movement['label'] }}</p><p class="mt-0.5 truncate text-xs text-zinc-500">{{ $movement['account'] }} <span class="px-1 text-zinc-300">·</span> {{ $movement['counterpart'] }}</p></div></div>
                            <div class="flex items-center justify-between gap-4 sm:justify-end"><div class="text-left sm:text-right"><p class="text-[11px] font-bold text-zinc-500">{{ $movement['date']?->format('d/m/Y') ?? '—' }} · {{ $movement['channel'] }}</p><p class="mt-1 text-base font-black tabular-nums {{ $movement['direction'] === 'in' ? 'text-emerald-600' : ($movement['direction'] === 'out' ? 'text-rose-600' : 'text-blue-600') }}">{{ $movement['direction'] === 'in' ? '+' : ($movement['direction'] === 'out' ? '−' : '') }}{{ number_format($movement['amount'], 2, ',', '.') }} €</p></div><span class="rounded-full bg-zinc-100 px-2.5 py-1 text-[9px] font-black uppercase text-zinc-500 dark:bg-zinc-800">{{ $movement['status'] }}</span></div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
        <p class="text-[11px] leading-relaxed text-zinc-400">Esta página apresenta apenas movimentos registados no Finance Pro IA e associados a uma conta bancária. Não existe ligação automática ao banco nem importação automática de operações MB WAY.</p>
    </section>
</div>
