@extends('layouts.admin')

@section('tituloPage', 'Gestión de Concejales')

@section('contenido')

{{-- Alertas de éxito --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Alertas de error --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Errores de validación --}}
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

<!-- Botón Nuevo Concejal - Responsive -->
<div class="mb-3 text-end">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-2"></i>
        <span class="d-none d-sm-inline">Nuevo Concejal</span>
        <span class="d-sm-none">Nuevo</span>
    </button>
</div>

<!-- Filtros Responsive -->
<div class="p-4 border bg-light rounded mb-4">
    <form method="GET">
        <div class="row align-items-center g-3">
            <!-- Búsqueda principal -->
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" 
                           placeholder="Buscar por nombre, email o partido" 
                           name="search" value="{{ request('search') }}">
                </div>
            </div>
            
            <!-- Número de lista -->
            <div class="col-6 col-md-2">
                <input name="numero_lista" type="number" min="1" 
                       value="{{ request('numero_lista') }}" 
                       class="form-control" 
                       placeholder="N° Lista">
            </div>
            
            <!-- Botón de búsqueda -->
            <div class="col-6 col-md-1">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search"></i>
                    <span class="d-none d-lg-inline ms-1">Buscar</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Tabla Responsive -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <!-- Checkbox solo en desktop -->
                <th width="50" class="d-none d-md-table-cell">
                    <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
                <th>Concejal</th>
                <th class="d-none d-md-table-cell">Email</th>
                <th class="d-none d-lg-table-cell">Partido</th>
                <th class="d-none d-lg-table-cell">N° Lista</th>
                <th class="d-none d-sm-table-cell">Fecha</th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($concejales as $concejal)
            <tr>
                <!-- Checkbox -->
                <td class="d-none d-md-table-cell">
                    <input type="checkbox" class="form-check-input item-checkbox">
                </td>
                
                <!-- Información principal -->
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            <i class="bi bi-person fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $concejal->name }}</div>
                            <!-- Info adicional en móvil -->
                            <div class="d-md-none">
                                <small class="text-muted">{{ $concejal->email }}</small>
                                @if($concejal->concejal->partido_politico)
                                    <div><small class="text-muted">{{ $concejal->concejal->partido_politico }}</small></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- Campos ocultos en móvil -->
                <td class="d-none d-md-table-cell">{{ $concejal->email }}</td>
                <td class="d-none d-lg-table-cell">{{ $concejal->concejal->partido_politico ?? 'Sin partido' }}</td>
                <td class="d-none d-lg-table-cell">{{ $concejal->concejal->numero_lista ?? 'N/A' }}</td>
                <td class="d-none d-sm-table-cell">{{ $concejal->created_at->format('d/m/Y') }}</td>
                
                <!-- Acciones -->
                <td>
                    <div class="btn-group" role="group">
                        <!-- Botón editar -->
                        <button class="btn btn-sm btn-outline-primary" 
                                title="Editar"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $concejal->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        
                        <!-- Botón eliminar -->
                        <form method="POST" action="{{ route('admin.concejales.destroy', $concejal) }}" 
                              style="display:inline" 
                              onsubmit="return confirm('¿Eliminar este concejal?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            <!-- Modal: Editar Concejal - Responsive -->
            <div class="modal fade" id="editModal{{ $concejal->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.concejales.update', $concejal) }}">
                            @csrf @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-pencil me-2"></i>
                                    <span class="d-none d-sm-inline">Editar Concejal</span>
                                    <span class="d-sm-none">Editar</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nombre</label>
                                        <input name="name" type="text" class="form-control" 
                                               value="{{ $concejal->name }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input name="email" type="email" class="form-control" 
                                               value="{{ $concejal->email }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nueva Contraseña</label>
                                        <input name="password" type="password" class="form-control" 
                                               placeholder="Dejar vacío para mantener la actual">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                        <input name="password_confirmation" type="password" class="form-control">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Partido Político</label>
                                        <input name="partido_politico" type="text" class="form-control" 
                                               value="{{ $concejal->concejal->partido_politico ?? '' }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Número de Lista</label>
                                        <input name="numero_lista" type="number" class="form-control" 
                                               value="{{ $concejal->concejal->numero_lista ?? '' }}" min="1">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer p-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <span class="d-none d-sm-inline">Cancelar</span>
                                    <span class="d-sm-none">❌</span>
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="d-none d-sm-inline">Guardar Cambios</span>
                                    <span class="d-sm-none">✓</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No hay concejales registrados</p>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<!-- Paginación Responsive -->
@if($concejales->hasPages())
    <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
        <small class="text-muted mb-2 mb-md-0">
            <span class="d-none d-sm-inline">Mostrando </span>
            {{ $concejales->firstItem() }}-{{ $concejales->lastItem() }} 
            <span class="d-none d-sm-inline">de </span>
            <span class="d-sm-none">/</span>
            {{ $concejales->total() }}
            <span class="d-none d-sm-inline"> concejales</span>
        </small>
        {{ $concejales->links() }}
    </div>
@endif

{{-- Modal de creación - Responsive --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.concejales.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Nuevo Concejal</span>
                        <span class="d-sm-none">Nuevo</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input name="name" type="text" class="form-control" 
                                   value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input name="email" type="email" class="form-control" 
                                   value="{{ old('email') }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <input name="password" type="password" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Confirmar Contraseña</label>
                            <input name="password_confirmation" type="password" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Partido Político</label>
                            <input name="partido_politico" type="text" class="form-control" 
                                   value="{{ old('partido_politico') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Número de Lista</label>
                            <input name="numero_lista" type="number" class="form-control" 
                                   value="{{ old('numero_lista') }}" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <span class="d-none d-sm-inline">Cancelar</span>
                        <span class="d-sm-none">❌</span>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="d-none d-sm-inline">Crear Concejal</span>
                        <span class="d-sm-none">✓</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
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
    
    // Abrir modal si hay errores de validación
    @if($errors->any() && old('_token'))
        const createModal = new bootstrap.Modal(document.getElementById('createModal'));
        createModal.show();
    @endif
    
    // Manejo responsive de tablas
    function handleTableResponsive() {
        const tables = document.querySelectorAll('.table-responsive');
        tables.forEach(table => {
            if (window.innerWidth < 768) {
                // Lógica para móvil
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
@endsection