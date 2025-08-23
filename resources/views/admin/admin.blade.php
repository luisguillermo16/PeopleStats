@extends('layouts.admin')

@section('tituloPage', 'Gestión de Usuarios')

@section('contenido')

    {{-- Sistema de Alertas Estándar --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
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

    <link rel="stylesheet" href="{{ asset('dist/css/adminEstilos/admin.css') }}">

    {{-- Botón Nuevo Usuario - Responsive --}}
    <div class="mb-3 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-circle me-2"></i>
            <span class="d-none d-sm-inline">Nuevo Usuario</span>
            <span class="d-sm-none">Nuevo</span>
        </button>
    </div>
            
    {{-- Sistema de Filtros Responsive --}}
    <div class="p-4 border bg-light rounded mb-4">
        <form method="GET" action="{{ route('admin') }}">
            <div class="row align-items-center g-3">
                <!-- Búsqueda principal -->
                <div class="col-12 col-md-6">
                    <div class="input-group">

                        <input type="text" class="form-control border-start-0" placeholder="Buscar usuarios..."
                            name="search" value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Filtro de rol -->
                <div class="col-6 col-md-2">
                    <select class="form-select" name="role">
                        <option value="">👥 Todos los roles</option>
                        @foreach (\Spatie\Permission\Models\Role::all() as $rol)
                            <option value="{{ $rol->name }}" {{ request('role') == $rol->name ? 'selected' : '' }}>
                                {{ $rol->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Filtro por Alcalde -->
                <div class="col-12 col-md-3">
                    <select class="form-select" name="alcalde_id">
                        <option value="">🏛️ Por campaña</option>
                        @foreach ($alcaldes as $alcalde)
                            <option value="{{ $alcalde->id }}" {{ request('alcalde_id') == $alcalde->id ? 'selected' : '' }}>
                                {{ $alcalde->name }}
                            </option>
                        @endforeach
                    </select>
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
           
    {{-- Tabla Responsive Estándar --}}
    <div class="table-responsive">
         @if(request('alcalde_id'))
               <div class="d-flex gap-2 mb-2">
                    <div class="card text-white bg-primary mb-0" style="min-width: 70px;">
                        <div class="card-body p-1 text-center">
                            <small class="d-block mb-0" style="font-size: 0.7rem;">Líderes</small>
                            <strong style="font-size: 1rem;">{{ $totalLideres ?? 0 }}</strong>
                        </div>
                    </div>
                    <div class="card text-white bg-info mb-0" style="min-width: 70px;">
                        <div class="card-body p-1 text-center">
                            <small class="d-block mb-0" style="font-size: 0.7rem;">Concejales</small>
                            <strong style="font-size: 1rem;">{{ $totalConcejales ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            @endif
        <table class="table">
            <thead>
                <tr>
                    <!-- Checkbox solo en desktop -->
                    <th width="50" class="d-none d-md-table-cell">
                       
                    </th>
                    <th>Usuario</th>
                    <th class="d-none d-md-table-cell">Email</th>
                    <th class="d-none d-lg-table-cell">Rol</th>
                    <th class="d-none d-lg-table-cell">Creado Por</th>
                    <th class="d-none d-sm-table-cell">Fecha Registro</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <!-- Checkbox -->
                        <td class="d-none d-md-table-cell">
                           
                        </td>

                        <!-- Información principal -->
                        <td>
                            <div class="d-flex align-items-center">
                              
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <!-- Info adicional en móvil -->
                                    <div class="d-md-none">
                                        <small class="text-muted">{{ $user->email }}</small>
                                        <div class="mt-1">
                                            @php
                                                $rol = $user->getRoleNames()->first();
                                            @endphp
                                            @if ($rol == 'super-admin')
                                                <span class="badge bg-success">🛡️ Super Admin</span>
                                            @elseif($rol == 'aspirante-alcaldia')
                                                <span class="badge bg-primary">👨‍💼 Candidato Alcalde</span>
                                            @elseif($rol == 'aspirante-concejo')
                                                <span class="badge bg-info">👤 Candidato Concejal</span>
                                            @elseif($rol == 'lider')
                                                <span class="badge bg-warning text-dark">👤 Líder Comunitario</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Campos ocultos en móvil -->
                        <td class="d-none d-md-table-cell">{{ $user->email }}</td>
                        <td class="d-none d-lg-table-cell">
                            @php
                                $rol = $user->getRoleNames()->first();
                            @endphp
                            @if ($rol == 'super-admin')
                                <span class="badge bg-success">🛡️ Super Admin</span>
                            @elseif($rol == 'aspirante-alcaldia')
                                <span class="badge bg-primary">👨‍💼 Candidato Alcalde</span>
                            @elseif($rol == 'aspirante-concejo')
                                <span class="badge bg-info">👤 Candidato Concejal</span>
                            @elseif($rol == 'lider')
                                <span class="badge bg-warning text-dark">👤 Líder Comunitario</span>
                            @endif
                        </td>
                        <td class="d-none d-lg-table-cell">
                             @php
                                $rol = $user->getRoleNames()->first();
                            @endphp

                            {{-- Mostrar si es líder o concejal --}}
                            @if (in_array($rol, ['lider', 'aspirante-concejo']))
                                @if ($user->alcalde)
                                    <span class="fw-semibold">{{ $user->alcalde->name }}</span>
                                @elseif ($user->concejal)
                                    <span class="fw-semibold">{{ $user->concejal->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="d-none d-sm-table-cell">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}
                        </td>

                        <!-- Acciones -->
                        <td>
                            <div class="btn-group" role="group">
                                <!-- Botón editar -->
                                <button class="btn btn-sm btn-outline-primary" title="Editar" data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Botón eliminar -->
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                    style="display:inline"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Editar Usuario - Responsive --}}
                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="bi bi-pencil me-2"></i>
                                            <span class="d-none d-sm-inline">Editar Usuario</span>
                                            <span class="d-sm-none">Editar</span>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold">Nombre</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $user->name }}" required>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold">Email</label>
                                                <input type="email" class="form-control" name="email"
                                                    value="{{ $user->email }}" required>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold">Nueva Contraseña</label>
                                                <input type="password" class="form-control" name="password"
                                                    placeholder="Dejar vacío para mantener">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                                <input type="password" class="form-control" name="password_confirmation">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label fw-semibold">Rol</label>
                                                <select class="form-select" name="role" required>
                                                    <option value="">Seleccionar rol</option>
                                                    @foreach (\Spatie\Permission\Models\Role::all() as $rol)
                                                        <option value="{{ $rol->name }}"
                                                            {{ $user->hasRole($rol->name) ? 'selected' : '' }}>
                                                            {{ $rol->name }}
                                                        </option>
                                                    @endforeach
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
                                            <span class="d-none d-sm-inline">Actualizar Usuario</span>
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
                            <p class="text-muted mt-2">No hay usuarios registrados</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación Responsive --}}
    <x-paginacion :collection="$users" />


    {{-- Modal Agregar Usuario - Responsive --}}
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-person-plus me-2"></i>
                            <span class="d-none d-sm-inline">Agregar Nuevo Usuario</span>
                            <span class="d-sm-none">Nuevo</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                    required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                    required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Rol</label>
                                <select class="form-select" name="role" required>
                                    <option value="">Seleccionar rol</option>
                                    @foreach (\Spatie\Permission\Models\Role::all() as $rol)
                                        <option value="{{ $rol->name }}"
                                            {{ old('role') == $rol->name ? 'selected' : '' }}>
                                            {{ $rol->name }}
                                        </option>
                                    @endforeach
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
                            <span class="d-none d-sm-inline">Crear Usuario</span>
                            <span class="d-sm-none">✓</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JavaScript Responsive --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los checkboxes
            const selectAllCheckbox = document.getElementById('selectAll');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    userCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Abrir modal si hay errores de validación
            @if ($errors->any() && old('_token'))
                const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
                addUserModal.show();
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
        });
    </script>

@endsection
