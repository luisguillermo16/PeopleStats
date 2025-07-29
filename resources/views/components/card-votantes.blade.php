@if ($rol === 'aspirante-alcaldia' || $rol === 'aspirante-concejo')
<div class="col-12 col-md-6 col-xl-4">
     <div class="card border-0 shadow-sm h-100 hover-shadow">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 fw-semibold">
                                <span class="d-none d-lg-inline">Total de Votantes Únicos</span>
                                <span class="d-lg-none">Votantes Únicos</span>
                            </h6>
                            <small class="text-muted">Registrados en el sistema</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-end">
                        <h2 class="mb-0 fw-bold text-success">{{ number_format($totalVotantes) }}</h2>
                      
                    </div>
                </div>
            </div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-success" style="width: {{ $porcentajeObjetivo }}%"></div>
            </div>
            <small class="text-muted mt-2 d-block">
                <i class="bi bi-info-circle me-1"></i>
                <span class="d-none d-sm-inline">{{ $porcentajeObjetivo }}% del objetivo alcanzado</span>
                <span class="d-sm-none">{{ $porcentajeObjetivo }}% objetivo</span>
            </small>
        </div>
    </div>
</div>
@endif
@if ($rol === 'lider')
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h6 class="card-title fw-semibold">Total de Votantes Registrados por ti</h6>
                <h2 class="fw-bold text-success">{{ number_format($totalVotantes) }}</h2>
              
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: {{ $porcentajeObjetivo }}%"></div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="bi bi-info-circle me-1"></i>
                    {{ $porcentajeObjetivo }}% del objetivo alcanzado
                </small>
            </div>
        </div>
    </div>
@endif
<style>
.hover-shadow {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
    transform: translateY(-3px);
}
</style>
