<div class="min-h-[85vh] flex flex-col items-center justify-center py-12 px-6">

    {{-- CABEÇALHO --}}
    <div class="text-center mb-16 animate-in fade-in slide-in-from-bottom-6 duration-1000">
        <div class="inline-flex p-4 bg-brand-500/10 rounded-[2rem] mb-6 shadow-inner">
            <flux:icon name="building-office-2" class="size-10 text-brand-600" />
        </div>
        <h1 class="text-5xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
            Hub Business
        </h1>
        <p class="text-zinc-500 font-bold mt-4 uppercase tracking-[0.3em] text-[11px] opacity-80">
            Selecione o seu portal de entrada
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 w-full max-w-5xl">

        {{-- OPÇÃO 1: CEO / FUNDADOR --}}
        <div class="group relative flex flex-col h-full">
            <div class="absolute -inset-1 bg-gradient-to-br from-brand-600 to-indigo-600 rounded-[3rem] blur opacity-10 group-hover:opacity-30 transition duration-700"></div>

            <div class="relative flex-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-10 flex flex-col shadow-sm group-hover:border-brand-500/50 transition-all">
                <div class="flex items-center gap-4 mb-8">
                    <div class="size-14 rounded-2xl bg-brand-500/10 flex items-center justify-center text-brand-600">
                        <flux:icon name="command-line" class="size-7" />
                    </div>
                    <div class="text-left">
                        <h3 class="text-2xl font-black dark:text-white uppercase italic tracking-tighter leading-none">Sou CEO</h3>
                        <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest mt-1">Dono do Negócio</p>
                    </div>
                </div>

                <p class="text-sm text-zinc-500 text-left leading-relaxed mb-8">
                    Para empreendedores que precisam de gerir a sua própria estrutura, finanças e capital humano.
                </p>

                {{-- LISTA DE ACESSOS --}}
                <ul class="space-y-4 mb-10 flex-1 text-left">
                    <li class="flex items-start gap-3 text-xs font-bold text-zinc-600 dark:text-zinc-300">
                        <flux:icon name="check-circle" variant="solid" class="size-4 text-brand-500 shrink-0" />
                        Controlo Total de Faturação & PnL
                    </li>
                    <li class="flex items-start gap-3 text-xs font-bold text-zinc-600 dark:text-zinc-300">
                        <flux:icon name="check-circle" variant="solid" class="size-4 text-brand-500 shrink-0" />
                        Gestão de Equipa, Salários & Férias
                    </li>
                    <li class="flex items-start gap-3 text-xs font-bold text-zinc-600 dark:text-zinc-300">
                        <flux:icon name="check-circle" variant="solid" class="size-4 text-brand-500 shrink-0" />
                        Consultoria IA Estratégica de Negócio
                    </li>
                </ul>

                <flux:button wire:click="enterAsOwner" variant="primary" class="w-full h-16 rounded-2xl font-black uppercase tracking-widest bg-brand-600 hover:bg-brand-700 border-none shadow-lg shadow-brand-500/20 text-sm">
                    Entrar como Administrador
                </flux:button>
            </div>
        </div>

        {{-- OPÇÃO 2: COLABORADOR / EQUIPA --}}
        <div class="group relative flex flex-col h-full">
            <div class="absolute -inset-1 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-[3rem] blur opacity-10 group-hover:opacity-30 transition duration-700"></div>

            <div class="relative flex-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-10 flex flex-col shadow-sm group-hover:border-emerald-500/50 transition-all">
                <div class="flex items-center gap-4 mb-8 text-left">
                    <div class="size-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                        <flux:icon name="users" class="size-7" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-black dark:text-white uppercase italic tracking-tighter leading-none text-left">Sou Colaborador</h3>
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mt-1">Membro de Equipa</p>
                    </div>
                </div>

                <p class="text-sm text-zinc-500 text-left leading-relaxed mb-8">
                    Para profissionais que trabalham numa empresa e possuem um código de acesso oficial.
                </p>

                {{-- LISTA DE ACESSOS --}}
                <ul class="space-y-4 mb-10 flex-1 text-left">
                    <li class="flex items-start gap-3 text-xs font-bold text-zinc-600 dark:text-zinc-300">
                        <flux:icon name="check-circle" variant="solid" class="size-4 text-emerald-500 shrink-0" />
                        Terminal Operacional & Checklist de Tarefas
                    </li>
                    <li class="flex items-start gap-3 text-xs font-bold text-zinc-600 dark:text-zinc-300">
                        <flux:icon name="check-circle" variant="solid" class="size-4 text-emerald-500 shrink-0" />
                        Registo de Ponto & Turnos de Trabalho
                    </li>
                    <li class="flex items-start gap-3 text-xs font-bold text-zinc-600 dark:text-zinc-300">
                        <flux:icon name="check-circle" variant="solid" class="size-4 text-emerald-500 shrink-0" />
                        Messenger Interno para Equipa
                    </li>
                </ul>

                <div class="space-y-4 w-full">
                    <flux:input
                        wire:model="accessCode"
                        placeholder="INSERIR TOKEN: EX-A1B2C3"
                        class="text-center font-black uppercase tracking-[0.2em] h-14 rounded-2xl border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 shadow-inner"
                    />

                    <flux:button wire:click="joinAsCollaborator" variant="primary" class="w-full h-16 rounded-2xl font-black uppercase tracking-widest bg-emerald-600 hover:bg-emerald-700 border-none shadow-lg shadow-emerald-500/20 text-sm">
                        Validar & Aceder
                    </flux:button>
                </div>
            </div>
        </div>

    </div>

    {{-- RODAPÉ --}}
    <div class="mt-16 flex flex-col items-center">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em] opacity-50 mb-4">
            Finance Pro · Protocolo de Segurança Empresarial
        </p>
        <flux:button href="{{ route('dashboard') }}" variant="ghost" size="sm" class="text-[10px] font-black uppercase tracking-widest">
            Voltar ao Painel Pessoal
        </flux:button>
    </div>
</div>
