<?php

namespace App\View\Components;

use App\Models\Votante;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class CardVotantes extends Component
{
    public $totalVotantes;
    public $crecimiento;
    public $porcentajeObjetivo;

    public function __construct()
    {
        $user = Auth::user();

        if ($user->hasRole('aspirante-alcaldia')) {
            // Votantes registrados por el alcalde
            $this->totalVotantes = Votante::where('alcalde_id', $user->id)->count();
        } elseif ($user->hasRole('aspirante-concejo')) {
            // Votantes registrados por el concejal
            $this->totalVotantes = Votante::where('concejal_id', $user->id)->count();
        } elseif ($user->hasRole('lider')) {
            // Votantes registrados por el líder
            $this->totalVotantes = Votante::where('user_id', $user->id)->count();
        } else {
            $this->totalVotantes = 0; // Usuario sin rol esperado
        }

        // Por ahora el crecimiento y objetivo son fijos (puedes ajustarlos luego)
        $this->crecimiento = 15; // % crecimiento mensual (ejemplo)

        $objetivo = 1000;
        $this->porcentajeObjetivo = $objetivo > 0
            ? round(($this->totalVotantes / $objetivo) * 100, 2)
            : 0;
    }

    public function render()
    {
        return view('components.card-votantes');
    }
}
