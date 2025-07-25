@extends('layouts.admin')

@section('tituloPage', 'Panel de Control')

@section('contenido')

{{-- Alertas --}}
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

@php
    $rol = auth()->user()->getRoleNames()->first();
@endphp

<!-- Cards dinámicas según rol -->
<div class="row g-4 mb-4">
    @if ($rol == 'aspirante-alcaldia')
        <x-card-votantes />
        <x-card-concejales />
        <x-card-lideres />
    @elseif ($rol == 'aspirante-concejo')
        <x-card-votantes />
        <x-card-lideres />
    @elseif ($rol == 'lider')
        <x-card-votantes />
    @else
        <div class="alert alert-danger">Rol no válido</div>
    @endif
</div>
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
