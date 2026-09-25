<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            @php
                $user = auth()->user();
                $workspace = $user->currentWorkspace;
                $isBusiness = $workspace?->type === 'business';

                $personalNav = [
                    'overview' => [
                        ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard'],
                    ],
                    'finance' => [
                        ['label' => 'Despesas', 'icon' => 'arrow-trending-down', 'route' => 'expenses.index'],
                        ['label' => 'Receitas', 'icon' => 'arrow-trending-up', 'route' => 'incomes.index'],
                        ['label' => 'Orçamento', 'icon' => 'banknotes', 'route' => 'hub.budget'],
                        ['label' => 'Objectivos', 'icon' => 'flag', 'route' => 'hub.goals'],
                    ],
                    'planeamento' => [
                        ['label' => 'Subscrições', 'icon' => 'arrow-path', 'route' => 'hub.subscriptions'],
                        ['label' => 'Dívidas', 'icon' => 'credit-card', 'route' => 'hub.debts'],
                        ['label' => 'Investimentos', 'icon' => 'chart-bar', 'route' => 'hub.investments'],
                        ['label' => 'Património', 'icon' => 'building-library', 'route' => 'hub.networth'],
                    ],
                    'ferramentas' => [
                        ['label' => 'Importar extrato', 'icon' => 'arrow-up-tray', 'route' => 'hub.import'],
                        ['label' => 'Relatórios', 'icon' => 'document-chart-bar', 'route' => 'hub.reports'],
                        ['label' => 'IA Financeira', 'icon' => 'sparkles', 'route' => 'ai'],
                    ],
                ];

                $businessNav = [
                    'overview' => [
                        ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'hub.business.dashboard'],
                    ],
                    'gestão financeira' => [
                        ['label' => 'Despesas', 'icon' => 'arrow-trending-down', 'route' => 'company-expenses'],
                        ['label' => 'Facturação', 'icon' => 'document-text', 'route' => 'hub.business.invoices'],
                        ['label' => 'Fluxo de caixa', 'icon' => 'arrows-right-left', 'route' => 'hub.business.cashflow'],
                        ['label' => 'Resultados', 'icon' => 'chart-bar-square', 'route' => 'hub.business.pnl'],
                    ],
                    'negócio' => [
                        ['label' => 'Clientes', 'icon' => 'users', 'route' => 'hub.business.clients'],
                        ['label' => 'Fornecedores', 'icon' => 'truck', 'route' => 'hub.business.suppliers'],
                        ['label' => 'Stock', 'icon' => 'archive-box', 'route' => 'hub.business.inventory'],
                        ['label' => 'Projectos', 'icon' => 'briefcase', 'route' => 'hub.business.projects'],
                    ],
                    'ferramentas' => [
                        ['label' => 'IA Estrategista', 'icon' => 'sparkles', 'route' => 'hub.business.ai'],
                        ['label' => 'Equipa', 'icon' => 'user-group', 'route' => 'hub.business.team'],
                    ],
                ];

                $nav = $isBusiness ? $businessNav : $personalNav;
            @endphp

            @if ($workspace)
                <div class="px-3 pb-3">
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 dark:border-zinc-800 dark:bg-zinc-900/70">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-[9px] font-black uppercase tracking-[0.16em] text-zinc-400">
                                {{ $isBusiness ? 'Empresa' : 'Espaço pessoal' }}
                            </div>
                            <span class="size-1.5 shrink-0 rounded-full bg-emerald-500"></span>
                        </div>
                        <div class="mt-0.5 truncate text-sm font-bold text-zinc-900 dark:text-white">
                            {{ $workspace->name }}
                        </div>
                        <div class="mt-1 text-[9px] font-medium text-zinc-500">
                            Os dados apresentados pertencem a este espaço.
                        </div>
                    </div>
                </div>
            @endif

            <flux:sidebar.nav class="px-2">
                @foreach ($nav as $group => $items)
                    @php
                        $available = collect($items)->filter(fn ($item) => route_exists($item['route']));
                    @endphp
                    @if ($available->isNotEmpty())
                        <flux:sidebar.group
                            :heading="match ($group) {
                                'overview' => 'Visão geral',
                                'finance' => 'Finanças',
                                'planeamento' => 'Planeamento',
                                'gestão financeira' => 'Finanças',
                                'negócio' => 'Negócio',
                                'ferramentas' => 'Ferramentas',
                                default => ucfirst($group),
                            }"
                            class="grid"
                        >
                            @foreach ($available as $item)
                                <flux:sidebar.item
                                    :icon="$item['icon']"
                                    :href="route($item['route'])"
                                    :current="request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*')"
                                    wire:navigate
                                >
                                    {{ __($item['label']) }}
                                </flux:sidebar.item>
                            @endforeach
                        </flux:sidebar.group>
                    @endif
                @endforeach
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav class="px-2">
                @if (route_exists('profile.edit'))
                    <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" :current="request()->routeIs('profile.*')" wire:navigate>
                        {{ __('Definições') }}
                    </flux:sidebar.item>
                @endif

                @if (route_exists('support.hub'))
                    <flux:sidebar.item icon="question-mark-circle" :href="route('support.hub')" :current="request()->routeIs('support.*')" wire:navigate>
                        {{ __('Ajuda') }}
                    </flux:sidebar.item>
                @endif
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="$user->name" />
        </flux:sidebar>

        <flux:header class="lg:hidden border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="$user->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <div class="px-3 py-2">
                        <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ $user->name }}</div>
                        <div class="truncate text-xs text-zinc-500">{{ $user->email }}</div>
                    </div>
                    <flux:menu.separator />
                    @if (route_exists('profile.edit'))
                        <flux:menu.item :href="route('profile.edit')" icon="cog-6-tooth" wire:navigate>{{ __('Definições') }}</flux:menu.item>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Terminar sessão') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
