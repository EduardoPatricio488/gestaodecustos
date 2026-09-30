<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Despesas - Finance Pro IA</title>
    <style>
        @page { margin: 1.5cm; }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #0f172a;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 18px;
            margin-bottom: 28px;
        }

        .app-name {
            font-size: 24px;
            font-weight: 700;
            color: #4f46e5;
        }

        .report-title {
            margin-top: 4px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .meta {
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }

        .summary {
            width: 100%;
            margin-bottom: 28px;
        }

        .summary-box {
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .summary-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .summary-value {
            margin-top: 4px;
            font-size: 18px;
            font-weight: 700;
            color: #dc2626;
        }

        .section-title {
            margin-bottom: 12px;
            padding-bottom: 7px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table.expenses {
            width: 100%;
            border-collapse: collapse;
        }

        table.expenses th {
            padding: 10px 8px;
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-size: 9px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
        }

        table.expenses td {
            padding: 10px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 10px;
            vertical-align: top;
        }

        .amount {
            color: #dc2626;
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .category {
            color: #475569;
        }

        .empty {
            padding: 25px;
            text-align: center;
            color: #64748b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="65%">
                <div class="app-name">Finance Pro <span style="color: #94a3b8; font-weight: 400;">IA</span></div>
                <div class="report-title">Relatório de Despesas</div>
            </td>
            <td class="meta">
                Gerado em {{ now()->format('d/m/Y H:i') }}<br>
                Registos: {{ $expenses->count() }}
            </td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td class="summary-box">
                <div class="summary-label">Total de despesas</div>
                <div class="summary-value">-{{ number_format((float) $expenses->sum('amount'), 2, ',', ' ') }}€</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Despesas registadas</div>

    @if($expenses->count() > 0)
        <table class="expenses">
            <thead>
                <tr>
                    <th width="15%">Data</th>
                    <th width="22%">Categoria</th>
                    <th width="43%">Descrição</th>
                    <th width="20%" style="text-align: right;">Montante</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                    <tr>
                        <td>
                            {{ $expense->spent_at ? \Carbon\Carbon::parse($expense->spent_at)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="category">
                            {{ $expense->category->name ?? 'Geral' }}
                        </td>
                        <td>
                            {{ $expense->description ?: 'Sem descrição' }}
                        </td>
                        <td class="amount">
                            -{{ number_format((float) $expense->amount, 2, ',', ' ') }}€
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Não existem despesas registadas para exportar.</div>
    @endif

    <footer>
        Finance Pro IA — Documento confidencial para uso interno.
    </footer>
</body>
</html>
