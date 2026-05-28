<!DOCTYPE html>
<html lang="pt">
<head>
<!-- Splash Screen exato para o teu iPhone 15 -->
<link rel="apple-touch-startup-image"
      href="/pwa/splash_screens/iPhone_16__iPhone_15_Pro__iPhone_15__iPhone_14_Pro_portrait.png"
      media="(device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">

{{-- Tags obrigatórias para ativar o modo App no iOS --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Finance Pro">
<link rel="apple-touch-icon" href="/pwa/splash_screens/apple-icon-180x180.png">
<!-- Configuração de UI para parecer App Nativa -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Finance Pro">
<link rel="apple-touch-icon" href="/pwa/splash_screens/apple-icon-180x180.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<!-- Imagem de arranque para iOS (Podes usar o mesmo ícone grande) -->
<link rel="apple-touch-startup-image" href="/icon-512x512.png">

    {{-- Apple Splash Screen Capability --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

{{-- Ícone para iPhone --}}
<link rel="apple-touch-icon" href="/icon-512x512.png">

{{-- Título da App no ecrã principal --}}
<meta name="apple-mobile-web-app-title" content="FinancePro">
    {{-- Tags específicas para iOS (iPhone) --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="FinancePro">
<link rel="apple-touch-icon" href="/icon-192x192.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Link para o Manifesto PWA --}}
<link rel="manifest" href="/manifest.json">
{{-- Cor da barra de status no telemóvel --}}
<meta name="theme-color" content="#10b981">

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

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @livewireStyles
</head>









<body
    class="app-shell antialiased bg-zinc-50 dark:bg-zinc-950 transition-colors duration-300"
    x-data="{
        privacyMode: localStorage.getItem('privacyMode') === 'true',

        togglePrivacy() {
            this.privacyMode = !this.privacyMode;
            localStorage.setItem('privacyMode', this.privacyMode);
        }
    }"
    x-on:keydown.window.alt.p="togglePrivacy()"
    x-on:keydown.window.ctrl.k.prevent="$dispatch('open-global-search')"
>

    {{-- Pesquisa Global --}}
    <livewire:global-search />

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

        // PLANOS
        $userPlan = $currentWs->plan ?? 'free';

        $isPremium = in_array($userPlan, [
            'plus',
            'pro',
            'company'
        ]);

        $planEmoji = $isPremium ? '💎' : '👤';

        $planText = $isPremium
            ? 'Plano Premium'
            : 'Plano Básico';

        // CONTADORES
        $counts = [

            // Pessoal
            'incomes' => \App\Models\Income::count(),
            'investments' => \App\Models\Investment::count(),
            'subscriptions' => \App\Models\Subscription::count(),
            'activity' => \App\Models\ActivityLog::count(),
            'goals' => \App\Models\Goal::count(),
            'debts' => \App\Models\Debt::count(),

            // Workspace
            'members' => $currentWs?->users()->count() ?? 0,
            'ranking' => $currentWs?->users()->count() ?? 0,

            // Suporte
            'support' => \App\Models\SupportTicket::where('status', 'open')->count(),

            // Empresa
            'company_expenses' => \App\Models\Expense::whereNull('category_id')->count(),
            'invoices' => \App\Models\Invoice::count(),
            'proposals' => \App\Models\Proposal::count(),
            'clients' => \App\Models\Client::count(),
            'projects' => \App\Models\Project::count(),
            'inventory' => \App\Models\Product::count(),
            'suppliers' => \App\Models\Supplier::count(),
            'employees' => \App\Models\Employee::count(),

            'tasks' => \App\Models\Task::where(
                'status',
                '!=',
                'concluido'
            )->count(),

            'absences' => \App\Models\Absence::where(
                'status',
                'pendente'
            )->count(),
        ];

        // Categorias pessoais
        $catCounts = \App\Models\Expense::selectRaw(
                'categories.name, count(*) as total'
            )
            ->join(
                'categories',
                'expenses.category_id',
                '=',
                'categories.id'
            )
            ->groupBy('categories.name')
            ->pluck('total', 'name')
            ->mapWithKeys(
                fn ($item, $key) => [
                    strtolower($key) => $item
                ]
            );

        // Badge
        $badge = fn ($val) =>
            $val > 0
                ? '<span class="ml-auto text-[10px] font-black bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-full text-zinc-500 group-hover:text-brand-600 transition-colors">'
                    .$val.
                  '</span>'
                : '';
    @endphp









    {{-- SIDEBAR --}}
    <flux:sidebar
        sticky
        collapsible="mobile"
        class="border-e border-zinc-200/80 bg-white/90 backdrop-blur-xl dark:border-zinc-800/80 dark:bg-zinc-950/90"
    >

        <flux:sidebar.header>
            <x-app-logo
                :sidebar="true"
                href="{{ route('dashboard') }}"
                wire:navigate
            />
        </flux:sidebar.header>

        <flux:sidebar.nav>
    @if($isAdminMode)
                {{-- ========================================== --}}
                {{-- MENU EXCLUSIVO ADMINISTRADOR               --}}
                {{-- ========================================== --}}
                <flux:sidebar.item icon="arrow-left-circle" :href="route('dashboard')" class="text-brand-600 font-bold" wire:navigate>
                    Sair do Admin
                </flux:sidebar.item>

                <flux:separator class="my-4 mx-2" />

                <flux:sidebar.group heading="Gestão Global">
                    <flux:sidebar.item icon="chart-pie" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        Estatísticas do Site
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                        Utilizadores {!! $badge(\App\Models\User::count()) !!}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('admin.support')" :current="request()->routeIs('admin.support')" wire:navigate>
                        Tickets Suporte {!! $badge(\App\Models\SupportTicket::where('status', 'open')->count()) !!}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Sistema" class="mt-4">
                    {{-- LINKS AGORA FUNCIONAIS --}}
                    <flux:sidebar.item icon="shield-check" :href="route('admin.logs')" :current="request()->routeIs('admin.logs')" wire:navigate>
                        Segurança & Logs
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.settings')" :current="request()->routeIs('admin.settings')" wire:navigate>
                        Configurações
                    </flux:sidebar.item>
                </flux:sidebar.group>

    @elseif($isBusinessMode)
        {{-- ========================================== --}}
{{-- MENU EMPRESARIAL (CORRIGIDO & COMPLETO)    --}}
{{-- ========================================== --}}
<flux:sidebar.item icon="arrow-left-circle" :href="route('dashboard')" class="text-zinc-500 font-bold" wire:navigate>
    Sair do Modo Empresa
</flux:sidebar.item>

<flux:separator class="my-4 mx-2" />

<flux:sidebar.item icon="chart-pie" :href="route('hub.business.dashboard')" :current="request()->routeIs('hub.business.dashboard')" wire:navigate>
    Dashboard Business
</flux:sidebar.item>

<flux:sidebar.group heading="Gestão de Operações" class="mt-4">
    <flux:sidebar.item icon="building-office-2" :href="route('company-expenses')" :current="request()->routeIs('company-expenses')" wire:navigate>
        Custos Operacionais {!! $badge($counts['company_expenses']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="document-text" :href="route('hub.business.invoices')" :current="request()->routeIs('hub.business.invoices')" wire:navigate>
        Faturação & Vendas {!! $badge($counts['invoices']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="newspaper" :href="route('hub.business.proposals')" :current="request()->routeIs('hub.business.proposals')" wire:navigate>
        Propostas {!! $badge($counts['proposals']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="user-group" :href="route('hub.business.clients')" :current="request()->routeIs('hub.business.clients')" wire:navigate>
        Clientes (CRM) {!! $badge($counts['clients']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="briefcase" :href="route('hub.business.projects')" :current="request()->routeIs('hub.business.projects')" wire:navigate>
        Projetos {!! $badge($counts['projects']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="presentation-chart-line" :href="route('hub.business.pnl')" :current="request()->routeIs('hub.business.pnl')" wire:navigate>
        Resultados (P&L)
    </flux:sidebar.item>

    <flux:sidebar.item icon="archive-box" :href="route('hub.business.inventory')" :current="request()->routeIs('hub.business.inventory')" wire:navigate>
        Stock / Inventário {!! $badge($counts['inventory']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="building-library" :href="route('hub.business.accounts')" :current="request()->routeIs('hub.business.accounts')" wire:navigate>
        Contas Empresa
    </flux:sidebar.item>

    <flux:sidebar.item icon="truck" :href="route('hub.business.suppliers')" :current="request()->routeIs('hub.business.suppliers')" wire:navigate>
        Fornecedores {!! $badge($counts['suppliers']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="check-badge" :href="route('hub.business.tasks')" :current="request()->routeIs('hub.business.tasks')" wire:navigate>
        Tarefas & Equipa {!! $badge($counts['tasks']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="command-line" :href="route('hub.business.timeline')" :current="request()->routeIs('hub.business.timeline')" wire:navigate>
        Timeline Operações
    </flux:sidebar.item>

    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('hub.business.messenger')" :current="request()->routeIs('hub.business.messenger')" wire:navigate>
        Messenger Equipa
    </flux:sidebar.item>

    <flux:sidebar.item icon="folder-open" :href="route('hub.business.vault')" :current="request()->routeIs('hub.business.vault')" wire:navigate>
        Arquivo Digital
    </flux:sidebar.item>

    <flux:sidebar.item icon="sparkles" :href="route('hub.business.ai')" :current="request()->routeIs('hub.business.ai')" wire:navigate>
        IA Estrategista
    </flux:sidebar.item>
</flux:sidebar.group>

<flux:sidebar.item icon="calendar-days" :href="route('hub.business.calendar')" :current="request()->routeIs('hub.business.calendar')" wire:navigate>
    Calendário Global
</flux:sidebar.item>

<flux:sidebar.group heading="Administrativo" class="mt-4">
    <flux:sidebar.item icon="users" :href="route('hub.business.team')" :current="request()->routeIs('hub.business.team')" wire:navigate>
        Recursos Humanos {!! $badge($counts['employees']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="calendar-days" :href="route('hub.business.absences')" :current="request()->routeIs('hub.business.absences')" wire:navigate>
        Férias & Ausências {!! $badge($counts['absences']) !!}
    </flux:sidebar.item>

    <flux:sidebar.item icon="receipt-percent" :href="route('hub.business.taxes')" :current="request()->routeIs('hub.business.taxes')" wire:navigate>
        Impostos & IVA
    </flux:sidebar.item>

    <flux:sidebar.item icon="adjustments-horizontal" :href="route('hub.business.settings')" :current="request()->routeIs('hub.business.settings')" wire:navigate>
        Perfil do Negócio
    </flux:sidebar.item>

    {{-- SUPORTE TÉCNICO EMPRESARIAL - ADICIONADO E CORRIGIDO --}}
    <flux:sidebar.item
        icon="chat-bubble-left-right"
        :href="route('hub.business.support')"
        :current="request()->routeIs('hub.business.support')"
        wire:navigate
    >
        Suporte Técnico {!! $badge(\App\Models\SupportTicket::where('status', 'open')->where('workspace_id', $currentWs->id)->count()) !!}
    </flux:sidebar.item>
</flux:sidebar.group>



    @else
        {{-- ========================================== --}}
        {{-- MENU PESSOAL (MODO NORMAL)                 --}}
        {{-- ========================================== --}}
        <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>Dashboard</flux:sidebar.item>

        {{-- LÓGICA DE CADEADO PARA MODO EMPRESA --}}
        @if($isPremium)
            <flux:sidebar.item icon="building-office" :href="route('hub.business.dashboard')" class="text-brand-600 dark:text-brand-400 font-black italic" wire:navigate>Área Empresarial →</flux:sidebar.item>
        @else
            <flux:sidebar.item icon="lock-closed" :href="route('hub.pricing')" class="text-zinc-400 opacity-60 group" wire:navigate>
                Área Empresarial 🔒
                <span class="ml-auto text-[8px] font-black bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded uppercase">Premium</span>
            </flux:sidebar.item>

        @endif

        <flux:separator class="my-4 mx-2" />

        <flux:sidebar.group heading="Finanças">
            <flux:sidebar.item icon="arrow-trending-up" :href="route('hub.incomes')" wire:navigate>
                Receitas {!! $badge($counts['incomes']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="chart-bar-square" :href="route('hub.investments')" wire:navigate>
                Investimentos {!! $badge($counts['investments']) !!}
            </flux:sidebar.item>

            <flux:sidebar.item icon="credit-card" :href="route('hub.subscriptions')" wire:navigate>
                Assinaturas {!! $badge($counts['subscriptions']) !!}
            </flux:sidebar.item>
            <flux:sidebar.item icon="briefcase" :href="route('hub.networth')" wire:navigate>Património Real</flux:sidebar.item>
        </flux:sidebar.group>






        <flux:sidebar.group heading="Ferramentas" class="mt-4">





    {{-- 1. Família vem primeiro agora --}}
    <flux:sidebar.item icon="user-group" :href="route('hub.family.manage')" :current="request()->routeIs('hub.family.manage')" wire:navigate>
        Família {!! $badge($counts['ranking']) !!}
    </flux:sidebar.item>


            <flux:sidebar.item icon="trophy" :href="route('hub.goals')" wire:navigate>Metas {!! $badge($counts['goals']) !!}</flux:sidebar.item>
            <flux:sidebar.item icon="hand-raised" :href="route('hub.debts')" wire:navigate>Dívidas {!! $badge($counts['debts']) !!}</flux:sidebar.item>

        </flux:sidebar.group>

        <flux:sidebar.item icon="chat-bubble-left-right" :href="route('support.hub')" wire:navigate>
            Suporte Técnico {!! $badge($counts['support']) !!}
        </flux:sidebar.item>

        {{-- Hubs Pessoais --}}
        <flux:sidebar.group heading="Categorias" class="grid mt-4">
            @php
                $personalHubs = ['casa', 'carro', 'alimentacao', 'transporte', 'saude', 'educacao', 'tecnologia', 'emprestimos', 'seguros', 'outras'];
                $hubIcons = ['casa'=>'home', 'carro'=>'truck', 'alimentacao'=>'shopping-cart', 'transporte'=>'bolt', 'saude'=>'heart', 'educacao'=>'academic-cap', 'tecnologia'=>'cpu-chip', 'emprestimos'=>'banknotes', 'seguros'=>'shield-check', 'outras'=>'ellipsis-horizontal'];
            @endphp
            @foreach($personalHubs as $slug)
                <flux:sidebar.item :icon="$hubIcons[$slug]" :href="route('hub.'.$slug)" wire:navigate>
                    {{ ucfirst($slug == 'alimentacao' ? 'Alimentação' : $slug) }}
                    @if(isset($catCounts[$slug]))
                        <span class="ml-auto text-[10px] font-black text-zinc-400 italic">#{{ $catCounts[$slug] }}</span>
                    @endif
                </flux:sidebar.item>
            @endforeach
        </flux:sidebar.group>

        @if($user->is_admin)
            <flux:separator class="my-4 mx-2" />
            <flux:sidebar.item icon="shield-check" :href="route('admin.dashboard')" class="text-purple-600 font-bold" wire:navigate>Painel Administrador</flux:sidebar.item>
        @endif
    @endif
</flux:sidebar.nav>

        <flux:spacer />

        {{-- BOTÃO TEMA --}}
        <div class="px-3 mb-6">
            <button x-data="{ darkMode: document.documentElement.classList.contains('dark') }" x-on:click="darkMode = !darkMode; darkMode ? (localStorage.theme = 'dark', document.documentElement.classList.add('dark')) : (localStorage.theme = 'light', document.documentElement.classList.remove('dark'))" class="w-full glass-card flex items-center gap-3 px-3 py-2 text-sm font-medium transition-all hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:icon.sun x-show="darkMode" variant="outline" class="w-5 h-5 text-brand-400" />
                <flux:icon.moon x-show="!darkMode" variant="outline" class="w-5 h-5 text-zinc-500" />
                <span x-text="darkMode ? 'Modo Claro' : 'Modo Escuro'"></span>
            </button>
        </div>
    </flux:sidebar>

    {{-- HEADER SUPERIOR --}}
    <flux:header class="lg:ps-64 border-b border-zinc-200/50 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-950/50 backdrop-blur-md z-40 relative">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <div class="flex items-center gap-6 w-full max-w-6xl px-4 justify-start">
            <button x-on:click="$dispatch('open-global-search')" class="flex items-center gap-3 px-4 py-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all group flex-1 max-w-3xl">
                <flux:icon name="magnifying-glass" class="size-4 group-hover:text-brand-500 transition-colors" />
                <span class="text-sm font-medium flex-1 text-left">Procurar tudo... (Ctrl K)</span>
            </button>
            <button
                @click="togglePrivacy()"
                class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-brand-500 transition-all shadow-sm"
                title="Modo de Privacidade (Alt + P)"
            >
                <flux:icon x-show="!privacyMode" name="eye" class="size-5" />
                <flux:icon x-show="privacyMode" name="eye-slash" class="size-5 text-brand-500" />
            </button>

            <livewire:notification-center />


            {{-- USER MENU COM EMOJI DE PLANO 💎/👤 --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button" class="flex items-center gap-3 px-2 py-1.5 rounded-2xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">
                    <flux:avatar :initials="auth()->user()->initials()" class="size-10 shadow-sm" />
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-bold text-zinc-800 dark:text-white leading-none">
                            {{ auth()->user()->name }} <span class="ml-1">{{ $planEmoji }}</span>
                        </p>
                        <p class="text-[10px] font-black uppercase text-zinc-400 mt-1">
                            {{ $planText }}
                        </p>
                    </div>
                    <flux:icon name="chevron-down" class="size-4 text-zinc-400" />
                </button>

                <div x-show="open" x-cloak @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-60 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl z-50 overflow-hidden">
    <div class="py-1">
        {{-- Perfil --}}
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
            👤 O meu perfil
        </a>

        {{-- NOVO TICKET INTEGRADO (Como linha de menu) --}}
        <a href="{{ $isBusinessMode ? route('hub.business.support') : route('support.hub') }}"
   wire:navigate
   class="w-full flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 transition-colors">
    <span>🎫</span>
    Novo Ticket Suporte
</a>

        @if(!$isPremium)
            <a href="{{ route('hub.pricing') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-brand-600 font-bold hover:bg-brand-50">
                💎 Obter Premium
            </a>
        @endif

        <div class="border-t border-zinc-200 dark:border-zinc-800 my-1"></div>

        {{-- Sair --}}
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
    </flux:header>

    <flux:main class="!p-0 lg:ps-64">
        <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            {{ $slot }}
        </div>
    </flux:main>

    @persist('toast') <flux:toast.group><flux:toast /></flux:toast.group> @endpersist

    @livewireScripts
    <script src="/flux/flux.js"></script>
    @fluxScripts

    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('Service Worker Registado!', reg))
                .catch(err => console.log('Erro ao registar Service Worker', err));
        });
    }
</script>
</body>
</html>
