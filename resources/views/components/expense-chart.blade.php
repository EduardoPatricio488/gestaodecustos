<div>
    <canvas id="expenseChart"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            const ctx = document.getElementById('expenseChart').getContext('2d');

            const data = {
                labels: @json($labels),   // Ex.: ["Alimentação", "Transporte"]
                datasets: [{
                    label: 'Despesas por Categoria',
                    data: @json($data),   // Ex.: [120, 75, 40]
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'bar', // ou 'pie', 'line', etc
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Despesas por Categoria'
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>
</div>
