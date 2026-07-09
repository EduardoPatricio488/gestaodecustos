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













@php
    $user = auth()->user();
    $currentWs = $user ? $user->currentWorkspace : null;
// --- LÓGICA DE MODO DE OBSERVAÇÃO ---
$myPersonalWsId = $user->workspaces()->where('type', 'personal')->first()?->id;
// Verificamos se o espaço atual não é o teu pessoal e se é do tipo 'personal' (ou seja, gestão de outra pessoa)
$isViewingOthers = $currentWs && $myPersonalWsId && ($currentWs->id !== $myPersonalWsId) && ($currentWs->type === 'personal');
    // 1. DEFINIÇÃO GLOBAL DE EMPRESAS
    $allBusinessWs = ($user)
        ? $user->workspaces()->where('type', '!=', 'personal')->get()
        : collect();

    $myCompanies = $allBusinessWs->filter(fn($ws) => $ws->pivot->role === 'admin');
    $collabCompanies = $allBusinessWs->filter(fn($ws) => $ws->pivot->role !== 'admin');

    // 2. MODOS DE VISUALIZAÇÃO E "SHADOW MODE"
    $isViewingAsCollab = session()->has('viewing_as_collaborator_id'); // Deteta se o CEO está a espreitar
    $isAdminMode = request()->routeIs('admin.*');

    $isBusinessMode = (
        request()->routeIs('hub.business.*') ||
        request()->routeIs('company-expenses')
    )
    && !request()->routeIs('hub.business.gateway')
    && !$isAdminMode;

    // 3. LÓGICA DE PERMISSÃO (SideBar Dinâmica)
    // Se o CEO estiver em "Shadow Mode", o $isManager passa a ser FALSE para a sidebar mostrar apenas o menu do colaborador
    $isManager = ($user && ($user->isAdminRole() || $user->isOwner())) && !$isViewingAsCollab;

    // 4. PLANOS E PERMISSÕES
    $userPlan = $currentWs->plan ?? 'free';
    $isDiamond = $user ? $user->isDiamond() : false;
    $isAnyPremium = $user ? $user->isAnyPremium() : false;

    if ($isDiamond) {
        $planEmoji = '💎'; $planText = 'Plano Diamante'; $planColor = 'text-indigo-600';
    } elseif ($user && $user->isStar()) {
        $planEmoji = '⭐'; $planText = 'Plano Premium'; $planColor = 'text-amber-500';
    } else {
        $planEmoji = '👤'; $planText = 'Plano Básico'; $planColor = 'text-zinc-400';
    }

    $workspaceId = $currentWs?->id ?? 0;

    // 4. CACHE DE CONTAGENS DA SIDEBAR
    $counts = \Illuminate\Support\Facades\Cache::remember(
        "layout:sidebar-counts:v2:{$user?->id}:{$workspaceId}:{$isAdminMode}:{$isBusinessMode}",
        60,
        function () use ($user, $currentWs, $workspaceId, $isAdminMode, $isBusinessMode) {
            if ($isAdminMode) {
                return [
                    'users'   => \App\Models\User::count(),
                    'support' => \App\Models\SupportTicket::where('status', 'open')->count(),
                ];
            }

            if ($isBusinessMode) {
                return [
                    'company_expenses' => \App\Models\Expense::where('workspace_id', $workspaceId)->count(),
                    'invoices'   => \App\Models\Invoice::where('workspace_id', $workspaceId)->count(),
                    'proposals'  => \App\Models\Proposal::where('workspace_id', $workspaceId)->count(),
                    'clients'    => \App\Models\Client::where('workspace_id', $workspaceId)->count(),
                    'projects'   => \App\Models\Project::where('workspace_id', $workspaceId)->count(),
                    'inventory'  => \App\Models\Product::where('workspace_id', $workspaceId)->count(),
                    'accounts'   => \App\Models\BankAccount::where('workspace_id', $workspaceId)->count(),
                    'suppliers'  => \App\Models\Supplier::where('workspace_id', $workspaceId)->count(),
                    'employees'  => \App\Models\Employee::where('workspace_id', $workspaceId)->count(),
                    'tasks'      => \App\Models\Task::where('workspace_id', $workspaceId)->where('status', '!=', 'concluido')->count(),
                    'absences'   => \App\Models\Absence::where('workspace_id', $workspaceId)->where('status', 'pendente')->count(),
                    'support'    => \App\Models\SupportTicket::where('status', 'open')->where('workspace_id', $workspaceId)->count(),
                ];
            }

            return [
                'incomes'       => \App\Models\Income::where('workspace_id', $workspaceId)->count(),
                'investments'   => \App\Models\Investment::where('workspace_id', $workspaceId)->count(),
                'subscriptions' => \App\Models\Subscription::where('workspace_id', $workspaceId)->count(),
                'activity'      => \App\Models\ActivityLog::where('workspace_id', $workspaceId)->count(),
                'goals'         => \App\Models\Goal::where('workspace_id', $workspaceId)->count(),
                'debts'         => \App\Models\Debt::where('workspace_id', $workspaceId)->count(),
                'reminders'     => \App\Models\Reminder::where('workspace_id', $workspaceId)->where('is_completed', false)->count(),
                'members'       => $currentWs?->users()->count() ?? 0,
                'ranking'       => $currentWs?->users()->count() ?? 0,
                'support'       => \App\Models\SupportTicket::where('status', 'open')->count(),
            ];
        }
    );

    // 5. CACHE DE CATEGORIAS
    $catCounts = \Illuminate\Support\Facades\Cache::remember(
        "layout:category-counts:v10:{$workspaceId}",
        60,
        fn () => \App\Models\Expense::selectRaw('categories.name, count(*) as total')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.workspace_id', $workspaceId)
            ->whereMonth('spent_at', now()->month)
            ->whereYear('spent_at', now()->year)
            ->groupBy('categories.name')
            ->pluck('total', 'name')
            ->mapWithKeys(fn ($item, $key) => [mb_strtolower(trim($key), 'UTF-8') => $item])
            ->toArray()
    );

    // 6. HELPER DE BADGES
    $badge = fn ($val) =>
        $val > 0
            ? '<span class="ml-auto text-[10px] font-black bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-full text-zinc-500 group-hover:text-brand-600 transition-colors">'.$val.'</span>'
            : '';


@endphp













<body

    class="layout-fixed app-shell antialiased bg-zinc-50 dark:bg-zinc-950 h-screen overflow-hidden flex"
    x-data="{
        privacyMode: localStorage.getItem('privacyMode') === 'true',
        mobileSidebarOpen: false
    }"
    x-init="$watch('privacyMode', v => localStorage.setItem('privacyMode', v))"
    x-on:privacy-changed.window="privacyMode = $event.detail.state"
    :class="{ 'privacy-mode': privacyMode }"
>
 {{-- Se estiver na conta de outros, mete uma linha amarela no topo de tudo --}}
    @if($isViewingOthers && $currentWs->type !== 'business')
        <div class="fixed top-0 left-0 right-0 h-1 bg-amber-500 z-[100]"></div>
    @endif

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
    {{-- LOGO DINÂMICO --}}
    <a href="{{ route('dashboard') }}" wire:navigate.hover class="flex items-center gap-3 group no-underline">

        {{-- Container da Imagem ou Inicial --}}
        <div class="shrink-0 size-9 rounded-xl overflow-hidden bg-emerald-600 flex items-center justify-center text-white shadow-lg group-hover:scale-105 transition-transform duration-300">
            @if($currentWs && $currentWs->logo_path)
                {{-- Mostra a foto se ela existir --}}
                <img src="{{ $currentWs->logo_url }}?t={{ time() }}" class="size-full object-cover">
            @else
                {{-- Mostra a inicial se não houver foto --}}
                <span class="text-lg font-black italic">
                    {{ substr($currentWs->name ?? 'F', 0, 1) }}
                </span>
            @endif
        </div>

        {{-- Nome do Workspace --}}
        <div class="flex flex-col justify-center min-w-0">
            <span class="text-[13px] font-black uppercase tracking-tighter text-zinc-800 dark:text-white leading-[1.1] whitespace-normal break-words">
                {{ $currentWs->name ?? 'Finance Pro' }}
            </span>
            <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-[0.2em] mt-1">
                @if($isAdminMode) Administração
                @elseif($isBusinessMode) Negócio
                @else Gestão Pessoal
                @endif
            </span>
        </div>
    </a>

    <flux:button
        class="lg:hidden"
        variant="subtle"
        square
        icon="x-mark"
        x-on:click="mobileSidebarOpen = false"
    />
</flux:sidebar.header>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
















<flux:sidebar.nav>
      @if($isViewingOthers)
        {{-- BOTÃO DE RETORNO RÁPIDO NA SIDEBAR --}}
        <flux:sidebar.item
            icon="arrow-left-circle"
            :href="route('workspace.switch.fast', $myPersonalWsId)"
            class="text-amber-600 dark:text-amber-500 font-black animate-in slide-in-from-left-4"
        >
            Sair da Gestão Externa
        </flux:sidebar.item>
        <flux:separator class="my-4 mx-2" />
    @endif
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
                        <flux:sidebar.item icon="shopping-bag" :href="route('admin.store')" :current="request()->routeIs('admin.store')" wire:navigate>
                            Loja
                        </flux:sidebar.item>
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

    @if(session()->has('viewing_as_collaborator_id'))
        {{-- BOTÃO 1: Se estiveres a ver a Maria, este botão volta a mostrar o Eduardo --}}
        <flux:sidebar.item
            icon="arrow-uturn-left"
            wire:click="stopViewingAsCollaborator"
            class="text-red-500 font-black animate-pulse cursor-pointer"
        >
            Sair da Vista Colab
        </flux:sidebar.item>
    @else
        {{-- BOTÃO 2: Se fores o CEO normal, este botão volta ao teu Dashboard Pessoal --}}
        <flux:sidebar.item
            icon="arrow-left-circle"
            :href="route('hub.business.exit')"
            class="text-zinc-500 font-bold"
        >
            Sair do Modo Empresa
        </flux:sidebar.item>
    @endif


    <flux:separator class="my-4 mx-2" />





    {{-- LÓGICA DE FILTRO: CEO/ADMIN vs COLABORADOR --}}
    @if($isManager)

        {{-- ==========================================
             VISTA: CEO / OWNER (ACESSO TOTAL)
        ========================================== --}}
        <flux:sidebar.item icon="chart-pie" :href="route('hub.business.dashboard')" :current="request()->routeIs('hub.business.dashboard')" wire:navigate.hover>
            Dashboard Business
        </flux:sidebar.item>

        <flux:sidebar.group heading="Gestão de Operações" class="mt-4">
            <flux:sidebar.item icon="building-office-2" :href="route('company-expenses')" wire:navigate.hover>
                Custos Operacionais {!! $badge($counts['company_expenses']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="document-text" :href="route('hub.business.invoices')" wire:navigate.hover>
                Faturação & Vendas {!! $badge($counts['invoices']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="newspaper" :href="route('hub.business.proposals')" wire:navigate.hover>
                Propostas {!! $badge($counts['proposals']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="user-group" :href="route('hub.business.clients')" wire:navigate.hover>
                Clientes (CRM) {!! $badge($counts['clients']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="briefcase" :href="route('hub.business.projects')" wire:navigate.hover>
                Projetos {!! $badge($counts['projects']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="presentation-chart-line" :href="route('hub.business.pnl')" wire:navigate.hover>
                Resultados (P&L)
            </flux:sidebar.item>
            <flux:sidebar.item icon="chart-bar" :href="route('hub.business.cashflow')" wire:navigate.hover>
                Fluxo de Caixa
            </flux:sidebar.item>
            <flux:sidebar.item icon="presentation-chart-line" :href="route('hub.business.costs')" wire:navigate.hover>
    Análise de Custos
</flux:sidebar.item>
            <flux:sidebar.item icon="clipboard-document-check" :href="route('hub.business.expense-approvals')" wire:navigate.hover>
                Aprovações
            </flux:sidebar.item>
            <flux:sidebar.item icon="document-text" :href="route('hub.business.at-invoices')" wire:navigate.hover>
                e-Fatura AT
            </flux:sidebar.item>
            <flux:sidebar.item icon="archive-box" :href="route('hub.business.inventory')" wire:navigate.hover>
                Stock / Inventário {!! $badge($counts['inventory']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="building-library" :href="route('hub.business.accounts')" wire:navigate.hover>
                Contas Empresa {!! $badge($counts['accounts'] ?? 0) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="truck" :href="route('hub.business.suppliers')" wire:navigate.hover>
                Fornecedores {!! $badge($counts['suppliers']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="check-badge" :href="route('hub.business.tasks')" wire:navigate.hover>
                Tarefas & Equipa {!! $badge($counts['tasks']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="command-line" :href="route('hub.business.timeline')" wire:navigate.hover>
                Timeline Operações
            </flux:sidebar.item>
            <flux:sidebar.item icon="chat-bubble-left-right" :href="route('hub.business.messenger')" wire:navigate.hover>
                Messenger Equipa
            </flux:sidebar.item>
            <flux:sidebar.item icon="folder-open" :href="route('hub.business.vault')" wire:navigate.hover>
                Arquivo Digital
            </flux:sidebar.item>
            <flux:sidebar.item icon="sparkles" :href="route('hub.business.ai')" wire:navigate.hover class="text-brand-600 dark:text-brand-400 font-bold">
                IA Estrategista
            </flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.item icon="calendar-days" :href="route('hub.business.calendar')" wire:navigate.hover>
            Calendário Global
        </flux:sidebar.item>

        <flux:sidebar.group heading="Administrativo" class="mt-4">
            <flux:sidebar.item icon="users" :href="route('hub.business.team')" wire:navigate.hover>
                Equipa {!! $badge($counts['employees']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item
    icon="user-plus"
    :href="route('hub.business.recruitment')"
    wire:navigate.hover
>
    Recrutamento
</flux:sidebar.item>
            <flux:sidebar.item icon="calendar-days" :href="route('hub.business.absences')" wire:navigate.hover>
                Férias & Ausências {!! $badge($counts['absences']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="receipt-percent" :href="route('hub.business.taxes')" wire:navigate.hover>
                Impostos & IVA
            </flux:sidebar.item>

            <flux:sidebar.item icon="chat-bubble-left-right" :href="route('hub.business.support')" wire:navigate.hover>
                Suporte Técnico {!! $badge(\App\Models\SupportTicket::where('status', 'open')->where('workspace_id', $currentWs?->id)->count()) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="adjustments-horizontal" :href="route('hub.business.settings')" wire:navigate.hover>
                Perfil do Negócio
            </flux:sidebar.item>
        </flux:sidebar.group>

    @else
        {{-- ==========================================
             VISTA: COLABORADOR (RESTRITO OPERACIONAL)
        ========================================== --}}
        <flux:sidebar.item icon="home" :href="route('hub.business.dashboard')" wire:navigate.hover>
            Meu Terminal
        </flux:sidebar.item>

        <flux:sidebar.group heading="O Meu Trabalho" class="mt-4">
            <flux:sidebar.item icon="check-badge" :href="route('hub.business.tasks')" wire:navigate.hover>
                As Minhas Tarefas {!! $badge($counts['tasks']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="briefcase" :href="route('hub.business.projects')" wire:navigate.hover>
                Os Meus Projetos
            </flux:sidebar.item>
              {{-- NOVO ITEM: GASTOS OPERACIONAIS --}}
    <flux:sidebar.item icon="banknotes" :href="route('hub.business.my-expenses')" wire:navigate.hover>
        Notas de Gastos
    </flux:sidebar.item>
            <flux:sidebar.item icon="calendar-days" :href="route('hub.business.calendar')" wire:navigate.hover>
                Calendário
            </flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.group heading="Comunicação" class="mt-4">
            <flux:sidebar.item icon="chat-bubble-left-right" :href="route('hub.business.messenger')" wire:navigate.hover>
                Messenger Equipa
            </flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.group heading="Recursos" class="mt-4">
            <flux:sidebar.item icon="identification" :href="route('hub.business.my-profile')" :current="request()->routeIs('hub.business.my-profile')" wire:navigate.hover>
                A Minha Ficha
            </flux:sidebar.item>

            <flux:sidebar.item icon="clock" :href="route('hub.business.absences')" wire:navigate.hover>
                As minhas férias / Faltas
            </flux:sidebar.item>
            <flux:sidebar.item icon="chat-bubble-left-right" :href="route('hub.business.support')" wire:navigate.hover>
                Suporte Interno
            </flux:sidebar.item>
        </flux:sidebar.group>
    @endif










            @else


{{-- ═══════════════════════════════════════════ --}}
{{-- MENU PESSOAL (MODO NORMAL)                  --}}
{{-- ═══════════════════════════════════════════ --}}
<flux:sidebar.item icon="squares-2x2" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate.hover>
    Dashboard
</flux:sidebar.item>
{{-- Lógica de Acesso à Área Empresarial (COM ESTADO BLOQUEADO PARA PLANO BÁSICO) --}}
@if(!$isBusinessMode)
    <div class="px-4 mt-6 mb-4">
        @if($user->isDiamond())
            {{-- UTILIZADOR DIAMANTE/BUSINESS: MOSTRA MENU NORMAL --}}
            <div x-data="{ openBusiness: false }" class="w-full text-center">
                {{-- BOTÃO PRINCIPAL --}}
                <button
                    @click="openBusiness = !openBusiness"
                    class="w-full flex items-center justify-center gap-2.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-brand-500/40 transition-all group relative overflow-hidden"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <flux:icon name="building-office" class="size-4 text-brand-600 group-hover:scale-110 transition-transform duration-300" />
                    <span class="text-[10px] font-black uppercase tracking-widest text-brand-600 dark:text-brand-400 italic leading-none">
                        Área Empresarial
                    </span>
                    <flux:icon name="chevron-down" variant="micro" class="size-3 text-zinc-400 transition-transform duration-300" x-bind:class="openBusiness ? 'rotate-180' : ''" />
                </button>

                {{-- ABA EXPANSÍVEL (SUBMENU) --}}
                <div x-show="openBusiness" x-cloak x-transition class="mt-3 space-y-1 border-t border-zinc-100 dark:border-zinc-800 pt-3 text-left">
                    {{-- MINHAS EMPRESAS (CEO) --}}
                    @if($myCompanies->count() > 0)
                        <p class="text-[8px] font-black uppercase text-zinc-400 tracking-widest text-center mb-2 opacity-60">Negócios Próprios</p>
                        @foreach($myCompanies as $ws)
                            <flux:sidebar.item
                                icon="building-office-2"
                                :href="route('workspace.switch.fast', $ws->id)"
                                class="justify-center text-[11px] font-bold py-2 {{ $currentWs?->id === $ws->id ? 'text-brand-600' : '' }}"
                            >
                                {{ $ws->name }}
                            </flux:sidebar.item>
                        @endforeach
                    @endif

                    {{-- ONDE SOU COLABORADOR --}}
                    @if($collabCompanies->count() > 0)
                        <p class="text-[8px] font-black uppercase text-zinc-400 tracking-widest text-center mt-4 mb-2 opacity-60">Como Colaborador</p>
                        @foreach($collabCompanies as $ws)
                            <flux:sidebar.item
                                icon="users"
                                :href="route('workspace.switch.fast', $ws->id)"
                                class="justify-center text-[11px] font-bold py-2 {{ $currentWs?->id === $ws->id ? 'text-emerald-600' : '' }}"
                            >
                                {{ $ws->name }}
                            </flux:sidebar.item>
                        @endforeach
                    @endif

                    {{-- BOTÃO NOVO ESPAÇO --}}
                    <a href="{{ route('hub.business.gateway', ['new' => 1]) }}"
                       wire:navigate
                       class="flex items-center justify-center gap-2 w-full py-3 mt-2 border-t border-dashed border-zinc-100 dark:border-zinc-800 text-brand-600 dark:text-brand-400 hover:opacity-70 transition-all no-underline group"
                    >
                        <flux:icon name="plus" class="size-3 group-hover:scale-110 transition-transform" />
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Novo Espaço</span>
                    </a>
                </div>
            </div>
        @else
            {{-- UTILIZADOR BÁSICO: MOSTRA OPÇÃO BLOQUEADA --}}
            <a href="{{ route('hub.pricing') }}" {{-- Altere para a sua rota de planos/pagamentos --}}
               wire:navigate
               class="w-full flex items-center justify-center gap-2.5 py-2.5 rounded-2xl bg-zinc-100/50 dark:bg-zinc-900/50 border border-zinc-200/60 dark:border-zinc-800/60 transition-all group opacity-70 hover:opacity-100 hover:border-amber-500/50"
               title="Disponível no Plano Business"
            >
                <div class="relative">
                    <flux:icon name="building-office" class="size-4 text-zinc-400 group-hover:text-amber-500 transition-colors" />
                    <div class="absolute -top-1.5 -right-1.5 bg-white dark:bg-zinc-950 rounded-full p-0.5">
                        <flux:icon name="lock-closed" variant="micro" class="size-2.5 text-amber-600" />
                    </div>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500 group-hover:text-zinc-700 dark:group-hover:text-zinc-300 italic leading-none">
                    Área Empresarial
                </span>
                <span class="text-[8px] bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-500 px-1.5 py-0.5 rounded-md font-bold ml-1 uppercase">Pro</span>
            </a>
        @endif
    </div>
@endif



{{-- COPIA DESDE AQUI --}}
@php
    // Definição segura das variáveis de plano para evitar erros de variável indefinida
    $isProUser = ($user->plan ?? '') === 'pro' || (method_exists($user, 'isDiamond') && $user->isDiamond());
    $isPlusUser = ($user->plan ?? '') === 'plus' || (method_exists($user, 'isPlus') && $user->isPlus());
    $hasLockInAccess = $isProUser || $isPlusUser;

$hasInventoryAccess = $isProUser || $isPlusUser;
$hasStoreAccess = $isProUser || $isPlusUser;
@endphp

@if($hasLockInAccess)
    {{-- ITEM COM ACESSO LIBERTADO --}}
    <flux:sidebar.item
        icon="lock-closed"
        :href="route('hub.lockin')"
        :current="request()->routeIs('hub.lockin')"
        wire:navigate.hover
        class="font-black group"
        style="color: #10b981;"
    >
        <div class="flex items-center gap-2 flex-1">
            <span style="background: linear-gradient(135deg, #10b981, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Lock In
            </span>
            {{-- Etiqueta do Plano --}}
            <span class="text-[6px] px-1 py-0.5 rounded font-black uppercase leading-none {{ $isProUser ? 'bg-violet-500 text-white' : 'bg-emerald-500 text-white' }}">
                {{ $isProUser ? 'Business' : 'Premium' }}
            </span>
        </div>

    </flux:sidebar.item>
@else
    {{-- ITEM BLOQUEADO (REDIRECIONA PARA PLANOS) --}}
    <flux:sidebar.item
        icon="lock-closed"
        :href="route('hub.pricing')"
        wire:navigate
        class="font-black opacity-60 grayscale group"
    >
        <div class="flex items-center justify-between w-full gap-2">
            <div class="flex items-center gap-1.5">
                <span class="text-zinc-500">Lock In</span>
                <flux:icon name="lock-closed" variant="micro" class="size-3 text-amber-600/80" />
            </div>

            <span class="text-[7px] font-black px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-900/20 text-amber-600 border border-amber-200/50 uppercase tracking-tighter">
                Upgrade
            </span>
        </div>
    </flux:sidebar.item>
@endif
{{-- ATÉ AQUI --}}


@php
    // Verifica acesso ao Inventário (Disponível em Plus e Business/Diamond)
    $hasInventoryAccess = ($user->plan ?? '') === 'plus' ||
                         ($user->plan ?? '') === 'pro' ||
                         (method_exists($user, 'isPlus') && $user->isPlus()) ||
                         (method_exists($user, 'isDiamond') && $user->isDiamond());
@endphp

@if($hasInventoryAccess)
    {{-- INVENTÁRIO DISPONÍVEL --}}
    <flux:sidebar.item
        icon="archive-box"
        :href="route('hub.inventory')"
        :current="request()->routeIs('hub.inventory')"
        wire:navigate
    >
        O Meu Inventário
    </flux:sidebar.item>
@else
    {{-- INVENTÁRIO BLOQUEADO --}}
    <flux:sidebar.item
        icon="archive-box"
        :href="route('hub.pricing')"
        wire:navigate
        class="opacity-60 grayscale group"
    >
        <div class="flex items-center justify-between w-full gap-2">
            <div class="flex items-center gap-1.5">
                <span class="text-zinc-500">O Meu Inventário</span>
                <flux:icon name="lock-closed" variant="micro" class="size-3 text-amber-600/80" />
            </div>

            <span class="text-[7px] font-black px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-900/20 text-amber-600 border border-amber-200/50 uppercase tracking-tighter">
                Plus
            </span>
        </div>
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
                    <flux:sidebar.item icon="calculator" :href="route('hub.budget')" :current="request()->routeIs('hub.budget')" wire:navigate.hover>
                        Orçamento
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-up-tray" :href="route('hub.import')" :current="request()->routeIs('hub.import')" wire:navigate.hover>
                        Importar Extrato
                    </flux:sidebar.item>
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
                    <flux:sidebar.item
                        icon="building-library"
                        :href="route('hub.banco')"
                        :current="request()->routeIs('hub.banco')"
                        wire:navigate.hover
                        class="font-black text-emerald-700 dark:text-emerald-400"
                    >
                        Banco
                        <span class="ml-auto text-[8px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-1.5 py-0.5 rounded uppercase">Novo</span>
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
                    <flux:sidebar.item icon="sparkles" :href="route('hub.wrapped')" :current="request()->routeIs('hub.wrapped')" wire:navigate.hover>
                        Wrapped {{ now()->year }}
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

               @php
    // Detecção simplificada e segura dos planos
    $isProUser = ($user->plan ?? '') === 'pro' || (method_exists($user, 'isDiamond') && $user->isDiamond());
    $isPlusUser = ($user->plan ?? '') === 'plus' || (method_exists($user, 'isPlus') && $user->isPlus());
    $hasLockInAccess = $isProUser || $isPlusUser;
@endphp



                <flux:sidebar.item icon="chat-bubble-left-right" :href="route('support.hub')" wire:navigate.hover>
                    Suporte Técnico {!! $badge($counts['support']) !!}
                </flux:sidebar.item>

                {{-- Categorias --}}
                <flux:sidebar.group heading="Categorias" class="grid mt-4">
                    @if($isAnyPremium)
                    <flux:sidebar.item
                        :href="route('categories')"
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

    \App\Models\Category::backfillMissingSlugs($workspaceId ?: null);

    $sidebarCategories = \App\Models\Category::where('workspace_id', $workspaceId)
        ->whereNotIn('name', $exclude)
        ->whereNotNull('slug')
        ->where('slug', '!=', '')
        ->orderBy('order', 'asc')
        ->orderBy('name', 'asc')
        ->get(['id', 'name', 'slug', 'icon', 'color', 'is_fixed'])
        ->unique('name');
@endphp

                    @foreach($sidebarCategories as $sidebarCat)
                        @php
                            $catSlug  = $sidebarCat->slug;
                            $catIcon  = $sidebarCat->icon ?? 'tag';
                            $lowerName = mb_strtolower($sidebarCat->name, 'UTF-8');
                            $catCount = $catCounts[$lowerName] ?? ($catCounts[$catSlug] ?? 0);
                            $catHref  = route('hub.category', $catSlug);
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
  {{-- Centro: Barra de Pesquisa + Aviso de Contexto --}}
 <div class="flex items-center justify-center gap-4 w-full"> {{-- Adicionei gap-4 e items-center --}}

    {{-- AVISO: MODO DE OBSERVAÇÃO --}}
    @if($isViewingOthers)
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-amber-500/10 border border-amber-500/20 rounded-2xl animate-in slide-in-from-top-2">
            <div class="relative flex items-center justify-center">
                <div class="size-1.5 rounded-full bg-amber-500 animate-ping absolute"></div>
                <flux:icon name="eye" variant="micro" class="size-3.5 text-amber-600 relative" />
            </div>
            <span class="text-[9px] font-black uppercase text-amber-600 tracking-widest whitespace-nowrap">
                A visualizar: <span class="text-amber-800 dark:text-amber-400 italic">{{ $currentWs->name }}</span>
            </span>
            <a href="{{ route('workspace.switch.fast', $myPersonalWsId) }}" class="ml-1 p-1 bg-amber-600 hover:bg-amber-700 text-white rounded-md transition-colors" title="Voltar à minha conta">
                <flux:icon name="arrow-uturn-left" variant="micro" class="size-3" />
            </a>
        </div>
    @endif
                <button x-on:click="$dispatch('open-global-search')" class="flex items-center gap-3 px-4 py-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all group w-full shadow-sm">
                    <flux:icon name="magnifying-glass" class="size-4 group-hover:text-brand-500 transition-colors" />
                    <span class="text-sm font-medium text-left flex-1">Procurar tudo... (Ctrl K)</span>
                </button>
            </div>



{{-- Lado Direito: Ações e Perfil --}}
<div class="flex items-center gap-2 sm:gap-3 justify-end">

    {{-- Loja --}}
    @php
    // Verifica se o utilizador tem acesso (Plus ou Pro)
    $hasStoreAccess = ($user->plan ?? '') === 'plus' ||
                      ($user->plan ?? '') === 'pro' ||
                      (method_exists($user, 'isPlus') && $user->isPlus()) ||
                      (method_exists($user, 'isDiamond') && $user->isDiamond());
@endphp

@if($hasStoreAccess)
    {{-- LOJA DISPONÍVEL (UTILIZADOR PREMIUM/BUSINESS) --}}
    <a href="{{ route('hub.store') }}"
       wire:navigate
       class="relative p-2.5 rounded-xl bg-white dark:bg-zinc-900 shadow-sm border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-brand-600 hover:border-brand-500/30 transition-all group"
       title="Loja Digital">
        <flux:icon name="shopping-bag" class="size-5 group-hover:scale-110 transition-transform duration-300" />
    </a>
@else
    {{-- LOJA BLOQUEADA (REDIRECIONA PARA PLANOS) --}}
    <a href="{{ route('hub.pricing') }}"
       wire:navigate
       class="relative p-2.5 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/50 shadow-sm border border-zinc-200/60 dark:border-zinc-800/60 text-zinc-400 opacity-80 hover:opacity-100 hover:border-amber-500/50 transition-all group"
       title="Loja Digital (Disponível em Planos Premium)">

        {{-- Ícone da Loja em tons de cinza --}}
        <flux:icon name="shopping-bag" class="size-5 grayscale" />

        {{-- CADEADO PEQUENO NO CANTO --}}
        <div class="absolute -top-1.5 -right-1.5 bg-white dark:bg-zinc-950 rounded-full p-0.5 border border-zinc-100 dark:border-zinc-800 shadow-sm">
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-full p-0.5">
                <flux:icon name="lock-closed" variant="micro" class="size-2.5 text-amber-600" />
            </div>
        </div>
    </a>
@endif

                {{-- Botão de Privacidade --}}
                @if(request()->routeIs('dashboard') || request()->routeIs('social.profile') || request()->routeIs('profile.edit'))
                    <button @click="privacyMode = !privacyMode"
                        class="p-2.5 rounded-xl bg-white dark:bg-zinc-900 shadow-sm border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-emerald-600 transition-all">
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

                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-60 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl z-50 overflow-hidden">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" wire:navigate.hover class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
                                👤 O meu perfil
                            </a>

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

                            @if(!auth()->user()->isAdmin())
                                <a href="{{ $isBusinessMode ? route('hub.business.support') : route('support.hub') }}"
                                    wire:navigate
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
                                    <span>🎫</span> Suporte Técnico
                                </a>
                            @endif

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

    @if(request()->routeIs('hub.store', 'store.product.show', 'store.compare', 'store.wishlist'))
        @persist('store-cart-fab')
            <livewire:store.cart-icon />
        @endpersist
    @endif

{{-- RECUPERADO: CHATBOT IA (Finance Bot) --}}
     @persist('finance-bot')
        <livewire:finance-bot />
    @endpersist

    @persist('toast')
        <flux:toast.group><flux:toast /></flux:toast.group>
    @endpersist

    {{-- 2. MODO VISUALIZAÇÃO / VOLTAR CEO (Canto Inferior Esquerdo) --}}
    @persist('finance-bot')
        <livewire:finance-bot />
    @endpersist

    {{-- 2. MODO VISUALIZAÇÃO CENTRADO EM BAIXO --}}
    @if(session()->has('impersonator_id') || session()->has('viewing_as_collaborator_id'))
        @php
            $exitViewUrl = session()->has('viewing_as_collaborator_id')
                ? route('hub.business.stop-viewing-collaborator')
                : route('hub.business.leave-impersonation');
            $viewLabel = session()->has('viewing_as_collaborator_id')
                ? 'Vista de Colaborador'
                : 'Modo Colaborador';
        @endphp
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[999] w-auto animate-in fade-in slide-in-from-bottom-10 duration-700">
            <div class="relative group">
                <!-- Aura de brilho atrás do botão -->
                <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-amber-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>

                <a href="{{ $exitViewUrl }}"
                   class="relative flex items-center gap-4 px-6 py-3 bg-zinc-900 dark:bg-zinc-800 border border-white/10 rounded-full shadow-2xl no-underline transition-all hover:scale-105 active:scale-95 group">

                    <div class="flex items-center gap-3">
                        <div class="size-8 rounded-full bg-red-600 flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform">
                            <flux:icon name="arrow-right-start-on-rectangle" class="size-4 text-white" />
                        </div>

                        <div class="flex flex-col items-start leading-none pr-4 border-r border-white/10">
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] text-red-500">Acesso Restrito</span>
                            <span class="text-[10px] font-black uppercase tracking-tight text-white mt-0.5">{{ $viewLabel }}</span>
                        </div>

                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 group-hover:text-white transition-colors">
                            Voltar para CEO
                        </span>
                    </div>
                </a>
            </div>
        </div>
    @endif

    {{-- MODAL DE DETALHES DO AVISO --}}
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
                <div class="space-y-8 text-left">
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
                            <flux:button variant="ghost" class="w-full h-14 rounded-2xl font-black uppercase text-[11px] tracking-widest border-2 border-zinc-100 dark:border-zinc-800">
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
            const main = document.querySelector('flux-main');
            if (main) main.scrollTop = 0;

            const el = document.getElementById('page-container');
            if (!el) return;
            const isSocial = location.pathname.startsWith('/social');
            el.style.maxWidth = isSocial ? '100rem' : '';
        };
        document.addEventListener('livewire:navigated', window.adjustPageContainer);
        document.addEventListener('livewire:init', () => {
            Livewire.on('openChatWith', (data) => { Livewire.dispatch('open-chat-with', { userId: data.userId }); });
            Livewire.on('copyToClipboard', (data) => { navigator.clipboard.writeText(data.text); });
        });
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
                <h2 class="text-xl font-black text-zinc-900 uppercase italic mb-6 text-left">Reportar Publicação</h2>
                <textarea wire:model="reportReason" class="w-full bg-zinc-50 border border-zinc-200 rounded-2xl p-5 text-sm mb-6 h-40 outline-none" placeholder="Motivo da denúncia..."></textarea>
                <div class="flex gap-4">
                    <button wire:click="$set('reportModal', false)" class="flex-1 font-black uppercase text-zinc-400">Cancelar</button>
                    <button wire:click="submitReport" class="flex-[2] bg-emerald-600 text-white h-14 rounded-2xl font-black uppercase shadow-lg">Enviar Ticket 🟢</button>
                </div>
            </div>
        </div>
    @endif



















    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- BARRA FLUTUANTE DE CONTEXTO EXTERNO (FIXA EM BAIXO)            --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($isViewingOthers)
    <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[999] w-auto animate-in fade-in slide-in-from-bottom-10 duration-700">
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-amber-600 to-orange-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>

            <div class="relative flex items-center gap-6 px-8 py-4 bg-white dark:bg-zinc-900 border border-amber-500/30 rounded-full shadow-2xl backdrop-blur-md">
                <div class="flex flex-col text-left leading-tight pr-6 border-r border-zinc-100 dark:border-zinc-800">
                    <span class="text-[8px] font-black uppercase text-amber-600 tracking-[0.2em]">Modo de Consulta Ativo</span>
                    <span class="text-sm font-black dark:text-white uppercase italic">{{ $currentWs->name }}</span>
                </div>

                {{-- O BOTÃO PARA VOLTAR --}}
                <a href="{{ route('workspace.switch.fast', $myPersonalWsId) }}"
                   class="flex items-center gap-3 px-6 py-2 bg-zinc-950 dark:bg-brand-600 text-white rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-xl group/btn">
                    <flux:icon name="arrow-uturn-left" variant="micro" class="size-4 transition-transform group-hover/btn:-translate-x-1" />
                    <span class="text-[10px] font-black uppercase tracking-widest whitespace-nowrap">Voltar à Minha Gestão</span>
                </a>
            </div>
        </div>
    </div>
@endif
</body>
</html>
