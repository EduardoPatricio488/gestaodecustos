<div class="space-y-10 pb-20 text-left">

    {{-- 1. HEADER DINÂMICO --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10 text-left">
            <div class="flex items-center gap-6 text-left">
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="calendar-days" class="w-10 h-10 text-brand-600" />
                </div>
                <div class="text-left">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                        {{ $isManager ? 'Gestão de Ausências' : 'Minhas Férias' }}
                    </h1>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">
                        {{ $isManager ? 'Controlo de disponibilidade de equipa' : 'Consulta e planeamento de descanso' }}
                    </p>
                </div>
            </div>

            <flux:modal.trigger name="absence-modal">
                <flux:button variant="primary" icon="plus" class="rounded-2xl px-8 h-14 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20 bg-brand-600 border-none">
                    {{ $isManager ? 'Registar Ausência' : 'Solicitar Ausência' }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    {{-- 2. KPIs DINÂMICOS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
        @if($isManager)
            {{-- VISTA CEO --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Ausentes Hoje</p>
                <h3 class="text-4xl font-black {{ $absentTodayCount > 0 ? 'text-orange-500' : 'dark:text-white' }}">{{ $absentTodayCount }}</h3>
            </div>
            <div class="bg-zinc-950 p-8 rounded-[2.5rem] border border-zinc-800">
                <p class="text-[10px] font-black text-brand-400 uppercase tracking-widest mb-1">Pedidos Pendentes</p>
                <h3 class="text-4xl font-black text-white italic leading-none">{{ $pendingApprovals }}</h3>
            </div>
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Dias Alocados (Mês)</p>
                <h3 class="text-4xl font-black dark:text-white">{{ $totalDaysMonth }}</h3>
            </div>
        @else
            {{-- VISTA COLABORADOR --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1 leading-none">Dias Gozados em {{ now()->year }}</p>
                <h3 class="text-4xl font-black text-emerald-600">{{ $usedDays }} <span class="text-xs uppercase text-zinc-400 font-bold">Dias</span></h3>
            </div>
            <div class="bg-zinc-950 p-8 rounded-[2.5rem] border border-zinc-800">
                <p class="text-[10px] font-black text-brand-400 uppercase tracking-widest mb-1 leading-none">Em Aprovação</p>
                <h3 class="text-4xl font-black text-white italic leading-none">{{ $pendingCount }}</h3>
            </div>
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1 leading-none">Períodos Validados</p>
                <h3 class="text-4xl font-black dark:text-white leading-none">{{ $approvedCount }}</h3>
            </div>
        @endif
    </div>

    {{-- 3. TABELA DE HISTÓRICO --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] overflow-hidden shadow-sm text-left">
        <div class="p-8 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20 text-left">
            <h2 class="text-xs font-black uppercase tracking-widest text-zinc-500 italic">
                {{ $isManager ? 'Mapa Global de Disponibilidade' : 'O Meu Mapa de Ausências' }}
            </h2>
        </div>

        <div class="overflow-x-auto text-left">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                        @if($isManager) <th class="p-6">Colaborador</th> @endif
                        <th class="p-6 text-center">Tipo</th>
                        <th class="p-6 text-center">Período</th>
                        <th class="p-6 text-center">Duração</th>
                        <th class="p-6 text-center">Estado</th>
                        <th class="p-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-left">
                    @forelse($absences as $abs)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all">
                            @if($isManager)
                                <td class="p-6">
                                    <span class="text-sm font-black dark:text-white uppercase">{{ $abs->employee->name }}</span>
                                </td>
                            @endif
                            <td class="p-6 text-center leading-none">
                                <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-lg text-[8px] font-black uppercase tracking-widest">
                                    {{ $abs->type_text }}
                                </span>
                            </td>
                            <td class="p-6 text-center leading-none">
                                <span class="text-xs font-bold dark:text-zinc-300 uppercase tracking-tighter">{{ $abs->start_date->format('d M') }} — {{ $abs->end_date->format('d M') }}</span>
                            </td>
                            <td class="p-6 text-center leading-none">
                                <span class="text-sm font-black dark:text-white">{{ $abs->business_days }}d</span>
                            </td>
                            <td class="p-6 text-center leading-none">
                                @if($abs->status === 'aprovado')
                                    <flux:badge variant="success" class="uppercase font-black text-[8px] px-3">Validado</flux:badge>
                                @elseif($abs->status === 'recusado')
                                    <flux:badge variant="danger" class="uppercase font-black text-[8px] px-3">Recusado</flux:badge>
                                @else
                                    <flux:badge variant="warning" class="uppercase font-black text-[8px] px-3 animate-pulse">Pendente</flux:badge>
                                @endif
                            </td>
                            <td class="p-6 text-right leading-none">
                                <div class="flex gap-2 justify-end opacity-0 group-hover:opacity-100 transition-all">
                                    @if($isManager && $abs->status === 'pendente')
                                        <flux:button wire:click="approve({{ $abs->id }})" variant="ghost" icon="check" size="sm" color="emerald" class="rounded-xl" />
                                    @endif
                                    <flux:button wire:click="delete({{ $abs->id }})" wire:confirm="Eliminar registo?" variant="ghost" icon="trash" size="sm" color="red" class="rounded-xl" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-24 text-center text-zinc-400 font-bold uppercase text-xs italic">Sem registos encontrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. MODAL DINÂMICO --}}
    <flux:modal name="absence-modal" position="center" class="md:w-[500px] !p-0 overflow-visible text-left">
        <div class="p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border dark:border-zinc-800 text-left">
            <div class="flex items-center gap-4 text-left">
                <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20"><flux:icon name="calendar-days" class="size-6" /></div>
                <div class="text-left">
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter leading-none">{{ $isManager ? 'Registar Ausência' : 'Nova Solicitação' }}</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium italic mt-2 text-left leading-none">{{ $isManager ? 'Inserir ausência na agenda da equipa.' : 'O pedido será validado pela administração.' }}</p>
                </div>
            </div>

            <div class="space-y-6 text-left">
                @if($isManager)
                    <div class="space-y-2 text-left">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Colaborador</flux:label>
                        <flux:select wire:model="employee_id" class="font-bold border-none !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 shadow-inner" placeholder="Selecionar Colaborador...">
                            @foreach($employees as $emp) <option value="{{ $emp->id }}">👤 {{ $emp->name }}</option> @endforeach
                        </flux:select>
                    </div>
                @endif

                <div class="space-y-2 text-left">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Tipo</flux:label>
                    <flux:select wire:model="type" class="font-black uppercase text-xs !bg-zinc-50 dark:!bg-zinc-900 border-none rounded-2xl h-14 shadow-inner">
                        <option value="ferias">🌴 Período de Férias</option>
                        <option value="doenca">🏥 Baixa Médica</option>
                        <option value="pessoal">🏠 Assuntos Pessoais</option>
                    </flux:select>
                </div>

                <div class="grid grid-cols-2 gap-6 p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] shadow-inner text-left">
                    <flux:input wire:model="start_date" type="date" label="Início" class="font-bold border-none" />
                    <flux:input wire:model="end_date" type="date" label="Conclusão" class="font-bold border-none" />
                </div>

                <flux:textarea wire:model="notes" label="Observações / Motivo" rows="3" class="rounded-2xl border-none bg-zinc-50 shadow-inner" />
            </div>

            <div class="flex gap-4 pt-4">
                <flux:modal.close><flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px]">Descartar</flux:button></flux:modal.close>
                <flux:button wire:click="{{ $isManager ? 'save' : 'submitRequest' }}" variant="primary" class="flex-[2] h-14 rounded-2xl bg-brand-600 font-black uppercase shadow-brand-500/20 shadow-xl border-none">
                    {{ $isManager ? 'Confirmar Registo' : 'Enviar para Aprovação' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
