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

                        {{-- Nombre --}}
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input name="nombre" type="text" class="form-control" value="{{ $votante->nombre }}" required>
                        </div>

                        {{-- Cédula --}}
                        <div class="col-md-6">
                            <label class="form-label">Cédula</label>
                            <input name="cedula" type="text" class="form-control" 
                                   value="{{ $votante->cedula }}" required
                                   inputmode="numeric" pattern="[0-9]*"
                                   oninput="this.value = this.value.replace(/[^0-9]/g,'');">
                        </div>

                        {{-- Teléfono --}}
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input name="telefono" type="text" class="form-control" value="{{ $votante->telefono }}" required
                                   inputmode="numeric" pattern="[0-9]*"
                                   oninput="this.value = this.value.replace(/[^0-9]/g,'');">
                        </div>

                        {{-- Barrio --}}
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

                        {{-- Lugar de Votación --}}
                        <div class="col-md-6">
                            <label class="form-label">Lugar de Votación</label>
                            <select class="form-select lugar-select" name="lugar_votacion_id" data-mesa-select="mesa_edit_{{ $votante->id }}">
                                <option value="">Seleccione un lugar</option>
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar['id'] }}" data-mesas='@json($lugar['mesas'])' 
                                        {{ $votante->lugar_votacion_id == $lugar['id'] ? 'selected' : '' }}>
                                        {{ $lugar['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Mesa --}}
                        <div class="col-md-6">
                            <label class="form-label">Mesa</label>
                            <select id="mesa_edit_{{ $votante->id }}" name="mesa_id" class="form-select" required>
                                <option value="">Seleccione una mesa</option>
                            </select>
                        </div>

                        {{-- Concejal o Alcalde --}}
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lugarSelect = document.querySelector('#editModal{{ $votante->id }} .lugar-select');
    const mesaSelect = document.getElementById('mesa_edit_{{ $votante->id }}');

    function actualizarMesas(lugarSelect, mesaSelect, votanteMesaId = null) {
        mesaSelect.innerHTML = '<option value="">Seleccione una mesa</option>';
        const selectedOption = lugarSelect.selectedOptions[0];
        if (!selectedOption) {
            mesaSelect.disabled = true;
            return;
        }
        try {
            const mesas = JSON.parse(selectedOption.dataset.mesas || '[]');
            mesas.forEach(mesa => {
                const opt = document.createElement('option');
                opt.value = mesa.id;
                opt.textContent = `Mesa ${mesa.numero}`;
                if (votanteMesaId && mesa.id == votanteMesaId) {
                    opt.selected = true;
                }
                mesaSelect.appendChild(opt);
            });
            mesaSelect.disabled = mesas.length === 0;
        } catch (error) {
            console.error('Error al procesar mesas:', error);
            mesaSelect.disabled = true;
        }
    }

    // Inicializar mesas si ya hay un valor seleccionado
    const votanteMesaId = {{ $votante->mesa_id ?? 'null' }};
    actualizarMesas(lugarSelect, mesaSelect, votanteMesaId);

    // Listener para cambios en el select de lugar
    lugarSelect.addEventListener('change', () => {
        actualizarMesas(lugarSelect, mesaSelect);
    });
});
</script>
@endpush
