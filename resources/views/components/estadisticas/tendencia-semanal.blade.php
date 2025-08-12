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
    const trendLabels = @json($labels);         // ej: ["mié", "jue", ...]
    const trendData = @json($data);            // ej: [0,0,0,0,190,0,0]
    const trendDates = @json($labelsFull ?? $labels); // fechas ISO para tooltip si las pasaste

    const ctxTrend = document.getElementById('trendChart').getContext('2d');

    // Crear gradiente
    const gradient = ctxTrend.createLinearGradient(0, 0, 0, 380);
    gradient.addColorStop(0, 'rgba(54,162,235,0.35)');
    gradient.addColorStop(1, 'rgba(54,162,235,0.05)');

    // Marcar el índice máximo para estilizar el punto
    const maxVal = Math.max(...trendData);
    const maxIndex = trendData.indexOf(maxVal);

    // Construimos arrays de colores para puntos (para destacar max)
    const pointBg = trendData.map((v, i) => i === maxIndex ? '#FF6B6B' : '#36A2EB');
    const pointBorder = trendData.map((v, i) => i === maxIndex ? '#fff' : '#fff');
    const pointRadius = trendData.map((v, i) => i === maxIndex ? 7 : 5);

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Votantes Registrados',
                data: trendData,
                borderColor: '#36A2EB',
                backgroundColor: gradient,
                tension: 0.32,
                borderWidth: 2,
                fill: true,
                pointBackgroundColor: pointBg,
                pointBorderColor: pointBorder,
                pointRadius: pointRadius,
                pointHoverRadius: 8,
                segment: {
                  borderJoinStyle: 'round'
                }
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12 }
                },
                tooltip: {
                    callbacks: {
                        title: function(items) {
                            const idx = items[0].dataIndex;
                            // si tienes trendDates (ISO), úsala; sino muestra label corto
                            return trendDates && trendDates[idx] ? trendDates[idx] : items[0].label;
                        },
                        label: function(ctx) {
                            return ` ${ctx.dataset.label}: ${ctx.parsed.y} votantes`;
                        },
                        afterBody: function(items) {
                            // mensaje extra si es el pico
                            const idx = items[0].dataIndex;
                            if (idx === maxIndex) return ['Pico de la semana ✅'];
                        }
                    }
                },
                annotation: undefined
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            },
            animation: {
                duration: 700,
                easing: 'easeOutQuart'
            }
        }
    });
</script>
@endpush
