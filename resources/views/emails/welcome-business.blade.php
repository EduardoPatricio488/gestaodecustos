<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1f2937; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 1px solid #e5e7eb; border-radius: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .brand { color: #10b981; font-weight: 800; font-size: 24px; text-transform: uppercase; }
        .content { font-size: 16px; }
        .footer { margin-top: 40px; font-size: 12px; color: #9ca3af; text-align: center; }
        .btn { display: inline-block; padding: 14px 28px; background-color: #10b981; color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="brand">Finance Pro IA</div>
        </div>
        <div class="content">
            <h1 style="font-style: italic; letter-spacing: -1px;">Olá, {{ auth()->user()->name }}!</h1>
            <p>Parabéns! Acabaste de ativar o ecossistema empresarial para a <strong>{{ $workspace->name }}</strong>.</p>
            <p>A partir de agora, tens acesso a ferramentas de gestão de elite:</p>
            <ul>
                <li>Gestão de Projetos e Margens</li>
                <li>Controlo de Custos Operacionais</li>
                <li>IA Estrategista de Negócio</li>
                <li>Gestão de Equipa e Colaboradores</li>
            </ul>
            <p>O capital inicial registado foi de: <strong>{{ number_format($workspace->initial_capital, 2) }}€</strong>.</p>

            <a href="{{ route('hub.business.dashboard') }}" class="btn">Entrar no Terminal Business</a>
        </div>
        <div class="footer">
            © {{ date('Y') }} Finance Pro IA · Gestão Profissional de Ativos
        </div>
    </div>
</body>
</html>
