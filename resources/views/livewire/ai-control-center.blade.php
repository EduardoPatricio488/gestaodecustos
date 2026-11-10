<div class="space-y-8 pb-8">
    {{-- Header --}}
    <section class="relative overflow-hidden rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 md:p-8">
        <div class="pointer-events-none absolute -right-16 -top-20 size-64 rounded-full bg-violet-500/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-violet-700 dark:border-violet-900 dark:bg-violet-950/50 dark:text-violet-300">
                    <flux:icon name="sparkles" class="size-3.5" />
                    Finance Pro Intelligence
                </div>
                <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white md:text-4xl">AI Intelligence Center</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">O centro de controlo da tua inteligência financeira: acompanha alertas, ações executadas e informação que a IA guardou.</p>
                @if($workspace)
                    <p class="mt-3 text-xs font-semibold text-zinc-400">Workspace ativo: <span class="text-zinc-600 dark:text-zinc-300">{{ $workspace->name }}</span></p>
                @endif
            </div>
            <div class="flex items-center gap-2 self-start rounded-2xl bg-zinc-50 px-4 py-3 dark:bg-zinc-800/70">
                <span class="relative flex size-2.5">
                    <span class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-50"></span>
                    <span class="relative inline-flex size-2.5 rounded-full bg-emerald-500"></span>
                </span>
                <span class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Centro de controlo ativo</span>
            </div>
        </div>
    </section>

    @if(!$workspace)
        <div class="rounded-3xl border border-dashed border-zinc-300 bg-white p-10 text-center dark:border-zinc-700 dark:bg-zinc-900">
            <flux:icon name="sparkles" class="mx-auto size-9 text-zinc-400" />
            <h2 class="mt-3 font-bold text-zinc-900 dark:text-white">Sem workspace ativo</h2>
            <p class="mt-1 text-sm text-zinc-500">Seleciona ou cria um workspace para veres a análise financeira da IA.</p>
        </div>
    @else
        {{-- Overview --}}
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-[0.16em] text-zinc-400">Saúde financeira</span>
                    <span class="rounded-xl bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400"><flux:icon name="sparkles" class="size-4" /></span>
                </div>
                <div class="mt-4 flex items-end gap-2">
                    <span class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $health['score'] ?? 0 }}</span>
                    <span class="pb-1 text-sm font-bold text-zinc-400">/100</span>
                </div>
                <p class="mt-1 text-sm font-bold text-brand-500">{{ $health['label'] ?? 'Sem dados suficientes' }}</p>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <div class="h-full rounded-full bg-brand-500 transition-all" style="width: {{ max(0, min(100, (int) ($health['score'] ?? 0))) }}%"></div>
                </div>
                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-zinc-500">
                    @if(isset($health['savings_rate']))<span>Poupança: <strong class="text-zinc-700 dark:text-zinc-300">{{ $health['savings_rate'] }}%</strong></span>@endif
                    @if(isset($health['margin']))<span>Margem: <strong class="text-zinc-700 dark:text-zinc-300">{{ $health['margin'] }}%</strong></span>@endif
                </div>
            </article>

            <article class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-[0.16em] text-zinc-400">Insights por tratar</span>
                    <span class="rounded-xl bg-amber-50 p-2 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400"><flux:icon name="bell" class="size-4" /></span>
                </div>
                <p class="mt-4 text-4xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $stats['unread'] }}</p>
                <p class="mt-1 text-sm text-zinc-500">Alertas ainda não marcados como lidos</p>
                <p class="mt-3 text-xs font-semibold text-amber-600 dark:text-amber-400">{{ $stats['critical'] }} com prioridade alta ou crítica</p>
            </article>

            <article class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-[0.16em] text-zinc-400">Ações da IA</span>
                    <span class="rounded-xl bg-sky-50 p-2 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400"><flux:icon name="sparkles" class="size-4" /></span>
                </div>
                <p class="mt-4 text-4xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $actions->count() }}</p>
                <p class="mt-1 text-sm text-zinc-500">Operações recentes registadas</p>
                <p class="mt-3 text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ $stats['completed_actions'] }} concluídas · {{ $stats['pending_actions'] }} pendentes</p>
            </article>

            <article class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-[0.16em] text-zinc-400">Memória ativa</span>
                    <span class="rounded-xl bg-violet-50 p-2 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400"><flux:icon name="sparkles" class="size-4" /></span>
                </div>
                <p class="mt-4 text-4xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $memories->count() }}</p>
                <p class="mt-1 text-sm text-zinc-500">Informações guardadas para futuras interações</p>
                <p class="mt-3 text-xs font-semibold text-violet-600 dark:text-violet-400">Controladas por ti</p>
            </article>
        </section>
    @endif

    @if(!empty($latestDiagnosis['text']))
        <section class="overflow-hidden rounded-3xl border border-violet-200 bg-white shadow-sm dark:border-violet-900/70 dark:bg-zinc-900">
            <div class="flex flex-col gap-2 border-b border-violet-100 bg-violet-50/70 px-5 py-4 dark:border-violet-900/50 dark:bg-violet-950/30 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon name="sparkles" class="size-4 text-violet-600 dark:text-violet-300" />
                    <h2 class="font-black text-zinc-900 dark:text-white">Último diagnóstico CFO</h2>
                </div>
                @if(!empty($latestDiagnosis['at']))
                    <span class="text-xs text-zinc-500">Gerado em {{ \Illuminate\Support\Carbon::parse($latestDiagnosis['at'])->format('d/m/Y H:i') }}</span>
                @endif
            </div>
            <div class="whitespace-pre-line px-5 py-5 text-sm leading-7 text-zinc-700 dark:text-zinc-300">{{ $latestDiagnosis['text'] }}</div>
            <div class="border-t border-zinc-100 px-5 py-3 text-[11px] text-zinc-400 dark:border-zinc-800">Relatório gerado pelo CFO Inteligente. É apresentado como diagnóstico e não como alerta automático.</div>
        </section>
    @endif

    {{-- Insight Center --}}
    <section class="space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded-xl bg-amber-50 p-2 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400"><flux:icon name="sparkles" class="size-4" /></span>
                    <h2 class="text-xl font-black text-zinc-900 dark:text-white">Insight Center</h2>
                </div>
                <p class="mt-2 text-sm text-zinc-500">Alertas e oportunidades detetados a partir dos dados financeiros.</p>
            </div>
            <span class="text-xs font-bold text-zinc-400">{{ $insights->count() }} ativos · {{ $stats['unread'] }} por ler</span>
        </div>

        @forelse($insights as $insight)
            @php
                $priorityLabel = ['critical' => 'Crítica', 'high' => 'Alta', 'medium' => 'Média', 'low' => 'Baixa'][$insight->priority] ?? ucfirst($insight->priority);
                $priorityClass = in_array($insight->priority, ['critical', 'high'], true) ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300' : ($insight->priority === 'medium' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300');
                $categoryLabel = ['spending' => 'Despesas', 'savings' => 'Poupança', 'goals' => 'Objetivos', 'margin' => 'Margem', 'revenue' => 'Receita', 'receivables' => 'Recebimentos'][$insight->category] ?? ucfirst(str_replace('_', ' ', $insight->category));
            @endphp
            <article class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 md:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ $priorityClass }}">{{ $priorityLabel }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">{{ $categoryLabel }}</span>
                            @if(!$insight->read_at)<span class="size-1.5 rounded-full bg-sky-500" title="Por ler"></span><span class="text-[10px] font-bold text-sky-600 dark:text-sky-400">Novo</span>@endif
                        </div>
                        <h3 class="mt-3 text-lg font-black text-zinc-900 dark:text-white">{{ $insight->title }}</h3>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $insight->message }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] text-zinc-400">
                            <span>Confiança: <strong class="text-zinc-600 dark:text-zinc-300">{{ $insight->confidence }}%</strong></span>
                            <span>Score: <strong class="text-zinc-600 dark:text-zinc-300">{{ $insight->score }}/100</strong></span>
                            <span>{{ str_replace('_', ' ', $insight->source) }}</span>
                            <span>{{ $insight->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2 lg:max-w-56 lg:justify-end">
                        @if($insight->link)
                            <a href="{{ $insight->link }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-3 py-2 text-xs font-bold text-white transition hover:opacity-90">Analisar <flux:icon name="arrow-up-right" class="size-3.5" /></a>
                        @endif
                        @if($insight->read_at === null)
                            <flux:button wire:click="markRead({{ $insight->id }})" size="sm" variant="ghost">Marcar lido</flux:button>
                        @endif
                        <flux:button wire:click="feedback({{ $insight->id }}, 'useful')" size="sm" variant="ghost">Útil</flux:button>
                        <flux:button wire:click="feedback({{ $insight->id }}, 'not_useful')" size="sm" variant="ghost">Não útil</flux:button>
                        <flux:button wire:click="feedback({{ $insight->id }}, 'more')" size="sm" variant="ghost">Mais disto</flux:button>
                        <flux:button wire:click="dismiss({{ $insight->id }})" size="sm" variant="ghost">Dispensar</flux:button>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-zinc-300 bg-white px-6 py-10 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400"><flux:icon name="sparkles" class="size-6" /></span>
                <h3 class="mt-4 font-bold text-zinc-900 dark:text-white">Ainda não existem insights ativos</h3>
                <p class="mx-auto mt-1 max-w-lg text-sm leading-6 text-zinc-500">O AI Observer cria alertas quando identifica alterações relevantes, riscos ou oportunidades nos teus dados. A ausência de alertas significa apenas que ainda não foi registado nenhum sinal elegível.</p>
            </div>
        @endforelse
    </section>

    {{-- Action Center --}}
    <section class="space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded-xl bg-sky-50 p-2 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400"><flux:icon name="sparkles" class="size-4" /></span>
                    <h2 class="text-xl font-black text-zinc-900 dark:text-white">Action Center</h2>
                </div>
                <p class="mt-2 text-sm text-zinc-500">Histórico das operações iniciadas pela IA e respetivo estado.</p>
            </div>
            <span class="text-xs font-bold text-zinc-400">Últimas 20 operações</span>
        </div>
        <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            @forelse($actions as $action)
                @php
                    $statusLabel = ['pending' => 'A aguardar confirmação', 'awaiting_confirmation' => 'A aguardar confirmação', 'completed' => 'Concluída', 'failed' => 'Falhou', 'cancelled' => 'Cancelada', 'rejected' => 'Recusada'][$action->status] ?? ucfirst(str_replace('_', ' ', $action->status));
                    $statusClass = $action->status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : (in_array($action->status, ['pending', 'awaiting_confirmation'], true) ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300');
                @endphp
                <div class="flex flex-col gap-3 border-b border-zinc-100 p-5 last:border-0 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="mt-0.5 rounded-xl bg-zinc-100 p-2 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon name="sparkles" class="size-4" /></span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-zinc-900 dark:text-white">{{ str($action->tool_name)->replace('_', ' ')->headline() }}</p>
                            <p class="mt-1 text-xs text-zinc-500">{{ str($action->action_type)->replace('_', ' ')->headline() }} · {{ $action->created_at?->format('d/m/Y H:i') }}</p>
                            @if($action->error_message)
                                <p class="mt-1 text-xs text-rose-500">{{ $action->error_message }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="inline-flex w-fit items-center rounded-full px-3 py-1.5 text-[10px] font-black {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
            @empty
                <div class="px-6 py-10 text-center">
                    <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400"><flux:icon name="sparkles" class="size-6" /></span>
                    <h3 class="mt-4 font-bold text-zinc-900 dark:text-white">Ainda não existem ações registadas</h3>
                    <p class="mx-auto mt-1 max-w-lg text-sm leading-6 text-zinc-500">Quando a IA preparar uma operação que exija confirmação ou executar uma ação autorizada, o respetivo registo aparecerá aqui. A análise e consulta de dados, por si só, não contam como ações.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- AI Memory --}}
    <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 border-b border-zinc-100 p-5 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between md:p-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded-xl bg-violet-50 p-2 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400"><flux:icon name="sparkles" class="size-4" /></span>
                    <h2 class="text-xl font-black text-zinc-900 dark:text-white">Memória da IA</h2>
                </div>
                <p class="mt-2 text-sm text-zinc-500">Informação persistente que ajuda a IA a manter contexto entre conversas. Podes consultar e apagar o que estiver guardado.</p>
            </div>
            @if($memories->isNotEmpty())
                <flux:button wire:click="clearMemories" wire:confirm="Queres apagar toda a memória guardada pela IA?" variant="danger" size="sm">Apagar toda a memória</flux:button>
            @endif
        </div>
        <div class="space-y-3 p-5 md:p-6">
            @forelse($memories as $memory)
                @php
                    $memoryContent = is_array($memory->value) ? $memory->value : [];
                    $isFinancialMemory = $memory->type === 'financial_context' && !empty($memoryContent);
                    $memoryCurrency = $memoryContent['currency'] ?? 'EUR';
                    $money = fn ($amount) => number_format((float) $amount, 2, ',', '.').' '.$memoryCurrency;
                    $periodLabel = !empty($memoryContent['period'])
                        ? \Illuminate\Support\Carbon::createFromFormat('Y-m', $memoryContent['period'])->translatedFormat('F Y')
                        : null;
                    $holdings = (array) data_get($memoryContent, 'investments.holdings', []);
                @endphp
                <div class="flex items-start justify-between gap-4 rounded-2xl border border-zinc-100 bg-zinc-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-wider text-violet-600 dark:text-violet-400">{{ str($memory->type)->replace('_', ' ')->headline() }}</span>
                            <span class="text-zinc-300 dark:text-zinc-700">·</span>
                            <span class="text-[10px] font-bold text-zinc-400">{{ str($memory->key)->replace('_', ' ') }}</span>
                        </div>
                        @if($isFinancialMemory)
                            <div class="mt-4 space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-xs font-bold text-zinc-500">{{ $periodLabel ? ucfirst($periodLabel) : 'Snapshot financeiro' }}</span>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">Snapshot calculado pelo backend</span>
                                </div>
                                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                                    @foreach([['Rendimento recorrente', data_get($memoryContent, 'income.recurring', 0)], ['Rendimento recebido', data_get($memoryContent, 'income.actual', 0)], ['Despesas registadas', data_get($memoryContent, 'expenses.actual', 0)], ['Poupança efetiva', data_get($memoryContent, 'savings.actual', 0)]] as [$metricLabel, $metricValue])
                                        <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                                            <p class="text-[10px] font-bold uppercase tracking-wide text-zinc-400">{{ $metricLabel }}</p>
                                            <p class="mt-1 text-lg font-black text-zinc-900 dark:text-white">{{ $money($metricValue) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide text-zinc-400">Carteira de investimentos</p>
                                            <p class="mt-1 text-xl font-black text-zinc-900 dark:text-white">{{ $money(data_get($memoryContent, 'investments.current_value', 0)) }}</p>
                                            <p class="mt-1 text-[11px] text-zinc-400">Capital investido: {{ $money(data_get($memoryContent, 'investments.invested_cost', 0)) }}</p>
                                        </div>
                                        @php $portfolioPnl = (float) data_get($memoryContent, 'investments.profit_loss', 0); @endphp
                                        <div class="text-right">
                                            <p class="text-[10px] font-bold uppercase tracking-wide text-zinc-400">Lucro / prejuízo não realizado</p>
                                            <p class="mt-1 text-sm font-black {{ $portfolioPnl >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $portfolioPnl >= 0 ? '+' : '' }}{{ $money($portfolioPnl) }}</p>
                                            @if(data_get($memoryContent, 'investments.profit_loss_percent') !== null)
                                                <p class="text-xs text-zinc-400">{{ number_format((float) data_get($memoryContent, 'investments.profit_loss_percent'), 2, ',', '.') }}%</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if(count($holdings))
                                        <div class="mt-4 overflow-x-auto">
                                            <table class="w-full min-w-[620px] text-left text-xs">
                                                <thead class="text-[10px] uppercase tracking-wide text-zinc-400">
                                                    <tr><th class="pb-2 pr-3">Ativo</th><th class="pb-2 pr-3 text-right">Quantidade</th><th class="pb-2 pr-3 text-right">Preço atual</th><th class="pb-2 pr-3 text-right">Valor atual</th><th class="pb-2 text-right">Lucro / prejuízo</th></tr>
                                                </thead>
                                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                                    @foreach($holdings as $holding)
                                                        @php $holdingPnl = (float) ($holding['profit_loss'] ?? 0); @endphp
                                                        <tr>
                                                            <td class="py-2 pr-3"><span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $holding['name'] ?? 'Ativo' }}</span>@if(!empty($holding['symbol'])) <span class="text-zinc-400">{{ $holding['symbol'] }}</span>@endif</td>
                                                            <td class="py-2 pr-3 text-right tabular-nums">{{ number_format((float) ($holding['quantity'] ?? 0), 4, ',', '.') }}</td>
                                                            <td class="py-2 pr-3 text-right tabular-nums">{{ number_format((float) ($holding['current_price'] ?? 0), 4, ',', '.') }}</td>
                                                            <td class="py-2 pr-3 text-right tabular-nums">{{ $money($holding['current_value'] ?? 0) }}</td>
                                                            <td class="py-2 text-right font-bold tabular-nums {{ $holdingPnl >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $holdingPnl >= 0 ? '+' : '' }}{{ $money($holdingPnl) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="mt-3 text-xs text-zinc-500">Não existem posições individuais registadas nesta memória.</p>
                                    @endif
                                </div>
                                <details class="rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    <summary class="cursor-pointer px-3 py-2 text-xs font-bold text-zinc-500">Ver dados técnicos guardados</summary>
                                    <pre class="max-h-72 overflow-auto whitespace-pre-wrap break-words border-t border-zinc-100 p-3 text-[10px] leading-5 text-zinc-500 dark:border-zinc-800">{{ json_encode($memoryContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                </details>
                            </div>
                        @else
                            <p class="mt-2 break-words text-sm leading-6 text-zinc-700 dark:text-zinc-200">{{ is_scalar($memoryContent) ? $memoryContent : json_encode($memoryContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</p>
                        @endif
                        <p class="mt-3 text-[10px] text-zinc-400">Importância {{ $memory->importance }}/100 @if($memory->last_used_at) · Utilizada {{ $memory->last_used_at->diffForHumans() }}@endif @if($memory->expires_at) · Expira {{ $memory->expires_at->format('d/m/Y') }}@endif</p>
                    </div>
                    <flux:button wire:click="forgetMemory({{ $memory->id }})" wire:confirm="Apagar esta memória?" variant="ghost" size="sm">Apagar</flux:button>
                </div>
            @empty
                <div class="py-8 text-center">
                    <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400"><flux:icon name="sparkles" class="size-6" /></span>
                    <h3 class="mt-4 font-bold text-zinc-900 dark:text-white">Ainda não existem memórias permanentes</h3>
                    <p class="mx-auto mt-1 max-w-lg text-sm leading-6 text-zinc-500">A IA ainda não guardou informação persistente neste workspace. Quando existirem memórias, poderás revê-las e removê-las individualmente.</p>
                </div>
            @endforelse
        </div>
        <div class="border-t border-zinc-100 bg-zinc-50/70 px-5 py-4 dark:border-zinc-800 dark:bg-zinc-950/40">
            <p class="flex items-start gap-2 text-xs leading-5 text-zinc-500"><flux:icon name="shield-check" class="mt-0.5 size-4 shrink-0 text-emerald-500" /> A memória é independente do histórico de conversas. Apagar uma memória remove-a da memória persistente da IA, sem apagar o histórico.</p>
        </div>
    </section>
</div>
