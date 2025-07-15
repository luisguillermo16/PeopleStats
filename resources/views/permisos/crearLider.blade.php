@extends('layouts.admin')

@section('tituloPage', 'Gestión de Líderes')

@section('contenido')

{{-- Alertas --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Errores de validación --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Barra de herramientas --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="flex-grow-1">
        <form method="GET" class="input-group" style="max-width: 500px;">
            <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por nombre, email o zona">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="bi bi-search"></i> Buscar
            </button>
        </form>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-2"></i>Nuevo Líder
        </button>
    </div>
</div>

{{-- Tabla --}}
<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th width="50"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                <th>Líder</th>
                <th>Email</th>
                <th>Zona de Influencia</th>
                <th>Creado por</th>
                <th>Fecha de Registro</th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($lideres as $lider)
            <tr>
                <td><input type="checkbox" class="form-check-input user-checkbox"></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3"><i class="bi bi-person-badge fs-4"></i></div>
                        <div>
                            <div class="fw-semibold">{{ $lider->name }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $lider->email }}</td>
                <td>{{ $lider->lider->zona_influencia ?? 'Sin especificar' }}</td>
                <td>
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
                <td>{{ $lider->created_at->format('d/m/Y') }}</td>
                <td>
                    {{-- Botón editar --}}
                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $lider->id }}">
                        <i class="bi bi-pencil"></i>
                    </button>

                    {{-- Eliminar --}}
                    <form method="POST" action="{{ route('admin.lideres.destroy', $lider) }}" style="display:inline" onsubmit="return confirm('¿Eliminar este líder?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

            {{-- Modal de edición --}}
            <div class="modal fade" id="editModal{{ $lider->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $lider->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-pencil me-2"></i>Editar Líder
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.lideres.update', $lider) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control" name="name" value="{{ $lider->name }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ $lider->email }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" name="telefono" value="{{ $lider->lider->telefono ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Actualizar Líder</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <tr><td colspan="9" class="text-center">No hay líderes registrados.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($lideres->hasPages())
    <div class="d-flex justify-content-center">
        {{ $lideres->links() }}
    </div>
@endif

{{-- Modal para crear nuevo líder --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Agregar Nuevo Líder
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.lideres.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Zona de Influencia</label>
                            <select class="form-select" name="zona_influencia" required>
                                <option value="">Seleccionar zona...</option>
                                <option value="Norte">Norte</option>
                                <option value="Sur">Sur</option>
                                <option value="Este">Este</option>
                                <option value="Oeste">Oeste</option>
                                <option value="Centro">Centro</option>
                                <option value="Metropolitana">Metropolitana</option>
                                <option value="Rural">Rural</option>
                                <option value="Urbana">Urbana</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Afiliación Política</label>
                            <select class="form-select" name="afiliacion_politica">
                                <option value="">Sin afiliación</option>
                                <option value="Conservador">Conservador</option>
                                <option value="Liberal">Liberal</option>
                                <option value="Centro Democrático">Centro Democrático</option>
                                <option value="Cambio Radical">Cambio Radical</option>
                                <option value="Polo Democrático">Polo Democrático</option>
                                <option value="Alianza Verde">Alianza Verde</option>
                                <option value="FARC">FARC</option>
                                <option value="Independiente">Independiente</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" placeholder="Ej: +57 300 123 4567">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Líder</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Seleccionar/deseleccionar todos los checkboxes
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Limpiar formulario al cerrar el modal
    document.getElementById('createModal').addEventListener('hidden.bs.modal', function () {
        const form = this.querySelector('form');
        form.reset();
    });
</script>
@endsection
