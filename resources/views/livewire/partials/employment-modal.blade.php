{{-- MODAL: NOVO CONTRATO DE TRABALHO (COM AUDITORIA)             --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-show-emprego.window="show()"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-close-emprego.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 flex max-h-[88vh] w-full max-w-2xl flex-col overflow-hidden rounded-[2.5rem] border border-zinc-200 bg-white shadow-xl dark:border-zinc-800 dark:bg-zinc-900">

            <form wire:submit.prevent="saveEmployment" class="flex min-h-0 flex-col">
                {{-- HEADER --}}
                <div class="shrink-0 border-b border-zinc-100 p-5 dark:border-zinc-800 sm:p-6">
                    <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="briefcase" class="size-6" />
                    </div>                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white">{{ $editingFixedId ? 'Editar Salário' : 'Configurar Salário' }}</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Cálculo Automático de Impostos</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5 custom-scrollbar sm:p-6">

                    @include('livewire.partials.onboarding-validation-errors')
                    @include('livewire.partials.salary-form')
                </div>

                {{-- FOOTER --}}
                <div class="shrink-0 border-t border-zinc-100 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mb-2 flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-2 dark:border-emerald-900/50 dark:bg-emerald-950/30">
                        <span class="text-xs font-black uppercase tracking-wide text-emerald-800 dark:text-emerald-300">Líquido final mensal</span>
                        <span class="text-2xl font-black tabular-nums text-emerald-700 dark:text-emerald-300">{{ number_format((float) $recAmount, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-12 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-indigo-500/20 transition-all">
                        Confirmar Contrato 💼
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

