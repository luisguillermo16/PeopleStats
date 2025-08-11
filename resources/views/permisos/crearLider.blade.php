@extends('layouts.admin')
@section('tituloPage', 'Gestión de Líderes')
@section('contenido')



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
    <table class="table rounded border shadow-sm overflow-hidden">
        <thead>
            <tr>
                <!-- Checkbox solo en desktop -->
                <th width="50" class="d-none d-md-table-cell">
                    
                </th>
                <th>Líder</th>
                <th class="d-none d-md-table-cell">Email</th>
                <th class="d-none d-lg-table-cell">Zona de Influencia</th>
                <th class="d-none d-lg-table-cell">Creado por</th>
                 <th class="d-none d-lg-table-cell">Votantes</th>
                <th class="d-none d-sm-table-cell">Fecha</th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($lideres as $lider)
            <tr>
                <!-- Checkbox -->
                <td class="d-none d-md-table-cell">
                    
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
                   <td class="d-none d-sm-table-cell">
                    <span class="badge bg-primary">
                        {{ $lider->votantesRegistrados->count() }}
                    </span>
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
                            class="form-eliminar" style="display:inline">
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Confirmación SweetAlert al eliminar
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Mensajes flash
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    @endif

    // Errores de validación
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#d33'
        });
    @endif

    // Abrir modal crear si error es de creación
    @if($errors->any() && old('_token') && !$errors->has('email'))
        const createModal = new bootstrap.Modal(document.getElementById('createModal'));
        createModal.show();
    @endif

    // Abrir modal editar si error pertenece a edición
    @if($errors->has('email') && session('edit_id'))
        const editModal = new bootstrap.Modal(document.getElementById('editModal{{ session("edit_id") }}'));
        editModal.show();
    @endif
});
</script>
@endpush
