@extends('layouts.admin')

@section('tituloPage', 'Reportes de Campaña')

@section('contenido')

{{-- Alertas simuladas --}}
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>Reporte generado correctamente.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

{{-- Encabezado --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h2 class="mb-2">
            <i class="bi bi-bar-chart me-2"></i>Reportes de Campaña
        </h2>
        <p class="text-muted mb-0">Análisis detallado del rendimiento de tu campaña electoral</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button class="btn btn-success">
            <i class="bi bi-download me-2"></i>Exportar Reporte
        </button>
    </div>
</div>

{{-- Resumen General --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card text-center border-primary h-100">
            <div class="card-body py-3">
                <i class="bi bi-people fs-1 text-primary"></i>
                <h3 class="mb-0">1234</h3>
                <small class="text-muted">Total Votantes</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card text-center border-success h-100">
            <div class="card-body py-3">
                <i class="bi bi-person-badge fs-1 text-success"></i>
                <h3 class="mb-0">56</h3>
                <small class="text-muted">Líderes Activos</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card text-center border-info h-100">
            <div class="card-body py-3">
                <i class="bi bi-person-check fs-1 text-info"></i>
                <h3 class="mb-0">12</h3>
                <small class="text-muted">Concejales</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card text-center border-warning h-100">
            <div class="card-body py-3">
                <i class="bi bi-grid-3x3-gap fs-1 text-warning"></i>
                <h3 class="mb-0">8</h3>
                <small class="text-muted">Mesas</small>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="p-4 border bg-light rounded mb-4">
    <form>
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Período</label>
                <select class="form-select">
                    <option>Todo el tiempo</option>
                    <option>Último mes</option>
                    <option>Última semana</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Tipo de Reporte</label>
                <select class="form-select">
                    <option>Completo</option>
                    <option>Solo Líderes</option>
                    <option>Solo Concejales</option>
                    <option>Por Mesas</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">&nbsp;</label>
                <button class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-2"></i>Aplicar Filtros
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#lideres">
            <i class="bi bi-person-badge me-2"></i>Líderes
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#concejales">
            <i class="bi bi-person-check me-2"></i>Concejales
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mesas">
            <i class="bi bi-grid-3x3-gap me-2"></i>Mesas
        </button>
    </li>
</ul>

<div class="tab-content">
    {{-- Simulación líderes --}}
    <div class="tab-pane fade show active" id="lideres">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-person-badge me-2"></i>Rendimiento por Líderes</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Líder</th>
                            <th class="text-center">Votantes</th>
                            <th class="text-center">Con Tel.</th>
                            <th class="text-center">Último Reg.</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="bi bi-trophy-fill text-warning"></i></td>
                            <td>Juan Pérez</td>
                            <td class="text-center"><span class="badge bg-primary">25</span></td>
                            <td class="text-center"><span class="badge bg-success">20</span></td>
                            <td class="text-center">2025-07-24</td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-gradient" style="width: 25%"><small class="fw-bold">25%</small></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-trophy-fill text-secondary"></i></td>
                            <td>Ana Gómez</td>
                            <td class="text-center"><span class="badge bg-primary">15</span></td>
                            <td class="text-center"><span class="badge bg-success">10</span></td>
                            <td class="text-center">2025-07-23</td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-gradient" style="width: 15%"><small class="fw-bold">15%</small></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Concejales y Mesas (puedes replicar igual con texto simulado) --}}
    <div class="tab-pane fade" id="concejales">
        <p class="text-center mt-3">Tabla de concejales (simulación)</p>
    </div>
    <div class="tab-pane fade" id="mesas">
        <p class="text-center mt-3">Resumen por mesas (simulación)</p>
    </div>
</div>

{{-- Gráficos de ejemplo --}}
<div class="row g-4 mt-4">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><h5><i class="bi bi-bar-chart me-2"></i>Tendencia de Registro</h5></div>
            <div class="card-body">
                <canvas id="tendenciaChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><h5><i class="bi bi-pie-chart me-2"></i>Distribución por Rol</h5></div>
            <div class="card-body">
                <canvas id="distribucionChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
new Chart(document.getElementById('tendenciaChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves'],
        datasets: [{
            label: 'Votantes Registrados',
            data: [5, 10, 15, 8],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

new Chart(document.getElementById('distribucionChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Con Líder', 'Con Concejal', 'Sin Asignar'],
        datasets: [{
            data: [60, 30, 10],
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ]
        }]
    },
    options: { responsive: true, maintainAspectRatio: true }
});
</script>

<style>
.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 50%;
}
</style>

@endsection
