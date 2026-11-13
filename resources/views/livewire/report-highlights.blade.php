<section id="destaques-diagnostico" class="rounded-3xl border border-violet-200 bg-white p-5 shadow-sm dark:border-violet-900/60 dark:bg-zinc-900 md:p-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="rounded-xl bg-violet-50 p-2 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400"><flux:icon name="sparkles" class="size-5" /></span>
                <h2 class="text-xl font-black text-zinc-900 dark:text-white">Destaques do diagnóstico</h2>
            </div>
            <p class="mt-2 text-sm leading-6 text-zinc-500">A opinião, os cenários e os conselhos do teu último relatório, reunidos num só lugar.</p>
        </div>
        @if($workspace)
            <a href="#diagnostico-financeiro" class="inline-flex items-center gap-1 text-xs font-bold text-violet-600 hover:underline dark:text-violet-400">
                Ver diagnóstico completo <flux:icon name="arrow-up" class="size-4" />
            </a>
        @endif
    </div>

    @if($reportSections)
        @if($reportNeedsReview)
            <p role="alert" class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-300">Este relatório foi gerado antes da verificação de valores. Atualiza-o antes de utilizar estas conclusões.</p>
        @endif
        <div class="mt-5 grid gap-4 lg:grid-cols-3">
            @foreach([
                ['o que penso', 'O que penso', 'chat-bubble-left-right'],
                ['cenarios', 'Cenários', 'chart-bar'],
                ['o que aconselho', 'O que aconselho', 'light-bulb'],
            ] as [$key, $title, $icon])
                <article class="rounded-2xl border border-zinc-200 bg-zinc-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                    <h3 class="flex items-center gap-2 text-sm font-black text-violet-700 dark:text-violet-300">
                        <flux:icon name="{{ $icon }}" class="size-4" /> {{ $title }}
                    </h3>
                    <div class="mt-3 text-sm leading-6 text-zinc-700 dark:text-zinc-300 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-2 [&_p]:mb-2 [&_strong]:font-bold">
                        {!! Str::markdown($reportSections[$key], ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
                    </div>
                </article>
            @endforeach
        </div>
        <p class="mt-4 text-xs leading-5 text-zinc-500">São excertos do relatório guardado, não uma nova análise. Atualiza o diagnóstico depois de alterares os teus registos.</p>
    @else
        <div class="mt-5 rounded-2xl border border-dashed border-zinc-300 p-5 dark:border-zinc-700">
            @if(!$workspace)
                <p class="text-sm text-zinc-500">Seleciona um workspace para consultares o diagnóstico.</p>
            @elseif($reportStatus === 'missing')
                <p class="text-sm text-zinc-500">Ainda não existe um diagnóstico guardado. Gera um relatório para veres aqui os seus destaques.</p>
            @else
                <p class="text-sm text-zinc-500">Não foi possível extrair estes tópicos do relatório guardado. Consulta o diagnóstico completo ou atualiza-o.</p>
            @endif
        </div>
    @endif
    <div class="mt-5 border-t border-zinc-100 pt-4 dark:border-zinc-800">
        <a href="{{ route('profile.edit') }}#memoria-ia" class="text-xs font-semibold text-zinc-500 hover:text-violet-600 hover:underline dark:text-zinc-400">Privacidade e dados guardados</a>
    </div>
</section>
