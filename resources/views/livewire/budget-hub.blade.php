<div class="space-y-8 pb-24">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 px-1">
        <div class="flex items-center gap-5">
            <div class="relative">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                    <flux:icon name="calculator" class="w-8 h-8 text-emerald-600" />
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Orçamento Mensal</h1>
                <p class="text-xs text-zinc-400 mt-1">Quanto podes gastar este mês · {{ $overview['month'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button wire:click="previousMonth" variant="ghost" icon="chevron-left" />
            <span class="text-sm font-black dark:text-white px-3">{{ $overview['month'] }}</span>
            <flux:button wire:click="nextMonth" variant="ghost" icon="chevron-right" />
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 flex-wrap">
        @foreach(['overview' => 'Visão Geral', 'categories' => 'Por Categoria', 'challenges' => 'Desafios 30 Dias', 'score' => 'Finance Score'] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all
                    {{ $activeTab === $tab ? 'bg-emerald-600 text-white shadow-lg' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if($activeTab === 'overview')
        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="col-span-2 bg-zinc-950 text-white p-7 rounded-3xl shadow-2xl relative overflow-hidden border border-zinc-800">
                <p class="text-[9px] font-black uppercase tracking-[0.35em] text-emerald-400 mb-2">Quanto Posso Gastar</p>
                <h3 class="text-5xl font-black tracking-tighter italic tabular-nums">
                    {{ number_format($overview['remaining'], 0, ',', ' ') }}<small class="text-xl not-italic">€</small>
                </h3>
                <div class="mt-4 flex items-center gap-3">
                    <div class="flex-1 h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all {{ $overview['percentage'] >= 100 ? 'bg-red-500' : ($overview['percentage'] >= 80 ? 'bg-orange-500' : 'bg-emerald-500') }}"
                             style="width:{{ min(100, $overview['percentage']) }}%"></div>
                    </div>
                    <span class="text-xs font-black tabular-nums">{{ $overview['percentage'] }}%</span>
                </div>
                <p class="mt-2 text-[10px] text-zinc-500">
                    {{ number_format($overview['total_spent'], 0, ',', '.') }}€ gastos de {{ number_format($overview['total_budget'], 0, ',', '.') }}€ orçamento
                </p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Seguro por Dia</p>
                <p class="text-3xl font-black text-emerald-500 tracking-tighter italic tabular-nums">
                    {{ number_format($overview['safe_to_spend_daily'], 0, ',', '.') }}€
                </p>
                <p class="text-[9px] text-zinc-400 mt-2 font-bold">{{ $overview['days_remaining'] }} dias restantes</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Projeção Fim Mês</p>
                <p class="text-3xl font-black {{ $overview['projected_spend'] > $overview['total_budget'] ? 'text-red-500' : 'text-blue-500' }} tracking-tighter italic tabular-nums">
                    {{ number_format($overview['projected_spend'], 0, ',', '.') }}€
                </p>
                <p class="text-[9px] text-zinc-400 mt-2 font-bold">Média {{ number_format($overview['daily_avg'], 0, ',', '.') }}€/dia</p>
            </div>
        </div>

        {{-- Alertas --}}
        @if($alerts->isNotEmpty())
            <div class="space-y-3">
                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400">Alertas</h3>
                @foreach($alerts as $alert)
                    <div class="flex items-center gap-4 p-4 rounded-2xl border
                        {{ $alert['type'] === 'danger' ? 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800' : 'bg-orange-50 dark:bg-orange-950/30 border-orange-200 dark:border-orange-800' }}">
                        <flux:icon :name="$alert['icon']" class="size-5 {{ $alert['type'] === 'danger' ? 'text-red-500' : 'text-orange-500' }}" />
                        <p class="text-sm font-bold {{ $alert['type'] === 'danger' ? 'text-red-700 dark:text-red-300' : 'text-orange-700 dark:text-orange-300' }}">
                            {{ $alert['message'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    @if($activeTab === 'categories')
        <div class="grid gap-4">
            @forelse($categories->where('budget', '>', 0) as $cat)
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center gap-3">
                            <div class="size-3 rounded-full" style="background:{{ $cat['color'] }}"></div>
                            <span class="font-black dark:text-white">{{ $cat['name'] }}</span>
                            @if($cat['alert_level'] === 'danger')
                                <span class="text-[9px] font-black bg-red-100 text-red-600 px-2 py-0.5 rounded-full uppercase">Ultrapassado</span>
                            @elseif($cat['alert_level'] === 'warning')
                                <span class="text-[9px] font-black bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full uppercase">80%+</span>
                            @endif
                        </div>
                        <span class="text-sm font-black tabular-nums dark:text-white">
                            {{ number_format($cat['spent'], 0, ',', '.') }} / {{ number_format($cat['budget'], 0, ',', '.') }}€
                        </span>
                    </div>
                    <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all"
                             style="width:{{ min(100, $cat['percentage']) }}%; background:{{ $cat['alert_level'] === 'danger' ? '#ef4444' : ($cat['alert_level'] === 'warning' ? '#f97316' : $cat['color']) }}"></div>
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-2 font-bold">
                        Restam {{ number_format($cat['remaining'], 0, ',', '.') }}€ · {{ $cat['percentage'] }}% utilizado
                    </p>
                </div>
            @empty
                <div class="text-center py-12 text-zinc-400">
                    <p class="font-bold">Nenhuma categoria com orçamento definido.</p>
                    <a href="{{ route('categories') }}" class="text-emerald-500 text-sm mt-2 inline-block">Definir orçamentos →</a>
                </div>
            @endforelse
        </div>
    @endif

    @if($activeTab === 'challenges')
        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 space-y-4">
                <h3 class="font-black dark:text-white uppercase tracking-widest text-sm">Novo Desafio</h3>
                <flux:input wire:model="challengeTitle" label="Título" placeholder="Ex: Menos de 50€ em Entretenimento" />
                <flux:select wire:model="challengeCategoryId" label="Categoria (opcional)">
                    <option value="">Todas as categorias</option>
                    @foreach($categoryOptions as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="challengeTarget" type="number" label="Limite (€)" placeholder="50.00" />
                <flux:input wire:model="challengeDays" type="number" label="Duração (dias)" min="7" max="90" />
                <flux:button wire:click="createChallenge" variant="primary" class="w-full rounded-2xl font-black">Criar Desafio</flux:button>
            </div>

            <div class="space-y-4">
                @forelse($challenges as $challenge)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-black dark:text-white">{{ $challenge->title }}</p>
                                <p class="text-[10px] text-zinc-400 mt-1">
                                    {{ $challenge->start_date->format('d/m') }} → {{ $challenge->end_date->format('d/m') }}
                                </p>
                            </div>
                            <span class="text-[9px] font-black uppercase px-2 py-1 rounded-full
                                {{ $challenge->status === 'completed' ? 'bg-emerald-100 text-emerald-600' : ($challenge->status === 'failed' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600') }}">
                                {{ $challenge->status }}
                            </span>
                        </div>
                        <div class="mt-3 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width:{{ $challenge->progress_pct }}%"></div>
                        </div>
                        <p class="text-[10px] text-zinc-400 mt-2 font-bold">
                            {{ number_format($challenge->spent, 0, ',', '.') }}€ / {{ number_format($challenge->target_amount, 0, ',', '.') }}€
                        </p>
                    </div>
                @empty
                    <p class="text-center text-zinc-400 py-8">Nenhum desafio ativo. Cria o teu primeiro!</p>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'score')
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 bg-zinc-950 text-white p-8 rounded-3xl text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-blue-500/10"></div>
                <p class="relative text-[9px] font-black uppercase tracking-[0.35em] text-emerald-400 mb-4">Finance Score</p>
                <div class="relative text-8xl font-black italic tabular-nums">{{ $score['score'] }}</div>
                <p class="relative text-lg font-black text-emerald-400 mt-2">{{ $scoreGrade }}</p>
            </div>
            <div class="lg:col-span-2 space-y-3">
                @foreach($score['breakdown'] as $key => $item)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 flex items-center gap-4">
                        <div class="flex-1">
                            <p class="font-black dark:text-white text-sm">{{ $item['label'] }}</p>
                            <p class="text-[10px] text-zinc-400">Peso: {{ $item['weight'] }}</p>
                        </div>
                        <div class="w-32 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width:{{ $item['score'] }}%"></div>
                        </div>
                        <span class="text-sm font-black tabular-nums w-8 text-right dark:text-white">{{ $item['score'] }}</span>
                    </div>
                @endforeach
                @if(!empty($score['tips']))
                    <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-2xl p-4 mt-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-blue-500 mb-2">Dicas da IA</p>
                        @foreach($score['tips'] as $tip)
                            <p class="text-sm text-blue-700 dark:text-blue-300 font-bold">• {{ $tip }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
