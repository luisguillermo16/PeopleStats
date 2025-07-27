@php
    $user = auth()->user();
@endphp

@if ($user->hasRole('aspirante-alcaldia') || $user->hasRole('aspirante-concejo'))
<div class="col-6 col-md-3">
    <a href="{{ route('verVotantes') }}" class="btn btn-outline-success w-100 py-3 text-decoration-none">
        <i class="bi bi-people fs-4 d-block mb-2"></i>
        <span class="d-none d-lg-inline">Gestionar Votantes</span>
        <span class="d-lg-none">Votantes</span>
    </a>
</div>
@endif

@if ($user->hasRole('lider'))
    <div class="col-6 col-md-3">
        <a href="{{ route('ingresarVotantes') }}" class="btn btn-outline-primary w-100 py-3 text-decoration-none">
            <i class="bi bi-person-badge fs-4 d-block mb-2"></i>
            <span class="d-none d-lg-inline">Ingresar Votantes</span>
            
        </a>
    </div>
@endif

<!-- Botón: Ver Concejales (solo para alcaldes) -->
@if ($user->hasRole('aspirante-alcaldia'))
    <div class="col-6 col-md-3">
        <a href="{{ route('crearConcejal') }}" class="btn btn-outline-primary w-100 py-3 text-decoration-none">
            <i class="bi bi-person-badge fs-4 d-block mb-2"></i>
            <span class="d-none d-lg-inline">Ver Concejales</span>
            <span class="d-lg-none">Concejales</span>
        </a>
    </div>
@endif

<!-- Botón: Gestionar Líderes (solo para alcaldes y concejales) -->
@if ($user->hasRole('aspirante-alcaldia') || $user->hasRole('aspirante-concejo'))
    <div class="col-6 col-md-3">
        <a href="{{ route('crearLider') }}" class="btn btn-outline-warning w-100 py-3 text-decoration-none">
            <i class="bi bi-person-gear fs-4 d-block mb-2"></i>
            <span class="d-none d-lg-inline">Gestionar Líderes</span>
            <span class="d-lg-none">Líderes</span>
        </a>
    </div>
@endif

<!-- Botón: Ver Reportes (solo para alcaldes) -->
@if ($user->hasRole('aspirante-alcaldia'))
    <div class="col-6 col-md-3">
        <a href="{{ route('reporteAlcalde') }}" class="btn btn-outline-info w-100 py-3 text-decoration-none">
            <i class="bi bi-bar-chart fs-4 d-block mb-2"></i>
            <span class="d-none d-lg-inline">Ver Reportes</span>
            <span class="d-lg-none">Reportes</span>
        </a>
    </div>
@endif
