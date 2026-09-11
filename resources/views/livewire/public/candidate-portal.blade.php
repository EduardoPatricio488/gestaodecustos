<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-white">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
        <div class="mb-6 flex flex-col gap-4 rounded-[2rem] border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-xl font-black text-white shadow-lg shadow-emerald-500/20">
                    {{ strtoupper(substr($candidate->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                        Portal de Carreira
                    </div>
                    <h1 class="mt-1 text-xl font-black tracking-tight sm:text-2xl">Olá, {{ $candidate->name }} 👋</h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">O teu perfil profissional e as tuas candidaturas num só lugar.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/" class="inline-flex items-center gap-2 rounded-xl bg-zinc-100 px-4 py-2.5 text-xs font-bold text-zinc-700 transition hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                    <flux:icon name="home" class="size-4" /> Início
                </a>
                <button type="button" wire:click="logout" class="inline-flex items-center gap-2 rounded-xl bg-red-500/10 px-4 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-500/15 dark:text-red-400">
                    <flux:icon name="arrow-right-start-on-rectangle" class="size-4" /> Terminar sessão
                </button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[250px_minmax(0,1fr)]">
            <aside class="h-fit rounded-[2rem] border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:sticky lg:top-6">
                <div class="mb-3 px-3 pt-2 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Área pessoal</div>
                @foreach([
                    'overview' => ['icon' => 'squares-2x2', 'label' => 'Visão geral'],
                    'profile' => ['icon' => 'user-circle', 'label' => 'Perfil profissional'],
                    'jobs' => ['icon' => 'briefcase', 'label' => 'Oportunidades'],
                    'applications' => ['icon' => 'clipboard-document-check', 'label' => 'As minhas candidaturas'],
                ] as $section => $item)
                    <button type="button" wire:click="$set('activeSection', '{{ $section }}')" class="mb-1 flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-bold transition {{ $activeSection === $section ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/15' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
                        <flux:icon name="{{ $item['icon'] }}" class="size-5" />
                        {{ $item['label'] }}
                    </button>
                @endforeach
            </aside>

            <main class="min-w-0">
                @if($activeSection === 'overview')
                    <section class="space-y-6">
                        <div class="relative overflow-hidden rounded-[2.25rem] bg-zinc-950 p-7 text-white shadow-xl sm:p-10">
                            <div class="pointer-events-none absolute -right-20 -top-20 size-72 rounded-full bg-emerald-500/20 blur-3xl"></div>
                            <div class="relative max-w-3xl">
                                <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-emerald-300">Perfil de recrutamento</span>
                                <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">Prepara o teu perfil para as melhores oportunidades.</h2>
                                <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-300">Mantém o teu CV, experiência, competências e preferências atualizados. As empresas conseguem receber uma candidatura mais completa e profissional.</p>
                                <div class="mt-6 flex flex-wrap gap-3">
                                    <button type="button" wire:click="$set('activeSection', 'profile')" class="rounded-xl bg-emerald-500 px-5 py-3 text-xs font-black text-white transition hover:bg-emerald-400">Completar perfil</button>
                                    <button type="button" wire:click="$set('activeSection', 'jobs')" class="rounded-xl border border-white/10 bg-white/10 px-5 py-3 text-xs font-black text-white transition hover:bg-white/15">Ver oportunidades</button>
                                </div>
                            </div>
                        </div>

                        @php
                            $profileFields = ['headline','phone','location','preferred_area','employment_type','availability','education','experience','skills','languages','about','cv_path'];
                            $filled = collect($profileFields)->filter(fn($field) => filled($candidate->{$field}))->count();
                            $completion = (int) round(($filled / count($profileFields)) * 100);
                            $pendingApplications = $applications->where('status', 'pending')->count();
                            $acceptedApplications = $applications->where('status', 'accepted')->count();
                        @endphp

                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Perfil</p>
                                <p class="mt-2 text-2xl font-black">{{ $completion }}%</p>
                                <p class="mt-1 text-xs text-zinc-500">Completude profissional</p>
                            </div>
                            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Oportunidades</p>
                                <p class="mt-2 text-2xl font-black">{{ $companies->count() }}</p>
                                <p class="mt-1 text-xs text-zinc-500">Empresas com recrutamento aberto</p>
                            </div>
                            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Em análise</p>
                                <p class="mt-2 text-2xl font-black">{{ $pendingApplications }}</p>
                                <p class="mt-1 text-xs text-zinc-500">Candidaturas pendentes</p>
                            </div>
                            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Aceites</p>
                                <p class="mt-2 text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $acceptedApplications }}</p>
                                <p class="mt-1 text-xs text-zinc-500">Processos concluídos</p>
                            </div>
                        </div>

                        <div class="rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="font-black">Próximo passo</h3>
                                    <p class="mt-1 text-xs text-zinc-500">Um perfil completo aumenta a qualidade da candidatura.</p>
                                </div>
                                <span class="text-sm font-black text-emerald-600">{{ $completion }}%</span>
                            </div>
                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ $completion }}%"></div>
                            </div>
                        </div>
                    </section>
                @endif

                @if($activeSection === 'profile')
                    <section class="rounded-[2rem] border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="border-b border-zinc-100 p-6 dark:border-zinc-800 sm:p-8">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600">Perfil profissional</span>
                                    <h2 class="mt-2 text-2xl font-black tracking-tight">O teu perfil de candidato</h2>
                                    <p class="mt-1 text-sm text-zinc-500">Estes dados acompanham as tuas candidaturas e ajudam o recrutador a conhecer-te.</p>
                                </div>
                                @if($profileSaved)<span class="rounded-full bg-emerald-500/10 px-3 py-1.5 text-[10px] font-black text-emerald-600">Guardado</span>@endif
                            </div>
                        </div>

                        <form wire:submit="saveProfile" class="space-y-8 p-6 sm:p-8">
                            <div>
                                <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-zinc-400">Identidade e contacto</h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <flux:input wire:model="name" label="Nome completo" required />
                                    <flux:input wire:model="email" type="email" label="Email profissional" required />
                                    <flux:input wire:model="phone" label="Telefone" placeholder="+351 ..." />
                                    <flux:input wire:model="location" label="Localização" placeholder="Lisboa, Portugal" />
                                </div>
                            </div>

                            <div>
                                <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-zinc-400">Posicionamento profissional</h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <flux:input wire:model="headline" label="Título profissional" placeholder="Ex.: Junior Web Developer | Laravel & PHP" />
                                    <flux:input wire:model="preferred_area" label="Área pretendida" placeholder="Desenvolvimento Web" />
                                    <flux:select wire:model="employment_type" label="Tipo de emprego">
                                        <option value="">Selecionar</option>
                                        <option value="full_time">Full-time</option>
                                        <option value="part_time">Part-time</option>
                                        <option value="hybrid">Híbrido</option>
                                        <option value="remote">Remoto</option>
                                        <option value="internship">Estágio</option>
                                        <option value="freelance">Freelance</option>
                                    </flux:select>
                                    <flux:select wire:model="availability" label="Disponibilidade">
                                        <option value="">Selecionar</option>
                                        <option value="immediate">Imediata</option>
                                        <option value="1_month">Até 1 mês</option>
                                        <option value="2_months">1–2 meses</option>
                                        <option value="3_months">Mais de 2 meses</option>
                                    </flux:select>
                                    <flux:input wire:model="salary_expectation" type="number" min="0" step="50" label="Expectativa salarial (€ / mês)" />
                                </div>
                            </div>

                            <div>
                                <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-zinc-400">Presença profissional</h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <flux:input wire:model="linkedin_url" type="url" label="LinkedIn" placeholder="https://www.linkedin.com/in/..." />
                                    <flux:input wire:model="portfolio_url" type="url" label="Portfólio / GitHub" placeholder="https://..." />
                                </div>
                            </div>

                            <div class="grid gap-6 lg:grid-cols-2">
                                <flux:textarea wire:model="about" label="Sobre mim" rows="6" placeholder="Apresenta-te brevemente, objetivos e proposta de valor..." />
                                <flux:textarea wire:model="experience" label="Experiência profissional" rows="6" placeholder="Empresa, função, período, responsabilidades e resultados..." />
                                <flux:textarea wire:model="education" label="Formação académica" rows="5" placeholder="Curso, instituição, ano..." />
                                <flux:textarea wire:model="skills" label="Competências" rows="5" placeholder="Laravel, PHP, SQL, Power BI, Git..." />
                                <flux:textarea wire:model="languages" label="Idiomas" rows="4" placeholder="Português — nativo; Inglês — B2..." />
                                <flux:textarea wire:model="certifications" label="Certificações" rows="4" placeholder="Certificações, cursos e formação complementar..." />
                            </div>

                            <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-950">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-sm font-black">Currículo profissional</h3>
                                        <p class="mt-1 text-xs text-zinc-500">PDF até 5 MB. É usado nas candidaturas.</p>
                                        @if($candidate->cv_path)
                                            <p class="mt-2 text-xs font-bold text-emerald-600">CV carregado ✓</p>
                                        @endif
                                    </div>
                                    <div class="min-w-64">
                                        <input wire:model="cv" type="file" accept="application/pdf" class="block w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-900" />
                                        @error('cv')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end border-t border-zinc-100 pt-6 dark:border-zinc-800">
                                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-black text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-500 disabled:opacity-50">
                                    <flux:icon name="check" class="size-4" /> Guardar perfil
                                </button>
                            </div>
                        </form>
                    </section>
                @endif

                @if($activeSection === 'jobs')
                    <section class="space-y-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600">Oportunidades</span>
                                <h2 class="mt-2 text-2xl font-black tracking-tight">Vagas disponíveis</h2>
                                <p class="mt-1 text-sm text-zinc-500">As vagas são sincronizadas diretamente com as empresas que têm o recrutamento ativo.</p>
                            </div>
                            <span class="rounded-full bg-zinc-100 px-3 py-1.5 text-[10px] font-black text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $companies->count() }} empresas</span>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            @forelse($companies as $company)
                                @php
                                    $alreadyApplied = $applications->contains('workspace_id', $company->id);
                                    $title = trim($company->recruitment_announcement ?: ($company->industry ? 'Oportunidades em '.$company->industry : 'Oportunidade profissional'));
                                @endphp
                                <article class="overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                                    <div class="p-6">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-emerald-500/10 text-lg font-black text-emerald-600">
                                                    @if($company->logo_path)<img src="{{ asset('storage/'.$company->logo_path) }}" class="size-full object-cover">@else{{ strtoupper(substr($company->name, 0, 1)) }}@endif
                                                </div>
                                                <div>
                                                    <h3 class="font-black">{{ $company->name }}</h3>
                                                    <p class="mt-1 text-xs text-zinc-500">{{ $company->industry ?: 'Empresa' }} @if($company->address) · {{ $company->address }} @endif</p>
                                                </div>
                                            </div>
                                            <span class="shrink-0 rounded-full bg-emerald-500/10 px-2.5 py-1 text-[9px] font-black text-emerald-600">{{ $company->recruitment_vacancies }} {{ $company->recruitment_vacancies === 1 ? 'vaga' : 'vagas' }}</span>
                                        </div>

                                        <div class="mt-6 rounded-2xl bg-zinc-50 p-5 dark:bg-zinc-950">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Oportunidade</p>
                                            <h4 class="mt-2 text-lg font-black">{{ $title }}</h4>
                                            @if($company->recruitment_description)
                                                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $company->recruitment_description }}</p>
                                            @endif
                                            @if($company->recruitment_extra_info)
                                                <p class="mt-3 line-clamp-4 whitespace-pre-line text-xs leading-5 text-zinc-500">{{ $company->recruitment_extra_info }}</p>
                                            @endif
                                        </div>

                                        <div class="mt-5 flex items-center justify-between gap-3">
                                            <span class="text-[10px] text-zinc-400">{{ $alreadyApplied ? 'Candidatura já submetida' : 'Recrutamento aberto' }}</span>
                                            @if($alreadyApplied)
                                                <span class="rounded-xl bg-zinc-100 px-4 py-2.5 text-xs font-black text-zinc-500 dark:bg-zinc-800">Candidatado ✓</span>
                                            @else
                                                <button type="button" wire:click="openApplication({{ $company->id }})" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-black text-white shadow-lg shadow-emerald-500/15 transition hover:bg-emerald-500">Candidatar-me</button>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="md:col-span-2 rounded-[2rem] border border-dashed border-zinc-300 p-12 text-center dark:border-zinc-700">
                                    <flux:icon name="briefcase" class="mx-auto size-10 text-zinc-300" />
                                    <h3 class="mt-4 font-black">Não existem vagas publicadas neste momento</h3>
                                    <p class="mt-2 text-sm text-zinc-500">As oportunidades aparecem aqui automaticamente quando uma empresa ativa o recrutamento e define vagas abertas.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($activeSection === 'applications')
                    <section class="space-y-6">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-violet-600">Processos</span>
                            <h2 class="mt-2 text-2xl font-black tracking-tight">As minhas candidaturas</h2>
                            <p class="mt-1 text-sm text-zinc-500">Consulta o estado de cada processo de recrutamento.</p>
                        </div>

                        <div class="overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            @forelse($applications as $application)
                                <div class="flex flex-col gap-5 border-b border-zinc-100 p-6 last:border-0 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-100 text-sm font-black dark:bg-zinc-800">
                                            @if($application->company_logo)<img src="{{ asset('storage/'.$application->company_logo) }}" class="size-full object-cover">@else{{ strtoupper(substr($application->company_name ?: 'E', 0, 1)) }}@endif
                                        </div>
                                        <div>
                                            <h3 class="font-black">{{ $application->company_name ?: 'Empresa' }}</h3>
                                            <p class="mt-1 text-xs font-bold text-emerald-600">{{ $application->role }}</p>
                                            <p class="mt-1 text-[10px] text-zinc-400">Submetida em {{ \Carbon\Carbon::parse($application->created_at)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @switch($application->status)
                                            @case('accepted')
                                                <span class="rounded-full bg-emerald-500/10 px-3 py-1.5 text-[10px] font-black text-emerald-600">Aceite</span>
                                                @break
                                            @case('rejected')
                                                <span class="rounded-full bg-red-500/10 px-3 py-1.5 text-[10px] font-black text-red-600">Não selecionado</span>
                                                @break
                                            @default
                                                <span class="rounded-full bg-amber-500/10 px-3 py-1.5 text-[10px] font-black text-amber-600">Em análise</span>
                                        @endswitch
                                    </div>
                                </div>
                            @empty
                                <div class="p-12 text-center">
                                    <flux:icon name="clipboard-document" class="mx-auto size-10 text-zinc-300" />
                                    <h3 class="mt-4 font-black">Ainda não tens candidaturas</h3>
                                    <p class="mt-2 text-sm text-zinc-500">Explora as oportunidades e candidata-te às posições que fazem sentido para o teu perfil.</p>
                                    <button type="button" wire:click="$set('activeSection', 'jobs')" class="mt-5 rounded-xl bg-emerald-600 px-5 py-3 text-xs font-black text-white">Ver oportunidades</button>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif
            </main>
        </div>
    </div>

    <flux:modal name="candidate-application-modal" class="w-full max-w-xl">
        <div class="space-y-6">
            <div>
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600">Nova candidatura</span>
                <h2 class="mt-2 text-2xl font-black">Candidatura para {{ $selectedCompanyName }}</h2>
                <p class="mt-1 text-sm text-zinc-500">Será enviado o teu perfil profissional e o CV guardado no teu perfil.</p>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-950">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-black">Perfil a enviar</p>
                        <p class="mt-1 text-[11px] text-zinc-500">{{ $candidate->headline ?: 'Perfil profissional' }}</p>
                    </div>
                    <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-[9px] font-black text-emerald-600">CV disponível ✓</span>
                </div>
            </div>

            <flux:textarea wire:model="applicationNotes" label="Mensagem ao recrutador" rows="5" placeholder="Escreve uma breve mensagem ou informação adicional relevante para esta candidatura..." />

            <div class="flex justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                <flux:modal.close>
                    <button type="button" class="rounded-xl bg-zinc-100 px-4 py-2.5 text-xs font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">Cancelar</button>
                </flux:modal.close>
                <button type="button" wire:click="submitApplication" wire:loading.attr="disabled" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-black text-white shadow-lg shadow-emerald-500/15 disabled:opacity-50">Enviar candidatura</button>
            </div>
        </div>
    </flux:modal>
</div>
