<div class="space-y-10 pb-20">
    {{-- 1. HEADER DE MISSÃO --}}
    <div class="relative">
        {{-- Glow decorativo --}}
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full transition-all duration-700 shadow-brand-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] shadow-2xl">
                        <flux:icon name="trophy" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Metas de Futuro</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Roadmap Financeiro</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Converta capital em <span class="text-brand-600 font-bold uppercase tracking-tighter">Conquistas e Património</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="goal-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20 bg-brand-600 text-white border-none">
                        Nova Meta
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </header>
    </div>

    {{-- 2. KPIs DE PERFORMANCE DE POUPANÇA --}}
    @php
        $totalTarget = $goals->sum('target_amount');
        $totalCurrent = $goals->sum('current_amount');
        $globalProgress = $totalTarget > 0 ? ($totalCurrent / $totalTarget) * 100 : 0;
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Acumulado --}}
        <div class="stat-card bg-zinc-950 text-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 group">
            <div class="absolute -right-10 -top-10 size-40 bg-brand-500/10 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-400 mb-2">Total em Carteira</p>
                <h3 class="text-5xl font-black tracking-tighter italic text-white">{{ number_format($totalCurrent, 0, ',', ' ') }} <small class="text-xl not-italic ml-1">€</small></h3>
                <p class="mt-4 text-[9px] font-bold text-zinc-500 uppercase tracking-widest italic">Capital alocado a objetivos</p>
            </div>
            <flux:icon name="wallet" class="absolute -right-4 -bottom-4 size-24 text-white/5 -rotate-12" />
        </div>

        {{-- Esforço Global --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Progresso Consolidado</p>
                <h3 class="text-4xl font-black dark:text-white tracking-tighter italic">{{ round($globalProgress) }}%</h3>
            </div>
            <div class="mt-4 h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500 shadow-[0_0_10px_#3b82f6]" style="width: {{ $globalProgress }}%"></div>
            </div>
        </div>

        {{-- Em Falta --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Gap para Conclusão</p>
                <h3 class="text-4xl font-black text-orange-500 tracking-tighter italic">{{ number_format($totalTarget - $totalCurrent, 0, ',', ' ') }} €</h3>
            </div>
            <p class="mt-4 text-[9px] font-black text-emerald-600 uppercase italic">Faltam {{ number_format($totalTarget, 0) }}€ para o pleno</p>
        </div>
    </div>

    {{-- 3. PIPELINE DE CONQUISTAS (GRELHA DE OBJETIVOS) --}}
    <div class="space-y-6">
        <div class="flex items-center gap-3 px-2">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="flag" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Roadmap de Metas Individuais</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($goals as $goal)
                @php
                    $perc = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
                    $isCompleted = $perc >= 100;
                @endphp

                <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:border-brand-500/40 transition-all duration-500 group relative overflow-hidden">

                    {{-- Badge de Percentagem Flutuante --}}
                    <div class="absolute top-6 right-6 flex items-center gap-2">
                        <flux:button
                            wire:click="delete({{ $goal->id }})"
                            wire:confirm="Confirmas a anulação desta meta?"
                            variant="ghost"
                            icon="trash"
                            size="sm"
                            class="opacity-0 group-hover:opacity-100 transition-opacity text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
                        />
                    </div>

                    {{-- Conteúdo do Objetivo --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-xl font-black dark:text-white uppercase tracking-tighter italic leading-none group-hover:text-brand-600 transition-colors">
                                {{ $goal->name }}
                            </h3>
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mt-2">
                                Status: {{ $isCompleted ? 'Concluído' : 'Em Progressão' }}
                            </p>
                        </div>

                        {{-- Ticker de Valores --}}
                        <div class="flex justify-between items-end">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Acumulado</span>
                                <span class="text-3xl font-black dark:text-white tracking-tighter italic">
                                    {{ number_format($goal->current_amount, 0, ',', ' ') }} <small class="text-xs">€</small>
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Meta</span>
                                <p class="text-sm font-bold dark:text-zinc-300 tracking-tight">
                                    {{ number_format($goal->target_amount, 0, ',', ' ') }} €
                                </p>
                            </div>
                        </div>

                        {{-- Barra de Progresso High-Tech --}}
                        <div class="relative pt-2">
                            <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-3 overflow-hidden p-0.5 border border-zinc-200/50 dark:border-zinc-700/50 shadow-inner">
                                <div class="h-full rounded-full transition-all duration-1000 shadow-[0_0_10px] {{ $isCompleted ? 'bg-emerald-500 shadow-emerald-500/40' : 'bg-brand-500 shadow-brand-500/40' }}"
                                     style="width: {{ min($perc, 100) }}%">
                                </div>
                            </div>

                            <div class="flex justify-between items-center mt-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-black {{ $isCompleted ? 'text-emerald-500' : 'text-brand-600' }} uppercase tracking-tighter">
                                        {{ round($perc) }}%
                                    </span>
                                    <div class="size-1 rounded-full bg-zinc-300"></div>
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase italic">Power Level</span>
                                </div>

                                <div class="px-3 py-1 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-100 dark:border-zinc-700">
                                    <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">
                                        {{ $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('M Y') : 'Life-Time' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($goals->isEmpty())
                <div class="lg:col-span-3 py-24 text-center glass-card rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                    <flux:icon name="sparkles" class="size-12 text-zinc-200 mx-auto mb-4" />
                    <p class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px]">Sem planos de futuro registados</p>
                </div>
            @endif
        </div>
    </div>

    {{-- 4. MODAL: ARQUITETAR NOVO OBJETIVO (DESIGN SaaS PRO) --}}
    <flux:modal name="goal-modal" position="center" class="md:w-[550px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Cabeçalho do Modal --}}
            <div class="text-center space-y-2">
                <div class="inline-flex p-3 bg-brand-500/10 rounded-2xl mb-2 text-brand-600">
                    <flux:icon name="flag" class="size-6" />
                </div>
                <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">Arquitetar Objetivo</flux:heading>
                <p class="text-xs text-zinc-400 font-medium italic">Defina os parâmetros financeiros da sua próxima conquista.</p>
            </div>

            {{-- Formulário --}}
            <div class="space-y-6">
                {{-- Nome --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Identificação da Meta</flux:label>
                    <flux:input
                        wire:model="name"
                        placeholder="Ex: Viagem ao Japão, Reserva de Emergência, Carro Novo..."
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl h-14 shadow-inner"
                    />
                </div>

                <div class="grid grid-cols-2 gap-6">
                    {{-- Montante Alvo --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Montante Alvo (€)</flux:label>
                        <flux:input
                            wire:model="target_amount"
                            type="number"
                            placeholder="0,00"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl h-14 shadow-inner text-brand-600"
                        />
                    </div>
                    {{-- Capital Atual --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Capital Inicial (€)</flux:label>
                        <flux:input
                            wire:model="current_amount"
                            type="number"
                            placeholder="0,00"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                </div>

                {{-- Data Limite --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Timeline Estimada (Opcional)</flux:label>
                    <flux:input
                        wire:model="deadline"
                        type="date"
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl h-14 shadow-inner"
                    />
                </div>
            </div>

            {{-- Acções --}}
            <div class="flex gap-4 pt-4">
                <flux:button
                    x-on:click="$dispatch('modal-close', { name: 'goal-modal' })"
                    variant="ghost"
                    class="flex-1 font-black uppercase text-[10px] text-zinc-400 hover:text-zinc-600 h-14 rounded-2xl"
                >
                    Descartar
                </flux:button>

                <flux:button
                    wire:click="save"
                    variant="primary"
                    class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl bg-brand-600 border-none text-white hover:scale-[1.02] transition-transform"
                >
                    Validar Objetivo
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Gestão de Roadmap de Conquistas
        </p>
    </footer>

</div> {{-- FECHO DA DIV RAIZ PRINCIPAL QUE ABRIU NA PARTE 1 --}}
