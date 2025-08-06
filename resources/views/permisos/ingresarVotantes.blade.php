@extends('layouts.admin')

@section('tituloPage', 'Gestión de Votantes')

@section('contenido')



{{-- Botones de acción --}}
<div class="mb-3 d-flex flex-column flex-md-row gap-2 justify-content-md-end">
    {{-- Botón Importar Excel --}}
    <div class="d-flex align-items-center">
        <form action="{{ route('votantes.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-sm-row gap-2 align-items-center">
            @csrf
            <div class="position-relative">
                <input type="file" 
                       id="excel_file" 
                       name="excel_file" 
                       class="form-control d-none" 
                       accept=".xlsx,.xls,.csv"
                       required>
                <button type="button" 
                        class="btn btn-outline-success btn-sm" 
                        onclick="document.getElementById('excel_file').click()">
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    <span class="d-none d-sm-inline">Seleccionar</span> Excel
                </button>
            </div>
            <button type="submit" 
                    class="btn btn-success btn-sm" 
                    id="importBtn" 
                    disabled>
                <i class="bi bi-upload me-1"></i>
                <span class="d-none d-sm-inline">Importar</span>
                <span class="d-sm-none">Import</span>
            </button>
        </form>
    </div>
    
    {{-- Botón Nuevo Votante --}}
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-1"></i>
        <span class="d-none d-sm-inline">Nuevo Votante</span>
        <span class="d-sm-none">Nuevo</span>
    </button>
</div>


{{-- Tabla --}}
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th class="d-none d-md-table-cell"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                <th>Nombre</th>
                <th class="d-none d-md-table-cell">Cédula</th>
                <th class="d-none d-lg-table-cell">Teléfono</th>
                 <th class="d-none d-lg-table-cell">Barrio</th>
                <th class="d-none d-lg-table-cell">Lugar de Votación</th>
                <th class="d-none d-lg-table-cell">Mesa</th>
                <th class="d-none d-lg-table-cell">
                    @if(is_null($lider->concejal_id)) ¿Vota Concejal? @else ¿Vota Alcalde? @endif
                </th>
                <th width="150">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($votantes as $votante)
                <tr>
                    <td class="d-none d-md-table-cell"><input type="checkbox" class="form-check-input item-checkbox"></td>
                    <td>
                        <div class="fw-semibold">{{ $votante->nombre }}</div>
                        <div class="d-md-none"><small class="text-muted">{{ $votante->telefono }}</small></div>
                    </td>
                    <td class="d-none d-md-table-cell">{{ $votante->cedula }}</td>
                    <td class="d-none d-lg-table-cell">{{ $votante->telefono ?? 'Sin asignar' }}</td>
                    <td class="d-none d-lg-table-cell">{{ $votante->barrio->nombre ?? 'Sin asignar' }}</td>
                    <td class="d-none d-lg-table-cell">{{ $votante->lugarVotacion->nombre ?? 'Sin asignar' }}</td>
                    <td class="d-none d-lg-table-cell">{{ $votante->mesa ?? 'Sin asignar' }}</td>
                    <td class="d-none d-lg-table-cell">
                      @if(is_null($lider->concejal_id))
                            {{ $votante->concejal?->name ?? $votante->concejal?->user?->name ?? 'No' }}
                        @else
                            {{ $votante->alcalde_id ? 'Sí' : 'No' }}
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $votante->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center py-4">No hay votantes registrados</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<x-paginacion :collection="$votantes" />

{{-- Modal Crear --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('votantes.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Nuevo Votante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input name="nombre" type="text" class="form-control" value="{{ old('nombre') }}" required>
                        </div>
                       <div class="col-md-6">
                            <label class="form-label">Cédula</label>
                            <input name="cedula" type="text" class="form-control" 
                                
                                required
                                inputmode="numeric"
                                pattern="[0-9]*"
                                oninput="this.value = this.value.replace(/[^0-9]/g,'');">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input name="telefono" type="text" class="form-control" value="{{ old('telefono') }}" 
                                required
                                inputmode="numeric"
                                pattern="[0-9]*"
                                oninput="this.value = this.value.replace(/[^0-9]/g,'');">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barrio</label>
                            <select name="barrio_id" class="form-select" required>
                                <option value="">Seleccione un barrio</option>
                                @foreach($barrios as $barrio)
                                    <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lugar de Votación</label>
                            <select id="lugar_select_create" name="lugar_votacion_id" class="form-select lugar-select" data-mesa-select="mesa_select_create" required>
                                <option value="">Seleccione un lugar</option>
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar['id'] }}" data-mesas='@json($lugar['mesas'])'>{{ $lugar['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mesa</label>
                            <select id="mesa_select_create" name="mesa" class="form-select mesa-select" disabled required>
                                <option value="">Seleccione una mesa</option>
                            </select>
                        </div>

                        @if(is_null($lider->concejal_id))
                            <div class="col-md-6">
                                <label class="form-label">¿También vota por el concejal?</label>
                                <select name="concejal_id" class="form-select">
                                    <option value="">Seleccione</option>
                                    @foreach($concejalOpciones as $concejal)
                                        <option value="{{ $concejal->id }}">{{ $concejal->name ?? $concejal->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="col-md-6">
                                <label class="form-label">¿También vota al alcalde?</label>
                                <select name="tambien_vota_alcalde" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Votante</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modales Editar --}}
@foreach ($votantes as $votante)
    <div class="modal fade" id="editModal{{ $votante->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('votantes.update', $votante->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Votante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input name="nombre" type="text" class="form-control" value="{{ $votante->nombre }}" required>
                            </div>
                          <div class="col-md-6">
                                <label class="form-label">Cédula</label>
                                <input name="cedula" type="text" class="form-control" 
                                    value="{{ $votante->cedula }}" 
                                    required
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    oninput="this.value = this.value.replace(/[^0-9]/g,'');">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input name="telefono" type="text" class="form-control" value="{{ $votante->telefono }}" required
                                inputmode="numeric"
                                pattern="[0-9]*"
                                oninput="this.value = this.value.replace(/[^0-9]/g,'');">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Barrio</label>
                                <select name="barrio_id" class="form-select" required>
                                    <option value="">Seleccione un barrio</option>
                                    @foreach($barrios as $barrio)
                                        <option value="{{ $barrio->id }}" {{ $votante->barrio_id == $barrio->id ? 'selected' : '' }}>
                                            {{ $barrio->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Lugar de Votación</label>
                                <select class="form-select lugar-select" name="lugar_votacion_id" data-mesa-select="mesa_edit_{{ $votante->id }}">
                                    <option value="">Seleccione un lugar</option>
                                    @foreach($lugares as $lugar)
                                        <option value="{{ $lugar['id'] }}"
                                            data-mesas='@json($lugar['mesas'])'
                                            {{ $votante->lugar_votacion_id == $lugar['id'] ? 'selected' : '' }}>
                                            {{ $lugar['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mesa</label>
                                <select class="form-select mesa-select" name="mesa" id="mesa_edit_{{ $votante->id }}">
                                    <option value="">Seleccione una mesa</option>
                                    @if ($votante->lugarVotacion)
                                        @foreach($votante->lugarVotacion->mesas as $mesa)
                                            <option value="{{ $mesa->numero }}" {{ $mesa->numero == $votante->mesa ? 'selected' : '' }}>
                                                {{ $mesa->numero }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            @if(is_null($lider->concejal_id))
                                <div class="col-md-6">
                                    <label class="form-label">¿También vota por el concejal?</label>
                                    <select name="concejal_id" class="form-select">
                                        <option value="">Seleccione</option>
                                        @foreach($concejalOpciones as $concejal)
                                            <option value="{{ $concejal->id }}" {{ $votante->concejal_id == $concejal->id ? 'selected' : '' }}>
                                                {{ $concejal->name ?? $concejal->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label class="form-label">¿También vota al alcalde?</label>
                                    <select name="tambien_vota_alcalde" class="form-select" required>
                                        <option value="">Seleccione</option>
                                       <option value="1" {{ $votante->alcalde_id ? 'selected' : '' }}>Sí</option>
                                        <option value="0" {{ !$votante->alcalde_id ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            @endif
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
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ==========================
    // Actualización de Mesas
    // ==========================
    const actualizarMesas = (lugarSelect, mesaSelect) => {
        mesaSelect.innerHTML = '<option value="">Seleccione una mesa</option>';
        const mesas = JSON.parse(lugarSelect.selectedOptions[0]?.dataset.mesas || '[]');
        mesas.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.numero;
            opt.textContent = m.numero;
            mesaSelect.appendChild(opt);
        });
        mesaSelect.disabled = mesas.length === 0;
    };

    document.querySelectorAll('.lugar-select').forEach(select => {
        const mesaId = select.dataset.mesaSelect;
        const modal = select.closest('.modal');
        const mesaSelect = modal.querySelector(`#${mesaId}`);
        if (select.value) actualizarMesas(select, mesaSelect);
        select.addEventListener('change', () => actualizarMesas(select, mesaSelect));
    });

    // ==========================
    // Checkbox seleccionar todos
    // ==========================
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // ==========================
    // Reabrir modal si hay errores
    // ==========================
    @if ($errors->any() && !session('editModalId'))
        const modalCreate = new bootstrap.Modal(document.getElementById('createModal'));
        modalCreate.show(); // Se abre con fondo normal
    @endif

    // ==========================
    // Reabrir modal editar si aplica
    // ==========================
    @if(session('editModalId'))
        new bootstrap.Modal(document.getElementById('editModal{{ session('editModalId') }}')).show();
    @endif

    // ==========================
    // Confirmación SweetAlert antes de eliminar
    // ==========================
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
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // ==========================
    // SweetAlert de éxito y error
    // ==========================
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

    // ==========================
    // SweetAlert de validación (no cierra modal)
    // ==========================
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#d33'
        });
    @endif

    // ==========================
    // Limpiar backdrop al cerrar modal
    // ==========================
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', () => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow'); // evita scroll bloqueado
            document.body.style.removeProperty('padding-right');
        });
    });
});
</script>
@endpush
