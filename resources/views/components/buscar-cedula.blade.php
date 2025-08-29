<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <h5 class="card-title mb-3 text-secondary fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-search text-info fs-4"></i> Verificación de Votante
        </h5>

        <div class="input-group">
            <span class="input-group-text bg-info text-white rounded-start-3">
                <i class="bi bi-person-vcard"></i>
            </span>
            <input 
                type="number" 
                id="cedula" 
                name="cedula" 
                class="form-control border-info" 
                placeholder="Ingrese cédula del votante" 
                required
            >
            <button 
                type="button" 
                id="btnBuscarCedula" 
                class="btn btn-info text-white fw-semibold px-3 rounded-end-3"
            >
                Verificar
            </button>
        </div>

        <div id="feedbackCedula" class="form-text mt-2 text-muted">
            Ingresa la cédula y presiona <strong>Verificar</strong> para comprobar si ya está registrada.
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputCedula = document.getElementById('cedula');
    const feedback = document.getElementById('feedbackCedula');

    document.getElementById('btnBuscarCedula').addEventListener('click', function () {
        const cedula = inputCedula.value.trim();

        if (!cedula) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Por favor, ingresa una cédula antes de buscar.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Animación de estado de búsqueda
      
        feedback.classList.remove('text-success', 'text-danger');
        feedback.classList.add('text-info');

        fetch(`{{ route('votantes.buscar_por_cedula') }}?cedula=${cedula}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    feedback.classList.remove('text-info', 'text-success');
                    feedback.classList.add('text-danger');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Votante Duplicado',
                        text: data.message || 'Este votante ya está registrado en esta campaña.',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    feedback.classList.remove('text-info', 'text-danger');
                    feedback.classList.add('text-success');

                    Swal.fire({
                        icon: 'success',
                        title: 'Cédula Disponible',
                        text: data.message || 'Esta cédula no está registrada. Puedes continuar con el registro.',
                        confirmButtonColor: '#28a745'
                    });
                }
            })
            .catch(error => {
             
                feedback.classList.remove('text-info', 'text-success');
                feedback.classList.add('text-danger');

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo verificar la cédula.',
                    confirmButtonColor: '#d33'
                });
            });
    });
});
</script>
