@extends('layouts.admin')

@section('tituloPage', 'Gestión de Barrios')

@section('contenido')
<div class="container py-6">

    <h1 class="mb-4 d-flex align-items-center">
    </h1>


    

    {{-- Formulario crear barrio --}}
    <div class="mb-4 p-4 border rounded bg-light">
        <form action="{{ route('crearBarrios.store') }}" method="POST" class="row g-3 align-items-end">
            @csrf
            <div class="col-12 col-md-8">
                <label for="nombre" class="form-label fw-semibold">Nombre del barrio</label>
                <input type="text" name="nombre" id="nombre" required 
                       class="form-control @error('nombre') is-invalid @enderror" 
                       value="{{ old('nombre') }}">
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-4 text-md-end">
                <button type="submit" class="btn btn-primary w-100 w-md-auto">
                    <i class="bi bi-plus-circle me-1"></i>
                    <span class="d-none d-sm-inline">Crear Barrio</span>
                    <span class="d-sm-none">Crear</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Lista de barrios --}}
    <div class="bg-white p-3 rounded shadow">
        <h2 class="mb-3 fw-semibold">Barrios registrados</h2>

        @if($barrios->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nombre de Barrio</th>
                            <th width="130" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barrios as $barrio)
                            <tr>
                                <td>{{ $barrio->nombre }}</td>
                                <td class="text-center">
                            <form action="{{ route('crearBarrios.destroy', $barrio->id) }}" method="POST" class="form-eliminar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3-fill"></i>
                                    <span class="d-none d-sm-inline">Eliminar</span>
                                </button>
                            </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted fst-italic">No hay barrios registrados aún.</p>
        @endif
    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmación SweetAlert antes de eliminar
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

    // Abrir modal de creación si hay errores
    @if($errors->any() && old('_token') && !$errors->has('email'))
        const createModal = new bootstrap.Modal(document.getElementById('createModal'));
        createModal.show();
    @endif

    // Abrir modal de edición si error pertenece a edición
    @if($errors->has('email') && session('edit_id'))
        const editModal = new bootstrap.Modal(document.getElementById('editModal{{ session("edit_id") }}'));
        editModal.show();
    @endif
});
</script>
@endpush
