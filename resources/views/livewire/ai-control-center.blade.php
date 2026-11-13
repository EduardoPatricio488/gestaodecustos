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
                <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">O centro de controlo da tua inteligência financeira: acompanha alertas e ações executadas.</p>
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
        <section class="grid items-start gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @php
                $healthBreakdown = data_get($health, 'inputs.breakdown', []);
                $availableComponents = collect($healthBreakdown)->where('available', true)->count();
                $healthUnavailable = $workspace->type === 'personal' && $availableComponents === 0;
                $savingsComponent = data_get($healthBreakdown, 'savings', []);
            @endphp
            <details class="group rounded-3xl border border-zinc-200 bg-white shadow-sm transition hover:border-emerald-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-emerald-700">
                <summary class="cursor-pointer list-none rounded-3xl p-5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-emerald-500 [&::-webkit-details-marker]:hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-[0.16em] text-zinc-400">Saúde financeira</span>
                        <span class="rounded-xl bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400"><flux:icon name="sparkles" class="size-4" /></span>
                    </div>
                    <div class="mt-4 flex items-end gap-2">
                        <span class="text-4xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $healthUnavailable ? '—' : ($health['score'] ?? 0) }}</span>
                        <span class="pb-1 text-sm font-bold text-zinc-400">/100</span>
                    </div>
                    <p class="mt-1 text-sm font-bold text-brand-500">{{ $healthUnavailable ? 'Sem dados suficientes' : ($health['label'] ?? 'Sem dados suficientes') }}</p>
                    @unless($healthUnavailable)
                        <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-brand-500 transition-all" style="width: {{ max(0, min(100, (int) ($health['score'] ?? 0))) }}%"></div>
                        </div>
                    @endunless
                    <div class="mt-3 text-xs text-zinc-500">
                        @if($workspace->type === 'personal')
                            @if(data_get($savingsComponent, 'available'))
                                Poupança sobre receitas registadas: <strong class="text-zinc-700 dark:text-zinc-300">{{ number_format((float) $savingsComponent['value'], 1, ',', '.') }}%</strong>
                            @else
                                Poupança indisponível: sem receitas registadas no período.
                            @endif
                        @elseif(isset($health['margin']))
                            Margem: <strong class="text-zinc-700 dark:text-zinc-300">{{ number_format((float) $health['margin'], 1, ',', '.') }}%</strong>
                        @endif
                    </div>
                    <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">Ver cálculo <flux:icon name="chevron-down" class="size-4 transition-transform group-open:rotate-180" /></span>
                </summary>
                <div class="space-y-3 border-t border-zinc-100 px-5 pb-5 pt-4 dark:border-zinc-800">
                    @if($workspace->type === 'personal')
                        <p class="text-xs leading-5 text-zinc-500">Finance Score do mês atual · {{ $availableComponents }} de {{ count($healthBreakdown) }} componentes com dados. Os pesos disponíveis são ajustados quando faltam dados.</p>
                        @foreach($healthBreakdown as $component)
                            <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/60">
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <span class="font-bold text-zinc-700 dark:text-zinc-200">{{ $component['label'] }} <span class="font-normal text-zinc-400">· peso {{ $component['weight'] }}</span></span>
                                    <strong class="shrink-0 text-zinc-900 dark:text-white">{{ $component['available'] ? $component['score'].'/100' : 'Sem dados' }}</strong>
                                </div>
                                <p class="mt-1 text-xs leading-5 text-zinc-500">
                                    @if($component['available'])
                                        {{ number_format((float) $component['value'], 2, ',', '.') }} {{ $component['unit'] }}
                                    @else
                                        {{ $component['reason'] }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs leading-5 text-zinc-500">Pontuação estimada a partir das receitas, custos, margem, autonomia e recebimentos em atraso. Não representa o saldo disponível.</p>
                        @if(isset($health['runway_months']))<p class="text-xs text-zinc-500">Autonomia estimada: <strong class="text-zinc-700 dark:text-zinc-300">{{ number_format((float) $health['runway_months'], 1, ',', '.') }} meses</strong></p>@endif
                    @endif
                </div>
            </details>

            <button type="button" wire:click="showUnreadInsights"
                    x-on:click="document.getElementById('ai-insight-center').scrollIntoView({ behavior: 'smooth', block: 'start' })"
                    class="w-full rounded-3xl border border-zinc-200 bg-white p-5 text-left shadow-sm transition hover:border-amber-300 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-amber-500 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-amber-700">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-[0.16em] text-zinc-400">Insights por tratar</span>
                    <span class="rounded-xl bg-amber-50 p-2 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400"><flux:icon name="bell" class="size-4" /></span>
                </div>
                <p class="mt-4 text-4xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $stats['unread'] }}</p>
                <p class="mt-1 text-sm text-zinc-500">Por ler entre os últimos 30 alertas ativos</p>
                <p class="mt-3 text-xs font-semibold text-amber-600 dark:text-amber-400">{{ $stats['critical'] }} por ler com prioridade alta ou crítica</p>
                <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400">Ver alertas por ler <flux:icon name="arrow-down" class="size-4" /></span>
            </button>

            <details class="group rounded-3xl border border-zinc-200 bg-white shadow-sm transition hover:border-sky-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-sky-700">
                <summary class="cursor-pointer list-none rounded-3xl p-5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-500 [&::-webkit-details-marker]:hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-[0.16em] text-zinc-400">Ações da IA</span>
                        <span class="rounded-xl bg-sky-50 p-2 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400"><flux:icon name="sparkles" class="size-4" /></span>
                    </div>
                    <p class="mt-4 text-4xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $actions->count() }}</p>
                    <p class="mt-1 text-sm text-zinc-500">Operações registadas · últimas 20</p>
                    <p class="mt-3 text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ $stats['completed_actions'] }} concluídas · {{ $stats['pending_actions'] }} pendentes @if($stats['failed_actions']) · {{ $stats['failed_actions'] }} falhadas @endif</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-sky-600 dark:text-sky-400">Ver operações recentes <flux:icon name="chevron-down" class="size-4 transition-transform group-open:rotate-180" /></span>
                </summary>
                <div class="space-y-2 border-t border-zinc-100 px-5 pb-5 pt-4 dark:border-zinc-800">
                    @forelse($actions as $action)
                        @php
                            $actionStatus = ['completed' => 'Concluída', 'pending' => 'Pendente', 'awaiting_confirmation' => 'A aguardar confirmação', 'failed' => 'Falhada'][$action->status] ?? ucfirst(str_replace('_', ' ', $action->status));
                        @endphp
                        <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/60">
                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ data_get($action->result_payload, 'title') ?: ucfirst(str_replace('_', ' ', $action->tool_name)) }}</p>
                            <p class="mt-1 text-xs text-zinc-500">{{ $actionStatus }} · {{ $action->created_at?->diffForHumans() }}</p>
                            @if(data_get($action->result_payload, 'summary'))
                                <p class="mt-1 text-xs leading-5 text-zinc-500">{{ data_get($action->result_payload, 'summary') }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs leading-5 text-zinc-500">Ainda não existem operações registadas neste workspace.</p>
                    @endforelse
                </div>
            </details>

        </section>
    @endif

    {{-- Insight Center --}}
    <section id="ai-insight-center" class="scroll-mt-24 space-y-4">
        @php
            $categoryLabels = ['spending' => 'Despesas', 'savings' => 'Poupança', 'goals' => 'Objetivos', 'margin' => 'Margem', 'revenue' => 'Receita', 'receivables' => 'Recebimentos'];
        @endphp
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded-xl bg-amber-50 p-2 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400"><flux:icon name="sparkles" class="size-4" /></span>
                    <h2 class="text-xl font-black text-zinc-900 dark:text-white">Insight Center</h2>
                </div>
                <p class="mt-2 text-sm text-zinc-500">Alertas e oportunidades detetados a partir dos dados financeiros.</p>
            </div>
            <span class="text-xs font-bold text-zinc-400">{{ $insights->count() }} ativos · {{ $stats['unread'] }} por ler · últimos 30</span>
        </div>

        @if($insights->isNotEmpty())
            <div class="flex flex-wrap items-end justify-between gap-3 rounded-2xl border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                <div role="group" aria-label="Filtrar alertas" class="flex flex-wrap gap-1">
                    @foreach(['all' => 'Todos', 'unread' => 'Por ler', 'important' => 'Prioridade alta'] as $value => $label)
                        <button type="button" wire:click="$set('insightFilter', '{{ $value }}')" aria-pressed="{{ $insightFilter === $value ? 'true' : 'false' }}" class="rounded-xl px-3 py-2 text-xs font-bold transition {{ $insightFilter === $value ? 'bg-violet-600 text-white' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="flex items-center gap-2">
                    <label for="insight-category" class="text-xs font-bold text-zinc-500">Categoria</label>
                    <select id="insight-category" wire:model.live="insightCategory" class="rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-200">
                        <option value="all">Todas</option>
                        @foreach($insights->pluck('category')->filter()->unique()->sort() as $category)
                            <option value="{{ $category }}">{{ $categoryLabels[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <p class="text-xs font-medium text-zinc-500" aria-live="polite">{{ $visibleInsights->count() }} de {{ $insights->count() }} alertas apresentados</p>
        @endif

        @if($insightActionError)
            <p role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300">{{ $insightActionError }}</p>
        @endif

        @forelse($visibleInsights as $insight)
            @php
                $priorityLabel = ['critical' => 'Crítica', 'high' => 'Alta', 'medium' => 'Média', 'low' => 'Baixa'][$insight->priority] ?? ucfirst($insight->priority);
                $priorityClass = in_array($insight->priority, ['critical', 'high'], true) ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300' : ($insight->priority === 'medium' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300');
                $categoryLabel = $categoryLabels[$insight->category] ?? ucfirst(str_replace('_', ' ', $insight->category));
                $selectedFeedback = data_get($insight->data, 'feedback');
            @endphp
            <details wire:key="insight-{{ $insight->id }}" wire:ignore.self class="group rounded-3xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-violet-500 md:px-6 [&::-webkit-details-marker]:hidden">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ $priorityClass }}">{{ $priorityLabel }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">{{ $categoryLabel }}</span>
                            @if(!$insight->read_at)<span class="rounded-full bg-sky-50 px-2 py-1 text-[10px] font-bold text-sky-700 dark:bg-sky-950/50 dark:text-sky-300">Por ler</span>@endif
                        </div>
                        <h3 class="mt-2 text-base font-black text-zinc-900 dark:text-white">{{ $insight->title }}</h3>
                        <p class="mt-1 text-xs text-zinc-400">{{ $insight->created_at?->diffForHumans() }}</p>
                    </div>
                    <span class="flex shrink-0 items-center gap-2 text-xs font-bold text-violet-600 dark:text-violet-400"><span class="hidden sm:inline">Ver detalhes</span><flux:icon name="chevron-down" class="size-5 transition-transform group-open:rotate-180" /></span>
                </summary>
                <div class="space-y-4 border-t border-zinc-100 px-5 pb-5 pt-4 dark:border-zinc-800 md:px-6">
                    <p class="max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $insight->message }}</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500">
                        <span>Confiança: <strong class="text-zinc-700 dark:text-zinc-300">{{ $insight->confidence }}%</strong></span>
                        <span>Score: <strong class="text-zinc-700 dark:text-zinc-300">{{ $insight->score }}/100</strong></span>
                        <span>{{ str_replace('_', ' ', $insight->source) }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($insight->link)
                            <a href="{{ $insight->link }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-3 py-2 text-xs font-bold text-white transition hover:opacity-90">Analisar <flux:icon name="arrow-up-right" class="size-3.5" /></a>
                        @endif
                        @if($insight->read_at === null)
                            <flux:button wire:click="markRead({{ $insight->id }})" size="sm" variant="ghost">Marcar lido</flux:button>
                        @endif
                        <flux:button wire:click="dismiss({{ $insight->id }})" size="sm" variant="ghost">Dispensar</flux:button>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                        <span class="text-xs font-semibold text-zinc-500">Este alerta foi útil?</span>
                        @foreach(['useful' => 'Útil', 'not_useful' => 'Não útil', 'more' => 'Mais disto'] as $value => $label)
                            <button type="button" wire:click="feedback({{ $insight->id }}, '{{ $value }}')" wire:loading.attr="disabled" wire:target="feedback" aria-pressed="{{ $selectedFeedback === $value ? 'true' : 'false' }}" class="rounded-lg border px-3 py-1.5 text-xs font-bold transition disabled:opacity-50 {{ $selectedFeedback === $value ? 'border-violet-500 bg-violet-100 text-violet-800 dark:border-violet-400 dark:bg-violet-950 dark:text-violet-200' : 'border-zinc-200 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <p role="status" aria-live="polite" class="text-xs leading-5 text-zinc-500">
                        <span wire:loading wire:target="feedback">A guardar avaliação...</span>
                        <span wire:loading.remove wire:target="feedback">
                            @if($selectedFeedback === 'useful')
                                Avaliação guardada: consideraste este alerta útil.
                            @elseif($selectedFeedback === 'not_useful')
                                Avaliação guardada: consideraste este alerta não útil.
                            @elseif($selectedFeedback === 'more')
                                Avaliação guardada: queres mais alertas deste tipo. Esta escolha ainda não altera automaticamente os alertas futuros.
                            @else
                                A tua avaliação fica guardada neste alerta e pode ser alterada.
                            @endif
                        </span>
                    </p>
                </div>
            </details>
        @empty
            <div class="rounded-3xl border border-dashed border-zinc-300 bg-white px-6 py-10 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400"><flux:icon name="sparkles" class="size-6" /></span>
                @if($insights->isEmpty())
                    <h3 class="mt-4 font-bold text-zinc-900 dark:text-white">Ainda não existem insights ativos</h3>
                    <p class="mx-auto mt-1 max-w-lg text-sm leading-6 text-zinc-500">O AI Observer cria alertas quando identifica alterações relevantes, riscos ou oportunidades nos teus dados. A ausência de alertas significa apenas que ainda não foi registado nenhum sinal elegível.</p>
                @else
                    <h3 class="mt-4 font-bold text-zinc-900 dark:text-white">Nenhum alerta corresponde aos filtros</h3>
                    <button type="button" wire:click="resetInsightFilters" class="mt-2 text-sm font-bold text-violet-600 hover:underline dark:text-violet-400">Limpar filtros</button>
                @endif
            </div>
        @endforelse
    </section>

    @include('livewire.report-highlights')
</div>
