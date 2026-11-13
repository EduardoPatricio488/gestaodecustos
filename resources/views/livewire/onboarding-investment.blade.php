<div x-data="{ showXtbUploadModal: false }" @keydown.escape.window="showXtbUploadModal = false">
    <div class="flex flex-wrap gap-3">
        <button type="button" wire:click="createAsset" class="inline-flex h-12 items-center gap-2 rounded-2xl bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
            <flux:icon name="plus" class="size-4" /> Novo Investimento
        </button>
        <button type="button" @click="showXtbUploadModal = true" class="inline-flex h-12 items-center gap-2 rounded-2xl border border-emerald-300 bg-emerald-50 px-5 text-sm font-bold text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
            <flux:icon name="arrow-up-tray" class="size-4" /> Upload de relatórios
        </button>
    </div>
    @if($onboardingSaved)<p role="status" class="mt-3 text-sm font-bold text-emerald-600">Investimento guardado. Podes adicionar outro ou continuar.</p>@endif
    <section class="mt-5 space-y-3 rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Investimentos guardados</h3>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Posições disponíveis no espaço financeiro.</p>
        </div>
        @forelse($xtbInvestments as $investment)
            <div wire:key="onboarding-investment-{{ $investment->id }}" class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-950">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="break-words text-sm font-bold text-zinc-900 dark:text-white">{{ $investment->name }}</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $investment->symbol }}@if($investment->xtb_position_id) · Posição {{ $investment->xtb_position_id }}@endif · Quantidade: {{ rtrim(rtrim(number_format($investment->quantity, 6, ',', ' '), '0'), ',') }}</p>
                    </div>
                    @if($investment->current_price !== null)
                        <p class="text-sm font-bold tabular-nums text-zinc-700 dark:text-zinc-200">{{ number_format($investment->quantity * $investment->current_price, 2, ',', ' ') }} €</p>
                    @endif
                    <button type="button" wire:click="toggleInvestment({{ $investment->id }})" aria-expanded="{{ $expandedInvestmentId === $investment->id ? 'true' : 'false' }}" aria-controls="onboarding-investment-details-{{ $investment->id }}" aria-label="{{ $expandedInvestmentId === $investment->id ? 'Fechar' : 'Editar' }} {{ $investment->name }}" class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-indigo-600 dark:hover:bg-zinc-800">
                        <flux:icon name="{{ $expandedInvestmentId === $investment->id ? 'chevron-up' : 'chevron-down' }}" class="size-5" />
                    </button>
                </div>
                @if($expandedInvestmentId === $investment->id)
                    <form id="onboarding-investment-details-{{ $investment->id }}" wire:submit.prevent="saveInvestment" class="mt-4 space-y-4 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                        @include('livewire.partials.onboarding-validation-errors', ['onboardingModal' => true])
                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:input wire:model="xtbForm.name" label="Nome do ativo" />
                            <flux:input wire:model="xtbForm.symbol" label="Ticker / Símbolo" />
                            <flux:input wire:model="xtbForm.isin" label="Código ISIN" />
                            <div>
                                <label for="xtb-type-{{ $investment->id }}" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Classe</label>
                                <select id="xtb-type-{{ $investment->id }}" wire:model.live="xtbForm.type" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                    <option value="Acao">Ação</option>
                                    <option value="ETF">ETF</option>
                                    <option value="Fundo">Fundo</option>
                                    <option value="Cripto">Cripto</option>
                                    <option value="Divida">Dívida</option>
                                </select>
                            </div>
                            <flux:input wire:model="xtbForm.quantity" type="number" step="any" min="0" label="Quantidade" />
                            <flux:input wire:model="xtbForm.average_price" type="number" step="any" min="0" label="Preço médio por unidade (€)" />
                            <flux:input wire:model="xtbForm.current_price" type="number" step="any" min="0" label="Preço atual por unidade (€)" />
                            <flux:input wire:model="xtbForm.fees" type="number" step="any" min="0" label="Comissões (€)" />
                            <flux:input wire:model="xtbForm.operation_date" type="date" label="Data da operação" />
                            <div>
                                <label for="xtb-account-{{ $investment->id }}" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Conta associada</label>
                                <select id="xtb-account-{{ $investment->id }}" wire:model="xtbForm.bank_account_id" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                    <option value="">Selecionar conta associada</option>
                                    @foreach($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <flux:input wire:model="xtbForm.exchange" label="Bolsa" />
                            <flux:input wire:model="xtbForm.network" label="Rede" />
                            <flux:input wire:model="xtbForm.provider" label="Gestora / Provider" />
                            @if(($xtbForm['type'] ?? null) === 'Divida')
                                <div>
                                    <label for="xtb-product-{{ $investment->id }}" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Produto</label>
                                    <select id="xtb-product-{{ $investment->id }}" wire:model="xtbForm.product_type" class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                        <option value="">Selecionar produto</option>
                                        <option value="CA">Certificados de Aforro</option>
                                        <option value="CT">Certificados do Tesouro</option>
                                    </select>
                                </div>
                                <flux:input wire:model="xtbForm.issuer" label="Emitente" />
                                <flux:input wire:model="xtbForm.series" label="Série" />
                                <flux:input wire:model="xtbForm.interest_rate" type="number" step="any" min="0" label="Taxa base (%)" />
                                <flux:input wire:model="xtbForm.loyalty_bonus" type="number" step="any" min="0" label="Prémio de permanência (%)" />
                                <flux:input wire:model="xtbForm.capitalization_date" type="date" label="Data de capitalização" />
                            @endif
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">O valor atual é calculado a partir da quantidade e do preço atual. A origem e os identificadores de importação mantêm-se para evitar duplicados. Uma nova importação pode atualizar os dados novamente.</p>
                        <div class="flex flex-wrap justify-between gap-2">
                            <button type="button" wire:click="deleteAsset({{ $investment->id }})" wire:confirm="Eliminar este investimento e os rendimentos associados? Uma nova importação do relatório pode voltar a criá-lo." class="inline-flex items-center gap-2 rounded-xl border border-rose-200 px-4 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50 dark:border-rose-900 dark:hover:bg-rose-950/30">
                                <flux:icon name="trash" class="size-4" /> Eliminar
                            </button>
                            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">Guardar alterações</button>
                        </div>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Ainda não há investimentos guardados neste espaço.</p>
        @endforelse
    </section>
    @include('livewire.partials.investment-modal', ['onboardingModal' => true])
    @include('livewire.partials.investment-report-modal', ['onboardingModal' => true])
</div>
