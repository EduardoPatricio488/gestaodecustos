<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Despesas</title>

    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f3f3f3; text-align: left; }
    </style>
</head>

<body>

    <h1>Relatório de Despesas</h1>

    <p>Utilizador: {{ $user->name }}</p>
    <p>Total gasto: <strong>{{ number_format($total, 2, ',', ' ') }} €</strong></p>

    <table>
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Data</th>
                <th>Valor</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($expenses as $e)
                <tr>
                    <td>{{ $e->description ?? '-' }}</td>
                    <td>{{ $e->category?->name ?? '-' }}</td>
                    <td>{{ $e->spent_at->format('d/m/Y') }}</td>
                    <td>{{ number_format($e->amount, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
