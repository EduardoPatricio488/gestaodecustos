<div class="grid grid-cols-2 gap-2 rounded-2xl bg-zinc-100 p-1 dark:bg-zinc-800">
                        <button type="button" wire:click="$set('salaryFormMode', 'manual')" class="rounded-xl px-3 py-3 text-[10px] font-black uppercase transition {{ $salaryFormMode === 'manual' ? 'bg-white text-indigo-600 shadow dark:bg-zinc-900' : 'text-zinc-500' }}">Preencher manualmente</button>
                        <button type="button" wire:click="$set('salaryFormMode', 'scanner')" class="rounded-xl px-3 py-3 text-[10px] font-black uppercase transition {{ $salaryFormMode === 'scanner' ? 'bg-white text-indigo-600 shadow dark:bg-zinc-900' : 'text-zinc-500' }}">Scanner do contrato IA</button>
                    </div>

                    @if($salaryFormMode === 'scanner')
                        <section class="space-y-4 rounded-3xl border border-indigo-200 bg-indigo-50/70 p-5 dark:border-indigo-900/50 dark:bg-indigo-950/20">
                            <div>
                                <h3 class="text-sm font-black uppercase text-indigo-700 dark:text-indigo-300">Ler fotografia do contrato</h3>
                                <p class="mt-1 text-xs leading-5 text-zinc-600 dark:text-zinc-300">Carrega uma fotografia nítida do contrato. A IA identifica os dados profissionais, remuneração, subsídios e benefícios e preenche o formulário. Podes rever e corrigir tudo antes de confirmar.</p>
                                <p class="mt-2 text-xs leading-5 text-zinc-600 dark:text-zinc-300">Ao analisar, a fotografia e os dados visíveis no contrato são enviados ao fornecedor de IA (OpenRouter).</p>
                            </div>
                            <label class="flex min-h-36 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-indigo-300 bg-white p-5 text-center dark:border-indigo-800 dark:bg-zinc-900">
                                <flux:icon name="camera" class="mb-2 size-8 text-indigo-500" />
                                <span class="text-xs font-black text-zinc-700 dark:text-zinc-200">Escolher fotografia do contrato</span>
                                <span class="mt-1 text-[10px] text-zinc-500">JPG, PNG ou WEBP · máximo 10 MB</span>
                                <input type="file" wire:model="contractScanPhoto" accept="image/jpeg,image/png,image/webp" capture="environment" class="sr-only">
                            </label>
                            @if($contractScanPhoto)
                                <p class="text-xs font-semibold text-indigo-700 dark:text-indigo-300">{{ $contractScanPhoto->getClientOriginalName() }}</p>
                                <img src="{{ $contractScanPhoto->temporaryUrl() }}" alt="Pré-visualização do contrato" class="mx-auto max-h-64 rounded-xl border border-zinc-200 object-contain dark:border-zinc-700">
                            @endif
                            @error('contractScanPhoto')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            <div wire:loading wire:target="contractScanPhoto" class="text-xs font-semibold text-indigo-600">A carregar fotografia...</div>
                            <button type="button" wire:click="scanEmploymentContract" wire:loading.attr="disabled" wire:target="scanEmploymentContract,contractScanPhoto" @disabled(! $contractScanPhoto || $contractScanProcessing) class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-xs font-black uppercase text-white shadow-lg shadow-indigo-600/20 disabled:cursor-not-allowed disabled:opacity-50">
                                <flux:icon name="sparkles" class="size-4" />
                                <span wire:loading.remove wire:target="scanEmploymentContract">Analisar contrato com IA</span>
                                <span wire:loading wire:target="scanEmploymentContract">A analisar fotografia...</span>
                            </button>
                            <p class="text-[10px] leading-4 text-zinc-500">A análise pode falhar em fotografias pouco nítidas. Os dados só são guardados quando confirmares o contrato.</p>
                        </section>
                    @else
                    {{-- DESCRIÇÃO --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-indigo-600 z-10">Nome da Empresa</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Salário Mensal - Empresa X" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-indigo-500 transition-all">
                    </div>

                    <section class="space-y-3 rounded-3xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Dados profissionais e contrato</h3>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input type="text" wire:model="recPosition" placeholder="Cargo / função" class="h-11 rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                            <input type="text" wire:model="recDepartment" placeholder="Departamento" class="h-11 rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                            <select wire:model.live="recContractType" class="h-11 rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                <option value="sem_termo">Contrato sem termo</option><option value="termo_certo">Termo certo</option><option value="termo_incerto">Termo incerto</option><option value="estagio">Estágio</option><option value="prestacao_servicos">Prestação de serviços</option><option value="outro">Outro</option>
                            </select>
                            <select wire:model="recContractStatus" class="h-11 rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                <option value="active">Ativo</option><option value="ended">Terminado</option><option value="suspended">Suspenso</option>
                            </select>
                            <label class="text-[9px] font-bold uppercase text-zinc-500">Data de início<input type="date" wire:model="recStartDate" class="mt-1 h-11 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"></label>
                            @if($recContractType !== 'sem_termo')
                                <label class="text-[9px] font-bold uppercase text-zinc-500">Data de fim<input type="date" wire:model="recEndDate" class="mt-1 h-11 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"></label>
                            @endif
                            @unless($editingFixedId)
                                <label class="text-[9px] font-bold uppercase text-zinc-500">Estado da retenção<select wire:model.live="recWithholdingStatus" class="mt-1 h-11 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"><option value="normal">Retenção normal estimada</option><option value="exempt">Sem retenção na fonte</option></select></label>
                            @endunless
                        </div>
                        <p class="text-[10px] text-zinc-500">O dia habitual de pagamento é configurado abaixo. O cálculo fiscal disponível é uma estimativa para o Continente em 2026.</p>
                    </section>

                    {{-- VALORES BRUTO / LÍQUIDO --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Valor Bruto (€)</label>
                            <input type="number" step="0.01" wire:model.live="recSalaryGross" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Resultado Líquido (€)</label>
                            <input type="number" step="0.01" wire:model.live="recAmount" class="w-full h-14 bg-emerald-50 dark:bg-emerald-900/10 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none" readonly>
                        </div>
                    </div>
                    {{-- IRS JOVEM --}}
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/10 rounded-3xl border border-indigo-100 dark:border-indigo-900/30 space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="recIrsJovem" class="mt-0.5 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                            <span>
                                <span class="block text-sm font-black text-indigo-700 dark:text-indigo-300">Tenho IRS Jovem</span>
                                <span class="block text-[10px] text-zinc-500 mt-1">A retenção estimada continua a ser apresentada separadamente. A isenção não é aplicada como redução direta da taxa.</span>
                            </span>
                        </label>
                        @if($recIrsJovem)
                            <div>
                                <label class="text-[9px] font-black text-zinc-500 uppercase block mb-1">Ano de benefício</label>
                                <select wire:model.live="recIrsJovemYear" class="w-full h-11 bg-white dark:bg-zinc-800 border border-indigo-100 dark:border-indigo-900/30 rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                                    @for($year = 1; $year <= 10; $year++)
                                        <option value="{{ $year }}">{{ $year }}.º ano</option>
                                    @endfor
                                </select>
                            </div>
                            @if($recSalaryGross > 0)
                                <div class="rounded-xl border border-indigo-200 bg-white/80 p-3 dark:border-indigo-800 dark:bg-zinc-900/70">
                                    <p class="text-xs font-black text-indigo-700 dark:text-indigo-300">
                                        {{ number_format($calculatedIRSJovemRate * 100, 0) }}% de isenção no {{ $recIrsJovemYear }}.º ano
                                    </p>
                                    <p class="mt-1 text-[10px] text-zinc-600 dark:text-zinc-300">
                                        Remuneração mensal elegível isenta estimada: {{ number_format($calculatedIRSJovemExemptIncome, 2, ',', ' ') }} €.
                                    </p>
                                    <p class="mt-1 text-[10px] font-bold text-indigo-700 dark:text-indigo-300">
                                        IRS estimado após a isenção: {{ number_format((float) $calculatedIRS, 2, ',', ' ') }} €/mês.
                                        Líquido estimado com o benefício: ~{{ number_format((float) $recAmount, 2, ',', ' ') }} €/mês.
                                    </p>
                                    <p class="mt-1 text-[9px] text-zinc-500">A estimativa considera a parcela isenta e aplica a tabela de retenção apenas à remuneração não isenta. A taxa da tabela não é reduzida diretamente; o IRS Jovem continua sujeito ao apuramento anual.</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- SUBSÍDIOS DE FÉRIAS E NATAL --}}
                    <div class="p-4 bg-sky-50 dark:bg-sky-900/10 rounded-3xl border border-sky-100 dark:border-sky-900/30 space-y-3">
                        <div>
                            <p class="text-[10px] font-black uppercase text-sky-700 dark:text-sky-300 tracking-widest">Subsídios de Férias e de Natal</p>
                            <p class="text-[10px] text-zinc-500 mt-1">Define o valor bruto de cada subsídio e como é pago.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <label class="text-[9px] font-black text-zinc-500 uppercase block">Subsídio de férias</label>
                                <label class="flex items-center gap-2 text-xs font-bold text-zinc-700 dark:text-zinc-200 cursor-pointer">
                                    <input type="checkbox" wire:model.live="recHolidayAllowanceSameAsSalary" class="rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                                    Igual ao salário bruto
                                </label>
                                @unless($recHolidayAllowanceSameAsSalary)
                                    <input type="number" min="0" step="0.01" wire:model.live="recHolidayAllowanceGross" placeholder="Valor bruto (€)" class="w-full h-11 bg-white dark:bg-zinc-800 border border-sky-100 dark:border-sky-900/30 rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                                @endunless
                            </div>
                            <div class="space-y-3">
                                <label class="text-[9px] font-black text-zinc-500 uppercase block">Subsídio de Natal</label>
                                <label class="flex items-center gap-2 text-xs font-bold text-zinc-700 dark:text-zinc-200 cursor-pointer">
                                    <input type="checkbox" wire:model.live="recChristmasAllowanceSameAsSalary" class="rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                                    Igual ao salário bruto
                                </label>
                                @unless($recChristmasAllowanceSameAsSalary)
                                    <input type="number" min="0" step="0.01" wire:model.live="recChristmasAllowanceGross" placeholder="Valor bruto (€)" class="w-full h-11 bg-white dark:bg-zinc-800 border border-sky-100 dark:border-sky-900/30 rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                                @endunless
                            </div>
                        </div>

                        <label class="flex items-start gap-3 pt-4 border-t border-sky-100 dark:border-sky-900/30 cursor-pointer">
                            <input type="checkbox" wire:model.live="recAllowancesInDuodecimos" class="mt-0.5 rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                            <span>
                                <span class="block text-xs font-black text-zinc-700 dark:text-zinc-200">Receber em duodécimos</span>
                                <span class="block text-[10px] text-zinc-500 mt-1">Os dois subsídios são divididos por 12 e incluídos na estimativa mensal.</span>
                            </span>
                        </label>
                        @unless($recAllowancesInDuodecimos)
                            <p class="text-[10px] text-zinc-500">Recebidos por inteiro nos meses correspondentes; não incluídos na estimativa mensal.</p>
                        @endunless
                        @if($recAllowancesInDuodecimos)
                            @php
                                $monthlyDuodecimos = (
                                    ($recHolidayAllowanceSameAsSalary ? (float) $recSalaryGross : (float) $recHolidayAllowanceGross)
                                    + ($recChristmasAllowanceSameAsSalary ? (float) $recSalaryGross : (float) $recChristmasAllowanceGross)
                                ) / 12;
                            @endphp
                            <p class="text-[10px] font-bold text-sky-700 dark:text-sky-300">
                                Acréscimo bruto mensal estimado: + {{ number_format($monthlyDuodecimos, 2, ',', ' ') }} €
                            </p>
                        @endif
                    </div>

                    {{-- SUBSIDIO ALIMENTAÇÃO --}}
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-3xl border border-amber-100 dark:border-amber-900/30">
                        <p class="text-[10px] font-black uppercase text-amber-600 mb-4 ml-1 tracking-widest">Subsídio de Alimentação</p>
                        <p class="text-[10px] text-amber-800 dark:text-amber-200 mb-4">
                            A estimativa anual considera 11 meses: normalmente não há subsídio de alimentação durante o mês de férias. O valor por dia e os dias trabalhados são editáveis.
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">Valor por dia</label>
                                <input type="number" step="0.01" wire:model.live="recMealAllowance" class="w-full h-11 bg-white dark:bg-zinc-800 border-none rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                            </div>
                            <div class="relative">
                                <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">Dias trabalhados</label>
                                <input type="number" wire:model.live="recWorkingDays" class="w-full h-11 bg-white dark:bg-zinc-800 border-none rounded-xl px-4 font-bold text-sm text-center text-zinc-900 dark:text-white">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">Onde entra o subsídio de alimentação?</label>
                            <select wire:model.live="recMealDestination" class="w-full h-11 bg-white dark:bg-zinc-800 border border-amber-100 dark:border-amber-900/30 rounded-xl px-4 font-bold text-sm text-zinc-900 dark:text-white">
                                <option value="meal_card">Cartão de refeição separado</option>
                                <optgroup label="Banco">
                                    @foreach($this->bankAccounts->where('user_id', auth()->id()) as $acc)
                                        <option value="bank:{{ $acc->id }}">{{ $acc->name }}</option>
                                    @endforeach
                                </optgroup>
                                <option value="cash">Dinheiro físico</option>
                            </select>
                            <p class="text-[9px] text-zinc-500 mt-2">Escolhe cartão de refeição separado, uma conta bancária ou dinheiro físico.</p>
                        </div>
                        <p class="text-[10px] font-bold text-amber-700 dark:text-amber-300 mt-3">
                            Estimativa anual ({{ number_format($recMealAllowance * $recWorkingDays, 2, ',', ' ') }} € × 11): {{ number_format($recMealAllowance * $recWorkingDays * 11, 2, ',', ' ') }} €
                        </p>
                    </div>

                    {{-- QUADRO DE AUDITORIA (IDENTICO AO ONBOARDING) --}}
                    @if($recSalaryGross > 0)
                    <div class="p-4 bg-zinc-950 text-white rounded-[2rem] border border-white/10 shadow-2xl space-y-3 animate-in fade-in zoom-in-95">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 italic">Cálculo de Salário</h3>
                            <span class="text-[9px] font-black bg-emerald-500/10 px-2 py-0.5 rounded text-emerald-400 uppercase">Estimativa 2026</span>
                        </div>
                        <div class="space-y-2 font-mono text-[11px]">
                            <div class="flex justify-between text-zinc-400">
                                <span>SEGURANÇA SOCIAL (11%)</span>
                                <span class="font-bold text-red-400">- {{ number_format($calculatedSS, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>{{ $recIrsJovem ? 'IRS JOVEM (RETENÇÃO ESTIMADA)' : 'IRS NORMAL (RETENÇÃO ESTIMADA)' }}</span>
                                <span class="font-bold text-red-400">- {{ number_format($calculatedIRS, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>SUBSÍDIO DE ALIMENTAÇÃO</span>
                                <span class="font-bold text-emerald-400">+ {{ number_format($calculatedSA, 2, ',', ' ') }} €</span>
                            </div>
                            @if($recAllowancesInDuodecimos)
                                <div class="flex justify-between text-zinc-400">
                                    <span>DUODÉCIMOS (BRUTO)</span>
                                    <span class="font-bold text-emerald-400">+ {{ number_format($monthlyDuodecimos, 2, ',', ' ') }} €</span>
                                </div>
                            @endif
                        </div>                    </div>
                    @endif

                    {{-- DIA DE PAGAMENTO + DESTINO --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-indigo-600 z-10">Onde entra este valor?</label>
                            <select wire:model.live="recSalaryDestination" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-indigo-500 transition-all">
                                <option value="cash">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts->where('user_id', auth()->id()) as $acc)
                                    <option value="bank:{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <label class="flex items-start gap-3 p-4 bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30 rounded-2xl cursor-pointer">
                        <input type="checkbox" wire:model="recSalaryReceivedThisMonth" class="mt-0.5 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                        <span>
                            <span class="block text-xs font-black text-indigo-700 dark:text-indigo-300">Já recebi este salário este mês</span>
                            <span class="block text-[10px] text-zinc-500 mt-1">Esta indicação fica associada ao mês atual.</span>
                        </span>
                    </label>
                    <div>
                        <label class="text-[9px] font-black uppercase text-zinc-500">Benefícios e regalias do contrato</label>
                        <textarea wire:model="recAdditionalBenefits" rows="3" maxlength="2000" placeholder="Ex.: seguro de saúde, prémio anual, formação, teletrabalho, viatura, passe de transporte..." class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></textarea>
                        @error('recAdditionalBenefits')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    @endif
