<div class="space-y-10 pb-20">
    {{-- 1. HEADER DE DISPONIBILIDADE (ESTILO SaaS PREMIUM) --}}
    <div class="relative">
        {{-- Glow decorativo de fundo --}}
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700 shadow-brand-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="calendar-days" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Férias & Ausências</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">Recursos Humanos</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Planeamento de <span class="text-brand-600 font-bold uppercase tracking-tighter">Disponibilidade e Fluxo de Equipa</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="absence-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Registar Ausência
                    </flux:button>
                </flux:modal.trigger>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
            </div>
        </div>
    </div>

    {{-- 2. KPIs DE DISPONIBILIDADE (ANALYTICS OPERACIONAL) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Ausentes Hoje --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all {{ $absentTodayCount > 0 ? 'hover:border-orange-500/30' : 'hover:border-brand-500/30' }}">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 {{ $absentTodayCount > 0 ? 'bg-orange-50 dark:bg-orange-500/10 text-orange-600' : 'bg-zinc-50 dark:bg-zinc-800 text-zinc-500' }} rounded-2xl">
                    <flux:icon name="user-minus" variant="outline" class="size-6" />
                </div>
                @if($absentTodayCount > 0)
                    <span class="text-[9px] font-black text-orange-500 bg-orange-50 dark:bg-orange-500/10 px-2 py-1 rounded-lg uppercase animate-pulse">Alerta</span>
                @endif
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Membros Ausentes Hoje</p>
            <h3 class="text-4xl font-black {{ $absentTodayCount > 0 ? 'text-orange-500' : 'text-zinc-900 dark:text-white' }} tracking-tighter">
                {{ $absentTodayCount }}
                <span class="text-xs text-zinc-400 uppercase font-bold ml-2 tracking-widest italic">Colaboradores</span>
            </h3>
        </div>

        {{-- Pendentes (Black Glass) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400 shadow-inner">
                        <flux:icon name="bell" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">Ação Necessária</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Solicitações Pendentes</p>
                <h3 class="text-4xl font-black text-white tracking-tighter italic">
                    {{ $pendingApprovals }}
                </h3>
            </div>
        </div>

        {{-- Dias Totais no Mês --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-emerald-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-2xl text-zinc-500">
                    <flux:icon name="calendar" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Dias de Férias Alocados</p>
            <h3 class="text-4xl font-black dark:text-white tracking-tighter">{{ $totalDaysMonth }}</h3>
            <p class="mt-4 text-[9px] text-zinc-400 font-bold uppercase tracking-widest italic">Consolidado Mensal</p>
        </div>
    </div>

    {{-- 3. LEDGER DE AUSÊNCIAS (ESTILO SaaS LEDGER) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-900/30">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Mapa de Disponibilidade</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter text-zinc-800 dark:text-zinc-200">Histórico de Ausências & Férias</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm">{{ count($absences) }} Registos</flux:badge>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-widest">
                        <th class="p-6">Colaborador</th>
                        <th class="p-6 text-center">Tipo de Ausência</th>
                        <th class="p-6 text-center">Período / Datas</th>
                        <th class="p-6 text-center">Duração</th>
                        <th class="p-6 text-center">Estado de Validação</th>
                        <th class="p-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($absences as $abs)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row">
                            {{-- COLUNA COLABORADOR --}}
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-sm text-zinc-500 font-black text-xs uppercase">
                                        {{ substr($abs->employee->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $abs->employee->name }}</span>
                                </div>
                            </td>

                            {{-- COLUNA TIPO --}}
                            <td class="p-6 text-center">
                                <span class="inline-flex px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-black uppercase text-[8px] tracking-widest rounded-xl border border-zinc-200 dark:border-zinc-700">
                                    {{ $abs->type_text }}
                                </span>
                            </td>

                            {{-- COLUNA PERÍODO --}}
                            <td class="p-6 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold dark:text-zinc-300 uppercase tracking-tighter">
                                        {{ $abs->start_date->format('d M') }} — {{ $abs->end_date->format('d M') }}
                                    </span>
                                    <span class="text-[9px] text-zinc-400 font-bold uppercase mt-0.5">{{ $abs->start_date->format('Y') }}</span>
                                </div>
                            </td>

                            {{-- COLUNA DIAS --}}
                            <td class="p-6 text-center">
                                <div class="inline-flex items-center justify-center size-9 rounded-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 shadow-inner">
                                    <span class="text-xs font-black dark:text-white">{{ $abs->business_days }}d</span>
                                </div>
                            </td>

                            {{-- COLUNA ESTADO --}}
                            <td class="p-6 text-center">
                                @if($abs->status === 'aprovado')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-black uppercase text-[8px] tracking-widest rounded-xl border border-emerald-200 dark:border-emerald-800">
                                        <div class="size-1 rounded-full bg-emerald-500"></div>
                                        Validado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 font-black uppercase text-[8px] tracking-widest rounded-xl border border-amber-200 dark:border-amber-800 animate-pulse">
                                        <div class="size-1 rounded-full bg-amber-500"></div>
                                        Pendente
                                    </span>
                                @endif
                            </td>

                            {{-- AÇÕES DISCRETAS --}}
                            <td class="p-6 text-right pr-8">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                    @if($abs->status === 'pendente')
                                        <flux:button wire:click="approve({{ $abs->id }})" variant="ghost" icon="check" size="sm" color="emerald" class="rounded-lg" title="Aprovar Pedido" />
                                    @endif
                                    <flux:button wire:click="delete({{ $abs->id }})" wire:confirm="Eliminar registo de ausência?" variant="ghost" icon="trash" size="sm" color="red" class="rounded-lg" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-24 text-center">
                                <div class="flex flex-col items-center justify-center gap-4">
                                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                                        <flux:icon name="calendar-days" class="size-12 text-zinc-200 dark:text-zinc-700" />
                                    </div>
                                    <div class="space-y-1 text-center">
                                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Sem Histórico</p>
                                        <p class="text-zinc-400 text-xs italic font-medium">A agenda da equipa está limpa de ausências.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. MODAL: REGISTAR AUSÊNCIA (DESIGN SaaS PRO) --}}
    <flux:modal name="absence-modal" position="center" class="md:w-[550px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-900 dark:bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="calendar-days" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Registar Ausência</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium italic">Agenda o período de indisponibilidade do colaborador.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- SECÇÃO: COLABORADOR E TIPO --}}
                <div class="space-y-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Membro da Equipa</flux:label>
                        <flux:select wire:model="employee_id" class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" placeholder="Selecionar Colaborador...">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">👤 {{ $emp->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Natureza da Ausência</flux:label>
                        <flux:select wire:model="type" class="font-black uppercase text-xs !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner">
                            <option value="ferias">🌴 Período de Férias</option>
                            <option value="doenca">🏥 Baixa Médica / Saúde</option>
                            <option value="falta_justificada">📄 Falta Justificada</option>
                            <option value="pessoal">🏠 Assuntos Pessoais</option>
                        </flux:select>
                    </div>
                </div>

                {{-- SECÇÃO: PERÍODO CRONOLÓGICO (PAINEL DE DESTAQUE) --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2 px-1">
                        <flux:icon name="clock" class="size-3 text-brand-500" />
                        <p class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">Definição de Datas</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Data de Início</flux:label>
                            <flux:input wire:model="start_date" type="date" class="font-bold !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm" />
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Data de Conclusão</flux:label>
                            <flux:input wire:model="end_date" type="date" class="font-bold !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm" />
                        </div>
                    </div>
                </div>

                {{-- NOTAS ADICIONAIS --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Observações Internas</flux:label>
                    <flux:textarea
                        wire:model="notes"
                        rows="2"
                        placeholder="Opcional: Motivo ou detalhes do pedido..."
                        class="rounded-2xl shadow-sm border-none !bg-zinc-50 dark:!bg-zinc-900 text-sm p-4"
                    />
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    Confirmar Registo
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Disponibilidade Operacional
        </p>
    </footer>
</div>
