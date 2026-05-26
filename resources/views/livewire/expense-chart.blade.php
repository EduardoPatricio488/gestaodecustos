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
            labels: @json($labels),
            datasets: [{
                label: '€ Gastos',
                data: @json($data),
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
</script>
