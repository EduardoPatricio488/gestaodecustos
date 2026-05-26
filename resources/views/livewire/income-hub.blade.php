<div class="space-y-10 pb-20">
    {{-- 1. HEADER DE GESTÃO DE RECEITAS --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full group-hover:bg-emerald-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-emerald-500/10">
                    <flux:icon name="arrow-trending-up" class="w-10 h-10 text-emerald-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Gestão de Receitas</h1>
                    <flux:badge variant="success" class="bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Cash-In</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Controlo estratégico de fluxos e rendimentos do grupo</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:modal.trigger name="add-fixed-income">
                <flux:button variant="ghost" icon="calendar-days" class="rounded-2xl font-bold text-zinc-500 hover:text-emerald-600">
                    Configurar Salário
                </flux:button>
            </flux:modal.trigger>

            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

            <flux:modal.trigger name="add-extra-income">
                <flux:button variant="primary" icon="plus" class="bg-emerald-600 hover:bg-emerald-700 rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                    Receita Extra
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    {{-- 2. CARD DE RESUMO MENSAL (CENTRO DE ENTRADAS) --}}
    <div class="relative overflow-hidden bg-emerald-600 p-10 rounded-[2.5rem] shadow-2xl group border-none">
        {{-- Efeito decorativo de rede financeira --}}
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 50 Q 25 40 50 50 T 100 50" fill="none" stroke="white" stroke-width="0.5" />
                <path d="M0 30 Q 25 20 50 30 T 100 30" fill="none" stroke="white" stroke-width="0.5" />
            </svg>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-8">
            <div class="text-center lg:text-left space-y-2">
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-100 opacity-80">Total Projetado para {{ now()->translatedFormat('F') }}</h3>
                <div class="flex items-baseline justify-center lg:justify-start gap-4">
                    <span class="text-7xl font-black text-white tracking-tighter italic">
                        {{ number_format($totalMonthly, 2, ',', ' ') }} <span class="text-3xl">€</span>
                    </span>
                </div>
                <div class="flex items-center justify-center lg:justify-start gap-3 mt-4">
                    <div class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/20">
                        <span class="text-[10px] font-black text-white uppercase tracking-widest">Saldo Realizado: {{ round(($totalMonthly > 0 ? (collect($extraIncomes)->sum('amount') / $totalMonthly) * 100 : 0)) }}%</span>
                    </div>
                </div>
            </div>

            <div class="hidden lg:block">
                <flux:icon name="banknotes" class="size-32 text-white/10 rotate-12" />
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- COLUNA 1: RENDIMENTOS FIXOS (ESTILO RECORRÊNCIA) --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="calendar-days" variant="outline" class="size-4" />
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Rendimentos Fixos</h2>
                </div>
                <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-2 py-0.5 border-none">
                    {{ $fixedIncomes->count() }} Ativos
                </flux:badge>
            </div>

            <div class="space-y-4">
                @forelse($fixedIncomes as $fixed)
                    <div class="glass-card p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] flex justify-between items-center group transition-all hover:border-emerald-500/40 shadow-sm">
                        <div class="flex items-center gap-5">
                            {{-- Tile de Dia do Mês --}}
                            <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 shadow-inner">
                                <span class="text-[8px] font-black uppercase text-zinc-400 leading-none mb-1">DIA</span>
                                <span class="text-xl font-black text-emerald-600 leading-none">{{ sprintf('%02d', $fixed->day_of_month) }}</span>
                            </div>

                            <div>
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $fixed->description }}</p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest">Recorrência Mensal</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <span class="text-xl font-black text-emerald-600 tracking-tighter italic">
                                {{ number_format($fixed->amount, 2, ',', ' ') }} €
                            </span>
                            <flux:button wire:click="deleteFixed({{ $fixed->id }})" wire:confirm="Eliminar rendimento fixo?" variant="ghost" icon="trash" size="xs" color="red" class="opacity-0 group-hover:opacity-100 transition-opacity" />
                        </div>
                    </div>
                @empty
                    <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                        <flux:icon name="clock" class="size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4" />
                        <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest">Sem salários configurados</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- COLUNA 2: ENTRADAS EXTRAS (ESTILO LEDGER) --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="sparkles" variant="outline" class="size-4" />
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Entradas Extras ({{ now()->translatedFormat('M') }})</h2>
                </div>
                <flux:badge variant="success" class="bg-emerald-500/10 text-emerald-600 text-[10px] font-black uppercase px-2 py-0.5 border-none">
                    {{ $extraIncomes->count() }} Lançamentos
                </flux:badge>
            </div>

            <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                            <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                                <th class="p-5">Data</th>
                                <th class="p-5">Descrição do Ganho</th>
                                <th class="p-5 text-right px-8">Valor</th>
                                <th class="p-5 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            @forelse($extraIncomes as $extra)
                                <tr class="hover:bg-emerald-50/30 dark:hover:bg-emerald-500/5 transition-all group">
                                    <td class="p-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black dark:text-white leading-none">{{ \Carbon\Carbon::parse($extra->received_at)->format('d') }}</span>
                                            <span class="text-[9px] font-black text-zinc-400 uppercase mt-1">{{ \Carbon\Carbon::parse($extra->received_at)->translatedFormat('M') }}</span>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <p class="text-sm font-bold dark:text-white uppercase tracking-tight">{{ $extra->description }}</p>
                                        <p class="text-[10px] text-zinc-500 font-medium italic">Receita Pontual</p>
                                    </td>
                                    <td class="p-5 text-right">
                                        <span class="text-lg font-black text-emerald-600 tracking-tighter">
                                            +{{ number_format($extra->amount, 2, ',', ' ') }} €
                                        </span>
                                    </td>
                                    <td class="p-5 text-right pr-6">
                                        <flux:button wire:click="deleteExtra({{ $extra->id }})" variant="ghost" icon="trash" size="xs" color="red" class="opacity-0 group-hover:opacity-100 transition-opacity" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-20 text-center">
                                        <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest">Sem ganhos extra este mês</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. MODAL: CONFIGURAR SALÁRIO / RENDIMENTO FIXO --}}
    <flux:modal name="add-fixed-income" position="center" class="md:w-[500px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">
            <div class="absolute top-6 right-6">
                <flux:modal.close><flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" /></flux:modal.close>
            </div>

            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-lg shadow-emerald-500/20">
                    <flux:icon name="calendar-days" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Configurar Salário</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Define um rendimento que se repete todos os meses.</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Identificação do Rendimento</flux:label>
                    <flux:input wire:model="recDescription" placeholder="Ex: Salário Mensal - Empresa X" class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Valor Líquido (€)</flux:label>
                        <flux:input
                            wire:model="recAmount"
                            type="number"
                            step="0.01"
                            class="font-black text-2xl text-emerald-600 !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                            placeholder="0,00"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Dia de Recebimento</flux:label>
                        <flux:input
                            wire:model="recDay"
                            type="number"
                            min="1"
                            max="31"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner text-center"
                        />
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <flux:modal.close><flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Cancelar</flux:button></flux:modal.close>
                <flux:button wire:click="saveFixed" variant="primary" class="flex-[2] bg-emerald-600 hover:bg-emerald-700 border-none font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20 h-14 rounded-2xl">
                    Guardar Salário
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- 4. MODAL: NOVA RECEITA EXTRA --}}
    <flux:modal name="add-extra-income" position="center" class="md:w-[500px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">
            <div class="absolute top-6 right-6">
                <flux:modal.close><flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" /></flux:modal.close>
            </div>

            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-lg shadow-emerald-500/20">
                    <flux:icon name="sparkles" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Receita Extra</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Regista uma entrada de dinheiro pontual no grupo.</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Origem do Ganho</flux:label>
                    <flux:input wire:model="description" placeholder="Ex: Venda de equipamento, Freelance..." class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Valor Recebido (€)</flux:label>
                        <flux:input
                            wire:model="amount"
                            type="number"
                            step="0.01"
                            class="font-black text-2xl text-emerald-600 !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                            placeholder="0,00"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Data de Entrada</flux:label>
                        <flux:input wire:model="received_at" type="date" class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <flux:modal.close><flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Cancelar</flux:button></flux:modal.close>
                <flux:button wire:click="saveExtra" variant="primary" class="flex-[2] bg-emerald-600 hover:bg-emerald-700 border-none font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20 h-14 rounded-2xl">
                    Confirmar Ganho
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Receitas e Fluxo
        </p>
    </footer>
</div>
