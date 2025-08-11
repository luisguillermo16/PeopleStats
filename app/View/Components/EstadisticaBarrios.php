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

        if (!$usuario) {
            return collect();
        }

        // Consulta base
        $query = Votante::select('barrio_id', DB::raw('count(*) as total'))
            ->groupBy('barrio_id')
            ->with('barrio:id,nombre');

        // Filtrado según el rol
        if ($usuario->hasRole('aspirante-alcaldia')) {
            $query->where('alcalde_id', $usuario->id);
        } elseif ($usuario->hasRole('aspirante-concejo')) {
            $query->where('concejal_id', $usuario->id);
        } elseif ($usuario->hasRole('lider')) {
            $query->where('lider_id', $usuario->id);
        } else {
            // Si el rol no coincide con ninguno de los tres, no retorna datos
            return collect();
        }

        // Retorna datos mapeados
        return $query->get()->map(function ($item) {
            return [
                'nombre' => optional($item->barrio)->nombre ?? 'Sin barrio',
                'total'  => $item->total
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
