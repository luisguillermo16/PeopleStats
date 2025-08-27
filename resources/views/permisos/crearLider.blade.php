@extends('layouts.admin')
@section('tituloPage', 'Gestión de Líderes')
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

{{-- Botón Crear --}}
<div class="mb-3 d-grid d-sm-block text-sm-end">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-2"></i> Nuevo Líder
    </button>
</div>

{{-- Filtros --}}
<div class="p-3 p-md-4 border bg-light rounded mb-4">
    <form method="GET">
        <div class="row align-items-end g-2 g-md-3">
            <div class="col-12 col-md-9 col-lg-10">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" name="search"
                           placeholder="Buscar por nombre" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-3 col-lg-2">
                <div class="d-grid d-md-block">
                    <button class="btn btn-outline-primary w-100" type="submit">
                        <i class="bi bi-search me-1"></i> 
                        <span class="d-none d-sm-inline">Buscar</span>
                        <span class="d-sm-none">OK</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Vista Desktop/Tablet - Tabla --}}
<div class="d-none d-md-block">
    <div class="table-responsive">
        <table class="table rounded border shadow-sm overflow-hidden">
            <thead>
                <tr>
                    <th>Líder</th>
                    <th>Email</th>
                    
                    <th class="d-none d-lg-table-cell">Creado por</th>
                    <th>Votantes</th>
                    <th class="d-none d-lg-table-cell">Fecha</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lideres as $lider)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person fs-4 me-2"></i>
                                <div>
                                    <div class="fw-semibold">{{ $lider->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $lider->email }}</td>
                    
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
                        <td><span class="badge bg-primary">{{ $lider->votantesRegistrados->count() ?? 0 }}</span></td>
                        <td class="d-none d-lg-table-cell">{{ $lider->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $lider->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
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

                    {{-- Modal Editar Desktop --}}
                    <div class="modal fade" id="editModal{{ $lider->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.lideres.update', $lider) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Líder</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" name="name" class="form-control" value="{{ $lider->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $lider->email }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Teléfono</label>
                                                <input type="tel" name="telefono" class="form-control" value="{{ $lider->lider->telefono ?? '' }}">
                                            </div>
                                        
                                            <div class="col-12">
                                                <label class="form-label">Nueva Contraseña</label>
                                                <input type="password" name="password" class="form-control" placeholder="Dejar vacío para mantener la actual">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer p-4">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2"></i><br>
                            No hay líderes registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Vista Móvil - Cards --}}
<div class="d-md-none">
    @forelse ($lideres as $lider)
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-person-circle fs-4 me-2 text-primary mt-1"></i>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="card-title mb-1 text-truncate">{{ $lider->name }}</h6>
                                <p class="card-text small text-muted mb-1 text-truncate">
                                    <i class="bi bi-envelope-at me-1"></i>{{ $lider->email }}
                                </p>
                               
                              
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    @if($lider->creadoPorConcejal)
                                        <small class="text-muted">
                                            <i class="bi bi-person-badge me-1"></i>Por: {{ $lider->creadoPorConcejal->name }}
                                        </small>
                                    @elseif($lider->creadoPorAlcalde)
                                        <small class="text-muted">
                                            <i class="bi bi-person-badge me-1"></i>Por: {{ $lider->creadoPorAlcalde->name }}
                                        </small>
                                    @endif
                                    <small class="badge bg-primary">
                                        <i class="bi bi-people me-1"></i>{{ $lider->votantesRegistrados->count() ?? 0 }}
                                    </small>
                                </div>
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
                                    <button class="dropdown-item" data-bs-toggle="modal" 
                                            data-bs-target="#editModalMobile{{ $lider->id }}">
                                        <i class="bi bi-pencil me-2"></i>Editar
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('admin.lideres.destroy', $lider) }}" 
                                          class="form-eliminar">
                                        @csrf @method('DELETE')
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

        {{-- Modal Editar Móvil --}}
        <div class="modal fade" id="editModalMobile{{ $lider->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.lideres.update', $lider) }}">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h6 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Líder</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name" class="form-control" value="{{ $lider->name }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $lider->email }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control" value="{{ $lider->lider->telefono ?? '' }}">
                                </div>
                               
                                <div class="col-12">
                                    <label class="form-label">Nueva Contraseña</label>
                                    <input type="password" name="password" class="form-control" placeholder="Dejar vacío para mantener la actual">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer p-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-2">No hay líderes registrados</p>
        </div>
    @endforelse
</div>

{{-- Paginación --}}
<x-paginacion :collection="$lideres" />

{{-- Modal Crear --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered d-none d-md-block">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.lideres.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Agregar Nuevo Líder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="Ej: +57 300 123 4567">
                        </div>
                      
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Líder</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Modal Crear Móvil --}}
    <div class="modal-dialog modal-dialog-centered d-md-none">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.lideres.store') }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nuevo Líder</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="Ej: +57 300 123 4567">
                        </div>
                      
                    </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡Esta acción no se puede deshacer!',
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
});
</script>
@endpush