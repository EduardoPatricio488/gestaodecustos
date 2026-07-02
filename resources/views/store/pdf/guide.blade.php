<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        h1 { font-size: 22px; color: #047857; margin-bottom: 4px; }
        h2 { font-size: 16px; color: #065f46; margin-top: 24px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        p { margin: 8px 0; }
        ul { margin: 8px 0 8px 20px; }
        .cover { text-align: center; padding: 80px 40px; }
        .badge { display: inline-block; background: #ecfdf5; color: #047857; padding: 4px 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .footer { position: fixed; bottom: 0; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="cover">
        <p class="badge">Finance Hub PRO — Guia PDF</p>
        <h1>{{ $title }}</h1>
        <p>Documento exclusivo para clientes</p>
    </div>

    <pagebreak />

    @foreach($sections as $section)
        <h2>{{ $section['title'] }}</h2>
        @foreach($section['paragraphs'] as $paragraph)
            <p>{{ $paragraph }}</p>
        @endforeach
        @if(!empty($section['bullets']))
            <ul>
                @foreach($section['bullets'] as $bullet)
                    <li>{{ $bullet }}</li>
                @endforeach
            </ul>
        @endif
    @endforeach

    <div class="footer">© Finance Hub PRO — Uso pessoal licenciado</div>
</body>
</html>
