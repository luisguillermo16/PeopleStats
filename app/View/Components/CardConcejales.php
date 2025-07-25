<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class CardConcejales extends Component
{
    public $totalConcejales;
    public $estado;
    public $progreso;

    public function __construct()
    {
        // Alcalde autenticado
        $alcalde = Auth::user();

        // Contar concejales vinculados
        $this->totalConcejales = User::role('aspirante-concejo')
            ->where('alcalde_id', $alcalde->id)
            ->count();

        // Determinar estado (dummy por ahora)
        $this->estado = $this->totalConcejales >= 24 ? 'Estable' : 'Faltan';

        // Porcentaje progreso (ejemplo máximo 24 concejales)
        $objetivo = 50;
        $this->progreso = $objetivo > 0
            ? round(($this->totalConcejales / $objetivo) * 100, 2)
            : 0;
    }

    public function render()
    {
        return view('components.card-concejales');
    }
}
