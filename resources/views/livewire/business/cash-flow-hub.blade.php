<div class="space-y-8 pb-24">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-5">
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                <flux:icon name="chart-bar" class="w-8 h-8 text-violet-600" />
            </div>
            <div>
                <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Fluxo de Caixa</h1>
                <p class="text-xs text-zinc-400 mt-1">Previsão de entradas e saídas · {{ $forecastDays }} dias</p>
            </div>
        </div>
        <flux:select wire:model.live="forecastDays" class="w-40">
            <option value="30">30 dias</option>
            <option value="60">60 dias</option>
            <option value="90">90 dias</option>
            <option value="180">180 dias</option>
        </flux:select>
    </div>

    @if($forecast['alert'])
        <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-2xl flex items-center gap-3">
            <flux:icon name="exclamation-triangle" class="size-5 text-red-500" />
            <p class="font-bold text-red-700 dark:text-red-300">{{ $forecast['alert'] }}</p>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-zinc-950 text-white p-6 rounded-3xl">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Saldo Atual</p>
            <p class="text-3xl font-black italic tabular-nums">{{ number_format($forecast['current_balance'], 0, ',', '.') }}€</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-3xl">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Previsão Final</p>
            <p class="text-3xl font-black italic tabular-nums {{ $forecast['forecast_end'] < 0 ? 'text-red-500' : 'text-emerald-500' }}">
                {{ number_format($forecast['forecast_end'], 0, ',', '.') }}€
            </p>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-3xl">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Mínimo Previsto</p>
            <p class="text-3xl font-black italic tabular-nums {{ $forecast['min_balance'] < 0 ? 'text-red-500' : 'text-orange-500' }}">
                {{ number_format($forecast['min_balance'], 0, ',', '.') }}€
            </p>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-3xl">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Máximo Previsto</p>
            <p class="text-3xl font-black italic tabular-nums text-blue-500">
                {{ number_format($forecast['max_balance'], 0, ',', '.') }}€
            </p>
        </div>
    </div>

    {{-- Timeline Chart --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6"
         x-data="{
            timeline: {{ json_encode($forecast['timeline']) }},
            init() {
                const ctx = this.$refs.chart.getContext('2d');
                const labels = this.timeline.map(t => t.date);
                const data = this.timeline.map(t => t.balance);
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Saldo Previsto (€)',
                            data,
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139,92,246,0.1)',
                            fill: true,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: false } }
                    }
                });
            }
         }">
        <h3 class="font-black dark:text-white uppercase tracking-widest text-sm mb-4">Evolução Prevista</h3>
        <canvas x-ref="chart" height="80"></canvas>
    </div>

    {{-- Eventos --}}
    @if(!empty($forecast['events']))
        <div>
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Eventos Previstos</h3>
            <div class="space-y-2">
                @foreach($forecast['events'] as $event)
                    <div class="flex justify-between items-center p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="size-2 rounded-full {{ $event['type'] === 'inflow' ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                            <span class="font-bold text-sm dark:text-white">{{ $event['label'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-black tabular-nums {{ $event['amount'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                {{ $event['amount'] >= 0 ? '+' : '' }}{{ number_format($event['amount'], 0, ',', '.') }}€
                            </span>
                            <p class="text-[10px] text-zinc-400">{{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
