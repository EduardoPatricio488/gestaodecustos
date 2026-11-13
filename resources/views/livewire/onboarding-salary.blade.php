<form wire:submit.prevent="saveEmployment" class="space-y-5">
    <div class="flex items-center gap-4">
        <div class="rounded-2xl bg-indigo-600 p-3 text-white shadow-lg">
            <flux:icon name="briefcase" class="size-6" />
        </div>
        <div>
            <h3 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white">{{ $onboardingSalaryId ? 'Editar Salário' : 'Configurar Salário' }}</h3>
            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Cálculo Automático de Impostos</p>
        </div>
    </div>

    @include('livewire.partials.salary-form')

    @if($errors->any())
        <div role="alert" class="rounded-2xl bg-rose-50 p-4 text-sm text-rose-700 dark:bg-rose-950/30 dark:text-rose-300">
            <p class="font-bold">Confirma os dados do salário:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 dark:border-emerald-900/50 dark:bg-emerald-950/30">
        <span class="text-xs font-black uppercase tracking-wide text-emerald-800 dark:text-emerald-300">Líquido final mensal</span>
        <span class="text-xl font-black tabular-nums text-emerald-700 dark:text-emerald-300">{{ number_format((float) $recAmount, 2, ',', ' ') }} €</span>
    </div>
    @include('livewire.partials.onboarding-footer', [
        'footerStep' => 4, 'footerTotal' => $footerTotal,
        'footerPrevious' => '$parent.previousStep()', 'footerSkip' => '$parent.skipStep()',
        'footerNext' => null, 'footerSubmit' => true,
        'footerNextLabel' => 'Guardar e Continuar',
        'footerLoadingTarget' => 'saveEmployment,scanEmploymentContract,contractScanPhoto',
    ])
</form>
