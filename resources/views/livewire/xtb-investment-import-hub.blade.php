<div class="space-y-8">
    @if(!empty($onboardingModal) && $onboardingImportStatus)
        <p role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-200">{{ $onboardingImportStatus }}</p>
    @endif
    <div class="flex flex-wrap gap-2 rounded-2xl border border-zinc-200 bg-white p-1.5 dark:border-zinc-800 dark:bg-zinc-900">
        <button type="button" wire:click="switchProvider('xtb')" class="flex-1 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-wider transition {{ $activeProvider === 'xtb' ? 'bg-emerald-600 text-white shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">XTB</button>
        <button type="button" wire:click="switchProvider('aforronet')" class="flex-1 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-wider transition {{ $activeProvider === 'aforronet' ? 'bg-teal-700 text-white shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">AforroNet</button>
    </div>
    @if($activeProvider === 'xtb')
    <div class="relative overflow-hidden rounded-[2rem] border border-emerald-200/70 dark:border-emerald-900/60 bg-gradient-to-br from-emerald-50 via-white to-teal-50 dark:from-emerald-950/50 dark:via-zinc-950 dark:to-teal-950/30 p-7 sm:p-10">
        <div class="absolute -right-12 -top-16 size-64 rounded-full bg-emerald-400/10 blur-3xl"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-300/70 dark:border-emerald-700/60 bg-white/70 dark:bg-zinc-900/60 px-3 py-1.5 text-[10px] font-black uppercase tracking-[.18em] text-emerald-700 dark:text-emerald-300">
                    <span aria-hidden="true">✦</span> Importação inteligente com IA
                </div>
                <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-zinc-950 dark:text-white">Upload de Investimentos</h1>
                <p class="mt-3 text-sm sm:text-base leading-relaxed text-zinc-600 dark:text-zinc-300">Carrega o relatório da XTB e deixa o Finance Pro identificar os ativos, quantidades, preços médios e valores atuais. Revê tudo antes de sincronizar a tua carteira.</p>
            </div>
            <div class="shrink-0 flex items-center gap-3 rounded-2xl border border-white/80 dark:border-zinc-700 bg-white/80 dark:bg-zinc-900/70 px-5 py-4 shadow-sm">
                <div class="flex size-12 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">
                    <span class="text-2xl" aria-hidden="true">📊</span>
                </div>
                <div><p class="text-xs font-black text-zinc-900 dark:text-white">Relatório XTB</p><p class="text-[10px] text-zinc-500">Excel · XLSX · XLS · CSV</p></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <section class="rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 sm:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="size-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300 flex items-center justify-center"><span class="text-xl" aria-hidden="true">☁</span></div>
                <div><h2 class="font-black text-zinc-900 dark:text-white">Carregar relatório</h2><p class="text-xs text-zinc-500">Seleciona o ficheiro exportado da tua conta XTB.</p></div>
            </div>

            <form wire:submit="analyzeReport" class="space-y-5">
                <label class="group flex min-h-52 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50/70 dark:bg-zinc-950/40 px-5 py-8 text-center transition hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20">
                    <input type="file" wire:model="reportFile" accept=".xlsx,.xls,.csv,.txt" class="sr-only">
                    <span class="mb-3 flex size-14 items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 text-emerald-600 shadow-sm"><span class="text-3xl" aria-hidden="true">📄</span></span>
                    <span class="font-bold text-sm text-zinc-800 dark:text-zinc-100">Clica para escolher ou arrasta o ficheiro</span>
                    <span class="mt-1 text-xs text-zinc-500">Excel ou CSV · máximo 10 MB</span>
                    @if($reportFile)
                        <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-3 py-1.5 text-xs font-bold text-emerald-800 dark:text-emerald-200"><span aria-hidden="true">✓</span> {{ $reportFile->getClientOriginalName() }}</span>
                    @endif
                </label>
                @error('reportFile') <p class="text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="reportFile" class="text-xs font-semibold text-indigo-600">A carregar ficheiro...</div>
                <div class="flex flex-wrap gap-3">
                    <flux:button type="submit" variant="primary" icon="sparkles" class="flex-1 justify-center" wire:loading.attr="disabled" wire:target="analyzeReport,reportFile">
                        <span wire:loading.remove wire:target="analyzeReport">Analisar relatório com IA</span>
                        <span wire:loading wire:target="analyzeReport">A analisar posições...</span>
                    </flux:button>
                    @if(!empty($onboardingModal))
                    <details class="w-full">
                        <summary class="cursor-pointer rounded-xl border border-sky-200 p-4 text-sm font-bold text-sky-700 dark:border-sky-800 dark:text-sky-300">Como obter o relatório na XTB</summary>
                    @endif
                    {{-- GUIA VISUAL: EXPORTAR RELATÓRIO XTB --}}
    <section class="overflow-hidden rounded-3xl border border-sky-200 dark:border-sky-900/60 bg-white dark:bg-zinc-900 shadow-sm">
        <div class="border-b border-sky-100 dark:border-sky-900/50 bg-sky-50/70 dark:bg-sky-950/20 px-6 py-5 sm:px-8">
            <div class="flex items-center gap-3">
                <div class="flex size-11 items-center justify-center rounded-2xl bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300 text-xl" aria-hidden="true">📘</div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[.18em] text-sky-700 dark:text-sky-300">Guia passo a passo</p>
                    <h2 class="mt-1 text-xl font-black text-zinc-950 dark:text-white">Como obter o relatório na XTB</h2>
                </div>
            </div>
            <p class="mt-3 max-w-3xl text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">Segue as imagens pela ordem apresentada para exportares o relatório necessário à importação da tua carteira.</p>
        </div>

        <div class="grid gap-6 p-5 sm:grid-cols-2 lg:grid-cols-2 sm:p-8">
            <article class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950/50">
                <div class="aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-800"><img src="https://raw.githubusercontent.com/EduardoPatricio488/gestaodecustos/main/instru%C3%A7%C3%B5es%20de%20relatorio%20xtb/1.jpeg" alt="Instruções XTB - passo 1" loading="lazy" class="h-full w-full object-contain"></div>
                <div class="p-5"><p class="text-xs font-black uppercase tracking-widest text-sky-600 dark:text-sky-400">Passo 1</p><h3 class="mt-2 text-base font-bold text-zinc-900 dark:text-white">Abre o menu do perfil</h3><p class="mt-2 text-sm leading-relaxed text-zinc-500">Na aplicação XTB, toca no ícone do perfil no canto superior esquerdo.</p></div>
            </article>
            <article class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950/50">
                <div class="aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-800"><img src="https://raw.githubusercontent.com/EduardoPatricio488/gestaodecustos/main/instru%C3%A7%C3%B5es%20de%20relatorio%20xtb/2.jpeg" alt="Instruções XTB - passo 2" loading="lazy" class="h-full w-full object-contain"></div>
                <div class="p-5"><p class="text-xs font-black uppercase tracking-widest text-sky-600 dark:text-sky-400">Passo 2</p><h3 class="mt-2 text-base font-bold text-zinc-900 dark:text-white">Entra no Histórico de operações</h3><p class="mt-2 text-sm leading-relaxed text-zinc-500">No menu, seleciona «Histórico de operações».</p></div>
            </article>
            <article class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950/50">
                <div class="aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-800"><img src="https://raw.githubusercontent.com/EduardoPatricio488/gestaodecustos/main/instru%C3%A7%C3%B5es%20de%20relatorio%20xtb/3.jpeg" alt="Instruções XTB - passo 3" loading="lazy" class="h-full w-full object-contain"></div>
                <div class="p-5"><p class="text-xs font-black uppercase tracking-widest text-sky-600 dark:text-sky-400">Passo 3</p><h3 class="mt-2 text-base font-bold text-zinc-900 dark:text-white">Abre os relatórios de histórico</h3><p class="mt-2 text-sm leading-relaxed text-zinc-500">No ecrã do histórico, toca no ícone de transferência no canto superior direito.</p></div>
            </article>
            <article class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950/50">
                <div class="aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-800"><img src="https://raw.githubusercontent.com/EduardoPatricio488/gestaodecustos/main/instru%C3%A7%C3%B5es%20de%20relatorio%20xtb/4.jpeg" alt="Instruções XTB - passo 4" loading="lazy" class="h-full w-full object-contain"></div>
                <div class="p-5"><p class="text-xs font-black uppercase tracking-widest text-sky-600 dark:text-sky-400">Passo 4</p><h3 class="mt-2 text-base font-bold text-zinc-900 dark:text-white">Cria um novo relatório</h3><p class="mt-2 text-sm leading-relaxed text-zinc-500">Na área «Relatórios de histórico», carrega em «Novo relatório».</p></div>
            </article>
            <article class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950/50">
                <div class="aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-800"><img src="https://raw.githubusercontent.com/EduardoPatricio488/gestaodecustos/main/instru%C3%A7%C3%B5es%20de%20relatorio%20xtb/5.jpeg" alt="Instruções XTB - passo 5" loading="lazy" class="h-full w-full object-contain"></div>
                <div class="p-5"><p class="text-xs font-black uppercase tracking-widest text-sky-600 dark:text-sky-400">Passo 5</p><h3 class="mt-2 text-base font-bold text-zinc-900 dark:text-white">Seleciona «Tudo» e gera</h3><p class="mt-2 text-sm leading-relaxed text-zinc-500">Define o intervalo como «Tudo», confirma a conta e toca em «Gerar relatório».</p></div>
            </article>
            <article class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950/50">
                <div class="aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-800"><img src="https://raw.githubusercontent.com/EduardoPatricio488/gestaodecustos/main/instru%C3%A7%C3%B5es%20de%20relatorio%20xtb/6.jpeg" alt="Instruções XTB - passo 6" loading="lazy" class="h-full w-full object-contain"></div>
                <div class="p-5"><p class="text-xs font-black uppercase tracking-widest text-sky-600 dark:text-sky-400">Passo 6</p><h3 class="mt-2 text-base font-bold text-zinc-900 dark:text-white">Aguarda a geração do relatório</h3><p class="mt-2 text-sm leading-relaxed text-zinc-500">Quando terminar, entra em «Relatórios de histórico» para localizar e descarregar o relatório. Depois extrai o ZIP e carrega o ficheiro Excel acima.</p></div>
            </article>
        </div>
        <div class="mx-5 mb-5 flex gap-3 rounded-2xl border border-amber-200 dark:border-amber-900/60 bg-amber-50 dark:bg-amber-950/20 p-4 sm:mx-7 sm:mb-7">
            <span class="text-lg" aria-hidden="true">💡</span>
            <p class="text-xs leading-relaxed text-amber-900 dark:text-amber-200"><strong>Importante:</strong> não envies o ZIP diretamente. Depois de o extraires, seleciona o ficheiro Excel (.xlsx ou .xls) que está dentro da pasta extraída. Também são aceites ficheiros CSV.</p>
        </div>
    </section>
                    @if(!empty($onboardingModal))
                    </details>
                    @endif

    @if($previewReady)
                        <flux:button type="button" variant="ghost" wire:click="clearReport">Limpar</flux:button>
                    @endif
                </div>
            </form>
        </section>

        <aside class="space-y-4">
            <div class="rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Como funciona</h3>
                <ol class="mt-5 space-y-4">
                    <li class="flex gap-3"><span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-black text-emerald-700">1</span><span class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-300"><strong class="block text-zinc-900 dark:text-white">Carregar</strong>Exporta o relatório completo na XTB.</span></li>
                    <li class="flex gap-3"><span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-black text-emerald-700">2</span><span class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-300"><strong class="block text-zinc-900 dark:text-white">Analisar</strong>O sistema lê a tabela de posições abertas e valida os dados.</span></li>
                    <li class="flex gap-3"><span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-black text-emerald-700">3</span><span class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-300"><strong class="block text-zinc-900 dark:text-white">Confirmar</strong>Revê os ativos antes de os guardar na carteira.</span></li>
                </ol>
            </div>
            <div class="rounded-2xl border border-amber-200 dark:border-amber-900/60 bg-amber-50/70 dark:bg-amber-950/20 p-5">
                <div class="flex gap-2 text-amber-800 dark:text-amber-300"><span aria-hidden="true">🛡</span><p class="text-xs font-black">Importação sem duplicados</p></div>
                <p class="mt-2 text-xs leading-relaxed text-amber-900/80 dark:text-amber-200/80">As posições abertas são a fonte principal. As compras históricas e posições fechadas não são somadas à carteira atual. Uma nova importação atualiza os ativos XTB já existentes.</p>
            </div>
        </aside>
    </div>

    @if($previewReady)
        <section class="overflow-hidden rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
            <div class="flex flex-col gap-4 border-b border-zinc-100 dark:border-zinc-800 p-6 sm:flex-row sm:items-center sm:justify-between">
                <div><div class="flex items-center gap-2"><span class="text-lg text-emerald-600" aria-hidden="true">✓</span><h2 class="font-black text-zinc-900 dark:text-white">Pré-visualização dos investimentos</h2></div><p class="mt-1 text-xs text-zinc-500">Confirma os valores encontrados antes de importar.</p></div>
                @if($reportDate)<span class="text-xs text-zinc-500">Relatório de {{ $reportDate }}</span>@endif
            </div>

            <div class="grid grid-cols-2 gap-3 p-5 sm:grid-cols-3">
                <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4"><p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Posições identificadas</p><p class="mt-1 text-2xl font-black text-zinc-900 dark:text-white">{{ $reportSummary['positions_count'] ?? count($positions) }}</p></div>
                <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4"><p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Valor atual total</p><p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format((float)($reportSummary['open_value'] ?? 0), 2, ',', ' ') }} €</p></div>
                <div class="col-span-2 sm:col-span-1 rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4"><p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Lucro não realizado</p><p class="mt-1 text-2xl font-black {{ ($reportSummary['open_profit'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format((float)($reportSummary['open_profit'] ?? 0), 2, ',', ' ') }} €</p></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-950 text-[10px] uppercase tracking-wider text-zinc-500"><tr><th class="px-5 py-3">Ativo</th><th class="px-5 py-3">Tipo</th><th class="px-5 py-3 text-right">Quantidade</th><th class="px-5 py-3 text-right">Preço médio</th><th class="px-5 py-3 text-right">Preço atual</th><th class="px-5 py-3 text-right">Valor atual</th><th class="px-5 py-3 text-right">Lucro estimado</th></tr></thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($positions as $position)
                            <tr>
                                <td class="px-5 py-4"><p class="font-bold text-zinc-900 dark:text-white">{{ $position['name'] }}</p><p class="text-xs text-zinc-500">{{ $position['symbol'] }}@if($position['xtb_position_id']) · Posição {{ $position['xtb_position_id'] }}@endif</p></td>
                                <td class="px-5 py-4"><span class="rounded-full bg-indigo-50 dark:bg-indigo-950/50 px-2.5 py-1 text-[10px] font-bold text-indigo-700 dark:text-indigo-300">{{ $position['type'] }}</span></td>
                                <td class="px-5 py-4 text-right font-semibold tabular-nums">{{ number_format((float)$position['quantity'], 4, ',', ' ') }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format((float)$position['average_price'], 4, ',', ' ') }} €</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format((float)$position['current_price'], 4, ',', ' ') }} €</td>
                                <td class="px-5 py-4 text-right font-black tabular-nums">{{ number_format((float)$position['current_value'], 2, ',', ' ') }} €</td>
                                <td class="px-5 py-4 text-right font-bold tabular-nums {{ $position['profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $position['profit'] >= 0 ? '+' : '' }}{{ number_format((float)$position['profit'], 2, ',', ' ') }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($aiSummary)
                <div class="mx-5 my-5 rounded-2xl border border-violet-200 dark:border-violet-900/60 bg-violet-50/70 dark:bg-violet-950/20 p-5">
                    <div class="flex items-center gap-2 text-violet-700 dark:text-violet-300"><span aria-hidden="true">✦</span><p class="text-xs font-black uppercase tracking-wider">Validação do assistente IA</p></div>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $aiSummary }}</p>
                </div>
            @endif

            <div class="flex flex-col gap-3 border-t border-zinc-100 dark:border-zinc-800 p-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-zinc-500">As posições XTB são identificadas individualmente. A importação atualiza as existentes e cria as que ainda não estão registadas.</p>
                <div class="flex gap-3">
                    <flux:button type="button" variant="ghost" wire:click="clearReport">Cancelar</flux:button>
                    <flux:button type="button" variant="primary" icon="arrow-down-tray" wire:click="importPositions" wire:loading.attr="disabled" wire:target="importPositions,reportFile,analyzeReport">
                        <span wire:loading.remove wire:target="importPositions">Confirmar e importar</span><span wire:loading wire:target="importPositions">A importar...</span>
                    </flux:button>
                </div>
            </div>
        </section>
    @endif

    @else
        <div class="space-y-6">
            <section class="rounded-3xl border border-teal-200 bg-gradient-to-br from-teal-50 via-white to-cyan-50 p-6 dark:border-teal-900/60 dark:from-teal-950/40 dark:via-zinc-950 dark:to-cyan-950/20 sm:p-8">
                <div class="flex items-start gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-teal-100 text-2xl dark:bg-teal-900/60">🏦</div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[.18em] text-teal-700 dark:text-teal-300">Importação e valorização automática</p>
                        <h2 class="mt-1 text-2xl font-black text-zinc-950 dark:text-white">Certificados de Aforro — Série E</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">Carrega o PDF do extrato. O Finance Pro lê cada subscrição, valida os valores e atualiza as capitalizações trimestrais com as taxas históricas disponíveis, sem precisares de importar o extrato todos os meses.</p>
                    </div>
                </div>
                <div class="mt-5 rounded-2xl border border-teal-200/80 bg-white/80 p-4 text-xs leading-relaxed text-teal-900 dark:border-teal-800 dark:bg-zinc-900/70 dark:text-teal-200"><strong>Nota:</strong> os juros são capitalizados trimestralmente, não em tempo real. O cálculo usa um histórico público que reproduz a metodologia do IGCP a partir da Euribor a 3 meses; não é uma API oficial do IGCP. Se o histórico estiver indisponível ou desatualizado, o saldo não é alterado.</div>
            </section>

            <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <form wire:submit="analyzeAforroNetFile" class="space-y-4">
                    <label class="block text-sm font-bold text-zinc-800 dark:text-zinc-100">Ficheiro PDF do extrato AforroNet</label>
                    <label class="group flex min-h-52 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-teal-300 bg-teal-50/50 px-5 py-8 text-center transition hover:border-teal-500 hover:bg-teal-50 dark:border-teal-800 dark:bg-zinc-950/40 dark:hover:bg-teal-950/20">
                        <input type="file" wire:model="aforroNetFile" accept=".pdf,application/pdf" class="sr-only">
                        <span class="mb-3 flex size-14 items-center justify-center rounded-2xl bg-white text-teal-700 shadow-sm dark:bg-zinc-900"><span class="text-3xl" aria-hidden="true">📄</span></span>
                        <span class="font-bold text-sm text-zinc-800 dark:text-zinc-100">Clica para escolher ou arrasta o PDF</span>
                        <span class="mt-1 text-xs text-zinc-500">Documento PDF · máximo 10 MB</span>
                        @if($aforroNetFile)
                            <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-teal-100 px-3 py-1.5 text-xs font-bold text-teal-800 dark:bg-teal-900/40 dark:text-teal-200"><span aria-hidden="true">✓</span> {{ $aforroNetFile->getClientOriginalName() }}</span>
                        @endif
                    </label>
                    @error('aforroNetFile') <p class="text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="aforroNetFile" class="text-xs font-semibold text-teal-700 dark:text-teal-300">A carregar PDF...</div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="analyzeAforroNetFile,aforroNetFile" class="w-full rounded-xl bg-teal-700 px-5 py-3.5 text-sm font-black text-white transition hover:bg-teal-800 disabled:opacity-60">
                        <span wire:loading.remove wire:target="analyzeAforroNetFile">Analisar PDF e validar subscrições</span><span wire:loading wire:target="analyzeAforroNetFile">A analisar documento...</span>
                    </button>
                </form>
            </section>

            @if($aforroNetPreviewReady)
                <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="border-b border-zinc-100 p-5 dark:border-zinc-800">
                        <h3 class="font-black text-zinc-900 dark:text-white">Pré-visualização das subscrições</h3>
                        <p class="mt-1 text-xs text-zinc-500">Extrato de {{ $aforroNetPosition['report_date'] }} · {{ count($aforroNetSubscriptions) }} subscrições individuais</p>
                    </div>
                    <div class="grid gap-3 p-5 sm:grid-cols-3">
                        <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-950"><p class="text-[10px] font-bold uppercase text-zinc-500">Unidades</p><p class="mt-2 text-xl font-black text-zinc-900 dark:text-white">{{ number_format($aforroNetPosition['quantity'], 0, ',', ' ') }}</p></div>
                        <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-950"><p class="text-[10px] font-bold uppercase text-zinc-500">Valor atual</p><p class="mt-2 text-xl font-black text-teal-700 dark:text-teal-300">{{ number_format($aforroNetPosition['current_value'], 2, ',', ' ') }} €</p></div>
                        <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-950"><p class="text-[10px] font-bold uppercase text-zinc-500">Valorização acumulada</p><p class="mt-2 text-xl font-black text-emerald-600">+{{ number_format($aforroNetPosition['profit'], 2, ',', ' ') }} €</p></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[700px] text-left text-sm">
                            <thead class="bg-zinc-50 text-[10px] uppercase tracking-wider text-zinc-500 dark:bg-zinc-950"><tr><th class="px-5 py-3">Data</th><th class="px-5 py-3">N.º subscrição</th><th class="px-5 py-3 text-right">Unidades</th><th class="px-5 py-3 text-right">Valor unitário</th><th class="px-5 py-3 text-right">Valor atual</th></tr></thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @foreach($aforroNetSubscriptions as $subscription)
                                    <tr>
                                        <td class="px-5 py-4">{{ \Illuminate\Support\Carbon::parse($subscription['operation_date'])->format('d/m/Y') }}</td>
                                        <td class="px-5 py-4 font-semibold">{{ $subscription['subscription_number'] }}</td>
                                        <td class="px-5 py-4 text-right tabular-nums">{{ number_format($subscription['quantity'], 0, ',', ' ') }}</td>
                                        <td class="px-5 py-4 text-right tabular-nums">{{ number_format($subscription['current_price'], 5, ',', ' ') }} €</td>
                                        <td class="px-5 py-4 text-right font-black tabular-nums">{{ number_format($subscription['current_value'], 2, ',', ' ') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-col gap-3 border-t border-zinc-100 p-5 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-zinc-500">A soma das subscrições foi validada contra o total do PDF. O capital de aquisição é 1,00 € por unidade.</p>
                        <div class="flex gap-3">
                            <button type="button" wire:click="clearAforroNet" class="rounded-xl px-4 py-2 text-xs font-bold text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">Cancelar</button>
                            <button type="button" wire:click="importAforroNetPosition" wire:loading.attr="disabled" wire:target="importAforroNetPosition,aforroNetFile,analyzeAforroNetFile" class="rounded-xl bg-teal-700 px-5 py-2.5 text-xs font-black text-white hover:bg-teal-800 disabled:opacity-60">
                                <span wire:loading.remove wire:target="importAforroNetPosition">Confirmar e importar</span><span wire:loading wire:target="importAforroNetPosition">A importar...</span>
                            </button>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    @endif

</div>
