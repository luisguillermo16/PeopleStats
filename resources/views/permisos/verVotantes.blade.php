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
<div class="p-3 p-md-4 border bg-light rounded mb-4">
    <form method="GET">
        <div class="row align-items-end g-2 g-md-3">
            <!-- Búsqueda principal -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" 
                           placeholder="Buscar por nombre o cédula..." 
                           name="search" value="{{ request('search') }}">
                </div>
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
            
           @auth
            @if(auth()->user()->hasRole('aspirante-alcaldia'))
                <!-- Filtro por concejales -->
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
            @endif
        @endauth
            
            <!-- Botones de acción -->
            <div class="col-6 col-md-3 col-lg-2">
                <div class="d-grid d-md-block">
                    <button class="btn btn-outline-primary w-100" type="submit">
                        <i class="bi bi-search me-1"></i>
                        <span class="d-none d-sm-inline">Buscar</span>
                        <span class="d-sm-none">OK</span>
                    </button>
                </div>
            </div>
            
            <!-- Botón limpiar filtros -->
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ request()->url() }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i>
                    <span class="d-none d-lg-inline">Limpiar</span>
                    <span class="d-lg-none">Limpiar</span>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Tarjetas cuadradas compactas con descripción --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-sm-4 col-md-3">
        <div class="card text-center border-primary square-card">
            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                <i class="bi bi-people fs-3 text-primary"></i>
                <h5 class="mb-1 mt-1">{{ $totalVotantesFiltrados }}</h5>
                <small class="text-muted text-center fs-7">
                    @if(request()->hasAny(['search', 'lider', 'concejal']))
                        Votantes Filtrados
                    @else
                        Total Votantes
                    @endif
                </small>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasRole('aspirante-alcaldia'))
    <div class="col-6 col-sm-4 col-md-3">
        <div class="card text-center border-info square-card">
            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                <i class="bi bi-person-badge fs-3 text-info"></i>
                <h5 class="mb-1 mt-1">{{ $totalConcejalesFiltrados }}</h5>
                <small class="text-muted text-center fs-7">
                    @if(request()->hasAny(['search', 'lider', 'concejal']))
                        Concejales Activos
                    @else
                        Concejales
                    @endif
                </small>
            </div>
        </div>
    </div>
    @endif

    <div class="col-6 col-sm-4 col-md-3">
        <div class="card text-center border-warning square-card">
            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                <i class="bi bi-grid-3x3-gap fs-3 text-warning"></i>
                <h5 class="mb-1 mt-1">{{ $totalMesasFiltradas }}</h5>
                <small class="text-muted text-center fs-7">
                    @if(request()->hasAny(['search', 'lider', 'concejal']))
                        Mesas Activas
                    @else
                        Mesas
                    @endif
                </small>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-4 col-md-3">
        <div class="card text-center border-success square-card">
            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                <i class="bi bi-person-badge fs-3 text-success"></i>
                <h5 class="mb-1 mt-1">{{ $totalLideresFiltrados }}</h5>
                <small class="text-muted text-center fs-7">
                    @if(request()->hasAny(['search', 'lider', 'concejal']))
                        Líderes Activos
                    @else
                        Líderes
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Vista Desktop/Tablet - Tabla --}}
<div class="d-none d-md-block">
    <div class="table-responsive">
        <table class="table rounded border shadow-sm overflow-hidden">
            <thead>
                <tr>
                    <th class="d-none d-lg-table-cell">Votante</th>
                    <th class="d-none d-lg-table-cell" >Cédula</th>
                    <th class="d-none d-lg-table-cell">Teléfono</th>
                    <th class="d-none d-lg-table-cell">Mesa</th>
                    <th class="d-none d-lg-table-cell">Barrio</th>
                    <th class="d-none d-lg-table-cell">Líder</th>
                    <th class="d-none d-lg-table-cell">Concejal</th>
                 
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($votantes as $votante)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person fs-4 me-2"></i>
                                <div>
                                    <div class="fw-semibold">{{ $votante->nombre }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="fw-semibold">{{ $votante->cedula }}</span></td>
                        <td class="d-none d-lg-table-cell">
                            @if($votante->telefono)
                                <a href="tel:{{ $votante->telefono }}" class="text-decoration-none">
                                    <i class="bi bi-telephone me-1"></i>{{ $votante->telefono }}
                                </a>
                            @else
                                <span class="text-muted">No registrado</span>
                            @endif
                        </td>
                        <td><span class="fw-semibold">Mesa {{ $votante->mesa->numero }}</span></td>
                         <td class="d-none d-lg-table-cell">{{ $votante->barrio->nombre ?? 'Sin asignar' }}</td>
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
                       
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2"></i><br>
                            No hay votantes registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Vista Móvil - Cards --}}
<div class="d-md-none">
    @forelse ($votantes as $votante)
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-person-circle fs-4 me-2 text-primary mt-1"></i>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="card-title mb-1 text-truncate">{{ $votante->nombre }}</h6>
                                <p class="card-text small text-muted mb-1 text-truncate">
                                    <i class="bi  me-1"></i>C.C: {{ $votante->cedula }}
                                </p>
                                @if($votante->telefono)
                                    <p class="card-text small text-muted mb-1 text-truncate">
                                        <i class="bi bi-telephone me-1"></i>
                                        <a href="tel:{{ $votante->telefono }}" class="text-muted">{{ $votante->telefono }}</a>
                                    </p>
                                @endif
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <small class="badge bg-primary">
                                        <i class="bi bi-grid me-1"></i>Mesa {{ $votante->mesa->numero }}
                                    </small>
                                    @if($votante->lider)
                                        <small class="text-muted text-truncate">
                                            <i class="bi bi-person-badge me-1"></i>{{ $votante->lider->name }}
                                        </small>
                                    @endif
                                </div>
                                @if($votante->concejal_id)
                                    @php
                                        $concejal = App\Models\User::find($votante->concejal_id);
                                    @endphp
                                    @if($concejal)
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="bi bi-person-badge me-1"></i>Concejal: {{ $concejal->name }}
                                            </small>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item">
                                        <i class="bi bi-pencil me-2"></i>Editar
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item">
                                        <i class="bi bi-eye me-2"></i>Ver detalles
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger">
                                        <i class="bi bi-trash me-2"></i>Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-2">No hay votantes registrados</p>
        </div>
    @endforelse
</div>

{{-- Paginación Responsive --}}
<x-paginacion :collection="$votantes" />

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

/* Mejoras adicionales para las cards */
.card {
    transition: all 0.2s ease;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    transform: translateY(-1px);
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.min-w-0 {
    min-width: 0;
}
</style>

@endsection