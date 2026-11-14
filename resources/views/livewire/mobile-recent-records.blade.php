<div wire:poll.30s class="space-y-4">
    @if($this->records->isNotEmpty())
        <section class="rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-black text-zinc-900 dark:text-white">Registos recentes</h2>
                    <p class="mt-1 text-xs text-zinc-500">Os registos ficam disponíveis aqui durante 48 horas.</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Últimas 48h</span>
            </div>

            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach($this->records as $record)
                    <div wire:key="recent-record-{{ $record['id'] }}" class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                        <div @class([
                            'flex size-10 shrink-0 items-center justify-center rounded-2xl text-lg font-black',
                            'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-300' => $record['tone'] === 'rose',
                            'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' => $record['tone'] === 'emerald',
                            'bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-300' => $record['tone'] === 'blue',
                            'bg-violet-50 text-violet-600 dark:bg-violet-950 dark:text-violet-300' => $record['tone'] === 'violet',
                            'bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-300' => $record['tone'] === 'amber',
                        ])>{{ $record['icon'] }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-zinc-900 dark:text-white">{{ $record['title'] }}</p>
                            <p class="mt-0.5 text-xs text-zinc-500">{{ $record['type'] }}@if($record['detail']) · {{ $record['detail'] }}@endif</p>
                        </div>
                        <div class="shrink-0 text-right">
                            @if($record['amount'] !== null)
                                <p class="text-sm font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format((float) $record['amount'], 2, ',', '.') }} €</p>
                            @endif
                            <p class="mt-0.5 text-[10px] text-zinc-400">{{ $record['created_at']->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
