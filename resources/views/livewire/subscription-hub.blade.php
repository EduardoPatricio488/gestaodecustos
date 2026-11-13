<div class="{{ $dashboardMode ? '' : 'space-y-10 pb-20' }}">
    @php
        $cycleLabels = ['monthly' => 'Mensal', 'quarterly' => 'Trimestral', 'semiannual' => 'Semestral', 'annual' => 'Anual'];
        $statusLabels = ['active' => 'Ativa', 'paused' => 'Pausada', 'cancelled' => 'Cancelada'];
        $statusStyles = [
            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20',
            'paused' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20',
            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:border-rose-500/20',
        ];
    @endphp

    @unless($dashboardMode)
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full transition-all duration-700 shadow-brand-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="credit-card" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Assinaturas</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Custos Fixos</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Monitorizacao de debitos, renovacoes, ciclos e lembretes.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:button wire:click="openExtraModal" variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">Nova Assinatura</flux:button>
            </div>
        </header>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <div class="xl:col-span-2 stat-card bg-zinc-950 text-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800">
            <div class="absolute -right-10 -top-10 size-40 bg-brand-500/10 blur-3xl rounded-full"></div>
            <div class="relative z-10 flex h-full flex-col justify-between gap-8">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-400 mb-2">Custo mensal pessoal</p>
                    <h3 class="text-5xl font-black tracking-tighter italic text-white">{{ number_format($totalMonthly, 2, ',', ' ') }} <small class="text-xl not-italic ml-1">E U R</small></h3>
                    <p class="mt-3 text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ number_format($totalAnnual, 2, ',', ' ') }} EUR / ano</p>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-amber-400/20 bg-amber-400/10 p-3">
                            <p class="text-[9px] font-black uppercase tracking-widest text-amber-300">Pago pela empresa / mês</p>
                            <p class="mt-1 text-xl font-black text-amber-200">{{ number_format($companyPaidMonthly, 2, ',', ' ') }} EUR</p>
                            <p class="text-[10px] text-amber-200/70">{{ number_format($companyPaidAnnual, 2, ',', ' ') }} EUR / ano</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Total de assinaturas / mês</p>
                            <p class="mt-1 text-xl font-black text-white">{{ number_format($totalMonthlyIncludingCompany, 2, ',', ' ') }} EUR</p>
                            <p class="text-[10px] text-zinc-400">Inclui valores pagos pela empresa</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white/5 p-4 border border-white/10"><p class="text-[9px] uppercase tracking-widest text-zinc-500 font-black">Ativas</p><p class="text-2xl font-black">{{ $activeCount }}</p></div>
                    <div class="rounded-2xl bg-white/5 p-4 border border-white/10"><p class="text-[9px] uppercase tracking-widest text-zinc-500 font-black">Pausadas</p><p class="text-2xl font-black">{{ $pausedCount }}</p></div>
                    <div class="rounded-2xl bg-white/5 p-4 border border-white/10"><p class="text-[9px] uppercase tracking-widest text-zinc-500 font-black">Cancel.</p><p class="text-2xl font-black">{{ $cancelledCount }}</p></div>
                </div>
            </div>
            <flux:icon name="banknotes" class="absolute -right-4 -bottom-4 size-24 text-white/5 -rotate-12" />
        </div>
        <div class="glass-card p-7 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Por pagar este mes</p>
            <h3 class="text-2xl sm:text-3xl md:text-4xl font-black text-orange-500 tracking-tighter italic">{{ number_format($upcoming, 2, ',', ' ') }} EUR</h3>
            <p class="mt-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Media por assinatura</p>
            <p class="text-xl font-black dark:text-white">{{ number_format($averageMonthly, 2, ',', ' ') }} EUR</p>
        </div>
        <div class="glass-card p-7 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Proximo debito</p>
            @if($nextSub)
                <h3 class="text-2xl font-black dark:text-white tracking-tight">{{ $nextSub->name }}</h3>
                <p class="mt-2 text-sm font-bold text-brand-600">{{ $nextSub->next_billing_date->format('d/m/Y') }}</p>
                <p class="mt-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Daqui a {{ $nextSub->days_until_billing }} dias</p>
            @else
                <h3 class="text-2xl font-black dark:text-white tracking-tight">Sem debitos</h3>
                <p class="mt-2 text-sm font-bold text-zinc-500">Adiciona a primeira assinatura.</p>
            @endif
        </div>
    </div>

    @if($platformPlan)
        <div class="glass-card p-7 bg-gradient-to-br from-brand-600 to-brand-700 text-white rounded-[2rem] shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4"><div class="p-3 bg-white/10 rounded-2xl"><flux:icon name="sparkles" class="size-6" /></div><div><p class="text-[9px] font-black uppercase tracking-[0.3em] text-white/70">O teu plano Finance Pro</p><h3 class="text-xl font-black uppercase italic tracking-tighter">Finance Pro {{ $platformPlan->name }}</h3></div></div>
            <div class="flex items-center gap-6"><div class="text-right"><p class="text-2xl font-black italic tracking-tighter">{{ number_format($platformPlan->price, 2, ',', ' ') }} EUR</p><p class="text-[9px] font-black uppercase tracking-widest text-white/70">por mês</p></div><flux:button wire:click="openPlatformPlanModal" variant="ghost" class="rounded-2xl font-black uppercase tracking-widest text-[10px] bg-white/10 hover:bg-white/20 border-none text-white">Gerir Plano</flux:button></div>
        </div>
    @endif

    <div class="space-y-6">
        <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-4 sm:p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">
                <label class="xl:col-span-2"><span class="sr-only">Pesquisar</span><div class="relative"><flux:icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-zinc-400" /><input wire:model.live.debounce.250ms="search" type="search" placeholder="Pesquisar por nome ou notas..." class="w-full h-12 rounded-2xl border-0 bg-zinc-50 pl-11 pr-4 text-sm font-bold outline-none ring-0 transition focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white"></div></label>
                <select wire:model.live="categoryFilter" class="h-12 rounded-2xl border-0 bg-zinc-50 px-4 text-xs font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white"><option value="all">Todas as Assinaturas</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select>
                <select wire:model.live="statusFilter" class="h-12 rounded-2xl border-0 bg-zinc-50 px-4 text-xs font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white"><option value="all">Todos estados</option><option value="active">Ativas</option><option value="paused">Pausadas</option><option value="cancelled">Canceladas</option></select>
                <select wire:model.live="cycleFilter" class="h-12 rounded-2xl border-0 bg-zinc-50 px-4 text-xs font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white"><option value="all">Todos ciclos</option><option value="monthly">Mensal</option><option value="quarterly">Trimestral</option><option value="semiannual">Semestral</option><option value="annual">Anual</option></select>
                <select wire:model.live="sortBy" class="h-12 rounded-2xl border-0 bg-zinc-50 px-4 text-xs font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white"><option value="billing_day">Dia de debito</option><option value="next_billing">Mais proxima</option><option value="amount_desc">Maior valor</option><option value="amount_asc">Menor valor</option><option value="name">Nome</option></select>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <button wire:click="$set('amountFilter', 'all')" class="h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $amountFilter === 'all' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500' }}">Todos valores</button>
                <button wire:click="$set('amountFilter', 'under_10')" class="h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $amountFilter === 'under_10' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500' }}">Ate 10 EUR</button>
                <button wire:click="$set('amountFilter', '10_30')" class="h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $amountFilter === '10_30' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500' }}">10 a 30 EUR</button>
                <button wire:click="$set('amountFilter', 'over_30')" class="h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $amountFilter === 'over_30' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500' }}">Mais de 30 EUR</button>
                <button wire:click="resetFilters" class="ml-auto h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-500/10">Limpar filtros</button>
            </div>
        </div>
        <div class="flex items-center justify-between gap-3 px-2"><div class="flex items-center gap-3"><div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500"><flux:icon name="queue-list" variant="outline" class="size-4" /></div><h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Assinaturas encontradas</h2></div><span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ $subscriptions->count() }} resultados</span></div>
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @foreach($subscriptions as $sub)
                <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm hover:border-brand-500/40 transition-all duration-200 group"><div class="p-6 flex flex-col gap-5">
                    <div class="flex items-start justify-between gap-4"><div class="flex items-start gap-4 min-w-0"><div class="size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 flex flex-col items-center justify-center shadow-inner shrink-0"><span class="text-[8px] font-black text-zinc-400 uppercase leading-none mb-1">Dia</span><span class="text-xl font-black text-brand-600 leading-none tracking-tighter">{{ str_pad($sub->billing_day, 2, '0', STR_PAD_LEFT) }}</span></div><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h4 class="font-black dark:text-white uppercase text-base tracking-tight truncate">{{ $sub->name }}</h4><span class="px-2.5 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest {{ $statusStyles[$sub->status] ?? $statusStyles['active'] }}">{{ $statusLabels[$sub->status] ?? 'Ativa' }}</span></div><div class="mt-2 flex flex-wrap items-center gap-2"><span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[9px] font-black uppercase tracking-widest rounded-md border border-zinc-200 dark:border-zinc-700">{{ $sub->category?->name ?? 'Sem categoria' }}</span>@if($sub->paid_by_company)<span class="px-2 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 text-[9px] font-black uppercase tracking-widest rounded-md border border-amber-200 dark:border-amber-500/20">Pago pela empresa</span>@endif<span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[9px] font-black uppercase tracking-widest rounded-md border border-zinc-200 dark:border-zinc-700">{{ $cycleLabels[$sub->cycle] ?? 'Mensal' }}</span>@if($sub->bankAccount)<span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[9px] font-black uppercase tracking-widest rounded-md border border-zinc-200 dark:border-zinc-700">{{ $sub->bankAccount->name }}</span>@endif</div></div></div><div class="text-right shrink-0"><p class="text-2xl font-black dark:text-white tracking-tighter italic">{{ number_format($sub->amount, 2, ',', ' ') }} <small class="text-xs">EUR</small></p><p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ number_format($sub->monthly_equivalent, 2, ',', ' ') }} EUR / mes</p></div></div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3"><div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-100 dark:border-zinc-800"><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Proximo</p><p class="text-sm font-black dark:text-white mt-1">{{ $sub->next_billing_date->format('d/m') }}</p></div><div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-100 dark:border-zinc-800"><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Faltam</p><p class="text-sm font-black dark:text-white mt-1">{{ $sub->days_until_billing }} dias</p></div><div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-100 dark:border-zinc-800"><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Inicio</p><p class="text-sm font-black dark:text-white mt-1">{{ $sub->started_at ? $sub->started_at->format('d/m/Y') : '-' }}</p></div><div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-100 dark:border-zinc-800"><p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Renova</p><p class="text-sm font-black dark:text-white mt-1">{{ $sub->renewal_date ? $sub->renewal_date->format('d/m/Y') : '-' }}</p></div></div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1"><div class="flex flex-wrap items-center gap-2">@if($sub->notify_before_billing)<span class="inline-flex items-center gap-2 rounded-xl bg-brand-50 dark:bg-brand-500/10 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-brand-700 dark:text-brand-300"><flux:icon name="bell" class="size-3.5" />Avisar {{ $sub->notify_days_before ?: 1 }} dias antes</span>@else<span class="inline-flex items-center gap-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-zinc-400"><flux:icon name="bell-slash" class="size-3.5" />Sem lembrete</span>@endif @if($sub->notes)<span class="text-xs font-semibold text-zinc-500 line-clamp-1">{{ $sub->notes }}</span>@endif</div><div class="flex items-center gap-2"><div x-data="{ open: false }" class="relative"><button @click="open = !open" @click.outside="open = false" title="Mais opções" class="rounded-xl p-2 text-zinc-400 transition-all hover:bg-zinc-100 dark:hover:bg-zinc-800"><flux:icon name="ellipsis-horizontal" class="size-4" /></button><div x-show="open" x-cloak x-transition class="absolute right-0 bottom-full mb-2 z-50 w-52 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl overflow-hidden"><div class="p-1"><button wire:click="openPaymentHistory({{ $sub->id }})" @click="open = false" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-all text-left"><flux:icon name="clock" class="size-4" /> Histórico de mensalidades</button><button wire:click="edit({{ $sub->id }})" @click="open = false" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-all text-left"><flux:icon name="pencil-square" class="size-4" /> Editar Detalhes</button><button wire:click="duplicate({{ $sub->id }})" @click="open = false" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-all text-left"><flux:icon name="document-duplicate" class="size-4" /> Duplicar</button><div class="my-1 border-t border-zinc-100 dark:border-zinc-800"></div>@if($sub->status === 'active')<button wire:click="toggleStatus({{ $sub->id }})" @click="open = false" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-all text-left"><flux:icon name="pause-circle" class="size-4" /> Pausar Assinatura</button>@else<button wire:click="toggleStatus({{ $sub->id }})" @click="open = false" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-xl transition-all text-left"><flux:icon name="play-circle" class="size-4" /> Reativar agora</button>@endif<div class="my-1 border-t border-zinc-100 dark:border-zinc-800"></div><button wire:click="delete({{ $sub->id }})" wire:confirm="Eliminar permanentemente a assinatura '{{ $sub->name }}'?" @click="open = false" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-all text-left"><flux:icon name="trash" class="size-4" /> Remover Assinatura</button></div></div></div></div></div>
                </div></div>
            @endforeach
            @if($subscriptions->isEmpty())<div class="xl:col-span-2 py-20 text-center glass-card rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800"><flux:icon name="credit-card" class="size-12 text-zinc-300 mx-auto mb-4" /><p class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px]">Sem assinaturas para estes filtros</p><button wire:click="resetFilters" class="mt-5 text-brand-600 font-black text-[10px] uppercase tracking-widest">Limpar filtros</button></div>@endif
        </div>
    </div>

    @endunless

    @if($showExtraModal)
    <div x-data="{ open: false, show(){requestAnimationFrame(()=>{this.open=true;document.documentElement.classList.add('overflow-hidden')})}, close(){this.open=false;document.documentElement.classList.remove('overflow-hidden');setTimeout(()=> $wire.closeExtraModal(),50)} }" x-init="show()" x-on:keydown.escape.window="close()">
        <div x-show="open" x-cloak x-transition.opacity.duration.50ms @click="close()" class="fixed inset-0 {{ $dashboardMode ? 'z-[69]' : 'z-50' }} bg-zinc-950/70"></div>
        <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 {{ $dashboardMode ? 'z-[70]' : 'z-50' }} {{ $dashboardMode && empty($onboardingModal) ? 'lg:left-64 lg:w-[calc(100vw-16rem)]' : '' }} flex items-center justify-center p-4 will-change-transform">
            <div @click.stop x-show="open" x-transition:enter="transition duration-65 ease-[cubic-bezier(.18,.89,.32,1.28)] transform-gpu" x-transition:enter-start="opacity-0 scale-[0.997] translate-y-0.5" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition duration-50 ease-[cubic-bezier(.4,0,.2,1)] transform-gpu" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-[0.985] translate-y-1" class="relative w-full max-w-2xl overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-xl dark:border-zinc-800 dark:bg-zinc-900">
                <form wire:submit.prevent="save" class="flex max-h-[86vh] flex-col">
                    <div class="flex shrink-0 items-center gap-4 border-b border-zinc-100 p-6 pb-4 dark:border-zinc-800">
                        <div class="rounded-2xl bg-brand-600 p-3 text-white shadow"><flux:icon name="{{ $transportPassMode ? 'train-front' : 'credit-card' }}" class="size-5" /></div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-xl font-black uppercase italic tracking-tight text-zinc-900 dark:text-white">{{ $transportPassMode ? ($editingId ? 'Editar Passe Mensal' : 'Novo Passe Mensal') : ($editingId ? 'Editar Assinatura' : 'Nova Assinatura') }}</h2>
                            <p class="mt-1.5 text-[10px] font-black uppercase italic tracking-widest text-zinc-400">{{ $transportPassMode ? 'Configuração do passe mensal de transporte' : 'Configuração completa de custo recorrente' }}</p>
                        </div>
                        <button type="button" @click="close()" aria-label="Fechar janela" class="rounded-full p-2 text-zinc-400 transition-all hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-white"><flux:icon name="x-mark" class="size-5" /></button>
                    </div>
                    <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">
                        @include('livewire.partials.onboarding-validation-errors')
                        @php
                            $subscriptionLabelClass = 'text-[10px] font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400';
                            $subscriptionInputClass = 'h-14 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 text-sm font-bold text-zinc-900 placeholder:text-zinc-400 outline-none focus:ring-2 focus:ring-brand-500/40 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white';
                        @endphp
                        @if(!$editingId)
                            <div class="grid grid-cols-2 gap-2 rounded-2xl border border-zinc-200 bg-zinc-100 p-1.5 dark:border-zinc-700 dark:bg-zinc-950">
                                <button type="button" wire:click="$set('transportPassMode', false)" class="h-11 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ !$transportPassMode ? 'bg-brand-600 text-white shadow-lg' : 'text-zinc-500 hover:bg-white dark:text-zinc-400 dark:hover:bg-zinc-800' }}">Assinatura normal</button>
                                <button type="button" wire:click="$set('transportPassMode', true)" class="h-11 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $transportPassMode ? 'bg-brand-600 text-white shadow-lg' : 'text-zinc-500 hover:bg-white dark:text-zinc-400 dark:hover:bg-zinc-800' }}">Passe mensal</button>
                            </div>
                        @endif
                        @if($transportPassMode)
                            <div class="space-y-5">
                                <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 dark:border-sky-900 dark:bg-sky-950/30">
                                    <p class="text-xs font-black uppercase tracking-widest text-sky-700 dark:text-sky-300">Passe mensal de transporte</p>
                                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">Será criada automaticamente uma assinatura mensal na categoria Transporte.</p>
                                </div>
                                <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Nome do passe</span><input wire:model="transportPassName" type="text" placeholder="Ex: Passe Navegante" class="{{ $subscriptionInputClass }}"></label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Valor mensal (EUR)</span><input wire:model="transportPassAmount" type="number" min="0.01" step="0.01" placeholder="40.00" class="{{ $subscriptionInputClass }}"></label>
                                    <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Data de início / pagamento</span><input wire:model="transportPassStartedAt" type="date" class="{{ $subscriptionInputClass }}"></label>
                                </div>
                                <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Método de pagamento</span><select wire:model="transportPassPaymentMethod" class="{{ $subscriptionInputClass }}"><option value="card">Cartão</option><option value="direct_debit">Débito direto</option><option value="bank_transfer">Transferência</option><option value="paypal">PayPal</option><option value="cash">Numerário</option></select></label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950"><input type="checkbox" wire:model="transportPassPaidByCompany" class="mt-1 rounded border-zinc-300 text-brand-500 focus:ring-brand-500"><span><span class="block text-sm font-black text-zinc-900 dark:text-white">Pago pela empresa?</span><span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400">Quando ativo, o passe fica registado, mas é excluído das despesas, previsões e totais financeiros pessoais.</span></span></label>
                                @if($transportPassPaidByCompany)
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-semibold text-amber-800 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200">Atenção: este valor não será contabilizado nos teus custos pessoais.</div>
                                @endif
                            </div>
                        @else
                        <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Nome da assinatura</span><input wire:model="name" type="text" placeholder="Ex: Netflix, Spotify, Renda..." class="{{ $subscriptionInputClass }}"></label>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Valor</span><input wire:model="amount" type="number" step="0.01" min="0" placeholder="12.99" class="{{ $subscriptionInputClass }}"></label>
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Dia de débito</span><input wire:model="billing_day" type="number" min="1" max="31" placeholder="Ex: 15" class="{{ $subscriptionInputClass }}"></label>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Categoria</span><select wire:model="category_id" class="{{ $subscriptionInputClass }}"><option value="">Selecionar tipo de serviço...</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></label>
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Ciclo</span><select wire:model.live="billing_cycle" class="{{ $subscriptionInputClass }}"><option value="monthly">Mensal</option><option value="quarterly">Trimestral</option><option value="semiannual">Semestral</option><option value="annual">Anual</option></select></label>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Conta bancária</span><select wire:model="bank_account_id" required class="{{ $subscriptionInputClass }}"><option value="">Selecionar conta bancária...</option>@foreach($bankAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }}{{ $account->bank_name ? ' · '.$account->bank_name : '' }}{{ $account->iban ? ' · '.substr($account->iban, -4) : '' }}</option>@endforeach</select>@if($bankAccounts->isEmpty())<span class="text-xs font-semibold text-amber-600">Ainda não tens contas bancárias registadas neste espaço financeiro.</span>@endif</label>
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Estado</span><select wire:model="status" class="{{ $subscriptionInputClass }}"><option value="active">Ativa</option><option value="paused">Pausada</option><option value="cancelled">Cancelada</option></select></label>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Início</span><input wire:model.live="started_at" type="date" class="{{ $subscriptionInputClass }}"></label>
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Renovação</span><input type="date" value="{{ $renewal_date }}" disabled class="{{ $subscriptionInputClass }} cursor-not-allowed opacity-75"><span class="block text-[10px] font-semibold text-zinc-400">Calculada automaticamente com base na data de início (ou na data atual, se não estiver preenchida) e no ciclo selecionado.</span></label>
                        </div>
                        <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Notas</span><textarea wire:model="notes" rows="3" placeholder="Ex: plano familiar, contrato anual, promoção termina em breve..." class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm font-bold text-zinc-900 placeholder:text-zinc-400 outline-none focus:ring-2 focus:ring-brand-500/40 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></textarea></label>
                        <div class="space-y-3 rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950">
                            <div class="flex items-center justify-between gap-4"><div><p class="{{ $subscriptionLabelClass }}">Lembrete de cobrança</p><p class="mt-1 text-xs font-bold text-zinc-500 dark:text-zinc-400">Avisar antes da renovação ou débito.</p></div><input type="checkbox" wire:model="notify_before_billing" class="rounded border-zinc-300 text-brand-500 focus:ring-brand-500"></div>
                            <label class="block space-y-2"><span class="{{ $subscriptionLabelClass }}">Dias antes</span><input wire:model="notify_days_before" type="number" min="1" max="30" placeholder="3" class="{{ $subscriptionInputClass }}"></label>
                        </div>
                        @endif
                    </div>
                    <div class="flex shrink-0 flex-col gap-3 border-t border-zinc-100 bg-white p-6 pt-4 dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row"><button type="button" @click="close()" class="h-14 w-full rounded-2xl text-xs font-bold uppercase tracking-widest text-zinc-500 transition-all hover:bg-zinc-100 dark:hover:bg-zinc-800">Cancelar</button><button type="submit" wire:loading.attr="disabled" wire:target="save" class="h-14 w-full rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 transition-all hover:bg-brand-500 disabled:opacity-60"><span wire:loading.remove wire:target="save">{{ $editingId ? ($transportPassMode ? 'Atualizar Passe Mensal' : 'Atualizar Assinatura') : ($transportPassMode ? 'Criar Passe Mensal' : 'Guardar Assinatura') }}</span><span wire:loading wire:target="save">A guardar...</span></button></div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($showPaymentHistoryModal)
        <div class="fixed inset-0 z-[110] flex items-center justify-center p-4 sm:p-6" wire:click.self="closePaymentHistory">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="closePaymentHistory"></div>
            <section class="relative w-full max-w-2xl max-h-[85vh] overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">
                <header class="flex items-center justify-between gap-4 border-b border-zinc-200 p-5 dark:border-zinc-800">
                    <div><p class="text-[10px] font-black uppercase tracking-widest text-brand-600">Histórico de pagamentos</p><h3 class="mt-1 text-xl font-black text-zinc-900 dark:text-white">{{ $paymentHistoryTitle }}</h3><p class="mt-1 text-xs text-zinc-500">{{ $paymentHistoryCount }} ciclo(s) desde o início até hoje</p></div>
                    <button type="button" wire:click="closePaymentHistory" class="rounded-xl p-2 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800"><flux:icon name="x-mark" class="size-5" /></button>
                </header>
                <div class="border-b border-zinc-100 bg-emerald-50/70 px-5 py-4 dark:border-zinc-800 dark:bg-emerald-500/5">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400">Total estimado gasto desde o início</p>
                    <p class="mt-1 text-3xl font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format($paymentHistoryTotal, 2, ',', '.') }} €</p>
                    <p class="mt-1 text-[10px] text-zinc-500">Calculado com base no valor e na periodicidade da assinatura, incluindo os ciclos até hoje.</p>
                </div>
                <div class="max-h-[55vh] overflow-y-auto p-5">
                    @forelse($paymentHistory as $payment)
                        <article class="flex flex-col gap-3 border-b border-zinc-100 py-4 first:pt-0 last:border-0 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Mensalidade de {{ $payment['billing_date'] }}</p><p class="mt-1 text-xs text-zinc-500">{{ $payment['source'] }}</p></div>
                            <p class="text-base font-black tabular-nums text-blue-600">{{ number_format($payment['amount'], 2, ',', '.') }} €</p>
                        </article>
                    @empty
                        <div class="py-12 text-center"><div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800"><flux:icon name="inbox" class="size-6" /></div><p class="mt-4 text-sm font-bold text-zinc-700 dark:text-zinc-300">Ainda não existem mensalidades registadas.</p><p class="mt-1 text-xs text-zinc-500">Os pagamentos automáticos aparecerão aqui após cada renovação.</p></div>
                    @endforelse
                </div>
            </section>
        </div>
    @endif

    @unless($dashboardMode)
        <footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60"><p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">(c) {{ date('Y') }} {{ config('app.name') }} - Sistema de Monitorizacao</p></footer>
    @endunless

    @if($showPlatformPlanModal)
        @php
            $stripe = $stripePlanDetails;
            $customer = $stripe['customer'] ?? [];
            $stripeSub = $stripe['subscription'] ?? [];
            $card = $stripe['payment_method'] ?? [];
            $invoices = $stripe['invoices'] ?? [];
            $formatStripeDate = function ($timestamp) { return $timestamp ? \Illuminate\Support\Carbon::createFromTimestamp($timestamp)->format('d/m/Y H:i') : '—'; };
            $formatStripeMoney = function ($amount, $currency = 'EUR') { return $amount === null ? '—' : number_format($amount / 100, 2, ',', ' ').' '.strtoupper($currency); };
        @endphp
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" wire:click.self="closePlatformPlanModal">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="closePlatformPlanModal"></div>
            <div class="relative w-full max-w-5xl max-h-[92vh] overflow-y-auto rounded-[2rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl">
                <div class="sticky top-0 z-20 flex items-center justify-between gap-4 p-6 border-b border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur">
                    <div><p class="text-[9px] font-black uppercase tracking-[0.3em] text-brand-600">Finance Pro • Stripe</p><h3 class="text-2xl font-black tracking-tight">Detalhes do plano e pagamentos</h3><p class="text-xs text-zinc-500 mt-1">Informação sincronizada directamente com o Stripe.</p></div>
                    <button type="button" wire:click="closePlatformPlanModal" class="size-10 rounded-xl flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-800"><flux:icon name="x-mark" class="size-5" /></button>
                </div>

                <div class="p-6 space-y-6">
                    @if($stripePlanError)
                        <div class="rounded-2xl border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-950/20 p-5"><p class="text-sm font-bold text-amber-800 dark:text-amber-300">{{ $stripePlanError }}</p><p class="mt-1 text-xs text-amber-700 dark:text-amber-400">Os dados locais do plano continuam disponíveis, mas os detalhes Stripe não puderam ser sincronizados.</p></div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 p-5"><p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Plano</p><p class="mt-1 text-lg font-black">Finance Pro {{ $platformPlan?->name ?? '—' }}</p></div>
                        <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 p-5"><p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Valor</p><p class="mt-1 text-lg font-black">{{ $formatStripeMoney($stripeSub['amount'] ?? null, $stripeSub['currency'] ?? 'EUR') !== '—' ? $formatStripeMoney($stripeSub['amount'] ?? null, $stripeSub['currency'] ?? 'EUR').' / mês' : number_format($platformPlan?->price ?? 0, 2, ',', ' ').' EUR / mês' }}</p></div>
                        <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 p-5"><p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Estado Stripe</p><p class="mt-1 text-lg font-black">{{ $stripeSub['status_label'] ?? ($stripeSub['status'] ?? 'Sem subscrição Stripe recorrente') }}</p><p class="text-[10px] text-zinc-500 mt-1">{{ $stripeSub['status'] ?? '—' }}</p></div>
                        <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 p-5"><p class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Estado local</p><p class="mt-1 text-lg font-black">{{ $platformCashierSubscription?->active() ? 'Ativa' : 'Sem subscrição Cashier activa' }}</p></div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <section class="rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 space-y-4"><div class="flex items-center gap-3"><div class="p-2 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600"><flux:icon name="user" class="size-5" /></div><div><h4 class="text-sm font-black uppercase tracking-widest">Cliente Stripe</h4><p class="text-xs text-zinc-500">Dados da conta de faturação</p></div></div><div class="grid grid-cols-1 sm:grid-cols-2 gap-4"><div><p class="text-[9px] font-black uppercase text-zinc-500">Nome</p><p class="text-sm font-bold mt-1">{{ $customer['name'] ?? '—' }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Email</p><p class="text-sm font-bold mt-1 break-all">{{ $customer['email'] ?? '—' }}</p></div><div class="sm:col-span-2"><p class="text-[9px] font-black uppercase text-zinc-500">Stripe Customer ID</p><p class="text-xs font-mono font-bold mt-1 break-all">{{ $customer['id'] ?? '—' }}</p></div></div></section>
                        <section class="rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 space-y-4"><div class="flex items-center gap-3"><div class="p-2 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600"><flux:icon name="arrow-path" class="size-5" /></div><div><h4 class="text-sm font-black uppercase tracking-widest">Subscrição Stripe</h4><p class="text-xs text-zinc-500">Ciclo e estado actual</p></div></div><div class="grid grid-cols-1 sm:grid-cols-2 gap-4"><div class="sm:col-span-2"><p class="text-[9px] font-black uppercase text-zinc-500">Stripe Subscription ID</p><p class="text-xs font-mono font-bold mt-1 break-all">{{ $stripeSub['id'] ?? '—' }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Criada</p><p class="text-sm font-bold mt-1">{{ $formatStripeDate($stripeSub['created'] ?? null) }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Próximo período</p><p class="text-sm font-bold mt-1">{{ $formatStripeDate($stripeSub['current_period_end'] ?? null) }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Início do período</p><p class="text-sm font-bold mt-1">{{ $formatStripeDate($stripeSub['current_period_start'] ?? null) }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Cancelamento</p><p class="text-sm font-bold mt-1">{{ !empty($stripeSub['cancel_at_period_end']) ? 'No fim do período' : ($stripeSub['cancel_at'] ?? null ?? null ?? null ? $formatStripeDate($stripeSub['cancel_at'] ?? null ?? null ?? null ?? null) : 'Não agendado') }}</p></div></div></section>
                    </div>

                    <section class="rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5"><div class="flex items-center gap-3 mb-5"><div class="p-2 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600"><flux:icon name="credit-card" class="size-5" /></div><div><h4 class="text-sm font-black uppercase tracking-widest">Método de pagamento</h4><p class="text-xs text-zinc-500">Apenas dados de cartão seguros fornecidos pelo Stripe</p></div></div>@if($card)<div class="grid grid-cols-2 md:grid-cols-4 gap-4"><div><p class="text-[9px] font-black uppercase text-zinc-500">Tipo</p><p class="text-sm font-bold mt-1 uppercase">{{ $card['type'] ?? '—' }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Cartão</p><p class="text-sm font-bold mt-1 uppercase">{{ $card['brand'] ?? '—' }} •••• {{ $card['last4'] ?? '—' }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Validade</p><p class="text-sm font-bold mt-1">{{ $card['exp_month'] ?? '—' }}/{{ $card['exp_year'] ?? '—' }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Titular</p><p class="text-sm font-bold mt-1">{{ $card['holder'] ?? '—' }}</p></div><div class="col-span-2 md:col-span-4"><p class="text-[9px] font-black uppercase text-zinc-500">Payment Method ID</p><p class="text-xs font-mono font-bold mt-1 break-all">{{ $card['id'] ?? '—' }}</p></div></div>@else<div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 text-sm text-zinc-500">Não existe um método de pagamento Stripe disponível para esta subscrição.</div>@endif</section>

                    <section class="rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5"><div class="flex items-center gap-3 mb-5"><div class="p-2 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600"><flux:icon name="map-pin" class="size-5" /></div><div><h4 class="text-sm font-black uppercase tracking-widest">Morada de faturação</h4><p class="text-xs text-zinc-500">Endereço registado no Stripe</p></div></div><div class="grid grid-cols-2 md:grid-cols-3 gap-4"><div class="col-span-2 md:col-span-3"><p class="text-[9px] font-black uppercase text-zinc-500">Morada</p><p class="text-sm font-bold mt-1">{{ $customer['address']['line1'] ?? '—' }}{{ !empty($customer['address']['line2']) ? ', '.$customer['address']['line2'] : '' }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Cidade</p><p class="text-sm font-bold mt-1">{{ $customer['address']['city'] ?? '—' }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Código postal</p><p class="text-sm font-bold mt-1">{{ $customer['address']['postal_code'] ?? '—' }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">País</p><p class="text-sm font-bold mt-1">{{ $customer['address']['country'] ?? '—' }}</p></div></div></section>

                    <section><div class="flex items-center justify-between gap-3 mb-3"><div><h4 class="text-sm font-black uppercase tracking-widest">Histórico Stripe</h4><p class="text-xs text-zinc-500 mt-1">Invoices e pagamentos associados ao cliente</p></div><span class="text-xs font-bold text-zinc-500">{{ count($invoices) }} invoice(s)</span></div><div class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800">@forelse($invoices as $invoice)<div class="p-5 border-b last:border-b-0 border-zinc-200 dark:border-zinc-800"><div class="grid grid-cols-1 md:grid-cols-7 gap-4 items-start"><div class="md:col-span-2"><p class="text-[9px] font-black uppercase text-zinc-500">Invoice</p><p class="text-xs font-mono font-bold mt-1 break-all">{{ $invoice['number'] ?: $invoice['id'] }}</p><p class="text-[10px] text-zinc-500 mt-1 break-all">{{ $invoice['id'] }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Valor pago</p><p class="text-sm font-black mt-1">{{ $formatStripeMoney($invoice['amount_paid'], $invoice['currency']) }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Estado</p><p class="text-sm font-bold mt-1">{{ ucfirst($invoice['status'] ?? '—') }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Criada</p><p class="text-xs font-bold mt-1">{{ $formatStripeDate($invoice['created']) }}</p></div><div><p class="text-[9px] font-black uppercase text-zinc-500">Paga</p><p class="text-xs font-bold mt-1">{{ $formatStripeDate($invoice['paid_at']) }}</p></div><div class="flex flex-wrap gap-2">@if($invoice['hosted_invoice_url'])<a href="{{ $invoice['hosted_invoice_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-50 dark:bg-brand-500/10 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-brand-700 dark:text-brand-300">Invoice</a>@endif @if($invoice['invoice_pdf'])<a href="{{ $invoice['invoice_pdf'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-300">PDF</a>@endif</div></div>@if($invoice['payment_intent_id'])<p class="mt-3 text-[10px] text-zinc-500">PaymentIntent: <span class="font-mono font-bold break-all">{{ $invoice['payment_intent_id'] }}</span></p>@endif</div>@empty<div class="p-8 text-sm text-zinc-500">Ainda não existem invoices Stripe para este cliente.</div>@endforelse</div></section>

                    <div class="rounded-2xl border border-blue-200 dark:border-blue-900/50 bg-blue-50 dark:bg-blue-950/20 p-5"><div class="flex gap-3"><flux:icon name="shield-check" class="size-5 shrink-0 text-blue-600 dark:text-blue-400" /><div><p class="text-xs font-black text-blue-800 dark:text-blue-300">Proteção de dados de pagamento</p><p class="mt-1 text-xs text-blue-700 dark:text-blue-400">O Finance Pro mostra apenas dados seguros fornecidos pelo Stripe, como marca, últimos 4 dígitos e validade. O número completo do cartão e o CVC nunca são recolhidos, guardados ou apresentados pela aplicação.</p></div></div></div>
                </div>
            </div>
        </div>
    @endif
</div>
