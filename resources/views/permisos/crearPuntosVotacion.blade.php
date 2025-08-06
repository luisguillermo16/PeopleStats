@extends('layouts.admin')

@section('tituloPage', 'Puntos de Votación')

@section('contenido')
<div class="container-fluid mt-4">
    <!-- Header con título y botón principal -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h4 class="mb-2 mb-md-0">
            
        </h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearPuntoModal">
            <i class="bi bi-plus-circle me-2"></i>
            <span class="d-none d-sm-inline">Nuevo Punto de Votación</span>
            <span class="d-sm-none">Nuevo Punto</span>
        </button>
    </div>

    <!-- Sistema de Alertas Estándar -->
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
                               placeholder="Buscar puntos de votación..." 
                               name="search" value="{{ request('search') }}">
                    </div>
                </div>
                
                <!-- Filtro por creador -->
                <div class="col-6 col-md-3">
                    <select name="creador" class="form-select">
                        <option value="">Todos los creadores</option>
                        <option value="aspirante-alcaldia" {{ request('creador') == 'aspirante-alcaldia' ? 'selected' : '' }}>Aspirantes Alcaldía</option>
                        <option value="aspirante-concejo" {{ request('creador') == 'aspirante-concejo' ? 'selected' : '' }}>Aspirantes Concejo</option>
                        <option value="lider" {{ request('creador') == 'lider' ? 'selected' : '' }}>Líderes</option>
                    </select>
                </div>
                
                <!-- Botón de búsqueda -->
                <div class="col-6 col-md-3">
                    <button class="btn btn-outline-primary w-100" type="submit">
                        <i class="bi bi-search"></i>
                        <span class="d-none d-lg-inline ms-1">Buscar</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla Responsive -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <!-- Checkbox solo en desktop -->
                            <th width="50" class="d-none d-md-table-cell">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Punto de Votación</th>
                            <th class="d-none d-lg-table-cell">Dirección</th>
                            <th class="d-none d-md-table-cell">Mesas</th>
                            <th class="d-none d-lg-table-cell">Creado por</th>
                            <th class="d-none d-sm-table-cell">Fecha</th>
                            <th width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lugares as $lugar)
                            <tr>
                                <!-- Checkbox -->
                                <td class="d-none d-md-table-cell">
                                    <input type="checkbox" class="form-check-input item-checkbox" value="{{ $lugar->id }}">
                                </td>
                                
                                <!-- Información principal -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $lugar->nombre }}</div>
                                            <!-- Info adicional en móvil -->
                                            <div class="d-lg-none">
                                                <small class="text-muted">
                                                    <i class="bi bi-geo me-1"></i>{{ Str::limit($lugar->direccion, 30) }}
                                                </small>
                                            </div>
                                            <!-- Mesas en móvil -->
                                            <div class="d-md-none mt-1">
                                                @foreach($lugar->mesas as $mesa)
                                                    <span class="badge bg-primary me-1">{{ $mesa->numero }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Dirección - oculta en móvil/tablet -->
                                <td class="d-none d-lg-table-cell">
                                    <small class="text-muted">
                                        <i class="bi bi-geo me-1"></i>{{ $lugar->direccion ?: 'No especificada' }}
                                    </small>
                                </td>
                                
                                <!-- Mesas - ocultas en móvil -->
                                <td class="d-none d-md-table-cell">
                                    @forelse($lugar->mesas as $mesa)
                                        <span class="badge bg-primary me-1">{{ $mesa->numero }}</span>
                                    @empty
                                        <span class="text-muted">Sin mesas</span>
                                    @endforelse
                                </td>
                                
                                <!-- Creado por - oculto hasta LG -->
                                <td class="d-none d-lg-table-cell">
                                    @if($lugar->alcalde_id)
                                        <span class="badge bg-success">
                                            <i class="bi bi-person-badge me-1"></i>Aspirante Alcaldía
                                        </span>
                                    @elseif($lugar->concejal_id)
                                        <span class="badge bg-info">
                                            <i class="bi bi-person me-1"></i>Aspirante Concejo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No definido</span>
                                    @endif
                                </td>
                                
                                <!-- Fecha - oculta en móvil -->
                                <td class="d-none d-sm-table-cell">
                                    <small class="text-muted">{{ $lugar->created_at?->format('d/m/Y') }}</small>
                                </td>
                                
                                <!-- Acciones -->
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Botón editar -->
                                        <button class="btn btn-sm btn-outline-warning btn-editar"
                                                title="Editar"
                                                data-id="{{ $lugar->id }}"
                                                data-nombre="{{ $lugar->nombre }}"
                                                data-direccion="{{ $lugar->direccion }}"
                                                data-mesas='@json($lugar->mesas)'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <!-- Botón eliminar -->
                                        <form action="{{ route('destroyPuntosVotacion', $lugar) }}" method="POST" 
                                              style="display:inline-block" 
                                               class="form-eliminar">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No hay puntos de votación registrados</p>
                                    <small class="text-muted">Crea el primer punto de votación haciendo clic en "Nuevo Punto"</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación Responsive -->
    @if(method_exists($lugares, 'hasPages') && $lugares->hasPages())
        <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <small class="text-muted mb-2 mb-md-0">
                <span class="d-none d-sm-inline">Mostrando </span>
                {{ $lugares->firstItem() }}-{{ $lugares->lastItem() }} 
                <span class="d-none d-sm-inline">de </span>
                <span class="d-sm-none">/</span>
                {{ $lugares->total() }}
                <span class="d-none d-sm-inline"> puntos de votación</span>
            </small>
            {{ $lugares->links() }}
        </div>
    @endif
</div>

<!-- Modal Crear Punto de Votación -->
<div class="modal fade" id="crearPuntoModal" tabindex="-1" aria-labelledby="crearPuntoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('storePuntosVotacion') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="crearPuntoModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Crear Nuevo Punto de Votación</span>
                        <span class="d-sm-none">Nuevo Punto</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Errores de validación -->
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

                    <div class="row g-3">
                        <!-- Nombre del lugar -->
                        <div class="col-12">
                            <label for="nombre" class="form-label fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i>Nombre del Lugar
                            </label>
                            <input type="text" name="nombre" id="nombre" class="form-control" 
                                   value="{{ old('nombre') }}" 
                                   placeholder="Ej: Colegio San José"
                                   required>
                        </div>

                        <!-- Dirección -->
                        <div class="col-12">
                            <label for="direccion" class="form-label fw-semibold">
                                <i class="bi bi-geo me-1"></i>Dirección
                            </label>
                            <textarea name="direccion" id="direccion" class="form-control" rows="2" 
                                      placeholder="Ej: Calle 15 #8-25, Barrio Centro" required>{{ old('direccion') }}</textarea>
                        </div>

                        <!-- Mesas -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-table me-1"></i>Mesas de Votación
                            </label>
                            <div id="mesas-container">
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-table"></i>
                                    </span>
                                    <input type="text" name="mesas[]" class="form-control" 
                                           placeholder="Número de Mesa (Ej: 001)" required>
                                    <button type="button" class="btn btn-outline-danger btn-remove-mesa">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-agregar-mesa">
                                <i class="bi bi-plus me-1"></i>Agregar otra mesa
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <span class="d-none d-sm-inline">Cancelar</span>
                        <span class="d-sm-none">❌</span>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>
                        <span class="d-none d-sm-inline">Crear Punto de Votación</span>
                        <span class="d-sm-none">Crear</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Punto de Votación CORREGIDO -->
<div class="modal fade" id="editarPuntoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="form-editar-punto">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>
                        <span class="d-none d-sm-inline">Editar Punto de Votación</span>
                        <span class="d-sm-none">Editar Punto</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Nombre del lugar -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i>Nombre del Lugar
                            </label>
                            <input type="text" name="nombre" id="editar-nombre" class="form-control" required>
                        </div>
                        
                        <!-- Dirección -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-geo me-1"></i>Dirección
                            </label>
                            <textarea name="direccion" id="editar-direccion" class="form-control" rows="2" required></textarea>
                        </div>
                        
                        <!-- Mesas -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-table me-1"></i>Mesas de Votación
                            </label>
                            <div id="editar-mesas-container">
                                <!-- Se llenarán dinámicamente con inputs name="mesas[]" -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-agregar-mesa-editar">
                                <i class="bi bi-plus me-1"></i>Agregar otra mesa
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <span class="d-none d-sm-inline">Cancelar</span>
                        <span class="d-sm-none">❌</span>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>
                        <span class="d-none d-sm-inline">Actualizar Punto</span>
                        <span class="d-sm-none">Actualizar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        const createModal = new bootstrap.Modal(document.getElementById('crearPuntoModal'));
        createModal.show();
    @endif
    
    // Agregar mesa en modal de creación
    document.getElementById('btn-agregar-mesa').addEventListener('click', function() {
        const container = document.getElementById('mesas-container');
        const newMesa = document.createElement('div');
        newMesa.className = 'input-group mb-2';
        newMesa.innerHTML = `
            <span class="input-group-text bg-light">
                <i class="bi bi-table"></i>
            </span>
            <input type="text" name="mesas[]" class="form-control" placeholder="Número de Mesa" required>
            <button type="button" class="btn btn-outline-danger btn-remove-mesa">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
        container.appendChild(newMesa);
    });
    
    // Agregar mesa en modal de EDICIÓN (CORREGIDO)
    document.getElementById('btn-agregar-mesa-editar').addEventListener('click', function() {
        const container = document.getElementById('editar-mesas-container');
        const newMesa = document.createElement('div');
        newMesa.className = 'input-group mb-2';
        newMesa.innerHTML = `
            <span class="input-group-text bg-light">
                <i class="bi bi-table"></i>
            </span>
            <input type="text" name="mesas[]" class="form-control" placeholder="Número de Mesa" required>
            <button type="button" class="btn btn-outline-danger btn-remove-mesa">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
        container.appendChild(newMesa);
    });
    
    // Remover mesa (modal creación y edición)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-mesa')) {
            const inputGroup = e.target.closest('.input-group');
            const container = inputGroup.parentElement;
            if (container.children.length > 1) {
                inputGroup.remove();
            } else {
                alert('Debe mantener al menos una mesa');
            }
        }
    });
    
    // Manejar botón editar (CORREGIDO)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-editar')) {
            const btn = e.target.closest('.btn-editar');
            const id = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const direccion = btn.dataset.direccion;
            const mesas = JSON.parse(btn.dataset.mesas || '[]');
            
            // Llenar campos del modal
            document.getElementById('editar-nombre').value = nombre;
            document.getElementById('editar-direccion').value = direccion;

            // Actualizar la acción del formulario
            const updateRoute = "{{ route('updatePuntosVotacion', '__ID__') }}".replace('__ID__', id);
            document.getElementById('form-editar-punto').action = updateRoute;
            
            // Mostrar mesas existentes como inputs editables
            const mesasContainer = document.getElementById('editar-mesas-container');
            mesasContainer.innerHTML = '';
            
            if (mesas.length > 0) {
                mesas.forEach((mesa, index) => {
                    const mesaElement = document.createElement('div');
                    mesaElement.className = 'input-group mb-2';
                    mesaElement.innerHTML = `
                        <span class="input-group-text bg-light">
                            <i class="bi bi-table"></i>
                        </span>
                        <input type="text" name="mesas[]" class="form-control" value="${mesa.numero}" required>
                        <button type="button" class="btn btn-outline-danger btn-remove-mesa">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    `;
                    mesasContainer.appendChild(mesaElement);
                });
            } else {
                // Si no hay mesas, agregar al menos una vacía
                const mesaElement = document.createElement('div');
                mesaElement.className = 'input-group mb-2';
                mesaElement.innerHTML = `
                    <span class="input-group-text bg-light">
                        <i class="bi bi-table"></i>
                    </span>
                    <input type="text" name="mesas[]" class="form-control" placeholder="Número de Mesa" required>
                    <button type="button" class="btn btn-outline-danger btn-remove-mesa">
                        <i class="bi bi-x-lg"></i>
                    </button>
                `;
                mesasContainer.appendChild(mesaElement);
            }
            
            // Mostrar modal
            const editModal = new bootstrap.Modal(document.getElementById('editarPuntoModal'));
            editModal.show();
        }
    });
    
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
    
    handleTableResponsive();
    window.addEventListener('resize', handleTableResponsive);
});
</script>

<style>
@media (max-width: 767px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .table-mobile th,
    .table-mobile td {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
}
</style>
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