<div class="space-y-8 pb-24" x-data="{ privacyMode: true }">
    <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <flux:icon name="receipt-percent" class="size-7 text-amber-600" />
                <flux:badge variant="neutral" class="text-[10px] uppercase tracking-widest">Informação fiscal</flux:badge>
            </div>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-zinc-900 dark:text-white">Impostos & Obrigações</h1>
            <p class="mt-2 max-w-3xl text-sm text-zinc-500 dark:text-zinc-400">Consulta os valores fiscais que resultam diretamente dos dados registados no Finance Pro AI. A aplicação não substitui o contabilista nem determina obrigações fiscais oficiais.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" x-on:click="privacyMode = !privacyMode" class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3 text-zinc-500 hover:text-zinc-900 dark:hover:text-white" aria-label="Alternar privacidade">
                <template x-if="privacyMode"><flux:icon name="eye-slash" class="size-5" /></template>
                <template x-if="!privacyMode"><flux:icon name="eye" class="size-5" /></template>
            </button>
            <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate>Painel Business</flux:button>
        </div>
    </header>

    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-950/20">
        <div class="flex gap-4">
            <flux:icon name="information-circle" class="size-6 shrink-0 text-amber-600" />
            <div>
                <p class="font-bold text-amber-900 dark:text-amber-200">Limites desta área</p>
                <p class="mt-1 text-sm leading-6 text-amber-800 dark:text-amber-300">{{ $taxDisclaimer }}</p>
            </div>
        </div>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">IVA liquidado</p>
            <p class="mt-3 text-3xl font-black text-zinc-900 dark:text-white" :class="privacyMode ? 'blur-md select-none' : ''">{{ number_format($vatCollected, 2, ',', ' ') }} €</p>
            <p class="mt-2 text-xs text-zinc-500">IVA registado nas faturas pagas este mês.</p>
        </div>
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">IVA dedutível registado</p>
            <p class="mt-3 text-3xl font-black text-zinc-900 dark:text-white" :class="privacyMode ? 'blur-md select-none' : ''">{{ number_format($vatDeductible, 2, ',', ' ') }} €</p>
            <p class="mt-2 text-xs text-zinc-500">IVA registado nas despesas empresariais deste mês.</p>
        </div>
        <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-900/50 dark:bg-amber-950/20">
            <p class="text-[10px] font-black uppercase tracking-widest text-amber-700 dark:text-amber-300">Saldo informativo de IVA</p>
            <p class="mt-3 text-3xl font-black text-amber-900 dark:text-amber-200" :class="privacyMode ? 'blur-md select-none' : ''">{{ number_format($vatNet, 2, ',', ' ') }} €</p>
            <p class="mt-2 text-xs text-amber-800 dark:text-amber-300">{{ $vatNet >= 0 ? 'Saldo positivo com base nos registos atuais.' : 'Saldo negativo com base nos registos atuais.' }}</p>
        </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach([
            ['title'=>'TSU / Segurança Social','value'=>$tsuEstimate,'icon'=>'users','text'=>'Não calculada automaticamente.'],
            ['title'=>'IRC','value'=>$ircProvision,'icon'=>'presentation-chart-bar','text'=>'Não calculado automaticamente.'],
            ['title'=>'Retenções IRS','value'=>$irsWithheld,'icon'=>'banknotes','text'=>'Não calculadas automaticamente.'],
            ['title'=>'Derrama','value'=>$derrama,'icon'=>'building-office','text'=>'Não calculada automaticamente.'],
        ] as $item)
            <div class="rounded-3xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ $item['title'] }}</p>
                    <flux:icon name="{{ $item['icon'] }}" class="size-5 text-zinc-400" />
                </div>
                <p class="mt-4 text-2xl font-black text-zinc-900 dark:text-white">{{ number_format($item['value'], 2, ',', ' ') }} €</p>
                <p class="mt-2 text-xs text-zinc-500">{{ $item['text'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="rounded-3xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-zinc-900 dark:text-white">Estado fiscal registado</h2>
                <p class="mt-1 text-sm text-zinc-500">Resumo operacional, sem transformar dados incompletos em obrigações legais.</p>
            </div>
            <flux:badge :variant="$vatNet > 0 ? 'warning' : 'success'" class="uppercase tracking-widest">IVA {{ $vatNet > 0 ? 'positivo' : 'sem saldo positivo' }}</flux:badge>
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-800/50"><span class="text-zinc-500">IVA</span><strong class="float-right text-zinc-900 dark:text-white">{{ number_format($vatNet, 2, ',', ' ') }} €</strong></div>
            <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-800/50"><span class="text-zinc-500">Outras obrigações automáticas</span><strong class="float-right text-zinc-900 dark:text-white">Não disponíveis</strong></div>
        </div>
    </section>
</div>
