<!DOCTYPE html>
<html>
<body>
    <h2>Olá, <?php echo e($user->name); ?>! 💰</h2>
    <p>O teu resumo financeiro do mês de <strong><?php echo e($data['monthName']); ?></strong> já está pronto.</p>
    <p>Encontras em anexo o relatório detalhado em PDF com todos os teus gastos e ganhos.</p>
    <br>
    <p>Atenciosamente,<br>Equipa <?php echo e(config('app.name')); ?></p>
</body>
</html>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views\emails\monthly-report.blade.php ENDPATH**/ ?>