@extends('layouts.admin')

@section('tituloPage', 'Gestión de Concejales')

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
            <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por nombre, email o partido">
            <input name="numero_lista" type="number" min="1" value="{{ request('numero_lista') }}" class="form-control" placeholder="Número de lista">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="bi bi-search"></i> Buscar
            </button>
        </form>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-2"></i>Nuevo Concejal
        </button>
    </div>
</div>

{{-- Tabla --}}
<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th width="50"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                <th>Concejal</th>
                <th>Email</th>
                <th>Partido</th>
                <th>N° Lista</th>
                <th>Fecha de Registro</th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($concejales as $concejal)
            <tr>
                <td><input type="checkbox" class="form-check-input user-checkbox"></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3"><i class="bi bi-person fs-4"></i></div>
                        <div>
                            <div class="fw-semibold">{{ $concejal->name }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $concejal->email }}</td>
                <td>{{ $concejal->concejal->partido_politico ?? 'Sin partido' }}</td>
                <td>{{ $concejal->concejal->numero_lista ?? 'N/A' }}</td>
                <td>{{ $concejal->created_at->format('d/m/Y') }}</td>
                <td>
                    {{-- Botón editar --}}
                    <button class="btn btn-sm btn-outline-primary me-1"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $concejal->id }}">
                        <i class="bi bi-pencil"></i>
                    </button>

                    {{-- Eliminar --}}
                    <form method="POST"
                          action="{{ route('admin.concejales.destroy', $concejal) }}"
                          style="display:inline"
                          onsubmit="return confirm('¿Eliminar este concejal?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

            {{-- Modal de edición --}}
            <div class="modal fade" id="editModal{{ $concejal->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-pencil me-2"></i>
                                Editar Concejal
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.concejales.update', $concejal) }}">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre</label>
                                        <input name="name" type="text" class="form-control" value="{{ $concejal->name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input name="email" type="email" class="form-control" value="{{ $concejal->email }}" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input name="password" type="password" class="form-control" placeholder="Dejar vacío para mantener la actual">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar Contraseña</label>
                                        <input name="password_confirmation" type="password" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Partido Político</label>
                                        <input name="partido_politico" type="text" class="form-control" value="{{ $concejal->concejal->partido_politico ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Número de Lista</label>
                                        <input name="numero_lista" type="number" class="form-control" value="{{ $concejal->concejal->numero_lista ?? '' }}" min="1">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <tr><td colspan="7" class="text-center">No hay concejales registrados.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
@if($concejales->hasPages())
    <div class="d-flex justify-content-center">
        {{ $concejales->links() }}
    </div>
@endif

{{-- Modal de creación --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>
                    Agregar Nuevo Concejal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.concejales.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input name="password" type="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input name="password_confirmation" type="password" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Partido Político</label>
                            <input name="partido_politico" type="text" class="form-control" value="{{ old('partido_politico') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Número de Lista</label>
                            <input name="numero_lista" type="number" class="form-control" value="{{ old('numero_lista') }}" min="1">
                        </div>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Concejal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad para seleccionar todos los checkboxes
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Abrir modal de creación si hay errores de validación
    @if($errors->any() && old('_token'))
        const createModal = new bootstrap.Modal(document.getElementById('createModal'));
        createModal.show();
    @endif
    
    // Contador de caracteres para el textarea de propuestas
    const propuestasTextareas = document.querySelectorAll('textarea[name="propuestas"]');
    propuestasTextareas.forEach(textarea => {
        const maxLength = 1000;
        const helpText = textarea.nextElementSibling;
        
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            helpText.textContent = `${remaining} caracteres restantes`;
            if (remaining < 100) {
                helpText.classList.add('text-warning');
            } else {
                helpText.classList.remove('text-warning');
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Llamar inicialmente
    });
});
</script>
@endsection
