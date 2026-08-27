<div class="space-y-8">
    {{-- HEADER --}}
    <x-page-header title="Transparência do Grupo" description="Análise detalhada de performance e atividade: {{ $workspaceName }}">
        <x-slot:actions>
            <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate>Voltar</flux:button>
        </x-slot:actions>
    </x-page-header>

    {{-- SUÍTE FAMILIAR: 15 FUNCIONALIDADES --}}
    <div class="space-y-4">
        <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 px-2">Suíte Familiar Inteligente (15 funcionalidades)</h2>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">1. Cofrinhos por objetivo</p>
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">{{ $goalPockets->count() }} metas</p>
                </div>
                <div class="space-y-3">
                    @forelse($goalPockets->take(4) as $goal)
                        <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-black dark:text-white">{{ $goal['name'] }}</p>
                                <p class="text-xs font-black text-brand-600">{{ number_format($goal['progress'], 1, ',', '.') }}%</p>
                            </div>
                            <div class="h-2 rounded-full bg-zinc-200 dark:bg-zinc-700 overflow-hidden">
                                <div class="h-full bg-emerald-500" style="width: {{ min(100, $goal['progress']) }}%"></div>
                            </div>
                            <p class="text-[10px] text-zinc-500 mt-2">Falta {{ number_format($goal['remaining'], 2, ',', '.') }} {{ $workspaceCurrency }} · necessário/mês {{ number_format($goal['required_monthly'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                            <div class="mt-3 grid grid-cols-2 gap-2 text-[10px]">
                                <div class="rounded-lg bg-white dark:bg-zinc-900/50 p-2">
                                    <p class="font-black uppercase text-zinc-400">Previsao</p>
                                    <p class="font-black {{ $goal['is_late_by_forecast'] ? 'text-red-500' : 'text-emerald-600' }}">
                                        {{ $goal['predicted_completion'] ? $goal['predicted_completion']->format('m/Y') : 'Sem ritmo' }}
                                    </p>
                                </div>
                                <div class="rounded-lg bg-white dark:bg-zinc-900/50 p-2">
                                    <p class="font-black uppercase text-zinc-400">Ritmo 90d</p>
                                    <p class="font-black dark:text-white">{{ number_format($goal['monthly_pace'], 2, ',', '.') }} {{ $workspaceCurrency }}/mes</p>
                                </div>
                            </div>
                            @if($goal['alert'])
                                <p class="mt-2 text-[10px] font-black uppercase {{ $goal['status'] === 'risco' ? 'text-red-500' : 'text-emerald-600' }}">{{ $goal['alert'] }}</p>
                            @endif
                            @if($goal['contributors']->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach($goal['contributors']->take(3) as $contributor)
                                        <span class="px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-[9px] font-black text-emerald-700 dark:text-emerald-300">
                                            {{ \Illuminate\Support\Str::limit($contributor['name'], 12) }} - {{ number_format($contributor['amount'], 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500">Sem metas registradas.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-zinc-950 text-white rounded-[2rem] p-6 border border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">2. Calendário financeiro</p>
                <p class="text-3xl font-black mt-3">{{ $financialCalendar->count() }}</p>
                <p class="text-[10px] text-zinc-400 mt-1">eventos até o fim do mês</p>
                <p class="text-[10px] text-amber-400 mt-4 font-bold">Dias com risco de caixa negativo: {{ count($riskDays) }}</p>
                <div class="mt-3 space-y-2">
                    @foreach($financialCalendar->take(4) as $event)
                        <div class="text-[10px] p-2 rounded-lg {{ in_array($event['type'], ['income', 'salary'], true) ? 'bg-emerald-500/10 text-emerald-200' : 'bg-white/5' }} flex justify-between gap-2">
                            <span>{{ $event['date']->format('d/m') }} · {{ $event['label'] }}</span>
                            <span class="font-black">{{ $event['amount'] > 0 ? (in_array($event['type'], ['income', 'salary'], true) ? '+' : '-') . number_format($event['amount'], 2, ',', '.') : '-' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 rounded-[1.5rem] p-5 border border-zinc-200 dark:border-zinc-800">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-black uppercase text-zinc-400">3. Semana economica</p>
                        <p class="text-2xl font-black text-emerald-600 mt-2">{{ $weekMedals }}</p>
                        <p class="text-[10px] text-zinc-500">medalhas de categoria</p>
                    </div>
                    <button wire:click="createEconomicWeekChallenge" class="px-3 py-2 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-[9px] font-black uppercase">
                        Criar
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[1.5rem] p-5 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase text-zinc-400">4. Renegociação</p>
                <p class="text-2xl font-black text-brand-600 mt-2">{{ number_format($renegotiationLeads->sum('annual_saving'), 0, ',', '.') }} {{ $workspaceCurrency }}</p>
                <p class="text-[10px] text-zinc-500">economia anual potencial</p>
                <div class="mt-3 space-y-1.5">
                    @foreach($renegotiationLeads->take(2) as $lead)
                        <div class="flex justify-between gap-2 text-[9px] text-zinc-500">
                            <span class="truncate">{{ $lead['service'] }}</span>
                            <span class="font-black text-brand-600">{{ number_format($lead['annual_saving'], 0, ',', '.') }}/ano</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[1.5rem] p-5 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase text-zinc-400">5. Score previsibilidade</p>
                <p class="text-2xl font-black mt-2 {{ $predictabilityScore >= 65 ? 'text-emerald-600' : 'text-amber-500' }}">{{ number_format($predictabilityScore, 1, ',', '.') }}</p>
                <p class="text-[10px] text-zinc-500">estabilidade média das categorias</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[1.5rem] p-5 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase text-zinc-400">6. Assinaturas duplicadas</p>
                <p class="text-2xl font-black text-red-500 mt-2">{{ $duplicateSubscriptions->count() }}</p>
                <p class="text-[10px] text-zinc-500">serviços repetidos no grupo</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-4">7. Regras de autopoupança</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($autoSavingsProfiles as $profile)
                        <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[10px] font-black uppercase text-zinc-500">{{ $profile['profile'] }}</p>
                            <p class="text-xl font-black text-emerald-600 mt-1">{{ $profile['percent'] }}%</p>
                            <p class="text-[10px] text-zinc-500 mt-1">{{ number_format($profile['monthly'], 2, ',', '.') }} {{ $workspaceCurrency }}/mês</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">8. Gastos invisíveis</p>
                <p class="text-3xl font-black text-red-500 mt-2">{{ number_format($invisibleMonthly, 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                <p class="text-[10px] text-zinc-500">impacto mensal · {{ number_format($invisibleMonthly * 12, 2, ',', '.') }} {{ $workspaceCurrency }}/ano</p>
                <div class="mt-3 space-y-2">
                    @foreach($invisibleTop as $item)
                        <div class="text-[10px] p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/30 flex justify-between">
                            <span>{{ $item['label'] }}</span>
                            <span class="font-black">{{ number_format($item['total'], 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">9. Simulador decisão grande</p>
                <p class="text-sm font-black dark:text-white mt-2">Item base: {{ number_format($bigDecision['item_value'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                <div class="mt-3 space-y-2 text-[10px]">
                    <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/30">Comprar agora: impacto 30d {{ number_format($bigDecision['buy_now']['impact_30d'], 2, ',', '.') }}</div>
                    <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/30">Esperar: preço futuro {{ number_format($bigDecision['wait']['future_price'], 2, ',', '.') }}</div>
                    <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/30">Parcelar 12x: {{ number_format($bigDecision['installment']['monthly'], 2, ',', '.') }}/mês</div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">10. Dependência de renda</p>
                <p class="text-2xl font-black mt-2 {{ $incomeDependencyRisk === 'alto' ? 'text-red-500' : ($incomeDependencyRisk === 'medio' ? 'text-amber-500' : 'text-emerald-600') }}">Risco {{ strtoupper($incomeDependencyRisk) }}</p>
                <div class="mt-3 space-y-2 text-[10px]">
                    @foreach($incomeDependency->take(4) as $source)
                        <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/30 flex justify-between">
                            <span>{{ $source['source'] }}</span>
                            <span class="font-black">{{ number_format($source['share'], 1, ',', '.') }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">11. Estresse financeiro (30d)</p>
                <p class="text-3xl font-black mt-2 {{ $stressForecast['probability'] >= 60 ? 'text-red-500' : 'text-emerald-600' }}">{{ number_format($stressForecast['probability'], 1, ',', '.') }}%</p>
                <p class="text-[10px] text-zinc-500 mt-1">Nível: {{ strtoupper($stressForecast['pressure_level']) }}</p>
                <p class="text-[10px] text-zinc-500">Saldo projetado: {{ number_format($stressForecast['projected_end_balance'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-3">12. Modo adolescente/filho</p>
                <div class="space-y-2 text-[10px]">
                    @forelse($teenAccounts as $account)
                        <div class="p-3 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/30">
                            <p class="font-black dark:text-white">{{ $account['member'] }} {{ $account['needs_approval'] ? '· Aprovação necessária' : '' }}</p>
                            <p class="text-zinc-500">Mesada: {{ number_format($account['allowance'], 2, ',', '.') }} {{ $workspaceCurrency }} / {{ $account['frequency'] }}</p>
                            <p class="text-zinc-500">Gasto: {{ number_format($account['spent'], 2, ',', '.') }} · Limite: {{ number_format($account['spending_limit'], 2, ',', '.') }}</p>
                        </div>
                    @empty
                        <p class="text-zinc-500">Sem subcontas configuradas.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-zinc-950 text-white rounded-[2rem] p-6 border border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">13. Relatório fim do mês em 1 página</p>
                <div class="mt-4 space-y-3 text-[10px]">
                    <div>
                        <p class="font-black text-emerald-400 uppercase">Vitórias</p>
                        @foreach($onePageReport['wins'] as $item)
                            <p class="text-zinc-300">• {{ $item }}</p>
                        @endforeach
                    </div>
                    <div>
                        <p class="font-black text-amber-400 uppercase">Desvios</p>
                        @foreach($onePageReport['deviations'] as $item)
                            <p class="text-zinc-300">• {{ $item }}</p>
                        @endforeach
                    </div>
                    <div>
                        <p class="font-black text-brand-300 uppercase">3 ações práticas</p>
                        @foreach($onePageReport['actions'] as $item)
                            <p class="text-zinc-300">• {{ $item }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-3">14. Alertas por contexto temporal</p>
                <p class="text-xs font-black text-brand-600 uppercase mb-2">Período: {{ $temporalAlerts['period'] }}</p>
                <div class="space-y-2 text-[11px]">
                    @foreach($temporalAlerts['messages'] as $message)
                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-100 dark:border-zinc-800">{{ $message }}</div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-3">15. Liga de constância avançada</p>
                <div class="space-y-2 text-[10px]">
                    @foreach($consistencyLeague->take(4) as $row)
                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-100 dark:border-zinc-800">
                            <div class="flex justify-between">
                                <p class="font-black dark:text-white">{{ $row['position'] }}º · {{ $row['name'] }}</p>
                                <p class="font-black text-brand-600">{{ number_format($row['consistency_points'], 1, ',', '.') }} pts</p>
                            </div>
                            <p class="text-zinc-500 mt-1">Melhora relativa: {{ number_format($row['relative_improvement'], 1, ',', '.') }}% · Regularidade: {{ number_format($row['regularity_score'], 1, ',', '.') }}</p>
                            <p class="text-zinc-500">Bônus recuperação: {{ $row['recovery_bonus'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- PAINEL DE SAÚDE DA FAMÍLIA --}}
    <div class="space-y-4">
        <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 px-2">Painel de Saúde da Família</h2>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-1 bg-zinc-950 text-white rounded-[2rem] p-6 border border-zinc-800">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Score Geral</p>
                <p class="text-5xl font-black italic mt-2">{{ number_format($familyHealth['score'], 1, ',', '.') }}</p>
                <p class="text-[10px] text-zinc-400 mt-2">Disciplina + Poupança + Aderência</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-5 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Disciplina Orçamental</p>
                <p class="text-3xl font-black mt-1 text-brand-600">{{ number_format($familyHealth['budget_discipline'], 1, ',', '.') }}</p>
                <p class="text-[10px] text-zinc-400 mt-2">Pontuação baseada em execução do orçamento</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-5 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Taxa de Poupança do Grupo</p>
                <p class="text-3xl font-black mt-1 {{ $familyHealth['group_savings_rate'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ number_format($familyHealth['group_savings_rate'], 1, ',', '.') }}%
                </p>
                <p class="text-[10px] text-zinc-400 mt-2">Receita: {{ number_format($familyHealth['group_income'], 0, ',', '.') }} {{ $workspaceCurrency }} · Despesa: {{ number_format($familyHealth['group_expense'], 0, ',', '.') }} {{ $workspaceCurrency }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-5 border border-zinc-200 dark:border-zinc-800">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Aderência por Categoria</p>
                <p class="text-3xl font-black mt-1 text-emerald-600">{{ number_format($familyHealth['category_adherence'], 1, ',', '.') }}%</p>
                <p class="text-[10px] text-zinc-400 mt-2">{{ $familyHealth['within_budget'] }} de {{ $familyHealth['budget_categories'] }} categorias dentro do limite</p>
            </div>
        </div>
    </div>

    {{-- LIGA DE POUPANÇA --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Liga de Poupança ({{ $monthLabel }})</h2>
            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Ranking por taxa de poupança</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($savingsLeague as $row)
                @php
                    $isTop = $row['position'] === 1;
                    $isNegative = $row['savings_rate'] < 0;
                @endphp
                <div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border {{ $isTop ? 'border-emerald-300 dark:border-emerald-700' : 'border-zinc-200 dark:border-zinc-800' }} shadow-sm relative overflow-hidden">
                    @if($isTop)
                        <div class="absolute top-0 right-0 px-3 py-1 text-[9px] font-black bg-emerald-500 text-white uppercase rounded-bl-xl">Campeão</div>
                    @endif

                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl {{ $isTop ? 'bg-emerald-600' : 'bg-brand-600' }} text-white flex items-center justify-center font-black text-xs uppercase">
                                {{ substr($row['name'], 0, 2) }}
                            </div>
                            <div>
                                <p class="text-lg font-black dark:text-white leading-none">{{ $row['name'] }}</p>
                                <p class="text-[10px] text-zinc-500 uppercase font-bold mt-1">{{ $row['position'] }}º lugar</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black px-2 py-1 rounded-lg uppercase {{ $isNegative ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $row['savings_rate'] }}%
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Receita</p>
                            <p class="text-xs font-black text-emerald-600">{{ number_format($row['income'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                        </div>
                        <div class="p-2 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Despesa</p>
                            <p class="text-xs font-black text-red-500">{{ number_format($row['expense'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                        </div>
                        <div class="p-2 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Poupado</p>
                            <p class="text-xs font-black {{ $row['saved'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($row['saved'], 2, ',', '.') }} {{ $workspaceCurrency }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- RANKING DE CONSTÂNCIA --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Ranking por Constância</h2>
            <span class="text-[10px] font-black uppercase tracking-widest text-brand-600">Sequência de meses com poupança positiva</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($consistencyLeague as $row)
                <div class="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-lg font-black dark:text-white leading-none">{{ $row['name'] }}</p>
                            <p class="text-[10px] text-zinc-500 uppercase font-bold mt-1">{{ $row['position'] }}º lugar</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Pontos</p>
                            <p class="text-2xl font-black text-brand-600">{{ number_format($row['consistency_points'], 1, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/40">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Sequência Atual</p>
                            <p class="text-sm font-black text-emerald-600">{{ $row['current_streak'] }}m</p>
                        </div>
                        <div class="p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/40">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Melhor</p>
                            <p class="text-sm font-black text-brand-600">{{ $row['max_streak'] }}m</p>
                        </div>
                        <div class="p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/40">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Média Taxa</p>
                            <p class="text-sm font-black {{ $row['avg_rate'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($row['avg_rate'], 1, ',', '.') }}%</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 1. BALANÇO FINANCEIRO POR MEMBRO --}}
    <div class="space-y-4">
        <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 px-2">Performance Individual (Mês Atual)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($memberStats as $user)
                <div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-black text-xs shadow-lg uppercase">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div>
                            <p class="text-lg font-black dark:text-white leading-none">{{ $user->name }}</p>
                            <p class="text-[10px] text-zinc-500 uppercase font-bold mt-1">Lvl {{ $user->level }} · {{ $user->xp }} XP</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        <div>
                            <p class="text-[9px] font-black text-zinc-400 uppercase">Ganhou</p>
                            <p class="text-md font-bold text-emerald-600">+{{ number_format($user->total_incomes, 2) }}€</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-zinc-400 uppercase">Gastou</p>
                            <p class="text-md font-bold text-red-500">-{{ number_format($user->total_expenses, 2) }}€</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase text-zinc-500 tracking-tighter">Balanço Líquido</span>
                        <span class="text-sm font-black {{ $user->net_balance >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ number_format($user->net_balance, 2) }} €
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- 2. RANKING DE ATIVIDADE --}}
        <div class="space-y-4 lg:col-span-1">
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 px-2">Top Contribuintes (Registos)</h2>
            <div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                @foreach($topRecorders as $index => $user)
                    <div class="flex items-center justify-between p-3 border-b border-zinc-50 dark:border-zinc-800 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black text-zinc-400 w-4">{{ $index + 1 }}º</span>
                            <p class="text-sm font-bold dark:text-white">{{ explode(' ', $user->name)[0] }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-600 text-[10px] font-black">{{ $user->expenses_count }} gastos</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 3. LOG DE ATIVIDADE (O QUE FIZERAM NA CONTA) --}}
        <div class="space-y-4 lg:col-span-2">
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 px-2 text-zinc-400">Histórico Recente da Conta</h2>
            <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-100 dark:border-zinc-800">
                        <tr>
                            <th class="p-4 text-[10px] font-black uppercase text-zinc-500">Membro</th>
                            <th class="p-4 text-[10px] font-black uppercase text-zinc-500">Ação Realizada</th>
                            <th class="p-4 text-right text-[10px] font-black uppercase text-zinc-500">Quando</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($recentActivities as $log)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[8px] font-black uppercase text-zinc-500 border dark:border-zinc-700">
                                            {{ substr($log->user->name, 0, 2) }}
                                        </div>
                                        <span class="text-xs font-bold dark:text-zinc-200">{{ explode(' ', $log->user->name)[0] }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-zinc-600 dark:text-zinc-400">
                                            {{ $log->description }}
                                        </span>
                                        <span class="text-[9px] font-black uppercase text-brand-600 opacity-70">{{ $log->model_type }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-right">
                                    <span class="text-[10px] text-zinc-400 font-medium italic">{{ $log->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
