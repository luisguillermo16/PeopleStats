@extends('layouts.admin')

@section('tituloPage', 'Puntos de Votación')

@section('contenido')
<div class="container-fluid mt-4">
    <!-- Header con título y botón principal -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h4 class="mb-2 mb-md-0">
            
        </h4>
        <div class="d-grid d-sm-block">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearPuntoModal">
                <i class="bi bi-plus-circle me-2"></i>
                <span class="d-none d-sm-inline">Nuevo Punto de Votación</span>
                <span class="d-sm-none">Nuevo Punto</span>
            </button>
        </div>
    </div>

    <!-- Filtros Responsive -->
    <div class="p-3 p-md-4 border bg-light rounded mb-4">
        <form method="GET">
            <div class="row align-items-end g-2 g-md-3">
                <!-- Búsqueda principal -->
                <div class="col-12 col-md-6 col-lg-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" 
                               placeholder="Buscar puntos de votación..." 
                               name="search" value="{{ request('search') }}">
                    </div>
                </div>
                
                <!-- Filtro por creador -->
                <div class="col-6 col-md-3 col-lg-2">
                    <select name="creador" class="form-select">
                        <option value="">Todos</option>
                        <option value="aspirante-alcaldia" {{ request('creador') == 'aspirante-alcaldia' ? 'selected' : '' }}>Alcaldía</option>
                        <option value="aspirante-concejo" {{ request('creador') == 'aspirante-concejo' ? 'selected' : '' }}>Concejo</option>
                        <option value="lider" {{ request('creador') == 'lider' ? 'selected' : '' }}>Líderes</option>
                    </select>
                </div>
                
                <!-- Botón de búsqueda -->
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="d-grid">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i>
                            <span class="d-none d-lg-inline ms-1">Buscar</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Vista Desktop/Tablet - Tabla -->
    <div class="d-none d-md-block">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table rounded border shadow-sm overflow-hidden">
                        <thead class="table-light">
                            <tr>
                                <th>Punto de Votación</th>
                                <th class="d-none d-lg-table-cell">Dirección</th>
                                <th>Mesas</th>
                                <th class="d-none d-lg-table-cell">Creado por</th>
                                <th class="d-none d-lg-table-cell">Fecha</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lugares as $lugar)
                                <tr>
                                    <!-- Información principal -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $lugar->nombre }}</div>
                                                <div class="d-lg-none">
                                                    <small class="text-muted">
                                                        <i class="bi bi-geo me-1"></i>{{ Str::limit($lugar->direccion, 30) }}
                                                    </small>
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
                                    
                                    <!-- Mesas -->
                                    <td>
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
                                    <td class="d-none d-lg-table-cell">
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
                                    <td colspan="6" class="text-center py-5">
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
    </div>

    <!-- Vista Móvil - Cards -->
    <div class="d-md-none">
        @forelse($lugares as $lugar)
            <div class="card mb-3 shadow-sm">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-9">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-geo-alt-fill text-primary fs-4 me-2 mt-1"></i>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="card-title mb-1 text-truncate fw-semibold">{{ $lugar->nombre }}</h6>
                                    
                                    @if($lugar->direccion)
                                        <p class="card-text small text-muted mb-2 text-truncate">
                                            <i class="bi bi-geo me-1"></i>{{ $lugar->direccion }}
                                        </p>
                                    @endif
                                    
                                    <!-- Mesas -->
                                    <div class="mb-2">
                                        @forelse($lugar->mesas as $mesa)
                                            <span class="badge bg-primary me-1 mb-1">{{ $mesa->numero }}</span>
                                        @empty
                                            <span class="badge bg-secondary">Sin mesas</span>
                                        @endforelse
                                    </div>
                                    
                                    <!-- Info adicional -->
                                    <div class="d-flex align-items-center gap-3 mt-2">
                                        @if($lugar->alcalde_id || $lugar->concejal_id)
                                            <small class="text-muted">
                                                @if($lugar->alcalde_id)
                                                    <i class="bi bi-person-badge me-1"></i>Alcaldía
                                                @else
                                                    <i class="bi bi-person me-1"></i>Concejo
                                                @endif
                                            </small>
                                        @endif
                                        
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>{{ $lugar->created_at?->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-3 text-end">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle w-100" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item btn-editar" 
                                                data-id="{{ $lugar->id }}"
                                                data-nombre="{{ $lugar->nombre }}"
                                                data-direccion="{{ $lugar->direccion }}"
                                                data-mesas='@json($lugar->mesas)'>
                                            <i class="bi bi-pencil me-2 text-warning"></i>Editar
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('destroyPuntosVotacion', $lugar) }}" method="POST" 
                                              class="form-eliminar">
                                            @csrf 
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="bi bi-trash me-2"></i>Eliminar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-geo-alt" style="font-size: 4rem;"></i>
                <p class="mt-2 mb-0 fw-semibold">No hay puntos de votación registrados</p>
                <small>Crea el primer punto de votación haciendo clic en "Nuevo Punto"</small>
            </div>
        @endforelse
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

<!-- Modal Editar Punto de Votación -->
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
    
    // Agregar mesa en modal de EDICIÓN
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
    
    // Manejar botón editar
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
});
</script>
@endpush