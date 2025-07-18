@extends('layouts.admin')
@section('tituloPage', 'Gestión de Líderes')
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

{{-- Botón principal - Responsive --}}
<div class="mb-3 text-end">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-2"></i>
        <span class="d-none d-sm-inline">Nuevo Líder</span>
        <span class="d-sm-none">Nuevo</span>
    </button>
</div>

{{-- Filtros Responsive --}}
<div class="p-4 border bg-light rounded mb-4">
    <form method="GET">
        <div class="row align-items-center g-3">
            <!-- Búsqueda principal -->
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" 
                           placeholder="Buscar por nombre, email o zona..." 
                           name="search" value="{{ request('search') }}">
                </div>
            </div>
            
            <!-- Botón de búsqueda -->
            <div class="col-12 col-md-4">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search"></i>
                    <span class="d-none d-lg-inline ms-1">Buscar</span>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Tabla Responsive --}}
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <!-- Checkbox solo en desktop -->
                <th width="50" class="d-none d-md-table-cell">
                    <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
                <th>Líder</th>
                <th class="d-none d-md-table-cell">Email</th>
                <th class="d-none d-lg-table-cell">Zona de Influencia</th>
                <th class="d-none d-lg-table-cell">Creado por</th>
                <th class="d-none d-sm-table-cell">Fecha</th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($lideres as $lider)
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
                            <div class="fw-semibold">{{ $lider->name }}</div>
                            <!-- Info adicional en móvil -->
                            <div class="d-md-none">
                                <small class="text-muted">{{ $lider->email }}</small>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $lider->lider->zona_influencia ?? 'Sin especificar' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- Campos ocultos en móvil -->
                <td class="d-none d-md-table-cell">{{ $lider->email }}</td>
                <td class="d-none d-lg-table-cell">{{ $lider->lider->zona_influencia ?? 'Sin especificar' }}</td>
                <td class="d-none d-lg-table-cell">
                    @if($lider->creadoPorConcejal)
                        <span class="badge bg-info">{{ $lider->creadoPorConcejal->name }}</span>
                        <small class="text-muted d-block">Concejal</small>
                    @elseif($lider->creadoPorAlcalde)
                        <span class="badge bg-success">{{ $lider->creadoPorAlcalde->name }}</span>
                        <small class="text-muted d-block">Alcalde</small>
                    @else
                        <span class="text-muted">Sin información</span>
                    @endif
                </td>
                <td class="d-none d-sm-table-cell">{{ $lider->created_at->format('d/m/Y') }}</td>
                
                <!-- Acciones -->
                <td>
                    <div class="btn-group" role="group">
                        <!-- Botón editar -->
                        <button class="btn btn-sm btn-outline-primary" 
                                title="Editar"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $lider->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        
                        <!-- Botón eliminar -->
                        <form method="POST" action="{{ route('admin.lideres.destroy', $lider) }}" 
                              style="display:inline" 
                              onsubmit="return confirm('¿Eliminar este líder?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            {{-- Modal de edición responsive --}}
            <div class="modal fade" id="editModal{{ $lider->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.lideres.update', $lider) }}">
                            @csrf @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-pencil me-2"></i>
                                    <span class="d-none d-sm-inline">Editar Líder</span>
                                    <span class="d-sm-none">Editar</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nombre</label>
                                        <input name="name" type="text" class="form-control" 
                                               value="{{ $lider->name }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input name="email" type="email" class="form-control" 
                                               value="{{ $lider->email }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Teléfono</label>
                                        <input name="telefono" type="tel" class="form-control" 
                                               value="{{ $lider->lider->telefono ?? '' }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Zona de Influencia</label>
                                        <select name="zona_influencia" class="form-select">
                                            <option value="">Seleccionar zona...</option>
                                            <option value="Norte" {{ ($lider->lider->zona_influencia ?? '') == 'Norte' ? 'selected' : '' }}>Norte</option>
                                            <option value="Sur" {{ ($lider->lider->zona_influencia ?? '') == 'Sur' ? 'selected' : '' }}>Sur</option>
                                            <option value="Este" {{ ($lider->lider->zona_influencia ?? '') == 'Este' ? 'selected' : '' }}>Este</option>
                                            <option value="Oeste" {{ ($lider->lider->zona_influencia ?? '') == 'Oeste' ? 'selected' : '' }}>Oeste</option>
                                            <option value="Centro" {{ ($lider->lider->zona_influencia ?? '') == 'Centro' ? 'selected' : '' }}>Centro</option>
                                            <option value="Metropolitana" {{ ($lider->lider->zona_influencia ?? '') == 'Metropolitana' ? 'selected' : '' }}>Metropolitana</option>
                                            <option value="Rural" {{ ($lider->lider->zona_influencia ?? '') == 'Rural' ? 'selected' : '' }}>Rural</option>
                                            <option value="Urbana" {{ ($lider->lider->zona_influencia ?? '') == 'Urbana' ? 'selected' : '' }}>Urbana</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer p-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <span class="d-none d-sm-inline">Cancelar</span>
                                    <span class="d-sm-none">❌</span>
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="d-none d-sm-inline">Actualizar Líder</span>
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
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No hay líderes registrados</p>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<x-paginacion :collection="$lideres" />

{{-- Modal para crear nuevo líder - Responsive --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.lideres.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Nuevo Líder</span>
                        <span class="d-sm-none">Nuevo</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Campos del formulario -->
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
                            <label class="form-label fw-semibold">Zona de Influencia</label>
                            <select name="zona_influencia" class="form-select" required>
                                <option value="">Seleccionar zona...</option>
                                <option value="Norte" {{ old('zona_influencia') == 'Norte' ? 'selected' : '' }}>Norte</option>
                                <option value="Sur" {{ old('zona_influencia') == 'Sur' ? 'selected' : '' }}>Sur</option>
                                <option value="Este" {{ old('zona_influencia') == 'Este' ? 'selected' : '' }}>Este</option>
                                <option value="Oeste" {{ old('zona_influencia') == 'Oeste' ? 'selected' : '' }}>Oeste</option>
                                <option value="Centro" {{ old('zona_influencia') == 'Centro' ? 'selected' : '' }}>Centro</option>
                                <option value="Metropolitana" {{ old('zona_influencia') == 'Metropolitana' ? 'selected' : '' }}>Metropolitana</option>
                                <option value="Rural" {{ old('zona_influencia') == 'Rural' ? 'selected' : '' }}>Rural</option>
                                <option value="Urbana" {{ old('zona_influencia') == 'Urbana' ? 'selected' : '' }}>Urbana</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Afiliación Política</label>
                            <select name="afiliacion_politica" class="form-select">
                                <option value="">Sin afiliación</option>
                                <option value="Conservador" {{ old('afiliacion_politica') == 'Conservador' ? 'selected' : '' }}>Conservador</option>
                                <option value="Liberal" {{ old('afiliacion_politica') == 'Liberal' ? 'selected' : '' }}>Liberal</option>
                                <option value="Centro Democrático" {{ old('afiliacion_politica') == 'Centro Democrático' ? 'selected' : '' }}>Centro Democrático</option>
                                <option value="Cambio Radical" {{ old('afiliacion_politica') == 'Cambio Radical' ? 'selected' : '' }}>Cambio Radical</option>
                                <option value="Polo Democrático" {{ old('afiliacion_politica') == 'Polo Democrático' ? 'selected' : '' }}>Polo Democrático</option>
                                <option value="Alianza Verde" {{ old('afiliacion_politica') == 'Alianza Verde' ? 'selected' : '' }}>Alianza Verde</option>
                                <option value="FARC" {{ old('afiliacion_politica') == 'FARC' ? 'selected' : '' }}>FARC</option>
                                <option value="Independiente" {{ old('afiliacion_politica') == 'Independiente' ? 'selected' : '' }}>Independiente</option>
                                <option value="Otro" {{ old('afiliacion_politica') == 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input name="telefono" type="tel" class="form-control" 
                                   value="{{ old('telefono') }}" 
                                   placeholder="Ej: +57 300 123 4567">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <span class="d-none d-sm-inline">Cancelar</span>
                        <span class="d-sm-none">❌</span>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="d-none d-sm-inline">Crear Líder</span>
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
                table.classList.add('table-mobile');
            } else {
                table.classList.remove('table-mobile');
            }
        });
    }
    
    // Ejecutar al cargar y redimensionar
    handleTableResponsive();
    window.addEventListener('resize', handleTableResponsive);
    
    // Limpiar formulario al cerrar modal
    document.getElementById('createModal').addEventListener('hidden.bs.modal', function () {
        const form = this.querySelector('form');
        form.reset();
    });
});
</script>
@endsection