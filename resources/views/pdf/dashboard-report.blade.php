<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1f2937; }
        .header { border-bottom: 4px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .period { font-size: 12px; color: #6b7280; }
        .summary-box { background: #f3f4f6; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding: 8px; }
        td { padding: 8px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .amount { text-align: right; font-weight: bold; }
        .total-row { background: #f9fafb; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">Relatório Financeiro: {{ $workspaceName }}</h2>
        <p class="period">Período: {{ $start }} até {{ $end }}</p>
    </div>

    <div class="summary-box">
        <table style="margin:0">
            <tr>
                <td>Total Receitas: <strong>{{ number_format($totalEarned, 2) }} €</strong></td>
                <td>Total Gastos: <strong style="color:#dc2626">{{ number_format($totalSpent, 2) }} €</strong></td>
                <td style="text-align:right">Saldo: <strong>{{ number_format($totalEarned - $totalSpent, 2) }} €</strong></td>
            </tr>
        </table>
    </div>

    @if($expenses->count() > 0)
        <h4>Lista de Gastos</h4>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th class="amount">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $e)
                    <tr>
                        <td>{{ $e->spent_at->format('d/m/Y') }}</td>
                        <td>{{ $e->category->name }}</td>
                        <td>{{ $e->description ?: $e->subcategory }}</td>
                        <td class="amount">-{{ number_format($e->amount, 2) }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($incomes->count() > 0)
        <h4 style="margin-top:30px">Lista de Receitas</h4>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th class="amount">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomes as $i)
                    <tr>
                        <td>{{ $i->received_at->format('d/m/Y') }}</td>
                        <td>{{ $i->description }}</td>
                        <td class="amount" style="color:#059669">+{{ number_format($i->amount, 2) }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div style="margin-top:40px; font-size:9px; text-align:center; color:#999;">
        Documento gerado por {{ $userName }} em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
