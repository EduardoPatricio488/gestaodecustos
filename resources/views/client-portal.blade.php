<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Cliente — {{ $client->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-zinc-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="text-center mb-12">
            <p class="text-[9px] font-black uppercase tracking-[0.5em] text-emerald-600 mb-2">Portal do Cliente</p>
            <h1 class="text-3xl font-black text-zinc-900">{{ $workspace->name }}</h1>
            <p class="text-zinc-500 mt-2">Olá, <strong>{{ $client->name }}</strong></p>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-zinc-100 text-center">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Faturas</p>
                <p class="text-3xl font-black text-zinc-900 mt-2">{{ $invoices->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-zinc-100 text-center">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Propostas</p>
                <p class="text-3xl font-black text-zinc-900 mt-2">{{ $proposals->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-zinc-100 text-center">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Em Dívida</p>
                <p class="text-3xl font-black text-red-500 mt-2">{{ number_format($client->pending_debt, 0, ',', '.') }}€</p>
            </div>
        </div>

        @if($invoices->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-zinc-100 p-6 mb-6">
                <h2 class="font-black text-zinc-900 uppercase tracking-widest text-sm mb-4">Faturas</h2>
                <div class="space-y-3">
                    @foreach($invoices as $invoice)
                        <div class="flex justify-between items-center py-3 border-b border-zinc-50 last:border-0">
                            <div>
                                <p class="font-bold text-sm">#{{ $invoice->invoice_number }}</p>
                                <p class="text-xs text-zinc-400">Vencimento: {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '—' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-black tabular-nums">{{ number_format($invoice->total_amount, 2, ',', '.') }}€</p>
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full
                                    {{ $invoice->status === 'paga' ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600' }}">
                                    {{ $invoice->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($proposals->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-zinc-100 p-6 mb-6">
                <h2 class="font-black text-zinc-900 uppercase tracking-widest text-sm mb-4">Propostas</h2>
                <div class="space-y-3">
                    @foreach($proposals as $proposal)
                        <div class="flex justify-between items-center py-3 border-b border-zinc-50 last:border-0">
                            <div>
                                <p class="font-bold text-sm">{{ $proposal->title }}</p>
                                <p class="text-xs text-zinc-400">#{{ $proposal->proposal_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-black tabular-nums">{{ number_format($proposal->amount, 2, ',', '.') }}€</p>
                                <span class="text-[9px] font-black uppercase text-zinc-500">{{ $proposal->status }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <p class="text-center text-[10px] text-zinc-400 mt-8">Powered by Finance Pro · Link seguro e privado</p>
    </div>
</body>
</html>
