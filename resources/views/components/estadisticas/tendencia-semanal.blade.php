<div class="col-12 col-lg-8">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-graph-up me-2"></i>Tendencia Semanal</h5>
        </div>
        <div class="card-body" style="height: 380px;">
            <canvas id="{{ $chartId }}"></canvas>
            <div id="debug-{{ $chartId }}" style="display: none; margin-top: 10px; font-size: 12px; color: #666;"></div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Intentar cargar Chart.js desde múltiples fuentes -->
<script>
// Función para cargar Chart.js
function loadChartJS() {
    return new Promise((resolve, reject) => {
        // Verificar si ya está cargado
        if (typeof Chart !== 'undefined') {
            console.log('TendenciaSemanal: Chart.js already loaded');
            resolve();
            return;
        }

        // Intentar cargar desde CDN principal
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
        script.onload = () => {
            console.log('TendenciaSemanal: Chart.js loaded from cdnjs');
            resolve();
        };
        script.onerror = () => {
            console.warn('TendenciaSemanal: Failed to load from cdnjs, trying jsdelivr');
            
            // Intentar CDN alternativo
            const script2 = document.createElement('script');
            script2.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
            script2.onload = () => {
                console.log('TendenciaSemanal: Chart.js loaded from jsdelivr');
                resolve();
            };
            script2.onerror = () => {
                console.error('TendenciaSemanal: Failed to load Chart.js from all sources');
                reject(new Error('No se pudo cargar Chart.js'));
            };
            document.head.appendChild(script2);
        };
        document.head.appendChild(script);
    });
}

// Cargar Chart.js y luego inicializar el gráfico
loadChartJS().then(() => {
    console.log('TendenciaSemanal: Chart.js loaded successfully, initializing chart...');
    initializeChart();
}).catch((error) => {
    console.error('TendenciaSemanal: Failed to load Chart.js:', error);
    const debugDiv = document.getElementById('debug-{{ $chartId }}');
    if (debugDiv) {
        debugDiv.style.display = 'block';
        debugDiv.innerHTML = '<strong>Error:</strong> No se pudo cargar Chart.js. Verifique su conexión a internet.';
    }
});

function initializeChart() {
    console.log('TendenciaSemanal: Starting chart initialization...');
    
    const trendLabels = @json($labels);
    const trendData = @json($data);
    const trendDates = @json($labelsFull ?? $labels);
    const chartId = @json($chartId);
    
    console.log('TendenciaSemanal: Data received:', {
        labels: trendLabels,
        data: trendData,
        dates: trendDates,
        chartId: chartId
    });

    const canvas = document.getElementById(chartId);
    if (!canvas) {
        console.error('TendenciaSemanal: Canvas element not found:', chartId);
        return;
    }
    console.log('TendenciaSemanal: Canvas found:', canvas);

    const ctxTrend = canvas.getContext('2d');
    if (!ctxTrend) {
        console.error('TendenciaSemanal: Could not get 2D context for canvas:', chartId);
        return;
    }
    console.log('TendenciaSemanal: 2D context obtained');

    // Verificar si Chart está disponible
    if (typeof Chart === 'undefined') {
        console.error('TendenciaSemanal: Chart.js not loaded!');
        const debugDiv = document.getElementById('debug-' + chartId);
        if (debugDiv) {
            debugDiv.style.display = 'block';
            debugDiv.innerHTML = '<strong>Error:</strong> Chart.js no se cargó correctamente. Verifique la conexión a internet.';
        }
        return;
    }
    console.log('TendenciaSemanal: Chart.js is available');

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

    console.log('TendenciaSemanal: Creating chart with data:', {
        labels: trendLabels,
        data: trendData,
        maxVal: maxVal,
        maxIndex: maxIndex
    });

    try {
        const chart = new Chart(ctxTrend, {
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
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { 
                    mode: 'index', 
                    intersect: false 
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            boxWidth: 12,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: function(items) {
                                const idx = items[0].dataIndex;
                                return trendDates && trendDates[idx] ? trendDates[idx] : items[0].label;
                            },
                            label: function(ctx) {
                                return ` ${ctx.dataset.label}: ${ctx.parsed.y} votantes`;
                            },
                            afterBody: function(items) {
                                const idx = items[0].dataIndex;
                                if (idx === maxIndex) return ['Pico de la semana ✅'];
                                return [];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            precision: 0,
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: { 
                            display: false 
                        }
                    }
                },
                animation: {
                    duration: 700,
                    easing: 'easeOutQuart'
                }
            }
        });
        
        console.log('TendenciaSemanal: Chart created successfully:', chart);
        
    } catch (error) {
        console.error('TendenciaSemanal: Error creating chart:', error);
        
        // Mostrar mensaje de error en el canvas
        ctxTrend.font = '16px Arial';
        ctxTrend.fillStyle = '#666';
        ctxTrend.textAlign = 'center';
        ctxTrend.fillText('Error al cargar el gráfico', canvas.width / 2, canvas.height / 2);
        
        // Mostrar detalles del error en el div de debug
        const debugDiv = document.getElementById('debug-' + chartId);
        if (debugDiv) {
            debugDiv.style.display = 'block';
            debugDiv.innerHTML = '<strong>Error:</strong> ' + error.message;
        }
    }
}
</script>
@endpush
