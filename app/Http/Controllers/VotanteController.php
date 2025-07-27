<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Votante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotanteController extends Controller
{
    public function create()
    {
        $lider = User::role('lider')->where('id', Auth::id())->first();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $concejalOpciones = [];

        if (is_null($lider->concejal_id) && !is_null($lider->alcalde_id)) {
            $concejalOpciones = User::role('aspirante-concejo')
                ->where('alcalde_id', $lider->alcalde_id)
                ->get();
        }

        return view('votantes.create', compact('lider', 'concejalOpciones'));
    }

    public function store(Request $request)
    {
        $lider = User::role('lider')->where('id', Auth::id())->first();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario autenticado.');
        }

        $rules = [
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:votantes,cedula',
            'telefono' => 'required|string|max:20',
            'mesa' => 'required|string|max:255',
        ];

        if (!is_null($lider->concejal_id)) {
            $rules['tambien_vota_alcalde'] = 'required|in:1,0';
        } elseif (!is_null($lider->alcalde_id)) {
            $rules['concejal_id'] = 'nullable|exists:users,id';
        }

        $request->validate($rules, [
            'cedula.unique' => 'Esta cédula ya ha sido registrada.',
            'concejal_id.exists' => 'El concejal seleccionado no es válido.',
        ]);

        $votante = new Votante();
        $votante->nombre = $request->nombre;
        $votante->cedula = $request->cedula;
        $votante->telefono = $request->telefono;
        $votante->mesa = $request->mesa;
        $votante->lider_id = $lider->id;

        if (!is_null($lider->concejal_id)) {
            $votante->concejal_id = $lider->concejal_id;

            if ($request->tambien_vota_alcalde == '1' && !is_null($lider->alcalde_id)) {
                $votante->alcalde_id = $lider->alcalde_id;
            }
        } elseif (!is_null($lider->alcalde_id)) {
            $votante->alcalde_id = $lider->alcalde_id;

            if ($request->filled('concejal_id')) {
                $concejalUsuario = User::role('aspirante-concejo')->where('id', $request->concejal_id)->first();

                if (!$concejalUsuario) {
                    return redirect()->back()->with('error', 'El concejal seleccionado no es válido.');
                }

                $votante->concejal_id = $request->concejal_id;
            }
        }

        $votante->save();

        return redirect()->route('ingresarVotantes')->with('success', 'Votante registrado correctamente.');
    }

    public function index()
    {
        $lider = User::role('lider')->where('id', Auth::id())->first();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $votantes = Votante::where('lider_id', $lider->id)->paginate(10);
        $concejalOpciones = [];

        if (is_null($lider->concejal_id) && !is_null($lider->alcalde_id)) {
            $concejalOpciones = User::role('aspirante-concejo')
                ->where('alcalde_id', $lider->alcalde_id)
                ->get();
        }

        return view('permisos.ingresarVotantes', compact('votantes', 'lider', 'concejalOpciones'));
    }


    public function update(Request $request, $id)
    {
        $votante = Votante::findOrFail($id);
        $lider = User::role('lider')->where('id', Auth::id())->first();

        if (!$lider || $votante->lider_id !== $lider->id) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar este votante.');
        }

        $rules = [
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:votantes,cedula,' . $votante->id,
            'telefono' => 'required|string|max:20',
            'mesa' => 'required|string|max:255',
        ];

        if (!is_null($lider->concejal_id)) {
            $rules['tambien_vota_alcalde'] = 'required|in:1,0';
        } elseif (!is_null($lider->alcalde_id)) {
            $rules['concejal_id'] = 'nullable|exists:users,id';
        }

        $request->validate($rules);

        $votante->nombre = $request->nombre;
        $votante->cedula = $request->cedula;
        $votante->telefono = $request->telefono;
        $votante->mesa = $request->mesa;

        if (!is_null($lider->concejal_id)) {
            $votante->concejal_id = $lider->concejal_id;

            if ($request->tambien_vota_alcalde == '1' && !is_null($lider->alcalde_id)) {
                $votante->alcalde_id = $lider->alcalde_id;
            }
        } elseif (!is_null($lider->alcalde_id)) {
            $votante->alcalde_id = $lider->alcalde_id;

            if ($request->filled('concejal_id')) {
                $concejalUsuario = User::role('aspirante-concejo')->where('id', $request->concejal_id)->first();

                if (!$concejalUsuario) {
                    return redirect()->back()->with('error', 'El concejal seleccionado no es válido.');
                }

                $votante->concejal_id = $request->concejal_id;
            } else {
                $votante->concejal_id = null;
            }
        }

        $votante->save();

        return redirect()->route('ingresarVotantes')->with('success', 'Votante actualizado correctamente.');
    }

    public function destroy($id)
    {
        $votante = Votante::findOrFail($id);
        $lider = User::role('lider')->where('id', Auth::id())->first();

        if (!$lider || $votante->lider_id !== $lider->id) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este votante.');
        }

        $votante->delete();

        return redirect()->route('ingresarVotantes')->with('success', 'Votante eliminado correctamente.');
    }
    
public function edit(Votante $votante)
{
    $user = Auth::user();

    // Asegura que el votante pertenezca al líder actual
    if ($votante->lider_id !== $user->id) {
        return redirect()->route('votantes.index')->with('error', 'No autorizado para editar este votante.');
    }

    $lider = $user;

    // Obtiene los votantes de este líder
   $votantes = Votante::where('lider_id', $lider->id)->paginate(10);

    // ✅ Corrección: usar Spatie para contar por rol
    $totalConcejales = User::role('aspirante-concejo')->count();
    $totalLideres = User::role('lider')->count();

    // ✅ Opcional: obtener usuarios con el rol concejal para mostrar en el formulario
    $concejalOpciones = User::role('aspirante-concejo')->get();

    $totalVotantes = $votantes->count();
    $totalMesas = Votante::where('lider_id', $lider->id)->distinct('mesa')->count('mesa');

    return view('permisos.ingresarVotantes', compact(
        'votante',
        'lider',
        'concejalOpciones',
        'votantes',
        'totalVotantes',
        'totalConcejales',
        'totalMesas',
        'totalLideres'
    ));
}



}
