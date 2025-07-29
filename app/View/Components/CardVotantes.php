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
    public $rol; 

    public function __construct()
    {
        $user = Auth::user();
        $this->rol = $user->getRoleNames()->first(); 

        if ($user->hasRole('aspirante-alcaldia')) {
            $this->totalVotantes = Votante::where('alcalde_id', $user->id)->count();
        } elseif ($user->hasRole('aspirante-concejo')) {
            $this->totalVotantes = Votante::where('concejal_id', $user->id)->count();
        } elseif ($user->hasRole('lider')) {
            $this->totalVotantes = Votante::where('lider_id', $user->id)->count();
        } else {
            $this->totalVotantes = 0;
        }

      

        $objetivo = 15000;
        $this->porcentajeObjetivo = $objetivo > 0
            ? round(($this->totalVotantes / $objetivo) * 100, 2)
            : 0;
            $this->rol = $user->getRoleNames()->first();
          
    }

    public function render()
    {
        return view('components.card-votantes');
    }
}

