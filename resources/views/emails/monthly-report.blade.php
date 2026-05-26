<!DOCTYPE html>
<html>
<body>
    <h2>Olá, {{ $user->name }}! 💰</h2>
    <p>O teu resumo financeiro do mês de <strong>{{ $data['monthName'] }}</strong> já está pronto.</p>
    <p>Encontras em anexo o relatório detalhado em PDF com todos os teus gastos e ganhos.</p>
    <br>
    <p>Atenciosamente,<br>Equipa {{ config('app.name') }}</p>
</body>
</html>
