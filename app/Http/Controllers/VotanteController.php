<?php

namespace App\Http\Controllers;

use App\Models\Votante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotanteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Mostrar todos los votantes del líder autenticado
    public function index()
    {
       $votantes = Votante::where('user_id', Auth::id())->get();
    return view('permisos.ingresarVotantes', compact('votantes'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('votantes.create');
    }

    // Guardar nuevo votante
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:votantes,cedula',
            'mesa' => 'nullable|string|max:50',
            'donacion' => 'nullable|numeric|min:0',
        ]);

        Votante::create([
            'nombre' => $request->nombre,
            'cedula' => $request->cedula,
            'mesa' => $request->mesa,
            'donacion' => $request->donacion,
            'user_id' => Auth::id(), // El líder que registra el votante
        ]);

        return redirect()->route('votantes.index')->with('success', 'Votante registrado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(Votante $votante)
    {
        // Solo puede editar si es el dueño
        if ($votante->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('votantes.edit', compact('votante'));
    }

    // Actualizar votante
    public function update(Request $request, Votante $votante)
    {
        if ($votante->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:votantes,cedula,' . $votante->id,
            'mesa' => 'nullable|string|max:50',
            'donacion' => 'nullable|numeric|min:0',
        ]);

        $votante->update([
            'nombre' => $request->nombre,
            'cedula' => $request->cedula,
            'mesa' => $request->mesa,
            'donacion' => $request->donacion,
        ]);

        return redirect()->route('votantes.index')->with('success', 'Votante actualizado correctamente.');
    }

    // Eliminar votante
    public function destroy(Votante $votante)
    {
        if ($votante->user_id !== Auth::id()) {
            abort(403);
        }

        $votante->delete();
        return redirect()->route('votantes.index')->with('success', 'Votante eliminado.');
    }

    // Consultar si una cédula ya está registrada (para búsqueda rápida)
    public function buscarPorCedula(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string',
        ]);

        $votante = Votante::where('cedula', $request->cedula)->first();

        if ($votante) {
            return response()->json([
                'existe' => true,
                'nombre' => $votante->nombre,
                'registrado_por' => $votante->user->name ?? 'Desconocido',
            ]);
        }

        return response()->json(['existe' => false]);
    }
}
