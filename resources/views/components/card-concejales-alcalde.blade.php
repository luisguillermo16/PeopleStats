<div class="col-12 col-md-6 col-xl-4">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-person-badge-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 fw-semibold">
                                <span class="d-none d-lg-inline">Total de Concejales</span>
                                <span class="d-lg-none">Concejales</span>
                            </h6>
                            <small class="text-muted">Activos en el sistema</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-end">
                        <h2 class="mb-0 fw-bold text-primary">{{ $totalConcejales }}</h2>
                        <span class="badge bg-info bg-opacity-10 text-info ms-2">
                            <i class="bi bi-dash"></i>{{ $estado }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-primary" style="width: {{ $progreso }}%"></div>
            </div>
            <small class="text-muted mt-2 d-block">
                <i class="bi bi-info-circle me-1"></i>
                <span class="d-none d-sm-inline">{{ $progreso }}% del cupo disponible</span>
                <span class="d-sm-none">{{ $progreso }}% cupo</span>
            </small>
        </div>
    </div>
</div>
