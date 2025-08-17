@extends('layouts.admin')

@section('tituloPage', 'Gestión de Concejales')

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
        <i class="bi bi-plus-circle me-2"></i> Nuevo Concejal
    </button>
</div>

{{-- Filtros --}}
<div class="p-3 p-md-4 border bg-light rounded mb-4">
    <form method="GET">
        <div class="row align-items-end g-2 g-md-3">
            <div class="col-12 col-md-6 col-lg-7">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" name="search"
                           placeholder="Buscar por nombre, email o partido" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <input name="numero_lista" type="number" min="1" value="{{ request('numero_lista') }}"
                       class="form-control" placeholder="N° Lista">
            </div>
            <div class="col-6 col-md-3 col-lg-3">
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
                    <th>Concejal</th>
                    <th>Email</th>
                    <th class="d-none d-lg-table-cell">Partido</th>
                    <th class="d-none d-lg-table-cell">N° Lista</th>
                    <th>Votantes</th>
                    <th class="d-none d-lg-table-cell">Fecha</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($concejales as $concejal)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person fs-4 me-2"></i>
                                <div>
                                    <div class="fw-semibold">{{ $concejal->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $concejal->email }}</td>
                        <td class="d-none d-lg-table-cell">{{ $concejal->concejal->partido_politico ?? 'Sin partido' }}</td>
                        <td class="d-none d-lg-table-cell">{{ $concejal->concejal->numero_lista ?? 'N/A' }}</td>
                        <td><span class="badge bg-primary">{{ $concejal->votantes_count ?? 0 }}</span></td>
                        <td class="d-none d-lg-table-cell">{{ $concejal->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $concejal->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.concejales.destroy', $concejal) }}"
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
                    <div class="modal fade" id="editModal{{ $concejal->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.concejales.update', $concejal) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Concejal</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" name="name" class="form-control" value="{{ $concejal->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $concejal->email }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Partido Político</label>
                                                <input type="text" name="partido_politico" class="form-control" value="{{ $concejal->concejal->partido_politico ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Número de Lista</label>
                                                <input type="number" name="numero_lista" class="form-control" value="{{ $concejal->concejal->numero_lista ?? '' }}">
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
                            No hay concejales registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Vista Móvil - Cards --}}
<div class="d-md-none">
    @forelse ($concejales as $concejal)
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-person-circle fs-4 me-2 text-primary mt-1"></i>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="card-title mb-1 text-truncate">{{ $concejal->name }}</h6>
                                <p class="card-text small text-muted mb-1 text-truncate">
                                    <i class="bi bi-envelope-at me-1"></i>{{ $concejal->email }}
                                </p>
                                @if($concejal->concejal->partido_politico)
                                    <p class="card-text small text-muted mb-1 text-truncate">
                                        <i class="bi bi-flag me-1"></i>{{ $concejal->concejal->partido_politico }}
                                    </p>
                                @endif
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    @if($concejal->concejal->numero_lista)
                                        <small class="text-muted">
                                            <i class="bi bi-list-ol me-1"></i>Lista {{ $concejal->concejal->numero_lista }}
                                        </small>
                                    @endif
                                    <small class="badge bg-primary">
                                        <i class="bi bi-people me-1"></i>{{ $concejal->votantes_count ?? 0 }}
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
                                            data-bs-target="#editModalMobile{{ $concejal->id }}">
                                        <i class="bi bi-pencil me-2"></i>Editar
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('admin.concejales.destroy', $concejal) }}" 
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
        <div class="modal fade" id="editModalMobile{{ $concejal->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.concejales.update', $concejal) }}">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h6 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Concejal</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name" class="form-control" value="{{ $concejal->name }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $concejal->email }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Partido Político</label>
                                    <input type="text" name="partido_politico" class="form-control" value="{{ $concejal->concejal->partido_politico ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Número de Lista</label>
                                    <input type="number" name="numero_lista" class="form-control" value="{{ $concejal->concejal->numero_lista ?? '' }}">
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
            <p class="mt-2">No hay concejales registrados</p>
        </div>
    @endforelse
</div>

{{-- Paginación --}}
<x-paginacion :collection="$concejales" />

{{-- Modal Crear --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered d-none d-md-block">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.concejales.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Agregar Nuevo Concejal</h5>
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
                            <label class="form-label">Partido Político</label>
                            <input type="text" name="partido_politico" class="form-control" value="{{ old('partido_politico') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Número de Lista</label>
                            <input type="number" name="numero_lista" class="form-control" value="{{ old('numero_lista') }}" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Concejal</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Modal Crear Móvil --}}
    <div class="modal-dialog modal-dialog-centered d-md-none">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.concejales.store') }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nuevo Concejal</h6>
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
                            <label class="form-label">Partido Político</label>
                            <input type="text" name="partido_politico" class="form-control" value="{{ old('partido_politico') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Número de Lista</label>
                            <input type="number" name="numero_lista" class="form-control" value="{{ old('numero_lista') }}" min="1">
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