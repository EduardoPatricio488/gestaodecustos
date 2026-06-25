<div class="space-y-8 pb-20">
    @once
    <style>
        @keyframes progressSimulated {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 95%; }
        }
        .animate-progress {
            animation: progressSimulated 10s ease-out forwards;
        }
    </style>
    @endonce

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- DIV RAIZ ÚNICO (obrigatório para o Livewire)                       --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="space-y-8 pb-20">

    {{-- Alpine apenas no conteúdo principal, NÃO no div raiz --}}
    <div x-data="fitnessHub()" x-init="init()">

        {{-- ── FRASE MOTIVACIONAL DO DIA ──────────────────────────────────── --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-red-500 to-pink-600 p-6 rounded-[2rem] shadow-2xl shadow-orange-500/20 border border-orange-400/20 mb-8">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 400 120" preserveAspectRatio="none">
                    <path d="M0 60 Q 100 20 200 60 T 400 60" fill="none" stroke="white" stroke-width="1"/>
                    <path d="M0 80 Q 100 40 200 80 T 400 80" fill="none" stroke="white" stroke-width="0.5"/>
                </svg>
            </div>
            <div class="relative z-10 flex items-center justify-between gap-6">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-orange-100/70 mb-1">Mensagem do Dia</p>
                    <p class="text-lg font-black text-white italic">{{ $dailyQuote }}</p>
                </div>
                @if($streak > 0)
                    <div class="flex-shrink-0 flex flex-col items-center justify-center size-20 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm">
                        <span class="text-3xl">🔥</span>
                        <span class="text-xl font-black text-white leading-none">{{ $streak }}</span>
                        <span class="text-[8px] font-black uppercase text-orange-100/70">streak</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── HEADER ──────────────────────────────────────────────────────── --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-5">
                <div class="relative">
                    <div class="absolute inset-0 bg-orange-500/20 blur-2xl rounded-full"></div>
                    <div class="relative p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                        <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Zona de Treino</h1>
                        <span class="text-[9px] font-black uppercase tracking-widest bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 px-3 py-1 rounded-full">Performance Hub</span>
                    </div>
                    <p class="text-xs text-zinc-400 mt-1 font-medium">Move-te. Regista. Evolui. · {{ now()->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2 rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <button wire:click="$set('showGoalModal', true)" class="flex items-center gap-2 px-4 h-10 rounded-xl text-zinc-500 hover:text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-500/10 font-black uppercase text-[10px] tracking-widest transition-colors">
                    🎯 Nova Meta
                </button>
                <div class="h-5 w-px bg-zinc-200 dark:bg-zinc-800"></div>
                <button
                    wire:click="$set('showActivityModal', true)"
                    class="flex items-center gap-2 px-5 h-10 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-black uppercase text-[10px] tracking-widest shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02] active:scale-95">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Registar Treino
                </button>

            </div>
        </div>

        {{-- ── KPIs DO MÊS ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-zinc-950 p-6 rounded-[2rem] shadow-xl border border-zinc-800 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 size-20 bg-orange-500/10 blur-2xl rounded-full"></div>
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-orange-400 mb-2">Sessões este Mês</p>
                <h3 class="text-4xl font-black text-white tracking-tighter">{{ $stats['total_activities'] }}</h3>
                <p class="text-[10px] text-zinc-500 font-bold mt-1 uppercase">treinos registados</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Distância Total</p>
                <h3 class="text-3xl font-black text-orange-500 tracking-tighter">{{ $stats['total_distance'] }} <small class="text-sm font-bold text-zinc-400">km</small></h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1">este mês</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Calorias Queimadas</p>
                <h3 class="text-3xl font-black text-red-500 tracking-tighter">{{ number_format($stats['total_calories']) }} <small class="text-xs font-bold text-zinc-400">kcal</small></h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1">este mês</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Tempo Ativo</p>
                <h3 class="text-3xl font-black text-emerald-500 tracking-tighter">
                    {{ intdiv($stats['total_minutes'], 60) }}h <small class="text-xl">{{ $stats['total_minutes'] % 60 }}m</small>
                </h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1">este mês</p>
            </div>
        </div>

        {{-- ── GRÁFICO SEMANAL + METAS ──────────────────────────────────────── --}}
        <div class="grid gap-6 lg:grid-cols-5 mb-8">

            {{-- GRÁFICO 7 DIAS --}}
            <div class="lg:col-span-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Esta Semana</p>
                        <h2 class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Atividade Diária</h2>
                    </div>
                    <div class="flex items-center gap-2 text-[9px] font-black uppercase text-zinc-400">
                        <div class="size-2 rounded-full bg-orange-500"></div> Minutos
                    </div>
                </div>

                <div class="flex items-end gap-2 h-40">
                    @foreach($weekChart as $day)
                        @php $height = $day['minutes'] > 0 ? max(8, min(100, ($day['minutes'] / 120) * 100)) : 4; @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group/bar cursor-pointer" title="{{ $day['minutes'] }} min · {{ $day['calories'] }} kcal">
                            <div class="w-full flex flex-col items-center justify-end h-full pb-2">
                                @if($day['count'] > 0)
                                    <span class="text-[8px] font-black text-orange-500 mb-1">{{ $day['minutes'] }}m</span>
                                @endif
                                <div class="w-full rounded-t-xl transition-all duration-500
                                    {{ $day['isToday'] ? 'bg-orange-500 shadow-lg shadow-orange-500/40' : ($day['count'] > 0 ? 'bg-orange-200 dark:bg-orange-500/30 group-hover/bar:bg-orange-400' : 'bg-zinc-100 dark:bg-zinc-800') }}"
                                    style="height: {{ $height }}%">
                                </div>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest {{ $day['isToday'] ? 'text-orange-500' : 'text-zinc-400' }}">{{ $day['label'] }}</span>
                            <span class="text-[8px] font-bold text-zinc-300 dark:text-zinc-600">{{ $day['date'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- METAS FITNESS --}}
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-6 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Objetivos</p>
                        <h2 class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Metas Ativas</h2>
                    </div>
                    <button wire:click="$set('showGoalModal', true)" class="size-8 flex items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-500 hover:bg-orange-100 transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>

                <div class="space-y-4 flex-1 overflow-y-auto pr-1">
                    @forelse($fitnessGoals as $goal)
                        <div class="group/goal">
                            <div class="flex justify-between items-end mb-1.5">
                                <div>
                                    <span class="text-xs font-black uppercase tracking-tight dark:text-white group-hover/goal:text-orange-500 transition-colors">{{ $goal->name }}</span>
                                    <p class="text-[8px] font-bold text-zinc-400 uppercase mt-0.5">{{ $goal->type_label }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black {{ $goal->completed ? 'text-emerald-500' : 'text-orange-500' }}">
                                        {{ $goal->progress }} / {{ $goal->target }}
                                    </span>
                                    <button wire:click="deleteGoal({{ $goal->id }})" wire:confirm="Apagar esta meta?" class="opacity-0 group-hover/goal:opacity-100 transition-opacity size-5 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10 text-red-400 hover:bg-red-100">
                                        <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 {{ $goal->completed ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.4)]' }}"
                                    style="width: {{ $goal->percentage }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-[8px] font-black {{ $goal->completed ? 'text-emerald-500' : 'text-orange-500' }}">{{ round($goal->percentage) }}%</span>
                                @if($goal->completed)
                                    <span class="text-[8px] font-black text-emerald-500">✓ Concluído!</span>
                                @elseif($goal->deadline)
                                    <span class="text-[8px] text-zinc-400 font-bold">até {{ $goal->deadline->format('d/m') }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <span class="text-4xl mb-3">🎯</span>
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Sem metas definidas</p>
                            <button wire:click="$set('showGoalModal', true)" class="mt-3 text-[9px] font-black uppercase text-orange-500 hover:underline">Criar primeira meta →</button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── ZONA PRINCIPAL: ATIVIDADES + CHAT IA ────────────────────────── --}}
        <div class="grid gap-6 lg:grid-cols-5">

            {{-- ATIVIDADES RECENTES --}}
            <div class="lg:col-span-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Histórico</p>
                        <h2 class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Treinos Recentes</h2>
                    </div>
                    <button wire:click="$set('showActivityModal', true)" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-black text-[9px] uppercase tracking-widest transition-colors shadow-sm shadow-orange-500/20">
                        + Novo
                    </button>
                </div>

                <div class="divide-y divide-zinc-50 dark:divide-zinc-800/50">
                    @forelse($recentActivities as $activity)
    <div wire:click="viewActivity({{ $activity->id }})"
         class="flex items-center gap-4 p-5 hover:bg-zinc-50 dark:hover:bg-orange-500/10 transition-all group/row cursor-pointer active:scale-[0.98] relative">

        <!-- Ícone -->
        <div class="size-12 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 group-hover/row:scale-110 transition-transform {{ $activity->type_color }}">
            {{ $activity->type_icon }}
        </div>

        <!-- Info Principal -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <span class="font-black text-sm dark:text-white uppercase tracking-tight">{{ ucfirst($activity->type) }}</span>
                <span class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">Visualizar →</span>
            </div>
            <div class="flex items-center gap-3 mt-0.5 flex-wrap">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">{{ $activity->activity_date->translatedFormat('d M Y') }}</span>
                @if($activity->distance_km)
                    <span class="text-[9px] font-black text-orange-500">📍 {{ $activity->distance_km }} km</span>
                @endif
            </div>
        </div>

        <!-- Tempo e Botão de Apagar -->
        <div class="flex items-center gap-4 flex-shrink-0">
            <div class="text-right">
                <span class="text-sm font-black dark:text-white">{{ $activity->formatted_duration }}</span>
            </div>

            <!-- BOTÃO DE APAGAR (Aparece no Hover da linha) -->
            <button
                wire:click.stop="deleteActivity({{ $activity->id }})"
                wire:confirm="Queres mesmo apagar este registo de treino?"
                class="opacity-0 group-hover/row:opacity-100 transition-all size-9 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white"
                title="Apagar Registo"
            >
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
@empty
                        <div class="flex flex-col items-center justify-center py-20 text-center">
                            <span class="text-5xl mb-4">🏃</span>
                            <p class="text-zinc-400 font-black uppercase tracking-widest text-[10px]">Nenhum treino registado ainda</p>
                            <button wire:click="$set('showActivityModal', true)" class="mt-4 text-[10px] font-black uppercase text-orange-500 hover:underline">
                                Registar primeiro treino →
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- CHAT COACH IA --}}
            <div class="lg:col-span-2 flex flex-col bg-zinc-950 rounded-[2.5rem] border border-zinc-800 shadow-xl overflow-hidden" style="height: 600px;">
                <div class="p-5 border-b border-zinc-800 flex items-center gap-3 flex-shrink-0">
                    <div class="relative">
                        <div class="size-10 rounded-2xl bg-orange-500/20 border border-orange-500/30 flex items-center justify-center text-lg">🤖</div>
                        <div class="absolute -bottom-0.5 -right-0.5 size-3 rounded-full bg-emerald-500 border-2 border-zinc-950"></div>
                    </div>
                    <div>
                        <p class="text-xs font-black text-white uppercase tracking-widest">Coach IA</p>
                        <p class="text-[9px] text-zinc-500 font-bold">Sempre disponível · Powered by Claude</p>
                    </div>
                    <div class="ml-auto">
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-emerald-500/10 rounded-full border border-emerald-500/20">
                            <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[8px] font-black text-emerald-400 uppercase">Online</span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar" id="chat-messages" x-ref="chatMessages">
                    @foreach($this->messages as $msg)
                        <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            @if($msg['role'] === 'assistant')
                                <div class="size-6 rounded-xl bg-orange-500/20 flex items-center justify-center text-xs mr-2 flex-shrink-0 mt-1">🤖</div>
                            @endif
                            <div class="max-w-[85%]">
                                <div class="px-4 py-3 rounded-2xl text-xs font-medium leading-relaxed
                                    {{ $msg['role'] === 'user'
                                        ? 'bg-orange-500 text-white rounded-tr-sm'
                                        : 'bg-zinc-800 text-zinc-200 rounded-tl-sm' }}">
                                    {!! nl2br(e($msg['text'])) !!}
                                </div>
                                <p class="text-[8px] text-zinc-600 font-bold mt-1 {{ $msg['role'] === 'user' ? 'text-right' : 'text-left' }} px-1">{{ $msg['time'] }}</p>
                            </div>
                        </div>
                    @endforeach

                    @if($isTyping)
                        <div class="flex justify-start items-end gap-2">
                            <div class="size-6 rounded-xl bg-orange-500/20 flex items-center justify-center text-xs">🤖</div>
                            <div class="bg-zinc-800 rounded-2xl rounded-tl-sm px-4 py-3">
                                <div class="flex gap-1 items-center">
                                    <div class="size-1.5 rounded-full bg-zinc-500 animate-bounce" style="animation-delay: 0ms"></div>
                                    <div class="size-1.5 rounded-full bg-zinc-500 animate-bounce" style="animation-delay: 150ms"></div>
                                    <div class="size-1.5 rounded-full bg-zinc-500 animate-bounce" style="animation-delay: 300ms"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-4 py-2 flex gap-2 overflow-x-auto no-scrollbar flex-shrink-0 border-t border-zinc-800/50">
                    @foreach(['Plano de treino semanal', 'Dicas de recuperação', 'Nutrição pós-treino', 'Motivação!'] as $suggestion)
                        <button wire:click="$set('chatInput', '{{ $suggestion }}')"
                            class="flex-shrink-0 text-[8px] font-black uppercase px-3 py-1.5 rounded-full bg-zinc-800 text-zinc-400 hover:bg-orange-500/20 hover:text-orange-400 transition-colors border border-zinc-700 hover:border-orange-500/30">
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>

                <div class="p-3 border-t border-zinc-800 flex-shrink-0">
                    <div class="flex gap-2 items-center bg-zinc-900 rounded-2xl border border-zinc-700 focus-within:border-orange-500/50 transition-colors px-3 py-2">
                        <input
                            type="text"
                            wire:model="chatInput"
                            wire:keydown.enter="sendChat"
                            placeholder="Pergunta ao teu coach..."
                            class="flex-1 bg-transparent text-xs text-zinc-300 placeholder-zinc-600 outline-none font-medium"
                        >
                        <button wire:click="sendChat" wire:loading.attr="disabled"
                            class="size-7 flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white transition-colors flex-shrink-0 disabled:opacity-50">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- ═══ FIM DO DIV ALPINE ═══ --}}

    {{-- ── WEARABLES & DISPOSITIVOS CONECTADOS ──────────────────────────── --}}
<div class="relative overflow-hidden bg-gradient-to-br from-zinc-950 to-zinc-900 p-8 rounded-[2.5rem] border border-zinc-800 shadow-xl"
     x-data="{
         device: '',
         loading: false,
         result: null,
         error: null,
         importLoading: false,
         importMsg: null,
         importError: null,
         async search() {
             if (!this.device.trim()) return;
             this.loading = true;
             this.result = null;
             this.error = null;
             try {
                 const res = await fetch('/api/smartwatch-info', {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                     body: JSON.stringify({ device: this.device })
                 });
                 const data = await res.json();
                 if (data.error) { this.error = data.error; } else { this.result = data; }
             } catch(e) { this.error = 'Erro de ligação.'; }
             this.loading = false;
         },
         async importFile(event) {
             const file = event.target.files[0];
             if (!file) return;
             this.importLoading = true;
             this.importMsg = null;
             this.importError = null;
             const form = new FormData();
             form.append('file', file);
             form.append('_token', document.querySelector('meta[name=csrf-token]').content);
             try {
                 const res = await fetch('/api/mifitness/import', { method: 'POST', body: form });
                 const data = await res.json();
                 if (data.error) { this.importError = data.error; } else { this.importMsg = data.message; }
             } catch(e) { this.importError = 'Erro ao enviar ficheiro.'; }
             this.importLoading = false;
             event.target.value = '';
         }
     }">

    <div class="absolute -right-10 -bottom-10 size-48 bg-blue-500/5 blur-3xl rounded-full"></div>

    {{-- Cabeçalho --}}
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-5">
            <div class="size-14 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-2xl flex-shrink-0">⌚</div>
            <div>
                <h3 class="text-base font-black text-white uppercase italic tracking-tighter">Smartwatch & Wearables</h3>
                <p class="text-xs text-zinc-500 font-medium mt-1">Associa o teu dispositivo e importa dados automaticamente</p>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button wire:click="$set('showDeviceModal', true)"
                class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-orange-500/20">
                ➕ Associar Dispositivo
            </button>
            <button wire:click="$toggle('showMiFitnessGuide')"
                class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-zinc-800 border border-zinc-700 text-zinc-300 font-black text-[10px] uppercase tracking-widest hover:bg-zinc-700 transition-colors">
                📱 Guia Mi Fitness
            </button>
        </div>
    </div>

    {{-- GUIA MI FITNESS (toggle) --}}
    @if($showMiFitnessGuide)
    <div class="relative z-10 mb-8 bg-zinc-900 border border-orange-500/20 rounded-[2rem] p-6 animate-in slide-in-from-top-2 duration-300">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <span class="text-2xl">📱</span>
                <div>
                    <p class="font-black text-white uppercase tracking-tighter">Como ligar o Xiaomi Smart Band 10</p>
                    <p class="text-[9px] text-zinc-500 uppercase tracking-widest mt-0.5">Mi Fitness → Exportar dados → Importar aqui</p>
                </div>
            </div>
            <button wire:click="$set('showMiFitnessGuide', false)" class="size-7 rounded-full bg-zinc-800 text-zinc-400 hover:bg-zinc-700 flex items-center justify-center text-xs">✕</button>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Método 1: Exportar da Mi Fitness --}}
            <div class="space-y-3">
                <p class="text-[9px] font-black uppercase text-orange-400 tracking-widest mb-3">📤 Método 1 — Exportar da Mi Fitness</p>
                @foreach([
                    ['1', 'Abre a app Mi Fitness no telemóvel', '📱'],
                    ['2', 'Vai a Perfil → Configurações (ícone ⚙️ no canto superior direito)', '⚙️'],
                    ['3', 'Seleciona "Exportar dados de fitness"', '📂'],
                    ['4', 'Escolhe o período e exporta como CSV ou TCX', '📊'],
                    ['5', 'Faz upload do ficheiro na secção "Importar Ficheiro" abaixo', '⬆️'],
                ] as [$n, $step, $icon])
                <div class="flex items-start gap-3">
                    <div class="size-6 rounded-full bg-orange-500/20 border border-orange-500/30 flex items-center justify-center text-[10px] font-black text-orange-400 flex-shrink-0 mt-0.5">{{ $n }}</div>
                    <p class="text-xs text-zinc-300 font-medium leading-relaxed">{{ $step }}</p>
                </div>
                @endforeach
            </div>

            {{-- Método 2: Via Strava --}}
            <div class="space-y-3">
                <p class="text-[9px] font-black uppercase text-emerald-400 tracking-widest mb-3">🔄 Método 2 — Sincronizar via Strava (automático)</p>
                @foreach([
                    ['1', 'Na Mi Fitness: Perfil → Aplicações de terceiros → Strava', '📱'],
                    ['2', 'Liga a tua conta Strava (gratuita em strava.com)', '🔗'],
                    ['3', 'Os treinos passam automaticamente para o Strava', '✅'],
                    ['4', 'Usa o botão "Importar do Strava" para trazer os dados', '🚴'],
                ] as [$n, $step, $icon])
                <div class="flex items-start gap-3">
                    <div class="size-6 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-[10px] font-black text-emerald-400 flex-shrink-0 mt-0.5">{{ $n }}</div>
                    <p class="text-xs text-zinc-300 font-medium leading-relaxed">{{ $step }}</p>
                </div>
                @endforeach
                <a href="https://www.strava.com/settings/apps" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-xl bg-[#fc4c02] text-white font-black text-[9px] uppercase tracking-widest hover:opacity-90 transition-opacity">
                    🚴 Abrir Strava
                </a>
            </div>
        </div>

        {{-- Import File --}}
        <div class="mt-6 pt-5 border-t border-zinc-800">
            <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-3">⬆️ Importar Ficheiro (TCX / CSV do Mi Fitness)</p>
            <div class="flex flex-col sm:flex-row gap-3 items-start">
                <label class="flex-1 flex items-center gap-3 bg-zinc-800 border border-dashed border-zinc-600 hover:border-orange-500/50 rounded-2xl px-5 py-4 cursor-pointer transition-colors group">
                    <span class="text-xl">📁</span>
                    <div>
                        <p class="text-xs font-black text-zinc-300 group-hover:text-white transition-colors">Selecionar ficheiro .tcx ou .csv</p>
                        <p class="text-[9px] text-zinc-600 mt-0.5">Exportado da Mi Fitness · Tamanho máx. 10MB</p>
                    </div>
                    <input type="file" accept=".tcx,.xml,.csv" @change="importFile($event)" class="hidden">
                </label>

                <div x-show="importLoading" class="flex items-center gap-2 px-5 py-4 rounded-2xl bg-zinc-800 border border-zinc-700">
                    <svg class="size-4 animate-spin text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    <span class="text-[10px] font-black text-zinc-400 uppercase">A importar...</span>
                </div>
            </div>

            <div x-show="importMsg && !importLoading" x-cloak class="mt-3 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <p class="text-emerald-400 text-xs font-bold" x-text="'✅ ' + importMsg"></p>
            </div>
            <div x-show="importError && !importLoading" x-cloak class="mt-3 p-3 bg-red-500/10 border border-red-500/20 rounded-xl">
                <p class="text-red-400 text-xs font-bold" x-text="'⚠️ ' + importError"></p>
            </div>
        </div>
    </div>
    @endif

    {{-- DISPOSITIVOS CONECTADOS --}}
    <div class="relative z-10 mb-8">
        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-500 mb-4">⌚ Os Meus Dispositivos</p>

        @if($connectedDevices->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 bg-zinc-900/50 border border-dashed border-zinc-700 rounded-[2rem] text-center">
                <span class="text-4xl mb-3">⌚</span>
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Nenhum dispositivo associado</p>
                <button wire:click="$set('showDeviceModal', true)" class="mt-3 text-[9px] font-black uppercase text-orange-500 hover:underline">
                    Associar primeiro dispositivo →
                </button>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($connectedDevices as $dev)
                <div class="group/dev bg-zinc-900 border border-zinc-800 hover:border-zinc-600 rounded-[2rem] p-5 transition-all relative overflow-hidden">
                    <div class="absolute -right-3 -top-3 size-16 bg-orange-500/5 blur-2xl rounded-full"></div>

                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-2xl">{{ $dev->emoji }}</div>
                            <div>
                                <p class="font-black text-white text-sm uppercase tracking-tight">{{ $dev->name }}</p>
                                <p class="text-[9px] font-bold text-zinc-500 uppercase mt-0.5">{{ $dev->brand }}</p>
                            </div>
                        </div>
                        <button wire:click="disconnectDevice({{ $dev->id }})" wire:confirm="Remover dispositivo?"
                            class="opacity-0 group-hover/dev:opacity-100 transition-opacity size-7 flex items-center justify-center rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black px-2 py-1 rounded-full border {{ $dev->provider_color }}">{{ $dev->provider_label }}</span>
                        <span class="text-[9px] font-bold {{ $dev->status_color }}">{{ $dev->status_label }}</span>
                    </div>

                    @if($dev->last_synced_at)
                        <p class="text-[8px] text-zinc-600 mt-2">Última sync: {{ $dev->last_synced_at->diffForHumans() }}</p>
                    @endif

                    <button wire:click="markSynced({{ $dev->id }})"
                        class="mt-3 w-full py-2 rounded-xl bg-zinc-800 hover:bg-orange-500/20 text-zinc-400 hover:text-orange-400 font-black text-[9px] uppercase tracking-widest transition-all border border-zinc-700 hover:border-orange-500/20">
                        🔄 Registar Sincronização
                    </button>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Campo de pesquisa do dispositivo --}}
    <div class="relative z-10 mb-6">
        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-500 mb-3">🔍 Pesquisar informação sobre um dispositivo</p>
        <div class="flex gap-3">
            <div class="flex-1 flex items-center gap-3 bg-zinc-900 border border-zinc-700 rounded-2xl px-4 focus-within:border-orange-500/50 transition-colors">
                <span class="text-lg">⌚</span>
                <input
                    type="text"
                    x-model="device"
                    @keydown.enter="search()"
                    placeholder="Ex: Xiaomi Smart Band 10, Apple Watch Series 9, Garmin Forerunner..."
                    class="flex-1 bg-transparent text-sm text-zinc-300 placeholder-zinc-600 outline-none font-medium py-4"
                >
            </div>
            <button
                @click="search()"
                :disabled="loading || !device.trim()"
                class="px-6 py-4 rounded-2xl bg-orange-500 hover:bg-orange-600 disabled:opacity-40 disabled:cursor-not-allowed text-white font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2">
                <span x-show="!loading">Pesquisar</span>
                <span x-show="loading" class="flex items-center gap-2">
                    <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    A pesquisar...
                </span>
            </button>
        </div>

        {{-- Sugestões rápidas --}}
        <div class="flex gap-2 mt-3 flex-wrap">
            @foreach(['Xiaomi Smart Band 10', 'Apple Watch Series 10', 'Samsung Galaxy Watch 7', 'Garmin Forerunner 265', 'Fitbit Charge 6'] as $suggestion)
            <button @click="device = '{{ $suggestion }}'; search()"
                class="text-[8px] font-black uppercase px-3 py-1.5 rounded-full bg-zinc-800 text-zinc-500 hover:bg-orange-500/20 hover:text-orange-400 transition-colors border border-zinc-700 hover:border-orange-500/30">
                {{ $suggestion }}
            </button>
            @endforeach
        </div>
    </div>











{{-- Resultado --}}
    <div class="relative z-10">

        {{-- Loading skeleton --}}
        <div x-show="loading" class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-pulse">
            <div class="md:col-span-1 bg-zinc-800/50 rounded-3xl h-48"></div>
            <div class="md:col-span-2 grid grid-cols-2 gap-3">
                <div class="bg-zinc-800/50 rounded-2xl h-20"></div>
                <div class="bg-zinc-800/50 rounded-2xl h-20"></div>
            </div>
        </div>

        {{-- Erro --}}
        <div x-show="error && !loading" class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl mb-6">
            <p class="text-red-400 text-xs font-bold text-center" x-text="error"></p>
        </div>

        {{-- Resultado Detalhado do Dispositivo --}}
<div x-show="result && !loading" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-in zoom-in-95 duration-500">

    {{-- COLUNA ESQUERDA: CARD PRINCIPAL + PROS/CONTRAS (4 Colunas) --}}
    <div class="lg:col-span-4 space-y-4">
        <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-[2.5rem] p-8 flex flex-col items-center text-center shadow-xl">
            <div class="text-7xl mb-4 drop-shadow-2xl" x-text="result?.emoji || '⌚'"></div>
            <p class="font-black text-white text-2xl uppercase tracking-tighter" x-text="result?.name"></p>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-[10px] bg-orange-500 text-white font-black px-3 py-1 rounded-full uppercase tracking-widest" x-text="result?.brand"></span>
                <span class="text-[10px] bg-zinc-700 text-zinc-300 font-black px-3 py-1 rounded-full uppercase" x-text="result?.year"></span>
            </div>
        </div>

        {{-- PRÓS E CONTRAS --}}
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-[2rem] p-6 space-y-4">
            <div>
                <p class="text-[9px] font-black uppercase text-emerald-500 tracking-widest mb-3">✅ Pontos Fortes</p>
                <div class="space-y-2">
                    <template x-for="pro in result?.pros">
                        <div class="flex items-center gap-2 text-xs font-bold text-zinc-300">
                            <div class="size-1 rounded-full bg-emerald-500"></div>
                            <span x-text="pro"></span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="pt-4 border-t border-zinc-800">
                <p class="text-[9px] font-black uppercase text-red-500 tracking-widest mb-3">❌ Pontos Fracos</p>
                <div class="space-y-2">
                    <template x-for="con in result?.cons">
                        <div class="flex items-center gap-2 text-xs font-bold text-zinc-400">
                            <div class="size-1 rounded-full bg-red-500"></div>
                            <span x-text="con"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- OPINIÃO DO COACH --}}
        <template x-if="result?.coach_advice">
            <div class="bg-gradient-to-br from-orange-600 to-red-700 p-6 rounded-[2.5rem] shadow-xl relative overflow-hidden group">
                <div class="absolute -right-2 -bottom-2 text-7xl opacity-20 group-hover:scale-110 transition-transform">💡</div>
                <p class="text-[9px] font-black uppercase text-orange-100 tracking-widest mb-2 italic">Opinião Sincera do Coach</p>
                <p class="text-sm font-bold text-white italic leading-relaxed" x-text="result.coach_advice"></p>
            </div>
        </template>
    </div>

    {{-- COLUNA DIREITA: ESPECIFICAÇÕES TÉCNICAS (8 Colunas) --}}
    <div class="lg:col-span-8 space-y-4">

        {{-- TOP GRID: Sensores e Display --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-zinc-900/50 border border-zinc-800 rounded-[2rem] p-6">
                <p class="text-[9px] font-black uppercase text-zinc-500 mb-4 flex items-center gap-2">❤️ Saúde e Sensores</p>
                <div class="flex flex-wrap gap-2">
                    <template x-for="metric in result?.health_metrics">
                        <span class="text-[10px] font-bold px-3 py-1.5 rounded-xl bg-red-500/5 text-red-400 border border-red-500/10" x-text="metric"></span>
                    </template>
                    <template x-for="sensor in result?.sensors">
                        <span class="text-[10px] font-bold px-3 py-1.5 rounded-xl bg-zinc-800 text-zinc-400" x-text="sensor"></span>
                    </template>
                </div>
            </div>
            <div class="bg-zinc-900/50 border border-zinc-800 rounded-[2rem] p-6">
                <p class="text-[9px] font-black uppercase text-zinc-500 mb-4 flex items-center gap-2">📱 Ecrã e Construção</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-zinc-500 uppercase">Painel</span>
                        <span class="text-xs font-black text-zinc-200" x-text="result?.display"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-zinc-500 uppercase">Materiais</span>
                        <span class="text-xs font-black text-zinc-200" x-text="result?.materials"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-zinc-500 uppercase">Resistência</span>
                        <span class="text-xs font-black text-emerald-400" x-text="result?.water"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- INFO CARDS --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-zinc-800/30 p-6 rounded-[2rem] border border-zinc-800 text-center">
                <p class="text-[8px] font-black text-zinc-500 uppercase mb-2">Bateria</p>
                <p class="text-lg font-black text-white" x-text="result?.battery"></p>
            </div>
            <div class="bg-zinc-800/30 p-6 rounded-[2rem] border border-zinc-800 text-center">
                <p class="text-[8px] font-black text-zinc-500 uppercase mb-2">GPS</p>
                <p class="text-lg font-black" :class="result?.gps ? 'text-emerald-400' : 'text-zinc-600'" x-text="result?.gps ? 'Sim' : 'Não'"></p>
            </div>
            <div class="bg-zinc-800/30 p-6 rounded-[2rem] border border-zinc-800 text-center">
                <p class="text-[8px] font-black text-zinc-500 uppercase mb-2">NFC</p>
                <p class="text-lg font-black" :class="result?.nfc ? 'text-emerald-400' : 'text-zinc-600'" x-text="result?.nfc ? '✓ Sim' : '✗ Não'"></p>
            </div>
        </div>

        {{-- DESPORTO --}}
        <div class="bg-zinc-900/50 border border-zinc-800 rounded-[2rem] p-6">
            <p class="text-[9px] font-black uppercase text-zinc-500 mb-3 flex items-center gap-2">🏃 Performance Desportiva</p>
            <div class="flex flex-wrap gap-2">
                <template x-for="sport in result?.sports">
                    <span class="text-[10px] font-bold px-3 py-1.5 rounded-xl bg-emerald-500/5 text-emerald-400 border border-emerald-500/10" x-text="sport"></span>
                </template>
            </div>
        </div>

        {{-- DICA INTEGRAÇÃO --}}
        <div class="bg-blue-500/5 border border-blue-500/20 rounded-[2rem] p-5 flex items-start gap-4">
            <span class="text-2xl">📲</span>
            <div>
                <p class="text-[10px] font-black uppercase text-blue-400 tracking-[0.2em]">Dica de Integração com Finance Pro</p>
                <p class="text-xs text-zinc-400 mt-1 font-medium leading-relaxed" x-text="result?.integration_tip"></p>
            </div>
        </div>
    </div>
</div>

















</div>

    {{-- ── STRAVA WIDGET ────────────────────────────────────────────────── --}}
    @livewire('strava-widget')

    {{-- Footer --}}
    <footer class="pt-10 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Zona de Performance Física
        </p>
    </footer>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: REGISTAR ATIVIDADE (PARTE 2: CABEÇALHO E CONTEXTO)          --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
@if($showActivityModal)
<div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="$set('showActivityModal', false)"></div>
    <div class="relative w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300">

        {{-- Loader IA --}}
        <div wire:loading wire:target="analyzePhoto" class="absolute inset-0 bg-white/90 dark:bg-zinc-950/90 z-[120] flex items-center justify-center backdrop-blur-sm">
            <div class="size-12 border-4 border-orange-500/20 border-t-orange-500 rounded-full animate-spin"></div>
        </div>

        <div class="overflow-y-auto custom-scrollbar p-8 md:p-10" style="max-height: 90vh;">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black uppercase italic dark:text-white">Registo de Performance</h2>
                <button wire:click="$set('showActivityModal', false)" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" class="size-6" /></button>
            </div>

            {{-- 2. IA ANALYSER --}}
                <div class="mb-8 bg-zinc-50 dark:bg-zinc-900/50 p-6 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-orange-500 animate-pulse">✨</span>
                        <label class="text-[9px] font-black uppercase text-orange-500 tracking-[0.2em]">2. Coach IA (Auto-Preenchimento)</label>
                    </div>

                    <div class="relative border-2 border-dashed border-orange-500/20 rounded-3xl p-6 text-center hover:border-orange-500/40 transition-all cursor-pointer group">
                        <input type="file" wire:model="activityPhoto" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">

                        @if($activityPhoto)
                            <div class="relative z-20">
                                <img src="{{ $activityPhoto->temporaryUrl() }}" class="size-40 mx-auto rounded-[1.5rem] object-cover shadow-2xl ring-4 ring-orange-500/10">
                                <div class="mt-6">
                                    <button type="button" wire:click="analyzePhoto" wire:loading.attr="disabled"
                                        class="w-full h-12 bg-orange-500 hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-orange-500/30 transition-all flex items-center justify-center gap-3">
                                        <flux:icon name="sparkles" class="size-4" wire:loading.remove wire:target="analyzePhoto" />
                                        <span wire:loading.remove wire:target="analyzePhoto">Analisar com IA</span>
                                        <span wire:loading wire:target="analyzePhoto">A Processar...</span>
                                    </button>
                                    <button type="button" wire:click="$set('activityPhoto', null)" class="mt-3 text-[9px] font-black text-zinc-400 uppercase hover:text-red-500 transition-colors">Limpar foto</button>
                                </div>
                            </div>
                        @else
                            <div class="py-4">
                                <flux:icon name="camera" class="size-8 text-zinc-300 dark:text-zinc-700 mx-auto mb-3" />
                                <p class="text-xs font-bold dark:text-white">Foto da consola ou relógio?</p>
                                <p class="text-[9px] text-zinc-500 uppercase mt-1">O Coach preenche o formulário por ti.</p>
                            </div>
                        @endif
                    </div>

                    @if($showPhotoAnalysis && $photoAnalysisResult)
                        <div class="p-4 bg-white dark:bg-zinc-900 border border-orange-500/30 rounded-2xl animate-in slide-in-from-top-2 duration-500 shadow-sm">
                            <p class="text-xs text-orange-600 dark:text-orange-400 font-medium italic leading-relaxed">"{{ $photoAnalysisResult }}"</p>
                        </div>
                    @endif
                </div>

            {{-- 2. FORMULÁRIO TÉCNICO --}}
            <div class="space-y-10">
                {{-- CONTEXTO --}}
                <div class="space-y-4">
                    <p class="text-[10px] font-black uppercase text-orange-500 tracking-widest border-l-2 border-orange-500 pl-3">Contexto e Origem</p>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model="activityApp" label="App de Registo" />
                        <flux:input wire:model="activityLocation" label="Localização" />
                        <flux:input wire:model="activityDate" type="date" label="Data" />
                        <flux:input wire:model="activityTime" type="time" label="Hora de Início" />
                    </div>
                </div>

                {{-- MÉTRICAS BASE --}}
                <div class="space-y-4">
                    <p class="text-[10px] font-black uppercase text-orange-500 tracking-widest border-l-2 border-orange-500 pl-3">Métricas de Sessão</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <flux:input wire:model="activityDistance" label="Distância (km)" />
                        <flux:input wire:model="activityDuration" label="Duração (min)" />
                        <flux:input wire:model="activityCalories" label="Kcal Ativas" />
                        <flux:input wire:model="activityTotalCalories" label="Kcal Totais" />

                    </div>
                </div>

                {{-- PERFORMANCE --}}
                <div class="space-y-4 bg-zinc-50 dark:bg-zinc-900/50 p-6 rounded-[2rem]">
                    <p class="text-[10px] font-black uppercase text-blue-500 tracking-widest mb-4">Ritmo e Biometria</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <flux:input wire:model="pace" label="Ritmo Médio" />
                        <flux:input wire:model="hrAvg" label="FC Média (bpm)" />
                        <flux:input wire:model="hrMax" label="FC Máxima (bpm)" />
                        <flux:input wire:model="steps" label="Passos" />
                        <flux:input wire:model="cadenceAvg" label="Cadência Méd." />
                        <flux:input wire:model="strideAvg" label="Passada Méd. (cm)" />
                    </div>
                </div>

                {{-- ZONAS --}}
                <div class="space-y-4">
                    <p class="text-[10px] font-black uppercase text-orange-500 tracking-widest border-l-2 border-orange-500 pl-3">Zonas de Intensidade (Tempo)</p>
                    <div class="grid grid-cols-5 gap-2">
                        <flux:input wire:model="zoneLight" label="Leve" />
                        <flux:input wire:model="zoneIntensive" label="Intens." />
                        <flux:input wire:model="zoneAerobic" label="Aerób." />
                        <flux:input wire:model="zoneAnaerobic" label="Anaer." />
                        <flux:input wire:model="zoneVO2Max" label="VO2" />
                    </div>
                </div>

                {{-- EFEITO --}}
                <div class="space-y-4 bg-zinc-950 p-6 rounded-[2rem] text-white">
                    <p class="text-[10px] font-black uppercase text-emerald-400 tracking-widest mb-4">Efeito e Recuperação</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <flux:input wire:model="teAerobic" label="TE Aeróbico" class="!bg-zinc-800" />
                        <flux:input wire:model="teAnaerobic" label="TE Anaeróbico" class="!bg-zinc-800" />
                        <flux:input wire:model="recoveryTime" label="Recuperação" class="!bg-zinc-800" />
                        <flux:input wire:model="trainingLoad" label="Carga" class="!bg-zinc-800" />
                    </div>
                </div>

                <div class="flex gap-4 pt-10">
                    <button type="button" wire:click="$set('showActivityModal', false)" class="flex-1 h-16 rounded-[1.5rem] text-zinc-400 font-black uppercase text-[10px]">Cancelar</button>
                    <button wire:click="saveActivity" class="flex-[2] h-16 bg-orange-600 text-white rounded-[1.5rem] font-black uppercase tracking-widest text-[11px] shadow-2xl transition-all">Guardar Sessão 💪</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: NOVA META FITNESS                                           --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @if($showGoalModal)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        wire:click.self="$set('showGoalModal', false)">
        <div class="w-full max-w-md bg-white dark:bg-zinc-950 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl" wire:click.stop>
            <div class="p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black uppercase italic tracking-tighter dark:text-white">🎯 Nova Meta</h2>
                    <button wire:click="$set('showGoalModal', false)" class="p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[9px] font-black uppercase tracking-widest text-orange-500 z-10">Nome da Meta</label>
                        <input type="text" wire:model="goalName" placeholder="Ex: Correr 20km por semana"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-orange-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                        @error('goalName') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[9px] font-black uppercase tracking-widest text-zinc-400 z-10">Tipo de Meta</label>
                        <select wire:model="goalType"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-orange-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all appearance-none">
                            <option value="distancia_semanal">📍 Distância Semanal (km)</option>
                            <option value="calorias_mensais">🔥 Calorias Mensais (kcal)</option>
                            <option value="sessoes_semanais">⚡ Sessões por Semana</option>
                            <option value="tempo_semanal">⏱️ Tempo Semanal (min)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[9px] font-black uppercase tracking-widest text-zinc-400 z-10">Objetivo</label>
                            <input type="number" wire:model="goalTarget" placeholder="20" min="1"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-orange-500 rounded-2xl py-4 px-5 text-lg font-black text-orange-500 outline-none transition-all text-center">
                            @error('goalTarget') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[9px] font-black uppercase tracking-widest text-zinc-400 z-10">Prazo</label>
                            <input type="date" wire:model="goalDeadline"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-orange-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" wire:click="$set('showGoalModal', false)"
                        class="flex-1 h-12 rounded-2xl text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold uppercase text-xs tracking-widest transition-all">
                        Cancelar
                    </button>
                    <button type="button" wire:click="saveGoal"
                        class="flex-[2] h-12 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs shadow-lg shadow-orange-500/20 transition-all">
                        Criar Meta 🎯
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
{{-- ─── MODAL: ASSOCIAR DISPOSITIVO ──────────────────────────────────────── --}}
@if($showDeviceModal)
<div class="fixed inset-0 z-[120] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-zinc-950/70 backdrop-blur-sm" wire:click="$set('showDeviceModal', false)"></div>
    <div class="relative w-full max-w-md bg-white dark:bg-zinc-950 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl animate-in zoom-in-95 duration-300">
        <div class="p-8 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black uppercase italic tracking-tighter dark:text-white">⌚ Associar Dispositivo</h2>
                <button wire:click="$set('showDeviceModal', false)" class="p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Sugestões rápidas --}}
            <div>
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-3">Seleciona o teu dispositivo</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['Xiaomi Smart Band 10', 'Xiaomi', '⌚'],
                        ['Xiaomi Smart Band 9', 'Xiaomi', '⌚'],
                        ['Redmi Watch 4', 'Xiaomi', '⌚'],
                        ['Apple Watch Series 10', 'Apple', '⌚'],
                        ['Samsung Galaxy Watch 7', 'Samsung', '⌚'],
                        ['Garmin Forerunner 265', 'Garmin', '🏃'],
                        ['Fitbit Charge 6', 'Fitbit', '💙'],
                        ['Outro dispositivo', '', '⌚'],
                    ] as [$name, $brand, $emoji])
                    <button type="button"
                        wire:click="$set('deviceName', '{{ $name }}'); $set('deviceBrand', '{{ $brand }}'); $set('deviceEmoji', '{{ $emoji }}')"
                        class="flex items-center gap-2 p-3 rounded-2xl border-2 text-left transition-all {{ $deviceName === $name ? 'border-orange-500 bg-orange-500/5' : 'border-zinc-200 dark:border-zinc-800 hover:border-orange-500/40' }}">
                        <span>{{ $emoji }}</span>
                        <div>
                            <p class="text-[10px] font-black dark:text-white leading-tight">{{ $name }}</p>
                            @if($brand)<p class="text-[8px] font-bold text-zinc-400 uppercase">{{ $brand }}</p>@endif
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[9px] font-black uppercase tracking-widest text-orange-500 z-10">Nome do Dispositivo</label>
                    <input type="text" wire:model="deviceName" placeholder="Ex: Xiaomi Smart Band 10"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-orange-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                    @error('deviceName') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[9px] font-black uppercase tracking-widest text-zinc-400 z-10">Marca</label>
                        <input type="text" wire:model="deviceBrand" placeholder="Xiaomi"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-orange-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                    </div>
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[9px] font-black uppercase tracking-widest text-zinc-400 z-10">Emoji</label>
                        <input type="text" wire:model="deviceEmoji" placeholder="⌚" maxlength="4"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-orange-500 rounded-2xl py-4 px-5 text-2xl outline-none transition-all text-center">
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" wire:click="$set('showDeviceModal', false)"
                    class="flex-1 h-12 rounded-2xl text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold uppercase text-xs tracking-widest transition-all">
                    Cancelar
                </button>
                <button type="button" wire:click="saveDevice"
                    class="flex-[2] h-12 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs shadow-lg shadow-orange-500/20 transition-all">
                    Associar ⌚
                </button>
            </div>
        </div>
    </div>
</div>
@endif
{{-- MODAL DE DETALHES DO TREINO ── --}}
@if($showDetailsModal && $selectedActivity)
<div class="fixed inset-0 z-[150] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-md" wire:click="$set('showDetailsModal', false)"></div>

    <div class="relative w-full max-w-xl bg-white dark:bg-zinc-900 rounded-[3rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">

        {{-- Header com Imagem de Fundo --}}
        <div class="relative h-52 bg-orange-600">
            @if($selectedActivity->photo_path)
                <img src="{{ Storage::url($selectedActivity->photo_path) }}" class="absolute inset-0 w-full h-full object-cover opacity-40">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-zinc-900/40 to-transparent"></div>

            <button wire:click="$set('showDetailsModal', false)" class="absolute top-6 right-6 size-10 rounded-full bg-black/20 backdrop-blur-md flex items-center justify-center text-white hover:bg-black/40 transition-colors">
                <flux:icon name="x-mark" class="size-5" />
            </button>

            <div class="absolute bottom-6 left-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full bg-orange-500 text-[9px] font-black text-white uppercase tracking-widest">
                        {{ $selectedActivity->type }}
                    </span>
                    <span class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest">
                        {{ $selectedActivity->activity_date->translatedFormat('d M Y') }}
                    </span>
                </div>
                <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter leading-none">Resumo de Performance</h2>
            </div>
        </div>

        <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">

            {{-- MÉTRICAS PRINCIPAIS (GRID 3) --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center bg-zinc-50 dark:bg-zinc-800/30 p-4 rounded-3xl">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Distância</p>
                    <p class="text-2xl font-black dark:text-white tracking-tighter">{{ $selectedActivity->distance_km ?? '--' }}<small class="text-xs ml-1 font-bold text-zinc-500">km</small></p>
                </div>
                <div class="text-center bg-zinc-50 dark:bg-zinc-800/30 p-4 rounded-3xl">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Duração</p>
                    <p class="text-2xl font-black dark:text-white tracking-tighter">{{ $selectedActivity->duration_minutes }}<small class="text-xs ml-1 font-bold text-zinc-500">min</small></p>
                </div>
                <div class="text-center bg-zinc-50 dark:bg-zinc-800/30 p-4 rounded-3xl">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Calorias</p>
                    <p class="text-2xl font-black text-red-500 tracking-tighter">{{ number_format($selectedActivity->calories ?? 0) }}<small class="text-xs ml-1 font-bold text-red-500/50">kcal</small></p>
                </div>
            </div>

            {{-- PERFORMANCE BIOMÉTRICA (GRID 2) --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-zinc-950 p-5 rounded-[2rem] border border-zinc-800 flex items-center gap-4">
                    <div class="size-12 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500 text-xl">⏱️</div>
                    <div>
                        <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Ritmo Médio</p>
                        <p class="text-lg font-black text-white">{{ $selectedActivity->pace ?: '--' }} <small class="text-[10px]">/km</small></p>
                    </div>
                </div>
                <div class="bg-zinc-950 p-5 rounded-[2rem] border border-zinc-800 flex items-center gap-4">
                    <div class="size-12 rounded-2xl bg-red-500/10 flex items-center justify-center text-red-500 text-xl">❤️</div>
                    <div>
                        <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">FC Média</p>
                        <p class="text-lg font-black text-white">{{ $selectedActivity->hr_avg ?: '--' }} <small class="text-[10px]">bpm</small></p>
                    </div>
                </div>
            </div>

            {{-- DETALHES TÉCNICOS ADICIONAIS (Novos Campos) --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center border border-zinc-100 dark:border-zinc-800 p-3 rounded-2xl">
                    <p class="text-[7px] font-black text-zinc-400 uppercase mb-1">Passos</p>
                    <p class="text-sm font-black dark:text-white">{{ $selectedActivity->steps ?: '--' }}</p>
                </div>
                <div class="text-center border border-zinc-100 dark:border-zinc-800 p-3 rounded-2xl">
                    <p class="text-[7px] font-black text-zinc-400 uppercase mb-1">Cadência</p>
                    <p class="text-sm font-black dark:text-white">{{ $selectedActivity->cadence ?: '--' }}</p>
                </div>
                <div class="text-center border border-zinc-100 dark:border-zinc-800 p-3 rounded-2xl">
                    <p class="text-[7px] font-black text-zinc-400 uppercase mb-1">Passada</p>
                    <p class="text-sm font-black dark:text-white">{{ $selectedActivity->stride ?: '--' }}<small class="text-[8px]">cm</small></p>
                </div>
            </div>

            {{-- CARGA E RECUPERAÇÃO --}}
            <div class="bg-zinc-50 dark:bg-zinc-800/20 p-6 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Análise de Carga IA</p>
                    <div class="flex gap-2">
                        <span class="text-[9px] font-bold text-orange-500 bg-orange-500/10 px-2 py-0.5 rounded-full">TE: {{ $selectedActivity->te_aerobic ?: '--' }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-[8px] font-bold text-zinc-500 uppercase mb-1">Tempo de Recuperação</p>
                        <p class="text-3xl font-black text-emerald-500 italic">{{ $selectedActivity->recovery_time ?: '--' }}<small class="text-sm not-italic ml-1">horas</small></p>
                    </div>
                    <div>
                        <p class="text-[8px] font-bold text-zinc-500 uppercase mb-1">Carga de Treino</p>
                        <p class="text-3xl font-black text-orange-500 italic">{{ $selectedActivity->training_load ?: '--' }}</p>
                    </div>
                </div>
            </div>

            {{-- INTENSIDADE (ZONAS) --}}
            <div class="space-y-4">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Intensidade do Treino (Tempo)</p>
                <div class="space-y-5">
                    @if($selectedActivity->zone_vo2)
                    <div>
                        <div class="flex justify-between text-[9px] font-black uppercase mb-1.5">
                            <span class="text-orange-500">Zona VO2 Máximo</span>
                            <span class="dark:text-white">{{ $selectedActivity->zone_vo2 }}</span>
                        </div>
                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500 rounded-full shadow-[0_0_8px_rgba(249,115,22,0.4)]" style="width: 85%"></div>
                        </div>
                    </div>
                    @endif

                    @if($selectedActivity->zone_anaerobic)
                    <div>
                        <div class="flex justify-between text-[9px] font-black uppercase mb-1.5">
                            <span class="text-yellow-500">Zona Anaeróbica</span>
                            <span class="dark:text-white">{{ $selectedActivity->zone_anaerobic }}</span>
                        </div>
                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-500 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <button wire:click="$set('showDetailsModal', false)" class="w-full h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase tracking-widest dark:text-white hover:bg-zinc-200 transition-all active:scale-95 shadow-lg">
                Fechar Resumo
            </button>
        </div>
    </div>
</div>
@endif
</div>
{{-- ═══ FIM DO DIV RAIZ ÚNICO ═══ --}}

<script>
function fitnessHub() {
    return {
        init() {
            this.$watch('$wire.messages', () => {
                this.$nextTick(() => {
                    const el = this.$refs.chatMessages;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            });

            this.$nextTick(() => {
                const el = this.$refs.chatMessages;
                if (el) el.scrollTop = el.scrollHeight;
            });

            window.addEventListener('chat-sent', () => {
                this.$nextTick(() => {
                    const el = this.$refs.chatMessages;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            });

            window.addEventListener('chat-reply', () => {
                this.$nextTick(() => {
                    const el = this.$refs.chatMessages;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            });
        }
    }
}
</script>
