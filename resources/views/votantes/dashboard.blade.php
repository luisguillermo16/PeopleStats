@extends('layouts.admin')

@section('tituloPage', 'Estadísticas de Votantes')

@section('contenido')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm"><div class="card-body">
            <h6 class="text-muted mb-1">Total votantes</h6>
            <div class="h4 mb-0">{{ $totalVotantes }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm"><div class="card-body">
            <h6 class="text-muted mb-1">Mesas distintas</h6>
            <div class="h4 mb-0">{{ $totalMesas }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm"><div class="card-body">
            <h6 class="text-muted mb-1">Concejales</h6>
            <div class="h4 mb-0">{{ $totalConcejales }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm"><div class="card-body">
            <h6 class="text-muted mb-1">Líderes</h6>
            <div class="h4 mb-0">{{ $totalLideres }}</div>
        </div></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-0">Votantes por lugar</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse ($votantesPorLugar as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item['nombre'] }}</span>
                            <span class="fw-semibold">{{ $item['total'] }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">Sin datos</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-0">Votantes por barrio</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse ($votantesPorBarrio as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item['nombre'] }}</span>
                            <span class="fw-semibold">{{ $item['total'] }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">Sin datos</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-0">Votantes por mes</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse ($votantesPorMes as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item['mes'] }}</span>
                            <span class="fw-semibold">{{ $item['total'] }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">Sin datos</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection


