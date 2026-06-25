<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA Manifest & Theme --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#10b981">



    {{-- iOS PWA Configuration --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Finance Pro">

    {{-- Apple Icons --}}
    <link rel="apple-touch-icon" href="/pwa/splash_screens/apple-icon-180x180.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/icon-512x512.png">

   {{-- iOS Splash Screens (Comentado para evitar avisos no PC) --}}
    {{--
    <link rel="apple-touch-startup-image"
          href="/pwa/splash_screens/iPhone_16__iPhone_15_Pro__iPhone_15__iPhone_14_Pro_portrait.png"
          media="(device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icon-512x512.png">
    --}}

    <title>{{ config('app.name') }}</title>

    <script>
        if (
            localStorage.theme === 'dark' ||
            (!('theme' in localStorage) &&
            window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    @include('partials.head')

    <link rel="stylesheet" href="/flux/flux.css">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
<style>
    html::after {
        content: '';
        position: fixed;
        inset: 0;
        background: white;
        opacity: 0;
        pointer-events: none;
        z-index: 99999;
        transition: opacity 150ms ease;
    }
    html.theme-switching::after {
        opacity: 0.15;
    }
</style>
    @livewireStyles
</head>

<body
    class="layout-fixed app-shell antialiased bg-zinc-50 dark:bg-zinc-950 h-screen overflow-hidden flex"
    x-data="{
        privacyMode: false,
        mobileSidebarOpen: false
    }"
    x-on:privacy-changed.window="privacyMode = $event.detail.state"
>

    {{-- 1. PESQUISA GLOBAL --}}
    <livewire:global-search />

    {{-- 2. LÓGICA DE TUTORIAIS INTEGRADA (UNIFICADA) --}}
    @auth
        @if(auth()->user()->isAdmin())
            {{-- Tutorial Verde --}}
            <livewire:admin-onboarding />
        @elseif(auth()->user()->isModerator())
            {{-- Tutorial Dourado --}}
            <livewire:moderator-onboarding />
        @elseif(auth()->user()->isAnalyst())
            {{-- Tutorial Azul --}}
            <livewire:analyst-onboarding />
        @elseif(!auth()->user()->isAdminRole())
            {{-- Tutorial de Configuração Financeira (User Normal) --}}
            @persist('onboarding-wizard')
                <livewire:onboarding-wizard />
            @endpersist
        @endif
    @endauth

 {{-- ═══════════════════════════════════════════ --}}
    {{-- BARRA DE AVISOS GLOBAIS (LOGICA & UI)       --}}
    {{-- ═══════════════════════════════════════════ --}}
    @php
        $globalAnnouncements = \Illuminate\Support\Facades\DB::table('site_announcements')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    @endphp

    @if($globalAnnouncements->count() > 0)
        <div x-data="{
                dismissed: JSON.parse(localStorage.getItem('dismissed_announcements') || '[]'),
                dismiss(id) {
                    this.dismissed.push(id);
                    localStorage.setItem('dismissed_announcements', JSON.stringify(this.dismissed));
                }
             }"
             x-on:close-announcement-globally.window="dismiss($event.detail.id)"
             class="fixed bottom-0 left-0 right-0 z-[100] flex flex-col">

            @foreach($globalAnnouncements as $ann)
                @php
                    $tipoNorm = strtolower($ann->type);
                    $isUrgent = in_array($tipoNorm, ['danger', 'urgente', 'erro']);
                    $colorClasses = match(true) {
                        in_array($tipoNorm, ['info', 'informativo']) => 'bg-blue-600 text-white',
                        in_array($tipoNorm, ['success', 'sucesso', 'concluído']) => 'bg-emerald-600 text-white',
                        in_array($tipoNorm, ['warning', 'aviso']) => 'bg-amber-400 text-black',
                        $isUrgent => 'bg-red-600 text-white',
                        default => 'bg-zinc-800 text-white',
                    };
                @endphp

                <div x-show="'{{ $isUrgent }}' || !dismissed.includes({{ $ann->id }})"
                     class="w-full py-3 px-6 text-center shadow-[0_-10px_30px_rgba(0,0,0,0.2)] border-t border-white/10 flex items-center justify-between gap-3 {{ $colorClasses }}">

                    <div class="w-10"></div>

                    <button class="flex-1 flex items-center justify-center gap-3 outline-none group"
                            x-on:click="$dispatch('show-announcement', {
                                id: {{ $ann->id }},
                                title: '{{ $ann->title }}',
                                message: '{{ str_replace(['\r', '\n', "'"], ' ', $ann->message) }}',
                                type: '{{ $tipoNorm }}',
                                date: '{{ \Carbon\Carbon::parse($ann->created_at)->format('d/m H:i') }}',
                                expires: '{{ $ann->expires_at ? \Carbon\Carbon::parse($ann->expires_at)->diffForHumans() : 'Nunca' }}'
                            }); $dispatch('modal-show', { name: 'announcement-detail' })">

                        <flux:icon name="megaphone" class="size-4 opacity-80 group-hover:scale-125 transition-transform" />

                        <div class="flex items-center gap-3">
                            <span class="bg-black/10 px-2 py-0.5 rounded text-[9px] font-black uppercase">{{ $ann->title }}</span>
                            <span class="text-[10px] md:text-[11px] font-black uppercase tracking-[0.2em] truncate max-w-[250px] md:max-w-none">{{ $ann->message }}</span>
                        </div>
                    </button>

                    <div class="w-10 flex justify-end">
                        @if(!$isUrgent)
                            <button x-on:click.stop="dismiss({{ $ann->id }})" class="size-8 rounded-full flex items-center justify-center hover:bg-black/10 transition-all hover:rotate-90">
                                <flux:icon name="x-mark" class="size-4" />
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    <style>
        body { padding-bottom: {{ $globalAnnouncements->count() * 48 }}px !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
    </style>
@endif















    @php

        $user = auth()->user();
        $currentWs = $user->currentWorkspace;

        $isAdminMode = request()->routeIs('admin.*');

        $isBusinessMode =
            (
                request()->routeIs('hub.business.*') ||
                request()->routeIs('company-expenses')
            )
            && !$isAdminMode;

        $userPlan = $currentWs->plan ?? 'free';

        // Variáveis booleanas para controle de acesso no resto do layout
        $isDiamond = $user->isDiamond();
        $isAnyPremium = $user->isAnyPremium();

        // Definição inteligente de ícones, textos e cores
        if ($isDiamond) {
            $planEmoji = '💎';
            $planText  = 'Plano Diamante';
            $planColor = 'text-indigo-600';
        } elseif ($user->isStar()) {
            $planEmoji = '⭐';
            $planText  = 'Plano Premium';
            $planColor = 'text-amber-500';
        } else {
            $planEmoji = '👤';
            $planText  = 'Plano Básico';
            $planColor = 'text-zinc-400';
        }

        $workspaceId = $currentWs?->id ?? 0;

        $counts = \Illuminate\Support\Facades\Cache::remember(
            "layout:sidebar-counts:v2:{$user->id}:{$workspaceId}:{$isAdminMode}:{$isBusinessMode}",
            60,
            function () use ($currentWs, $workspaceId, $isAdminMode, $isBusinessMode) {
                if ($isAdminMode) {
                    return [
                        'users'   => \App\Models\User::count(),
                        'support' => \App\Models\SupportTicket::where('status', 'open')->count(),
                    ];
                }


                if ($isBusinessMode) {
                    return [
                        'company_expenses' => \App\Models\Expense::where('workspace_id', $workspaceId)->whereNull('category_id')->count(),
                        'invoices'   => \App\Models\Invoice::where('workspace_id', $workspaceId)->count(),
                        'proposals'  => \App\Models\Proposal::where('workspace_id', $workspaceId)->count(),
                        'clients'    => \App\Models\Client::where('workspace_id', $workspaceId)->count(),
                        'projects'   => \App\Models\Project::where('workspace_id', $workspaceId)->count(),
                        'inventory'  => \App\Models\Product::where('workspace_id', $workspaceId)->count(),
                        'suppliers'  => \App\Models\Supplier::where('workspace_id', $workspaceId)->count(),
                        'employees'  => \App\Models\Employee::where('workspace_id', $workspaceId)->count(),
                        'tasks'      => \App\Models\Task::where('workspace_id', $workspaceId)->where('status', '!=', 'concluido')->count(),
                        'absences'   => \App\Models\Absence::where('workspace_id', $workspaceId)->where('status', 'pendente')->count(),
                        'support'    => \App\Models\SupportTicket::where('status', 'open')->where('workspace_id', $workspaceId)->count(),
                    ];
                }

                $memberCount = $currentWs?->users()->count() ?? 0;

                return [
    'incomes'       => \App\Models\Income::where('workspace_id', $workspaceId)->count(),
    'investments'   => \App\Models\Investment::where('workspace_id', $workspaceId)->count(),
    'subscriptions' => \App\Models\Subscription::where('workspace_id', $workspaceId)->count(),
    'activity'      => \App\Models\ActivityLog::where('workspace_id', $workspaceId)->count(),
    'goals'         => \App\Models\Goal::where('workspace_id', $workspaceId)->count(),
    'debts'         => \App\Models\Debt::where('workspace_id', $workspaceId)->count(),

    'reminders'     => \App\Models\Reminder::where('workspace_id', $workspaceId)
        ->where('is_completed', false)
        ->count(),

    'members'       => $memberCount,
    'ranking'       => $memberCount,
    'support'       => \App\Models\SupportTicket::where('status', 'open')->count(),
];
            }
        );

      $catCounts = \Illuminate\Support\Facades\Cache::remember(
            "layout:category-counts:v10:{$workspaceId}", // Mudança para v10 para forçar limpeza
            60,
            fn () => \App\Models\Expense::selectRaw('categories.name, count(*) as total')
                ->join('categories', 'expenses.category_id', '=', 'categories.id')
                ->where('expenses.workspace_id', $workspaceId)
                ->whereMonth('spent_at', now()->month) // <--- Sincroniza com este mês
                ->whereYear('spent_at', now()->year)
                ->groupBy('categories.name')
                ->pluck('total', 'name')
                ->mapWithKeys(fn ($item, $key) => [
                    mb_strtolower(trim($key), 'UTF-8') => $item // Normalização total (acentos + espaços)
                ])
                ->toArray()
        );
        $badge = fn ($val) =>
            $val > 0
                ? '<span class="ml-auto text-[10px] font-black bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-full text-zinc-500 group-hover:text-brand-600 transition-colors">'.$val.'</span>'
                : '';
    @endphp

  {{-- ═══════════════════════════════════════════ --}}
    {{-- SIDEBAR LATERAL FIXA (CORRIGIDA)            --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div
        x-show="mobileSidebarOpen"
        x-cloak
        x-transition.opacity
        x-on:click="mobileSidebarOpen = false"
        class="fixed inset-0 z-[45] bg-zinc-950/60 backdrop-blur-sm lg:hidden"
        aria-hidden="true"
    ></div>

    <flux:sidebar
        sticky
        x-bind:data-mobile-sidebar-open="mobileSidebarOpen || null"
        x-on:click="if (window.innerWidth < 1024 && $event.target.closest('a')) mobileSidebarOpen = false"
        class="mobile-sidebar-panel h-screen z-50 border-e border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex flex-col"
    >
        <flux:sidebar.header class="flex items-center justify-between gap-3 px-6 py-4">
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate.hover />

            <flux:button
                class="lg:hidden"
                variant="subtle"
                square
                icon="x-mark"
                x-on:click="mobileSidebarOpen = false"
                aria-label="Fechar menu"
            />
        </flux:sidebar.header>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
















<flux:sidebar.nav>
    @if($isAdminMode)
        @php $adminUser = auth()->user(); @endphp
        <div class="flex flex-col h-full justify-between">

            {{-- NAVEGAÇÃO FILTRADA POR NÍVEL --}}
            <div class="space-y-1">

                {{-- ══════════════════════════════════════════════════
                     VISÃO GERAL — Admin + Moderador + Analista
                ══════════════════════════════════════════════════ --}}
                <flux:sidebar.group heading="Visão Geral">
                    <flux:sidebar.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        Painel de Controlo
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- ══════════════════════════════════════════════════
                     DADOS — Admin + Moderador + Analista
                ══════════════════════════════════════════════════ --}}
                <flux:sidebar.group heading="Dados & Analíticas" class="mt-4">
                    <flux:sidebar.item icon="chart-bar" :href="route('admin.stats')" :current="request()->routeIs('admin.stats')" wire:navigate>
                        Estatísticas
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="bolt" :href="route('admin.productivity')" :current="request()->routeIs('admin.productivity')" wire:navigate>
                        Produtividade
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="trophy" :href="route('admin.gamification')" :current="request()->routeIs('admin.gamification')" wire:navigate>
                        Ranking & XP
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- ══════════════════════════════════════════════════
                     MONITORIZAÇÃO — Admin + Analista (NÃO Moderador)
                ══════════════════════════════════════════════════ --}}
                @if($adminUser->isAdmin() || $adminUser->isAnalyst())
                    <flux:sidebar.group heading="Monitorização" class="mt-4">
                        <flux:sidebar.item icon="cpu-chip" :href="route('admin.ai')" :current="request()->routeIs('admin.ai')" wire:navigate>
                            Monitor de IA
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif

                {{-- ══════════════════════════════════════════════════
                     UTILIZADORES & SUPORTE — Admin + Moderador (NÃO Analista)
                ══════════════════════════════════════════════════ --}}
                @if($adminUser->isAdmin() || $adminUser->isModerator())
                    <flux:sidebar.group heading="Utilizadores & Suporte" class="mt-4">
                        <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                            Gestão de Utilizadores
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="chat-bubble-left-right" :href="route('admin.support')" :current="request()->routeIs('admin.support')" wire:navigate>
                            Tickets de Suporte
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="megaphone" :href="route('admin.communication')" :current="request()->routeIs('admin.communication')" wire:navigate>
                            Avisos Globais
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif

                {{-- ══════════════════════════════════════════════════
                     SISTEMA — Apenas Admin
                ══════════════════════════════════════════════════ --}}
                @if($adminUser->isAdmin())
                    <flux:sidebar.group heading="Sistema" class="mt-4">
                        <flux:sidebar.item icon="credit-card" :href="route('admin.billing')" :current="request()->routeIs('admin.billing')" wire:navigate>
                            Faturação & Pagamentos
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="shield-check" :href="route('admin.logs')" :current="request()->routeIs('admin.logs')" wire:navigate>
                            Segurança & Logs
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.settings')" :current="request()->routeIs('admin.settings')" wire:navigate>
                            Configurações Globais
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif

            </div>

            {{-- ══ TERMINAR SESSÃO: Sempre no fundo ══ --}}
            <div class="mt-auto pb-4">
                <flux:separator class="my-4 mx-2" />
                <flux:sidebar.item
                    icon="arrow-right-start-on-rectangle"
                    href="#"
                    class="text-red-600 hover:text-red-700 font-bold uppercase text-[10px] tracking-[0.2em]"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                >
                    Terminar Sessão
                </flux:sidebar.item>
            </div>

        </div> {{-- Fim da div flex-col --}}















            @elseif($isBusinessMode)
                {{-- MENU EMPRESARIAL --}}
                <flux:sidebar.item icon="arrow-left-circle" :href="route('dashboard')" class="text-zinc-500 font-bold" wire:navigate.hover>
                    Sair do Modo Empresa
                </flux:sidebar.item>

                <flux:separator class="my-4 mx-2" />

                <flux:sidebar.item icon="chart-pie" :href="route('hub.business.dashboard')" :current="request()->routeIs('hub.business.dashboard')" wire:navigate.hover>
                    Dashboard Business
                </flux:sidebar.item>

                <flux:sidebar.group heading="Gestão de Operações" class="mt-4">
                    <flux:sidebar.item icon="building-office-2" :href="route('company-expenses')" :current="request()->routeIs('company-expenses')" wire:navigate.hover>
                        Custos Operacionais {!! $badge($counts['company_expenses']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-text" :href="route('hub.business.invoices')" :current="request()->routeIs('hub.business.invoices')" wire:navigate.hover>
                        Faturação & Vendas {!! $badge($counts['invoices']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="newspaper" :href="route('hub.business.proposals')" :current="request()->routeIs('hub.business.proposals')" wire:navigate.hover>
                        Propostas {!! $badge($counts['proposals']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('hub.business.clients')" :current="request()->routeIs('hub.business.clients')" wire:navigate.hover>
                        Clientes (CRM) {!! $badge($counts['clients']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('hub.business.projects')" :current="request()->routeIs('hub.business.projects')" wire:navigate.hover>
                        Projetos {!! $badge($counts['projects']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="presentation-chart-line" :href="route('hub.business.pnl')" :current="request()->routeIs('hub.business.pnl')" wire:navigate.hover>
                        Resultados (P&L)
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('hub.business.inventory')" :current="request()->routeIs('hub.business.inventory')" wire:navigate.hover>
                        Stock / Inventário {!! $badge($counts['inventory']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-library" :href="route('hub.business.accounts')" :current="request()->routeIs('hub.business.accounts')" wire:navigate.hover>
                        Contas Empresa
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="truck" :href="route('hub.business.suppliers')" :current="request()->routeIs('hub.business.suppliers')" wire:navigate.hover>
                        Fornecedores {!! $badge($counts['suppliers']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="check-badge" :href="route('hub.business.tasks')" :current="request()->routeIs('hub.business.tasks')" wire:navigate.hover>
                        Tarefas & Equipa {!! $badge($counts['tasks']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="command-line" :href="route('hub.business.timeline')" :current="request()->routeIs('hub.business.timeline')" wire:navigate.hover>
                        Timeline Operações
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('hub.business.messenger')" :current="request()->routeIs('hub.business.messenger')" wire:navigate.hover>
                        Messenger Equipa
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="folder-open" :href="route('hub.business.vault')" :current="request()->routeIs('hub.business.vault')" wire:navigate.hover>
                        Arquivo Digital
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="sparkles" :href="route('hub.business.ai')" :current="request()->routeIs('hub.business.ai')" wire:navigate.hover>
                        IA Estrategista
                    </flux:sidebar.item>

                </flux:sidebar.group>

                <flux:sidebar.item icon="calendar-days" :href="route('hub.business.calendar')" :current="request()->routeIs('hub.business.calendar')" wire:navigate.hover>
                    Calendário Global
                </flux:sidebar.item>

                <flux:sidebar.group heading="Administrativo" class="mt-4">
                    <flux:sidebar.item icon="users" :href="route('hub.business.team')" :current="request()->routeIs('hub.business.team')" wire:navigate.hover>
                        Recursos Humanos {!! $badge($counts['employees']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar-days" :href="route('hub.business.absences')" :current="request()->routeIs('hub.business.absences')" wire:navigate.hover>
                        Férias & Ausências {!! $badge($counts['absences']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="receipt-percent" :href="route('hub.business.taxes')" :current="request()->routeIs('hub.business.taxes')" wire:navigate.hover>
                        Impostos & IVA
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="adjustments-horizontal" :href="route('hub.business.settings')" :current="request()->routeIs('hub.business.settings')" wire:navigate.hover>
                        Perfil do Negócio
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="chat-bubble-left-right"
                        :href="route('hub.business.support')"
                        :current="request()->routeIs('hub.business.support')"
                        wire:navigate.hover
                    >
                        Suporte Técnico {!! $badge(\App\Models\SupportTicket::where('status', 'open')->where('workspace_id', $currentWs->id)->count()) !!}
                    </flux:sidebar.item>
                </flux:sidebar.group>

            @else
                {{-- ═══════════════════════════════════════════ --}}
                {{-- MENU PESSOAL (MODO NORMAL)                  --}}
                {{-- ═══════════════════════════════════════════ --}}
                <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate.hover>
                    Dashboard
                </flux:sidebar.item>

                {{-- Lógica de Acesso à Área Empresarial (Apenas Diamante) --}}
@if($user->isDiamond())
    <flux:sidebar.item icon="building-office" :href="route('hub.business.dashboard')" class="text-brand-600 dark:text-brand-400 font-black italic" wire:navigate.hover>
        Área Empresarial →
    </flux:sidebar.item>
@else
    {{-- Aparece bloqueado para Free ou Star --}}
    <flux:sidebar.item icon="building-office" :href="route('hub.pricing')" class="text-zinc-400 opacity-60 group" wire:navigate.hover title="Requer Plano Diamante">
        <span class="flex items-center gap-1.5">
           Área Empresarial <span class="text-[11px]">💎</span>
            <flux:icon name="lock-closed" variant="micro" class="size-3 text-zinc-400" />

        </span>
    </flux:sidebar.item>
@endif

                {{-- Finance Connect --}}
                @php
                    $unreadSocial = \App\Models\SocialNotification::where('user_id', $user->id)->whereNull('read_at')->count();
                @endphp
                <flux:sidebar.item
                    icon="globe-alt"
                    href="/social"
                    wire:navigate
                    class="text-indigo-600 dark:text-indigo-400 font-black italic mt-1 group"
                >
                    Finance Connect
                    @if($unreadSocial > 0)
                        <span class="ml-auto text-[9px] font-black bg-indigo-600 text-white px-2 py-0.5 rounded-full">{{ $unreadSocial > 9 ? '9+' : $unreadSocial }}</span>
                    @else
                        <span class="ml-auto flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse opacity-0 group-hover:opacity-100 transition-opacity"></span>
                    @endif
                </flux:sidebar.item>

                <flux:separator class="my-4 mx-2" />

                <flux:sidebar.group heading="Finanças">
                    <flux:sidebar.item icon="arrow-trending-up" :href="route('hub.incomes')" wire:navigate.hover>
                        Receitas {!! $badge($counts['incomes']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="hand-raised" :href="route('hub.debts')" wire:navigate.hover>
                        Dívidas {!! $badge($counts['debts']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chart-bar-square" :href="route('hub.investments')" wire:navigate.hover>
                        Investimentos {!! $badge($counts['investments']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="credit-card" :href="route('hub.subscriptions')" wire:navigate.hover>
                        Assinaturas {!! $badge($counts['subscriptions']) !!}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('hub.networth')" wire:navigate.hover>
                        Património Real
                    </flux:sidebar.item>
                </flux:sidebar.group>

            @if($user->isStar() || $user->isDiamond())
    {{-- Acesso Liberado: Plano Premium ou superior --}}
    <flux:sidebar.item
        icon="sparkles"
        :href="route('ai')"
        :current="request()->routeIs('ai')"
        wire:navigate.hover
        class="text-brand-600 dark:text-brand-400 font-black"
    >
        CFO Inteligente
        <span class="ml-auto text-[8px] font-black bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded uppercase">IA</span>
    </flux:sidebar.item>
@else
    {{-- Bloqueado: Redireciona para Planos --}}
    <flux:sidebar.item
        icon="sparkles"
        :href="route('hub.pricing')"
        class="text-zinc-400 opacity-60 group"
        wire:navigate.hover
        title="Requer Plano Premium">
        <span class="flex items-center justify-between w-full">
            CFO Inteligente
            <span class="flex items-center gap-1 ml-auto">
                <span class="text-[10px]">⭐</span>
                <flux:icon name="lock-closed" variant="micro" class="size-3 text-zinc-400" />
            </span>
        </span>
    </flux:sidebar.item>
@endif


                <flux:sidebar.group heading="Ferramentas" class="mt-4">
<flux:sidebar.item
            icon="calendar-days"
            :href="route('hub.calendar')"
            :current="request()->routeIs('hub.calendar')"
            wire:navigate.hover
        >
            Calendário
        </flux:sidebar.item>
                    <flux:sidebar.item icon="clock" :href="route('hub.reminders')" :current="request()->routeIs('hub.reminders')" wire:navigate.hover>
    Lembretes {!! $badge($counts['reminders']) !!}
</flux:sidebar.item>

                    <flux:sidebar.item icon="user-group" :href="route('hub.family.manage')" :current="request()->routeIs('hub.family.manage')" wire:navigate.hover>
                        Família {!! $badge($counts['ranking']) !!}
                    </flux:sidebar.item>


                    <flux:sidebar.item icon="trophy" :href="route('hub.goals')" wire:navigate.hover>
                        Metas {!! $badge($counts['goals']) !!}
                    </flux:sidebar.item>

                </flux:sidebar.group>
{{-- ✅ ZONA DE TREINO — usa icon="bolt" nativo do Flux/Heroicons --}}
                <flux:sidebar.item
                    icon="bolt"
                    :href="route('hub.fitness')"
                    :current="request()->routeIs('hub.fitness')"
                    wire:navigate.hover
                    class="text-orange-500 dark:text-orange-400 font-black"
                >
                    Zona de Treino
                    <span class="ml-auto text-[8px] font-black bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400 px-1.5 py-0.5 rounded uppercase">Novo</span>
                </flux:sidebar.item>
                <flux:sidebar.item icon="chat-bubble-left-right" :href="route('support.hub')" wire:navigate.hover>
                    Suporte Técnico {!! $badge($counts['support']) !!}
                </flux:sidebar.item>

                {{-- Categorias --}}
                <flux:sidebar.group heading="Categorias" class="grid mt-4">
                    @if($isAnyPremium)
                    <flux:sidebar.item
                        href="/categories"
                        wire:navigate.hover
                        class="group mt-1 rounded-xl border border-emerald-500/20 bg-gradient-to-r from-emerald-500/10 to-green-500/10 hover:from-emerald-500/20 hover:to-green-500/20 transition-all duration-300 shadow-sm hover:shadow-emerald-500/20"
                    >
                        <div class="flex items-center gap-2">
                            <flux:icon name="adjustments-horizontal" class="size-4 text-emerald-500 transition-transform duration-300 group-hover:rotate-12" />
                            <span class="text-[10px] font-black uppercase tracking-[0.25em] text-emerald-600 dark:text-emerald-400">
                                Gerir Categorias
                            </span>
                        </div>
                    </flux:sidebar.item>
                    @else
                    <flux:sidebar.item
                        :href="route('hub.pricing')"
                        wire:navigate.hover
                        class="group mt-1 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 opacity-60"
                    >
                        <div class="flex items-center gap-2">
                            <flux:icon name="adjustments-horizontal" class="size-4 text-zinc-400" />
                            <span class="text-[10px] font-black uppercase tracking-[0.25em] text-zinc-400 flex items-center gap-1">
                                Gerir Categorias ⭐
                                <flux:icon name="lock-closed" variant="micro" class="size-3 text-zinc-400" />
                            </span>
                        </div>
                    </flux:sidebar.item>
                    @endif

                   @php
    $catCounts = is_array($catCounts ?? null) ? $catCounts : [];

    $exclude = ['Streaming (Vídeo/TV)', 'Música & Podcasts', 'Software & SaaS', 'Gaming', 'Fitness & Ginásio', 'Cloud & Armazenamento', 'Notícias & Revistas', 'Educação & Cursos', 'VPN & Segurança', 'Seguros & Finanças', 'Serviços Casa (Net/TV)', 'Outros'];

    $sidebarCategories = \App\Models\Category::where('workspace_id', $workspaceId)
        ->whereNotIn('name', $exclude)
        ->orderBy('order', 'asc')
        ->orderBy('name', 'asc')
        ->get(['id', 'name', 'slug', 'icon', 'color', 'is_fixed'])
        ->unique('name'); // <--- ADICIONA ISTO PARA REMOVER DUPLICADOS
@endphp

                    @foreach($sidebarCategories as $sidebarCat)
                        @php
                            $catSlug  = $sidebarCat->slug;
                            $catIcon  = $sidebarCat->icon ?? 'tag';
                            $lowerName = mb_strtolower($sidebarCat->name, 'UTF-8');
                            $catCount = $catCounts[$lowerName] ?? ($catCounts[$catSlug] ?? 0);
                            $catHref  = '/hub/' . $catSlug;
                        @endphp

                        <flux:sidebar.item :icon="$catIcon" :href="$catHref" wire:navigate.hover>
                            {{ $sidebarCat->name }}
                            @if($catCount > 0)
                                <span class="ml-auto text-[10px] font-black text-zinc-400 italic">#{{ $catCount }}</span>
                            @endif
                        </flux:sidebar.item>
                    @endforeach
                </flux:sidebar.group>

                @if($user->is_admin)
                    <flux:separator class="my-4 mx-2" />
                    <flux:sidebar.item icon="shield-check" :href="route('admin.dashboard')" class="text-purple-600 font-bold" wire:navigate.hover>
                        Painel Administrador
                    </flux:sidebar.item>
                @endif
            @endif
        </flux:sidebar.nav>

        <flux:spacer />

        <div class="px-3 mb-6">
            <button
                x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
                x-on:click="
                    darkMode = !darkMode;
                    document.documentElement.classList.add('theme-switching');
                    darkMode
                        ? (localStorage.theme = 'dark', document.documentElement.classList.add('dark'))
                        : (localStorage.theme = 'light', document.documentElement.classList.remove('dark'));
                    setTimeout(() => document.documentElement.classList.remove('theme-switching'), 200);
                "
                class="w-full glass-card flex items-center gap-3 px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm"
            >
                <flux:icon.sun x-show="darkMode" variant="outline" class="w-5 h-5 text-brand-400" />
                <flux:icon.moon x-show="!darkMode" variant="outline" class="w-5 h-5 text-zinc-500" />
                <span x-text="darkMode ? 'Modo Claro' : 'Modo Escuro'"></span>
            </button>
        </div>
    </flux:sidebar>




{{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- HEADER SUPERIOR FIXO (CENTRADO & COMPLETO)                     --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <flux:header class="z-40 border-b border-zinc-200/50 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-950/50 backdrop-blur-md flex justify-center">

        {{-- Botão Mobile - Posicionado de forma a não empurrar o centro --}}
        <flux:button
            class="lg:hidden ms-2 absolute left-2"
            variant="subtle"
            square
            icon="bars-2"
            x-on:click="mobileSidebarOpen = true"
            aria-label="Abrir menu"
        />

        {{-- Container Centrado: Alinhado com o Dashboard (max-w-6xl) --}}
        <div class="grid grid-cols-[auto_1fr_auto] items-center w-full max-w-6xl px-4 sm:px-6 lg:px-8 gap-3 mx-auto">

            {{-- Lado Esquerdo: Ícone Fitness --}}
            <div class="flex items-center">
                <a href="{{ route('hub.fitness') }}"
                    wire:navigate
                    class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-orange-500 hover:border-orange-500/30 transition-all shadow-sm group"
                    title="Zona de Treino">
                    <svg class="size-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </a>
            </div>






 {{-- Centro: Barra de Pesquisa --}}
  <div class="flex justify-center">
                <button x-on:click="$dispatch('open-global-search')" class="flex items-center gap-3 px-4 py-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all group w-full shadow-sm">
                    <flux:icon name="magnifying-glass" class="size-4 group-hover:text-brand-500 transition-colors" />
                    <span class="text-sm font-medium text-left flex-1">Procurar tudo... (Ctrl K)</span>
                </button>
            </div>






            {{-- Lado Direito: Ações e Perfil --}}
<div class="flex items-center gap-2 sm:gap-4 justify-end">

   {{-- Botão de Privacidade (Atualizado para aparecer no perfil) --}}
{{-- Botão de Privacidade (Deve incluir profile.edit) --}}
@if(request()->routeIs('dashboard') || request()->routeIs('social.profile') || request()->routeIs('profile.edit'))
    <button @click="privacyMode = !privacyMode"
        class="p-2.5 rounded-xl bg-white dark:bg-zinc-900 shadow-sm border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-emerald-600 transition-all">
        {{-- Ícone muda conforme o estado da variável privacyMode --}}
        <flux:icon x-show="!privacyMode" name="eye" class="size-5" />
        <flux:icon x-show="privacyMode" name="eye-slash" class="size-5 text-emerald-500" />
    </button>
@endif
    {{-- Centro de Notificações --}}
    <livewire:notification-center />

    {{-- Menu de Utilizador --}}
    <div x-data="{ open: false }" class="relative flex-shrink-0">
        <button @click="open = !open" type="button" class="flex items-center gap-2 sm:gap-3 px-1 sm:px-2 py-1.5 rounded-2xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">
            <flux:avatar :initials="auth()->user()->initials()" class="size-9 sm:size-10 shadow-sm" />
            <div class="hidden md:block text-left">
                <p class="text-sm font-bold text-zinc-800 dark:text-white leading-none">
                    {{ auth()->user()->name }} <span class="ml-1">{{ $planEmoji }}</span>
                </p>
                <p class="text-[10px] font-black uppercase {{ $planColor }} mt-1">
                    {{ $planText }}
                </p>
            </div>
            <flux:icon name="chevron-down" class="size-4 text-zinc-400" />
        </button>

        {{-- Dropdown do Menu --}}
        <div x-show="open" x-cloak @click.outside="open = false" x-transition
            class="absolute right-0 mt-2 w-60 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl z-50 overflow-hidden">
            <div class="py-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
                    👤 O meu perfil
                </a>

                {{-- EXIBIÇÃO DE PLANO (Apenas se for Diamante ou Star) --}}
                @if($user->isDiamond() || $user->isStar())
                    <a href="{{ route('hub.pricing') }}" wire:navigate
                       class="w-full flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 transition-colors border-b border-zinc-50 dark:border-zinc-800/50">
                        @if($user->isDiamond())
                            <span class="text-indigo-500">💎</span> O meu plano Diamante
                        @else
                            <span class="text-amber-500">⭐</span> O meu plano Premium
                        @endif
                    </a>
                @endif

               {{-- Link de Suporte (Apenas se NÃO for Admin) --}}
@if(!auth()->user()->isAdmin())
    <a href="{{ $isBusinessMode ? route('hub.business.support') : route('support.hub') }}"
        wire:navigate
        class="w-full flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
        <span>🎫</span> Suporte Técnico
    </a>
@endif

                {{-- BOTÃO DE UPGRADE (Apenas para Plano Básico E se NÃO for Admin) --}}
@if(!$isAnyPremium && !auth()->user()->isAdmin())
    <a href="{{ route('hub.pricing') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 text-sm text-brand-600 font-black hover:bg-brand-50 dark:hover:bg-brand-500/10">
        <span class="animate-pulse">💎</span> OBTER PREMIUM
    </a>
@endif

                <div class="border-t border-zinc-200 dark:border-zinc-800 my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-500 font-bold hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
                        🚪 Sair da Conta
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>









        </div>
    </flux:header>

    <flux:main class="lg:ps-64 h-screen overflow-y-auto custom-scrollbar pt-16">
        <div id="page-container" class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            {{ $slot }}
        </div>
    </flux:main>

    {{-- Desregista qualquer SW existente --}}
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(regs => {
            regs.forEach(r => r.unregister());
        });
    }
    </script>

    @persist('finance-bot')
        <livewire:finance-bot />
    @endpersist

    @persist('toast')
        <flux:toast.group><flux:toast /></flux:toast.group>
    @endpersist





 {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL DE DETALHES DO AVISO (CENTRADO REAL) --}}
    {{-- ═══════════════════════════════════════════ --}}
    <flux:modal name="announcement-detail" variant="center" class="md:w-[600px] !p-0 overflow-hidden border-none shadow-2xl">
        <div x-data="{
            id: null, title: '', message: '', type: '', date: '', expires: '',
            get getColor() {
                return {
                    'info': 'bg-blue-600', 'informativo': 'bg-blue-600',
                    'success': 'bg-emerald-600', 'sucesso': 'bg-emerald-600',
                    'warning': 'bg-amber-500', 'aviso': 'bg-amber-500',
                    'danger': 'bg-red-600', 'urgente': 'bg-red-600'
                }[this.type] || 'bg-zinc-600';
            }
        }"
        x-on:show-announcement.window="id = $event.detail.id; title = $event.detail.title; message = $event.detail.message; type = $event.detail.type.toLowerCase(); date = $event.detail.date; expires = $event.detail.expires"
        class="text-left bg-white dark:bg-zinc-900 flex flex-col">

            <div class="h-2 w-full shadow-inner" :class="getColor"></div>

            <div class="p-10">
                <div class="flex items-start justify-between mb-10">
                    <div class="flex items-center gap-6">
                        <div class="size-16 rounded-[1.5rem] flex items-center justify-center shadow-xl text-white" :class="getColor">
                            <flux:icon name="megaphone" class="size-8" />
                        </div>
                        <div class="text-left">
                            <h2 class="text-3xl font-black uppercase italic tracking-tighter dark:text-white leading-none mb-2" x-text="title"></h2>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest text-white shadow-sm" :class="getColor" x-text="type"></span>
                                <span class="text-[11px] text-zinc-400 font-black uppercase tracking-widest" x-text="date"></span>
                            </div>
                        </div>
                    </div>
                    <flux:modal.close><flux:button variant="ghost" icon="x-mark" class="rounded-full" /></flux:modal.close>
                </div>

                <div class="space-y-8">
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-800/40 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800 shadow-inner">
                        <p class="text-zinc-600 dark:text-zinc-300 leading-relaxed text-lg font-bold italic" x-text="message"></p>
                    </div>

                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-zinc-400 px-2">
                        <span>Validade do Aviso:</span>
                        <span class="text-zinc-900 dark:text-white" x-text="expires"></span>
                    </div>

                    <div class="flex gap-4">
                        <template x-if="type !== 'urgente' && type !== 'danger' && type !== 'erro'">
                            <flux:button class="flex-1 h-14 rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-lg hover:scale-[1.02] transition-transform"
                                x-on:click="$dispatch('close-announcement-globally', { id: id }); $dispatch('modal-close', {name: 'announcement-detail'})">
                                Entendido, remover aviso
                            </flux:button>
                        </template>

                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full h-14 rounded-2xl font-black uppercase text-[11px] tracking-widest border-2 border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                Sair do Dossiê
                            </flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            </div>
        </div>
    </flux:modal>



 @fluxScripts
    @livewireScripts
    <script src="/flux/flux.js"></script>

<script>
if (!window.__appLayoutInitialized) {
    window.__appLayoutInitialized = true;

    window.adjustPageContainer = function() {
        const el = document.getElementById('page-container');
        if (!el) return;
        const isSocial = location.pathname.startsWith('/social');
        el.style.maxWidth = isSocial ? '100rem' : '';
        el.style.paddingLeft = isSocial ? '1rem' : '';
        el.style.paddingRight = isSocial ? '1rem' : '';
        el.style.paddingTop = isSocial ? '1.5rem' : '';
    };

    document.addEventListener('livewire:navigated', window.adjustPageContainer);

    document.addEventListener('livewire:init', () => {

        // ABRIR CHAT
        Livewire.on('openChatWith', (data) => {
            Livewire.dispatch('open-chat-with', { userId: data.userId });
        });

        // COPIAR LINK
        Livewire.on('copyToClipboard', (data) => {
            navigator.clipboard.writeText(data.text);
        });

    });

    window.normalizeAppShellLayout = function() {
        const main = document.querySelector('[data-flux-main]');
        if (!main) return;

        main.scrollLeft = 0;
        document.documentElement.scrollLeft = 0;
        document.body.scrollLeft = 0;

        requestAnimationFrame(() => {
            main.style.transform = 'translateZ(0)';
            requestAnimationFrame(() => {
                main.style.transform = '';
            });
        });
    };

    window.addEventListener('load', window.normalizeAppShellLayout);
    document.addEventListener('livewire:navigated', window.normalizeAppShellLayout);
}

window.adjustPageContainer && window.adjustPageContainer();
</script>
{{-- MODAL PRIVACIDADE --}}
    <flux:modal wire:model="showPrivacyModal" position="center" class="md:w-[350px]">
        <div class="space-y-6 text-center">
            <h2 class="text-xl font-black uppercase italic text-zinc-900">Confirmar Identidade</h2>
            <form wire:submit.prevent="unlockPrivacy" class="space-y-4">
                <flux:input wire:model="privacyPassword" type="password" placeholder="Tua password..." class="rounded-xl" />
                <flux:button type="submit" variant="primary" class="w-full bg-emerald-600 border-none font-black uppercase">Desbloquear 🟢</flux:button>
            </form>
        </div>
    </flux:modal>

    {{-- MODAL REPORTE --}}
    @if($reportModal ?? false)
        <div class="fixed inset-0 z-[500] flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-md">
            <div class="w-full max-w-lg bg-white rounded-[2.5rem] p-8 shadow-2xl">
                <h2 class="text-xl font-black text-zinc-900 uppercase italic mb-6">Reportar Publicação</h2>
                <textarea wire:model="reportReason" class="w-full bg-zinc-50 border border-zinc-200 rounded-2xl p-5 text-sm mb-6 h-40 outline-none" placeholder="Motivo da denúncia..."></textarea>
                <div class="flex gap-4">
                    <button wire:click="$set('reportModal', false)" class="flex-1 font-black uppercase text-zinc-400">Cancelar</button>
                    <button wire:click="submitReport" class="flex-[2] bg-emerald-600 text-white h-14 rounded-2xl font-black uppercase shadow-lg">Enviar Ticket 🟢</button>
                </div>
            </div>
        </div>
    @endif
</body>
</html>
