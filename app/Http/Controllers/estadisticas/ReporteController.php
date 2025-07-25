<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Votante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // Usuario autenticado (puede ser alcalde, concejal, o líder)
        $usuario = Auth::user();
        
        // Variables dinámicas para el resumen general
        $totalVotantes = $this->getTotalVotantes($usuario);
        $lideresActivos = $this->getLideresActivos($usuario);
        $totalConcejales = $this->getTotalConcejales($usuario);
        $totalMesas = $this->getTotalMesas($usuario);
        
        // Variables para filtros dinámicos
        $periodos = [
            'all' => 'Todo el tiempo',
            'month' => 'Último mes',
            'week' => 'Última semana'
        ];
        
        $tiposReporte = [
            'completo' => 'Completo',
            'lideres' => 'Solo Líderes',
            'concejales' => 'Solo Concejales',
            'mesas' => 'Por Mesas'
        ];
        
        // Aplicar filtros si existen
        $filtros = [
            'periodo' => $request->get('periodo', 'all'),
            'tipo' => $request->get('tipo', 'completo')
        ];
        
        // Variables para las pestañas
        $reporteLideres = $this->getReporteLideres($usuario, $filtros);
        $reporteConcejales = $this->getReporteConcejales($usuario, $filtros);
        $reporteMesas = $this->getReporteMesas($usuario, $filtros);
        
        // Variables para gráficos
        $tendenciaRegistro = $this->getTendenciaRegistro($usuario, $filtros);
        $distribucionPorRol = $this->getDistribucionPorRol($usuario);
        
        return view('reportes.index', compact(
            'totalVotantes',
            'lideresActivos', 
            'totalConcejales',
            'totalMesas',
            'periodos',
            'tiposReporte',
            'filtros',
            'reporteLideres',
            'reporteConcejales',
            'reporteMesas',
            'tendenciaRegistro',
            'distribucionPorRol'
        ));
    }
    
    private function getTotalVotantes($usuario)
    {
        $query = Votante::query();
        
        if ($usuario->hasRole('alcalde')) {
            $query->where('alcalde_id', $usuario->id);
        } elseif ($usuario->hasRole('aspirante-concejo')) {
            $query->where('concejal_id', $usuario->id);
        } elseif ($usuario->hasRole('lider')) {
            $query->where('lider_id', $usuario->id);
        }
        
        return $query->count();
    }
    
    private function getLideresActivos($usuario)
    {
        $query = User::role('lider');
        
        if ($usuario->hasRole('alcalde')) {
            $query->where('alcalde_id', $usuario->id);
        } elseif ($usuario->hasRole('aspirante-concejo')) {
            $query->where('concejal_id', $usuario->id);
        } elseif ($usuario->hasRole('lider')) {
            return 1; // El líder se cuenta a sí mismo
        }
        
        return $query->whereHas('votantes')->count();
    }
    
    private function getTotalConcejales($usuario)
    {
        if ($usuario->hasRole('alcalde')) {
            return User::role('aspirante-concejo')
                ->where('alcalde_id', $usuario->id)
                ->count();
        } elseif ($usuario->hasRole('aspirante-concejo')) {
            return 1;
        }
        
        return 0;
    }
    
    private function getTotalMesas($usuario)
    {
        $query = Votante::query();
        
        if ($usuario->hasRole('alcalde')) {
            $query->where('alcalde_id', $usuario->id);
        } elseif ($usuario->hasRole('aspirante-concejo')) {
            $query->where('concejal_id', $usuario->id);
        } elseif ($usuario->hasRole('lider')) {
            $query->where('lider_id', $usuario->id);
        }
        
        return $query->distinct('mesa')->count('mesa');
    }
    
    private function getReporteLideres($usuario, $filtros)
    {
        $query = User::role('lider')
            ->withCount(['votantes' => function($q) use ($filtros) {
                if ($filtros['periodo'] === 'month') {
                    $q->where('created_at', '>=', Carbon::now()->subMonth());
                } elseif ($filtros['periodo'] === 'week') {
                    $q->where('created_at', '>=', Carbon::now()->subWeek());
                }
            }])
            ->withCount(['votantes as votantes_con_telefono_count' => function($q) use ($filtros) {
                $q->whereNotNull('telefono');
                if ($filtros['periodo'] === 'month') {
                    $q->where('created_at', '>=', Carbon::now()->subMonth());
                } elseif ($filtros['periodo'] === 'week') {
                    $q->where('created_at', '>=', Carbon::now()->subWeek());
                }
            }])
            ->with(['votantes' => function($q) {
                $q->latest()->limit(1);
            }]);
        
        if ($usuario->hasRole('alcalde')) {
            $query->where('alcalde_id', $usuario->id);
        } elseif ($usuario->hasRole('aspirante-concejo')) {
            $query->where('concejal_id', $usuario->id);
        } elseif ($usuario->hasRole('lider')) {
            $query->where('id', $usuario->id);
        }
        
        $lideres = $query->get()->map(function($lider) {
            $ultimoRegistro = $lider->votantes->first();
            
            return [
                'nombre' => $lider->name,
                'votantes_count' => $lider->votantes_count,
                'votantes_con_telefono' => $lider->votantes_con_telefono_count,
                'ultimo_registro' => $ultimoRegistro ? $ultimoRegistro->created_at->format('Y-m-d') : 'N/A',
                'porcentaje' => $this->calcularPorcentajeLider($lider->votantes_count)
            ];
        });
        
        return $lideres->sortByDesc('votantes_count');
    }
    
    private function getReporteConcejales($usuario, $filtros)
    {
        if (!$usuario->hasRole('alcalde')) {
            return collect();
        }
        
        $query = User::role('aspirante-concejo')
            ->where('alcalde_id', $usuario->id)
            ->withCount(['votantes' => function($q) use ($filtros) {
                if ($filtros['periodo'] === 'month') {
                    $q->where('created_at', '>=', Carbon::now()->subMonth());
                } elseif ($filtros['periodo'] === 'week') {
                    $q->where('created_at', '>=', Carbon::now()->subWeek());
                }
            }])
            ->withCount(['lideres' => function($q) {
                $q->whereHas('votantes');
            }]);
        
        return $query->get()->map(function($concejal) {
            return [
                'nombre' => $concejal->name,
                'votantes_count' => $concejal->votantes_count,
                'lideres_count' => $concejal->lideres_count,
                'porcentaje' => $this->calcularPorcentajeConcejal($concejal->votantes_count)
            ];
        })->sortByDesc('votantes_count');
    }
    
    private function getReporteMesas($usuario, $filtros)
    {
        $query = Votante::select('mesa', DB::raw('count(*) as total_votantes'))
            ->groupBy('mesa');
        
        if ($usuario->hasRole('alcalde')) {
            $query->where('alcalde_id', $usuario->id);
        } elseif ($usuario->hasRole('aspirante-concejo')) {
            $query->where('concejal_id', $usuario->id);
        } elseif ($usuario->hasRole('lider')) {
            $query->where('lider_id', $usuario->id);
        }
        
        if ($filtros['periodo'] === 'month') {
            $query->where('created_at', '>=', Carbon::now()->subMonth());
        } elseif ($filtros['periodo'] === 'week') {
            $query->where('created_at', '>=', Carbon::now()->subWeek());
        }
        
        return $query->orderByDesc('total_votantes')->get();
    }
    
    private function getTendenciaRegistro($usuario, $filtros)
    {
        $dias = 7; // Últimos 7 días
        $fechas = [];
        $datos = [];
        
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $fechas[] = $fecha->format('D');
            
            $query = Votante::whereDate('created_at', $fecha->format('Y-m-d'));
            
            if ($usuario->hasRole('alcalde')) {
                $query->where('alcalde_id', $usuario->id);
            } elseif ($usuario->hasRole('aspirante-concejo')) {
                $query->where('concejal_id', $usuario->id);
            } elseif ($usuario->hasRole('lider')) {
                $query->where('lider_id', $usuario->id);
            }
            
            $datos[] = $query->count();
        }
        
        return [
            'labels' => $fechas,
            'data' => $datos
        ];
    }
    
    private function getDistribucionPorRol($usuario)
    {
        $conLider = Votante::whereNotNull('lider_id');
        $conConcejal = Votante::whereNotNull('concejal_id');
        $sinAsignar = Votante::whereNull('lider_id')->whereNull('concejal_id');
        
        if ($usuario->hasRole('alcalde')) {
            $conLider->where('alcalde_id', $usuario->id);
            $conConcejal->where('alcalde_id', $usuario->id);
            $sinAsignar->where('alcalde_id', $usuario->id);
        } elseif ($usuario->hasRole('aspirante-concejo')) {
            $conLider->where('concejal_id', $usuario->id);
            $conConcejal->where('concejal_id', $usuario->id);
            $sinAsignar->where('concejal_id', $usuario->id);
        } elseif ($usuario->hasRole('lider')) {
            $conLider->where('lider_id', $usuario->id);
            $conConcejal->where('lider_id', $usuario->id);
            $sinAsignar->where('lider_id', $usuario->id);
        }
        
        return [
            'con_lider' => $conLider->count(),
            'con_concejal' => $conConcejal->count(),
            'sin_asignar' => $sinAsignar->count()
        ];
    }
    
    private function calcularPorcentajeLider($votantes)
    {
        $totalGeneral = Votante::count();
        return $totalGeneral > 0 ? round(($votantes / $totalGeneral) * 100, 1) : 0;
    }
    
    private function calcularPorcentajeConcejal($votantes)
    {
        $totalGeneral = Votante::count();
        return $totalGeneral > 0 ? round(($votantes / $totalGeneral) * 100, 1) : 0;
    }
}