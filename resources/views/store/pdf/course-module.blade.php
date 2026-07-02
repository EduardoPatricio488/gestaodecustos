<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>{{ $courseTitle }} — Módulo {{ $module['number'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        h1 { font-size: 18px; color: #047857; }
        h2 { font-size: 14px; color: #065f46; margin-top: 20px; }
        p { margin: 8px 0; }
        ul { margin: 8px 0 8px 20px; }
        .meta { font-size: 10px; color: #6b7280; margin-bottom: 16px; }
    </style>
</head>
<body>
    <p class="meta">{{ $courseTitle }} · Módulo {{ $module['number'] }} · {{ $module['duration'] ?? '' }}</p>
    <h1>{{ $module['title'] }}</h1>
    <p>{{ $module['description'] }}</p>

    @if(!empty($module['topics']))
        <h2>Conteúdos do módulo</h2>
        <ul>
            @foreach($module['topics'] as $topic)
                <li>{{ $topic }}</li>
            @endforeach
        </ul>
    @endif

    @if(!empty($module['exercises']))
        <h2>Exercícios práticos</h2>
        <ul>
            @foreach($module['exercises'] as $exercise)
                <li>{{ $exercise }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
