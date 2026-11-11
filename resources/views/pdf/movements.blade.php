<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Todos os movimentos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #18181b; font-size: 9px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 18px; }
        h1 { font-size: 21px; margin: 0 0 5px; }
        .muted { color: #71717a; }
        .summary { margin: 12px 0 18px; padding: 10px; background: #f4f4f5; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #27272a; color: white; text-align: left; padding: 8px 6px; font-size: 8px; }
        td { border-bottom: 1px solid #e4e4e7; padding: 7px 6px; vertical-align: top; }
        tr:nth-child(even) td { background: #fafafa; }
        .amount { text-align: right; white-space: nowrap; }
        .footer { margin-top: 16px; color: #71717a; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Todos os movimentos</h1>
        <div class="muted">{{ $workspaceName }} · Histórico completo de receitas, despesas, transferências e assinaturas</div>
    </div>
    <div class="summary"><strong>{{ number_format($total, 0, ',', '.') }}</strong> registos incluídos · Gerado em {{ $generatedAt }}</div>
    <table>
        <thead><tr><th>Data</th><th>Tipo</th><th>Descrição</th><th>Categoria / Detalhe</th><th>Direção</th><th>Estado</th><th class="amount">Valor</th></tr></thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('d/m/Y') : '—' }}</td>
                <td>{{ $row['type'] }}</td>
                <td>{{ $row['description'] }}</td>
                <td>{{ $row['detail'] }}</td>
                <td>{{ $row['direction'] }}</td>
                <td>{{ $row['status'] }}</td>
                <td class="amount">{{ number_format($row['amount'], 2, ',', '.') }} €</td>
            </tr>
        @empty
            <tr><td colspan="7">Não existem movimentos registados.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="footer">Documento confidencial · Finance Pro IA · {{ $generatedAt }}</div>
</body>
</html>
