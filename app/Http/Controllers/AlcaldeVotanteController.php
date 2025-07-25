<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Votante;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlcaldeVotanteController extends Controller
{
   public function index(Request $request)
{
    $user = auth()->user();

    // Verificar rol
    if (!$user->hasRole('aspirante-alcaldia')) {
        abort(403, 'Acceso no autorizado');
    }

    // Base query para paginación y filtros
    $query = Votante::where('alcalde_id', $user->id)
                    ->with(['lider', 'concejal']);

    // Aplicar filtros
    if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where(function($q) use ($search) {
            $q->where('nombre', 'LIKE', "%{$search}%")
              ->orWhere('cedula', 'LIKE', "%{$search}%");
        });
    }

    if ($request->filled('mesa')) {
        $query->where('mesa', $request->get('mesa'));
    }

    if ($request->filled('lider')) {
        $query->where('lider_id', $request->get('lider'));
    }

    if ($request->filled('concejal')) {
        $query->where('concejal_id', $request->get('concejal'));
    }

    // Paginación
    $votantes = $query->paginate(5)->appends($request->query());

    // -----------------------------
    // CONSULTAS PARA ESTADÍSTICAS
    // -----------------------------

    // Total votantes
    $totalVotantes = Votante::where('alcalde_id', $user->id)->count();

    // Total concejales únicos
    $totalConcejales = Votante::where('alcalde_id', $user->id)
        ->whereNotNull('concejal_id')
        ->distinct('concejal_id')
        ->count('concejal_id');

    // Total mesas únicas
    $totalMesas = Votante::where('alcalde_id', $user->id)
        ->distinct('mesa')
        ->count('mesa');

    // Total líderes únicos
    $totalLideres = Votante::where('alcalde_id', $user->id)
        ->whereNotNull('lider_id')
        ->distinct('lider_id')
        ->count('lider_id');

    // Listas para filtros
    $lideresIds = Votante::where('alcalde_id', $user->id)
        ->whereNotNull('lider_id')
        ->distinct()
        ->pluck('lider_id')
        ->toArray();

    $lideres = User::whereIn('id', $lideresIds)
        ->select('id', 'name')
        ->get();

    $concejalesIds = Votante::where('alcalde_id', $user->id)
        ->whereNotNull('concejal_id')
        ->distinct()
        ->pluck('concejal_id')
        ->toArray();

    $concejales = User::whereIn('id', $concejalesIds)
        ->select('id', 'name')
        ->get();

    return view('userAlcalde.votantesAlcalde', compact(
        'votantes',
        'lideres',
        'concejales',
        'totalVotantes',
        'totalConcejales',
        'totalMesas',
        'totalLideres'
    ));
}
}