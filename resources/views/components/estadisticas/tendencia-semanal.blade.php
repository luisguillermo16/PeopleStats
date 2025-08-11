<div class="col-12 col-lg-8">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-graph-up me-2"></i>Tendencia Semanal</h5>
        </div>
        <div class="card-body" style="height: 380px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const trendLabels = @json($labels);
    const trendData = @json($data);

    const ctxTrend = document.getElementById('trendChart').getContext('2d');

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Votantes Registrados',
                data: trendData,
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3,
                borderWidth: 2,
                fill: true,
                pointBackgroundColor: '#36A2EB',
                pointBorderColor: '#fff',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
