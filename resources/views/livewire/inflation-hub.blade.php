<div class="space-y-10 pb-24">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="absolute inset-0 bg-orange-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="fire" class="w-10 h-10 text-orange-500" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Análise de Inflação</h1>
                    <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Poder de Compra</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Impacto real da inflação no salário, despesas e poupança</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        <div class="xl:col-span-4 space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm space-y-5">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Parâmetros</p>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Inflação Anual (%)</label>
                    <input wire:model.live="inflationRate" type="number" min="0" max="30" step="0.1"
                        class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-orange-500/30" />
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Crescimento Salarial (%)</label>
                    <input wire:model.live="salaryGrowthRate" type="number" min="-5" max="30" step="0.1"
                        class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-orange-500/30" />
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Horizonte (anos)</label>
                    <input wire:model.live="horizonYears" type="number" min="1" max="40" step="1"
                        class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-orange-500/30" />
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800 pt-5 space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Salário Mensal Atual (€)</label>
                    <input wire:model.live="monthlySalary" type="number" min="0" step="0.01"
                        class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-orange-500/30" />
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Despesas Mensais Atuais (€)</label>
                    <input wire:model.live="monthlyExpenses" type="number" min="0" step="0.01"
                        class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-orange-500/30" />
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Poupança Mensal (€)</label>
                    <input wire:model.live="monthlySavings" type="number" min="0" step="0.01"
                        class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-orange-500/30" />
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Reserva em Caixa (€)</label>
                    <input wire:model.live="cashReserve" type="number" min="0" step="0.01"
                        class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-orange-500/30" />
                </div>
            </div>
        </div>

        <div class="xl:col-span-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-zinc-950 text-white p-6 rounded-[2rem] border border-zinc-800">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Salário Futuro (Nominal)</p>
                    <p class="text-2xl font-black italic mt-1">{{ number_format($data['futureSalaryNominal'], 0, ',', ' ') }}€</p>
                </div>
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Salário Futuro (Real)</p>
                    <p class="text-2xl font-black italic mt-1 text-emerald-500">{{ number_format($data['futureSalaryReal'], 0, ',', ' ') }}€</p>
                </div>
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Custo de Vida Futuro</p>
                    <p class="text-2xl font-black italic mt-1 text-red-500">{{ number_format($data['futureMonthlyExpensesNominal'], 0, ',', ' ') }}€</p>
                </div>
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Gap Salarial</p>
                    <p class="text-2xl font-black italic mt-1 {{ $data['salaryGap'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $data['salaryGap'] >= 0 ? '+' : '' }}{{ number_format($data['salaryGap'], 0, ',', ' ') }}€
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm space-y-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Evolução: Salário vs Inflação</p>
                        <p class="text-xs text-zinc-500 mt-1">Comparação do salário nominal, salário real e custo de vida</p>
                    </div>
                    <p class="text-xs font-black {{ $data['purchasingPowerLossPct'] > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                        {{ $data['purchasingPowerLossPct'] > 0 ? 'Perda' : 'Ganho' }} de poder de compra: {{ number_format(abs($data['purchasingPowerLossPct']), 1, ',', '.') }}%
                    </p>
                </div>

                @php
                    $maxVal = collect($data['series'])->flatMap(fn($p) => [$p['salary_nominal'], $p['salary_real'], $p['expense_nominal']])->max() ?: 1;
                @endphp

                <div class="space-y-2">
                    @foreach($data['series'] as $point)
                        @php
                            $wNom = ($point['salary_nominal'] / $maxVal) * 100;
                            $wReal = ($point['salary_real'] / $maxVal) * 100;
                            $wExp = ($point['expense_nominal'] / $maxVal) * 100;
                        @endphp
                        <div class="p-3 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/30">
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">{{ $point['year'] }}</p>
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-20 text-[9px] font-black text-zinc-500 uppercase">Nominal</span>
                                    <div class="flex-1 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-500" style="width: {{ $wNom }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-black tabular-nums dark:text-white">{{ number_format($point['salary_nominal'], 0, ',', ' ') }}€</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-20 text-[9px] font-black text-zinc-500 uppercase">Real</span>
                                    <div class="flex-1 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500" style="width: {{ $wReal }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-black tabular-nums dark:text-white">{{ number_format($point['salary_real'], 0, ',', ' ') }}€</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-20 text-[9px] font-black text-zinc-500 uppercase">Despesas</span>
                                    <div class="flex-1 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500" style="width: {{ $wExp }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-black tabular-nums dark:text-white">{{ number_format($point['expense_nominal'], 0, ',', ' ') }}€</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Poupança Mensal Acumulada</p>
                    <p class="text-2xl font-black dark:text-white mt-1">{{ number_format($data['nominalSaved'], 0, ',', ' ') }}€</p>
                    <p class="text-xs text-zinc-500 mt-2">Valor real estimado: <span class="font-black text-amber-600">{{ number_format($data['realSaved'], 0, ',', ' ') }}€</span></p>
                </div>
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Reserva de Caixa e Inflação</p>
                    <p class="text-2xl font-black dark:text-white mt-1">{{ number_format($cashReserve, 0, ',', ' ') }}€</p>
                    <p class="text-xs text-zinc-500 mt-2">Perda de poder de compra: <span class="font-black text-red-500">-{{ number_format($data['cashReserveLoss'], 0, ',', ' ') }}€</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
