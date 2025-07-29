<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LugarVotacion;
use App\Models\Mesa;

class LugarVotacionController extends Controller
{
    /**
     * Lista todos los lugares de votación
     */
    public function index()
    {
        $lugares = LugarVotacion::with('mesas')->orderBy('created_at', 'desc')->get();

        return view('permisos.crearPuntosVotacion', compact('lugares'));
    }

    /**
     * Muestra formulario para crear un nuevo lugar (modal lo maneja)
     */
    public function create()
    {
        $lugares = LugarVotacion::with('mesas')->orderBy('created_at', 'desc')->get();
        return view('permisos.crearPuntosVotacion', compact('lugares'));
    }

    /**
     * Guarda un nuevo lugar con sus mesas
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'direccion'   => 'nullable|string|max:500',
            'alcalde_id'  => 'nullable|integer|exists:users,id',
            'concejal_id' => 'nullable|integer|exists:users,id',
            'mesas'       => 'required|array|min:1',
            'mesas.*'     => 'required|string|max:50',
        ]);

        $lugar = LugarVotacion::create([
            'nombre'      => $request->nombre,
            'direccion'   => $request->direccion,
            'alcalde_id'  => $request->alcalde_id,
            'concejal_id' => $request->concejal_id,
        ]);

        // Procesar mesas
        foreach ($request->mesas as $mesa) {
            $mesasSeparadas = array_map('trim', explode(',', $mesa));
            foreach ($mesasSeparadas as $mesaNumero) {
                if (!empty($mesaNumero)) {
                    $lugar->mesas()->create(['numero' => $mesaNumero]);
                }
            }
        }

        return redirect()->route('lugares')->with('success', 'Punto de votación creado correctamente.');
    }

    /**
     * Editar un lugar (misma vista pero con datos)
     */
    public function edit(LugarVotacion $lugar)
    {
        $lugares = LugarVotacion::with('mesas')->get();
        return view('permisos.crearPuntosVotacion', compact('lugares', 'lugar'));
    }

    /**
     * Actualizar datos del lugar y manejar mesas
     */
    public function update(Request $request, LugarVotacion $lugar)
    {
        // Validación básica
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'direccion'   => 'nullable|string|max:500',
            'alcalde_id'  => 'nullable|integer|exists:users,id',
            'concejal_id' => 'nullable|integer|exists:users,id',
        ]);

        // Actualizar lugar
        $lugar->update($request->only(['nombre', 'direccion', 'alcalde_id', 'concejal_id']));

        /**
         * 1. Eliminar mesas seleccionadas
         */
        if ($request->has('mesas_eliminar')) {
            foreach ($request->mesas_eliminar as $mesaId) {
                $mesa = $lugar->mesas()->find($mesaId);
                if ($mesa) {
                    $mesa->delete();
                }
            }
        }

        /**
         * 2. Agregar mesas nuevas
         */
        if ($request->has('mesas_nuevas')) {
            foreach ($request->mesas_nuevas as $mesaNueva) {
                $mesasSeparadas = array_map('trim', explode(',', $mesaNueva));
                foreach ($mesasSeparadas as $numero) {
                    if (!empty($numero)) {
                        $lugar->mesas()->create(['numero' => $numero]);
                    }
                }
            }
        }

        return redirect()->route('lugares')->with('success', 'Punto de votación actualizado correctamente.');
    }

    /**
     * Eliminar un lugar y sus mesas asociadas
     */
    public function destroy(LugarVotacion $lugar)
    {
        $lugar->delete();

        return redirect()->route('lugares')->with('success', 'Punto de votación eliminado correctamente.');
    }
}
