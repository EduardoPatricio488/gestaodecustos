<div class="bg-white p-6 rounded-xl shadow">
    <h2 class="text-lg font-bold mb-4">Despesas por Categoria</h2>

    <canvas id="expenseChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('expenseChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels, 15, 512) ?>,
            datasets: [{
                label: '€ Gastos',
                data: <?php echo json_encode($data, 15, 512) ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script><?php /**PATH C:\Projetos\gestao-de-custos\resources\views\livewire\expense-chart.blade.php ENDPATH**/ ?>