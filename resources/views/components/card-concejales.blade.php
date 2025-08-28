<div class="col-12 col-md-6 col-xl-4">
 <a href="{{ route('crearConcejal') }}" class="text-decoration-none">
    <div class="card border-0 shadow-sm h-100 hover-shadow dashboard-card concejales-card">
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
                        <h2 class="mb-0 fw-bold text-primary counter"
                        data-target="{{ $totalConcejales }}">0</h2>
                    </div>
                </div>
            </div>
             <div class="text-end text-primary counter">
            <span class="ver-mas">Ver más →</span>
            </div>
            
        </div>
        </div>
    </a>
</div>
<style>
.hover-shadow {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15); /* Sombra más fuerte */
    transform: translateY(-3px); /* Se eleva un poco */
}
</style>
