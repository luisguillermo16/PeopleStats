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
            <input name="nivel_liderazgo" value="{{ request('nivel_liderazgo') }}" class="form-control" placeholder="Nivel de liderazgo">
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
                <th>Nivel</th>
                <th>Afiliación</th>
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
                <td>{{ $lider->lider->nivel_liderazgo ?? '' }}</td>
                <td>{{ $lider->lider->afiliacion_politica ?? 'Sin afiliación' }}</td>
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
            
            {{-- Modal de edición para cada líder --}}
            <div class="modal fade" id="editModal{{ $lider->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $lider->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $lider->id }}">
                                <i class="bi bi-pencil me-2"></i>Editar Líder
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <form method="POST" action="{{ route('admin.lideres.update', $lider) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    {{-- Fila 1: Nombre y Email --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_name{{ $lider->id }}" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="edit_name{{ $lider->id }}" name="name" value="{{ $lider->name }}" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_email{{ $lider->id }}" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="edit_email{{ $lider->id }}" name="email" value="{{ $lider->email }}" required>
                                    </div>
                                    
                                    {{-- Fila 2: Zona de Influencia y Nivel de Liderazgo --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_zona_influencia{{ $lider->id }}" class="form-label">Zona de Influencia</label>
                                        <select class="form-select" id="edit_zona_influencia{{ $lider->id }}" name="zona_influencia" required>
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
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_nivel_liderazgo{{ $lider->id }}" class="form-label">Nivel de Liderazgo</label>
                                        <select class="form-select" id="edit_nivel_liderazgo{{ $lider->id }}" name="nivel_liderazgo" required>
                                            <option value="">Seleccionar nivel...</option>
                                            <option value="1" {{ ($lider->lider->nivel_liderazgo ?? '') == '1' ? 'selected' : '' }}>Nivel 1 - Básico</option>
                                            <option value="2" {{ ($lider->lider->nivel_liderazgo ?? '') == '2' ? 'selected' : '' }}>Nivel 2 - Intermedio</option>
                                            <option value="3" {{ ($lider->lider->nivel_liderazgo ?? '') == '3' ? 'selected' : '' }}>Nivel 3 - Avanzado</option>
                                            <option value="4" {{ ($lider->lider->nivel_liderazgo ?? '') == '4' ? 'selected' : '' }}>Nivel 4 - Experto</option>
                                            <option value="5" {{ ($lider->lider->nivel_liderazgo ?? '') == '5' ? 'selected' : '' }}>Nivel 5 - Maestro</option>
                                        </select>
                                    </div>
                                    
                                    {{-- Fila 3: Afiliación Política y Teléfono --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_afiliacion_politica{{ $lider->id }}" class="form-label">Afiliación Política</label>
                                        <select class="form-select" id="edit_afiliacion_politica{{ $lider->id }}" name="afiliacion_politica">
                                            <option value="">Sin afiliación</option>
                                            <option value="Conservador" {{ ($lider->lider->afiliacion_politica ?? '') == 'Conservador' ? 'selected' : '' }}>Conservador</option>
                                            <option value="Liberal" {{ ($lider->lider->afiliacion_politica ?? '') == 'Liberal' ? 'selected' : '' }}>Liberal</option>
                                            <option value="Centro Democrático" {{ ($lider->lider->afiliacion_politica ?? '') == 'Centro Democrático' ? 'selected' : '' }}>Centro Democrático</option>
                                            <option value="Cambio Radical" {{ ($lider->lider->afiliacion_politica ?? '') == 'Cambio Radical' ? 'selected' : '' }}>Cambio Radical</option>
                                            <option value="Polo Democrático" {{ ($lider->lider->afiliacion_politica ?? '') == 'Polo Democrático' ? 'selected' : '' }}>Polo Democrático</option>
                                            <option value="Alianza Verde" {{ ($lider->lider->afiliacion_politica ?? '') == 'Alianza Verde' ? 'selected' : '' }}>Alianza Verde</option>
                                            <option value="FARC" {{ ($lider->lider->afiliacion_politica ?? '') == 'FARC' ? 'selected' : '' }}>FARC</option>
                                            <option value="Independiente" {{ ($lider->lider->afiliacion_politica ?? '') == 'Independiente' ? 'selected' : '' }}>Independiente</option>
                                            <option value="Otro" {{ ($lider->lider->afiliacion_politica ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_telefono{{ $lider->id }}" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="edit_telefono{{ $lider->id }}" name="telefono" value="{{ $lider->lider->telefono ?? '' }}" placeholder="Ej: +57 300 123 4567">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Actualizar Líder
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <tr><td colspan="8" class="text-center">No hay líderes registrados.</td></tr>
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
                <h5 class="modal-title" id="createModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Agregar Nuevo Líder
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="{{ route('admin.lideres.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        {{-- Fila 1: Nombre y Email --}}
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        {{-- Fila 2: Contraseña y Confirmar Contraseña --}}
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        {{-- Fila 3: Zona de Influencia y Nivel de Liderazgo --}}
                        <div class="col-md-6 mb-3">
                            <label for="zona_influencia" class="form-label">Zona de Influencia</label>
                            <select class="form-select" id="zona_influencia" name="zona_influencia" required>
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
                            <label for="nivel_liderazgo" class="form-label">Nivel de Liderazgo</label>
                            <select class="form-select" id="nivel_liderazgo" name="nivel_liderazgo" required>
                                <option value="">Seleccionar nivel...</option>
                                <option value="1">Nivel 1 - Básico</option>
                                <option value="2">Nivel 2 - Intermedio</option>
                                <option value="3">Nivel 3 - Avanzado</option>
                                <option value="4">Nivel 4 - Experto</option>
                                <option value="5">Nivel 5 - Maestro</option>
                            </select>
                        </div>
                        
                        {{-- Fila 4: Afiliación Política y Teléfono --}}
                        <div class="col-md-6 mb-3">
                            <label for="afiliacion_politica" class="form-label">Afiliación Política</label>
                            <select class="form-select" id="afiliacion_politica" name="afiliacion_politica">
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
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej: +57 300 123 4567">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Crear Líder
                    </button>
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

// Limpiar formulario cuando se cierre el modal
document.getElementById('createModal').addEventListener('hidden.bs.modal', function () {
    const form = this.querySelector('form');
    form.reset();
});
</script>
@endsection