<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LugarVotacion;
use App\Models\Mesa;

class LugarVotacionController extends Controller
{
    public function index()
    {
        $lugares = LugarVotacion::with('mesas')->orderBy('created_at', 'desc')->get();
        return view('permisos.crearPuntosVotacion', compact('lugares'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'alcalde_id' => 'nullable|integer|exists:users,id',
            'mesas' => 'required|array|min:1',
            'mesas.*' => 'required|string|max:50',
        ]);

        $lugar = LugarVotacion::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'alcalde_id' => $request->alcalde_id,
        ]);

        foreach ($request->mesas as $numero) {
            $lugar->mesas()->create(['numero' => trim($numero)]);
        }

        return redirect()->route('lugares')->with('success', 'Punto de votación creado correctamente.');
    }

    public function update(Request $request, LugarVotacion $lugar)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'mesas_existentes' => 'nullable|array',
            'mesas_existentes.*' => 'required|string|max:50',
            'mesas_nuevas' => 'nullable|array',
            'mesas_nuevas.*' => 'required|string|max:50',
        ]);

        $lugar->update([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
        ]);

        // Actualizar mesas existentes
        $idsMantener = [];
        if ($request->filled('mesas_existentes')) {
            foreach ($request->mesas_existentes as $id => $numero) {
                $mesa = Mesa::where('id', $id)->where('lugar_votacion_id', $lugar->id)->first();
                if ($mesa) {
                    $mesa->update(['numero' => $numero]);
                    $idsMantener[] = $mesa->id;
                }
            }
        }

        // Eliminar mesas que no están en el array recibido
        $lugar->mesas()->whereNotIn('id', $idsMantener)->delete();

        // Agregar nuevas mesas
        if ($request->filled('mesas_nuevas')) {
            foreach ($request->mesas_nuevas as $numero) {
                $lugar->mesas()->create(['numero' => trim($numero)]);
            }
        }

        return redirect()->route('lugares')->with('success', 'Punto de votación actualizado correctamente.');
    }

    public function destroy(LugarVotacion $lugar)
    {
        $lugar->delete();
        return redirect()->route('lugares')->with('success', 'Punto de votación eliminado correctamente.');
    }
}
