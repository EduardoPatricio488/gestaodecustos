<div class="space-y-4">
    <p class="text-sm font-bold text-zinc-800 dark:text-zinc-200">Para que fonte de rendimento é este registo?</p>
    <div class="grid gap-2 sm:grid-cols-2">
        @foreach([
            ['emprego', 'briefcase', 'Salário / Contrato de Trabalho'],
            ['renda', 'home', 'Renda Imobiliária'],
            ['freelance', 'computer-desktop', 'Freelance / Trabalho Independente'],
            ['investimento', 'chart-bar-square', 'Rendimento de Investimento'],
            ['reforma', 'building-library', 'Reforma / Pensão'],
            ['bolsa', 'academic-cap', 'Bolsa de Estudo'],
            ['outro', 'ellipsis-horizontal-circle', 'Outra Fonte'],
        ] as [$source, $icon, $label])
            <button type="button" wire:click="openIncomeSource('{{ $source }}')" class="flex items-center gap-3 rounded-2xl border border-zinc-200 bg-white p-4 text-left text-sm font-bold text-zinc-700 transition hover:border-indigo-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                <flux:icon name="{{ $icon }}" class="size-5 shrink-0 text-indigo-500" />
                {{ $label }}
            </button>
        @endforeach
    </div>
    <p class="text-xs text-zinc-500">Cada rendimento é guardado separadamente quando confirmares o respetivo formulário.</p>
    @if($onboardingSaved)<p role="status" class="text-sm font-bold text-emerald-600">Rendimento guardado. Podes adicionar outro ou continuar.</p>@endif
    @include('livewire.partials.employment-modal', ['onboardingModal' => true])
    @include('livewire.partials.income-source-modals', ['onboardingModal' => true])
</div>
