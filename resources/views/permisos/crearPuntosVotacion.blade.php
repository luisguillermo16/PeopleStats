@extends('layouts.admin')

@section('tituloPage', 'Puntos de Votación')

@section('contenido')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Puntos de Votación</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearPuntoModal">
            + Nuevo Punto
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Lugar</th>
                            <th>Dirección</th>
                            <th>Mesas</th>
                            <th>Creado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lugares as $lugar)
                            <tr>
                                <td>{{ $lugar->nombre }}</td>
                                <td>{{ $lugar->direccion }}</td>
                                <td>
                                    @foreach($lugar->mesas as $mesa)
                                        <span class="badge bg-primary">{{ $mesa->numero }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($lugar->alcalde_id)
                                        Alcalde (ID {{ $lugar->alcalde_id }})
                                    @elseif($lugar->concejal_id)
                                        Concejal (ID {{ $lugar->concejal_id }})
                                    @else
                                        No definido
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-editar"
                                        data-id="{{ $lugar->id }}"
                                        data-nombre="{{ $lugar->nombre }}"
                                        data-direccion="{{ $lugar->direccion }}"
                                        data-mesas='@json($lugar->mesas)'>
                                        Editar
                                    </button>
                                    <form action="{{ route('destroyPuntosVotacion', $lugar) }}" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar este punto de votación?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay puntos de votación registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Punto de Votación -->
<div class="modal fade" id="crearPuntoModal" tabindex="-1" aria-labelledby="crearPuntoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('storePuntosVotacion') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="crearPuntoModalLabel">Crear Nuevo Punto de Votación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre del Lugar</label>
          <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="direccion" class="form-label">Dirección (opcional)</label>
          <textarea name="direccion" id="direccion" class="form-control" rows="2"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Mesas</label>
          <div id="mesas-container">
            <div class="input-group mb-2">
              <input type="text" name="mesas[]" class="form-control" placeholder="Número de Mesa" required>
              <button type="button" class="btn btn-danger btn-remove-mesa">&times;</button>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary" id="btn-agregar-mesa">+ Agregar otra mesa</button>
        </div>

        <input type="hidden" name="alcalde_id" value="{{ auth()->user()->id }}">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Crear Punto</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Punto de Votación -->
<div class="modal fade" id="editarPuntoModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content" id="form-editar-punto">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Editar Punto de Votación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nombre del Lugar</label>
          <input type="text" name="nombre" id="editar-nombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Dirección</label>
          <textarea name="direccion" id="editar-direccion" class="form-control"></textarea>
        </div>
        <div class="mb-3">
          <label>Mesas Existentes</label>
          <div id="editar-mesas-container"></div>
        </div>
        <div class="mt-4 ">
          <label>Agregar Nuevas Mesas</label>
          <div id="editar-mesas-nuevas-container"></div>
          <button type="button" class="btn btn-sm btn-outline-primary" id="btn-agregar-mesa-nueva-editar">+ Agregar mesa</button>
        </div>
        <input type="hidden" name="alcalde_id" value="{{ auth()->user()->id }}">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Actualizar Punto</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Crear mesas
    document.getElementById('btn-agregar-mesa').addEventListener('click', function () {
        const contenedor = document.getElementById('mesas-container');
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="text" name="mesas[]" class="form-control" placeholder="Número de Mesa" required>
            <button type="button" class="btn btn-danger btn-remove-mesa">&times;</button>
        `;
        contenedor.appendChild(div);
    });

    document.body.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-mesa')) {
            e.target.closest('.input-group').remove();
        }
    });

    // Editar mesas
    document.querySelectorAll('.btn-editar').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const nombre = button.dataset.nombre;
            const direccion = button.dataset.direccion;
            const mesas = JSON.parse(button.dataset.mesas);

            document.getElementById('form-editar-punto').action = `/lugares/${id}`;
            document.getElementById('editar-nombre').value = nombre;
            document.getElementById('editar-direccion').value = direccion;

            const mesasContainer = document.getElementById('editar-mesas-container');
            mesasContainer.innerHTML = '';
            mesas.forEach(mesa => {
                mesasContainer.innerHTML += `
                    <div class="input-group mb-2">
                        <input type="text" name="mesas_existentes[${mesa.id}]" class="form-control" value="${mesa.numero}" required>
                        <button type="button" class="btn btn-danger btn-remove-mesa">&times;</button>
                    </div>
                `;
            });

            const modal = new bootstrap.Modal(document.getElementById('editarPuntoModal'));
            modal.show();
        });
    });

    document.getElementById('btn-agregar-mesa-nueva-editar').addEventListener('click', () => {
        document.getElementById('editar-mesas-nuevas-container').innerHTML += `
            <div class="input-group mb-2">
                <input type="text" name="mesas_nuevas[]" class="form-control" placeholder="Número de Mesa" required>
                <button type="button" class="btn btn-danger btn-remove-mesa">&times;</button>
            </div>
        `;
    });
});
</script>
@endpush
