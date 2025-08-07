<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Votante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstadisticaBarrios extends Component
{
    public $votantesPorBarrio;

    public function __construct()
    {
        $this->votantesPorBarrio = $this->getDatos();
    }

private function getDatos()
{
    $usuario = Auth::user();

    if (!$usuario || !$usuario->hasRole('aspirante-alcaldia')) {
        return collect();
    }

    return Votante::select('barrio_id', DB::raw('count(*) as total'))
        ->where('alcalde_id', $usuario->id)
        ->groupBy('barrio_id')
        ->with('barrio:id,nombre')
        ->get()
        ->map(function ($item) {
            return [
                'nombre' => optional($item->barrio)->nombre ?? 'Sin barrio',
                'total' => $item->total
            ];
        });
}


    public function render(): View|Closure|string
    {
        return view('components.estadisticas.estadistica-barrios', [
            'votantesPorBarrio' => $this->votantesPorBarrio,
        ]);
    }
}
