@extends('layouts.admin')

@section('tituloPage', 'Gestión de Usuarios')

@section('contenido')

{{-- Mensajes --}}
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

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
   <link rel="stylesheet" href="{{ asset('dist/css/adminEstilos/admin.css') }}">
<!-- Botón Nuevo Usuario -->
<div class="mb-3 text-end">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Usuario
    </button>
</div>

<!-- Filtros -->
<div class="p-4 border bg-light rounded mb-4">
    <form method="GET" action="{{ route('admin') }}">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Buscar usuarios..." 
                           name="search" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="role">
                    <option value="">👥 Todos los roles</option>
                    @foreach(\Spatie\Permission\Models\Role::all() as $rol)
                        <option value="{{ $rol->name }}" {{ request('role') == $rol->name ? 'selected' : '' }}>
                            {{ $rol->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Tabla de usuarios -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th width="50"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha de Registro</th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td><input type="checkbox" class="form-check-input user-checkbox"></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3"><i class="bi bi-person"></i></div>
                        <div>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <small class="text-muted">ID: {{ $user->id }}</small>
                        </div>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                @php
                    $rol = $user->getRoleNames()->first(); // p. ej., "super-admin"
                @endphp

                @if($rol == 'super-admin')
                    <span class="badge bg-success">🛡️ Super Admin</span>
                @elseif($rol == 'aspirante-alcaldia')
                    <span class="badge bg-primary">👨‍💼 Candidato Alcalde</span>
                @elseif($rol == 'aspirante-concejo')
                    <span class="badge bg-info">👤 Candidato Concejal</span>
                @elseif($rol == 'lider')
                    <span class="badge bg-warning text-dark">👤 Líder Comunitario</span>
                @endif

                </td>
                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" title="Editar" 
                            data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" 
                          style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

            <!-- Modal: Editar Usuario -->
            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Usuario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nombre</label>
                                        <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password" placeholder="Dejar vacío para mantener">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                        <input type="password" class="form-control" name="password_confirmation">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Rol</label>
                                        <select class="form-select" name="role" required>
                                            <option value="">Seleccionar rol</option>
                                            @foreach(\Spatie\Permission\Models\Role::all() as $rol)
                                                <option value="{{ $rol->name }}" {{ $user->hasRole($rol->name) ? 'selected' : '' }}>
                                                    {{ $rol->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer p-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No hay usuarios registrados</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginación -->
<div class="p-3 d-flex justify-content-between align-items-center">
    <small class="text-muted">
        Mostrando {{ $users->firstItem() }}-{{ $users->lastItem() }} de {{ $users->total() }} usuarios
    </small>
    {{ $users->appends(request()->query())->links() }}
</div>

<!-- Modal: Agregar Usuario -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Agregar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar Contraseña</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol</label>
                            <select class="form-select" name="role" required>
                                <option value="">Seleccionar rol</option>
                                @foreach(\Spatie\Permission\Models\Role::all() as $rol)
                                    <option value="{{ $rol->name }}" {{ old('role') == $rol->name ? 'selected' : '' }}>
                                        {{ $rol->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
