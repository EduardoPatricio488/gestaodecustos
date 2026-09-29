<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada — Finance Pro IA</title>
    <style>
        :root {
            color-scheme: light dark;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #fafafa;
            color: #18181b;
        }

        .container {
            width: min(680px, calc(100% - 40px));
            text-align: center;
            padding: 40px 20px;
        }

        .code {
            margin: 0;
            font-size: clamp(96px, 20vw, 180px);
            line-height: .85;
            font-weight: 900;
            font-style: italic;
            letter-spacing: -0.08em;
            color: #e4e4e7;
        }

        .icon {
            width: 76px;
            height: 76px;
            margin: -8px auto 28px;
            display: grid;
            place-items: center;
            border-radius: 24px;
            background: #10b981;
            color: white;
            font-size: 38px;
            box-shadow: 0 20px 45px rgba(16, 185, 129, .25);
        }

        h1 {
            margin: 0;
            font-size: clamp(28px, 5vw, 40px);
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        p {
            margin: 14px auto 30px;
            max-width: 520px;
            color: #71717a;
            line-height: 1.6;
        }

        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            padding: 0 28px;
            border-radius: 16px;
            background: #059669;
            color: white;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: .04em;
            transition: transform .15s ease, background .15s ease;
        }

        a:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        @media (prefers-color-scheme: dark) {
            body { background: #09090b; color: #fafafa; }
            .code { color: #27272a; }
            p { color: #a1a1aa; }
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="code">404</div>
        <div class="icon" aria-hidden="true">⌕</div>
        <h1>Página não encontrada</h1>
        <p>Parece que este cofre financeiro não existe ou foi movido para outra localização.</p>
        <a href="{{ url('/') }}">Voltar ao início</a>
    </main>
</body>
</html>
