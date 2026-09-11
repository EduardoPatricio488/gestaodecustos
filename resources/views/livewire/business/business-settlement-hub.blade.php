<div class="space-y-6 p-6">
    <div><h1 class="text-2xl font-semibold">Pagamentos e acertos</h1><p class="text-sm text-zinc-500">Pagamentos parciais, notas de crédito e saldos em aberto.</p></div>
    <div class="grid gap-6 lg:grid-cols-2">
        <form wire:submit="savePayment" class="space-y-4 rounded-2xl border bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="font-semibold">Registar pagamento</h2>
            <select wire:model="type" class="w-full rounded-lg border p-2"><option value="invoice">Recebimento de cliente</option><option value="expense">Pagamento a fornecedor</option></select>
            <select wire:model="recordId" class="w-full rounded-lg border p-2"><option value="">Selecionar documento</option>@if($type==='invoice') @foreach($invoices as $invoice)<option value="{{ $invoice->id }}">{{ $invoice->invoice_number }} — {{ number_format($invoice->outstanding_amount,2,',',' ') }} {{ $invoice->currency }}</option>@endforeach @else @foreach($expenses as $expense)<option value="{{ $expense->id }}">{{ $expense->title ?: $expense->description }} — {{ number_format($expense->outstanding_amount,2,',',' ') }} {{ $expense->currency }}</option>@endforeach @endif</select>
            <input wire:model="amount" type="number" step="0.01" min="0.01" placeholder="Valor" class="w-full rounded-lg border p-2">
            <select wire:model="bankAccountId" class="w-full rounded-lg border p-2"><option value="">Sem conta associada</option>@foreach($bankAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency }})</option>@endforeach</select>
            <input wire:model="paidAt" type="date" class="w-full rounded-lg border p-2"><input wire:model="reference" type="text" placeholder="Referência" class="w-full rounded-lg border p-2">
            <button class="rounded-lg bg-zinc-900 px-4 py-2 text-white">Registar</button>
        </form>
        <form wire:submit="saveCreditNote" class="space-y-4 rounded-2xl border bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="font-semibold">Nota de crédito</h2>
            <select wire:model="invoiceIdForCredit" class="w-full rounded-lg border p-2"><option value="">Selecionar fatura</option>@foreach($invoices as $invoice)<option value="{{ $invoice->id }}">{{ $invoice->invoice_number }} — {{ number_format($invoice->outstanding_amount,2,',',' ') }} {{ $invoice->currency }}</option>@endforeach</select>
            <input wire:model="creditNumber" type="text" placeholder="Número da nota de crédito" class="w-full rounded-lg border p-2"><input wire:model="creditExclVat" type="number" step="0.01" min="0.01" placeholder="Valor sem IVA" class="w-full rounded-lg border p-2"><input wire:model="creditVat" type="number" step="0.01" min="0" placeholder="IVA" class="w-full rounded-lg border p-2"><textarea wire:model="creditReason" placeholder="Motivo" class="w-full rounded-lg border p-2"></textarea>
            <button class="rounded-lg border px-4 py-2">Emitir nota de crédito</button>
        </form>
    </div>
    @if($errors->any()) <div class="rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div> @endif
</div>
