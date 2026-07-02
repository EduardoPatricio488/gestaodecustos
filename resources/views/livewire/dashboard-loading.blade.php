<div class="min-h-screen flex flex-col items-center justify-center bg-zinc-100 dark:bg-zinc-900">

    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-10 shadow-xl text-center max-w-md">

        <flux:icon name="building-office" class="size-12 mx-auto text-brand-600 mb-4" />

        <h1 class="text-2xl font-black uppercase tracking-tight dark:text-white">
            A carregar workspace...
        </h1>

        <p class="text-zinc-500 dark:text-zinc-400 mt-2">
            Estamos a preparar o teu ambiente de trabalho.
        </p>

        <p class="text-zinc-500 dark:text-zinc-400 mt-1">
            Se isto demorar, escolhe um workspace manualmente.
        </p>

        <a
            href="{{ route('hub.business.my-profile') }}"
            class="mt-6 inline-block px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-black uppercase tracking-widest transition"
        >
            Selecionar Workspace
        </a>
    </div>

</div>
