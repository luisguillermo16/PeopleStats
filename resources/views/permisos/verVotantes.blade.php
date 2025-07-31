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

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Encabezado de la vista --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h2 class="mb-2">
            <i class="bi bi-people me-2"></i>
            <span class="d-none d-sm-inline">Votantes asignados a tu campaña</span>
            <span class="d-sm-none">Mis Votantes</span>
        </h2>
        <p class="text-muted mb-0">
            <span class="d-none d-md-inline">Gestiona y visualiza los votantes registrados bajo tu candidatura</span>
           
        </p>
    </div>
</div>

{{-- Sistema de Filtros Responsive --}}
<div class="p-4 border bg-light rounded mb-4">
    <form method="GET">
        <div class="row align-items-center g-3">
            <!-- Búsqueda principal -->
            <div class="col-12 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" 
                           placeholder="Buscar por nombre o cédula..." 
                           name="search" value="{{ request('search') }}">
                </div>
            </div>
            
            <!-- Filtro por mesa -->
            <div class="col-6 col-md-3 col-lg-2">
                <input name="mesa" type="text" 
                       value="{{ request('mesa') }}" 
                       class="form-control" 
                       placeholder="Mesa">
            </div>
            
            <!-- Filtro por líder -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="lider" class="form-select">
                    <option value="">Todos los líderes</option>
                    @foreach($lideres ?? [] as $lider)
                        <option value="{{ $lider->id }}" {{ request('lider') == $lider->id ? 'selected' : '' }}>
                            {{ $lider->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filtro por concejal -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="concejal" class="form-select">
                    <option value="">Todos los concejales</option>
                    @foreach($concejales ?? [] as $concejal)
                        <option value="{{ $concejal->id }}" {{ request('concejal') == $concejal->id ? 'selected' : '' }}>
                            {{ $concejal->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Botón de búsqueda -->
            <div class="col-6 col-md-3 col-lg-1">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search"></i>
                    <span class="d-none d-lg-inline ms-1">Buscar</span>
                </button>
            </div>
            
            <!-- Botón limpiar filtros -->
            <div class="col-6 col-md-3 col-lg-1">
                <a href="{{ request()->url() }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i>
                    <span class="d-none d-lg-inline ms-1">Limpiar</span>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body py-3">
                <i class="bi bi-people fs-1 text-primary"></i>
                <h4 class="mb-0">{{ $totalVotantes }}</h4>
                <small class="text-muted">
                    <span class="d-none d-sm-inline">Total Votantes</span>
                    <span class="d-sm-none">Total</span>
                </small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-info">
            <div class="card-body py-3">
                <i class="bi bi-person-badge fs-1 text-info"></i>
                <h4 class="mb-0">{{ $totalConcejales }}</h4>
                <small class="text-muted">Concejales</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body py-3">
                <i class="bi bi-grid-3x3-gap fs-1 text-warning"></i>
                <h4 class="mb-0">{{ $totalMesas }}</h4>
                <small class="text-muted">Mesas</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-success">
            <div class="card-body py-3">
                <i class="bi bi-person-badge fs-1 text-success"></i>
                <h4 class="mb-0">{{ $totalLideres }}</h4>
                <small class="text-muted">Líderes</small>
            </div>
        </div>
    </div>
</div>

{{-- Tabla Responsive Estándar --}}
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <!-- Checkbox solo en desktop -->
                <th width="50" class="d-none d-md-table-cell">
                    <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
                <th>Votantes</th>
                <th class="d-none d-md-table-cell">Cédula</th>
                <th class="d-none d-lg-table-cell">Teléfono</th>
                <th class="d-none d-sm-table-cell">Mesa</th>
                <th class="d-none d-lg-table-cell">Líder</th>
                <th class="d-none d-lg-table-cell">Concejal</th>
                <th width="100">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($votantes as $votante)
            <tr>
                <!-- Checkbox -->
                <td class="d-none d-md-table-cell">
                    <input type="checkbox" class="form-check-input item-checkbox">
                </td>
                
                <!-- Información principal del votante -->
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            <i class="bi bi-person-circle fs-4 text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $votante->nombre }}</div>
                            <!-- Info adicional en móvil -->
                            <div class="d-md-none">
                                <small class="text-muted d-block">C.C: {{ $votante->cedula }}</small>
                                @if($votante->telefono)
                                    <small class="text-muted d-block">
                                        <i class="bi bi-telephone me-1"></i>{{ $votante->telefono }}
                                    </small>
                                @endif
                                <small class="text-primary d-block">Mesa: {{ $votante->mesa }}</small>
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- Campos ocultos en móvil -->
                <td class="d-none d-md-table-cell">
                    <span class="badge bg-secondary">{{ $votante->cedula }}</span>
                </td>
                <td class="d-none d-lg-table-cell">
                    @if($votante->telefono)
                        <a href="tel:{{ $votante->telefono }}" class="text-decoration-none">
                            <i class="bi bi-telephone me-1"></i>{{ $votante->telefono }}
                        </a>
                    @else
                        <span class="text-muted">No registrado</span>
                    @endif
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge bg-primary">Mesa {{ $votante->mesa }}</span>
                </td>
                <td class="d-none d-lg-table-cell">
                    @if($votante->lider)
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-badge me-2 text-success"></i>
                            <span>{{ $votante->lider->name }}</span>
                        </div>
                    @else
                        <span class="text-muted">Sin asignar</span>
                    @endif
                </td>
                <td class="d-none d-lg-table-cell">
                    @php
                        $concejal = null;
                        if($votante->concejal_id) {
                            $concejal = App\Models\User::find($votante->concejal_id);
                        }
                    @endphp
                    @if($concejal)
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-badge me-2 text-info"></i>
                            <span>{{ $concejal->name }}</span>
                        </div>
                    @elseif($votante->concejal_id)
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-badge me-2 text-warning"></i>
                            <span>ID: {{ $votante->concejal_id }}</span>
                        </div>
                    @else
                        <span class="text-muted">Sin asignar</span>
                    @endif
                </td>
                
                <!-- Acciones -->
                <td>
                    <div class="btn-group" role="group">
                        <!-- Botón ver detalles -->
                        <button class="btn btn-sm btn-outline-info" 
                                title="Ver detalles"
                                data-bs-toggle="modal"
                                data-bs-target="#detailModal{{ $votante->id }}">
                            <i class="bi bi-eye"></i>
                        </button>
                        
                      
                    </div>
                </td>
            </tr>


        @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No hay votantes registrados</h4>
                    <p class="text-muted">Aún no hay votantes asignados a tu campaña.</p>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación Responsive --}}
<x-paginacion :collection="$votantes"  />

{{-- JavaScript Responsive --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los checkboxes
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Manejo responsive de tablas
    function handleTableResponsive() {
        const tables = document.querySelectorAll('.table-responsive');
        tables.forEach(table => {
            if (window.innerWidth < 768) {
                table.classList.add('table-mobile');
            } else {
                table.classList.remove('table-mobile');
            }
        });
    }
    
    // Ejecutar al cargar y redimensionar
    handleTableResponsive();
    window.addEventListener('resize', handleTableResponsive);
});
</script>

<style>
/* CSS personalizado para mejorar responsive */
@media (max-width: 767px) {
    .btn-group {
        flex-direction: row;
        justify-content: center;
    }
    
    .table-mobile th,
    .table-mobile td {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }
    
    .user-avatar i {
        font-size: 1.5rem !important;
    }
}


</style>

@endsection