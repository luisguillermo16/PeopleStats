<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Votante;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerVotanteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('aspirante-concejo') && !$user->hasRole('aspirante-alcaldia')) {
            abort(403, 'Acceso no autorizado');
        }

        $query = Votante::with(['lider', 'concejal', 'mesa']);

        if ($user->hasRole('aspirante-alcaldia')) {
            $query->where('alcalde_id', $user->id);
        } elseif ($user->hasRole('aspirante-concejo')) {
            $query->where('concejal_id', $user->id);
        }

        // Buscador general
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('cedula', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por mesa (por número, no por ID)
        if ($request->filled('mesa')) {
            $mesaNumero = $request->get('mesa');
            $query->whereHas('mesa', function ($q) use ($mesaNumero) {
                $q->where('numero', $mesaNumero);
            });
        }

        if ($request->filled('lider')) {
            $query->where('lider_id', $request->get('lider'));
        }

        if ($request->filled('concejal')) {
            $query->where('concejal_id', $request->get('concejal'));
        }

        // Obtener votantes con paginación
        $votantes = $query->paginate(5)->appends($request->query());

        // Determinar el campo de filtro según el rol del usuario
        $filtroCampo = $user->hasRole('aspirante-alcaldia') ? 'alcalde_id' : 'concejal_id';

        // ESTADÍSTICAS TOTALES (sin filtros)
        $totalVotantes = Votante::where($filtroCampo, $user->id)->count();
        $totalMesas = Votante::where($filtroCampo, $user->id)
            ->distinct('mesa_id')
            ->count('mesa_id');
        $totalLideres = Votante::where($filtroCampo, $user->id)
            ->whereNotNull('lider_id')->distinct()->count('lider_id');
        $totalConcejales = Votante::where($filtroCampo, $user->id)
            ->whereNotNull('concejal_id')->distinct()->count('concejal_id');

        // ESTADÍSTICAS FILTRADAS (según los filtros aplicados)
        $queryFiltrada = Votante::where($filtroCampo, $user->id);

        // Aplicar los mismos filtros para las estadísticas
        if ($request->filled('search')) {
            $search = $request->get('search');
            $queryFiltrada->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('cedula', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('mesa')) {
            $mesaNumero = $request->get('mesa');
            $queryFiltrada->whereHas('mesa', function ($q) use ($mesaNumero) {
                $q->where('numero', $mesaNumero);
            });
        }

        if ($request->filled('lider')) {
            $queryFiltrada->where('lider_id', $request->get('lider'));
        }

        if ($request->filled('concejal')) {
            $queryFiltrada->where('concejal_id', $request->get('concejal'));
        }

        // Obtener los votantes filtrados para calcular estadísticas
        $votantesFiltrados = $queryFiltrada->get();

        // Calcular estadísticas dinámicas
        $totalVotantesFiltrados = $votantesFiltrados->count();
        
        $totalMesasFiltradas = $votantesFiltrados
            ->whereNotNull('mesa_id')
            ->pluck('mesa_id')
            ->unique()
            ->count();
            
        $totalLideresFiltrados = $votantesFiltrados
            ->whereNotNull('lider_id')
            ->pluck('lider_id')
            ->unique()
            ->count();
            
        $totalConcejalesFiltrados = $votantesFiltrados
            ->whereNotNull('concejal_id')
            ->pluck('concejal_id')
            ->unique()
            ->count();

        // Obtener líderes y concejales para los selectores de filtro
        $lideresIds = Votante::where($filtroCampo, $user->id)
            ->whereNotNull('lider_id')->distinct()->pluck('lider_id')->toArray();
        $lideres = User::whereIn('id', $lideresIds)->select('id', 'name')->get();

        $concejalesIds = Votante::where($filtroCampo, $user->id)
            ->whereNotNull('concejal_id')->distinct()->pluck('concejal_id')->toArray();
        $concejales = User::whereIn('id', $concejalesIds)->select('id', 'name')->get();

        return view('permisos.verVotantes', compact(
            'votantes',
            'lideres',
            'concejales',
            // Estadísticas totales
            'totalVotantes',
            'totalConcejales',
            'totalMesas',
            'totalLideres',
            // Estadísticas filtradas
            'totalVotantesFiltrados',
            'totalConcejalesFiltrados',
            'totalMesasFiltradas',
            'totalLideresFiltrados'
        ));
    }
}