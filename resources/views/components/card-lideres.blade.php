<div class="col-12 col-md-6 col-xl-4">
<a href="{{ route('crearLider') }}" class="text-decoration-none">
    <div class="card border-0 shadow-sm h-100 hover-shadow dashboard-card lideres-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-people-fill text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 fw-semibold">
                                <span class="d-none d-lg-inline">Total de Líderes</span>
                                <span class="d-lg-none">Líderes</span>
                            </h6>
                            <small class="text-muted">Activos en el sistema</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-end">
                        <h2 class="mb-0 fw-bold text-warning counter"
                        data-target="{{ $totalLideres }}">0</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </a>
</div>


