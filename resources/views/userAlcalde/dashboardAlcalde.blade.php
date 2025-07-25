@extends('layouts.admin')
@section('tituloPage', '')
@section('contenido')

{{-- Sistema de Alertas Estándar --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Header del Dashboard -->
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-speedometer2 me-2"></i>
                <span class="d-none d-sm-inline">Dashboard del Alcalde</span>
                <span class="d-sm-none">Dashboard</span>
            </h1>
            <p class="text-muted mb-0">Resumen general del sistema electoral</p>
        </div>
        <div class="mt-3 mt-md-0">
            <small class="text-muted">
                <i class="bi bi-clock me-1"></i>Última actualización: Hoy, 2:30 PM
            </small>
        </div>
    </div>
</div>

<!-- Cards Principales -->
<div class="row g-4 mb-4">
    <!-- Card: Total de Votantes Únicos -->
    <x-card-votantes-alcalde/>
   
    <!-- Card: Total de Concejales -->
  <x-card-concejales-alcalde/>

    <!-- Card: Total de Líderes -->
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
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
                                <small class="text-muted">Registrados y activos</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-end">
                            <h2 class="mb-0 fw-bold text-warning">156</h2>
                            <span class="badge bg-warning bg-opacity-10 text-warning ms-2">
                                <i class="bi bi-arrow-up-short"></i>+12%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-warning" style="width: 75%"></div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="bi bi-info-circle me-1"></i>
                    <span class="d-none d-sm-inline">75% con actividad reciente</span>
                    <span class="d-sm-none">75% activos</span>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Acciones Rápidas -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning-charge me-2"></i>
                    <span class="d-none d-sm-inline">Acciones Rápidas</span>
                    <span class="d-sm-none">Acciones</span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <!-- Botón: Gestionar Votantes -->
                    <div class="col-6 col-md-3">
                        <a href="{{ route('votantesAlcalde') }}" class="btn btn-outline-success w-100 py-3 text-decoration-none">
                            <i class="bi bi-people fs-4 d-block mb-2"></i>
                            <span class="d-none d-lg-inline">Gestionar Votantes</span>
                            <span class="d-lg-none">Votantes</span>
                        </a>
                    </div>
                    
                    <!-- Botón: Ver Concejales -->
                    <div class="col-6 col-md-3">
                        <a href="{{ route('crearConcejal') }}" class="btn btn-outline-primary w-100 py-3 text-decoration-none">
                            <i class="bi bi-person-badge fs-4 d-block mb-2"></i>
                            <span class="d-none d-lg-inline">Ver Concejales</span>
                            <span class="d-lg-none">Concejales</span>
                        </a>
                    </div>
                    
                    <!-- Botón: Gestionar Líderes -->
                    <div class="col-6 col-md-3">
                        <a href="{{ route('crearLider') }}" class="btn btn-outline-warning w-100 py-3 text-decoration-none">
                            <i class="bi bi-person-gear fs-4 d-block mb-2"></i>
                            <span class="d-none d-lg-inline">Gestionar Líderes</span>
                            <span class="d-lg-none">Líderes</span>
                        </a>
                    </div>
                    
                    <!-- Botón: Reportes -->
                    <div class="col-6 col-md-3">
                        <a href="{{ route('reporteAlcalde') }}" class="btn btn-outline-info w-100 py-3 text-decoration-none">
                            <i class="bi bi-bar-chart fs-4 d-block mb-2"></i>
                            <span class="d-none d-lg-inline">Ver Reportes</span>
                            <span class="d-lg-none">Reportes</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de contadores al cargar la página
    function animateCounters() {
        const counters = document.querySelectorAll('h2[class*="fw-bold"]');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/,/g, ''));
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.ceil(current).toLocaleString();
                }
            }, 20);
        });
    }
    
    // Ejecutar animación después de un pequeño delay
    setTimeout(animateCounters, 500);
    
    // Hover effects para las cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection