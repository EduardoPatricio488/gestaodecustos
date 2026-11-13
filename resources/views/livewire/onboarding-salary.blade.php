<form wire:submit.prevent="saveEmployment" class="space-y-5">
    @if($savedEmploymentContracts->isNotEmpty())
        <section class="space-y-3 rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">Contratos de trabalho guardados</h3>
                    <p class="mt-1 text-xs text-zinc-500">Os registos anteriores ficam guardados quando adicionas um novo contrato.</p>
                </div>
                <button type="button" wire:click="createNewContract" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-black text-white transition hover:bg-indigo-700">
                    <flux:icon name="plus" class="size-4" /> Novo contrato
                </button>
            </div>
            <div class="space-y-2">
                @foreach($savedEmploymentContracts as $contract)
                    <div wire:key="saved-employment-contract-{{ $contract->id }}" class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-950">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-zinc-900 dark:text-white">{{ $contract->description }}</p>
                            <p class="mt-0.5 text-xs text-zinc-500">
                                {{ number_format((float) ($contract->metadata['salary_gross'] ?? $contract->amount), 2, ',', ' ') }} € brutos
                                · {{ $contract->is_active ? 'Ativo' : 'Inativo' }}
                            </p>
                        </div>
                        <button type="button" wire:click="editExistingContract({{ $contract->id }})" class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-50 dark:border-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-950">
                            <flux:icon name="pencil" class="mr-1 inline size-3.5" /> Editar
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
    @if($onboardingSalaryId)
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300" role="status">
            <p class="font-black">Já tens um registo de salário guardado.</p>
            <p class="mt-1 text-xs">Os dados abaixo correspondem ao teu registo atual. Podes confirmar ou atualizar a informação.</p>
        </div>
    @endif

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
