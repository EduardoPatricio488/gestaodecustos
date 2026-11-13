<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Calendário {{ $month->format('m/Y') }}</title>
    <style>
        @page { margin: 28px 32px 40px; }
        body { font-family: "DejaVu Sans", sans-serif; color: #1e293b; font-size: 9px; }
        .hero { width: 100%; border-collapse: collapse; background: #312e81; color: #fff; }
        .hero td { padding: 18px 20px; vertical-align: middle; }
        .eyebrow { color: #c7d2fe; font-size: 8px; font-weight: bold; letter-spacing: 1.3px; text-transform: uppercase; }
        h1 { margin: 5px 0 4px; font-size: 22px; color: #fff; }
        .subtitle { margin: 0; color: #e0e7ff; font-size: 8px; }
        .hero-month { width: 80px; text-align: right; color: #c7d2fe; font-size: 20px; font-weight: bold; }
        .summary { width: 100%; margin: 12px 0; border-collapse: separate; border-spacing: 5px 0; table-layout: fixed; }
        .summary td { padding: 10px 12px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; }
        .summary-label { color: #64748b; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        .summary-value { margin-top: 3px; color: #312e81; font-size: 16px; font-weight: bold; }
        .note { margin: 0 0 14px; padding: 8px 10px; background: #eef2ff; border-left: 3px solid #818cf8; color: #475569; font-size: 8px; }
        h2 { margin: 18px 0 8px; font-size: 14px; color: #312e81; }
        .section-caption { margin: 0 0 8px; color: #64748b; font-size: 8px; }
        .calendar { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .calendar th { padding: 7px 6px; background: #e0e7ff; color: #4338ca; font-size: 8px; text-align: left; }
        .calendar td { height: 44px; padding: 5px; border: 1px solid #e2e8f0; vertical-align: top; }
        .calendar .weekend { background: #f8fafc; }
        .calendar .empty { background: #f8fafc; }
        .calendar .today { background: #eef2ff; border: 2px solid #6366f1; }
        .day-number { color: #334155; font-size: 10px; font-weight: bold; }
        .today-label { margin-left: 3px; color: #4f46e5; font-size: 7px; font-weight: bold; }
        .count { margin-top: 4px; color: #64748b; font-size: 7px; }
        .dots { margin-top: 3px; line-height: 7px; }
        .dot { display: inline-block; width: 5px; height: 5px; margin-right: 2px; border-radius: 50%; }
        .day-heading { margin: 13px 0 4px; padding: 6px 9px; background: #eef2ff; color: #3730a3; font-weight: bold; page-break-after: avoid; }
        .day-heading-count { color: #6366f1; font-size: 8px; font-weight: normal; }
        .event { padding: 7px 5px; border-bottom: 1px solid #e2e8f0; page-break-inside: avoid; }
        .event table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .event td { vertical-align: top; }
        .event .amount { width: 105px; text-align: right; white-space: nowrap; font-weight: bold; }
        .event-type { font-size: 8px; font-weight: bold; }
        .event-label, .details { word-wrap: break-word; }
        .details { margin-top: 3px; color: #64748b; font-size: 8px; }
        .footer { position: fixed; bottom: -25px; width: 100%; padding-top: 7px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 7px; }
    </style>
</head>
<body>
    @php
        $padding = ($month->dayOfWeek + 6) % 7;
        $weeks = (int) ceil(($padding + $month->daysInMonth) / 7);
        $eventCount = $events->sum(fn ($dayEvents) => $dayEvents->count());
        $pendingFromDate = $generatedAt->format('H:i:s') < '18:00:00'
            ? $generatedAt->toDateString()
            : $generatedAt->copy()->addDay()->toDateString();
        $indicatorColors = [
            'text-emerald-500' => '#059669',
            'text-emerald-600' => '#059669',
            'text-red-500' => '#dc2626',
            'text-purple-500' => '#9333ea',
            'text-blue-500' => '#2563eb',
            'text-amber-500' => '#d97706',
            'text-orange-500' => '#ea580c',
            'text-indigo-500' => '#4f46e5',
        ];
    @endphp
    <table class="hero">
        <tr>
            <td>
                <div class="eyebrow">Finance Pro · Calendário mensal</div>
                <h1>{{ $month->translatedFormat('F Y') }}</h1>
                <p class="subtitle">{{ $workspace->name }} · Gerado em {{ $generatedAt->format('d/m/Y H:i') }}</p>
            </td>
            <td class="hero-month">{{ $month->format('m/Y') }}</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td><div class="summary-label">Registos</div><div class="summary-value">{{ $eventCount }}</div></td>
            <td><div class="summary-label">Dias com registos</div><div class="summary-value">{{ $events->count() }}</div></td>
            <td><div class="summary-label">Dias do mês</div><div class="summary-value">{{ $month->daysInMonth }}</div></td>
        </tr>
    </table>
    <p class="note">Todos os registos do mês selecionado, sem filtros. Inclui valores previstos, que não são confirmação de pagamento.</p>

    <h2>O mês num olhar</h2>
    <p class="section-caption">As cores correspondem aos tipos apresentados no detalhe abaixo.</p>
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
                                $dayEvents = $events->get($date->toDateString(), collect());
                                $count = $dayEvents->count();
                                $colors = $dayEvents->map(fn ($event) => $indicatorColors[$event['color'] ?? ''] ?? '#64748b')->unique()->take(5);
                            @endphp
                            <td class="{{ $date->isSameDay($generatedAt) ? 'today' : ($weekday >= 5 ? 'weekend' : '') }}">
                                <span class="day-number">{{ $day }}</span>
                                @if($date->isSameDay($generatedAt))
                                    <span class="today-label">HOJE</span>
                                @endif
                                @if($count)
                                    <div class="count">{{ $count }} {{ $count === 1 ? 'registo' : 'registos' }}</div>
                                    <div class="dots">
                                        @foreach($colors as $color)
                                            <span class="dot" style="background-color: {{ $color }}"></span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        @endif
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    <h2>Registos do mês</h2>
    <p class="section-caption">Detalhe cronológico de todos os registos.</p>
    @forelse($events->sortKeys() as $date => $dayEvents)
        <div class="day-heading">
            {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
            <span class="day-heading-count"> · {{ $dayEvents->count() }} {{ $dayEvents->count() === 1 ? 'registo' : 'registos' }}</span>
        </div>
        @foreach($dayEvents as $event)
            <div class="event">
                <table>
                    <tr>
                        <td>
                            <div class="event-label"><span class="event-type" style="color: {{ $indicatorColors[$event['color'] ?? ''] ?? '#475569' }}">{{ $event['calendar_icon_label'] ?? 'Registo' }}</span> · {{ $event['label'] }}</div>
                            @if(!empty($event['meta']))
                                <div class="details">{{ $event['meta'] }}</div>
                            @endif
                            @if(!empty($event['notes']))
                                <div class="details">{{ $event['notes'] }}</div>
                            @endif
                            @if(($event['subtype'] ?? null) === 'meal_allowance')
                                <div class="details">{{ match ($event['status']) { 'expected' => 'À espera', 'received' => 'Pago', 'partial' => 'Parcial', 'missed' => 'Não recebido' } }}</div>
                            @elseif(($event['subtype'] ?? null) === 'salary_projection' && $event['date'] >= $pendingFromDate)
                                <div class="details">À espera</div>
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
