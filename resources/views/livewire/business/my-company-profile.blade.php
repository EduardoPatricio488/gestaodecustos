<div class="space-y-10 pb-20 text-left" x-data="{ privacyMode: false }">

    {{-- 1. HEADER --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-emerald-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="size-20 rounded-[2rem] overflow-hidden border-4 border-white dark:border-zinc-800 shadow-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    @if($employee->photo_path)
                        <img src="{{ asset('storage/' . $employee->photo_path) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl font-black text-emerald-600">{{ substr($employee->name, 0, 1) }}</span>
                    @endif
                </div>
                <div>
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">{{ $employee->name }}</h1>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                            {{ $employee->role }}
                        </span>
                        <span class="text-zinc-400 text-xs font-bold uppercase tracking-widest">ID: #{{ 200 + ($employee->id % 50) }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" x-on:click="privacyMode = !privacyMode"
                    class="rounded-xl p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-500 shadow-sm transition-all hover:bg-zinc-50">
                    <flux:icon x-show="!privacyMode" name="eye" class="size-5" />
                    <flux:icon x-show="privacyMode" name="eye-slash" class="size-5 text-emerald-500" />
                </button>
            </div>
        </header>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- COLUNA: DADOS CONTRATUAIS --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-8 flex items-center gap-2">
                    <flux:icon name="document-text" variant="outline" class="size-4" /> Parâmetros Contratuais
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Vencimento Mensal Bruto</p>
                        <p class="text-3xl font-black dark:text-white tracking-tighter italic">
                            <span :class="privacyMode ? 'blur-md select-none' : ''">{{ number_format($employee->salary, 2, ',', ' ') }} €</span>
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Dia de Processamento</p>
                        <p class="text-3xl font-black dark:text-white tracking-tighter italic">Dia {{ $employee->pay_day }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Estado da Conta</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm font-black uppercase text-emerald-600">Vínculo Ativo</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Organização</p>
                        <p class="text-lg font-bold dark:text-zinc-300 uppercase mt-1">{{ $workspace->name }}</p>
                    </div>
                </div>
            </div>
  {{-- NOVO: REGISTO DE ASSIDUIDADE (PONTO) --}}
            <div class="space-y-6">
                <div class="flex flex-col md:flex-row justify-between items-end gap-4 px-2">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600">
                            <flux:icon name="clock" variant="outline" class="size-4" />
                        </div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 italic">O Meu Histórico de Ponto</h2>
                    </div>

                    <div class="flex gap-2">
                        <select wire:model.live="selectedMonth" class="bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl text-[10px] font-black uppercase p-2 outline-none">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="selectedYear" class="bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl text-[10px] font-black uppercase p-2 outline-none">
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                </div>

                <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-zinc-50 dark:bg-zinc-950/50 border-b border-zinc-100 dark:border-zinc-800 text-[9px] font-black uppercase text-zinc-400">
                            <tr>
                                <th class="px-8 py-5">Data</th>
                                <th class="px-6 py-5">Entrada</th>
                                <th class="px-6 py-5">Saída</th>
                                <th class="px-8 py-5 text-right">Total Horas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                            @forelse($attendanceLogs as $log)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-8 py-4 text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase">{{ \Carbon\Carbon::parse($log->date)->format('d M, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 rounded-lg text-[10px] font-black">
                                            {{ \Carbon\Carbon::parse($log->clock_in)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->clock_out)
                                            <span class="px-2.5 py-1 bg-amber-500/10 text-amber-600 rounded-lg text-[10px] font-black">
                                                {{ \Carbon\Carbon::parse($log->clock_out)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-[9px] font-black text-zinc-400 animate-pulse">EM SERVIÇO...</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-4 text-right">
    <span class="text-sm font-black dark:text-white">
        @if($log->total_minutes > 0)
            {{ floor($log->total_minutes / 60) }}h {{ $log->total_minutes % 60 }}min
        @elseif(!$log->clock_out)
            <span class="text-emerald-500 italic">Contagem ativa...</span>
        @else
            0h 1min {{-- Para registos imediatos --}}
        @endif
    </span>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-10 text-center text-zinc-400 text-[10px] font-black uppercase tracking-widest">
                                        Sem registos para este mês.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

              {{-- 4. O MEU COFRE DIGITAL (DOCUMENTOS OFICIAIS) --}}
    <div class="space-y-6 text-left">
        <div class="flex items-center gap-3 px-2">
            <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600">
                <flux:icon name="folder-open" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 italic">O Meu Cofre Digital</h2>
        </div>

        <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] overflow-hidden shadow-sm">
            <div class="p-8 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20 text-left">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Documentação Emitida pela Administração</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                        <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                            <th class="p-6">Documento</th>
                            <th class="p-6 text-center">Natureza</th>
                            <th class="p-6 text-center">Data de Emissão</th>
                            <th class="p-6 text-center">Tamanho</th>
                            <th class="p-6 text-right pr-10">Download</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                        @forelse($myDocuments as $doc)
                            <tr class="group hover:bg-emerald-50/30 dark:hover:bg-emerald-500/5 transition-all duration-300">
                                {{-- NOME DO FICHEIRO --}}
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 group-hover:text-emerald-600 transition-colors shadow-sm">
                                            <flux:icon name="document-text" class="size-5" />
                                        </div>
                                        <span class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $doc->title }}</span>
                                    </div>
                                </td>

                                {{-- TIPO --}}
                                <td class="p-6 text-center">
                                    <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-lg text-[8px] font-black uppercase tracking-widest border border-zinc-200 dark:border-zinc-700 leading-none">
                                        {{ $doc->type === 'recibo' ? '📄 Recibo' : ($doc->type === 'contrato' ? '📜 Contrato' : '📁 Outro') }}
                                    </span>
                                </td>

                                {{-- DATA --}}
                                <td class="p-6 text-center text-xs font-bold text-zinc-500 uppercase leading-none">
                                    {{ \Carbon\Carbon::parse($doc->created_at)->translatedFormat('d M, Y') }}
                                </td>

                                {{-- TAMANHO --}}
                                <td class="p-6 text-center text-[10px] font-mono text-zinc-400 leading-none">
                                    {{ $doc->file_size ?? '---' }}
                                </td>

                                {{-- AÇÃO DE DOWNLOAD --}}
                                <td class="p-6 text-center pr-8 ">
                                    <flux:button
    wire:click="downloadDocument({{ $doc->id }})"
    variant="ghost"
    size="sm"
    icon="cloud-arrow-down"
    class="text-zinc-400 hover:text-emerald-600"
/>

                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-24 text-center">
                                    <div class="flex flex-col items-center justify-center gap-4">
                                        <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                                            <flux:icon name="shield-check" class="size-12 text-zinc-200 dark:text-zinc-700" />
                                        </div>
                                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Cofre Vazio</p>
                                        <p class="text-zinc-400 text-xs italic font-medium">A administração ainda não carregou documentos oficiais para o teu perfil.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        </div>


        {{-- COLUNA LATERAL: SEGURANÇA E SUPORTE --}}
        <div class="space-y-6">
            <div class="p-8 bg-zinc-950 text-white rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
                <flux:icon name="shield-check" class="size-10 mb-6 text-emerald-500" />
                <h4 class="text-xl font-black uppercase italic leading-tight">Acesso Seguro</h4>
                <p class="text-[10px] font-medium opacity-60 mt-2 leading-relaxed">
                    Os teus dados estão protegidos sob o protocolo de segurança da empresa. Apenas tu e a administração têm acesso a estes valores.
                </p>
                <div class="absolute -right-4 -bottom-4 size-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
            </div>

            <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">
                <h4 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-4 text-left">Dúvidas com os dados?</h4>
                <p class="text-xs text-zinc-500 leading-relaxed mb-6 text-left">Se notares algum erro no teu salário ou cargo, entra em contacto com os RH através do suporte interno.</p>
                <flux:button href="{{ route('hub.business.support') }}" variant="ghost" class="w-full rounded-xl bg-zinc-50 dark:bg-zinc-800 font-black uppercase text-[10px] tracking-widest h-12">
                    Abrir Ticket
                </flux:button>
            </div>
        </div>
    </div>
    {{-- ZONA DE RESCISÃO --}}
    <div class="mt-20 pt-10 border-t border-red-500/20 text-left">

        {{-- Verificação direta do status vindo da DB --}}
        @if($employee && $employee->resignation_status === 'pending')

            <div class="glass-card p-10 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 rounded-[3rem] relative overflow-hidden animate-in fade-in duration-500">
                <div class="flex flex-col gap-6 relative z-10 text-left">
                    <div class="flex items-center gap-6">
                        <div class="size-14 rounded-2xl bg-amber-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                            <flux:icon name="clock" class="size-7 animate-pulse" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-amber-600 dark:text-amber-500 uppercase italic tracking-tighter leading-none">Pedido em Análise</h3>
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] mt-2">Enviado a {{ $ceoName }}</p>
                        </div>
                    </div>

                    <div class="p-5 bg-white dark:bg-zinc-950 rounded-2xl border border-amber-100 dark:border-zinc-800">
                        <p class="italic text-sm text-zinc-500 dark:text-zinc-400">"{{ $employee->resignation_reason }}"</p>
                    </div>
                    <p class="text-center text-[8px] font-black text-amber-600 uppercase tracking-[0.5em]">A aguardar resposta da administração</p>
                </div>
            </div>

        @else
            <div class="glass-card p-8 bg-red-500/[0.02] border border-red-500/20 rounded-[2.5rem] flex flex-col md:flex-row justify-between items-center gap-6 group transition-all hover:bg-red-500/[0.04]">
                <div class="flex items-center gap-6 text-left w-full">
                    <div class="size-14 rounded-2xl bg-red-500/10 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform shadow-inner">
                        <flux:icon name="exclamation-triangle" variant="outline" class="size-7" />
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-red-600 uppercase italic tracking-tighter leading-none">Cessação de Vínculo</h3>
                        <p class="text-xs text-zinc-500 font-medium mt-2">Solicitar a demissão formal da organização.</p>
                    </div>
                </div>

                <flux:modal.trigger name="resignation-modal">
                    <flux:button variant="ghost" class="h-12 px-8 rounded-xl font-black uppercase text-[10px] tracking-widest text-red-600 hover:bg-red-600 hover:text-white transition-all border border-red-500/20 shadow-sm">
                        Solicitar Demissão
                    </flux:button>
                </flux:modal.trigger>
            </div>
        @endif
    </div>

    {{-- MODAL DE RESCISÃO --}}
    <flux:modal name="resignation-modal" position="center" class="md:w-[500px]">
        <div class="space-y-6 text-left">
            <flux:heading size="xl" class="font-black uppercase italic text-red-600 leading-none">Confirmar Pedido</flux:heading>
            <p class="text-xs text-zinc-500 font-medium">Explica ao CEO o motivo da tua saída para que ele possa processar a rescisão.</p>

            <flux:textarea wire:model="resignationReason" placeholder="Escreve aqui o teu motivo..." rows="5" class="rounded-2xl" />

            <div class="flex gap-4">
                <flux:modal.close><flux:button variant="ghost" class="flex-1 font-bold uppercase">Cancelar</flux:button></flux:modal.close>
                <flux:button wire:click="requestResignation" variant="primary" class="flex-1 bg-red-600 border-none font-black uppercase shadow-lg shadow-red-500/20">Enviar Pedido</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
