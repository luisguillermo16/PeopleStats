<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class CardLideres extends Component
{
    public $totalLideres;
    public $estado;
    public $progreso;

    public function __construct()
    {
        $user = Auth::user(); // Usuario autenticado

        // Filtrado de líderes según el rol
        if ($user->hasRole('aspirante-alcaldia') || $user->hasRole('alcalde')) {
            // Líderes creados por este alcalde
            $this->totalLideres = User::role('lider')
                ->where('alcalde_id', $user->id)
                ->count();
        } elseif ($user->hasRole('aspirante-concejo') || $user->hasRole('concejal')) {
            // Líderes creados por este concejal
            $this->totalLideres = User::role('lider')
                ->where('concejal_id', $user->id)
                ->count();
        } else {
            // Si por algún error accede otro rol, mostrar 0
            $this->totalLideres = 0;
        }

       
    }

    public function render()
    {
        return view('components.card-lideres');
    }
}
