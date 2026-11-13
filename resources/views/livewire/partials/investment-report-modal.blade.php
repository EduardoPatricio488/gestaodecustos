<div
    x-cloak
    x-show="showXtbUploadModal"
    x-transition.opacity
    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 p-2 sm:p-5 backdrop-blur-sm"
    @click.self="showXtbUploadModal = false">
    <div
        x-show="showXtbUploadModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        class="relative flex max-h-[95vh] w-full max-w-7xl flex-col overflow-hidden rounded-3xl border border-zinc-200 bg-zinc-50 shadow-2xl dark:border-zinc-700 dark:bg-zinc-950 sm:max-h-[92vh]"
        @click.stop>
        <div class="flex shrink-0 items-center justify-between gap-4 border-b border-zinc-200 bg-white px-5 py-4 dark:border-zinc-800 dark:bg-zinc-900 sm:px-7">
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-[.18em] text-emerald-600 dark:text-emerald-400">Importação inteligente</p>
                <h2 class="mt-1 truncate text-lg font-black text-zinc-900 dark:text-white sm:text-xl">Upload de relatórios</h2>
            </div>
            <button type="button" @click="showXtbUploadModal = false" class="flex size-10 shrink-0 items-center justify-center rounded-xl border border-zinc-200 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="Fechar janela">
                <flux:icon name="x-mark" class="size-5" />
            </button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto p-3 sm:p-6">
            @if(!empty($onboardingModal))
                <p class="mb-4 rounded-xl border border-indigo-200 bg-indigo-50 p-4 text-sm leading-6 text-indigo-800 dark:border-indigo-800 dark:bg-indigo-950/30 dark:text-indigo-200">
                    Na análise XTB, se a IA estiver configurada, um resumo das posições e respetivos valores é enviado ao OpenRouter. Revê os dados antes de confirmar a importação.
                </p>
                <livewire:onboarding-investment-import :key="'onboarding-investment-import-modal'" />
            @else
                <livewire:xtb-investment-import-hub :key="'xtb-investment-import-modal'" />
            @endif
        </div>
    </div>
</div>
