<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barrio;
use Illuminate\Support\Facades\Auth;

class BarrioController extends Controller
{
    /**
     * Mostrar la lista de barrios y el formulario para crear uno nuevo.
     */
    public function index()
    {
        $barrios = Barrio::where('alcalde_id', Auth::id())->latest()->get();

        return view('permisos.crearBarrios', compact('barrios'));
    }

    /**
     * Guardar un nuevo barrio.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:barrios,nombre',
        ]);

        Barrio::create([
            'nombre' => $request->nombre,
            'alcalde_id' => Auth::id(),
        ]);

        return redirect()->route('crearBarrios.store')->with('success', 'Barrio creado exitosamente.');
    }

    /**
     * Eliminar un barrio.
     */
   public function destroy(Barrio $barrio)
{
    

    if ($barrio->alcalde_id !== Auth::id()) {
        abort(403, 'No autorizado para eliminar este barrio.');
    }

    $barrio->delete();

  return redirect()->route('crearBarrios')->with('success', 'Barrio eliminado correctamente.');
}

}
