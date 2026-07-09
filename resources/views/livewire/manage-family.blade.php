<div class="space-y-10 pb-24">
    @php
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        // 1. IDENTIFICAR O TEU COFRE PRIVADO
        $myPersonalWs = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();

        // 2. IDENTIFICAR A GESTÃO PARTILHADA (Onde és convidado)
        $sharedWs = $user->workspaces()->where('owner_id', '!=', $user->id)->where('type', '!=', 'business')->first();
        $sharedMembers = $sharedWs ? $sharedWs->users()->withPivot('role')->get() : collect();

        // 3. LOGICA DE ESTADO
        $isAtPersonal = $myPersonalWs && $workspace->id === $myPersonalWs->id;
        $isAtShared = $sharedWs && $workspace->id === $sharedWs->id;

        // 4. DEFINIR O TIPO DE CONTEXTO
        if ($isAtPersonal) { $contextType = 'private'; }
        elseif ($isAtShared) { $contextType = 'external'; }
        else { $contextType = 'shared_by_me'; }

        $ownerModel = \App\Models\User::find($workspace->owner_id);
    @endphp

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

            @if(!$isAtPersonal)
                <a href="{{ route('workspace.switch.fast', $myPersonalWs->id) }}"
                   class="group flex items-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl transition-all active:scale-95">
                    <flux:icon name="arrow-uturn-left" variant="micro" class="size-4" />
                    Voltar ao Meu Cofre
                </a>
            @endif
        </div>
    </div>


























<div class="space-y-10 pb-24">
    @php
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        // 1. IDENTIFICAR O TEU COFRE PRIVADO
        $myPersonalWs = $user->workspaces()->where('type', 'personal')->where('owner_id', $user->id)->first();

        // 2. IDENTIFICAR A GESTÃO PARTILHADA (Onde és convidado)
        $sharedWs = $user->workspaces()->where('owner_id', '!=', $user->id)->where('type', '!=', 'business')->first();
        $sharedMembers = $sharedWs ? $sharedWs->users()->withPivot('role')->get() : collect();

        // 3. LOGICA DE ESTADO
        $isAtPersonal = $myPersonalWs && $workspace->id === $myPersonalWs->id;
        $isAtShared = $sharedWs && $workspace->id === $sharedWs->id;

        // 4. DEFINIR O TIPO DE CONTEXTO
        if ($isAtPersonal) { $contextType = 'private'; }
        elseif ($isAtShared) { $contextType = 'external'; }
        else { $contextType = 'shared_by_me'; }

        $ownerModel = \App\Models\User::find($workspace->owner_id);
    @endphp




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

            {{-- INFO COM ALPINE.JS --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button"
                    class="flex items-center justify-center size-7 rounded-full bg-brand-600 text-white shadow-lg shadow-brand-500/40 ring-4 ring-brand-500/20 hover:scale-110 hover:bg-brand-500 transition-all group">
                    <flux:icon name="information-circle" variant="solid" class="size-5" />
                </button>
                <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute left-0 mt-3 w-80 p-6 bg-zinc-950 text-white border border-zinc-800 shadow-2xl rounded-[2rem] z-50">
                    <p class="text-[11px] text-zinc-400 leading-relaxed">
                        • Até <span class="text-white font-bold">5 membros</span> no mesmo grupo.<br>
                        • <span class="text-white font-bold">Transparência total:</span> todos vêem as despesas comuns.
                    </p>
                </div>
            </div>
        </div>
        @if($isAtShared) <flux:badge variant="neutral" size="sm" class="text-[7px] font-black uppercase">Ativo Agora</flux:badge> @endif
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-8 shadow-sm min-h-[400px] flex flex-col items-center">
        @if($sharedWs)
            {{-- AVATAR DO PROPRIETÁRIO --}}
            <div class="flex flex-col items-center text-center space-y-4 w-full">
                <div class="size-32 rounded-[2.5rem] overflow-hidden border-4 border-white dark:border-zinc-800 shadow-2xl bg-zinc-100 flex items-center justify-center">
    @if($sharedWs->logo_path)
        {{-- MUDANÇA AQUI: Usamos logo_url em vez de asset(logo_path) --}}
        <img src="{{ $sharedWs->logo_url }}?t={{ time() }}" class="size-full object-cover">
    @else
        <span class="text-3xl font-black text-brand-600 uppercase italic">{{ substr($sharedWs->name, 0, 2) }}</span>
    @endif
</div>

                <div>
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Proprietário</p>
                    <h3 class="text-xl font-black dark:text-white uppercase italic tracking-tighter">{{ $sharedWs->name }}</h3>
                </div>

                {{-- --- LISTA DE PESSOAS (MEMBROS) --- --}}
                <div class="w-full mt-6 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Pessoas Autorizadas</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full">
                        @foreach($sharedMembers as $sm)
                            <div class="flex items-center gap-3 p-3 bg-zinc-50/50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-100 dark:border-zinc-800/50">
                                <flux:avatar initials="{{ substr($sm->name, 0, 2) }}" class="size-8 shadow-sm border border-white dark:border-zinc-800" />
                                <div class="flex flex-col text-left min-w-0">
                                    <span class="text-[10px] font-black dark:text-white uppercase truncate">{{ $sm->name }}</span>
                                    <span class="text-[8px] font-bold text-brand-600 uppercase tracking-widest">{{ $sm->pivot->role ?? 'Membro' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if(!$isAtShared)
                    <div class="w-full mt-6">
                        <flux:button href="{{ route('workspace.switch.fast', $sharedWs->id) }}" variant="primary" class="w-full rounded-2xl bg-zinc-950 dark:bg-zinc-800 font-black uppercase text-[10px] h-12 shadow-lg">
                            Entrar nesta Gestão
                        </flux:button>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center">
                <div class="size-20 mx-auto bg-zinc-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mb-4">
                    <flux:icon name="users" class="size-8 text-zinc-300" />
                </div>
                <p class="text-xs font-black uppercase text-zinc-400 tracking-widest italic">Sem grupos partilhados ativos</p>
            </div>
        @endif
    </div>
</div>

        {{-- COLUNA B: O MEU COFRE PRIVADO --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4 text-left w-full">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600">
                        <flux:icon name="lock-closed" variant="outline" class="size-4" />
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-900 dark:text-white leading-none">O Meu Cofre Privado</h2>

                    {{-- INFO ALPINE --}}
                   <div x-data="{ open: false }" class="relative">
    <button @click="open = !open" type="button"
        class="flex items-center justify-center size-7 rounded-full bg-emerald-600 text-white shadow-lg shadow-emerald-500/40 ring-4 ring-emerald-500/20 hover:scale-110 hover:bg-emerald-500 transition-all group">
        <flux:icon name="information-circle" variant="solid" class="size-5 group-hover:rotate-12 transition-transform" />
    </button>
                        <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute left-0 mt-2 w-80 p-6 bg-zinc-950 text-white border border-zinc-800 shadow-2xl rounded-[2rem] z-50">
                            <div class="space-y-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <flux:icon name="shield-check" class="size-4 text-emerald-400" />
                                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Privacidade Total</span>
                                </div>




                <div>

                    <p class="text-lg font-black uppercase italic tracking-tighter">Cofre Privado</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="size-6 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0 mt-0.5"><flux:icon name="user" class="size-3.5 text-emerald-400" /></div>
                    <p class="text-[11px] text-zinc-400 font-medium leading-relaxed">Acesso <span class="text-white font-black">exclusivo e único</span>. Só tu tens a chave deste espaço.</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="size-6 rounded-lg bg-red-500/20 flex items-center justify-center shrink-0 mt-0.5"><flux:icon name="eye-slash" class="size-3.5 text-red-400" /></div>
                    <p class="text-[11px] text-zinc-400 font-medium leading-relaxed">Estes dados são <span class="text-white font-black">invisíveis</span> para membros de outros grupos.</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="size-6 rounded-lg bg-purple-500/20 flex items-center justify-center shrink-0 mt-0.5"><flux:icon name="sparkles" class="size-3.5 text-purple-400" /></div>
                    <p class="text-[11px] text-zinc-400 font-medium leading-relaxed">Ideal para gerir <span class="text-white font-black">gastos pessoais</span>, hobbies ou surpresas.</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="size-6 rounded-lg bg-zinc-500/20 flex items-center justify-center shrink-0 mt-0.5"><flux:icon name="no-symbol" class="size-3.5 text-zinc-400" /></div>
                    <p class="text-[11px] text-zinc-400 font-medium leading-relaxed">Não permite convites. É o teu <span class="text-white font-black">bunker financeiro</span> pessoal.</p>
                </div>
            </div>










                        </div>
                    </div>
                </div>
                @if($isAtPersonal) <flux:badge variant="success" size="sm" class="text-[7px] font-black uppercase">Ativo Agora</flux:badge> @endif
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-8 shadow-sm space-y-8 min-h-[400px] flex flex-col justify-center">
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <div class="size-32 rounded-[2.5rem] overflow-hidden border-4 border-emerald-50 dark:border-zinc-800 shadow-xl bg-zinc-50 flex items-center justify-center">
                            <img src="{{ $myPersonalWs->logo_url }}?v={{ time() }}" class="size-full object-cover">
                        </div>
                        <label class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-all cursor-pointer rounded-[2.5rem] backdrop-blur-[2px]">
                            <input type="file" wire:model="personalPhoto" class="hidden" accept="image/*">
                            <flux:icon name="camera" class="size-8 text-white" />
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-widest ml-1">Nome da Minha Gestão</flux:label>
                    <div class="flex gap-2">
                        <flux:input wire:model="personalWorkspaceName" class="flex-1 h-12 !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-xl font-bold shadow-inner" />
                        <flux:button wire:click="updatePersonalName" variant="primary" class="rounded-xl bg-emerald-600 shadow-lg shadow-emerald-500/20"><flux:icon name="check" variant="micro" /></flux:button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="$set('personalWorkspaceName', 'Cofre de {{ explode(' ', auth()->user()->name)[0] }}')" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">✨ Sugerir Individual</button>
                        <button wire:click="$set('personalWorkspaceName', 'Gestão Familiar {{ collect(explode(' ', auth()->user()->name))->last() }}')" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-brand-600 hover:text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">✨ Sugerir Família</button>
                    </div>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800 text-center">
                    <p class="text-[10px] text-zinc-500 italic leading-relaxed">Só tu tens as chaves de acesso a este espaço. Estes dados não são partilhados.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TABELA DE AUDITORIA (SÓ APARECE SE NÃO ESTIVER NO COFRE) --}}
    @if(!$isAtPersonal)
        <div class="space-y-6 pt-10">
            <div class="flex items-center gap-3 px-4">
                <div class="p-2 bg-brand-600 rounded-lg text-white"><flux:icon name="presentation-chart-line" variant="mini" class="size-4" /></div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Auditoria de Rendimento</h2>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-zinc-50/50 dark:bg-zinc-950/30 text-[9px] font-black uppercase text-zinc-400 tracking-widest border-b dark:border-zinc-800">
                                <th class="p-6">Membro</th>
                                <th class="p-6 text-right">Rendimento</th>
                                <th class="p-6 text-right">Despesas</th>
                                <th class="p-6 text-right">Net Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($memberStats as $stat)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all">
                                    <td class="p-6">
                                        <div class="flex items-center gap-3">
                                            <flux:avatar initials="{{ substr($stat->name, 0, 2) }}" class="size-10 shadow-sm" />
                                            <span class="text-sm font-black dark:text-white uppercase">{{ $stat->name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-6 text-right font-black text-emerald-600">+{{ number_format($stat->total_incomes, 2, ',', ' ') }}€</td>
                                    <td class="p-6 text-right font-black text-red-500">-{{ number_format($stat->total_expenses, 2, ',', ' ') }}€</td>
                                    <td class="p-6 text-right font-black {{ $stat->net_balance >= 0 ? 'text-emerald-500' : 'text-red-500' }} italic">
                                        {{ number_format($stat->net_balance, 2, ',', ' ') }}€
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

















    {{-- 4. ZONA DE AUDITORIA (TABELA) --}}
    @if(!$isAtPersonal)
    <div class="pt-10 space-y-8">
        <div class="flex items-center gap-3 px-4">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="shield-check" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Auditoria de Rendimento</h2>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-zinc-50/50 dark:bg-zinc-950/20 border-b dark:border-zinc-800">
                    <tr class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                        <th class="p-6">Membro</th>
                        <th class="p-6 text-right">Rendimento</th>
                        <th class="p-6 text-right">Despesas</th>
                        <th class="p-6 text-right">Balanço</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($memberStats as $stat)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-all">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <flux:avatar initials="{{ substr($stat->name, 0, 2) }}" class="size-10 rounded-xl" />
                                <span class="text-sm font-black dark:text-white uppercase">{{ $stat->name }}</span>
                            </div>
                        </td>
                        <td class="p-6 text-right font-bold text-emerald-600">+{{ number_format($stat->total_incomes, 2) }}€</td>
                        <td class="p-6 text-right font-bold text-red-500">-{{ number_format($stat->total_expenses, 2) }}€</td>
                        <td class="p-6 text-right font-black {{ $stat->net_balance >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ number_format($stat->net_balance, 2) }}€
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif


{{-- SEPARADOR DE SECÇÃO --}}
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


    {{-- LINHA 2: CÓDIGO + ENTRAR --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- PARTILHAR CÓDIGO --}}
        <div class="glass-card p-6 sm:p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem]
                    border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">

            <div class="absolute -left-10 -bottom-10 size-48 bg-brand-500/5 blur-[80px] rounded-full
                        group-hover:bg-brand-500/10 transition-all"></div>

            <div class="relative z-10 space-y-6">

    <div class="flex items-center gap-3 flex-wrap">
        <div class="p-2 bg-brand-500/10 rounded-xl">
            <flux:icon name="share" class="size-5 text-brand-600" />
        </div>

        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Código</p>
            <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">
                Convidar Membros
            </p>
        </div>
    </div>

    <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
        Partilha este código com quem queres adicionar ao teu grupo financeiro.
    </p>

    <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-950
                border border-zinc-200 dark:border-zinc-800 rounded-2xl px-6 py-4">

        <span class="text-3xl font-mono font-black text-brand-600 dark:text-brand-400 tracking-[0.3em] uppercase">
            {{ $inviteCode }}
        </span>

        <div class="flex items-center gap-2">
            {{-- BOTÃO COPIAR --}}
            <button
                x-data="{ copied: false }"
                @click="navigator.clipboard.writeText('{{ $inviteCode }}'); copied = true; setTimeout(() => copied = false, 2000)"
                class="p-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all group/copy"
                title="Copiar código"
            >
                <flux:icon x-show="!copied" name="clipboard" variant="micro" class="size-4 text-zinc-400 group-hover/copy:text-brand-600" />
                <flux:icon x-show="copied" x-cloak name="check" variant="micro" class="size-4 text-emerald-500" />
            </button>

            {{-- BOTÃO REGENERAR --}}
            <flux:button wire:click="generateInviteCode" size="sm" variant="ghost" icon="arrow-path"
                class="text-zinc-400 hover:text-brand-600 transition-colors"
                title="Gerar novo código" />
        </div>
    </div>

    <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest italic">
        O código não expira. Podes regenerá-lo a qualquer momento.
    </p>

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
                <input
                    wire:model="inviteCodeInput"
                    placeholder="EX: AB12CD34"
                    class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 font-mono font-black uppercase tracking-widest text-sm dark:text-white placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all shadow-inner"
                />
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
    {{-- 3. GESTÃO DE ACESSOS (TABELA DE MEMBROS) --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="shield-check" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Utilizadores com Privilégios</h2>
            </div>
            <flux:badge variant="neutral" class="font-black text-[9px] uppercase bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1">
                {{ count($members) }} Membros Ativos
            </flux:badge>
        </div>

        <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/50 dark:bg-zinc-950/20 text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em] border-b dark:border-zinc-800">
                            <th class="p-6">Utilizador</th>
                            <th class="p-6 text-center">Nível de Acesso</th>
                            <th class="p-6 text-right">Controlo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($members as $member)
                            <tr class="group hover:bg-zinc-50/50 dark:hover:bg-brand-500/5 transition-all">
                                {{-- Identificação --}}
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="relative shrink-0">
                                            <flux:avatar initials="{{ substr($member->name, 0, 2) }}" class="size-12 shadow-sm border-2 border-white dark:border-zinc-800" />
                                            @if($member->id === auth()->id())
                                                <div class="absolute -bottom-1 -right-1 size-4 bg-brand-500 rounded-full border-2 border-white dark:border-zinc-900"></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                                {{ $member->name }}
                                                @if($member->id === auth()->id())
                                                    <span class="text-[8px] bg-brand-500 text-white px-2 py-0.5 rounded-full font-black tracking-widest italic uppercase">Tu</span>
                                                @endif
                                            </p>
                                            <p class="text-[11px] text-zinc-500 font-medium italic">{{ $member->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Funções / Roles --}}
                                <td class="p-6">
                                    <div class="flex justify-center">
                                        @if($iAmAdmin && $member->id !== auth()->id())
                                            <div class="w-48">
                                                <flux:select
                                                    wire:change="updateRole({{ $member->id }}, $event.target.value)"
                                                    class="font-black uppercase text-[10px] tracking-tighter !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-xl h-10 shadow-inner"
                                                >
                                                    <option value="admin" {{ $member->pivot->role === 'admin' ? 'selected' : '' }}>👑 Administrador</option>
                                                    <option value="editor" {{ $member->pivot->role === 'editor' ? 'selected' : '' }}>✍️ Editor Master</option>
                                                    <option value="viewer" {{ $member->pivot->role === 'viewer' ? 'selected' : '' }}>👁️ Analista (Read)</option>
                                                </flux:select>
                                            </div>
                                        @else
                                            @php
                                                $roleStyle = match($member->pivot->role) {
                                                    'admin'  => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                                    'editor' => 'bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-500/20',
                                                    default  => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-[0.2em] border {{ $roleStyle }}">
                                                {{ $member->pivot->role === 'admin' ? '👑 Admin master' : ($member->pivot->role === 'editor' ? '✍️ Editor master' : '👁️ Analista') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>



























                                {{-- Acções --}}
                                <td class="p-6 text-right">
                                    @if($iAmAdmin && $member->id !== auth()->id())
                                        <flux:button
                                            wire:click="removeMember({{ $member->id }})"
                                            wire:confirm="Expulsar este membro definitivamente do workspace?"
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            color="red"
                                            class="opacity-0 group-hover:opacity-100 transition-all rounded-xl hover:bg-red-50 dark:hover:bg-red-950/20"
                                        />
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   {{-- 4. MONITORIZAÇÃO DE PERFORMANCE (PREMIER FINANCIAL LEDGER) --}}
    <div class="space-y-6 pt-12">

        {{-- CABEÇALHO DA SECÇÃO --}}
        <div class="flex items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-brand-600 rounded-lg shadow-lg shadow-brand-500/20 text-white">
                    <flux:icon name="presentation-chart-line" variant="mini" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Auditoria de Rendimento <span class="text-zinc-300 dark:text-zinc-600 mx-2">|</span> <span class="text-brand-600 italic">{{ now()->translatedFormat('F Y') }}</span></h2>
            </div>
        </div>

        <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">

            {{-- HEADER DA TABELA (DESKTOP) --}}
            <div class="hidden lg:grid grid-cols-12 gap-4 px-10 py-5 bg-zinc-50/50 dark:bg-zinc-950/30 border-b border-zinc-100 dark:border-zinc-800">
                <div class="col-span-4 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Membro do Grupo</div>
                <div class="col-span-3 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 text-right">Rendimento Bruto</div>
                <div class="col-span-3 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 text-right">Total Despesas</div>
                <div class="col-span-2 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 text-right">Net Balance</div>
            </div>

            {{-- LINHAS DE DADOS --}}
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach($memberStats as $user)
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 px-6 lg:px-10 py-6 items-center hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group">

                        {{-- 1. IDENTIDADE --}}
                        <div class="col-span-4 flex items-center gap-5">
                            <div class="relative shrink-0">
                                <div class="size-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center font-black text-xs shadow-xl border border-zinc-800 group-hover:scale-110 transition-transform">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div class="absolute -top-2 -left-2 size-6 bg-brand-600 text-white rounded-lg flex items-center justify-center text-[8px] font-black border-2 border-white dark:border-zinc-900 shadow-md">
                                    {{ $user->level }}
                                </div>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-black dark:text-white uppercase tracking-tight truncate">{{ $user->name }}</h3>
                                <p class="text-[8px] font-black text-brand-600 uppercase tracking-widest mt-0.5">{{ number_format($user->xp) }} XP ACUMULADO</p>
                            </div>
                        </div>

                        {{-- 2. RENDIMENTO BRUTO (ALINHADO À DIREITA) --}}
                        <div class="col-span-3 flex lg:block justify-between items-center">
                            <span class="lg:hidden text-[9px] font-black uppercase text-zinc-400">Rendimento:</span>
                            <p class="text-lg font-black text-emerald-600 tracking-tighter text-right">
                                +{{ number_format($user->total_incomes, 2, ',', ' ') }}€
                            </p>
                        </div>

                        {{-- 3. TOTAL DESPESAS (ALINHADO À DIREITA) --}}
                        <div class="col-span-3 flex lg:block justify-between items-center">
                            <span class="lg:hidden text-[9px] font-black uppercase text-zinc-400">Despesas:</span>
                            <p class="text-lg font-black text-red-500 tracking-tighter text-right">
                                -{{ number_format($user->total_expenses, 2, ',', ' ') }}€
                            </p>
                        </div>

                        {{-- 4. NET BALANCE (DETALHE ESTÉTICO FINAL) --}}
                        <div class="col-span-2 flex lg:block justify-between items-center pl-0 lg:pl-6">
                            <span class="lg:hidden text-[9px] font-black uppercase text-zinc-400">Balanço:</span>
                            <div class="lg:bg-zinc-50 lg:dark:bg-zinc-800/50 lg:p-3 rounded-2xl lg:border lg:border-zinc-100 lg:dark:border-zinc-800 text-right group-hover:bg-zinc-900 dark:group-hover:bg-white transition-colors duration-500">
                                <p class="text-lg font-black {{ $user->net_balance >= 0 ? 'text-emerald-500 group-hover:text-white dark:group-hover:text-zinc-900' : 'text-red-500' }} tracking-tighter leading-none italic">
                                    {{ $user->net_balance >= 0 ? '+' : '' }}{{ number_format($user->net_balance, 2, ',', ' ') }}€
                                </p>
                            </div>
                        </div>

                    </div>



                @endforeach
            </div>
        </div>
    </div>
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
                                <span class="text-xl font-black {{ $medalColor }} italic italic w-8 text-center">{{ $index + 1 }}º</span>
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

    {{-- Mesada Digital & Objetivos Familiares --}}
    @if($iAmAdmin)
        <div class="grid lg:grid-cols-2 gap-6 pt-8">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6">
                <h3 class="font-black dark:text-white uppercase tracking-widest text-sm mb-4">Permissões por Categoria</h3>
                <div class="space-y-3">
                    <flux:select wire:model="permUserId" label="Membro">
                        <option value="">— Selecionar —</option>
                        @foreach($members as $m)
                            @if($m->id !== auth()->id())
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endif
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="permCategoryId" label="Categoria visível">
                        <option value="">— Selecionar —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:button wire:click="setCategoryPermission" variant="primary" class="rounded-xl font-black">Dar Acesso</flux:button>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6">
                <h3 class="font-black dark:text-white uppercase tracking-widest text-sm mb-4">Mesada Digital</h3>
                <div class="space-y-3">
                    <flux:select wire:model="allowanceUserId" label="Membro">
                        <option value="">— Selecionar —</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="allowanceLimit" type="number" label="Limite Mensal (€)" />
                    <flux:button wire:click="setAllowance" variant="primary" class="rounded-xl font-black">Configurar Mesada</flux:button>
                </div>
                @if($allowances->isNotEmpty())
                    <div class="mt-4 space-y-2">
                        @foreach($allowances as $a)
                            <div class="flex justify-between text-sm p-3 bg-zinc-50 dark:bg-zinc-800 rounded-xl">
                                <span class="font-bold dark:text-white">{{ $a->user->name }}</span>
                                <span class="font-black text-emerald-600">{{ number_format($a->allowance_limit, 0, ',', '.') }}€/mês</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6">
                <h3 class="font-black dark:text-white uppercase tracking-widest text-sm mb-4">Objetivos Familiares</h3>
                @forelse($familyGoals as $goal)
                    @php $pct = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0; @endphp
                    <div class="mb-3 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl">
                        <div class="flex justify-between">
                            <span class="font-bold dark:text-white">{{ $goal->name }}</span>
                            <span class="text-sm font-black text-brand-600">{{ $pct }}%</span>
                        </div>
                        <div class="h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full mt-2 overflow-hidden">
                            <div class="h-full bg-brand-500 rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                        <p class="text-[10px] text-zinc-400 mt-1">Faltam {{ number_format(max(0, $goal->target_amount - $goal->current_amount), 0, ',', '.') }}€</p>
                    </div>
                @empty
                    <p class="text-zinc-400 text-sm">Cria metas em <a href="{{ route('hub.goals') }}" class="text-brand-500">Metas</a></p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ESTILOS TÉCNICOS --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
    </style>

</div> {{-- FECHO DA DIV RAIZ PRINCIPAL --}}
