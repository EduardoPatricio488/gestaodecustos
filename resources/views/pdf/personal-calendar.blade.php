<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Calendário {{ $month->format('m/Y') }}</title>
    <style>
        @page { margin: 30px 32px 38px; }
        body { font-family: "DejaVu Sans", sans-serif; color: #1e293b; font-size: 9px; }
        h1 { margin: 0 0 4px; font-size: 22px; color: #312e81; }
        h2 { margin: 22px 0 10px; font-size: 14px; color: #312e81; }
        .subtitle { margin: 0 0 6px; color: #64748b; }
        .note { margin: 0 0 14px; color: #64748b; font-size: 8px; }
        .calendar { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .calendar th { padding: 6px; background: #eef2ff; color: #4338ca; font-size: 8px; text-align: left; }
        .calendar td { height: 45px; padding: 5px; border: 1px solid #e2e8f0; vertical-align: top; }
        .calendar .empty { background: #f8fafc; }
        .calendar .today { background: #eef2ff; border: 2px solid #6366f1; }
        .day-number { font-weight: bold; }
        .count { margin-top: 5px; color: #64748b; font-size: 8px; }
        .day-heading { margin: 14px 0 5px; color: #4338ca; font-weight: bold; page-break-after: avoid; }
        .event { padding: 5px 0; border-bottom: 1px solid #e2e8f0; page-break-inside: avoid; }
        .event table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .event td { vertical-align: top; }
        .event .amount { width: 100px; text-align: right; white-space: nowrap; font-weight: bold; }
        .event-label, .details { word-wrap: break-word; }
        .details { margin-top: 2px; color: #64748b; font-size: 8px; }
        .footer { position: fixed; bottom: -23px; color: #94a3b8; font-size: 7px; }
    </style>
</head>
<body>
    <h1>Calendário · {{ $month->translatedFormat('F Y') }}</h1>
    <p class="subtitle">{{ $workspace->name }} · Gerado em {{ $generatedAt->format('d/m/Y H:i') }}</p>
    <p class="note">Todos os registos do mês selecionado, sem filtros. Inclui valores previstos, que não são confirmação de pagamento.</p>

    @php
        $padding = ($month->dayOfWeek + 6) % 7;
        $weeks = (int) ceil(($padding + $month->daysInMonth) / 7);
    @endphp
    <table class="calendar">
        <thead>
            <tr>
                @foreach(['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'] as $weekday)
                    <th>{{ $weekday }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for($week = 0; $week < $weeks; $week++)
                <tr>
                    @for($weekday = 0; $weekday < 7; $weekday++)
                        @php
                            $day = $week * 7 + $weekday - $padding + 1;
                        @endphp
                        @if($day < 1 || $day > $month->daysInMonth)
                            <td class="empty"></td>
                        @else
                            @php
                                $date = $month->copy()->day($day);
                                $count = $events->get($date->toDateString(), collect())->count();
                            @endphp
                            <td class="{{ $date->isSameDay($generatedAt) ? 'today' : '' }}">
                                <span class="day-number">{{ $day }}</span>
                                @if($count)
                                    <div class="count">{{ $count }} {{ $count === 1 ? 'registo' : 'registos' }}</div>
                                @endif
                            </td>
                        @endif
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    <h2>Registos do mês</h2>
    @forelse($events->sortKeys() as $date => $dayEvents)
        <div class="day-heading">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</div>
        @foreach($dayEvents as $event)
            <div class="event">
                <table>
                    <tr>
                        <td>
                            <div class="event-label"><strong>{{ $event['calendar_icon_label'] ?? 'Registo' }}</strong> · {{ $event['label'] }}</div>
                            @if(!empty($event['meta']))
                                <div class="details">{{ $event['meta'] }}</div>
                            @endif
                            @if(!empty($event['notes']))
                                <div class="details">{{ $event['notes'] }}</div>
                            @endif
                            @if(($event['subtype'] ?? null) === 'meal_allowance')
                                <div class="details">{{ match ($event['status']) { 'expected' => 'À espera', 'received' => 'Pago', 'partial' => 'Parcial', 'missed' => 'Não recebido' } }}</div>
                            @elseif($event['type'] === 'subscription' && !($event['schedule_known'] ?? true))
                                <div class="details">Ciclo ou data não definidos</div>
                            @elseif(($event['subtype'] ?? null) === 'payday')
                                <div class="details">Salário líquido previsto: {{ $workspace->money($event['payday_net_salary']) }} · Subsídio previsto: {{ $workspace->money($event['payday_meal_allowance']) }}</div>
                            @endif
                        </td>
                        @if(isset($event['amount']))
                            @php
                                $sign = match ($event['type']) {
                                    'income', 'salary' => '+',
                                    'expense', 'subscription' => '−',
                                    'debt' => ($event['debt_direction'] ?? null) === 'owed' ? '+' : '−',
                                    'transfer' => ($event['direction'] ?? null) === 'in' ? '+' : '−',
                                    default => '',
                                };
                                $currency = $event['type'] === 'transfer' ? ($event['currency'] ?? $workspace->currency) : $workspace->currency;
                            @endphp
                            <td class="amount">{{ $sign }}{{ number_format($event['amount'], 2, ',', '.') }} {{ $currency }}</td>
                        @endif
                    </tr>
                </table>
            </div>
        @endforeach
    @empty
        <p>Sem registos neste mês.</p>
    @endforelse
    <div class="footer">Finance Pro · Calendário mensal</div>
</body>
</html>
