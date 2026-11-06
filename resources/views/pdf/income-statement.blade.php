<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Extrato de Receitas</title>
    <style>
        @page { margin: 28px 30px 42px; }
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; color: #17212b; font-size: 9px; line-height: 1.45; }
        .topline { height: 7px; background: #059669; margin: -28px -30px 22px; }
        .header { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .header td { vertical-align: top; }
        .brand { color: #059669; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        h1 { margin: 5px 0 4px; color: #10251f; font-size: 25px; line-height: 1.15; }
        .subtitle { color: #64748b; font-size: 10px; }
        .meta { text-align: right; color: #64748b; font-size: 8px; line-height: 1.8; }
        .meta strong { color: #1f2937; }
        .period { display: inline-block; margin-top: 7px; padding: 5px 9px; border-radius: 5px; background: #ecfdf5; color: #047857; font-size: 9px; font-weight: bold; }
        .stats { width: 100%; border-collapse: separate; border-spacing: 7px 0; margin: 0 -7px 18px; }
        .stats td { width: 25%; padding: 11px 12px; background: #f4f7f6; border: 1px solid #e2e8e6; border-radius: 7px; vertical-align: top; }
        .stats .label { color: #64748b; font-size: 7px; font-weight: bold; text-transform: uppercase; letter-spacing: .7px; }
        .stats .value { margin-top: 5px; color: #10251f; font-size: 16px; font-weight: bold; }
        .stats .green .value { color: #047857; }
        .stats .amber .value { color: #b45309; }
        .note { margin: 7px 0 18px; padding: 8px 10px; background: #fffbeb; border-left: 3px solid #f59e0b; color: #78350f; font-size: 8px; }
        h2 { margin: 20px 0 8px; padding-bottom: 6px; border-bottom: 1px solid #dce5e1; color: #12372d; font-size: 12px; text-transform: uppercase; letter-spacing: .6px; }
        .section-note { margin: -3px 0 8px; color: #64748b; font-size: 8px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data th { padding: 7px 6px; background: #12372d; color: #fff; text-align: left; font-size: 7px; text-transform: uppercase; letter-spacing: .35px; }
        table.data td { padding: 6px; border-bottom: 1px solid #e8eeeb; vertical-align: top; font-size: 8px; }
        table.data tr:nth-child(even) td { background: #f8faf9; }
        .right { text-align: right !important; white-space: nowrap; }
        .muted { color: #64748b; }
        .strong { color: #10251f; font-weight: bold; }
        .green-text { color: #047857; font-weight: bold; }
        .amber-text { color: #b45309; font-weight: bold; }
        .pill { display: inline-block; padding: 2px 5px; border-radius: 3px; background: #e2e8f0; color: #334155; font-size: 7px; font-weight: bold; }
        .pill-paid { background: #d1fae5; color: #065f46; }
        .pill-pending { background: #fef3c7; color: #92400e; }
        .details { margin-top: 3px; color: #64748b; font-size: 7px; line-height: 1.45; }
        .empty { padding: 15px; background: #f8faf9; color: #64748b; text-align: center; border: 1px dashed #cbd5d1; }
        .summary-table { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
        .summary-table td { padding: 6px 8px; border-bottom: 1px solid #e8eeeb; }
        .summary-table td:last-child { text-align: right; font-weight: bold; }
        .summary-table .total td { padding-top: 9px; border-top: 2px solid #059669; border-bottom: 0; color: #047857; font-size: 11px; font-weight: bold; }
        .contract-meta { margin-top: 4px; color: #64748b; font-size: 7px; word-wrap: break-word; }
        .footer { position: fixed; left: 0; right: 0; bottom: -25px; padding-top: 7px; border-top: 1px solid #dce5e1; color: #94a3b8; font-size: 7px; }
        .footer .page { float: right; }
        .footer .page:after { content: counter(page); }
        .keep { page-break-inside: avoid; }
    </style>
</head>
<body>
    @php
        $sourceLabels = [
            'emprego' => 'Emprego',
            'renda' => 'Renda',
            'imobiliario' => 'Imobiliário',
            'freelance' => 'Freelance',
            'investimento' => 'Investimento',
            'reforma' => 'Reforma / Pensão',
            'bolsa' => 'Bolsa de estudo',
            'outro' => 'Outro',
        ];
        $frequencyLabels = [
            'pontual' => 'Pontual',
            'semanal' => 'Semanal',
            'mensal' => 'Mensal',
            'anual' => 'Anual',
        ];
        $deferredCount = $incomes->filter(fn ($income) => (bool) $income->added_to_salary)->count();
    @endphp

    <div class="topline"></div>

    <table class="header">
        <tr>
            <td>
                <div class="brand">Finance Pro · Gestão financeira</div>
                <h1>Extrato detalhado de receitas</h1>
                <div class="subtitle">{{ $workspaceName }} · Emitido para {{ $userName }}</div>
                <div class="period">{{ $periodLabel }} · {{ $startDate }} a {{ $endDate }}</div>
            </td>
            <td class="meta">
                <strong>Documento de movimentos</strong><br>
                Gerado em {{ $generatedAt }}<br>
                Moeda de referência: {{ $currency }}<br>
                Receitas registadas: {{ $incomes->count() }}<br>
                Pagamentos salariais: {{ $salaryPayments->count() }}
            </td>
        </tr>
    </table>

    <table class="stats">
        <tr>
            <td class="green">
                <div class="label">Total efetivamente recebido</div>
                <div class="value">{{ number_format($totalReceived, 2, ',', '.') }} {{ $currency }}</div>
            </td>
            <td>
                <div class="label">Receitas registadas</div>
                <div class="value">{{ number_format($incomeReceivedTotal, 2, ',', '.') }} {{ $currency }}</div>
            </td>
            <td>
                <div class="label">Salários pagos</div>
                <div class="value">{{ number_format($paidSalaryTotal, 2, ',', '.') }} {{ $currency }}</div>
            </td>
            <td class="amber">
                <div class="label">Salários pendentes</div>
                <div class="value">{{ number_format($pendingSalaryTotal, 2, ',', '.') }} {{ $currency }}</div>
            </td>
        </tr>
    </table>

    @if($deferredCount > 0 || $pendingSalaryTotal > 0)
        <div class="note">
            @if($deferredCount > 0)
                Existem {{ $deferredCount }} registo(s) de serviço extra no valor de {{ number_format($deferredIncomeTotal, 2, ',', '.') }} {{ $currency }} marcado(s) para integrar no salário. Estes valores são apresentados nos movimentos, mas não são somados ao total recebido para evitar duplicação.
            @endif
            @if($pendingSalaryTotal > 0)
                Existem também pagamentos salariais pendentes no valor de {{ number_format($pendingSalaryTotal, 2, ',', '.') }} {{ $currency }}. Não estão incluídos no total efetivamente recebido.
            @endif
        </div>
    @endif

    <h2>Resumo por origem</h2>
    @if($incomeBySource->isNotEmpty())
        <table class="data">
            <thead><tr><th>Origem</th><th class="right">Total registado</th></tr></thead>
            <tbody>
                @foreach($incomeBySource as $source => $value)
                    <tr>
                        <td>{{ $sourceLabels[$source] ?? ucfirst(str_replace('_', ' ', $source)) }}</td>
                        <td class="right">{{ number_format($value, 2, ',', '.') }} {{ $currency }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Não existem receitas registadas neste período.</div>
    @endif

    <h2>Movimentos de receita</h2>
    <p class="section-note">Inclui receitas pontuais, serviços extra, prémios e outras entradas com data dentro do período selecionado.</p>
    @if($incomes->isNotEmpty())
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 8%">Data</th>
                    <th style="width: 20%">Descrição</th>
                    <th style="width: 11%">Categoria / origem</th>
                    <th style="width: 15%">Contrato associado</th>
                    <th style="width: 13%">Destino</th>
                    <th class="right" style="width: 11%">Valor original</th>
                    <th class="right" style="width: 11%">Valor convertido</th>
                    <th style="width: 11%">Estado / imposto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomes as $income)
                    @php
                        $source = $sourceLabels[$income->source] ?? ucfirst(str_replace('_', ' ', (string) $income->source));
                        $kind = match ($income->salary_income_type) {
                            'service' => 'Serviço extra',
                            'prize' => 'Prémio',
                            default => $income->type ?: 'Receita',
                        };
                        $destination = $income->added_to_salary
                            ? 'A integrar no salário'
                            : ($income->bankAccount?->name ? 'Banco · '.$income->bankAccount->name : 'Dinheiro físico');
                        $converted = (float) ($income->amount_converted ?? $income->amount);
                        $estimatedTax = $income->tax_estimate
                            ? (float) $income->amount * (float) $income->tax_estimate / 100
                            : null;
                    @endphp
                    <tr>
                        <td>{{ $income->received_at?->format('d/m/Y') ?? '—' }}<div class="details">Registo #{{ $income->id }}</div></td>
                        <td>
                            <span class="strong">{{ $income->description }}</span>
                            @if($income->service_unit_amount)
                                <div class="details">{{ number_format((float) $income->service_unit_amount, 2, ',', '.') }} {{ $income->currency }} × {{ (int) ($income->service_quantity ?: 1) }} serviço(s)</div>
                            @endif
                            @if($income->notes)<div class="details">Notas: {{ $income->notes }}</div>@endif
                        </td>
                        <td>{{ $kind }}<div class="details">{{ $source }} · {{ $frequencyLabels[$income->frequency] ?? ($income->frequency ?: '—') }}</div></td>
                        <td>{{ $income->salaryContract?->description ?? '—' }}</td>
                        <td>{{ $destination }}</td>
                        <td class="right">{{ number_format((float) $income->amount, 2, ',', '.') }} {{ strtoupper($income->currency ?: $currency) }}</td>
                        <td class="right {{ $income->added_to_salary ? 'amber-text' : 'green-text' }}">
                            {{ number_format($converted, 2, ',', '.') }} {{ $currency }}
                        </td>
                        <td>
                            @if($income->added_to_salary)
                                <span class="pill pill-pending">A integrar no salário</span>
                            @else
                                <span class="pill pill-paid">Registada</span>
                            @endif
                            @if($income->tax_estimate)
                                <div class="details">Imposto estimado: {{ number_format((float) $income->tax_estimate, 1, ',', '.') }}% ({{ number_format($estimatedTax, 2, ',', '.') }} {{ strtoupper($income->currency ?: $currency) }})</div>
                            @endif
                            <div class="details">Criada: {{ $income->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Não foram encontrados movimentos de receita neste período.</div>
    @endif

    <h2>Pagamentos salariais</h2>
    <p class="section-note">O valor recebido considera o subsídio de alimentação à parte quando não está incluído no líquido pago em cartão.</p>
    @if($salaryPayments->isNotEmpty())
        <table class="data">
            <thead>
                <tr>
                    <th>Data / estado</th>
                    <th>Contrato</th>
                    <th class="right">Base bruta</th>
                    <th class="right">Duodécimos</th>
                    <th class="right">Complementos</th>
                    <th class="right">Seg. Social</th>
                    <th class="right">IRS</th>
                    <th class="right">Outros descontos</th>
                    <th class="right">Subs. alimentação</th>
                    <th class="right">Líquido registado</th>
                    <th class="right">Total considerado</th>
                    <th>Conta / notas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salaryPayments as $payment)
                    @php
                        $mealCard = ($payment->recurringIncome?->metadata['meal_allowance_destination'] ?? null) === 'meal_card';
                        $paymentTotal = $salaryPaymentValue($payment);
                    @endphp
                    <tr>
                        <td>
                            <span class="strong">{{ $payment->paid_at?->format('d/m/Y') ?? '—' }}</span>
                            <div class="details">Referência: {{ sprintf('%02d', $payment->payment_month) }}/{{ $payment->payment_year }}</div>
                            <span class="pill {{ $payment->status === 'paid' ? 'pill-paid' : 'pill-pending' }}">{{ $payment->status === 'paid' ? 'Pago' : ucfirst(str_replace('_', ' ', (string) $payment->status)) }}</span>
                            <div class="details">{{ $payment->is_automatic ? 'Automático' : 'Manual' }} · #{{ $payment->id }}</div>
                        </td>
                        <td>{{ $payment->recurringIncome?->description ?? 'Contrato não disponível' }}</td>
                        <td class="right">{{ number_format((float) $payment->salary_base_gross, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format((float) $payment->allowances_duodecimos, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format((float) $payment->other_complements, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format((float) $payment->social_security, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format((float) $payment->irs, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format((float) $payment->other_deductions, 2, ',', '.') }}</td>
                        <td class="right">
                            {{ number_format((float) $payment->meal_allowance, 2, ',', '.') }}
                            <div class="details">{{ $mealCard ? 'Incluído no líquido' : 'Somado ao líquido' }}</div>
                        </td>
                        <td class="right">{{ number_format((float) $payment->net_received, 2, ',', '.') }} {{ $currency }}</td>
                        <td class="right {{ $payment->status === 'paid' ? 'green-text' : 'amber-text' }}">{{ number_format($paymentTotal, 2, ',', '.') }} {{ $currency }}</td>
                        <td>
                            {{ $payment->bankAccount?->name ?? 'Sem conta bancária associada' }}
                            @if((float) $payment->bank_balance_amount > 0)
                                <div class="details">Valor creditado na conta: {{ number_format((float) $payment->bank_balance_amount, 2, ',', '.') }} {{ $currency }}</div>
                            @endif
                            @if($payment->notes)<div class="details">{{ $payment->notes }}</div>@endif
                            <div class="details">Criado: {{ $payment->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Não existem pagamentos salariais neste período.</div>
    @endif

    <h2>Rendimentos fixos configurados</h2>
    <p class="section-note">Informação de configuração dos rendimentos recorrentes. Estes valores são apenas informativos e não são adicionados ao total efetivamente recebido.</p>
    @if($recurringIncomes->isNotEmpty())
        <table class="data">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Origem</th>
                    <th class="right">Valor configurado</th>
                    <th>Frequência</th>
                    <th>Estado</th>
                    <th>Conta / destino</th>
                    <th>Notas e dados adicionais</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recurringIncomes as $recurring)
                    @php
                        $metadata = $recurring->metadata ?? [];
                        $metadataText = collect($metadata)->map(function ($value, $key) {
                            $label = ucfirst(str_replace('_', ' ', (string) $key));
                            $formatted = is_scalar($value) || $value === null
                                ? (is_bool($value) ? ($value ? 'Sim' : 'Não') : (string) ($value ?? '—'))
                                : json_encode($value, JSON_UNESCAPED_UNICODE);
                            return $label.': '.$formatted;
                        })->implode(' · ');
                    @endphp
                    <tr>
                        <td><span class="strong">{{ $recurring->description }}</span><div class="details">Registo #{{ $recurring->id }} · Criado: {{ $recurring->created_at?->format('d/m/Y') ?? '—' }}</div></td>
                        <td>{{ $sourceLabels[$recurring->source] ?? ucfirst(str_replace('_', ' ', (string) $recurring->source)) }}</td>
                        <td class="right">{{ number_format((float) $recurring->amount, 2, ',', '.') }} {{ $currency }}</td>
                        <td>{{ $frequencyLabels[$recurring->frequency] ?? ($recurring->frequency ?: '—') }}<div class="details">Dia: {{ $recurring->day_of_month ?: '—' }}</div></td>
                        <td><span class="pill {{ $recurring->is_active ? 'pill-paid' : 'pill-pending' }}">{{ $recurring->is_active ? 'Ativo' : 'Inativo' }}</span></td>
                        <td>{{ $recurring->bankAccount?->name ?? 'Dinheiro físico / não definido' }}</td>
                        <td>
                            {{ $recurring->notes ?: '—' }}
                            @if($metadataText)<div class="contract-meta">{{ $metadataText }}</div>@endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Não existem rendimentos fixos configurados.</div>
    @endif

    <h2>Conferência do período</h2>
    <table class="summary-table">
        <tr><td>Receitas recebidas (sem valores a integrar no salário)</td><td>{{ number_format($incomeReceivedTotal, 2, ',', '.') }} {{ $currency }}</td></tr>
        <tr><td>Pagamentos salariais com estado pago</td><td>{{ number_format($paidSalaryTotal, 2, ',', '.') }} {{ $currency }}</td></tr>
        <tr class="total"><td>TOTAL EFETIVAMENTE RECEBIDO</td><td>{{ number_format($totalReceived, 2, ',', '.') }} {{ $currency }}</td></tr>
        @if($deferredIncomeTotal > 0)
            <tr><td>Serviços extra marcados para integrar no salário (não somados)</td><td>{{ number_format($deferredIncomeTotal, 2, ',', '.') }} {{ $currency }}</td></tr>
        @endif
        @if($pendingSalaryTotal > 0)
            <tr><td>Pagamentos salariais pendentes (não somados)</td><td>{{ number_format($pendingSalaryTotal, 2, ',', '.') }} {{ $currency }}</td></tr>
        @endif
    </table>

    <div class="footer">
        Finance Pro · Extrato detalhado de receitas · {{ $workspaceName }}
        <span class="page">Página </span>
    </div>
</body>
</html>
