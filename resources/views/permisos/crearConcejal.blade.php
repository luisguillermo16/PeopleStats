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
<div class="mb-3 text-end">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-2"></i> Nuevo Concejal
    </button>
</div>

{{-- Filtros --}}
<div class="p-4 border bg-light rounded mb-4">
    <form method="GET">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" name="search"
                           placeholder="Buscar por nombre, email o partido" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <input name="numero_lista" type="number" min="1" value="{{ request('numero_lista') }}"
                       class="form-control" placeholder="N° Lista">
            </div>
            <div class="col-6 col-md-2">
                <button class="btn btn-outline-primary w-100" type="submit">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="table-responsive">
    <table class="table rounded border shadow-sm overflow-hidden">
        <thead>
            <tr>
                <th></th>
                <th>Concejal</th>
                <th class="d-none d-md-table-cell">Email</th>
                <th class="d-none d-lg-table-cell">Partido</th>
                <th class="d-none d-lg-table-cell">N° Lista</th>
                <th class="d-none d-sm-table-cell">Votantes</th>
                <th class="d-none d-sm-table-cell">Fecha</th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($concejales as $concejal)
                <tr>
                    <td class="d-none d-md-table-cell"></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person fs-4 me-2"></i>
                            <div>
                                <div class="fw-semibold">{{ $concejal->name }}</div>
                                <div class="d-md-none">
                                    <small class="text-muted">{{ $concejal->email }}</small><br>
                                    <small class="text-muted">
                                        <i class="bi bi-people me-1"></i>{{ $concejal->votantes_count ?? 0 }} votantes
                                    </small>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell">{{ $concejal->email }}</td>
                    <td class="d-none d-lg-table-cell">{{ $concejal->concejal->partido_politico ?? 'Sin partido' }}</td>
                    <td class="d-none d-lg-table-cell">{{ $concejal->concejal->numero_lista ?? 'N/A' }}</td>
                    <td class="d-none d-sm-table-cell"><span class="badge bg-primary">{{ $concejal->votantes_count ?? 0 }}</span></td>
                    <td class="d-none d-sm-table-cell">{{ $concejal->created_at->format('d/m/Y') }}</td>
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

                {{-- Modal Editar --}}
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
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2"></i><br>
                        No hay concejales registrados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<x-paginacion :collection="$concejales" />

{{-- Modal Crear --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
