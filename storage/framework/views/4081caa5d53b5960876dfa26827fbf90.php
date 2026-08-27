<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório Mensal</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        .header { background: #111827; color: #fff; padding: 16px; border-radius: 10px; }
        .kpi-grid { width: 100%; margin-top: 14px; border-collapse: collapse; }
        .kpi-grid td { width: 33.3%; border: 1px solid #e5e7eb; padding: 10px; vertical-align: top; }
        .kpi-label { color: #6b7280; font-size: 10px; text-transform: uppercase; }
        .kpi-value { font-size: 18px; font-weight: bold; margin-top: 4px; }
        table.stats { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.stats th, table.stats td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        table.stats th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; }
        .right { text-align: right; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">Relatório Financeiro Mensal</h2>
        <p style="margin:6px 0 0;" class="muted"><?php echo e($monthName); ?> / <?php echo e($year); ?></p>
    </div>

    <table class="kpi-grid">
        <tr>
            <td>
                <div class="kpi-label">Ganhos</div>
                <div class="kpi-value"><?php echo e(number_format($earned, 2, ',', '.')); ?> €</div>
            </td>
            <td>
                <div class="kpi-label">Gastos</div>
                <div class="kpi-value"><?php echo e(number_format($spent, 2, ',', '.')); ?> €</div>
            </td>
            <td>
                <div class="kpi-label">Saldo</div>
                <div class="kpi-value"><?php echo e(number_format($balance, 2, ',', '.')); ?> €</div>
            </td>
        </tr>
    </table>

    <h3 style="margin-top:18px;">Despesas por Categoria</h3>
    <table class="stats">
        <thead>
            <tr>
                <th>Categoria</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $categoryStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td><?php echo e($item->name); ?></td>
                    <td class="right"><?php echo e(number_format($item->total, 2, ',', '.')); ?> €</td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="2" class="muted">Sem despesas registadas no período.</td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <p class="muted" style="margin-top:18px;">Gerado automaticamente por <?php echo e(config('app.name')); ?> em <?php echo e(now()->format('d/m/Y H:i')); ?>.</p>
</body>
</html>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views\pdf\monthly-report.blade.php ENDPATH**/ ?>