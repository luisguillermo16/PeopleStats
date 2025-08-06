@php
    $user = auth()->user();
@endphp

<div class="row g-3">

    {{-- Botón para Aspirante Alcaldía o Concejo --}}
    @if ($user->hasRole('aspirante-alcaldia') || $user->hasRole('aspirante-concejo'))
        <div class="col-6 col-md-3">
            <a href="{{ route('verVotantes') }}" class="dashboard-action-card votantes-card hover-shadow text-decoration-none">
                <div class="icon bg-success bg-opacity-10">
                    <i class="bi bi-people text-success fs-3"></i>
                </div>
                <h6 class="mt-2 fw-semibold text-dark">Gestionar Votantes</h6>
            </a>
        </div>
    @endif

    {{-- Botón para Líder --}}
    @if ($user->hasRole('lider'))
        <div class="col-6 col-md-3">
            <a href="{{ route('ingresarVotantes') }}" class="dashboard-action-card concejales-card hover-shadow text-decoration-none">
                <div class="icon bg-primary bg-opacity-10">
                    <i class="bi bi-person-badge text-primary fs-3"></i>
                </div>
                <h6 class="mt-2 fw-semibold text-dark">Ingresar Votantes</h6>
            </a>
        </div>
    @endif

    {{-- Botón: Ver Concejales (solo para aspirante-alcaldia) --}}
    @if ($user->hasRole('aspirante-alcaldia'))
        <div class="col-6 col-md-3">
            <a href="{{ route('crearConcejal') }}" class="dashboard-action-card concejales-card hover-shadow text-decoration-none">
                <div class="icon bg-primary bg-opacity-10">
                    <i class="bi bi-person-badge text-primary fs-3"></i>
                </div>
                <h6 class="mt-2 fw-semibold text-dark">Ver Concejales</h6>
            </a>
        </div>
    @endif

    {{-- Botón: Gestionar Líderes (aspirante-alcaldia o aspirante-concejo) --}}
    @if ($user->hasRole('aspirante-alcaldia') || $user->hasRole('aspirante-concejo'))
        <div class="col-6 col-md-3">
            <a href="{{ route('crearLider') }}" class="dashboard-action-card lideres-card hover-shadow text-decoration-none">
                <div class="icon bg-warning bg-opacity-10">
                    <i class="bi bi-person-gear text-warning fs-3"></i>
                </div>
                <h6 class="mt-2 fw-semibold text-dark">Gestionar Líderes</h6>
            </a>
        </div>
    @endif

    {{-- Botón: Ver Reportes (solo para aspirante-alcaldia) --}}
    @if ($user->hasRole('aspirante-alcaldia'))
        <div class="col-6 col-md-3">
            <a href="{{ route('reporteAlcalde') }}" class="dashboard-action-card concejales-card hover-shadow text-decoration-none">
                <div class="icon bg-info bg-opacity-10">
                    <i class="bi bi-bar-chart text-info fs-3"></i>
                </div>
                <h6 class="mt-2 fw-semibold text-dark">Ver Reportes</h6>
            </a>
        </div>
    @endif

</div>
<style>
    .dashboard-action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
    animation: slideInFromRight 0.7s ease-out;
}

.dashboard-action-card .icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.4s ease;
}

/* Hover animado y barra superior */
.dashboard-action-card.hover-shadow::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background-size: 200% 100%;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: gradientShift 3s ease-in-out infinite;
}

.dashboard-action-card.hover-shadow:hover::before {
    opacity: 1;
}

.dashboard-action-card.hover-shadow:hover {
    transform: translateY(-6px) scale(1.015);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 10px 20px rgba(0, 0, 0, 0.05);
}

/* Gradientes según rol */
.votantes-card.hover-shadow::before {
    background: linear-gradient(90deg, #198754, #157347, #146c43);
}
.lideres-card.hover-shadow::before {
    background: linear-gradient(90deg, #f59e0b, #f97316, #d97706);
}
.concejales-card.hover-shadow::before {
    background: linear-gradient(90deg, #0d6efd, #0b5ed7, #0a58ca);
}

/* Animaciones ya existentes */
@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(30px) rotateY(10deg);
    }
    to {
        opacity: 1;
        transform: translateX(0) rotateY(0deg);
    }
}

    </style>