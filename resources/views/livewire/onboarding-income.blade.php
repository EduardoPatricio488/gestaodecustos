<div class="space-y-4">
    <p class="text-sm font-bold text-zinc-800 dark:text-zinc-200">Para que fonte de rendimento é este registo?</p>
    <div class="grid gap-2 sm:grid-cols-2">
        @foreach([
            ['emprego', 'briefcase', 'Estágio/Contrato de trabalho'],
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

    @if($savedIncomeGroups->isNotEmpty())
        <div class="space-y-4 pt-2" data-onboarding-saved-income-groups>
            @foreach($savedIncomeGroups as $group)
                <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900" data-income-source="{{ $group['source'] }}">
                    <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-4 py-3 dark:border-zinc-800">
                        <h3 class="text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-200">{{ $group['label'] }}</h3>
                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-bold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                            {{ $group['records']->count() }} {{ $group['records']->count() === 1 ? 'registo' : 'registos' }}
                        </span>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($group['records'] as $income)
                            @php
                                $frequencyLabels = [
                                    'diario' => 'Diário',
                                    'semanal' => 'Semanal',
                                    'quinzenal' => 'Quinzenal',
                                    'mensal' => 'Mensal',
                                    'trimestral' => 'Trimestral',
                                    'semestral' => 'Semestral',
                                    'anual' => 'Anual',
                                    'pontual' => 'Pontual',
                                ];
                            @endphp
                            <div class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $income->description }}</p>
                                    <p class="mt-1 text-[11px] text-zinc-500">
                                        {{ $frequencyLabels[$income->frequency] ?? ucfirst(str_replace('_', ' ', $income->frequency)) }}
                                        @if($income->day_of_month) · Dia {{ $income->day_of_month }} @endif
                                        · {{ $income->bankAccount?->name ?? 'Dinheiro físico' }}
                                    </p>
                                </div>
                                <div class="flex shrink-0 flex-wrap items-center justify-between gap-3 sm:justify-end">
                                    <div class="text-right">
                                        <p class="text-sm font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format((float) $income->amount, 2, ',', ' ') }} €</p>
                                        <span class="text-[10px] font-bold uppercase {{ $income->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400' }}">
                                            {{ $income->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="editSavedIncome({{ $income->id }})" class="rounded-lg border border-sky-200 px-3 py-2 text-[11px] font-bold text-sky-700 transition hover:bg-sky-50 dark:border-sky-900 dark:text-sky-300 dark:hover:bg-sky-950">
                                            <flux:icon name="pencil" class="mr-1 inline size-3.5" /> Editar
                                        </button>
                                        <button type="button" wire:click="deleteSavedIncome({{ $income->id }})" wire:confirm="Tens a certeza de que pretendes eliminar este rendimento?" class="rounded-lg border border-red-200 px-3 py-2 text-[11px] font-bold text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950">
                                            <flux:icon name="trash" class="mr-1 inline size-3.5" /> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <p class="rounded-2xl border border-dashed border-zinc-200 px-4 py-5 text-center text-xs text-zinc-500 dark:border-zinc-700">
            Ainda não tens rendimentos guardados.
        </p>
    @endif

    @if($onboardingSaved)<p role="status" class="text-sm font-bold text-emerald-600">Rendimento guardado. Podes adicionar outro ou continuar.</p>@endif
    @include('livewire.partials.employment-modal', ['onboardingModal' => true])
    @include('livewire.partials.income-source-modals', ['onboardingModal' => true])
</div>
