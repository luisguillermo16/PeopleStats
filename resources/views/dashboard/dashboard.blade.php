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
        <x-estadistica-barrios />
        <x-tendencia-semanal />
            
    @elseif ($rol == 'aspirante-concejo')
        <x-card-votantes />
        <x-card-lideres />
        <div class="col invisible">
            {{-- Si quieres, puedes dejar vacío o poner un placeholder transparente --}}
        </div>
         
        <x-estadistica-barrios />          
        <x-tendencia-semanal />

    @elseif ($rol == 'lider')
       
        {{-- Tarjeta de votantes --}}
        <x-card-votantes />
         {{-- Barra de búsqueda arriba --}}
        <div class="col-12 mb-3">
            <x-buscar-cedula />
        </div>
        {{-- Gráficas lado a lado en desktop, apiladas en móvil --}}
        <div class="d-flex flex-column flex-lg-row gap-3">
            <x-estadistica-barrios/>
            <x-tendencia-semanal/>
        </div>
            
    @else
        <div class="alert alert-danger">Rol no válido</div>
    @endif
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
           
        </div>
    </div>
</div>

@endsection