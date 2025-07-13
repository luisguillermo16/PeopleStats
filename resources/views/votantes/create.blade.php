@extends('layouts.app')

@section('title', 'Registrar Votante')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus"></i> Registrar Nuevo Votante
                    </h4>
                    <small class="text-muted">
                        @if($tipoLider === 'concejal')
                            Registrando para: <strong>{{ $concejalAsociado->name }}</strong> 
                            (Alcalde: {{ $alcaldeAsociado->name }})
                        @elseif($tipoLider === 'alcalde')
                            Registrando para: <strong>{{ $alcaldeAsociado->name }}</strong> (Alcalde)
                        @endif
                    </small>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('votantes.store') }}" id="formVotante">
                        @csrf

                        {{-- Datos básicos del votante --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user"></i> Nombre Completo *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre') }}" 
                                           required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cedula" class="form-label">
                                        <i class="fas fa-id-card"></i> Cédula *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('cedula') is-invalid @enderror" 
                                           id="cedula" 
                                           name="cedula" 
                                           value="{{ old('cedula') }}" 
                                           required>
                                    @error('cedula')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone"></i> Teléfono
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('telefono') is-invalid @enderror" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono') }}">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="direccion" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Dirección
                            </label>
                            <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                      id="direccion" 
                                      name="direccion" 
                                      rows="3">{{ old('direccion') }}</textarea>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Opciones específicas según tipo de líder --}}
                        @if($tipoLider === 'concejal')
                            {{-- CASO A: Líder de concejal --}}
                            <div class="card mt-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-vote-yea"></i> Opciones de Votación
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="vincular_alcalde" 
                                               name="vincular_alcalde" 
                                               value="1" 
                                               {{ old('vincular_alcalde') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="vincular_alcalde">
                                            <strong>¿El votante también desea votar por el alcalde {{ $alcaldeAsociado->name }}?</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        El votante ya está siendo registrado para {{ $concejalAsociado->name }}.
                                        Opcionalmente puede también votar por el alcalde asociado.
                                    </small>
                                </div>
                            </div>

                        @elseif($tipoLider === 'alcalde')
                            {{-- CASO B: Líder de alcalde --}}
                            <div class="card mt-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-vote-yea"></i> Opciones de Votación
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3">
                                        <strong>El votante será registrado para {{ $alcaldeAsociado->name }} (Alcalde)</strong>
                                    </p>

                                    @if($concejalesDisponibles->count() > 0)
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-users"></i> ¿Desea votar por un concejal también?
                                            </label>
                                            <select class="form-select @error('concejal_seleccionado') is-invalid @enderror" 
                                                    id="concejal_seleccionado" 
                                                    name="concejal_seleccionado">
                                                <option value="">-- No seleccionar concejal --</option>
                                                @foreach($concejalesDisponibles as $concejal)
                                                    <option value="{{ $concejal->id }}" 
                                                            {{ old('concejal_seleccionado') == $concejal->id ? 'selected' : '' }}>
                                                        {{ $concejal->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('concejal_seleccionado')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            No hay concejales disponibles para este alcalde.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Botones de acción --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('votantes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Registrar Votante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Validación en tiempo real --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formVotante');
    const cedulaInput = document.getElementById('cedula');
    const nombreInput = document.getElementById('nombre');

    // Validar cédula mientras escribe
    cedulaInput.addEventListener('input', function() {
        // Solo números
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Validar longitud
        if (this.value.length > 0 && this.value.length < 6) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });

    // Formatear nombre
    nombreInput.addEventListener('blur', function() {
        this.value = this.value.toLowerCase().replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
    });

    // Validación antes de enviar
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validar nombre
        if (nombreInput.value.trim().length < 3) {
            nombreInput.classList.add('is-invalid');
            isValid = false;
        }

        // Validar cédula
        if (cedulaInput.value.length < 6) {
            cedulaInput.classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Por favor corrige los errores antes de continuar');
        }
    });
});
</script>
@endsection