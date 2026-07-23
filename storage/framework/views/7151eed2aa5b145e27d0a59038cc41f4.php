<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#10b981">



    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Finance Pro">

    
    <link rel="apple-touch-icon" href="/pwa/splash_screens/apple-icon-180x180.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/icon-512x512.png">

   
    

    <title><?php echo e(config('app.name')); ?></title>

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

    <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <link rel="stylesheet" href="/flux/flux.css">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/app.css',
        'resources/js/app.js'
    ]); ?>
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
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>











<?php
    $user = auth()->user();
    $currentWs = $user ? $user->currentWorkspace : null;

    // 1. Procurar o cofre pessoal onde o Eduardo é o DONO real
    $myPersonalWs = $user ? $user->workspaces()
        ->where('type', 'personal')
        ->where('owner_id', $user->id)
        ->first() : null;

    // Garantimos que o ID é um número ou null
    $myPersonalWsId = $myPersonalWs?->id;

    // 2. Lógica de Observação: Só é verdade se o dono do cofre ATUAL não for o Eduardo
    // E se o Eduardo tiver um cofre dele para onde possa voltar (myPersonalWsId não nulo)
    $isViewingOthers = $currentWs &&
                       ($currentWs->type === 'personal') &&
                       ($currentWs->owner_id !== $user?->id) &&
                       ($myPersonalWsId !== null);
    // 1. DEFINIÇÃO GLOBAL DE EMPRESAS
    $allBusinessWs = ($user)
        ? $user->workspaces()->where('type', '!=', 'personal')->get()
        : collect();

    $myCompanies = $allBusinessWs->filter(fn($ws) => $ws->pivot->role === 'admin');
    $collabCompanies = $allBusinessWs->filter(fn($ws) => $ws->pivot->role !== 'admin');
  $myRestrictions = \App\Models\FamilyBudgetPermission::where('user_id', auth()->id())
        ->where('workspace_id', auth()->user()->current_workspace_id)
        ->whereNull('category_id')
        ->first();
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


?>











<body
    class="layout-fixed app-shell antialiased bg-zinc-50 dark:bg-zinc-950 h-screen overflow-hidden flex"
    x-data="{
        privacyMode: localStorage.getItem('privacyMode') === 'true',
        mobileSidebarOpen: false,
        isOnline: navigator.onLine, 
        offlineQueueCount: JSON.parse(localStorage.getItem('offline_vault') || '[]').length
    }"
    x-init="
        $watch('privacyMode', v => localStorage.setItem('privacyMode', v));
        window.addEventListener('online', () => { isOnline = true; syncOfflineData(); });
        window.addEventListener('offline', () => { isOnline = false; });
    "
    x-on:privacy-changed.window="privacyMode = $event.detail.state"
    :class="{ 'privacy-mode': privacyMode }"
>




<div x-show="!isOnline" x-cloak
     x-data="{
        statusNotice: '',
        amount: '',
        desc: '',
        cat: 'Geral',
        calcTotal: 0,
        calcInput: '',

        saveOffline() {
            if(!this.amount) return;

            const newItem = {
                id: Date.now(),
                amount: this.amount,
                description: this.desc || 'Sem descrição',
                category: this.cat,
                date: new Date().toISOString()
            };

            // Guardar e atualizar array para reatividade imediata
            offlineQueue.push(newItem);
            localStorage.setItem('offline_vault', JSON.stringify(offlineQueue));

            // Feedback visual para o utilizador
            this.statusNotice = `✅ Adicionado: ${newItem.description} (${newItem.amount}€)`;

            // Limpar campos
            this.amount = '';
            this.desc = '';

            // Esconder aviso após 3 segundos
            setTimeout(() => { this.statusNotice = ''; }, 4000);
        }
     }"
     class="fixed inset-0 z-[10000] overflow-y-auto bg-zinc-50 text-zinc-900 flex flex-col font-sans">

    
    <nav class="h-20 bg-white border-b border-zinc-200 flex items-center justify-between px-8 sticky top-0 z-20 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="size-10 rounded-xl bg-red-600 flex items-center justify-center text-white shadow-lg animate-pulse">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'bolt-slash','variant' => 'solid','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bolt-slash','variant' => 'solid','class' => 'size-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
            </div>
            <div class="text-left">
                <h2 class="text-sm font-black uppercase tracking-widest italic leading-none">Bunker Offline</h2>
                <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-[0.2em] mt-1.5">Eduardo Patricio · <span class="text-emerald-600">Modo Local</span></p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-[10px] font-black leading-none text-zinc-400 uppercase tracking-widest">Estado</p>
                <p class="text-[11px] font-black text-amber-500 mt-1 uppercase" x-text="offlineQueue.length > 0 ? 'Sincronização Pendente' : 'Standby'"></p>
            </div>
            <div class="size-2.5 rounded-full" :class="offlineQueue.length > 0 ? 'bg-amber-500 animate-ping' : 'bg-zinc-300'"></div>
        </div>
    </nav>

    <div class="relative w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 p-6 md:p-10 animate-in fade-in slide-in-from-bottom-4 duration-700">

        
        <div class="lg:col-span-7 space-y-6">

            
            <div x-show="statusNotice"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-sm">
                <span x-text="statusNotice"></span>
            </div>

            <section class="bg-white border border-zinc-200 p-8 rounded-[3rem] shadow-xl shadow-zinc-200/50 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase text-zinc-400 ml-4 tracking-[0.2em]">Quanto gastaste?</label>
                        <div class="relative group">
                            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-2xl font-black text-zinc-300 italic transition-colors group-focus-within:text-brand-600">€</span>
                            <input type="number" x-model="amount" placeholder="0.00"
                                   class="w-full h-24 bg-zinc-50 border-2 border-zinc-100 rounded-[2rem] text-5xl font-black text-center text-zinc-900 focus:border-brand-500 focus:ring-0 focus:bg-white transition-all shadow-inner">
                        </div>
                    </div>

                    <div class="space-y-6 pt-1">
                        <div>
                            <label class="text-[9px] font-black uppercase text-zinc-400 ml-4 tracking-[0.2em]">Onde?</label>
                            <input type="text" x-model="desc" placeholder="Ex: Café, Lidl..."
                                   class="w-full h-14 bg-zinc-50 border border-zinc-200 rounded-2xl px-6 font-bold text-sm text-zinc-800 focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-zinc-400 ml-4 tracking-[0.2em]">Categoria</label>
                            <select x-model="cat" class="w-full h-14 bg-zinc-50 border border-zinc-200 rounded-2xl px-4 font-black uppercase text-[10px] tracking-widest text-zinc-600 outline-none transition-all">
                                <option value="Geral">📂 Geral</option>
                                <option value="Alimentação">🛒 Alimentação</option>
                                <option value="Transporte">⛽ Transporte</option>
                                <option value="Lazer">🍿 Lazer</option>
                                <option value="Saúde">💊 Saúde</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button @click="saveOffline"
                    class="w-full h-20 bg-zinc-900 hover:bg-black text-white rounded-[2rem] font-black uppercase tracking-[0.3em] text-xs shadow-2xl transition-all active:scale-95 flex items-center justify-center gap-4">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'document-plus','variant' => 'solid','class' => 'size-6 text-brand-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'document-plus','variant' => 'solid','class' => 'size-6 text-brand-500']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    Confirmar Registo 🟢
                </button>
            </section>

            
            <div class="bg-white border border-zinc-200 p-6 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-left">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 italic">Soma de tickets rápida</p>
                    <p class="text-4xl font-black text-zinc-900 italic tracking-tighter" x-text="calcTotal.toFixed(2) + ' €'"></p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <input type="number" x-model="calcInput" placeholder="Soma..." class="flex-1 md:w-28 h-14 bg-zinc-100 border-none rounded-2xl text-center text-zinc-900 font-black">
                    <button @click="calcTotal += parseFloat(calcInput || 0); calcInput = ''" class="size-14 bg-brand-600 text-white rounded-2xl shadow-lg font-black text-xl">+</button>
                    <button @click="calcTotal = 0" class="px-5 h-14 border border-zinc-200 rounded-2xl text-red-500 font-black">C</button>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-5 space-y-6">

            
            <div class="p-6 bg-zinc-100 rounded-[2.5rem] border border-zinc-200 flex items-center gap-5">
                <div class="size-16 bg-white rounded-2xl border-4 border-white shadow-xl overflow-hidden shrink-0">
                    <img src="<?php echo e(asset($currentWs->logo_path ?? 'icon-192x192.png')); ?>" class="size-full object-cover">
                </div>
                <div class="text-left truncate">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em]">Cofre Ativo</p>
                    <p class="text-lg font-black text-zinc-900 uppercase italic truncate leading-none mt-1"><?php echo e($currentWs->name); ?></p>
                </div>
            </div>

            
            <div class="bg-white border border-zinc-200 rounded-[3rem] p-8 shadow-sm flex flex-col max-h-[600px]">
                <div class="flex items-center justify-between mb-8 border-b border-zinc-100 pb-4">
                    <div class="flex items-center gap-2">
                        <span class="size-5 rounded bg-brand-100 text-brand-600 flex items-center justify-center text-[10px] font-black" x-text="offlineQueue.length"></span>
                        <span class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Logs Pendentes</span>
                    </div>
                    <button @click="if(confirm('Apagar histórico offline?')){ offlineQueue = []; localStorage.setItem('offline_vault', '[]'); }"
                            class="text-[9px] font-black text-red-400 hover:text-red-600 uppercase tracking-tighter transition-colors">Limpar Tudo</button>
                </div>

                <div class="overflow-y-auto custom-scrollbar space-y-4 pr-1 flex-1 text-left">
                    <template x-for="(item, index) in offlineQueue" :key="item.id">
                        <div class="p-5 bg-zinc-50 border border-zinc-100 rounded-3xl group transition-all">
                            <div class="flex justify-between items-start">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="px-2 py-0.5 bg-white border border-zinc-200 rounded text-[7px] font-black uppercase text-zinc-400" x-text="item.category"></span>
                                        <span class="text-[7px] font-black text-zinc-300 uppercase italic" x-text="new Date(item.date).toLocaleTimeString()"></span>
                                    </div>
                                    <h5 class="text-xs font-black text-zinc-900 uppercase truncate" x-text="item.description"></h5>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-black text-emerald-600 tracking-tighter italic" x-text="parseFloat(item.amount).toFixed(2) + '€'"></p>
                                    <button @click="offlineQueue.splice(index, 1); localStorage.setItem('offline_vault', JSON.stringify(offlineQueue))"
                                            class="mt-2 size-6 rounded-lg text-zinc-300 hover:text-red-500 transition-colors flex items-center justify-center">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'trash','variant' => 'micro','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','variant' => 'micro','class' => 'size-3.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="offlineQueue.length === 0">
                        <div class="py-24 text-center space-y-3 opacity-20 italic">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'archive-box','class' => 'size-12 mx-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'archive-box','class' => 'size-12 mx-auto']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                            <p class="text-[9px] font-black uppercase tracking-widest">Bunker Vazio</p>
                        </div>
                    </template>
                </div>

                
                <div x-show="offlineQueue.length > 0" class="mt-6 pt-6 border-t border-zinc-100 flex justify-between items-center">
                    <span class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Total na Fila:</span>
                    <span class="text-3xl font-black text-emerald-600 italic tracking-tighter"
                          x-text="offlineQueue.reduce((acc, curr) => acc + parseFloat(curr.amount), 0).toFixed(2) + '€'"></span>
                </div>
            </div>

            <div class="p-6 bg-zinc-950 text-white rounded-[2.5rem] flex items-center gap-4">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'signal-slash','variant' => 'solid','class' => 'size-6 text-brand-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'signal-slash','variant' => 'solid','class' => 'size-6 text-brand-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                <p class="text-[9px] font-bold uppercase tracking-widest opacity-80 leading-relaxed text-left">
                    Os dados estão a ser cifrados e guardados localmente. Ao recuperar sinal de internet, serão processados pelo motor de auditoria PRO.
                </p>
            </div>
        </div>
    </div>
</div>
    
    
    
    <div x-show="isOnline" class="flex w-full"> 













 
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isViewingOthers && $currentWs->type !== 'business'): ?>
        <div class="fixed top-0 left-0 right-0 h-1 bg-amber-500 z-[100]"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('global-search', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isAdmin()): ?>
            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin-onboarding', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        <?php elseif(auth()->user()->isModerator()): ?>
            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('moderator-onboarding', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-2', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        <?php elseif(auth()->user()->isAnalyst()): ?>
            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('analyst-onboarding', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-3', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        <?php elseif(!auth()->user()->isAdminRole()): ?>
            
            <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('onboarding-wizard'); ?>">
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('onboarding-wizard', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-4', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

 
    
    
    <?php
        $globalAnnouncements = \Illuminate\Support\Facades\DB::table('site_announcements')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($globalAnnouncements->count() > 0): ?>
        <div x-data="{
                dismissed: JSON.parse(localStorage.getItem('dismissed_announcements') || '[]'),
                dismiss(id) {
                    this.dismissed.push(id);
                    localStorage.setItem('dismissed_announcements', JSON.stringify(this.dismissed));
                }
             }"
             x-on:close-announcement-globally.window="dismiss($event.detail.id)"
             class="fixed bottom-0 left-0 right-0 z-[100] flex flex-col">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $globalAnnouncements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $tipoNorm = strtolower($ann->type);
                    $isUrgent = in_array($tipoNorm, ['danger', 'urgente', 'erro']);
                    $colorClasses = match(true) {
                        in_array($tipoNorm, ['info', 'informativo']) => 'bg-blue-600 text-white',
                        in_array($tipoNorm, ['success', 'sucesso', 'concluído']) => 'bg-emerald-600 text-white',
                        in_array($tipoNorm, ['warning', 'aviso']) => 'bg-amber-400 text-black',
                        $isUrgent => 'bg-red-600 text-white',
                        default => 'bg-zinc-800 text-white',
                    };
                ?>

                <div x-show="'<?php echo e($isUrgent); ?>' || !dismissed.includes(<?php echo e($ann->id); ?>)"
                     class="w-full py-3 px-6 text-center shadow-[0_-10px_30px_rgba(0,0,0,0.2)] border-t border-white/10 flex items-center justify-between gap-3 <?php echo e($colorClasses); ?>">

                    <div class="w-10"></div>

                    <button class="flex-1 flex items-center justify-center gap-3 outline-none group"
                            x-on:click="$dispatch('show-announcement', {
                                id: <?php echo e($ann->id); ?>,
                                title: '<?php echo e($ann->title); ?>',
                                message: '<?php echo e(str_replace(['\r', '\n', "'"], ' ', $ann->message)); ?>',
                                type: '<?php echo e($tipoNorm); ?>',
                                date: '<?php echo e(\Carbon\Carbon::parse($ann->created_at)->format('d/m H:i')); ?>',
                                expires: '<?php echo e($ann->expires_at ? \Carbon\Carbon::parse($ann->expires_at)->diffForHumans() : 'Nunca'); ?>'
                            }); $dispatch('modal-show', { name: 'announcement-detail' })">

                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'megaphone','class' => 'size-4 opacity-80 group-hover:scale-125 transition-transform']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'megaphone','class' => 'size-4 opacity-80 group-hover:scale-125 transition-transform']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>

                        <div class="flex items-center gap-3">
                            <span class="bg-black/10 px-2 py-0.5 rounded text-[9px] font-black uppercase"><?php echo e($ann->title); ?></span>
                            <span class="text-[10px] md:text-[11px] font-black uppercase tracking-[0.2em] truncate max-w-[250px] md:max-w-none"><?php echo e($ann->message); ?></span>
                        </div>
                    </button>

                    <div class="w-10 flex justify-end">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isUrgent): ?>
                            <button x-on:click.stop="dismiss(<?php echo e($ann->id); ?>)" class="size-8 rounded-full flex items-center justify-center hover:bg-black/10 transition-all hover:rotate-90">
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','class' => 'size-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <style>
        body { padding-bottom: <?php echo e($globalAnnouncements->count() * 48); ?>px !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
    </style>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>














  
    
    
    <div
        x-show="mobileSidebarOpen"
        x-cloak
        x-transition.opacity
        x-on:click="mobileSidebarOpen = false"
        class="fixed inset-0 z-[45] bg-zinc-950/60 backdrop-blur-sm lg:hidden"
        aria-hidden="true"
    ></div>

    <?php if (isset($component)) { $__componentOriginal17e56bc23bb0192e474b351c4358d446 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal17e56bc23bb0192e474b351c4358d446 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.index','data' => ['sticky' => true,'xBind:dataMobileSidebarOpen' => 'mobileSidebarOpen || null','xOn:click' => 'if (window.innerWidth < 1024 && $event.target.closest(\'a\')) mobileSidebarOpen = false','class' => 'mobile-sidebar-panel h-screen z-50 border-e border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex flex-col']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['sticky' => true,'x-bind:data-mobile-sidebar-open' => 'mobileSidebarOpen || null','x-on:click' => 'if (window.innerWidth < 1024 && $event.target.closest(\'a\')) mobileSidebarOpen = false','class' => 'mobile-sidebar-panel h-screen z-50 border-e border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex flex-col']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

       <?php if (isset($component)) { $__componentOriginal837232b594bf97def5cd04bcaa1b6bb0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal837232b594bf97def5cd04bcaa1b6bb0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.header','data' => ['class' => 'flex items-center justify-between gap-3 px-6 py-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex items-center justify-between gap-3 px-6 py-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <a href="<?php echo e(route('dashboard')); ?>" wire:navigate.hover class="flex items-center gap-3 group no-underline">

        
        <div class="shrink-0 size-9 rounded-xl overflow-hidden bg-emerald-600 flex items-center justify-center text-white shadow-lg">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentWs && $currentWs->logo_path): ?>
                <img src="<?php echo e(asset($currentWs->logo_path)); ?>?t=<?php echo e(time()); ?>" class="size-full object-cover">
            <?php else: ?>
                <span class="text-lg font-black italic">
                    <?php echo e(substr($currentWs->name ?? 'F', 0, 1)); ?>

                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="flex flex-col justify-center min-w-0">
            <span class="text-[13px] font-black uppercase tracking-tighter text-zinc-800 dark:text-white leading-[1.1] whitespace-normal break-words">
                <?php echo e($currentWs->name ?? 'Finance Pro'); ?>

            </span>

            <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-[0.2em] mt-1">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdminMode): ?>
                    Administração
                <?php elseif($currentWs && $currentWs->type === 'business'): ?>
                    Negócio
                <?php else: ?>
                    Gestão Pessoal
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
        </div>
    </a>

    <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['class' => 'lg:hidden','variant' => 'subtle','square' => true,'icon' => 'x-mark','xOn:click' => 'mobileSidebarOpen = false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:hidden','variant' => 'subtle','square' => true,'icon' => 'x-mark','x-on:click' => 'mobileSidebarOpen = false']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal837232b594bf97def5cd04bcaa1b6bb0)): ?>
<?php $attributes = $__attributesOriginal837232b594bf97def5cd04bcaa1b6bb0; ?>
<?php unset($__attributesOriginal837232b594bf97def5cd04bcaa1b6bb0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal837232b594bf97def5cd04bcaa1b6bb0)): ?>
<?php $component = $__componentOriginal837232b594bf97def5cd04bcaa1b6bb0; ?>
<?php unset($__componentOriginal837232b594bf97def5cd04bcaa1b6bb0); ?>
<?php endif; ?>
<form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
</form>
















<?php if (isset($component)) { $__componentOriginal061367e9976089f15083f05bd78a70e4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal061367e9976089f15083f05bd78a70e4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.nav','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isViewingOthers): ?>
        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'arrow-left-circle','href' => route('workspace.switch.fast', ['id' => $myPersonalWsId]),'class' => 'text-amber-600 dark:text-amber-500 font-black animate-in slide-in-from-left-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'arrow-left-circle','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workspace.switch.fast', ['id' => $myPersonalWsId])),'class' => 'text-amber-600 dark:text-amber-500 font-black animate-in slide-in-from-left-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Sair da Gestão Externa
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalc481942d30cc0ab06077963cf20a45e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc481942d30cc0ab06077963cf20a45e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::separator','data' => ['class' => 'my-4 mx-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-4 mx-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $attributes = $__attributesOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $component = $__componentOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__componentOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdminMode): ?>
        <?php $adminUser = auth()->user(); ?>
        <div class="flex flex-col h-full justify-between">

            
            <div class="space-y-1">

                
                <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Visão Geral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Visão Geral']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'home','href' => route('admin.dashboard'),'current' => request()->routeIs('admin.dashboard'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'home','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.dashboard')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.dashboard')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Painel de Controlo
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

                
                <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Dados & Analíticas','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Dados & Analíticas','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chart-bar','href' => route('admin.stats'),'current' => request()->routeIs('admin.stats'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chart-bar','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.stats')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.stats')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Estatísticas
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'bolt','href' => route('admin.productivity'),'current' => request()->routeIs('admin.productivity'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bolt','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.productivity')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.productivity')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Produtividade
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'trophy','href' => route('admin.gamification'),'current' => request()->routeIs('admin.gamification'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'trophy','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.gamification')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.gamification')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Ranking & XP
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adminUser->isAdmin() || $adminUser->isAnalyst()): ?>
                    <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Monitorização','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Monitorização','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'cpu-chip','href' => route('admin.ai'),'current' => request()->routeIs('admin.ai'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'cpu-chip','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.ai')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.ai')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Monitor de IA
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adminUser->isAdmin() || $adminUser->isModerator()): ?>
                    <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Utilizadores & Suporte','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Utilizadores & Suporte','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'users','href' => route('admin.users'),'current' => request()->routeIs('admin.users'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'users','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.users')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.users')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Gestão de Utilizadores
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chat-bubble-left-right','href' => route('admin.support'),'current' => request()->routeIs('admin.support'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chat-bubble-left-right','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.support')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.support')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Tickets de Suporte
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'megaphone','href' => route('admin.communication'),'current' => request()->routeIs('admin.communication'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'megaphone','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.communication')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.communication')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Avisos Globais
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adminUser->isAdmin()): ?>
                    <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Sistema','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Sistema','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'shopping-bag','href' => route('admin.store'),'current' => request()->routeIs('admin.store'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'shopping-bag','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.store')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.store')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Loja
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'credit-card','href' => route('admin.billing'),'current' => request()->routeIs('admin.billing'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'credit-card','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.billing')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.billing')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Faturação & Pagamentos
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'shield-check','href' => route('admin.logs'),'current' => request()->routeIs('admin.logs'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'shield-check','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.logs')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.logs')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Segurança & Logs
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'cog-6-tooth','href' => route('admin.settings'),'current' => request()->routeIs('admin.settings'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'cog-6-tooth','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.settings')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.settings')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            Configurações Globais
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>

            
            <div class="mt-auto pb-4">
                <?php if (isset($component)) { $__componentOriginalc481942d30cc0ab06077963cf20a45e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc481942d30cc0ab06077963cf20a45e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::separator','data' => ['class' => 'my-4 mx-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-4 mx-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $attributes = $__attributesOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $component = $__componentOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__componentOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'arrow-right-start-on-rectangle','href' => '#','class' => 'text-red-600 hover:text-red-700 font-bold uppercase text-[10px] tracking-[0.2em]','onclick' => 'event.preventDefault(); document.getElementById(\'logout-form\').submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'arrow-right-start-on-rectangle','href' => '#','class' => 'text-red-600 hover:text-red-700 font-bold uppercase text-[10px] tracking-[0.2em]','onclick' => 'event.preventDefault(); document.getElementById(\'logout-form\').submit();']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    Terminar Sessão
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            </div>

        </div> 








<?php elseif($isBusinessMode): ?>
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('viewing_as_collaborator_id')): ?>
        
        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'arrow-uturn-left','wire:click' => 'stopViewingAsCollaborator','class' => 'text-red-500 font-black animate-pulse cursor-pointer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'arrow-uturn-left','wire:click' => 'stopViewingAsCollaborator','class' => 'text-red-500 font-black animate-pulse cursor-pointer']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Sair da Vista Colab
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
    <?php else: ?>
        
        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'arrow-left-circle','href' => route('hub.business.exit'),'class' => 'text-zinc-500 font-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'arrow-left-circle','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.exit')),'class' => 'text-zinc-500 font-bold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Sair do Modo Empresa
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


    <?php if (isset($component)) { $__componentOriginalc481942d30cc0ab06077963cf20a45e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc481942d30cc0ab06077963cf20a45e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::separator','data' => ['class' => 'my-4 mx-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-4 mx-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $attributes = $__attributesOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $component = $__componentOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__componentOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>





    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isManager): ?>

        
        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chart-pie','href' => route('hub.business.dashboard'),'current' => request()->routeIs('hub.business.dashboard'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chart-pie','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.dashboard')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.business.dashboard')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Dashboard Business
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Gestão de Operações','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Gestão de Operações','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'building-office-2','href' => route('company-expenses'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'building-office-2','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('company-expenses')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Custos Operacionais <?php echo $badge($counts['company_expenses']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'document-text','href' => route('hub.business.invoices'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'document-text','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.invoices')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Faturação & Vendas <?php echo $badge($counts['invoices']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'newspaper','href' => route('hub.business.proposals'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'newspaper','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.proposals')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Propostas <?php echo $badge($counts['proposals']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'user-group','href' => route('hub.business.clients'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'user-group','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.clients')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Clientes (CRM) <?php echo $badge($counts['clients']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'briefcase','href' => route('hub.business.projects'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'briefcase','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.projects')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Projetos <?php echo $badge($counts['projects']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'presentation-chart-line','href' => route('hub.business.pnl'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'presentation-chart-line','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.pnl')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Resultados (P&L)
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chart-bar','href' => route('hub.business.cashflow'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chart-bar','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.cashflow')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Fluxo de Caixa
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'presentation-chart-line','href' => route('hub.business.costs'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'presentation-chart-line','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.costs')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    Análise de Custos
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'clipboard-document-check','href' => route('hub.business.expense-approvals'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'clipboard-document-check','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.expense-approvals')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Aprovações
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'document-text','href' => route('hub.business.at-invoices'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'document-text','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.at-invoices')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                e-Fatura AT
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'archive-box','href' => route('hub.business.inventory'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'archive-box','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.inventory')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Stock / Inventário <?php echo $badge($counts['inventory']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'building-library','href' => route('hub.business.accounts'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'building-library','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.accounts')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Contas Empresa <?php echo $badge($counts['accounts'] ?? 0); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'truck','href' => route('hub.business.suppliers'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'truck','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.suppliers')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Fornecedores <?php echo $badge($counts['suppliers']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'check-badge','href' => route('hub.business.tasks'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'check-badge','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.tasks')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Tarefas & Equipa <?php echo $badge($counts['tasks']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'command-line','href' => route('hub.business.timeline'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'command-line','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.timeline')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Timeline Operações
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chat-bubble-left-right','href' => route('hub.business.messenger'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chat-bubble-left-right','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.messenger')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Messenger Equipa
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'folder-open','href' => route('hub.business.vault'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'folder-open','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.vault')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Arquivo Digital
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'sparkles','href' => route('hub.business.ai'),'wire:navigate.hover' => true,'class' => 'text-brand-600 dark:text-brand-400 font-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'sparkles','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.ai')),'wire:navigate.hover' => true,'class' => 'text-brand-600 dark:text-brand-400 font-bold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                IA Estrategista
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'calendar-days','href' => route('hub.business.calendar'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'calendar-days','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.calendar')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Calendário Global
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Administrativo','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Administrativo','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'users','href' => route('hub.business.team'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'users','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.team')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Equipa <?php echo $badge($counts['employees']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'user-plus','href' => route('hub.business.recruitment'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'user-plus','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.recruitment')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    Recrutamento
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'calendar-days','href' => route('hub.business.absences'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'calendar-days','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.absences')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Férias & Ausências <?php echo $badge($counts['absences']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'receipt-percent','href' => route('hub.business.taxes'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'receipt-percent','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.taxes')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Impostos & IVA
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chat-bubble-left-right','href' => route('hub.business.support'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chat-bubble-left-right','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.support')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Suporte Técnico <?php echo $badge(\App\Models\SupportTicket::where('status', 'open')->where('workspace_id', $currentWs?->id)->count()); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'adjustments-horizontal','href' => route('hub.business.settings'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'adjustments-horizontal','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.settings')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Perfil do Negócio
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

    <?php else: ?>
        
        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'home','href' => route('hub.business.dashboard'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'home','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.dashboard')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Meu Terminal
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'O Meu Trabalho','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'O Meu Trabalho','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'check-badge','href' => route('hub.business.tasks'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'check-badge','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.tasks')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                As Minhas Tarefas <?php echo $badge($counts['tasks']); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'briefcase','href' => route('hub.business.projects'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'briefcase','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.projects')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Os Meus Projetos
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
              
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'banknotes','href' => route('hub.business.my-expenses'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'banknotes','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.my-expenses')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Notas de Gastos
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'calendar-days','href' => route('hub.business.calendar'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'calendar-days','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.calendar')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Calendário
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Comunicação','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Comunicação','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chat-bubble-left-right','href' => route('hub.business.messenger'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chat-bubble-left-right','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.messenger')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Messenger Equipa
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Recursos','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Recursos','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'identification','href' => route('hub.business.my-profile'),'current' => request()->routeIs('hub.business.my-profile'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'identification','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.my-profile')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.business.my-profile')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                A Minha Ficha
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'clock','href' => route('hub.business.absences'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'clock','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.absences')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                As minhas férias / Faltas
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chat-bubble-left-right','href' => route('hub.business.support'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chat-bubble-left-right','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.support')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Suporte Interno
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>










            <?php else: ?>





<?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'squares-2x2','href' => route('dashboard'),'current' => request()->routeIs('dashboard'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'squares-2x2','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('dashboard')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('dashboard')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    Dashboard
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isBusinessMode): ?>
    <div class="px-4 mt-6 mb-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isDiamond()): ?>
            
            <div x-data="{ openBusiness: false }" class="w-full text-center">
                
                <button
                    @click="openBusiness = !openBusiness"
                    class="w-full flex items-center justify-center gap-2.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-brand-500/40 transition-all group relative overflow-hidden"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-office','class' => 'size-4 text-brand-600 group-hover:scale-110 transition-transform duration-300']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-office','class' => 'size-4 text-brand-600 group-hover:scale-110 transition-transform duration-300']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    <span class="text-[10px] font-black uppercase tracking-widest text-brand-600 dark:text-brand-400 italic leading-none">
                        Área Empresarial
                    </span>
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chevron-down','variant' => 'micro','class' => 'size-3 text-zinc-400 transition-transform duration-300','xBind:class' => 'openBusiness ? \'rotate-180\' : \'\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','variant' => 'micro','class' => 'size-3 text-zinc-400 transition-transform duration-300','x-bind:class' => 'openBusiness ? \'rotate-180\' : \'\'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                </button>

                
                <div x-show="openBusiness" x-cloak x-transition class="mt-3 space-y-1 border-t border-zinc-100 dark:border-zinc-800 pt-3 text-left">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($myCompanies->count() > 0): ?>
                        <p class="text-[8px] font-black uppercase text-zinc-400 tracking-widest text-center mb-2 opacity-60">Negócios Próprios</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $myCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'building-office-2','href' => route('workspace.switch.fast', $ws->id),'class' => 'justify-center text-[11px] font-bold py-2 '.e($currentWs?->id === $ws->id ? 'text-brand-600' : '').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'building-office-2','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workspace.switch.fast', $ws->id)),'class' => 'justify-center text-[11px] font-bold py-2 '.e($currentWs?->id === $ws->id ? 'text-brand-600' : '').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php echo e($ws->name); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($collabCompanies->count() > 0): ?>
                        <p class="text-[8px] font-black uppercase text-zinc-400 tracking-widest text-center mt-4 mb-2 opacity-60">Como Colaborador</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $collabCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'users','href' => route('workspace.switch.fast', $ws->id),'class' => 'justify-center text-[11px] font-bold py-2 '.e($currentWs?->id === $ws->id ? 'text-emerald-600' : '').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'users','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('workspace.switch.fast', $ws->id)),'class' => 'justify-center text-[11px] font-bold py-2 '.e($currentWs?->id === $ws->id ? 'text-emerald-600' : '').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php echo e($ws->name); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <a href="<?php echo e(route('hub.business.gateway', ['new' => 1])); ?>"
                       wire:navigate
                       class="flex items-center justify-center gap-2 w-full py-3 mt-2 border-t border-dashed border-zinc-100 dark:border-zinc-800 text-brand-600 dark:text-brand-400 hover:opacity-70 transition-all no-underline group"
                    >
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'plus','class' => 'size-3 group-hover:scale-110 transition-transform']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'size-3 group-hover:scale-110 transition-transform']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Novo Espaço</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            
            <a href="<?php echo e(route('hub.pricing')); ?>" 
               wire:navigate
               class="w-full flex items-center justify-center gap-2.5 py-2.5 rounded-2xl bg-zinc-100/50 dark:bg-zinc-900/50 border border-zinc-200/60 dark:border-zinc-800/60 transition-all group opacity-70 hover:opacity-100 hover:border-amber-500/50"
               title="Disponível no Plano Business"
            >
                <div class="relative">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-office','class' => 'size-4 text-zinc-400 group-hover:text-amber-500 transition-colors']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-office','class' => 'size-4 text-zinc-400 group-hover:text-amber-500 transition-colors']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    <div class="absolute -top-1.5 -right-1.5 bg-white dark:bg-zinc-950 rounded-full p-0.5">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'lock-closed','variant' => 'micro','class' => 'size-2.5 text-amber-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock-closed','variant' => 'micro','class' => 'size-2.5 text-amber-600']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    </div>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500 group-hover:text-zinc-700 dark:group-hover:text-zinc-300 italic leading-none">
                    Área Empresarial
                </span>
                <span class="text-[8px] bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-500 px-1.5 py-0.5 rounded-md font-bold ml-1 uppercase">Pro</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>




<?php
    // Definição segura das variáveis de plano para evitar erros de variável indefinida
    $isProUser = ($user->plan ?? '') === 'pro' || (method_exists($user, 'isDiamond') && $user->isDiamond());
    $isPlusUser = ($user->plan ?? '') === 'plus' || (method_exists($user, 'isPlus') && $user->isPlus());
    $hasLockInAccess = $isProUser || $isPlusUser;

$hasInventoryAccess = $isProUser || $isPlusUser;
$hasStoreAccess = $isProUser || $isPlusUser;
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasLockInAccess): ?>
    
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'lock-closed','href' => route('hub.lockin'),'current' => request()->routeIs('hub.lockin'),'wire:navigate.hover' => true,'class' => 'font-black group','style' => 'color: #10b981;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'lock-closed','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.lockin')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.lockin')),'wire:navigate.hover' => true,'class' => 'font-black group','style' => 'color: #10b981;']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="flex items-center gap-2 flex-1">
            <span style="background: linear-gradient(135deg, #10b981, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Lock In
            </span>
            
            <span class="text-[6px] px-1 py-0.5 rounded font-black uppercase leading-none <?php echo e($isProUser ? 'bg-violet-500 text-white' : 'bg-emerald-500 text-white'); ?>">
                <?php echo e($isProUser ? 'Business' : 'Premium'); ?>

            </span>
        </div>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php else: ?>
    
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'lock-closed','href' => route('hub.pricing'),'wire:navigate' => true,'class' => 'font-black opacity-60 grayscale group']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'lock-closed','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.pricing')),'wire:navigate' => true,'class' => 'font-black opacity-60 grayscale group']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="flex items-center justify-between w-full gap-2">
            <div class="flex items-center gap-1.5">
                <span class="text-zinc-500">Lock In</span>
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-amber-600/80']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-amber-600/80']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
            </div>

            <span class="text-[7px] font-black px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-900/20 text-amber-600 border border-amber-200/50 uppercase tracking-tighter">
                Upgrade
            </span>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



<?php
    // Verifica acesso ao Inventário (Disponível em Plus e Business/Diamond)
    $hasInventoryAccess = ($user->plan ?? '') === 'plus' ||
                         ($user->plan ?? '') === 'pro' ||
                         (method_exists($user, 'isPlus') && $user->isPlus()) ||
                         (method_exists($user, 'isDiamond') && $user->isDiamond());
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasInventoryAccess): ?>
    
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'archive-box','href' => route('hub.inventory'),'current' => request()->routeIs('hub.inventory'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'archive-box','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.inventory')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.inventory')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        O Meu Inventário
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php else: ?>
    
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'archive-box','href' => route('hub.pricing'),'wire:navigate' => true,'class' => 'opacity-60 grayscale group']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'archive-box','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.pricing')),'wire:navigate' => true,'class' => 'opacity-60 grayscale group']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="flex items-center justify-between w-full gap-2">
            <div class="flex items-center gap-1.5">
                <span class="text-zinc-500">O Meu Inventário</span>
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-amber-600/80']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-amber-600/80']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
            </div>

            <span class="text-[7px] font-black px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-900/20 text-amber-600 border border-amber-200/50 uppercase tracking-tighter">
                Plus
            </span>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>








                
                <?php
                    $unreadSocial = \App\Models\SocialNotification::where('user_id', $user->id)->whereNull('read_at')->count();
                ?>
                <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'globe-alt','href' => '/social','wire:navigate' => true,'class' => 'text-indigo-600 dark:text-indigo-400 font-black italic mt-1 group']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'globe-alt','href' => '/social','wire:navigate' => true,'class' => 'text-indigo-600 dark:text-indigo-400 font-black italic mt-1 group']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    Finance Connect
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadSocial > 0): ?>
                        <span class="ml-auto text-[9px] font-black bg-indigo-600 text-white px-2 py-0.5 rounded-full"><?php echo e($unreadSocial > 9 ? '9+' : $unreadSocial); ?></span>
                    <?php else: ?>
                        <span class="ml-auto flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse opacity-0 group-hover:opacity-100 transition-opacity"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalc481942d30cc0ab06077963cf20a45e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc481942d30cc0ab06077963cf20a45e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::separator','data' => ['class' => 'my-4 mx-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-4 mx-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $attributes = $__attributesOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $component = $__componentOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__componentOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Finanças']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Finanças']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($myRestrictions?->restrict_budget): ?>
    
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'lock-closed','class' => 'opacity-50 grayscale cursor-not-allowed','title' => 'Área Bloqueada pelo Administrador']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'lock-closed','class' => 'opacity-50 grayscale cursor-not-allowed','title' => 'Área Bloqueada pelo Administrador']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <span class="flex items-center justify-between w-full">
            Orçamento
            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-amber-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-amber-600']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
        </span>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php else: ?>
    
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'calculator','href' => route('hub.budget'),'current' => request()->routeIs('hub.budget'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'calculator','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.budget')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.budget')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Orçamento
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'arrow-up-tray','href' => route('hub.import'),'current' => request()->routeIs('hub.import'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'arrow-up-tray','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.import')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.import')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Importar Extrato
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'arrow-trending-up','href' => route('hub.incomes'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'arrow-trending-up','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.incomes')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Receitas <?php echo $badge($counts['incomes']); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'hand-raised','href' => route('hub.debts'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'hand-raised','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.debts')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Dívidas <?php echo $badge($counts['debts']); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chart-bar-square','href' => route('hub.investments'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chart-bar-square','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.investments')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Investimentos <?php echo $badge($counts['investments']); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'credit-card','href' => route('hub.subscriptions'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'credit-card','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.subscriptions')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Assinaturas <?php echo $badge($counts['subscriptions']); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'briefcase','href' => route('hub.networth'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'briefcase','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.networth')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Património Real
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'building-library','href' => route('hub.banco'),'current' => request()->routeIs('hub.banco'),'wire:navigate.hover' => true,'class' => 'font-black text-emerald-700 dark:text-emerald-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'building-library','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.banco')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.banco')),'wire:navigate.hover' => true,'class' => 'font-black text-emerald-700 dark:text-emerald-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Banco
                        <span class="ml-auto text-[8px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-1.5 py-0.5 rounded uppercase">Novo</span>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isStar() || $user->isDiamond()): ?>
    
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'sparkles','href' => route('ai'),'current' => request()->routeIs('ai'),'wire:navigate.hover' => true,'class' => 'text-brand-600 dark:text-brand-400 font-black']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'sparkles','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('ai')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('ai')),'wire:navigate.hover' => true,'class' => 'text-brand-600 dark:text-brand-400 font-black']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        CFO Inteligente
        <span class="ml-auto text-[8px] font-black bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded uppercase">IA</span>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php else: ?>
    
    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'sparkles','href' => route('hub.pricing'),'class' => 'text-zinc-400 opacity-60 group','wire:navigate.hover' => true,'title' => 'Requer Plano Premium']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'sparkles','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.pricing')),'class' => 'text-zinc-400 opacity-60 group','wire:navigate.hover' => true,'title' => 'Requer Plano Premium']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <span class="flex items-center justify-between w-full">
            CFO Inteligente
            <span class="flex items-center gap-1 ml-auto">
                <span class="text-[10px]">⭐</span>
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
            </span>
        </span>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Ferramentas','class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Ferramentas','class' => 'mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'calendar-days','href' => route('hub.calendar'),'current' => request()->routeIs('hub.calendar'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'calendar-days','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.calendar')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.calendar')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Calendário
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'clock','href' => route('hub.reminders'),'current' => request()->routeIs('hub.reminders'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'clock','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.reminders')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.reminders')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    Lembretes <?php echo $badge($counts['reminders']); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'user-group','href' => route('hub.family.manage'),'current' => request()->routeIs('hub.family.manage'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'user-group','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.family.manage')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.family.manage')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Família <?php echo $badge($counts['ranking']); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>


                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'trophy','href' => route('hub.goals'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'trophy','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.goals')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Metas <?php echo $badge($counts['goals']); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'sparkles','href' => route('hub.wrapped'),'current' => request()->routeIs('hub.wrapped'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'sparkles','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.wrapped')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.wrapped')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Wrapped <?php echo e(now()->year); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'bolt','href' => route('hub.fitness'),'current' => request()->routeIs('hub.fitness'),'wire:navigate.hover' => true,'class' => 'text-orange-500 dark:text-orange-400 font-black']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bolt','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.fitness')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.fitness')),'wire:navigate.hover' => true,'class' => 'text-orange-500 dark:text-orange-400 font-black']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    Zona de Treino
                    <span class="ml-auto text-[8px] font-black bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400 px-1.5 py-0.5 rounded uppercase">Novo</span>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

               <?php
    // Detecção simplificada e segura dos planos
    $isProUser = ($user->plan ?? '') === 'pro' || (method_exists($user, 'isDiamond') && $user->isDiamond());
    $isPlusUser = ($user->plan ?? '') === 'plus' || (method_exists($user, 'isPlus') && $user->isPlus());
    $hasLockInAccess = $isProUser || $isPlusUser;
?>



                <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chat-bubble-left-right','href' => route('support.hub'),'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chat-bubble-left-right','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('support.hub')),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    Suporte Técnico <?php echo $badge($counts['support']); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

                
                <?php if (isset($component)) { $__componentOriginal31257750338e37e989bcfa8eb3c88bb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.group','data' => ['heading' => 'Categorias','class' => 'grid mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => 'Categorias','class' => 'grid mt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAnyPremium): ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['href' => route('categories'),'wire:navigate.hover' => true,'class' => 'group mt-1 rounded-xl border border-emerald-500/20 bg-gradient-to-r from-emerald-500/10 to-green-500/10 hover:from-emerald-500/20 hover:to-green-500/20 transition-all duration-300 shadow-sm hover:shadow-emerald-500/20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('categories')),'wire:navigate.hover' => true,'class' => 'group mt-1 rounded-xl border border-emerald-500/20 bg-gradient-to-r from-emerald-500/10 to-green-500/10 hover:from-emerald-500/20 hover:to-green-500/20 transition-all duration-300 shadow-sm hover:shadow-emerald-500/20']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <div class="flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'adjustments-horizontal','class' => 'size-4 text-emerald-500 transition-transform duration-300 group-hover:rotate-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'adjustments-horizontal','class' => 'size-4 text-emerald-500 transition-transform duration-300 group-hover:rotate-12']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                            <span class="text-[10px] font-black uppercase tracking-[0.25em] text-emerald-600 dark:text-emerald-400">
                                Gerir Categorias
                            </span>
                        </div>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['href' => route('hub.pricing'),'wire:navigate.hover' => true,'class' => 'group mt-1 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 opacity-60']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.pricing')),'wire:navigate.hover' => true,'class' => 'group mt-1 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50 opacity-60']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <div class="flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'adjustments-horizontal','class' => 'size-4 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'adjustments-horizontal','class' => 'size-4 text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                            <span class="text-[10px] font-black uppercase tracking-[0.25em] text-zinc-400 flex items-center gap-1">
                                Gerir Categorias ⭐
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock-closed','variant' => 'micro','class' => 'size-3 text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                            </span>
                        </div>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                   <?php
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
?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sidebarCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sidebarCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $catSlug  = $sidebarCat->slug;
                            $catIcon  = $sidebarCat->icon ?? 'tag';
                            $lowerName = mb_strtolower($sidebarCat->name, 'UTF-8');
                            $catCount = $catCounts[$lowerName] ?? ($catCounts[$catSlug] ?? 0);
                            $catHref  = route('hub.category', $catSlug);
                        ?>

                        <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => $catIcon,'href' => $catHref,'wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($catIcon),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($catHref),'wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e($sidebarCat->name); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($catCount > 0): ?>
                                <span class="ml-auto text-[10px] font-black text-zinc-400 italic">#<?php echo e($catCount); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $attributes = $__attributesOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__attributesOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1)): ?>
<?php $component = $__componentOriginal31257750338e37e989bcfa8eb3c88bb1; ?>
<?php unset($__componentOriginal31257750338e37e989bcfa8eb3c88bb1); ?>
<?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->is_admin): ?>
                    <?php if (isset($component)) { $__componentOriginalc481942d30cc0ab06077963cf20a45e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc481942d30cc0ab06077963cf20a45e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::separator','data' => ['class' => 'my-4 mx-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-4 mx-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $attributes = $__attributesOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $component = $__componentOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__componentOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'shield-check','href' => route('admin.dashboard'),'class' => 'text-purple-600 font-bold','wire:navigate.hover' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'shield-check','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.dashboard')),'class' => 'text-purple-600 font-bold','wire:navigate.hover' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Painel Administrador
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal061367e9976089f15083f05bd78a70e4)): ?>
<?php $attributes = $__attributesOriginal061367e9976089f15083f05bd78a70e4; ?>
<?php unset($__attributesOriginal061367e9976089f15083f05bd78a70e4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal061367e9976089f15083f05bd78a70e4)): ?>
<?php $component = $__componentOriginal061367e9976089f15083f05bd78a70e4; ?>
<?php unset($__componentOriginal061367e9976089f15083f05bd78a70e4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal4a4f7aa062a095c651c2f80bb685a42a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4a4f7aa062a095c651c2f80bb685a42a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::spacer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::spacer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4a4f7aa062a095c651c2f80bb685a42a)): ?>
<?php $attributes = $__attributesOriginal4a4f7aa062a095c651c2f80bb685a42a; ?>
<?php unset($__attributesOriginal4a4f7aa062a095c651c2f80bb685a42a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4a4f7aa062a095c651c2f80bb685a42a)): ?>
<?php $component = $__componentOriginal4a4f7aa062a095c651c2f80bb685a42a; ?>
<?php unset($__componentOriginal4a4f7aa062a095c651c2f80bb685a42a); ?>
<?php endif; ?>

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
                <?php if (isset($component)) { $__componentOriginald5f987720f2b51852a6659d9a2c7a66b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5f987720f2b51852a6659d9a2c7a66b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.sun','data' => ['xShow' => 'darkMode','variant' => 'outline','class' => 'w-5 h-5 text-brand-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon.sun'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'darkMode','variant' => 'outline','class' => 'w-5 h-5 text-brand-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5f987720f2b51852a6659d9a2c7a66b)): ?>
<?php $attributes = $__attributesOriginald5f987720f2b51852a6659d9a2c7a66b; ?>
<?php unset($__attributesOriginald5f987720f2b51852a6659d9a2c7a66b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5f987720f2b51852a6659d9a2c7a66b)): ?>
<?php $component = $__componentOriginald5f987720f2b51852a6659d9a2c7a66b; ?>
<?php unset($__componentOriginald5f987720f2b51852a6659d9a2c7a66b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal8aa8d5bc914d8b570a9db7c847a44cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aa8d5bc914d8b570a9db7c847a44cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.moon','data' => ['xShow' => '!darkMode','variant' => 'outline','class' => 'w-5 h-5 text-zinc-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon.moon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => '!darkMode','variant' => 'outline','class' => 'w-5 h-5 text-zinc-500']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8aa8d5bc914d8b570a9db7c847a44cc2)): ?>
<?php $attributes = $__attributesOriginal8aa8d5bc914d8b570a9db7c847a44cc2; ?>
<?php unset($__attributesOriginal8aa8d5bc914d8b570a9db7c847a44cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8aa8d5bc914d8b570a9db7c847a44cc2)): ?>
<?php $component = $__componentOriginal8aa8d5bc914d8b570a9db7c847a44cc2; ?>
<?php unset($__componentOriginal8aa8d5bc914d8b570a9db7c847a44cc2); ?>
<?php endif; ?>
                <span x-text="darkMode ? 'Modo Claro' : 'Modo Escuro'"></span>
            </button>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal17e56bc23bb0192e474b351c4358d446)): ?>
<?php $attributes = $__attributesOriginal17e56bc23bb0192e474b351c4358d446; ?>
<?php unset($__attributesOriginal17e56bc23bb0192e474b351c4358d446); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal17e56bc23bb0192e474b351c4358d446)): ?>
<?php $component = $__componentOriginal17e56bc23bb0192e474b351c4358d446; ?>
<?php unset($__componentOriginal17e56bc23bb0192e474b351c4358d446); ?>
<?php endif; ?>





    
    
    <?php if (isset($component)) { $__componentOriginale96c14d638c792103c11b984a4ed1896 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale96c14d638c792103c11b984a4ed1896 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::header','data' => ['class' => 'z-40 border-b border-zinc-200/50 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-950/50 backdrop-blur-md flex justify-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'z-40 border-b border-zinc-200/50 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-950/50 backdrop-blur-md flex justify-center']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


        
        <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['class' => 'lg:hidden ms-2 absolute left-2','variant' => 'subtle','square' => true,'icon' => 'bars-2','xOn:click' => 'mobileSidebarOpen = true','ariaLabel' => 'Abrir menu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:hidden ms-2 absolute left-2','variant' => 'subtle','square' => true,'icon' => 'bars-2','x-on:click' => 'mobileSidebarOpen = true','aria-label' => 'Abrir menu']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>

        
        <div class="grid grid-cols-[auto_1fr_auto] items-center w-full max-w-6xl px-4 sm:px-6 lg:px-8 gap-3 mx-auto">

            
            <div class="flex items-center">
                <a href="<?php echo e(route('hub.fitness')); ?>"
                    wire:navigate
                    class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-orange-500 hover:border-orange-500/30 transition-all shadow-sm group"
                    title="Zona de Treino">
                    <svg class="size-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </a>
            </div>






 
  
 <div class="flex items-center justify-center gap-4 w-full"> 

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isViewingOthers): ?>
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-amber-500/10 border border-amber-500/20 rounded-2xl animate-in slide-in-from-top-2">
            <div class="relative flex items-center justify-center">
                <div class="size-1.5 rounded-full bg-amber-500 animate-ping absolute"></div>
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'eye','variant' => 'micro','class' => 'size-3.5 text-amber-600 relative']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'eye','variant' => 'micro','class' => 'size-3.5 text-amber-600 relative']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
            </div>
            <span class="text-[9px] font-black uppercase text-amber-600 tracking-widest whitespace-nowrap">
                A visualizar: <span class="text-amber-800 dark:text-amber-400 italic"><?php echo e($currentWs->name); ?></span>
            </span>
            <a href="<?php echo e(route('workspace.switch.fast', $myPersonalWsId)); ?>" class="ml-1 p-1 bg-amber-600 hover:bg-amber-700 text-white rounded-md transition-colors" title="Voltar à minha conta">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-uturn-left','variant' => 'micro','class' => 'size-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-uturn-left','variant' => 'micro','class' => 'size-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
            </a>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button x-on:click="$dispatch('open-global-search')" class="flex items-center gap-3 px-4 py-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-all group w-full shadow-sm">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'magnifying-glass','class' => 'size-4 group-hover:text-brand-500 transition-colors']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'size-4 group-hover:text-brand-500 transition-colors']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    <span class="text-sm font-medium text-left flex-1">Procurar tudo... (Ctrl K)</span>
                </button>
            </div>




<div class="flex items-center gap-2 sm:gap-3 justify-end">

    
    <?php
    // Verifica se o utilizador tem acesso (Plus ou Pro)
    $hasStoreAccess = ($user->plan ?? '') === 'plus' ||
                      ($user->plan ?? '') === 'pro' ||
                      (method_exists($user, 'isPlus') && $user->isPlus()) ||
                      (method_exists($user, 'isDiamond') && $user->isDiamond());
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasStoreAccess): ?>
    
    <a href="<?php echo e(route('hub.store')); ?>"
       wire:navigate
       class="relative p-2.5 rounded-xl bg-white dark:bg-zinc-900 shadow-sm border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-brand-600 hover:border-brand-500/30 transition-all group"
       title="Loja Digital">
        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shopping-bag','class' => 'size-5 group-hover:scale-110 transition-transform duration-300']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shopping-bag','class' => 'size-5 group-hover:scale-110 transition-transform duration-300']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
    </a>
<?php else: ?>
    
    <a href="<?php echo e(route('hub.pricing')); ?>"
       wire:navigate
       class="relative p-2.5 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/50 shadow-sm border border-zinc-200/60 dark:border-zinc-800/60 text-zinc-400 opacity-80 hover:opacity-100 hover:border-amber-500/50 transition-all group"
       title="Loja Digital (Disponível em Planos Premium)">

        
        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shopping-bag','class' => 'size-5 grayscale']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shopping-bag','class' => 'size-5 grayscale']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>

        
        <div class="absolute -top-1.5 -right-1.5 bg-white dark:bg-zinc-950 rounded-full p-0.5 border border-zinc-100 dark:border-zinc-800 shadow-sm">
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-full p-0.5">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'lock-closed','variant' => 'micro','class' => 'size-2.5 text-amber-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock-closed','variant' => 'micro','class' => 'size-2.5 text-amber-600']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
            </div>
        </div>
    </a>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('dashboard') || request()->routeIs('social.profile') || request()->routeIs('profile.edit')): ?>
                    <button @click="privacyMode = !privacyMode"
                        class="p-2.5 rounded-xl bg-white dark:bg-zinc-900 shadow-sm border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-emerald-600 transition-all">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['xShow' => '!privacyMode','name' => 'eye','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => '!privacyMode','name' => 'eye','class' => 'size-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['xShow' => 'privacyMode','name' => 'eye-slash','class' => 'size-5 text-emerald-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'privacyMode','name' => 'eye-slash','class' => 'size-5 text-emerald-500']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('notification-center', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-5', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

                
                <div x-data="{ open: false }" class="relative flex-shrink-0">
                    <button @click="open = !open" type="button" class="flex items-center gap-2 sm:gap-3 px-1 sm:px-2 py-1.5 rounded-2xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">
                        <?php if (isset($component)) { $__componentOriginal4dcb6e757bd07b9aa3bf7ee84cfc8690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4dcb6e757bd07b9aa3bf7ee84cfc8690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::avatar.index','data' => ['initials' => auth()->user()->initials(),'class' => 'size-9 sm:size-10 shadow-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['initials' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()->initials()),'class' => 'size-9 sm:size-10 shadow-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4dcb6e757bd07b9aa3bf7ee84cfc8690)): ?>
<?php $attributes = $__attributesOriginal4dcb6e757bd07b9aa3bf7ee84cfc8690; ?>
<?php unset($__attributesOriginal4dcb6e757bd07b9aa3bf7ee84cfc8690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4dcb6e757bd07b9aa3bf7ee84cfc8690)): ?>
<?php $component = $__componentOriginal4dcb6e757bd07b9aa3bf7ee84cfc8690; ?>
<?php unset($__componentOriginal4dcb6e757bd07b9aa3bf7ee84cfc8690); ?>
<?php endif; ?>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-bold text-zinc-800 dark:text-white leading-none">
                                <?php echo e(auth()->user()->name); ?> <span class="ml-1"><?php echo e($planEmoji); ?></span>
                            </p>
                            <p class="text-[10px] font-black uppercase <?php echo e($planColor); ?> mt-1">
                                <?php echo e($planText); ?>

                            </p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chevron-down','class' => 'size-4 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','class' => 'size-4 text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-60 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl z-50 overflow-hidden">
                        <div class="py-1">
                            <a href="<?php echo e(route('profile.edit')); ?>" wire:navigate.hover class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
                                👤 O meu perfil
                            </a>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isDiamond() || $user->isStar()): ?>
                                <a href="<?php echo e(route('hub.pricing')); ?>" wire:navigate
                                   class="w-full flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 transition-colors border-b border-zinc-50 dark:border-zinc-800/50">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isDiamond()): ?>
                                        <span class="text-indigo-500">💎</span> O meu plano Diamante
                                    <?php else: ?>
                                        <span class="text-amber-500">⭐</span> O meu plano Premium
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->isAdmin()): ?>
                                <a href="<?php echo e($isBusinessMode ? route('hub.business.support') : route('support.hub')); ?>"
                                    wire:navigate
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
                                    <span>🎫</span> Suporte Técnico
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isAnyPremium && !auth()->user()->isAdmin()): ?>
                                <a href="<?php echo e(route('hub.pricing')); ?>" wire:navigate class="flex items-center gap-2 px-4 py-2 text-sm text-brand-600 font-black hover:bg-brand-50 dark:hover:bg-brand-500/10">
                                    <span class="animate-pulse">💎</span> OBTER PREMIUM
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="border-t border-zinc-200 dark:border-zinc-800 my-1"></div>

                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-500 font-bold hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
                                    🚪 Sair da Conta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale96c14d638c792103c11b984a4ed1896)): ?>
<?php $attributes = $__attributesOriginale96c14d638c792103c11b984a4ed1896; ?>
<?php unset($__attributesOriginale96c14d638c792103c11b984a4ed1896); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale96c14d638c792103c11b984a4ed1896)): ?>
<?php $component = $__componentOriginale96c14d638c792103c11b984a4ed1896; ?>
<?php unset($__componentOriginale96c14d638c792103c11b984a4ed1896); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal95c5505ccad18880318521d2bba3eac7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal95c5505ccad18880318521d2bba3eac7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::main','data' => ['class' => 'lg:ps-64 h-screen overflow-y-auto custom-scrollbar pt-16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::main'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:ps-64 h-screen overflow-y-auto custom-scrollbar pt-16']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div id="page-container" class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            <?php echo e($slot); ?>

        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal95c5505ccad18880318521d2bba3eac7)): ?>
<?php $attributes = $__attributesOriginal95c5505ccad18880318521d2bba3eac7; ?>
<?php unset($__attributesOriginal95c5505ccad18880318521d2bba3eac7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal95c5505ccad18880318521d2bba3eac7)): ?>
<?php $component = $__componentOriginal95c5505ccad18880318521d2bba3eac7; ?>
<?php unset($__componentOriginal95c5505ccad18880318521d2bba3eac7); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('hub.store', 'store.product.show', 'store.compare', 'store.wishlist')): ?>
        <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('store-cart-fab'); ?>">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('store.cart-icon', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-6', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


     <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('finance-bot'); ?>">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('finance-bot', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-7', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
    </div>

    <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('toast'); ?>">
        <?php if (isset($component)) { $__componentOriginal9e52f305f7cdd22fd6be350cf248d973 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9e52f305f7cdd22fd6be350cf248d973 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::toast.group','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::toast.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal6e0689304ed9fe6f1f826bea0820c41b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6e0689304ed9fe6f1f826bea0820c41b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::toast.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6e0689304ed9fe6f1f826bea0820c41b)): ?>
<?php $attributes = $__attributesOriginal6e0689304ed9fe6f1f826bea0820c41b; ?>
<?php unset($__attributesOriginal6e0689304ed9fe6f1f826bea0820c41b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6e0689304ed9fe6f1f826bea0820c41b)): ?>
<?php $component = $__componentOriginal6e0689304ed9fe6f1f826bea0820c41b; ?>
<?php unset($__componentOriginal6e0689304ed9fe6f1f826bea0820c41b); ?>
<?php endif; ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9e52f305f7cdd22fd6be350cf248d973)): ?>
<?php $attributes = $__attributesOriginal9e52f305f7cdd22fd6be350cf248d973; ?>
<?php unset($__attributesOriginal9e52f305f7cdd22fd6be350cf248d973); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9e52f305f7cdd22fd6be350cf248d973)): ?>
<?php $component = $__componentOriginal9e52f305f7cdd22fd6be350cf248d973; ?>
<?php unset($__componentOriginal9e52f305f7cdd22fd6be350cf248d973); ?>
<?php endif; ?>
    </div>

    
    <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('finance-bot'); ?>">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('finance-bot', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3395991273-8', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('impersonator_id') || session()->has('viewing_as_collaborator_id')): ?>
        <?php
            $exitViewUrl = session()->has('viewing_as_collaborator_id')
                ? route('hub.business.stop-viewing-collaborator')
                : route('hub.business.leave-impersonation');
            $viewLabel = session()->has('viewing_as_collaborator_id')
                ? 'Vista de Colaborador'
                : 'Modo Colaborador';
        ?>
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[999] w-auto animate-in fade-in slide-in-from-bottom-10 duration-700">
            <div class="relative group">
                <!-- Aura de brilho atrás do botão -->
                <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-amber-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>

                <a href="<?php echo e($exitViewUrl); ?>"
                   class="relative flex items-center gap-4 px-6 py-3 bg-zinc-900 dark:bg-zinc-800 border border-white/10 rounded-full shadow-2xl no-underline transition-all hover:scale-105 active:scale-95 group">

                    <div class="flex items-center gap-3">
                        <div class="size-8 rounded-full bg-red-600 flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-right-start-on-rectangle','class' => 'size-4 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right-start-on-rectangle','class' => 'size-4 text-white']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                        </div>

                        <div class="flex flex-col items-start leading-none pr-4 border-r border-white/10">
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] text-red-500">Acesso Restrito</span>
                            <span class="text-[10px] font-black uppercase tracking-tight text-white mt-0.5"><?php echo e($viewLabel); ?></span>
                        </div>

                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 group-hover:text-white transition-colors">
                            Voltar para CEO
                        </span>
                    </div>
                </a>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal8cc9d3143946b992b324617832699c5f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc9d3143946b992b324617832699c5f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::modal.index','data' => ['name' => 'announcement-detail','variant' => 'center','class' => 'md:w-[600px] !p-0 overflow-hidden border-none shadow-2xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'announcement-detail','variant' => 'center','class' => 'md:w-[600px] !p-0 overflow-hidden border-none shadow-2xl']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'megaphone','class' => 'size-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'megaphone','class' => 'size-8']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                        </div>
                        <div class="text-left">
                            <h2 class="text-3xl font-black uppercase italic tracking-tighter dark:text-white leading-none mb-2" x-text="title"></h2>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest text-white shadow-sm" :class="getColor" x-text="type"></span>
                                <span class="text-[11px] text-zinc-400 font-black uppercase tracking-widest" x-text="date"></span>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($component)) { $__componentOriginalda55eef372798476d918d03158796935 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda55eef372798476d918d03158796935 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::modal.close','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::modal.close'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['variant' => 'ghost','icon' => 'x-mark','class' => 'rounded-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','icon' => 'x-mark','class' => 'rounded-full']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda55eef372798476d918d03158796935)): ?>
<?php $attributes = $__attributesOriginalda55eef372798476d918d03158796935; ?>
<?php unset($__attributesOriginalda55eef372798476d918d03158796935); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda55eef372798476d918d03158796935)): ?>
<?php $component = $__componentOriginalda55eef372798476d918d03158796935; ?>
<?php unset($__componentOriginalda55eef372798476d918d03158796935); ?>
<?php endif; ?>
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
                            <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['class' => 'flex-1 h-14 rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-lg hover:scale-[1.02] transition-transform','xOn:click' => '$dispatch(\'close-announcement-globally\', { id: id }); $dispatch(\'modal-close\', {name: \'announcement-detail\'})']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex-1 h-14 rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-lg hover:scale-[1.02] transition-transform','x-on:click' => '$dispatch(\'close-announcement-globally\', { id: id }); $dispatch(\'modal-close\', {name: \'announcement-detail\'})']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                Entendido, remover aviso
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
                        </template>
                        <?php if (isset($component)) { $__componentOriginalda55eef372798476d918d03158796935 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda55eef372798476d918d03158796935 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::modal.close','data' => ['class' => 'flex-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::modal.close'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['variant' => 'ghost','class' => 'w-full h-14 rounded-2xl font-black uppercase text-[11px] tracking-widest border-2 border-zinc-100 dark:border-zinc-800']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','class' => 'w-full h-14 rounded-2xl font-black uppercase text-[11px] tracking-widest border-2 border-zinc-100 dark:border-zinc-800']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                Sair do Dossiê
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda55eef372798476d918d03158796935)): ?>
<?php $attributes = $__attributesOriginalda55eef372798476d918d03158796935; ?>
<?php unset($__attributesOriginalda55eef372798476d918d03158796935); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda55eef372798476d918d03158796935)): ?>
<?php $component = $__componentOriginalda55eef372798476d918d03158796935; ?>
<?php unset($__componentOriginalda55eef372798476d918d03158796935); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cc9d3143946b992b324617832699c5f)): ?>
<?php $attributes = $__attributesOriginal8cc9d3143946b992b324617832699c5f; ?>
<?php unset($__attributesOriginal8cc9d3143946b992b324617832699c5f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cc9d3143946b992b324617832699c5f)): ?>
<?php $component = $__componentOriginal8cc9d3143946b992b324617832699c5f; ?>
<?php unset($__componentOriginal8cc9d3143946b992b324617832699c5f); ?>
<?php endif; ?>

    <?php app('livewire')->forceAssetInjection(); ?>
<?php echo app('flux')->scripts(); ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

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

    
    <?php if (isset($component)) { $__componentOriginal8cc9d3143946b992b324617832699c5f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc9d3143946b992b324617832699c5f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::modal.index','data' => ['wire:model' => 'showPrivacyModal','position' => 'center','class' => 'md:w-[350px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'showPrivacyModal','position' => 'center','class' => 'md:w-[350px]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="space-y-6 text-center">
            <h2 class="text-xl font-black uppercase italic text-zinc-900">Confirmar Identidade</h2>
            <form wire:submit.prevent="unlockPrivacy" class="space-y-4">
                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'privacyPassword','type' => 'password','placeholder' => 'Tua password...','class' => 'rounded-xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'privacyPassword','type' => 'password','placeholder' => 'Tua password...','class' => 'rounded-xl']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['type' => 'submit','variant' => 'primary','class' => 'w-full bg-emerald-600 border-none font-black uppercase']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','class' => 'w-full bg-emerald-600 border-none font-black uppercase']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Desbloquear 🟢 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
            </form>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cc9d3143946b992b324617832699c5f)): ?>
<?php $attributes = $__attributesOriginal8cc9d3143946b992b324617832699c5f; ?>
<?php unset($__attributesOriginal8cc9d3143946b992b324617832699c5f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cc9d3143946b992b324617832699c5f)): ?>
<?php $component = $__componentOriginal8cc9d3143946b992b324617832699c5f; ?>
<?php unset($__componentOriginal8cc9d3143946b992b324617832699c5f); ?>
<?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reportModal ?? false): ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



















    
    
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isViewingOthers): ?>
    <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[999] w-auto animate-in fade-in slide-in-from-bottom-10 duration-700">
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-amber-600 to-orange-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>

            <div class="relative flex items-center gap-6 px-8 py-4 bg-white dark:bg-zinc-900 border border-amber-500/30 rounded-full shadow-2xl backdrop-blur-md">
                <div class="flex flex-col text-left leading-tight pr-6 border-r border-zinc-100 dark:border-zinc-800">
                    <span class="text-[8px] font-black uppercase text-amber-600 tracking-[0.2em]">Modo de Consulta Ativo</span>
                    <span class="text-sm font-black dark:text-white uppercase italic"><?php echo e($currentWs->name); ?></span>
                </div>

                
                <a href="<?php echo e(route('workspace.switch.fast', $myPersonalWsId)); ?>"
                   class="flex items-center gap-3 px-6 py-2 bg-zinc-950 dark:bg-brand-600 text-white rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-xl group/btn">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-uturn-left','variant' => 'micro','class' => 'size-4 transition-transform group-hover/btn:-translate-x-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-uturn-left','variant' => 'micro','class' => 'size-4 transition-transform group-hover/btn:-translate-x-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    <span class="text-[10px] font-black uppercase tracking-widest whitespace-nowrap">Voltar à Minha Gestão</span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<script>
    // 1. Registo do Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }

    // 2. Função de Sincronização
    async function syncOfflineData() {
        const queue = JSON.parse(localStorage.getItem('offline_vault') || '[]');

        if (queue.length > 0) {
            console.log('📦 Sincronizando dados offline...');

            try {
                const response = await fetch('/api/offline/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ expenses: queue })
                });

                if (response.ok) {
                    localStorage.removeItem('offline_vault');
                    console.log('✅ Sincronização completa!');

                    // Avisar o utilizador
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { text: 'Gastos offline sincronizados! ✅' }
                    }));
                }
            } catch (error) {
                console.error('❌ Falha ao sincronizar:', error);
            }
        }
    }

    // Tenta sincronizar mal a página carrega e deteta internet
    window.addEventListener('online', syncOfflineData);
    if (navigator.onLine) syncOfflineData();
</script>
</body>
</html>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>