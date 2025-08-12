<div class="col-12 col-lg-4">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-pie-chart me-2"></i>Distribución por Barrio</h5>
        </div>
        <div class="card-body">
            <canvas id="barriosPieChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const dataBarrios = @json($votantesPorBarrio);

    const ctx = document.getElementById('barriosPieChart').getContext('2d');

    new Chart(ctx, {
        type: 'pie', // ← Aquí el cambio
        data: {
            labels: dataBarrios.map(b => b.nombre),
            datasets: [{
                label: 'Cantidad de Votantes',
                data: dataBarrios.map(b => b.total),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#66FF66', '#FF66B2',
                    '#3399FF', '#FF6666', '#9999FF', '#66FFFF'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw} votantes`;
                        }
                    }
                }
            }
        }
    });
</script>
