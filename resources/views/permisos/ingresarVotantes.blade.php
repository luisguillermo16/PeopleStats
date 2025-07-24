@extends('layouts.admin')

@section('tituloPage', 'Gestión de Votantes')

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

    {{-- Botón Nuevo --}}
    <div class="mb-3 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-2"></i>
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
                    <th class="d-none d-lg-table-cell">Mesa</th>
                    <th class="d-none d-sm-table-cell">Fecha</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($votantes as $votante)
                    <tr>
                        <td class="d-none d-md-table-cell">
                            <input type="checkbox" class="form-check-input item-checkbox">
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $votante->nombre }}</div>
                            <div class="d-md-none"><small class="text-muted">{{ $votante->telefono }}</small></div>
                        </td>
                        <td class="d-none d-md-table-cell">{{ $votante->cedula }}</td>
                        <td class="d-none d-lg-table-cell">{{ $votante->telefono }}</td>
                        <td class="d-none d-lg-table-cell">{{ $votante->mesa }}</td>
                        <td class="d-none d-sm-table-cell">{{ $votante->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('votantes.destroy', $votante) }}" onsubmit="return confirm('¿Eliminar este votante?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No hay votantes registrados</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación 
    @if($votantes->hasPages())
        <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <small class="text-muted mb-2 mb-md-0">
                Mostrando {{ $votantes->firstItem() }}-{{ $votantes->lastItem() }} de {{ $votantes->total() }} votantes
            </small>
            {{ $votantes->links() }}
        </div>
    @endif--}}
<!-- Modal para crear votante -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('votantes.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Nuevo Votante</span>
                        <span class="d-sm-none">Nuevo</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input name="nombre" type="text" class="form-control" value="{{ old('nombre') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Cédula</label>
                            <input name="cedula" type="text" class="form-control" value="{{ old('cedula') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input name="telefono" type="text" class="form-control" value="{{ old('telefono') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Mesa</label>
                            <input name="mesa" type="text" class="form-control" value="{{ old('mesa') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Donación</label>
                            <input name="donacion" type="text" class="form-control" value="{{ old('donacion') }}">
                        </div>


                    {{-- Caso A: Líder de un Alcalde --}}
                    @if(is_null($lider->concejal_id))
                      <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Seleccione un concejal</label>
                        <select name="concejal_id" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach($concejalOpciones as $concejal)
                                <option value="{{ $concejal->id }}">
                                    {{ $concejal->name ?? $concejal->user->name ?? 'Sin nombre' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Caso B: Líder de un Concejal --}}
                    @if(!is_null($lider->concejal_id))
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">¿También vota al alcalde?</label>
                            <select name="tambien_vota_alcalde" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option value="1" {{ old('tambien_vota_alcalde') == '1' ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ old('tambien_vota_alcalde') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    @endif
                    </div>
                </div>

                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <span class="d-none d-sm-inline">Cancelar</span>
                        <span class="d-sm-none">❌</span>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="d-none d-sm-inline">Guardar Votante</span>
                        <span class="d-sm-none">✓</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

   

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const votarPorConcejal = document.getElementById('votar_por_concejal');
        const selectorConcejal = document.getElementById('selector_concejal');

        function toggleConcejalSelector() {
            if (votarPorConcejal.value === 'si') {
                selectorConcejal.style.display = 'block';
            } else {
                selectorConcejal.style.display = 'none';
            }
        }

        votarPorConcejal.addEventListener('change', toggleConcejalSelector);

        // Ejecutar en caso de validación fallida y reenvío del formulario
        toggleConcejalSelector();

        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                itemCheckboxes.forEach(cb => cb.checked = this.checked);
            });
        }

        @if ($errors->any() && old('_token'))
            const createModal = new bootstrap.Modal(document.getElementById('createModal'));
            createModal.show();
        @endif
    });
</script>
@endpush
