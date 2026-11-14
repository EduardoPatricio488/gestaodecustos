<div wire:poll.30s class="space-y-4">
    @if($this->records->isNotEmpty())
        <section class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-white via-white to-emerald-50/70 p-4 shadow-sm dark:border-emerald-900/50 dark:from-zinc-900 dark:via-zinc-900 dark:to-emerald-950/30 sm:p-5">
            <div class="pointer-events-none absolute -right-10 -top-12 size-36 rounded-full bg-emerald-200/20 blur-2xl dark:bg-emerald-400/10"></div>

            <div class="relative mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-xl text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300" aria-hidden="true">↻</span>
                    <div>
                        <h2 class="text-base font-black text-zinc-900 dark:text-white">Registos recentes</h2>
                        <p class="mt-0.5 text-xs text-zinc-500">Tudo o que adicionaste nas últimas 48 horas.</p>
                    </div>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                        <span class="size-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                        {{ $this->records->count() }} {{ $this->records->count() === 1 ? 'registo' : 'registos' }}
                    </span>
                    <span class="text-[9px] font-semibold uppercase tracking-wider text-zinc-400">Últimas 48h</span>
                </div>
            </div>

            <div class="relative space-y-2">
                @foreach($this->records as $record)
                    @php
                        $ageSeconds = max(0, now()->diffInSeconds($record['created_at'], false) * -1);
                        $remainingPercent = max(0, min(100, (1 - ($ageSeconds / 172800)) * 100));
                        $isNew = $ageSeconds < 3600;
                    @endphp
                    <a href="{{ $record['url'] }}" wire:key="recent-record-{{ $record['id'] }}" wire:navigate class="group flex items-center gap-3 rounded-2xl border border-zinc-100 bg-white/90 p-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md active:scale-[0.99] focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 dark:border-zinc-800 dark:bg-zinc-950/70 dark:hover:border-emerald-800">
                        <div @class([
                            'flex size-11 shrink-0 items-center justify-center rounded-2xl text-lg font-black transition-transform duration-200 group-hover:scale-105',
                            'bg-rose-50 text-rose-600 dark:bg-rose-950/70 dark:text-rose-300' => $record['tone'] === 'rose',
                            'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/70 dark:text-emerald-300' => $record['tone'] === 'emerald',
                            'bg-blue-50 text-blue-600 dark:bg-blue-950/70 dark:text-blue-300' => $record['tone'] === 'blue',
                            'bg-violet-50 text-violet-600 dark:bg-violet-950/70 dark:text-violet-300' => $record['tone'] === 'violet',
                            'bg-amber-50 text-amber-600 dark:bg-amber-950/70 dark:text-amber-300' => $record['tone'] === 'amber',
                        ])>{{ $record['icon'] }}</div>

                        <div class="min-w-0 flex-1">
                            <div class="flex min-w-0 items-center gap-2">
                                <p class="truncate text-sm font-extrabold text-zinc-900 dark:text-white">{{ $record['title'] }}</p>
                                @if($isNew)
                                    <span class="shrink-0 rounded-full bg-sky-50 px-1.5 py-0.5 text-[9px] font-black uppercase text-sky-700 dark:bg-sky-950 dark:text-sky-300">Novo</span>
                                @endif
                            </div>
                            <p class="mt-0.5 truncate text-[11px] text-zinc-500">{{ $record['type'] }}@if($record['detail']) · {{ $record['detail'] }}@endif</p>
                            <div class="mt-2 h-1 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div class="h-full rounded-full bg-emerald-400/80 transition-all duration-500" style="width: {{ $remainingPercent }}%"></div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col items-end gap-1">
                            @if($record['amount'] !== null)
                                <p class="text-sm font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format((float) $record['amount'], 2, ',', '.') }} €</p>
                            @endif
                            <p class="text-[10px] font-medium text-zinc-400">{{ $record['created_at']->diffForHumans() }}</p>
                            <span class="text-sm text-zinc-300 transition-transform group-hover:translate-x-0.5 group-hover:text-emerald-600 dark:text-zinc-600" aria-hidden="true">›</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <p class="mt-3 text-center text-[10px] text-zinc-400">A barra indica o tempo que falta até o registo sair desta lista.</p>
        </section>
    @endif
</div>
