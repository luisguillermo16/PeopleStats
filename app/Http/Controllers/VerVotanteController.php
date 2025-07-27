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

    // Verificar si es aspirante a concejo o a alcaldía
    if (!$user->hasRole('aspirante-concejo') && !$user->hasRole('aspirante-alcaldia')) {
        abort(403, 'Acceso no autorizado');
    }

    // Filtrado dinámico según el rol
    $query = Votante::with(['lider', 'concejal']);

    if ($user->hasRole('aspirante-alcaldia')) {
        $query->where('alcalde_id', $user->id);
    } elseif ($user->hasRole('aspirante-concejo')) {
        $query->where('concejal_id', $user->id);
    }

    // Filtros adicionales
    if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where(function ($q) use ($search) {
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

    $votantes = $query->paginate(5)->appends($request->query());

    // Estadísticas y filtros
    $filtroCampo = $user->hasRole('aspirante-alcaldia') ? 'alcalde_id' : 'concejal_id';

    $totalVotantes = Votante::where($filtroCampo, $user->id)->count();
    $totalMesas = Votante::where($filtroCampo, $user->id)->distinct('mesa')->count('mesa');
    $totalLideres = Votante::where($filtroCampo, $user->id)->whereNotNull('lider_id')->distinct()->count('lider_id');
    $totalConcejales = Votante::where($filtroCampo, $user->id)->whereNotNull('concejal_id')->distinct()->count('concejal_id');

    $lideresIds = Votante::where($filtroCampo, $user->id)->whereNotNull('lider_id')->distinct()->pluck('lider_id')->toArray();
    $lideres = User::whereIn('id', $lideresIds)->select('id', 'name')->get();

    $concejalesIds = Votante::where($filtroCampo, $user->id)->whereNotNull('concejal_id')->distinct()->pluck('concejal_id')->toArray();
    $concejales = User::whereIn('id', $concejalesIds)->select('id', 'name')->get();

    return view('permisos.verVotantes', compact(
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