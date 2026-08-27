<div class="space-y-8 pb-24">
    <div class="flex items-center gap-5 px-1">
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
            <flux:icon name="beaker" class="w-8 h-8 text-cyan-600" />
        </div>
        <div>
            <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Simulação Familiar</h1>
            <p class="text-xs text-zinc-400 mt-1">Cenários "e se": salário, renda, inflação, filho e empréstimo</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 space-y-4">
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400">Parâmetros</h3>

            <flux:input wire:model.live="currentSalary" type="number" step="0.01" label="Salário Mensal Atual" />
            <flux:input wire:model.live="currentRent" type="number" step="0.01" label="Renda Mensal Atual" />
            <flux:input wire:model.live="otherExpenses" type="number" step="0.01" label="Outras Despesas Mensais" />

            <div class="border-t border-zinc-100 dark:border-zinc-800 pt-4 space-y-3">
                <flux:input wire:model.live="salaryChangePct" type="number" step="0.1" label="Alteração Salarial (%)" />
                <flux:input wire:model.live="inflationRate" type="number" step="0.1" label="Inflação (%)" />
                <flux:input wire:model.live="newChildren" type="number" min="0" label="Novo Filho (quantidade)" />
                <flux:input wire:model.live="costPerChild" type="number" step="0.01" label="Custo mensal por filho" />
            </div>

            <div class="border-t border-zinc-100 dark:border-zinc-800 pt-4 space-y-3">
                <flux:input wire:model.live="loanAmount" type="number" step="0.01" label="Montante Empréstimo" />
                <flux:input wire:model.live="loanAnnualRate" type="number" step="0.1" label="Juro anual (%)" />
                <flux:input wire:model.live="loanMonths" type="number" min="1" label="Prazo (meses)" />
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-zinc-950 text-white p-6 rounded-3xl border border-zinc-800">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Saldo Atual</p>
                    <p class="text-3xl font-black italic mt-1">{{ number_format($result['net_base'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                    <p class="text-[10px] text-zinc-500 mt-2">Taxa: {{ number_format($result['rate_base'], 1, ',', '.') }}%</p>
                </div>
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Saldo no Cenário</p>
                    <p class="text-3xl font-black italic mt-1 {{ $result['net_scenario'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ number_format($result['net_scenario'], 2, ',', '.') }} {{ $workspaceCurrency }}
                    </p>
                    <p class="text-[10px] text-zinc-400 mt-2">Taxa: {{ number_format($result['rate_scenario'], 1, ',', '.') }}%</p>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400">Impacto do Cenário</h3>
                <div class="grid md:grid-cols-3 gap-3">
                    <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40">
                        <p class="text-[9px] font-black uppercase text-zinc-400">Custo Novo Filho</p>
                        <p class="text-lg font-black dark:text-white">{{ number_format($result['children_cost'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40">
                        <p class="text-[9px] font-black uppercase text-zinc-400">Prestação Empréstimo</p>
                        <p class="text-lg font-black dark:text-white">{{ number_format($result['loan_payment'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                    </div>
                    <div class="p-4 rounded-2xl {{ $result['delta_net'] >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/30' : 'bg-red-50 dark:bg-red-950/30' }}">
                        <p class="text-[9px] font-black uppercase text-zinc-400">Variação de Saldo</p>
                        <p class="text-lg font-black {{ $result['delta_net'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $result['delta_net'] >= 0 ? '+' : '' }}{{ number_format($result['delta_net'], 2, ',', '.') }} {{ $workspaceCurrency }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6">
                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Projeção 12 Meses (Reserva)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($result['series'] as $point)
                        <div class="p-3 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-800/30">
                            <p class="text-[9px] font-black uppercase text-zinc-400">{{ $point['month'] }}</p>
                            <p class="text-xs font-black {{ $point['reserve'] >= 0 ? 'text-emerald-600' : 'text-red-500' }} mt-1">
                                {{ number_format($point['reserve'], 0, ',', '.') }} {{ $workspaceCurrency }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
