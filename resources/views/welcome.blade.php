<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} — Finanças pessoais inteligentes</title>

        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💰</text></svg>">

        <!-- Script de Deteção de Tema -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>

        @include('partials.head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-shell antialiased bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 transition-colors duration-300">

        <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
            <span class="flex items-center gap-3 font-bold uppercase tracking-tighter">
                <span class="flex size-10 items-center justify-center rounded-xl brand-gradient shadow-lg shadow-brand-500/20">
                    <svg class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                {{ config('app.name') }}
            </span>

            <nav class="flex items-center gap-4">
                {{-- Botão de Troca de Tema --}}
                <button
                    x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
                    x-on:click="darkMode = !darkMode; darkMode ? (localStorage.theme = 'dark', document.documentElement.classList.add('dark')) : (localStorage.theme = 'light', document.documentElement.classList.remove('dark'))"
                    class="flex size-9 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 transition-all hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800"
                >
                    <flux:icon.sun x-show="darkMode" variant="outline" class="size-5" />
                    <flux:icon.moon x-show="!darkMode" variant="outline" class="size-5" />
                </button>

                @auth
                    {{-- SESSÃO INICIADA --}}
                    <div class="flex items-center gap-3 pl-4 border-l border-zinc-200 dark:border-zinc-800">
                        <div class="hidden sm:flex flex-col items-end leading-none">
                            <span class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Sessão de</span>
                            <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ auth()->user()->name }}</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-brand-500/25 hover:bg-brand-500 transition-all">
                            Dashboard
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Sair da conta" class="flex size-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 dark:border-red-900/30 dark:bg-red-900/10 dark:text-red-500 dark:hover:bg-red-900/20 transition-all">
                                <flux:icon name="arrow-right-start-on-rectangle" variant="micro" class="size-5" />
                            </button>
                        </form>
                    </div>
                @else
                    {{-- VISITANTE --}}
                    <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">Entrar</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-brand-500/25 hover:bg-brand-500">Começar grátis</a>
                @endauth
            </nav>
        </header>

        <main class="mx-auto max-w-6xl px-6 pb-24 pt-8 lg:pt-16">
            <!-- HERO SECTION -->
            <div class="grid items-center gap-16 lg:grid-cols-2">
                <div>
                    <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-brand-500/20 bg-brand-500/10 px-3 py-1 text-sm font-medium text-brand-700 dark:text-brand-300">
                        <span class="size-2 rounded-full bg-brand-500 animate-pulse"></span>
                        Finanças pessoais com IA
                    </p>
                    <h1 class="text-5xl font-black tracking-tighter text-zinc-900 sm:text-6xl lg:text-7xl dark:text-white leading-[0.9]">
                        Todos os custos da vida, <br>
                        <span class="bg-gradient-to-r from-brand-500 to-brand-700 bg-clip-text text-transparent ">num só lugar</span>
                    </h1>
                    <p class="mt-6 text-lg leading-relaxed text-zinc-600 dark:text-zinc-400 max-w-md">
                        Casa, carro, trabalho, saúde — regista despesas por categoria,
                        acompanha tendências no dashboard e recebe insights da IA.
                    </p>
                    <div class="mt-10 flex flex-wrap gap-4">
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-8 py-4 font-bold text-white shadow-xl shadow-brand-500/30 hover:bg-brand-500 transition-all hover:-translate-y-1">
                                Criar conta grátis
                                <flux:icon name="arrow-right" variant="micro" />
                            </a>
                        @endguest
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="inline-flex items-center rounded-xl border border-zinc-300 bg-white dark:bg-zinc-900 px-8 py-4 font-bold hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800 transition-all">
                            {{ auth()->check() ? 'Abrir dashboard' : 'Já tenho conta' }}
                        </a>
                    </div>
                </div>

                <!-- DEMO CARD -->
                <div class="relative">
                    <div class="glass-card space-y-6 p-8 shadow-2xl shadow-brand-500/10 border-brand-500/10">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase tracking-widest text-zinc-400">Gasto este mês</span>
                            <flux:badge variant="success" class="bg-brand-500/10 text-brand-600 border-none font-bold">Demo Live</flux:badge>
                        </div>
                        <p class="text-5xl font-black dark:text-white">1.247,50 €</p>
                        <div class="space-y-4 pt-2">
                            @foreach ([['Casa', '#3b82f6', 42], ['Carro', '#ef4444', 28], ['Alimentação', '#f59e0b', 18]] as [$label, $color, $pct])
                                <div class="space-y-1">
                                    <div class="flex justify-between text-[10px] font-black uppercase tracking-wider">
                                        <span class="text-zinc-500">{{ $label }}</span>
                                        <span class="text-zinc-900 dark:text-zinc-100">{{ $pct }}%</span>
                                    </div>
                                    <div class="h-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                        <div class="h-full rounded-full transition-all duration-1000" style="width: {{ $pct }}%; background: {{ $color }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION: HOW IT WORKS -->
            <div class="mt-32 text-center space-y-4">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-brand-600">Fluxo Simples</h2>
                <p class="text-4xl font-black dark:text-white tracking-tighter">Como funciona o {{ config('app.name') }}?</p>

                <div class="mt-16 grid gap-8 sm:grid-cols-3">
                    <div class="space-y-4">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 shadow-lg font-black text-xl text-brand-500 border border-zinc-100 dark:border-zinc-800">1</div>
                        <h4 class="font-bold text-lg">Regista</h4>
                        <p class="text-sm text-zinc-500 leading-relaxed">Adiciona os teus ganhos e gastos em segundos, seja no PC ou no telemóvel.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 shadow-lg font-black text-xl text-brand-500 border border-zinc-100 dark:border-zinc-800">2</div>
                        <h4 class="font-bold text-lg">Planeia</h4>
                        <p class="text-sm text-zinc-500 leading-relaxed">Define orçamentos por categoria e metas de poupança para os teus sonhos.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 shadow-lg font-black text-xl text-brand-500 border border-zinc-100 dark:border-zinc-800">3</div>
                        <h4 class="font-bold text-lg">Evolui</h4>
                        <p class="text-sm text-zinc-500 leading-relaxed">Deixa a nossa IA analisar os teus padrões e sugerir onde podes poupar mais.</p>
                    </div>
                </div>
            </div>

            <!-- SECTION: FEATURES -->
            <div class="mt-32 grid gap-6 sm:grid-cols-3">
                @foreach ([
                    ['briefcase', 'Património Líquido', 'Soma total dos teus ativos e investimentos menos passivos.'],
                    ['credit-card', 'Assinaturas & Fixos', 'Controlo total de débitos diretos, Netflix, rendas e ginásio.'],
                    ['trophy', 'Objetivos de Poupança', 'Define metas visuais para o teu fundo de emergência ou férias.'],
                    ['chart-bar', 'Relatórios Anuais', 'Gráficos comparativos de fluxo de caixa mês a mês.'],
                    ['building-office-2', 'Despesas Empresa', 'Gestão separada para gastos profissionais e reembolsos.'],
                    ['sparkles', 'Análise com IA', 'Previsões de fim de mês e alertas de saúde financeira.'],
                ] as [$icon, $title, $desc])
                    <div class="glass-card p-8 group hover:border-brand-500/50 transition-all duration-300">
                        <span class="mb-6 flex size-12 items-center justify-center rounded-2xl bg-brand-500/10 text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform">
                            <flux:icon :name="$icon" variant="outline" />
                        </span>
                        <h3 class="font-black uppercase tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            <!-- FINAL CTA -->
            <div class="mt-32 text-center space-y-10 pb-20">
                <h2 class="text-5xl md:text-6xl font-black tracking-tighter dark:text-white leading-tight">
                    Pronto para dominar as <br>
                    <span class="text-brand-500 italic underline decoration-zinc-200 dark:decoration-zinc-800">tuas finanças?</span>
                </h2>
                <div class="flex justify-center gap-4">
                    <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="rounded-2xl bg-brand-600 px-12 py-5 text-xl font-black text-white shadow-2xl shadow-brand-500/40 hover:bg-brand-500 transition-all hover:scale-105">
                        {{ auth()->check() ? 'Ir para o Dashboard' : 'Começar Agora — É Grátis' }}
                    </a>
                </div>
                <p class="text-zinc-400 text-xs font-bold uppercase tracking-[0.4em]">© {{ date('Y') }} {{ config('app.name') }}</p>
            </div>
        </main>

        @fluxScripts
    </body>
</html>
