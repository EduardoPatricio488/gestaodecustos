<div class="grid grid-cols-2 gap-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 p-1">
    <button type="button" wire:click="$set('{{ $modePath }}', 'manual')"
        class="rounded-lg px-3 py-2 text-[11px] font-bold transition {{ $entryMode === 'manual' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' : 'text-zinc-500' }}">
        Introduzir dados
    </button>
    <button type="button" wire:click="$set('{{ $modePath }}', 'proof')"
        class="rounded-lg px-3 py-2 text-[11px] font-bold transition {{ $entryMode === 'proof' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' : 'text-zinc-500' }}">
        Importar Comprovativo de IBAN
    </button>
</div>

@if($entryMode === 'proof')
    <div class="rounded-2xl border border-dashed border-emerald-300 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-950/20 p-4 space-y-3">
        <div class="flex items-center gap-2">
            <div class="size-9 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 font-black">S</div>
            <div>
                <p class="text-xs font-black text-zinc-900 dark:text-white">Importar comprovativo bancário</p>
                <p class="text-[10px] text-zinc-500">Identifica IBAN português em PDFs com texto legível. Confirma os dados extraídos.</p>
            </div>
        </div>
        <div class="text-[11px] text-zinc-600 dark:text-zinc-300 space-y-1">
            <p class="font-bold">Como obter o comprovativo no Santander:</p>
            <ol class="list-decimal pl-4 space-y-0.5">
                <li>Abre a App Santander.</li>
                <li>Entra na conta que pretendes adicionar.</li>
                <li>Seleciona <strong>Comprovativo de IBAN</strong>.</li>
                <li>Visualiza ou partilha o documento e guarda-o em PDF.</li>
                <li>Carrega aqui o ficheiro PDF.</li>
            </ol>
        </div>
        <label for="{{ $proofInputId }}" class="group flex min-h-44 w-full cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-emerald-400 bg-white px-5 py-6 text-center shadow-sm transition-all duration-200 hover:border-emerald-600 hover:bg-emerald-50 hover:shadow-lg hover:shadow-emerald-500/10 dark:border-emerald-700 dark:bg-zinc-900 dark:hover:border-emerald-500 dark:hover:bg-emerald-950/40">
            <input id="{{ $proofInputId }}" type="file" wire:model="{{ $proofPath }}" accept="application/pdf,.pdf" class="sr-only">
            <span class="mb-3 flex size-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 transition-transform duration-200 group-hover:scale-110 dark:bg-emerald-900/50 dark:text-emerald-300">
                <flux:icon name="cloud-arrow-up" class="size-8" />
            </span>
            <span class="text-sm font-black text-emerald-800 dark:text-emerald-300">Clique para selecionar o comprovativo</span>
            <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Escolhe um ficheiro PDF no teu dispositivo</span>
            <span class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-black text-white shadow-md shadow-emerald-600/20 transition group-hover:bg-emerald-700">
                <flux:icon name="folder-open" class="size-4" />
                Escolher ficheiro
            </span>
            <span class="mt-2 text-[10px] font-medium text-zinc-400">Formato aceite: PDF · Máximo 5 MB</span>
        </label>
        <div wire:loading wire:target="{{ $proofPath }}" class="text-[10px] font-bold text-emerald-600">A analisar comprovativo...</div>
        @error($proofPath) <p class="text-[10px] text-red-500">{{ $message }}</p> @enderror
    </div>
@endif

@if($proofMessage)
    <p role="status" class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400">{{ $proofMessage }}</p>
@endif

<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <label for="{{ $accountFields['name'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Nome da Conta *</label>
        <input id="{{ $accountFields['name'] }}" wire:model="{{ $accountFields['name'] }}" type="text" placeholder="Ex: Conta Principal"
               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>

    @foreach($accountFields as $field)
        @error($field) <p role="alert" class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    @endforeach

    <div>
        <label for="{{ $accountFields['type'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Tipo *</label>
        <select id="{{ $accountFields['type'] }}" wire:model="{{ $accountFields['type'] }}" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="corrente">Conta Corrente</option>
            <option value="poupanca">Conta Poupança</option>
            <option value="cash">Dinheiro Físico</option>
            <option value="credito">Cartão de Crédito</option>
            <option value="investimento">Conta Investimento</option>
            <option value="tesouraria">Tesouraria</option>
            <option value="paypal">PayPal / Digital</option>
            <option value="outro">Outro</option>
        </select>
    </div>

    <div>
        <label for="{{ $accountFields['balance'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Saldo Atual *</label>
        <input id="{{ $accountFields['balance'] }}" wire:model="{{ $accountFields['balance'] }}" type="number" step="0.01" min="-999999999" placeholder="Introduz o saldo atual" required
               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>

    <div>
        <label for="{{ $accountFields['bank_name'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Banco</label>
        <input id="{{ $accountFields['bank_name'] }}" wire:model="{{ $accountFields['bank_name'] }}" type="text" placeholder="Ex: Millennium BCP"
               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>

    <div>
        <label for="{{ $accountFields['currency'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Moeda</label>
        <select id="{{ $accountFields['currency'] }}" wire:model="{{ $accountFields['currency'] }}" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="EUR">EUR €</option>
            <option value="USD">USD $</option>
            <option value="GBP">GBP £</option>
            <option value="CHF">CHF Fr.</option>
        </select>
    </div>

    <div>
        <label for="{{ $accountFields['account_number'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Número de Conta</label>
        <input id="{{ $accountFields['account_number'] }}" wire:model="{{ $accountFields['account_number'] }}" type="text" placeholder="Ex: 0003 54530704020"
               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
    <div>
        <label for="{{ $accountFields['iban'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">IBAN</label>
        <input id="{{ $accountFields['iban'] }}" wire:model="{{ $accountFields['iban'] }}" type="text" placeholder="PT50 XXXX XXXX XXXX XXXX XXXX X"
               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
    <div>
        <label for="{{ $accountFields['holder_name'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Titular</label>
        <input id="{{ $accountFields['holder_name'] }}" wire:model="{{ $accountFields['holder_name'] }}" type="text" placeholder="Nome do titular"
               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
    <div>
        <label for="{{ $accountFields['swift'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">BIC / SWIFT</label>
        <input id="{{ $accountFields['swift'] }}" wire:model="{{ $accountFields['swift'] }}" type="text" placeholder="TOTAPTPL"
               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>

    <div>
        <label for="{{ $accountFields['color'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Cor</label>
        <div class="flex items-center gap-2">
            <input id="{{ $accountFields['color'] }}" wire:model="{{ $accountFields['color'] }}" type="color" class="size-10 rounded-lg border border-zinc-200 dark:border-zinc-700 cursor-pointer">
            <span class="text-[11px] text-zinc-400">{{ $formColor }}</span>
        </div>
    </div>

    <div>
        <label for="{{ $accountFields['alert_below'] }}" class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Alerta abaixo de</label>
        <input id="{{ $accountFields['alert_below'] }}" wire:model="{{ $accountFields['alert_below'] }}" type="number" step="0.01" placeholder="Ex: 100.00"
               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>

    <div class="col-span-2 flex items-center gap-4">
        <label class="flex items-center gap-2 cursor-pointer">
            <input wire:model="{{ $accountFields['is_business'] }}" type="checkbox" class="size-4 rounded accent-emerald-600">
            <span class="text-[12px] font-bold text-zinc-700 dark:text-zinc-300">Conta Empresarial</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input wire:model="{{ $accountFields['include_in_total'] }}" type="checkbox" class="size-4 rounded accent-emerald-600">
            <span class="text-[12px] font-bold text-zinc-700 dark:text-zinc-300">Incluir nos totais</span>
        </label>
    </div>
</div>

