<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- STRAVA WIDGET                                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-[2.5rem] border shadow-2xl"
         style="background: linear-gradient(135deg, #fc4c02 0%, #e63900 40%, #1a0a00 100%); border-color: rgba(252,76,2,0.3);">

        {{-- Glow decorativo --}}
        <div class="absolute -right-16 -top-16 size-64 rounded-full blur-3xl" style="background: rgba(252,76,2,0.15)"></div>
        <div class="absolute -left-8 -bottom-8 size-48 rounded-full blur-3xl" style="background: rgba(252,76,2,0.08)"></div>

        {{-- Header do widget --}}
        <div class="relative z-10 flex items-center justify-between p-6 pb-4">
            <div class="flex items-center gap-4">
                <div class="size-12 rounded-2xl flex items-center justify-center shadow-lg" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2)">
                    <svg class="size-7" viewBox="0 0 24 24" fill="white">
                        <path d="M15.387 17.944l-2.089-4.116h-3.065L15.387 24l5.15-10.172h-3.066m-7.008-5.599l2.836 5.598h4.172L10.463 0l-7 13.828h4.169"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-orange-200/70">Integração</p>
                    <h2 class="text-xl font-black text-white uppercase italic tracking-tighter">Strava Connect</h2>
                </div>
            </div>

            @if($device)
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15)">
                        <div class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                        <span class="text-[9px] font-black text-emerald-300 uppercase tracking-widest">Ligado</span>
                    </div>
                    <button wire:click="refresh" wire:loading.attr="disabled"
                        class="size-9 flex items-center justify-center rounded-xl transition-all hover:scale-105 active:scale-95"
                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15)"
                        title="Atualizar dados">
                        <svg class="size-4 text-white {{ $isLoading ? 'animate-spin' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                    </button>
                    <a href="{{ route('strava.disconnect') }}"
                        onclick="return confirm('Desligar o Strava?')"
                        class="px-3 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors text-red-300 hover:text-white"
                        style="background: rgba(255,0,0,0.15); border: 1px solid rgba(255,0,0,0.2)">
                        Desligar
                    </a>
                </div>
            @endif
        </div>

        {{-- ── NÃO CONECTADO ──────────────────────────────────────────────── --}}
        @if(!$device)
        <div class="relative z-10 px-6 pb-8">
            <div class="rounded-[2rem] p-8 text-center" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08)">
                <svg class="size-16 mx-auto mb-4 opacity-80" viewBox="0 0 24 24" fill="white">
                    <path d="M15.387 17.944l-2.089-4.116h-3.065L15.387 24l5.15-10.172h-3.066m-7.008-5.599l2.836 5.598h4.172L10.463 0l-7 13.828h4.169"/>
                </svg>
                <h3 class="text-2xl font-black text-white uppercase italic tracking-tighter mb-2">Liga o teu Strava</h3>
                <p class="text-sm text-orange-200/70 font-medium mb-6 max-w-sm mx-auto">
                    Importa automaticamente todas as tuas atividades do Strava para o hub de fitness.
                    O teu Mi Fitness já sincroniza com o Strava — um clique e tens tudo aqui.
                </p>

                <div class="flex flex-wrap gap-3 justify-center mb-6">
                    @foreach(['🏃 Corridas', '🚴 Ciclismo', '🏊 Natação', '⚡ Stats em tempo real', '📊 Histórico completo'] as $feat)
                        <span class="text-[9px] font-black uppercase px-3 py-1.5 rounded-full text-orange-200" style="background: rgba(255,255,255,0.1)">{{ $feat }}</span>
                    @endforeach
                </div>

                <a href="{{ route('strava.connect') }}"
                    class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-white font-black text-sm uppercase tracking-widest shadow-2xl transition-all hover:scale-[1.02] active:scale-95"
                    style="color: #fc4c02">
                    <svg class="size-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.387 17.944l-2.089-4.116h-3.065L15.387 24l5.15-10.172h-3.066m-7.008-5.599l2.836 5.598h4.172L10.463 0l-7 13.828h4.169"/>
                    </svg>
                    Conectar com Strava
                </a>

                <p class="text-[9px] text-orange-200/50 mt-4 font-medium">
                    Precisas de uma conta Strava gratuita e da app Mi Fitness ligada ao Strava.
                </p>
            </div>
        </div>
        @else

        {{-- ── PERFIL DO ATLETA ───────────────────────────────────────────── --}}
        @if(!empty($athlete))
        <div class="relative z-10 px-6 pb-4">
            <div class="flex items-center gap-4 p-4 rounded-2xl" style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.08)">
                @if(!empty($athlete['profile']))
                    <img src="{{ $athlete['profile'] }}" alt="{{ $athlete['firstname'] ?? '' }}"
                        class="size-14 rounded-2xl object-cover border-2 border-white/20 flex-shrink-0">
                @else
                    <div class="size-14 rounded-2xl bg-white/10 flex items-center justify-center text-2xl flex-shrink-0">🏃</div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-black text-white text-lg uppercase tracking-tight truncate">
                        {{ ($athlete['firstname'] ?? '') . ' ' . ($athlete['lastname'] ?? '') }}
                    </p>
                    <p class="text-[10px] text-orange-200/60 font-medium">
                        {{ $athlete['city'] ?? '' }}{{ !empty($athlete['city']) && !empty($athlete['country']) ? ' · ' : '' }}{{ $athlete['country'] ?? '' }}
                    </p>
                </div>
                <div class="flex gap-4 flex-shrink-0">
                    <div class="text-center">
                        <p class="text-lg font-black text-white">{{ number_format($athlete['follower_count'] ?? 0) }}</p>
                        <p class="text-[8px] font-black uppercase text-orange-200/50">Seguidores</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-black text-white">{{ number_format($athlete['friend_count'] ?? 0) }}</p>
                        <p class="text-[8px] font-black uppercase text-orange-200/50">A seguir</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── TABS ───────────────────────────────────────────────────────── --}}
        <div class="relative z-10 px-6 pb-4">
            <div class="flex gap-1 p-1 rounded-2xl" style="background: rgba(0,0,0,0.3)">
                @foreach(['atividades' => '⚡ Atividades', 'estatisticas' => '📊 Stats', 'perfil' => '🏅 Troféus'] as $tab => $label)
                    <button wire:click="$set('activeTab', '{{ $tab }}')"
                        class="flex-1 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                            {{ $activeTab === $tab ? 'bg-white text-orange-500 shadow-lg' : 'text-white/60 hover:text-white' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── TAB: ATIVIDADES ────────────────────────────────────────────── --}}
        @if($activeTab === 'atividades')
        <div class="relative z-10 px-6 pb-6">
            {{-- Botão Sincronizar com Hub --}}
            <div class="mb-4 flex items-center justify-between">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-orange-200/60">Últimas atividades Strava</p>
                <button wire:click="syncToHub" wire:loading.attr="disabled"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white font-black text-[9px] uppercase tracking-widest transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                    style="color: #fc4c02">
                    <svg class="size-3.5 {{ $isSyncing ? 'animate-spin' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                    <span wire:loading.remove wire:target="syncToHub">Importar para Hub</span>
                    <span wire:loading wire:target="syncToHub">A importar...</span>
                </button>
            </div>

            @if(empty($activities))
                <div class="text-center py-10 rounded-2xl" style="background: rgba(0,0,0,0.2)">
                    <p class="text-white/50 font-bold text-sm">Nenhuma atividade encontrada</p>
                </div>
            @else
            <div class="space-y-2 max-h-96 overflow-y-auto pr-1 custom-scrollbar">
                @foreach($activities as $act)
                    @php
                        $distKm    = round(($act['distance'] ?? 0) / 1000, 2);
                        $movingSec = $act['moving_time'] ?? 0;
                        $movingMin = (int)floor($movingSec / 60);
                        $hrs       = (int)floor($movingMin / 60);
                        $mins      = $movingMin % 60;
                        $durationStr = $hrs > 0 ? "{$hrs}h {$mins}m" : "{$mins}m";
                        $pace      = $strava->formatPace($act['average_speed'] ?? 0);
                        $icon      = $strava->sportTypeIcon($act['sport_type'] ?? $act['type'] ?? '');
                        $typePt    = $strava->sportTypePt($act['sport_type'] ?? $act['type'] ?? '');
                        $dateStr   = isset($act['start_date_local'])
                            ? \Carbon\Carbon::parse($act['start_date_local'])->translatedFormat('d M Y')
                            : '';
                        $kudos     = $act['kudos_count'] ?? 0;
                        $elev      = $act['total_elevation_gain'] ?? 0;
                    @endphp
                    <div class="flex items-center gap-3 p-4 rounded-2xl transition-all hover:scale-[1.01]"
                         style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.06)">
                        <div class="size-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                             style="background: rgba(255,255,255,0.1)">{{ $icon }}</div>

                        <div class="flex-1 min-w-0">
                            <p class="font-black text-white text-sm truncate">{{ $act['name'] ?? $typePt }}</p>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                <span class="text-[9px] font-bold text-orange-200/60 uppercase">{{ $dateStr }}</span>
                                <span class="text-[9px] font-bold text-orange-200/60 uppercase">· {{ $typePt }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 flex-shrink-0 text-right">
                            @if($distKm > 0)
                                <div>
                                    <p class="text-sm font-black text-white">{{ $distKm }}<small class="text-[9px] ml-0.5 text-white/50">km</small></p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-black text-white">{{ $durationStr }}</p>
                            </div>
                            @if($act['average_heartrate'] ?? 0)
                                <div>
                                    <p class="text-sm font-black text-red-300">{{ (int)$act['average_heartrate'] }}<small class="text-[8px] ml-0.5 text-white/40">bpm</small></p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- ── TAB: ESTATÍSTICAS ──────────────────────────────────────────── --}}
        @if($activeTab === 'estatisticas')
        <div class="relative z-10 px-6 pb-6 space-y-4">
            @php
                $ytd = $stats['ytd_run_totals'] ?? [];
                $ytdRide = $stats['ytd_ride_totals'] ?? [];
                $allRun  = $stats['all_run_totals'] ?? [];
                $allRide = $stats['all_ride_totals'] ?? [];
                $recent  = $stats['recent_run_totals'] ?? [];
            @endphp

            {{-- YTD Corrida --}}
            <div class="rounded-2xl p-5" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08)">
                <p class="text-[9px] font-black uppercase tracking-widest text-orange-300 mb-3">🏃 Corrida — Este Ano</p>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-black text-white">{{ round(($ytd['distance'] ?? 0) / 1000, 0) }}</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase mt-0.5">km</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-white">{{ $ytd['count'] ?? 0 }}</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase mt-0.5">Atividades</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-white">{{ (int)round(($ytd['moving_time'] ?? 0) / 3600) }}</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase mt-0.5">horas</p>
                    </div>
                </div>
            </div>

            {{-- YTD Ciclismo --}}
            @if(($ytdRide['count'] ?? 0) > 0)
            <div class="rounded-2xl p-5" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08)">
                <p class="text-[9px] font-black uppercase tracking-widest text-blue-300 mb-3">🚴 Ciclismo — Este Ano</p>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-black text-white">{{ round(($ytdRide['distance'] ?? 0) / 1000, 0) }}</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase mt-0.5">km</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-white">{{ $ytdRide['count'] ?? 0 }}</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase mt-0.5">Atividades</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-white">{{ (int)round(($ytdRide['moving_time'] ?? 0) / 3600) }}</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase mt-0.5">horas</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- All-time --}}
            <div class="rounded-2xl p-5" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08)">
                <p class="text-[9px] font-black uppercase tracking-widest text-yellow-300 mb-3">🏅 Totais de Sempre</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl p-3 text-center" style="background: rgba(255,255,255,0.05)">
                        <p class="text-[8px] font-black uppercase text-white/40 mb-1">🏃 Corridas</p>
                        <p class="text-lg font-black text-white">{{ $allRun['count'] ?? 0 }}</p>
                        <p class="text-[8px] text-white/40">{{ round(($allRun['distance'] ?? 0) / 1000) }} km</p>
                    </div>
                    <div class="rounded-xl p-3 text-center" style="background: rgba(255,255,255,0.05)">
                        <p class="text-[8px] font-black uppercase text-white/40 mb-1">🚴 Pedaladas</p>
                        <p class="text-lg font-black text-white">{{ $allRide['count'] ?? 0 }}</p>
                        <p class="text-[8px] text-white/40">{{ round(($allRide['distance'] ?? 0) / 1000) }} km</p>
                    </div>
                </div>
            </div>

            {{-- Últimas 4 semanas --}}
            @if(!empty($recent))
            <div class="rounded-2xl p-5" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08)">
                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-300 mb-3">⚡ Últimas 4 Semanas</p>
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center">
                        <p class="text-xl font-black text-white">{{ $recent['count'] ?? 0 }}</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase">Corridas</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-black text-white">{{ round(($recent['distance'] ?? 0) / 1000, 1) }}</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase">km</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-black text-white">{{ (int)round(($recent['moving_time'] ?? 0) / 3600) }}h</p>
                        <p class="text-[8px] font-bold text-white/40 uppercase">Tempo</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ── TAB: TROFÉUS / BIGGEST ─────────────────────────────────────── --}}
        @if($activeTab === 'perfil')
        <div class="relative z-10 px-6 pb-6 space-y-3">
            @php
                $bigRide = $stats['biggest_ride_distance'] ?? 0;
                $bigClimb = $stats['biggest_climb_elevation_gain'] ?? 0;
            @endphp

            @if($bigRide > 0 || $bigClimb > 0)
            <div class="rounded-2xl p-5" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08)">
                <p class="text-[9px] font-black uppercase tracking-widest text-yellow-300 mb-4">🏆 Recordes Pessoais</p>
                <div class="grid grid-cols-2 gap-3">
                    @if($bigRide > 0)
                    <div class="rounded-xl p-4 text-center" style="background: rgba(255,255,255,0.05)">
                        <span class="text-3xl">🚴</span>
                        <p class="text-xl font-black text-white mt-2">{{ round($bigRide / 1000, 1) }}<small class="text-xs ml-1 text-white/50">km</small></p>
                        <p class="text-[8px] font-bold text-white/40 uppercase mt-1">Maior pedalada</p>
                    </div>
                    @endif
                    @if($bigClimb > 0)
                    <div class="rounded-xl p-4 text-center" style="background: rgba(255,255,255,0.05)">
                        <span class="text-3xl">⛰️</span>
                        <p class="text-xl font-black text-white mt-2">{{ round($bigClimb) }}<small class="text-xs ml-1 text-white/50">m</small></p>
                        <p class="text-[8px] font-bold text-white/40 uppercase mt-1">Maior escalada</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Link para Strava --}}
            @if(!empty($athlete['username']))
            <a href="https://www.strava.com/athletes/{{ $athlete['id'] ?? '' }}"
                target="_blank" rel="noopener"
                class="flex items-center justify-between p-5 rounded-2xl transition-all hover:scale-[1.01]"
                style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08)">
                <div class="flex items-center gap-3">
                    <svg class="size-6" viewBox="0 0 24 24" fill="white">
                        <path d="M15.387 17.944l-2.089-4.116h-3.065L15.387 24l5.15-10.172h-3.066m-7.008-5.599l2.836 5.598h4.172L10.463 0l-7 13.828h4.169"/>
                    </svg>
                    <div>
                        <p class="text-sm font-black text-white uppercase">Ver perfil no Strava</p>
                        <p class="text-[9px] text-white/40">strava.com/athletes/{{ $athlete['username'] ?? $athlete['id'] ?? '' }}</p>
                    </div>
                </div>
                <svg class="size-4 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
            @endif

            {{-- Última sync --}}
            <div class="text-center py-3">
                <p class="text-[9px] text-white/30 font-medium">
                    Última atualização: {{ $device->last_synced_at?->diffForHumans() ?? 'Nunca sincronizado' }}
                </p>
            </div>
        </div>
        @endif

        @endif {{-- fim if $device --}}
    </div>
</div>
