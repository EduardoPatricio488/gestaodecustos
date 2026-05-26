@php
    $themes = [
        'carro'       => ['color' => 'text-amber-500',  'label' => 'Logística de Veículo',  'budgetLabel' => 'Plafond de Manutenção'],
        'casa'        => ['color' => 'text-blue-500',   'label' => 'Gestão de Habitação',    'budgetLabel' => 'Gestão de Rendas'],
        'alimentacao' => ['color' => 'text-orange-500', 'label' => 'Controlo de Consumo',    'budgetLabel' => 'Budget de Nutrição'],
        'saude'       => ['color' => 'text-red-500',    'label' => 'Bem-estar e Saúde',       'budgetLabel' => 'Reserva Médica'],
        'tecnologia'  => ['color' => 'text-indigo-500', 'label' => 'Infraestrutura Digital', 'budgetLabel' => 'Budget SaaS'],
    ];
    $hubTheme = $themes[$slug] ?? ['color' => 'text-brand-600', 'label' => 'Gestão Estratégica', 'budgetLabel' => 'Teto Orçamental'];
@endphp

<div
    x-data="{ scannerPreview: null }"
    x-on:scan-completed.window="Flux.hide('ai-scanner-modal'); setTimeout(() => Flux.show('ai-review-modal'), 400);"
    x-on:open-add-expense-modal.window="Flux.hide('ai-review-modal'); setTimeout(() => Flux.show('add-expense-modal'), 400);"
    class="space-y-10 pb-24"
>

    {{-- ── HEADER ─────────────────────────────────────────────────── --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-72 {{ str_replace('text','bg',$hubTheme['color']) }}/5 blur-[120px] rounded-full pointer-events-none"></div>
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2 pt-6">
            <div class="flex items-center gap-6">
                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="{{ $icon }}" class="w-10 h-10 {{ $hubTheme['color'] }}" />
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">{{ $title }}</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Hub Inteligente</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">{{ $hubTheme['label'] }} · <span class="text-brand-600 font-bold uppercase tracking-tighter">{{ $currentWs->name }}</span></p>
                </div>
            </div>
            @if($canManage)
            <div class="flex items-center gap-4 bg-white dark:bg-zinc-900 p-3 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="ai-scanner-modal">
                    <flux:button variant="ghost" icon="sparkles" class="text-brand-600 rounded-2xl hover:bg-brand-50 font-black uppercase text-sm px-4">Scanner IA</flux:button>
                </flux:modal.trigger>
                <div class="h-8 w-px bg-zinc-200 dark:bg-zinc-800"></div>
                <flux:modal.trigger name="add-expense-modal">
                    <flux:button variant="primary" icon="plus" class="bg-brand-600 border-none shadow-lg rounded-2xl font-black uppercase text-sm px-8 text-white">Novo Registo</flux:button>
                </flux:modal.trigger>
                <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate class="rounded-xl" />
            </div>
            @endif
        </header>
    </div>

    {{-- ── PAINEL ORÇAMENTAL ──────────────────────────────────────── --}}
    <div class="relative overflow-hidden bg-zinc-950 p-10 rounded-[3rem] shadow-2xl border border-zinc-800 group">
        <div class="absolute -right-20 -top-20 size-80 {{ str_replace('text','bg',$hubTheme['color']) }}/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
            <div class="space-y-2">
                <p class="text-[10px] font-black text-brand-400 uppercase tracking-[0.4em] mb-4 italic">{{ $hubTheme['budgetLabel'] }} ({{ now()->translatedFormat('F') }})</p>
                <div class="flex flex-wrap items-baseline gap-4">
                    <h3 class="text-6xl font-black text-white tracking-tighter italic leading-none">{{ number_format($spentThisMonth,2,',',' ') }}€</h3>
                    <span class="text-2xl font-black text-zinc-600 uppercase tracking-tighter">/</span>
                    @if($editingBudget && $isOwner)
                        <input type="number" wire:model="budgetLimit" wire:keydown.enter="updateBudget" wire:blur="updateBudget" autofocus
                               class="w-44 bg-white/5 border border-white/10 rounded-2xl px-4 py-1 {{ $hubTheme['color'] }} font-black text-4xl outline-none shadow-inner">
                    @else
                        <button @if($isOwner) wire:click="$set('editingBudget', true)" @endif class="group/btn flex items-center gap-3 outline-none">
                            <span class="text-4xl font-black {{ $budgetLimit > 0 ? 'text-zinc-500' : 'text-zinc-700 animate-pulse' }} tracking-tighter italic uppercase">
                                {{ $budgetLimit > 0 ? number_format($budgetLimit,0).'€' : 'Definir Limite' }}
                            </span>
                            @if($isOwner)<flux:icon name="pencil" class="size-4 text-zinc-600 group-hover/btn:{{ $hubTheme['color'] }} transition-colors" />@endif
                        </button>
                    @endif
                </div>
            </div>
            @if($budgetLimit > 0)
                @php $perc = ($spentThisMonth / $budgetLimit) * 100; @endphp
                <div class="text-right">
                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Eficiência de Consumo</p>
                    <p class="text-5xl font-black {{ $perc >= 100 ? 'text-red-500' : ($perc >= 80 ? 'text-orange-500' : 'text-emerald-500') }} tracking-tighter italic">{{ round($perc) }}%</p>
                </div>
            @endif
        </div>
        @if($budgetLimit > 0)
        <div class="mt-10 h-2.5 w-full bg-white/5 rounded-full overflow-hidden p-0.5 border border-white/5 shadow-inner">
            <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $perc >= 100 ? 'bg-red-500 shadow-[0_0_20px_#ef4444]' : str_replace('text','bg',$hubTheme['color']).' shadow-[0_0_20px_rgba(59,130,246,0.6)]' }}" style="width: {{ min($perc,100) }}%"></div>
        </div>
        @endif
    </div>

    {{-- ── LEDGER ─────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="px-8 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-950/20">
            <p class="text-sm font-black dark:text-white uppercase italic tracking-tight">Histórico Detalhado: {{ $title }}</p>
            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none">{{ $expenses->count() }} Registos</flux:badge>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-[0.2em]">
                        <th class="p-6 w-32">Data</th>
                        <th class="p-6">Detalhes</th>
                        <th class="p-6 text-right px-8 w-72">Montante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row">
                        <td class="p-6 align-top">
                            <span class="text-2xl font-black dark:text-white leading-none tracking-tighter block">{{ $expense->spent_at->format('d') }}</span>
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mt-1.5 block">{{ $expense->spent_at->translatedFormat('M, Y') }}</span>
                        </td>
                        <td class="p-6 align-top">
                            <div class="flex flex-col gap-4">
                                <span class="text-[10px] w-fit font-black {{ $hubTheme['color'] }} uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 px-3 py-1 rounded-lg">{{ $expense->subcategory }}</span>
                                @if($expense->description)
                                <div class="relative pl-6 py-1">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ str_replace('text','bg',$hubTheme['color']) }}/40 rounded-full"></div>
                                    <p class="text-base font-medium text-zinc-800 dark:text-zinc-200 italic">"{{ $expense->description }}"</p>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="p-6 text-right px-8 align-middle">
                            <span class="text-xl font-black text-red-500 tracking-wide italic whitespace-nowrap block">-{{ number_format($expense->amount,2,',',' ') }}€</span>
                            <button wire:click="deleteExpense({{ $expense->id }})" wire:confirm="Tem a certeza?" class="mt-4 p-2 text-zinc-300 hover:text-red-500 opacity-0 group-hover/row:opacity-100 transition-all inline-block">
                                <flux:icon name="trash" variant="mini" class="size-4" />
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="p-32 text-center text-zinc-400 italic">Sem movimentos registados neste Hub.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1 — SCANNER IA                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <flux:modal name="ai-scanner-modal" position="center" class="md:w-[500px] !p-0 overflow-visible">
        <div class="relative p-10 bg-zinc-950 rounded-[3rem] border border-white/10 shadow-2xl space-y-8 overflow-hidden">

            {{-- Loader --}}
            <div wire:loading wire:target="scanReceiptWithAI"
                 class="absolute inset-0 bg-zinc-950/95 backdrop-blur-md z-50 flex flex-col items-center justify-center rounded-[3rem] gap-6">
                <div class="relative">
                    <div class="size-20 border-4 border-brand-500/20 border-t-brand-500 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <flux:icon name="sparkles" class="size-7 text-brand-400 animate-pulse" />
                    </div>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-brand-400 font-black uppercase tracking-[0.4em] text-[10px]">IA a processar fatura...</p>
                    <p class="text-zinc-600 text-[9px] uppercase tracking-widest">Extração de dados em curso</p>
                </div>
            </div>

            <div class="absolute -top-24 -left-24 size-64 bg-brand-500/10 blur-[100px] rounded-full animate-pulse pointer-events-none"></div>

            {{-- Cabeçalho --}}
            <div class="text-center space-y-2">
                <div class="inline-flex p-4 bg-brand-500/10 rounded-2xl text-brand-400">
                    <flux:icon name="sparkles" class="size-8" />
                </div>
                <flux:heading size="xl" class="text-white font-black uppercase italic tracking-tighter leading-none">Scanner IA Vision</flux:heading>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest italic">Protocolo de Extração Automática</p>
            </div>

            {{-- Erro do scan --}}
            @if($scanError)
            <div class="flex items-start gap-3 bg-red-500/10 border border-red-500/20 rounded-2xl px-5 py-4">
                <flux:icon name="exclamation-triangle" class="size-5 text-red-400 shrink-0 mt-0.5" />
                <p class="text-sm text-red-300 font-medium">{{ $scanError }}</p>
            </div>
            @endif

            {{-- Dropzone --}}
            <div class="relative bg-zinc-900/50 rounded-[2.5rem] p-10 border-2 border-dashed border-zinc-800 hover:border-brand-500/50 transition-all text-center group/drop">
                {{--
                    IMPORTANTE: o input NÃO usa wire:model para evitar conflitos com o Alpine preview.
                    O upload é feito manualmente via wire:model.live após selecção.
                --}}
                <input
                    type="file"
                    accept="image/*,.pdf"
                    x-ref="fileInput"
                    @change="
                        const file = $event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = e => { scannerPreview = e.target.result };
                            reader.readAsDataURL(file);
                        }
                    "
                    wire:model="receipt"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30"
                >

                <div class="relative z-10 pointer-events-none">
                    <template x-if="scannerPreview">
                        <div class="relative inline-block">
                            <img :src="scannerPreview" class="size-44 object-cover rounded-3xl border-4 border-brand-500/50 shadow-2xl mx-auto">
                            <div class="absolute -top-3 -right-3 size-10 bg-emerald-500 text-white rounded-full flex items-center justify-center border-4 border-zinc-950 shadow-lg">
                                <flux:icon name="check" variant="mini" />
                            </div>
                        </div>
                    </template>
                    <template x-if="!scannerPreview">
                        <div class="py-4 space-y-4">
                            <div class="size-20 bg-zinc-800/50 rounded-[1.5rem] flex items-center justify-center mx-auto border border-white/5 group-hover/drop:bg-brand-500/10 transition-colors">
                                <flux:icon name="camera" class="size-10 text-zinc-600 group-hover/drop:text-brand-400" />
                            </div>
                            <div>
                                <p class="text-sm font-black text-white uppercase tracking-tight italic">Anexar Documento</p>
                                <p class="text-[10px] text-zinc-600 mt-1 uppercase tracking-widest">Foto de fatura, recibo ou talão</p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Scan line animada --}}
            <div wire:loading wire:target="scanReceiptWithAI" class="absolute inset-x-0 pointer-events-none h-1 animate-scan-line z-20"></div>

            {{-- Botões --}}
            <div class="flex gap-4" wire:loading.remove wire:target="scanReceiptWithAI">
                <flux:modal.close class="flex-1">
                    <button type="button"
                        x-on:click="scannerPreview = null"
                        class="w-full h-14 text-zinc-600 font-bold uppercase text-xs hover:text-white transition tracking-widest">
                        Cancelar
                    </button>
                </flux:modal.close>

                {{--
                    Botão separado do Alpine e do wire:loading para evitar dead-lock.
                    Fica desabilitado só enquanto não há preview OU enquanto o Livewire está a processar.
                --}}
                <button
                    type="button"
                    wire:click="scanReceiptWithAI"
                    x-bind:disabled="!scannerPreview"
                    x-bind:class="!scannerPreview ? 'opacity-30 cursor-not-allowed' : 'hover:bg-brand-500 cursor-pointer'"
                    class="flex-[2] h-14 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 transition-all text-sm"
                >
                    Iniciar Extração
                </button>
            </div>

            {{-- Estado de loading nos botões --}}
            <div wire:loading wire:target="scanReceiptWithAI" class="flex gap-4">
                <div class="flex-1 h-14"></div>
                <div class="flex-[2] h-14 rounded-2xl bg-brand-600/50 text-white/50 font-black uppercase tracking-widest text-sm flex items-center justify-center gap-3">
                    <div class="size-4 border-2 border-white/20 border-t-white/80 rounded-full animate-spin"></div>
                    A processar...
                </div>
            </div>
        </div>
    </flux:modal>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2 — REVISÃO IA                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <flux:modal name="ai-review-modal" position="center" class="md:w-[680px] !p-0 overflow-visible">
        <div class="relative bg-zinc-950 rounded-[2.5rem] shadow-2xl border border-white/10 overflow-hidden">
            <div class="absolute -top-32 -right-32 size-96 bg-brand-500/10 blur-[120px] rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 size-80 bg-emerald-500/5 blur-[100px] rounded-full pointer-events-none"></div>

            {{-- Header --}}
            <div class="relative p-8 border-b border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <flux:icon name="sparkles" class="size-6 text-emerald-400" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-white uppercase italic tracking-tighter leading-none">Revisão IA</h2>
                        <p class="text-[10px] text-zinc-500 mt-1 font-bold uppercase tracking-widest">Confirme os dados extraídos automaticamente</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full px-3 py-1.5">
                        <div class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                        <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">IA Analisada</span>
                    </div>
                    <flux:modal.close>
                        <button class="p-2 rounded-full hover:bg-white/5 text-zinc-500 transition-colors">
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </flux:modal.close>
                </div>
            </div>

            @if(!empty($scannedData))
            <div class="p-8 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">

                {{-- Valor principal --}}
                <div class="relative overflow-hidden bg-white/3 border border-white/8 rounded-[2rem] p-8 text-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 to-transparent pointer-events-none"></div>
                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.4em] mb-2">Valor Total Extraído</p>
                    <p class="text-7xl font-black text-white tracking-tighter italic leading-none">
                        {{ number_format($scannedData['amount'], 2, ',', '.') }}€
                    </p>
                    @if(!empty($scannedData['tax']))
                    <p class="text-xs text-zinc-500 mt-3 uppercase tracking-widest font-bold">
                        IVA incluído: {{ number_format($scannedData['tax'], 2, ',', '.') }}€
                    </p>
                    @endif
                </div>

                {{-- Grid de metadados --}}
                <div class="grid grid-cols-2 gap-3">

                    <div class="col-span-2 bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-brand-500/10 shrink-0">
                            <flux:icon name="building-storefront" class="size-5 text-brand-400" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Estabelecimento</p>
                            <p class="text-base font-black text-white truncate italic">{{ $scannedData['store'] ?: '—' }}</p>
                        </div>
                    </div>

                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="calendar" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Data</p>
                            <p class="text-sm font-black text-white">{{ \Carbon\Carbon::parse($scannedData['date'])->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="tag" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Categoria</p>
                            <p class="text-sm font-black {{ $hubTheme['color'] }} uppercase">{{ $scannedData['subcategory'] }}</p>
                        </div>
                    </div>

                    @if(!empty($scannedData['payment_method']) && $scannedData['payment_method'] !== 'desconhecido')
                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="credit-card" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Pagamento</p>
                            <p class="text-sm font-black text-white uppercase">{{ $scannedData['payment_method'] }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($scannedData['invoice_number']))
                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="document-text" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Nº Fatura</p>
                            <p class="text-sm font-black text-white font-mono">{{ $scannedData['invoice_number'] }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($scannedData['nif_emitter']))
                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="identification" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">NIF Emitente</p>
                            <p class="text-sm font-black text-white font-mono">{{ $scannedData['nif_emitter'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Artigos detetados --}}
                @if(!empty($scannedData['items']) && count($scannedData['items']) > 0)
                <div class="bg-white/3 border border-white/8 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3 border-b border-white/5 flex items-center gap-2">
                        <div class="size-1.5 rounded-full bg-brand-500"></div>
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em]">Artigos Detetados ({{ count($scannedData['items']) }})</p>
                    </div>
                    <div class="divide-y divide-white/5">
                        @foreach($scannedData['items'] as $item)
                        <div class="px-5 py-3 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                @if(!empty($item['qty']) && $item['qty'] > 1)
                                    <span class="shrink-0 text-[9px] font-black text-brand-400 bg-brand-500/10 rounded-md px-2 py-0.5">×{{ $item['qty'] }}</span>
                                @endif
                                <p class="text-sm text-zinc-300 font-medium truncate">{{ $item['name'] ?? '—' }}</p>
                            </div>
                            @if(!empty($item['price']))
                            <span class="shrink-0 text-sm font-black text-white whitespace-nowrap">{{ number_format($item['price'],2,',','.') }}€</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Notas --}}
                @if(!empty($scannedData['notes']))
                <div class="flex gap-3 bg-amber-500/5 border border-amber-500/10 rounded-2xl px-5 py-4">
                    <flux:icon name="light-bulb" class="size-4 text-amber-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-zinc-400 italic leading-relaxed">{{ $scannedData['notes'] }}</p>
                </div>
                @endif

                <div class="flex gap-3 bg-white/3 border border-white/5 rounded-2xl px-5 py-4">
                    <flux:icon name="pencil-square" class="size-4 text-zinc-500 shrink-0 mt-0.5" />
                    <p class="text-[10px] text-zinc-500 leading-relaxed uppercase tracking-wide font-bold">Pode corrigir qualquer campo no passo seguinte antes de guardar.</p>
                </div>
            </div>

            <div class="px-8 pb-8 pt-4 flex items-center gap-4">
                <flux:modal.close class="flex-1">
                    <button type="button" class="w-full h-14 rounded-2xl font-bold text-sm text-zinc-500 hover:text-white hover:bg-white/5 transition-all tracking-widest uppercase border border-white/5">
                        Cancelar
                    </button>
                </flux:modal.close>
                <button
                    wire:click="confirmScannedData"
                    class="flex-[2] h-14 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 transition-all text-sm flex items-center justify-center gap-3"
                >
                    <flux:icon name="check-circle" class="size-5" />
                    Confirmar e Editar Registo
                </button>
            </div>
            @else
            <div class="p-16 text-center text-zinc-500 italic">Nenhum dado disponível.</div>
            @endif
        </div>
    </flux:modal>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 3 — REGISTO MANUAL                                      --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <flux:modal name="add-expense-modal" position="center" class="md:w-[600px] !p-0 overflow-visible">
        <div class="relative bg-white dark:bg-zinc-950 rounded-[2.5rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="absolute -top-24 -right-24 size-72 bg-brand-500/10 blur-[120px] rounded-full pointer-events-none"></div>

            <div class="relative p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-white/50 dark:bg-zinc-950/50 backdrop-blur-xl">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-500/20">
                        <flux:icon name="plus" class="size-5" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white leading-none italic uppercase">Confirmar Registo</h2>
                        <p class="text-[11px] text-zinc-500 mt-1.5 font-medium uppercase tracking-wider">{{ $title }} • Transação Validada</p>
                    </div>
                </div>
                <flux:modal.close>
                    <button class="p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 transition-colors">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </flux:modal.close>
            </div>

            <form wire:submit="save" class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-brand-600 z-10">Valor ({{ $currency }})</label>
                        <div class="relative">
                            <flux:input wire:model="amount" type="number" step="0.01" placeholder="0,00"
                                class="!h-20 !text-3xl !font-black !pl-6 !rounded-3xl !border-2 focus:!border-brand-500 !bg-zinc-50/50 dark:!bg-zinc-900/50 shadow-inner" />
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-300 dark:text-zinc-700">
                                <flux:icon name="banknotes" class="size-8" />
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-black uppercase tracking-widest text-zinc-400 z-10">Data do Gasto</label>
                        <flux:input wire:model="spent_at" type="date"
                            class="!h-20 !font-bold !text-lg !rounded-3xl !border-2 focus:!border-brand-500 !bg-zinc-50/50 dark:!bg-zinc-900/50 shadow-inner" />
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Classificação</flux:label>
                    <flux:select wire:model="subcategory" class="h-14 !rounded-2xl font-bold text-base border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900">
                        <option value="">Selecione categoria...</option>
                        @foreach($subcategories as $sub)
                        <option value="{{ $sub }}">{{ ucfirst(strtolower($sub)) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="p-6 rounded-[2rem] bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-brand-500 animate-pulse"></div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 italic">Detalhes Adicionais</p>
                    </div>
                    @if($slug == 'carro')
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="meta.km" label="Quilometragem" type="number" placeholder="Ex: 120000" class="rounded-xl" />
                            <flux:input wire:model="meta.local" label="Localização" placeholder="Ex: Galp" class="rounded-xl" />
                        </div>
                    @elseif($slug == 'casa' || $slug == 'seguros' || $slug == 'emprestimos')
                        <flux:input wire:model="meta.entidade" label="Entidade" icon="building-library" class="rounded-xl" />
                    @elseif($slug == 'alimentacao')
                        <flux:input wire:model="meta.pessoas" type="number" label="Nº de Pessoas" icon="users" class="rounded-xl" />
                    @endif
                    <flux:textarea wire:model="description" rows="2" placeholder="Nome da loja ou observação..."
                        class="!rounded-2xl !bg-white dark:!bg-zinc-950 border-zinc-200 dark:border-zinc-800 font-medium italic" />
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <flux:modal.close class="flex-1">
                        <button type="button" class="w-full h-16 rounded-2xl font-bold text-base text-zinc-500 hover:text-zinc-800 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all tracking-widest uppercase">Cancelar</button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary"
                        class="flex-[1.5] h-16 !rounded-2xl !font-black !text-base !uppercase !tracking-widest !bg-brand-600 hover:!bg-brand-700 shadow-xl shadow-brand-500/20 border-none">
                        Confirmar
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- ── RODAPÉ ─────────────────────────────────────────────────── --}}
    <footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <div class="flex flex-col items-center gap-4">
            <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center border border-zinc-200 dark:border-zinc-800">
                <flux:icon name="shield-check" class="size-4 text-zinc-400" />
            </div>
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em] leading-relaxed">
                © {{ date('Y') }} {{ config('app.name') }}<br>
                Protocolo Hub Inteligente v2.6 • {{ $title }}
            </p>
        </div>
    </footer>

    <style>
        @keyframes scan {
            0%   { top: 0%;   opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan-line {
            position: absolute; width: 100%;
            animation: scan 2.5s infinite ease-in-out;
            background: linear-gradient(to bottom, transparent, rgba(59,130,246,0.5), transparent);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
        [portal] { z-index: 100 !important; }
    </style>
</div>
