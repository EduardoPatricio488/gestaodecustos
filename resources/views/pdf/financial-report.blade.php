<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Auditoria Financeira</title>
    <style>
        /* Importação da fonte Inter para PDFs modernos */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        @page { margin: 1.5cm; }

        body {
            font-family: 'Inter', 'Helvetica', 'Arial', sans-serif;
            color: #0f172a;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Header Style */
        .header-table { width: 100%; border-bottom: 3px solid #4f46e5; padding-bottom: 25px; margin-bottom: 35px; }
        .app-name { font-size: 26px; font-weight: 800; color: #4f46e5; letter-spacing: -1.2px; margin-bottom: 0; }
        .report-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 3px; margin-top: 4px; }
        .meta-data { text-align: right; font-size: 10px; color: #94a3b8; line-height: 1.6; }
        .meta-data strong { color: #475569; }

        /* Resumo em Cards Profissionais */
        .summary-wrapper { width: 100%; margin-bottom: 50px; }
        .summary-table { width: 100%; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .card {
            padding: 20px;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }
        .card-label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 8px; }
        .card-value { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }

        .c-green { color: #059669; background-color: #f0fdf4; border-color: #d1fae5; }
        .c-red { color: #dc2626; background-color: #fef2f2; border-color: #fee2e2; }
        .c-indigo { color: #4f46e5; background-color: #f5f3ff; border-color: #e0e7ff; }

        /* Estilo das Tabelas de Dados */
        .section-header {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #1e293b;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        table.data-grid { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        table.data-grid th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 14px 12px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }
        table.data-grid td {
            padding: 14px 12px;
            font-size: 11px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Badges de Categoria */
        .badge {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            background: #f1f5f9;
            color: #475569;
            display: inline-block;
        }

        /* Alinhamento de Moeda */
        .amount { font-family: 'Inter', sans-serif; font-weight: 700; text-align: right; white-space: nowrap; }
        .neg { color: #dc2626; }
        .pos { color: #059669; }

        /* Footer */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding: 15px 0;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="60%">
                <div class="app-name">Finance Pro <span style="color: #94a3b8; font-weight: 400;">IA</span></div>
                <div class="report-title">Relatório Consolidado de Auditoria</div>
            </td>
            <td class="meta-data">
                Espaço: <strong>{{ $workspaceName }}</strong><br>
                Protocolo: <strong>#{{ date('Ymd') }}-{{ rand(100,999) }}</strong><br>
                Período: {{ $start }} — {{ $end }}
            </td>
        </tr>
    </table>

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td class="card c-green" width="33%">
                    <div class="card-label">Volume de Entradas</div>
                    <div class="card-value">+{{ number_format($totalIncomes, 2, ',', ' ') }}€</div>
                </td>
                <td class="card c-red" width="33%">
                    <div class="card-label">Volume de Saídas</div>
                    <div class="card-value">-{{ number_format($totalExpenses, 2, ',', ' ') }}€</div>
                </td>
                <td class="card c-indigo" width="33%">
                    <div class="card-label">Cash Flow Líquido</div>
                    <div class="card-value">{{ number_format($totalIncomes - $totalExpenses, 2, ',', ' ') }}€</div>
                </td>
            </tr>
        </table>
    </div>

    @if($expenses->count() > 0)
    <div class="section-header">Fluxo de Saídas Detalhado</div>
    <table class="data-grid">
        <thead>
            <tr>
                <th width="12%">Data</th>
                <th width="20%">Hub</th>
                <th width="48%">Descrição do Protocolo</th>
                <th width="20%" style="text-align: right;">Montante</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $e)
            <tr>
                <td>{{ $e->spent_at->format('d/m/Y') }}</td>
                <td><span class="badge">{{ $e->category->name ?? 'Geral' }}</span></td>
                <td>{{ $e->description ?: 'Registo operacional sem descrição' }}</td>
                <td class="amount neg">-{{ number_format($e->amount, 2, ',', ' ') }}€</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($incomes->count() > 0)
    <div class="section-header">Fluxo de Entradas Detalhado</div>
    <table class="data-grid">
        <thead>
            <tr>
                <th width="12%">Data</th>
                <th width="68%">Fonte / Descrição</th>
                <th width="20%" style="text-align: right;">Montante</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $i)
            <tr>
                <td>{{ \Carbon\Carbon::parse($i->received_at)->format('d/m/Y') }}</td>
                <td><strong>{{ $i->title ?? 'Rendimento' }}</strong></td>
                <td class="amount pos">+{{ number_format($i->amount, 2, ',', ' ') }}€</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <footer>
        Documento gerado em {{ $generatedAt }} via Protocolo Finance Pro IA. <br>
        <strong>Confidencial</strong> — Apenas para uso interno do Workspace {{ $workspaceName }}.
    </footer>

</body>
</html>
