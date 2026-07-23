<div class="space-y-10 pb-24">
   @php
    $user = auth()->user();
    $workspace = $user->currentWorkspace;

    // 1. Identificar o TEU cofre pessoal (Eduardo)
    $myPersonalWs = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();
    $myPersonalWsId = $myPersonalWs?->id;
    $personalMembers = $myPersonalWs ? $myPersonalWs->users : collect();

    // 2. Lógica de Estado: Estás no teu cofre ou no de outro?
    $isAtPrivate = $myPersonalWs && ($workspace->id === $myPersonalWs->id);

    // 3. IDENTIFICAR O ESPAÇO PARTILHADO (João)
    if (!$isAtPrivate && $workspace->type === 'personal') {
        $sharedWs = $workspace;
    } else {
        $sharedWs = $user->workspaces()
            ->where('owner_id', '!=', $user->id)
            ->where('type', 'personal')
            ->first();
    }

    // Definimos isAtShared para evitar erros no HTML
    $isAtShared = $sharedWs && ($workspace->id === $sharedWs->id);

    $sharedMembers = $sharedWs ? $sharedWs->users()->withPivot('role')->get() : collect();

    // 4. Contexto para o Hero Card
    $contextType = $isAtPrivate ? 'private' : 'external';
    $ownerModel = \App\Models\User::find($workspace->owner_id);
@endphp
<div class="bg-red-500 text-white p-2">
   Eu sou Admin deste espaço? {{ $iAmAdmin ? 'SIM' : 'NÃO' }}
</div>
    {{-- 1. HEADER & BREADCRUMB --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-2">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm">
                <flux:icon name="user-group" class="size-6 text-brand-600" />
            </div>
            <div>
                <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                    Comando do Grupo
                </h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-1">
                    Gestão de privilégios e auditoria do espaço <span class="text-brand-600 font-bold uppercase">{{ $workspace->name }}</span>
                </p>
            </div>
        </div>
        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-4 py-2">
            Workspace Ativo
        </flux:badge>
    </div>

    {{-- 2. HERO CARD (CONTEXTO ATUAL) --}}
    <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-10 shadow-xl">
        {{-- Glow Decorativo --}}
        <div class="absolute -right-20 -top-20 size-80 {{ $contextType === 'private' ? 'bg-emerald-500/10' : 'bg-brand-500/10' }} blur-[100px] rounded-full"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-8 text-left">
                <div class="size-24 rounded-[2.5rem] {{ $contextType === 'private' ? 'bg-emerald-600' : 'bg-brand-600' }} flex items-center justify-center text-white shadow-2xl shrink-0">
                    <flux:icon name="{{ $contextType === 'private' ? 'lock-closed' : 'users' }}" variant="solid" class="size-12" />
                </div>

                <div>
                    <div class="flex items-center gap-3 mb-3">
                        @if($contextType === 'private')
                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 rounded-full text-[10px] font-black uppercase tracking-widest">🔒 Cofre Privado</span>
                        @else
                            <span class="px-3 py-1 bg-brand-500/10 text-brand-600 border border-brand-500/20 rounded-full text-[10px] font-black uppercase tracking-widest">👥 Gestão Partilhada</span>
                        @endif
                    </div>

                    <h2 class="text-5xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                        {{ $workspace->name }}
                    </h2>

                    <p class="mt-4 text-base text-zinc-500 font-medium italic">
                        @if($contextType === 'private')
                            Este espaço é exclusivo para os teus dados. Ninguém mais tem acesso.
                        @else
                            Estás a visualizar a gestão de <span class="text-brand-600 font-black">{{ $ownerModel->name }}</span>.
                        @endif
                    </p>
                </div>
            </div>

            {{-- Só tenta mostrar o botão se NÃO estiver no pessoal E se o objeto do cofre existir --}}
@if(!$isAtPrivate && $myPersonalWs)
    <a href="{{ route('workspace.switch.fast', $myPersonalWs->id) }}"
       class="group flex items-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl transition-all active:scale-95 no-underline">
        <flux:icon name="arrow-uturn-left" variant="micro" class="size-4" />
        Voltar ao Meu Cofre
    </a>
@endif
        </div>
    </div>
@php
    $u = auth()->user();
    $curr = $u->currentWorkspace;

    // 1. Tentar encontrar o teu cofre pessoal (Onde és o dono)
    $home = $u->workspaces()
        ->where('type', 'personal')
        ->where('owner_id', $u->id)
        ->first();

    // 2. Verificar se o que estás a ver agora é o teu cofre pessoal
    $isAtHome = ($home && $curr->id === $home->id);

    // 3. Definir o Alvo:
    // Se estás no Pessoal -> O alvo é o próximo espaço da lista (Empresa ou João)
    // Se estás noutro -> O alvo é o teu Pessoal (Eduardo)
    $target = $isAtHome
        ? $u->workspaces()->where('workspaces.id', '!=', $curr->id)->first()
        : $home;
@endphp

<div class="inline-flex items-center gap-4 p-2 pl-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-md group">
    {{-- INFO ATUAL (Sempre Visível) --}}
    <div class="flex items-center gap-3 pr-4 border-e border-zinc-100 dark:border-zinc-800">
        <div class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isAtHome ? 'bg-emerald-400' : 'bg-amber-400' }} opacity-30"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 {{ $isAtHome ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
        </div>

        <div class="flex flex-col text-left">
            <span class="text-[7px] font-black uppercase text-zinc-400 tracking-[0.2em] leading-none mb-1">Espaço Ativo</span>
            <span class="text-[11px] font-black dark:text-white uppercase italic leading-none truncate max-w-[150px]">
                {{ $curr->name }}
            </span>
        </div>
    </div>

    {{-- BOTÃO DE TROCA (Só aparece se houver outro espaço) --}}
    @if($target)
        <a href="{{ route('workspace.switch.fast', $target->id) }}"
           class="flex items-center gap-2 px-4 py-2 bg-zinc-950 dark:bg-zinc-800 hover:bg-zinc-800 dark:hover:bg-brand-600 text-white rounded-xl transition-all active:scale-95 no-underline group/btn">

            <flux:icon name="arrow-path" variant="micro" class="size-3.5 transition-transform group-hover/btn:rotate-180 duration-500" />

            <span class="text-[10px] font-black uppercase tracking-widest whitespace-nowrap">
                Ir para {{ explode(' ', $target->name)[0] }}
            </span>
        </a>
    @else
        {{-- Fallback caso só tenhas 1 workspace no total --}}
        <div class="pr-3 flex items-center gap-2 opacity-40">
            <flux:icon name="lock-closed" variant="micro" class="size-3" />
            <span class="text-[9px] font-black uppercase tracking-widest">Gestão Única</span>
        </div>
    @endif
</div>
    {{-- 3. GRID: PARTILHADO VS PRIVADO --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

       {{-- COLUNA A: ESPAÇO PARTILHADO --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4 text-left w-full">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-brand-500/10 rounded-lg text-brand-600">
                        <flux:icon name="users" variant="outline" class="size-4" />
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-900 dark:text-white leading-none">
    Espaço Partilhado
</h2>
@if(!$isAtPrivate)
    <flux:badge variant="neutral" size="sm" class="text-[7px] font-black uppercase">Ativo Agora</flux:badge>
@endif

                    {{-- INFO COM ALPINE.JS --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="flex items-center justify-center size-7 rounded-full bg-brand-600 text-white shadow-lg shadow-brand-500/40 ring-4 ring-brand-500/20 hover:scale-110 hover:bg-brand-500 transition-all group">
                            <flux:icon name="information-circle" variant="solid" class="size-5" />
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                             class="absolute left-0 mt-3 w-80 p-6 bg-zinc-950 text-white border border-zinc-800 shadow-2xl rounded-[2rem] z-50">

                            <div class="space-y-5">
                                <div class="flex items-center gap-2 border-b border-white/10 pb-3">
                                    <span class="text-xs font-black uppercase tracking-widest text-brand-400">📜 Regras do Grupo</span>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <span class="shrink-0 text-lg">👥</span>
                                        <p class="text-[11px] text-zinc-400 leading-relaxed">
                                            <strong class="text-white">Capacidade Máxima:</strong> Podes convidar até <span class="text-brand-400 font-black">5 membros</span> autorizados para este espaço.
                                        </p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="shrink-0 text-lg">👁️</span>
                                        <p class="text-[11px] text-zinc-400 leading-relaxed">
                                            <strong class="text-white">Transparência Total:</strong> Todos os membros vêem <span class="text-brand-400 font-black">todas as despesas</span> e receitas registadas aqui.
                                        </p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="shrink-0 text-lg">👑</span>
                                        <p class="text-[11px] text-zinc-400 leading-relaxed">
                                            <strong class="text-white">Poder de Admin:</strong> Apenas o proprietário gera códigos de convite e pode expulsar utilizadores.
                                        </p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="shrink-0 text-lg">🤝</span>
                                        <p class="text-[11px] text-zinc-400 leading-relaxed">
                                            <strong class="text-white">Colaboração:</strong> Perfeito para gerir orçamentos de casa, supermercado e despesas fixas da família.
                                        </p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="shrink-0 text-lg">🔒</span>
                                        <p class="text-[11px] text-zinc-400 leading-relaxed">
                                            <strong class="text-white">Isolamento:</strong> O que é registado aqui <span class="text-emerald-500 font-bold">não é partilhado</span> com os teus outros grupos ou cofres privados.
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-white/5 p-3 rounded-xl">
                                    <p class="text-[9px] text-zinc-500 font-bold uppercase text-center italic">
                                        Usa este espaço para a economia comum da família.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- BADGE: Só aparece se estiveres num cofre visitado --}}
                @if($sharedWs)
                    <flux:badge variant="neutral" size="sm" class="text-[7px] font-black uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800">Visita Ativa</flux:badge>
                @endif
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-8 shadow-sm min-h-[400px] flex flex-col items-center">
                @if($sharedWs)
                    <div class="flex flex-col items-center text-center space-y-4 w-full h-full">
                        {{-- Moldura da Foto (João) --}}
                        <div class="size-32 rounded-[2.5rem] overflow-hidden border-4 border-white dark:border-zinc-800 shadow-2xl bg-zinc-100 flex items-center justify-center">
                            @if($sharedWs->logo_path)
                                <img src="{{ asset($sharedWs->logo_path) }}?t={{ time() }}" class="size-full object-cover">
                            @else
                                <span class="text-3xl font-black text-brand-600 uppercase italic">{{ substr($sharedWs->name, 0, 2) }}</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Proprietário da conta</p>
                            <h3 class="text-xl font-black dark:text-white uppercase italic tracking-tighter">{{ $sharedWs->name }}</h3>
                        </div>

                        {{-- Lista de Membros do Espaço Visitado --}}
                        <div class="w-full mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-left">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Membros Autorizados</p>
                            <div class="grid grid-cols-1 gap-2 w-full">
                                @foreach($sharedMembers as $sm)
                                    <div class="flex items-center gap-3 p-2 bg-zinc-50/50 dark:bg-zinc-950/50 rounded-xl border border-zinc-100 dark:border-zinc-800/50">
                                        <flux:avatar initials="{{ substr($sm->name, 0, 2) }}" class="size-8" />
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black dark:text-white uppercase truncate">{{ $sm->name }}</span>
                                            <span class="text-[8px] font-bold text-brand-600 uppercase">
                                                @if($sm->id === $sharedWs->owner_id) 👑 ADMINISTRADOR @else 👤 MEMBRO @endif
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- BOTÃO PARA SAIR DA VISTA (EDUARDO VOLTA AO SEU COFRE) --}}
                       @if(!$isAtPrivate && $myPersonalWs)
    <a href="{{ route('workspace.switch.fast', $myPersonalWs->id) }}"
       class="group flex items-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl transition-all active:scale-95 no-underline">
        <flux:icon name="arrow-uturn-left" variant="micro" class="size-4" />
        Voltar ao Meu Cofre
    </a>
@endif
                    </div>
                @else
                    {{-- ESTADO VAZIO: Quando estás no teu cofre e não visitas ninguém --}}
                    <div class="my-auto text-center opacity-30">
                        <flux:icon name="users" class="size-12 mx-auto mb-4" />
                        <p class="text-xs font-black uppercase tracking-widest italic px-6 leading-relaxed">
                            Não estás a visitar nenhuma gestão externa de momento.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- COLUNA B: O MEU COFRE PRIVADO --}}
<div class="space-y-6">
    <div class="flex items-center justify-between px-4 text-left w-full">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600">
                <flux:icon name="lock-closed" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-900 dark:text-white leading-none">
                O Meu Cofre Privado
            </h2>
        </div>
        @if($isAtPrivate)
            <flux:badge variant="success" size="sm" class="text-[7px] font-black uppercase">Ativo Agora</flux:badge>
        @endif
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-8 shadow-sm min-h-[400px] flex flex-col justify-center">

        @if($myPersonalWs)
            {{-- SE O COFRE JÁ EXISTE: MOSTRA A GESTÃO --}}
            <div class="flex flex-col items-center">
                <div class="relative group">
                    <div class="size-32 rounded-[2.5rem] overflow-hidden border-4 border-emerald-50 dark:border-zinc-800 shadow-xl bg-zinc-50 flex items-center justify-center">
                        @if($myPersonalWs->logo_path)
                            <img src="{{ asset($myPersonalWs->logo_path) }}?v={{ time() }}" class="size-full object-cover">
                        @else
                            <div class="text-2xl font-black text-emerald-600 uppercase italic">
                                {{ substr($user->name, 0, 1) }}{{ substr(explode(' ', $user->name)[1] ?? '', 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <label class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-all cursor-pointer rounded-[2.5rem] backdrop-blur-[2px]">
                        <input type="file" wire:model="personalPhoto" class="hidden" accept="image/*">
                        <flux:icon name="camera" class="size-8 text-white" />
                    </label>
                </div>
            </div>

            <div class="space-y-4 mt-6">
                <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-widest ml-1">Nome da Minha Gestão</flux:label>
                <div class="flex gap-2">
                    <flux:input wire:model="personalWorkspaceName" class="flex-1 h-12 !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-xl font-bold shadow-inner" />
                    <flux:button wire:click="updatePersonalName" variant="primary" class="rounded-xl bg-emerald-600 shadow-lg shadow-emerald-500/20">
                        <flux:icon name="check" variant="micro" />
                    </flux:button>
                </div>
            </div>

            <div class="p-4 mt-6 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800 text-center text-left">
                <p class="text-[10px] text-zinc-500 italic leading-relaxed">Só tu tens as chaves de acesso a este espaço. Estes dados não são partilhados.</p>
            </div>
        @else
            {{-- SE O COFRE NÃO EXISTE: MOSTRA O BOTÃO DE CRIAÇÃO --}}
            <div class="text-center space-y-6 animate-in fade-in zoom-in duration-700">
                <div class="size-20 mx-auto bg-emerald-500/10 text-emerald-600 rounded-[2rem] flex items-center justify-center shadow-inner">
                    <flux:icon name="shield-check" variant="solid" class="size-10" />
                </div>

                <div class="space-y-2">
                    <h3 class="text-xl font-black dark:text-white uppercase italic tracking-tighter">Ativar o teu Bunker</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed px-4">
                        Parece que ainda não tens um cofre pessoal criado. Ativa-o agora para começares a gerir as tuas finanças com privacidade total.
                    </p>
                </div>

                <flux:button wire:click="createPersonalSpace" variant="primary" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 border-none font-black uppercase tracking-widest text-[11px] shadow-xl shadow-emerald-500/20">
                    Criar o Meu Espaço 🚀
                </flux:button>
            </div>
        @endif

    </div>
</div>
    </div>



    {{-- 5. SEPARADOR DE SECÇÃO --}}
    <div class="relative py-12">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-zinc-200 dark:border-zinc-800"></div>
        </div>
        <div class="relative flex justify-center">
            <div class="bg-zinc-50 dark:bg-zinc-950 px-6 flex items-center gap-3">
                <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                    <flux:icon name="key" variant="micro" class="size-4 text-zinc-400" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Acessos e Convites</span>
            </div>
        </div>
    </div>






@if($iAmAdmin)
{{-- 6. PAINEL DE CONTROLO ADMINISTRATIVO --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-12">

        {{-- BLOCO A: PAINEL DE PRIVILÉGIOS (FORMATO SELECT TOTAL) --}}
        <div x-data="{ openPremium: false, openFinances: false, openTools: false }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm flex flex-col self-start">
            <div>
                {{-- Cabeçalho Principal --}}
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-purple-500/10 rounded-xl text-purple-600">
                        <flux:icon name="shield-check" variant="outline" class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Segurança</h3>
                        <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Gestão de Privilégios</p>
                    </div>
                </div>

                <div class="space-y-5">
                    {{-- 1. SELEÇÃO DO UTILIZADOR (NATIVO FLUX) --}}
                    <flux:select wire:model.live="permUserId" label="👤 Selecionar Utilizador">
                        <option value="">— Escolher Membro —</option>
                        @foreach($members as $m)
                            @if($m->id !== auth()->id())
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endif
                        @endforeach
                    </flux:select>

                    {{-- 2. MÓDULOS PREMIUM (FORMATO SELECT) --}}
                    <div class="space-y-2">
                        <flux:label>💎 Módulos Premium</flux:label>
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden shadow-sm">
                            <button @click="openPremium = !openPremium" type="button" class="flex items-center justify-between w-full h-10 px-3 bg-white dark:bg-zinc-800 text-sm outline-none">
                                <span class="text-zinc-500 dark:text-zinc-400" x-text="openPremium ? '— Fechar Opções —' : '— Configurar Módulos —'"></span>
                                <flux:icon name="chevron-up-down" variant="micro" class="size-4 text-zinc-400" />
                            </button>
                            <div x-show="openPremium" x-collapse class="p-2 bg-zinc-50 dark:bg-zinc-950/50 border-t dark:border-zinc-700 space-y-1">
                                @foreach([['💼 Área Empresa', 'restrictBusiness'], ['🛒 Loja/Stock', 'restrictStore'], ['🏋️ Zona Fitness', 'restrictFitness']] as $item)
                                    <label class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-zinc-800 rounded-md cursor-pointer transition-colors border border-transparent hover:border-zinc-200">
                                        <span class="text-[11px] font-bold uppercase dark:text-white">{{ $item[0] }}</span>
                                        <input type="checkbox" wire:model="{{ $item[1] }}" class="size-4 rounded border-zinc-300 text-purple-600 focus:ring-purple-500">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 3. FINANÇAS (FORMATO SELECT) --}}
                    <div class="space-y-2">
                        <flux:label>💰 Finanças a Bloquear</flux:label>
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden shadow-sm">
                            <button @click="openFinances = !openFinances" type="button" class="flex items-center justify-between w-full h-10 px-3 bg-white dark:bg-zinc-800 text-sm outline-none">
                                <span class="text-zinc-500 dark:text-zinc-400" x-text="openFinances ? '— Fechar Opções —' : '— Opções da Sidebar —'"></span>
                                <flux:icon name="chevron-up-down" variant="micro" class="size-4 text-zinc-400" />
                            </button>
                            <div x-show="openFinances" x-collapse class="p-2 bg-zinc-50 dark:bg-zinc-900/50 border-t dark:border-zinc-700 space-y-1 max-h-60 overflow-y-auto custom-scrollbar">
                                @php
                                    $finances = [
                                        ['🧮 Orçamento', 'restrictBudget'], ['📥 Importar', 'restrictImport'],
                                        ['📈 Receitas', 'restrictIncomes'], ['📊 Investimentos', 'restrictInvestments'],
                                        ['💳 Assinaturas', 'restrictSubs'], ['🏛️ Banco', 'restrictBank'],
                                    ];
                                @endphp
                                @foreach($finances as $f)
                                    <label class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-zinc-800 rounded-md cursor-pointer transition-colors border border-transparent hover:border-zinc-200">
                                        <span class="text-[11px] font-bold uppercase dark:text-white">{{ $f[0] }}</span>
                                        <input type="checkbox" wire:model="{{ $f[1] }}" class="size-4 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 4. FERRAMENTAS (FORMATO SELECT) --}}
                    <div class="space-y-2">
                        <flux:label>🛠️ Ferramentas a Bloquear</flux:label>
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden shadow-sm">
                            <button @click="openTools = !openTools" type="button" class="flex items-center justify-between w-full h-10 px-3 bg-white dark:bg-zinc-800 text-sm outline-none">
                                <span class="text-zinc-500 dark:text-zinc-400" x-text="openTools ? '— Fechar Opções —' : '— Opções da Sidebar —'"></span>
                                <flux:icon name="chevron-up-down" variant="micro" class="size-4 text-zinc-400" />
                            </button>
                            <div x-show="openTools" x-collapse class="p-2 bg-zinc-50 dark:bg-zinc-900/50 border-t dark:border-zinc-700 space-y-1">
                                @php
                                    $tools = [['📅 Calendário', 'restrictCalendar'], ['⏰ Lembretes', 'restrictReminders'], ['🏆 Metas', 'restrictGoals'], ['✨ Wrapped', 'restrictWrapped']];
                                @endphp
                                @foreach($tools as $t)
                                    <label class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-zinc-800 rounded-md cursor-pointer transition-colors border border-transparent hover:border-zinc-200">
                                        <span class="text-[11px] font-bold uppercase dark:text-white">{{ $t[0] }}</span>
                                        <input type="checkbox" wire:model="{{ $t[1] }}" class="size-4 rounded border-zinc-300 text-amber-600 focus:ring-amber-500">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                  {{-- 5. CATEGORIAS BLOQUEADAS (FORMATO MULTI-CHECKBOX) --}}
<div x-data="{ openCats: false }" class="space-y-2">
    <flux:label>🚫 Bloquear Categorias Específicas</flux:label>
    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden shadow-sm">
        <button @click="openCats = !openCats" type="button" class="flex items-center justify-between w-full h-10 px-3 bg-white dark:bg-zinc-800 text-sm outline-none">
            <span class="text-zinc-500 dark:text-zinc-400" x-text="openCats ? '— Fechar Lista —' : '— Selecionar Múltiplas Categorias —'"></span>
            <flux:icon name="chevron-up-down" variant="micro" class="size-4 text-zinc-400" />
        </button>

        <div x-show="openCats" x-collapse class="p-2 bg-zinc-50 dark:bg-zinc-900/50 border-t dark:border-zinc-700 space-y-1 max-h-60 overflow-y-auto custom-scrollbar">
            @forelse($categories as $c)
                <label class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-zinc-800 rounded-md cursor-pointer transition-colors border border-transparent hover:border-zinc-200">
                    <div class="flex items-center gap-2">
                        <flux:icon name="{{ $c->icon ?? 'tag' }}" variant="micro" class="size-3 text-zinc-400" />
                        <span class="text-[11px] font-bold uppercase dark:text-white">{{ $c->name }}</span>
                    </div>
                    <input type="checkbox" wire:model="selectedCategories" value="{{ $c->id }}" class="size-4 rounded border-zinc-300 text-red-600 focus:ring-red-500">
                </label>
            @empty
                <p class="p-4 text-[10px] text-zinc-400 text-center italic">Nenhuma categoria configurada.</p>
            @endforelse
        </div>
    </div>

</div>
                </div>
            </div>






            {{-- Botão de Salvar --}}
           <flux:button wire:click="updatePrivileges" variant="primary" class="w-full mt-8 rounded-2xl bg-emerald-600 border-none font-black uppercase tracking-widest text-[10px] h-14 shadow-lg shadow-emerald-500/20">
    Salvar Definições de Acesso 🔐
</flux:button>
        </div>














        {{-- BLOCO B: MESADA DIGITAL (TAMANHO INDEPENDENTE) --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm flex flex-col self-start">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-emerald-500/10 rounded-xl text-emerald-600">
                    <flux:icon name="banknotes" variant="outline" class="size-5" />
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Financeiro</h3>
                    <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Mesada Digital</p>
                </div>
            </div>

            {{-- CONFIGURAÇÃO DE MESADA --}}
            <div class="space-y-4 mb-8">
                <div class="grid grid-cols-1 gap-4">
                    {{-- 1. SELEÇÃO DO MEMBRO --}}
                    <flux:select wire:model.live="allowanceUserId" placeholder="Quem recebe?">
    <option value="">— Selecionar Membro —</option>
    @foreach($members as $m)
        <option value="{{ $m->id }}">{{ $m->name }}</option>
    @endforeach
</flux:select>

                    <div class="grid grid-cols-2 gap-3">
                        {{-- 2. VALOR DA MESADA --}}
                        <flux:input wire:model="allowanceLimit" type="number" label="Valor do Depósito" placeholder="0.00 €" />

                        {{-- 3. FREQUÊNCIA --}}
                        <flux:select wire:model="allowanceFrequency" label="Recorrência">
                            <option value="daily">🕒 Diário</option>
                            <option value="weekly">📅 Semanal</option>
                            <option value="monthly" selected>💳 Mensal</option>
                            <option value="yearly">🗓️ Anual</option>
                        </flux:select>
                    </div>

                    {{-- 4. LIMITE DE GASTO --}}
                    <div class="relative group">
                        <flux:input wire:model="spendingLimit" type="number" label="Limite Máximo de Gasto (Segurança)" placeholder="Teto máximo..." />
                        <p class="text-[8px] text-zinc-400 mt-1 italic leading-tight">Define o valor máximo que o membro pode gastar no total, independentemente da mesada.</p>
                    </div>

                    <flux:button wire:click="setAllowance" variant="primary" class="w-full h-12 bg-emerald-600 border-none font-black uppercase tracking-widest text-[10px] rounded-2xl shadow-lg shadow-emerald-500/20">
                        Ativar Mesada 🟢
                    </flux:button>
                </div>
            </div>

            {{-- LISTA DE MESADAS ATIVAS --}}
            <div class="overflow-y-auto max-h-[300px] custom-scrollbar pr-2 space-y-3">
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2 px-2">Registos Ativos</p>

               @forelse($allowances as $a)
    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 rounded-3xl border border-zinc-100 dark:border-zinc-800">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <flux:avatar initials="{{ substr($a->user->name, 0, 2) }}" class="size-8" />
                <div class="flex flex-col">
                    <span class="text-[10px] font-black uppercase dark:text-white">{{ $a->user->name }}</span>
                    <span class="text-[7px] font-bold text-zinc-400 uppercase italic">
                        Mensalidade: {{ $a->allowance_limit > 0 ? number_format($a->allowance_limit, 0).'€' : 'N/A' }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                {{-- Destaque para o limite aqui --}}
                <span class="text-[10px] font-black text-brand-600">
                    LIMITE: {{ $a->spending_limit > 0 ? number_format($a->spending_limit, 2, ',', ' ') . '€' : 'LIVRE' }}
                </span>
            </div>
        </div>
    </div>
@empty
    <p class="text-center text-[10px] opacity-30">Sem registos ativos</p>
@endforelse
            </div>
        </div>
















        {{-- BLOCO C: OBJETIVOS FAMILIARES (TAMANHO INDEPENDENTE) --}}
        <div class="bg-zinc-950 text-white rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden group flex flex-col self-start">
            <div class="absolute -right-10 -top-10 size-40 bg-brand-500/10 blur-[80px] rounded-full"></div>

            <div class="relative z-10 flex flex-col">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-brand-500/20 rounded-xl">
                        <flux:icon name="trophy" class="size-5 text-brand-400" />
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Poupança</h3>
                        <p class="text-lg font-black uppercase italic tracking-tighter">Metas de Grupo</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @forelse($familyGoals as $goal)
                        @php $pct = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0; @endphp
                        <div class="group/goal">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-[11px] font-black uppercase tracking-tight text-zinc-300 group-hover/goal:text-white transition-colors">{{ $goal->name }}</span>
                                <span class="text-xs font-black text-brand-400">{{ $pct }}%</span>
                            </div>
                            <div class="h-2.5 bg-white/5 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                <div class="h-full bg-gradient-to-r from-brand-600 to-brand-400 shadow-[0_0_12px_#3b82f6] transition-all duration-1000" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 opacity-30">
                            <p class="text-[10px] font-black uppercase tracking-widest italic text-zinc-500">Sem objetivos em curso</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endif




    {{-- 3. PAINEL INTEGRADO: AUDITORIA & MEMBROS --}}
<div class="space-y-6 mt-12">
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-brand-600 rounded-lg shadow-lg shadow-brand-500/20 text-white">
                <flux:icon name="presentation-chart-line" variant="mini" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Auditoria Global de Membros</h2>
        </div>
        <flux:badge variant="neutral" class="font-black text-[9px] uppercase bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1">
            {{ count($members) }} Utilizadores Ativos
        </flux:badge>
    </div>

    <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50/50 dark:bg-zinc-950/20 text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em] border-b dark:border-zinc-800">
                        <th class="p-6">Identidade</th>
                        <th class="p-6">Privilégios</th>
                        <th class="p-6 text-right">Rendimento</th>
                        <th class="p-6 text-right">Despesas</th>
                        <th class="p-6 text-right">Net Balance</th>
                        <th class="p-6 text-right">Dossiê</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($memberStats as $member)
    @php
    // 1. Procurar TODAS as permissões deste membro específico
    $memberPerms = \App\Models\FamilyBudgetPermission::with('category')
        ->where('user_id', $member->id)
        ->where('workspace_id', $workspace->id)
        ->get();

    // 2. Definir o registo Global (onde category_id é null)
    $globalPerms = $memberPerms->whereNull('category_id')->first();

    // 3. ESSENCIAL: Atribuir a $statsMembro para o card do lado direito não dar erro
    // E atribuir a $userAllowance caso algum card antigo ainda o use.
    $statsMembro = $globalPerms;
    $userAllowance = $globalPerms;

    // 4. Categorias bloqueadas
    $restrictedCategories = $memberPerms->whereNotNull('category_id');

    // 5. Organização de Listas Visuais (Sistema)
    $blockedModules = [];
    if ($globalPerms) {
        // Módulos Premium
        if ($globalPerms->restrict_business)   $blockedModules[] = ['💼', 'Área Empresa'];
        if ($globalPerms->restrict_store)      $blockedModules[] = ['🛒', 'Loja/Stock'];
        if ($globalPerms->restrict_fitness)    $blockedModules[] = ['🏋️', 'Zona Fitness'];

        // Finanças (Corrigido para formato [ícone, nome] para evitar erro de índice 1)
        if ($globalPerms->restrict_budget)      $blockedModules[] = ['🧮', 'Orçamento'];
        if ($globalPerms->restrict_import)      $blockedModules[] = ['📥', 'Importar'];
        if ($globalPerms->restrict_incomes)     $blockedModules[] = ['📈', 'Receitas'];
        if ($globalPerms->restrict_investments) $blockedModules[] = ['📊', 'Investimentos'];
        if ($globalPerms->restrict_subs)        $blockedModules[] = ['💳', 'Assinaturas'];
        if ($globalPerms->restrict_bank)        $blockedModules[] = ['🏛️', 'Banco'];

        // Ferramentas
        if ($globalPerms->restrict_calendar)    $blockedModules[] = ['📅', 'Calendário'];
        if ($globalPerms->restrict_reminders)   $blockedModules[] = ['⏰', 'Lembretes'];
        if ($globalPerms->restrict_goals)       $blockedModules[] = ['🏆', 'Metas'];
        if ($globalPerms->restrict_wrapped)     $blockedModules[] = ['✨', 'Wrapped'];
    }
@endphp
                        <tr x-data="{ open: false }" class="group hover:bg-zinc-50/50 dark:hover:bg-brand-500/5 transition-all">
                            {{-- COLUNA: IDENTIDADE --}}
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <flux:avatar initials="{{ substr($member->name, 0, 2) }}" class="size-11 shadow-sm border-2 border-white dark:border-zinc-800" />
                                    <div>
                                        <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                            {{ $member->name }}
                                            @if($member->id === auth()->id()) <span class="text-[8px] bg-brand-500 text-white px-2 py-0.5 rounded-full font-black tracking-widest italic">TU</span> @endif
                                        </p>
                                        <p class="text-[10px] text-zinc-500 font-medium italic">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- COLUNA: PRIVILÉGIOS --}}
                            <td class="p-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border {{ $member->id === $workspace->owner_id || $member->pivot->role === 'admin' ? 'bg-purple-500/10 text-purple-600 border-purple-500/20' : 'bg-zinc-500/10 text-zinc-500 border-zinc-500/20' }}">
                                    {{ $member->id === $workspace->owner_id || $member->pivot->role === 'admin' ? '👑 Admin Master' : '👤 Membro' }}
                                </span>
                            </td>

                            {{-- COLUNA: VALORES --}}
                            <td class="p-6 text-right font-black text-emerald-600">+{{ number_format($member->total_incomes, 2, ',', ' ') }}€</td>
                            <td class="p-6 text-right font-black text-red-500">-{{ number_format($member->total_expenses, 2, ',', ' ') }}€</td>
                            <td class="p-6 text-right font-black {{ $member->net_balance >= 0 ? 'text-emerald-500' : 'text-red-500' }} italic">
                                {{ $member->net_balance >= 0 ? '+' : '' }}{{ number_format($member->net_balance, 2, ',', ' ') }}€
                            </td>

                            {{-- COLUNA: AÇÕES (DOSSIÊ) --}}
                            <td class="p-6 text-right">
                                <button @click="open = true" class="p-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-950 dark:hover:bg-white hover:text-white dark:hover:text-zinc-950 rounded-xl transition-all">
                                    <flux:icon name="identification" variant="micro" class="size-4" />
                                </button>

                                {{-- MODAL: DOSSIÊ DE INTELIGÊNCIA --}}
                                <div x-show="open" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-md">
                                    <div @click.away="open = false" class="w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-[3rem] shadow-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 text-left animate-in zoom-in-95 duration-300">

                                        {{-- 1. HEADER DO DOSSIÊ --}}
                                        <div class="p-10 flex items-start justify-between bg-zinc-50/50 dark:bg-zinc-950/30">
                                            <div class="flex items-center gap-8">
                                                <div class="relative group">
                                                    <flux:avatar initials="{{ substr($member->name, 0, 2) }}" class="size-24 rounded-[2rem] shadow-2xl border-4 border-white dark:border-zinc-800" />
                                                    <div class="absolute -bottom-1 -right-1 size-6 bg-emerald-500 border-4 border-white dark:border-zinc-900 rounded-full shadow-lg"></div>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-3">
                                                        <h3 class="text-3xl font-black dark:text-white uppercase italic tracking-tighter leading-none">{{ $member->name }}</h3>
                                                        <flux:badge size="sm" class="!bg-emerald-500 !text-white font-black border-none text-[8px] tracking-widest px-2 shadow-sm">
                                                            {{ strtoupper($member->pivot->role ?? 'Membro') }}
                                                        </flux:badge>
                                                    </div>
                                                    <p class="text-sm text-zinc-500 font-bold uppercase tracking-widest mt-2">{{ $member->email }}</p>
                                                    <div class="flex items-center gap-4 mt-4 text-[9px] font-black text-zinc-400 uppercase tracking-widest">
                                                        <span>ID: <span class="text-zinc-900 dark:text-zinc-200">#F-{{ $member->id }}</span></span>
                                                        <span class="text-zinc-300">|</span>
                                                        <span>Membro desde: <span class="text-zinc-900 dark:text-zinc-200">{{ $member->created_at->format('M Y') }}</span></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <button @click="open = false" class="p-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 rounded-2xl text-zinc-500 transition-all">
                                                <flux:icon name="x-mark" variant="micro" class="size-4" />
                                            </button>
                                        </div>

                                        {{-- 2. CORPO DO DOSSIÊ --}}
                                        <div class="p-10 space-y-10 max-h-[60vh] overflow-y-auto custom-scrollbar">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

                                                {{-- LADO ESQUERDO: PERFORMANCE & RESTRIÇÕES --}}
                                                <div class="space-y-10">
                                                    <div>
                                                        <span class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.3em] block mb-5 border-l-2 border-emerald-500 pl-3">Performance Global</span>
                                                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                                                            <div class="flex justify-between items-center mb-3">
                                                                <span class="text-[10px] font-black uppercase text-zinc-500">Nível Familiar</span>
                                                                <span class="text-sm font-black text-emerald-600 italic">RANK #{{ $loop->iteration }}</span>
                                                            </div>
                                                            <div class="flex items-center gap-4">
                                                                <span class="text-3xl font-black dark:text-white">Lvl {{ $member->level ?? '1' }}</span>
                                                                <div class="flex-1 h-1.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                                                                    <div class="h-full bg-brand-600 shadow-[0_0_10px_#3b82f6]" style="width: 70%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


{{-- BLOCO: RESTRIÇÕES DE CATEGORIAS --}}
{{-- INÍCIO DA SECÇÃO DE RESTRIÇÕES NO DOSSIÊ --}}
<div class="space-y-12">
  @php
    $allPerms = \App\Models\FamilyBudgetPermission::with('category')
        ->where('user_id', $member->id)
        ->where('workspace_id', $workspace->id)
        ->get();

    $global = $allPerms->whereNull('category_id')->first();
    $blockedCats = $allPerms->whereNotNull('category_id');

    $premium = [];
    if($global?->restrict_business) $premium[] = ['💼', 'Área Empresa'];
    if($global?->restrict_store)    $premium[] = ['🛒', 'Loja/Stock'];
    if($global?->restrict_fitness)  $premium[] = ['🏋️', 'Zona Fitness'];

    $finances = [];
    // Adicionados os ícones para evitar o erro "Undefined array key 1"
    if($global?->restrict_budget)      $finances[] = ['🧮', 'Orçamento'];
    if($global?->restrict_import)      $finances[] = ['📥', 'Importar'];
    if($global?->restrict_incomes)     $finances[] = ['📈', 'Receitas'];
    if($global?->restrict_debts)       $finances[] = ['🤝', 'Dívidas'];
    if($global?->restrict_investments) $finances[] = ['📊', 'Investimentos'];
    if($global?->restrict_subs)        $finances[] = ['💳', 'Assinaturas'];
    if($global?->restrict_bank)        $finances[] = ['🏛️', 'Banco'];

    $tools = [];
    if($global?->restrict_calendar)  $tools[] = ['📅', 'Calendário'];
    if($global?->restrict_reminders) $tools[] = ['⏰', 'Lembretes'];
    if($global?->restrict_goals)     $tools[] = ['🏆', 'Metas'];
    if($global?->restrict_wrapped)   $tools[] = ['✨', 'Wrapped'];
@endphp
    {{-- 1. MÓDULOS PREMIUM --}}
    <div class="space-y-5">
        <div class="flex items-center gap-4">
            <div class="p-2.5 bg-purple-600 rounded-2xl shadow-lg shadow-purple-500/20 text-white">
                <flux:icon name="sparkles" variant="solid" class="size-5" />
            </div>
            <div class="flex flex-col">
                <h3 class="text-sm font-black uppercase tracking-[0.25em] dark:text-white leading-none">Módulos Premium</h3>
                <span class="text-[9px] font-bold text-zinc-400 uppercase mt-1 tracking-widest italic">Acesso a áreas exclusivas</span>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-purple-500/50 to-transparent"></div>
        </div>

        <div class="flex flex-wrap gap-2.5">
            @forelse($premium as $p)
                <div class="px-4 py-2.5 bg-white dark:bg-zinc-800 border-2 border-purple-500/20 rounded-2xl flex items-center gap-3 shadow-sm hover:border-purple-500 transition-all">
                    <span class="text-base">{{ $p[0] }}</span>
                    <span class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-tight">{{ $p[1] }}</span>
                </div>
            @empty
                <div class="w-full p-4 bg-emerald-500/5 border border-dashed border-emerald-500/20 rounded-2xl flex items-center gap-3">
                    <flux:icon name="check-circle" variant="solid" class="size-4 text-emerald-500" />
                    <p class="text-[10px] font-black uppercase text-emerald-600 italic">Utilizador com acesso total aos módulos</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 2. FINANÇAS --}}
    <div class="space-y-5">
        <div class="flex items-center gap-4">
            <div class="p-2.5 bg-emerald-600 rounded-2xl shadow-lg shadow-emerald-500/20 text-white">
                <flux:icon name="banknotes" variant="solid" class="size-5" />
            </div>
            <div class="flex flex-col">
                <h3 class="text-sm font-black uppercase tracking-[0.25em] dark:text-white leading-none">Finanças Bloqueadas</h3>
                <span class="text-[9px] font-bold text-zinc-400 uppercase mt-1 tracking-widest italic">Controlo de visibilidade financeira</span>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-emerald-500/50 to-transparent"></div>
        </div>

        <div class="flex flex-wrap gap-2.5">
            @forelse($finances as $f)
                <div class="px-4 py-2.5 bg-red-500/5 border-2 border-red-500/10 rounded-2xl flex items-center gap-3">
                    <span class="text-base">{{ $f[0] }}</span>
                    <span class="text-[10px] font-black text-red-600 dark:text-red-400 uppercase tracking-tight">{{ $f[1] }}</span>
                </div>
            @empty
                <div class="w-full p-4 bg-emerald-500/5 border border-dashed border-emerald-500/20 rounded-2xl flex items-center gap-3">
                    <flux:icon name="shield-check" variant="solid" class="size-4 text-emerald-500" />
                    <p class="text-[10px] font-black uppercase text-emerald-600 italic">Transparência financeira total ativa</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 3. FERRAMENTAS --}}
    <div class="space-y-5">
        <div class="flex items-center gap-4">
            <div class="p-2.5 bg-amber-500 rounded-2xl shadow-lg shadow-amber-500/20 text-white">
                <flux:icon name="wrench-screwdriver" variant="solid" class="size-5" />
            </div>
            <div class="flex flex-col">
                <h3 class="text-sm font-black uppercase tracking-[0.25em] dark:text-white leading-none">Ferramentas Restritas</h3>
                <span class="text-[9px] font-bold text-zinc-400 uppercase mt-1 tracking-widest italic">Acesso a utilitários de sistema</span>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-amber-500/50 to-transparent"></div>
        </div>

        <div class="flex flex-wrap gap-2.5">
            @forelse($tools as $t)
                <div class="px-4 py-2.5 bg-white dark:bg-zinc-800 border-2 border-amber-500/20 rounded-2xl flex items-center gap-3">
                    <span class="text-base">{{ $t[0] }}</span>
                    <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-tight">{{ $t[1] }}</span>
                </div>
            @empty
                <div class="w-full p-4 bg-emerald-500/5 border border-dashed border-emerald-500/20 rounded-2xl flex items-center gap-3">
                    <flux:icon name="cpu-chip" variant="solid" class="size-4 text-emerald-500" />
                    <p class="text-[10px] font-black uppercase text-emerald-600 italic">Todas as ferramentas operacionais libertadas</p>
                </div>
            @endforelse
        </div>
    </div>

   {{-- 4. CONTEÚDO RESTRITO (DENTRO DO MODAL/DOSSIER) --}}
<div class="space-y-5">
    <div class="flex items-center gap-4">
        <div class="p-2.5 bg-red-600 rounded-2xl shadow-lg shadow-red-500/20 text-white">
            <flux:icon name="no-symbol" variant="solid" class="size-5" />
        </div>
        <div class="flex flex-col text-left">
            <h3 class="text-sm font-black uppercase tracking-[0.25em] dark:text-white leading-none">Conteúdo Restrito</h3>
            <span class="text-[9px] font-bold text-zinc-400 uppercase mt-1 tracking-widest italic">Bloqueio de categorias específicas</span>
        </div>
        <div class="flex-1 h-px bg-gradient-to-r from-red-500/50 to-transparent"></div>
    </div>

    @php
        // Buscamos as categorias bloqueadas especificamente para ESTE membro ($member->id)
        $categoriesRestricted = \App\Models\FamilyBudgetPermission::where('user_id', $member->id)
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->whereNotNull('category_id')
            ->with('category') // Carrega o nome da categoria
            ->get();
    @endphp

    <div class="flex flex-wrap gap-2.5">
        @forelse($categoriesRestricted as $item)
            @if($item->category)
                <div class="px-4 py-2.5 bg-zinc-950 text-white rounded-2xl border-2 border-red-500/30 flex items-center gap-3 shadow-xl">
                    <flux:icon name="lock-closed" variant="micro" class="size-3 text-red-500" />
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ $item->category->name }}</span>
                </div>
            @endif
        @empty
            {{-- ESTE É O QUE ESTÁ A APARECER AGORA PORQUE A QUERY ACIMA ESTAVA FALHA --}}
            <div class="w-full p-4 bg-emerald-500/5 border border-dashed border-emerald-500/20 rounded-2xl flex items-center gap-3 justify-center">
                <flux:icon name="eye" variant="solid" class="size-4 text-emerald-500" />
                <p class="text-[10px] font-black uppercase text-emerald-600 italic">Sem restrições de categorias específicas</p>
            </div>
        @endforelse
    </div>
</div>
</div>
{{-- FIM DA SECÇÃO DE RESTRIÇÕES --}}
                                                </div>






















{{-- LADO DIREITO: ENGENHARIA DE CAPITAL --}}
<div class="space-y-6">
    <div class="flex items-center justify-between mb-5 px-1">
        <span class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.3em] border-l-2 border-brand-500 pl-3">Engenharia de Capital</span>
        <span class="text-[8px] font-black px-2 py-0.5 bg-emerald-500/10 text-emerald-600 rounded-full uppercase tracking-widest animate-pulse">Monitorização Ativa</span>
    </div>

   {{-- 1. CARD MESTRE: MESADA (VERSÃO SIMPLIFICADA) --}}
<div class="relative p-7 rounded-[2.5rem] bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-xl shadow-emerald-500/10 overflow-hidden">
    {{-- Ícone de Fundo Discreto --}}
    <flux:icon name="banknotes" variant="solid" class="absolute -bottom-2 -right-2 size-24 opacity-10" />

    <div class="relative z-10">
        {{-- Topo: Status --}}
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-white/20 rounded-xl">
                    <flux:icon name="credit-card" variant="solid" class="size-4" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest">Saldo Automático</span>
            </div>

            @if($userAllowance && $userAllowance->allowance_limit > 0)
                <span class="flex items-center gap-1.5 px-2 py-1 bg-white text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-tighter">
                    <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Ativo
                </span>
            @endif
        </div>

        @if($userAllowance && $userAllowance->allowance_limit > 0)
            {{-- Valor Principal --}}
            <div class="space-y-0.5">
                <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest opacity-80">Membro recebe</p>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-5xl font-black tracking-tighter">
                        {{ number_format($userAllowance->allowance_limit, 2, ',', ' ') }}€
                    </span>
                    <span class="text-sm font-medium opacity-90 lowercase italic">
                        / por {{ match($userAllowance->allowance_frequency) { 'daily' => 'dia', 'weekly' => 'semana', 'yearly' => 'ano', default => 'mês' } }}
                    </span>
                </div>
            </div>

            {{-- Detalhes Simples --}}
            <div class="mt-8 grid grid-cols-2 gap-4 border-t border-white/10 pt-4">
                <div>
                    <p class="text-[8px] font-black uppercase opacity-60">Próximo depósito</p>
                    <p class="text-xs font-bold mt-0.5">Dia 01 {{ now()->addMonth()->translatedFormat('F') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[8px] font-black uppercase opacity-60">Forma de envio</p>
                    <p class="text-xs font-bold mt-0.5">Transferência Interna</p>
                </div>
            </div>
        @else
            {{-- Estado Vazio Amigável --}}
            <div class="py-6 text-center">
                <p class="text-xl font-black text-white/50 uppercase tracking-tight italic leading-none">Sem mesada definida</p>
                <p class="text-[9px] mt-2 font-medium text-emerald-50/70 max-w-[200px] mx-auto uppercase leading-relaxed tracking-wider">
                    Usa o formulário à esquerda para definir um valor para este membro.
                </p>
            </div>
        @endif
    </div>
</div>

      {{-- 2. GRID: SALDO LÍQUIDO E O NOVO LIMITE DE SEGURANÇA --}}
    <div class="grid grid-cols-2 gap-4">
        {{-- Saldo Líquido --}}
        <div class="p-6 bg-zinc-950 text-white rounded-[2.5rem] shadow-xl border border-white/5 relative overflow-hidden">
             <div class="relative z-10">
                 <p class="text-[8px] font-black uppercase text-zinc-500 tracking-widest mb-1 italic">Net Balance</p>
                 <p class="text-2xl font-black {{ $member->net_balance >= 0 ? 'text-emerald-400' : 'text-red-400' }} tracking-tighter leading-none">
                    {{ $member->net_balance >= 0 ? '+' : '' }}{{ number_format($member->net_balance, 2, ',', ' ') }}€
                 </p>
             </div>
        </div>

        {{-- TETO DE SEGURANÇA --}}
<div class="p-6 bg-white dark:bg-zinc-900 border-2 {{ ($statsMembro && $statsMembro->spending_limit > 0) ? 'border-red-500/20 shadow-lg' : 'border-zinc-200 dark:border-zinc-800 shadow-sm' }} rounded-[2.5rem] flex flex-col justify-center">
    <div class="flex items-center gap-2 mb-1">
        <flux:icon name="no-symbol" variant="micro" class="{{ ($statsMembro && $statsMembro->spending_limit > 0) ? 'text-red-500' : 'text-zinc-400' }} size-3" />
        <p class="text-[8px] font-black uppercase text-zinc-400 tracking-widest">Teto de Gasto</p>
    </div>

    <p class="leading-none text-left">
        @if($statsMembro && $statsMembro->spending_limit > 0)
            <span class="text-2xl font-black dark:text-white tracking-tighter">
                {{ number_format($statsMembro->spending_limit, 2, ',', ' ') }}€
            </span>
        @else
            <span class="text-[10px] font-black text-zinc-300 dark:text-zinc-700 tracking-widest uppercase italic">Ilimitado</span>
        @endif
    </p>
</div>
</div>
    {{-- 3. SCORE IA DE COMPORTAMENTO FINANCEIRO --}}
    <div class="p-8 bg-zinc-50 dark:bg-zinc-950/50 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 relative">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h4 class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Análise de Comportamento</h4>
                <p class="text-xs font-bold dark:text-white mt-0.5">Perfil Baseado em IA</p>
            </div>
            <div class="p-2 bg-brand-500 rounded-xl text-white shadow-lg shadow-brand-500/30">
                <flux:icon name="sparkles" variant="solid" class="size-4" />
            </div>
        </div>

        <div class="space-y-8">
            {{-- Grande Score --}}
            <div class="flex items-end gap-5">
                @php
                    $ratio = $member->total_incomes > 0 ? min(100, ($member->total_expenses / $member->total_incomes) * 100) : 0;
                    $score = $ratio > 0 ? max(0, 100 - $ratio) : 100;
                    $color = $score > 70 ? 'text-emerald-500' : ($score > 40 ? 'text-amber-500' : 'text-red-500');
                    $glow  = $score > 70 ? 'shadow-emerald-500/20' : ($score > 40 ? 'shadow-amber-500/20' : 'shadow-red-500/20');
                @endphp

                <div class="flex flex-col">
                    <span class="text-7xl font-black {{ $color }} leading-none tracking-tighter drop-shadow-sm">{{ round($score) }}</span>
                </div>
                <div class="flex flex-col pb-1 leading-tight">
                    <span class="text-[11px] font-black dark:text-white uppercase tracking-widest italic">Pontos de Saúde</span>
                    <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-[0.2em] mt-1">
                        {{ $score > 70 ? 'Gestão de Excelência' : ($score > 40 ? 'Risco Moderado' : 'Economia em Crise') }}
                    </span>
                </div>
            </div>

            {{-- Top Categoria de Gasto --}}
            <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-4">
                    <div class="p-2.5 bg-brand-500/10 rounded-2xl text-brand-600 border border-brand-500/20">
                        <flux:icon name="presentation-chart-line" variant="micro" class="size-5" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black text-zinc-400 uppercase tracking-[0.2em]">Maior Fluxo de Saída</span>
                        <span class="text-[12px] font-black dark:text-white uppercase tracking-tight italic">
                            @php
                                $topSpending = \App\Models\Expense::where('user_id', $member->id)
                                    ->where('workspace_id', $workspace->id)
                                    ->select('subcategory', \DB::raw('count(*) as total'))
                                    ->groupBy('subcategory')
                                    ->orderByDesc('total')
                                    ->first();
                            @endphp
                            {{ $topSpending->subcategory ?? 'Sem Atividade' }}
                        </span>
                    </div>
                    <div class="ml-auto text-right">
                        <span class="text-xl font-black dark:text-white">{{ $topSpending->total ?? 0 }}</span>
                        <p class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest">Faturas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. PARTICIPAÇÃO EM OBJETIVOS DA FAMÍLIA --}}
    <div class="p-6 bg-zinc-900 text-white rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-600/10 to-transparent"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black uppercase text-brand-400 tracking-[0.3em]">Commitment</p>
                <p class="text-[12px] font-black text-white uppercase mt-1 italic tracking-tighter">
                    {{ $familyGoals->count() }} Objetivos em Foco
                </p>
            </div>
            <div class="flex -space-x-3">
                @foreach($familyGoals->take(3) as $goal)
                    <div class="size-10 rounded-full border-4 border-zinc-900 bg-brand-500 flex items-center justify-center text-[11px] font-black text-white shadow-xl group-hover:translate-y-[-2px] transition-transform" title="{{ $goal->name }}">
                        {{ substr($goal->name, 0, 1) }}
                    </div>
                @endforeach
                @if($familyGoals->count() > 3)
                     <div class="size-10 rounded-full border-4 border-zinc-900 bg-zinc-700 flex items-center justify-center text-[10px] font-black text-zinc-300">
                        +{{ $familyGoals->count() - 3 }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

































                                            {{-- 3. ZONA DE COMANDO (ADMINS) --}}
                                            @if($iAmAdmin && $member->id !== auth()->id())
                                                <div class="pt-10 border-t border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row gap-6">
                                                    <div class="flex-1">
                                                        <label class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em] block mb-3">Autoridade Familiar</label>
                                                        <flux:select wire:change="updateRole({{ $member->id }}, $event.target.value)" class="!bg-zinc-100 dark:!bg-zinc-800 border-none rounded-2xl font-black uppercase text-xs h-14">
                                                            <option value="admin" {{ ($member->pivot->role ?? '') === 'admin' ? 'selected' : '' }}>👑 Administrador Master</option>
                                                            <option value="member" {{ ($member->pivot->role ?? '') === 'member' ? 'selected' : '' }}>👤 Membro Participante</option>
                                                        </flux:select>
                                                    </div>

                                                </div>
                                            @endif
                                        </div>
 @if($iAmAdmin)
                                            <div class="flex flex-col justify-end">
                                                        <flux:button wire:click="removeMember({{ $member->id }})" wire:confirm="Expulsar este membro?" variant="ghost" class="h-14 rounded-2xl text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 font-black uppercase tracking-widest text-[10px]">
                                                            <flux:icon name="trash" class="size-4 mr-2" /> Revogar Acesso
                                                        </flux:button>
                                                    </div>
@endif
                                        {{-- 4. FOOTER --}}
                                        <div class="p-8 bg-zinc-50/80 dark:bg-zinc-950/50 border-t border-zinc-100 dark:border-zinc-800 text-center">
                                            <flux:button @click="open = false" variant="primary" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black uppercase tracking-[0.3em] text-[11px] border-none shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                                                Fechar Ficha de Auditoria
                                            </flux:button>


                                        </div>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


















@if($iAmAdmin)
    {{-- LINHA DE CÓDIGOS + CONVITES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- PARTILHAR CÓDIGO --}}
        <div class="glass-card p-6 sm:p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -left-10 -bottom-10 size-48 bg-brand-500/5 blur-[80px] rounded-full group-hover:bg-brand-500/10 transition-all"></div>
            <div class="relative z-10 space-y-6">
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="p-2 bg-brand-500/10 rounded-xl">
                        <flux:icon name="share" class="size-5 text-brand-600" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Código</p>
                        <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Convidar Membros</p>
                    </div>
                </div>

                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                    Partilha este código com quem queres adicionar ao teu grupo financeiro.
                </p>

                <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-6 py-4">
                    <span class="text-3xl font-mono font-black text-brand-600 dark:text-brand-400 tracking-[0.3em] uppercase">
                        {{ $inviteCode }}
                    </span>
                    <div class="flex items-center gap-2">
                        <button x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText('{{ $inviteCode }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="p-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all group/copy"
                            title="Copiar código">
                            <flux:icon x-show="!copied" name="clipboard" variant="micro" class="size-4 text-zinc-400 group-hover/copy:text-brand-600" />
                            <flux:icon x-show="copied" x-cloak name="check" variant="micro" class="size-4 text-emerald-500" />
                        </button>
                        <flux:button wire:click="generateInviteCode" size="sm" variant="ghost" icon="arrow-path" class="text-zinc-400 hover:text-brand-600 transition-colors" title="Gerar novo código" />
                    </div>
                </div>
                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest italic">O código não expira. Podes regenerá-lo a qualquer momento.</p>
            </div>
        </div>

        {{-- ENTRAR NUM GRUPO --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 size-48 bg-emerald-500/5 blur-[80px] rounded-full group-hover:bg-emerald-500/10 transition-all"></div>
            <div class="relative z-10 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-500/10 rounded-xl">
                        <flux:icon name="arrow-right-circle" class="size-5 text-emerald-600" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Juntar-me</p>
                        <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Entrar num Grupo</p>
                    </div>
                </div>
                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                    Tens um código de convite? Insere-o abaixo para te juntares a um grupo existente.
                </p>
                <div class="space-y-3">
                    <input wire:model="inviteCodeInput" placeholder="EX: AB12CD34" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 font-mono font-black uppercase tracking-widest text-sm dark:text-white placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all shadow-inner" />
                    @error('inviteCodeInput')
                        <p class="text-[10px] font-bold text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <flux:button wire:click="joinWorkspace" variant="primary" icon="arrow-right-circle" class="w-full rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-lg shadow-brand-500/20 h-12">
                    Entrar no Grupo
                </flux:button>
            </div>
        </div>
    </div>
@endif







    {{-- 5. RANKING & AUDITORIA DE ATIVIDADE (LEADERBOARD) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-6">

        {{-- COLUNA: TOP CONTRIBUINTES (ESTILO RANKING PREMIUM) --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-2">
                <div class="p-2 bg-amber-500 rounded-lg shadow-lg shadow-amber-500/20 text-white">
                    <flux:icon name="trophy" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Top Contribuintes</h2>
            </div>

            <div class="space-y-4">
                @foreach($topRecorders as $index => $user)
                    @php
                        $rankStyle = match($index) {
                            0 => 'border-amber-500/50 bg-amber-500/5 shadow-amber-500/10',
                            1 => 'border-zinc-400/50 bg-zinc-400/5',
                            2 => 'border-orange-600/50 bg-orange-600/5',
                            default => 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900'
                        };
                        $medalColor = match($index) {
                            0 => 'text-amber-500',
                            1 => 'text-zinc-400',
                            2 => 'text-orange-600',
                            default => 'text-zinc-500'
                        };
                        $userActivities = $recentActivities->where('user_id', $user->id);
                    @endphp

                    <div x-data="{ showActions: false }" class="glass-card {{ $rankStyle }} border rounded-[2rem] overflow-hidden transition-all duration-300 group">

                        {{-- Cabeçalho do Rank --}}
                        <div @click="showActions = !showActions" class="p-5 flex items-center justify-between cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-4">
                                <span class="text-xl font-black {{ $medalColor }} italic w-8 text-center">{{ $index + 1 }}º</span>
                                <div>
                                    <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                        {{ explode(' ', $user->name)[0] }}
                                        @if($user->id === auth()->id()) <span class="text-[8px] font-black text-brand-600 tracking-widest">(TU)</span> @endif
                                    </p>
                                    <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-tighter">{{ $user->expenses_count }} registos efetuados</p>
                                </div>
                            </div>
                            <flux:icon name="chevron-down" class="size-4 text-zinc-400 transition-transform duration-300" ::class="showActions ? 'rotate-180' : ''" />
                        </div>

                        {{-- Detalhes Expansíveis --}}
                        <div x-show="showActions" x-collapse x-cloak>
                            <div class="px-5 pb-5 border-t border-zinc-100 dark:border-zinc-800 pt-4 bg-zinc-50/50 dark:bg-zinc-950/50">
                                <div class="max-h-64 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                                    @forelse($userActivities as $log)
                                        <div class="p-3 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-[8px] font-black uppercase text-brand-600 tracking-widest italic">{{ $log->created_at->diffForHumans() }}</span>
                                                <flux:icon name="bolt" class="size-3 text-zinc-300" />
                                            </div>
                                            <p class="text-[11px] font-medium text-zinc-600 dark:text-zinc-400 leading-tight">{{ $log->description }}</p>
                                        </div>
                                    @empty
                                        <p class="text-center py-4 text-[10px] font-black uppercase text-zinc-400 italic">Sem atividade recente</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- COLUNA: LINHA DO TEMPO GLOBAL (2 COLUNAS) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand-600 rounded-lg shadow-lg shadow-brand-500/20 text-white">
                        <flux:icon name="clock" variant="outline" class="size-4" />
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Log de Operações</h2>
                </div>
            </div>

            <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm">
                <div class="max-h-[700px] overflow-y-auto p-8 space-y-8 custom-scrollbar">
                    @forelse($recentActivities as $log)
                        @php
                            $props = is_array($log->properties) ? $log->properties : json_decode($log->properties, true);
                            $fieldLabels = [
                                'amount' => 'Valor', 'spent_at' => 'Data', 'subcategory' => 'Subcategoria',
                                'entidade' => 'Empresa/Serviço', 'km' => 'Quilometragem', 'local' => 'Posto/Local', 'pessoas' => 'Nº Pessoas'
                            ];
                        @endphp

                        <div x-data="{ open: false }" class="relative pl-8 border-l-2 border-zinc-100 dark:border-zinc-800 last:border-l-transparent group">
                            <div class="absolute -left-[9px] top-0 size-4 rounded-full border-4 border-white dark:border-zinc-900 bg-brand-500 group-hover:scale-125 transition-all shadow-sm"></div>

                            <div class="flex flex-col gap-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <flux:avatar initials="{{ substr($log->user->name, 0, 2) }}" class="size-8 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700" />
                                        <div class="space-y-0.5">
                                            <p class="text-xs font-black dark:text-white uppercase tracking-tight">{{ $log->user->name }}</p>
                                            <p class="text-[11px] text-zinc-500 font-medium leading-relaxed">{{ $log->description }}</p>
                                        </div>
                                    </div>
                                    <button @click="open = !open" class="shrink-0 text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1.5 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-100 dark:border-zinc-700 hover:text-brand-500 transition-colors">
                                        <span x-text="open ? 'Recolher' : 'Detalhes'"></span>
                                    </button>
                                </div>

                                {{-- Meta-Informação Expansível --}}
                                <div x-show="open" x-collapse x-cloak class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                        @if(isset($props) && is_array($props))
                                            @foreach($props as $key => $val)
                                                @if($key === 'metadata' && is_array($val))
                                                    @foreach($val as $mKey => $mVal)
                                                        @if($mVal)
                                                            <div>
                                                                <span class="text-[8px] text-zinc-400 uppercase font-black block mb-0.5">{{ $fieldLabels[$mKey] ?? $mKey }}</span>
                                                                <span class="text-[10px] font-bold dark:text-zinc-200 uppercase truncate block">{{ $mVal }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @elseif(isset($fieldLabels[$key]) && $val)
                                                    <div>
                                                        <span class="text-[8px] text-zinc-400 uppercase font-black block mb-0.5">{{ $fieldLabels[$key] }}</span>
                                                        <span class="text-[10px] font-black text-brand-600 uppercase block">
                                                            {{ $key === 'amount' ? number_format($val, 2, ',', ' ') . ' €' : $val }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase italic">{{ $log->created_at->format('d M, H:i') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 italic text-zinc-400 uppercase text-[10px] tracking-widest">Auditoria vazia.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ESTILOS TÉCNICOS --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
    </style>

</div>
