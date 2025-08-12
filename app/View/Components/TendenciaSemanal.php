<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Votante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TendenciaSemanal extends Component
{
    public $labels;
    public $data;
    public $labelsFull;

    public function __construct()
    {
        [$this->labels, $this->data, $this->labelsFull] = $this->getTendencia();
    }

    private function getTendencia(): array
    {
        $usuario = Auth::user();

        // Rango: últimos 7 días (incluye hoy)
        $start = Carbon::today()->subDays(6)->startOfDay();
        $end   = Carbon::today()->endOfDay();

        // Cache por usuario y por semana (5 minutos)
        $cacheKey = 'tendencia_semanal_' . ($usuario ? $usuario->id : 'guest') . '_' . $start->format('Ymd');

        $countsByDate = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($start, $end, $usuario) {
            $q = Votante::select(DB::raw('DATE(created_at) as fecha'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$start, $end]);

            // Filtrado por rol si corresponde
            if ($usuario) {
                if (method_exists($usuario, 'hasRole') && $usuario->hasRole('aspirante-alcaldia')) {
                    $q->where('alcalde_id', $usuario->id);
                } elseif (method_exists($usuario, 'hasRole') && $usuario->hasRole('aspirante-concejo')) {
                    $q->where('concejal_id', $usuario->id);
                } elseif (method_exists($usuario, 'hasRole') && $usuario->hasRole('lider')) {
                    $q->where('lider_id', $usuario->id);
                }
            }

            return $q->groupBy('fecha')->orderBy('fecha')->get()->pluck('total', 'fecha')->toArray();
        });

        // Construir labels y asegurar 0s donde falten días
        Carbon::setLocale('es');
        $labels = [];
        $data = [];
        $labelsFull = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $iso = $d->format('Y-m-d');
            $labels[] = $d->locale('es')->isoFormat('ddd'); // ej: lun, mar
            $labelsFull[] = $iso;
            $data[] = isset($countsByDate[$iso]) ? (int)$countsByDate[$iso] : 0;
        }

        return [$labels, $data, $labelsFull];
    }

    public function render(): View|Closure|string
    {
        return view('components.estadisticas.tendencia-semanal', [
            'labels' => $this->labels,
            'data' => $this->data,
            'labelsFull' => $this->labelsFull,
        ]);
    }
}
