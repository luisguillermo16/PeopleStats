<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Votante;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TendenciaSemanal extends Component
{
    public $labels;
    public $data;

    public function __construct()
    {
        [$this->labels, $this->data] = $this->getTendencia();
    }

    private function getTendencia(): array
    {
        $usuario = Auth::user();
        $labels = [];
        $data = [];

        // Últimos 7 días
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $labels[] = $fecha->locale('es')->translatedFormat('D'); // Lun, Mar, etc.

            $query = Votante::whereDate('created_at', $fecha);

            // Filtrado por rol
            if ($usuario->hasRole('aspirante-alcaldia')) {
                $query->where('alcalde_id', $usuario->id);
            } elseif ($usuario->hasRole('aspirante-concejo')) {
                $query->where('concejal_id', $usuario->id);
            } elseif ($usuario->hasRole('lider')) {
                $query->where('lider_id', $usuario->id);
            }

            $data[] = $query->count();
        }

        return [$labels, $data];
    }

    public function render(): View|Closure|string
    {
        return view('components.estadisticas.tendencia-semanal', [
            'labels' => $this->labels,
            'data'   => $this->data
        ]);
    }
}
