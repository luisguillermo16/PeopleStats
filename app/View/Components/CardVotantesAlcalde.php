<?php

namespace App\View\Components;

use App\Models\Votante;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class CardVotantesAlcalde extends Component
{
    public $totalVotantes;
    public $crecimiento;
    public $porcentajeObjetivo;

    public function __construct()
    {
        // Obtener el alcalde autenticado
        $alcalde = Auth::user();

        // Total votantes relacionados a este alcalde
        $this->totalVotantes = Votante::where('alcalde_id', $alcalde->id)->count();

        // Cálculo de crecimiento (dummy por ahora, puedes reemplazar por lógica real)
        $this->crecimiento = 15; // % crecimiento mensual

        // Supongamos objetivo de 1000 votantes
        $objetivo = 1000;
        $this->porcentajeObjetivo = $objetivo > 0
            ? round(($this->totalVotantes / $objetivo) * 100, 2)
            : 0;
    }

    public function render()
    {
        return view('components.card-votantes-alcalde');
    }
}
