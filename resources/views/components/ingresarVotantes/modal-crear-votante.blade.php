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
                                   value="{{ old('cedula') }}"
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
                                    <option value="{{ $barrio->id }}" {{ old('barrio_id') == $barrio->id ? 'selected' : '' }}>
                                        {{ $barrio->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lugar de Votación</label>
                            <select id="lugar_select_create" name="lugar_votacion_id" class="form-select lugar-select" data-mesa-select="mesa_select_create" required>
                                <option value="">Seleccione un lugar</option>
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar['id'] }}" 
                                            data-mesas='@json($lugar['mesas'])'
                                            {{ old('lugar_votacion_id') == $lugar['id'] ? 'selected' : '' }}>
                                        {{ $lugar['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mesa</label>
                            <select id="mesa_select_create" name="mesa_id" class="form-select mesa-select" required>
                                <option value="">Seleccione una mesa</option>
                                {{-- Las opciones se llenarán dinámicamente con JavaScript --}}
                            </select>
                        </div>

                        @if(is_null($lider->concejal_id))
                            <div class="col-md-6">
                                <label class="form-label">¿También vota por el concejal?</label>
                                <select name="concejal_id" class="form-select">
                                    <option value="">Seleccione</option>
                                    @foreach($concejalOpciones as $concejal)
                                        <option value="{{ $concejal->id }}" {{ old('concejal_id') == $concejal->id ? 'selected' : '' }}>
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
                                    <option value="1" {{ old('tambien_vota_alcalde') == '1' ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ old('tambien_vota_alcalde') == '0' ? 'selected' : '' }}>No</option>
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lugarSelect = document.getElementById('lugar_select_create');
    const mesaSelect = document.getElementById('mesa_select_create');

    function actualizarMesas(lugarSelect, mesaSelect, mesaId = null) {
        mesaSelect.innerHTML = '<option value="">Seleccione una mesa</option>';
        const selectedOption = lugarSelect.selectedOptions[0];

        // Si no hay lugar seleccionado, deshabilitar mesa
        if (!selectedOption || !selectedOption.value) {
            mesaSelect.disabled = true;
            return;
        }

        try {
            const mesas = JSON.parse(selectedOption.dataset.mesas || '[]');
            mesas.forEach(mesa => {
                const opt = document.createElement('option');
                opt.value = mesa.id;
                opt.textContent = `Mesa ${mesa.numero}`;
                if (mesaId && mesa.id == mesaId) opt.selected = true;
                mesaSelect.appendChild(opt);
            });
            mesaSelect.disabled = mesas.length === 0;
        } catch (e) {
            console.error('Error al procesar mesas:', e);
            mesaSelect.disabled = true;
        }
    }

    // Inicializar mesas y estado de habilitado
    actualizarMesas(lugarSelect, mesaSelect, {{ old('mesa_id') ?? 'null' }});

    // Listener para cambios en lugar
    lugarSelect.addEventListener('change', () => {
        actualizarMesas(lugarSelect, mesaSelect);
    });
});
</script>
@endpush
