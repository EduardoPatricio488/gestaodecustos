<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} — Workbook</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        h1 { font-size: 22px; color: #047857; }
        h2 { font-size: 15px; color: #065f46; margin-top: 24px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        p { margin: 6px 0; }
        ul { margin: 6px 0 6px 18px; }
        .module { margin-bottom: 20px; page-break-inside: avoid; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Workbook completo — todos os módulos em PDF</p>

    @foreach($modules as $module)
        <div class="module">
            <h2>Módulo {{ $module['number'] }}: {{ $module['title'] }}</h2>
            <p>{{ $module['description'] }}</p>
            @if(!empty($module['topics']))
                <ul>
                    @foreach($module['topics'] as $topic)
                        <li>{{ $topic }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach
</body>
</html>
