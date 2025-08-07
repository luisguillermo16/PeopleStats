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
        <x-buscar-cedula />
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
                <x-acciones-rapidas />
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
