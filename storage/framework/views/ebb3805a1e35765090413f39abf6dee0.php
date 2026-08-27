<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1f2937; }
        .header { border-bottom: 4px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .period { font-size: 12px; color: #6b7280; }
        .summary-box { background: #f3f4f6; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding: 8px; }
        td { padding: 8px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .amount { text-align: right; font-weight: bold; }
        .total-row { background: #f9fafb; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">Relatório Financeiro: <?php echo e($workspaceName); ?></h2>
        <p class="period">Período: <?php echo e($start); ?> até <?php echo e($end); ?></p>
    </div>

    <div class="summary-box">
        <table style="margin:0">
            <tr>
                <td>Total Receitas: <strong><?php echo e(number_format($totalEarned, 2)); ?> €</strong></td>
                <td>Total Gastos: <strong style="color:#dc2626"><?php echo e(number_format($totalSpent, 2)); ?> €</strong></td>
                <td style="text-align:right">Saldo: <strong><?php echo e(number_format($totalEarned - $totalSpent, 2)); ?> €</strong></td>
            </tr>
        </table>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expenses->count() > 0): ?>
        <h4>Lista de Gastos</h4>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th class="amount">Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><?php echo e($e->spent_at->format('d/m/Y')); ?></td>
                        <td><?php echo e($e->category->name); ?></td>
                        <td><?php echo e($e->description ?: $e->subcategory); ?></td>
                        <td class="amount">-<?php echo e(number_format($e->amount, 2)); ?> €</td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($incomes->count() > 0): ?>
        <h4 style="margin-top:30px">Lista de Receitas</h4>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th class="amount">Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><?php echo e($i->received_at->format('d/m/Y')); ?></td>
                        <td><?php echo e($i->description); ?></td>
                        <td class="amount" style="color:#059669">+<?php echo e(number_format($i->amount, 2)); ?> €</td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div style="margin-top:40px; font-size:9px; text-align:center; color:#999;">
        Documento gerado por <?php echo e($userName); ?> em <?php echo e(now()->format('d/m/Y H:i')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views\pdf\dashboard-report.blade.php ENDPATH**/ ?>