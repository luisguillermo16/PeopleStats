<?php

namespace App\Http\Controllers;

use App\Models\Votante;
use App\Models\Lider;
use App\Models\Concejal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotanteController extends Controller
{
    public function create()
    {
        $lider = Lider::where('user_id', Auth::id())->first();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $concejalOpciones = [];

        if (is_null($lider->concejal_id) && !is_null($lider->alcalde_id)) {
            $concejalOpciones = Concejal::where('alcalde_id', $lider->alcalde_id)->get();
        }

        return view('votantes.create', compact('lider', 'concejalOpciones'));
    }

    public function store(Request $request)
    {
        $lider = Lider::where('user_id', Auth::id())->first();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario autenticado.');
        }

        $rules = [
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:votantes,cedula',
            'telefono' => 'required|string|max:20',
        ];

        if (!is_null($lider->concejal_id)) {
            $rules['tambien_vota_alcalde'] = 'required|in:1,0';
        } elseif (!is_null($lider->alcalde_id)) {
            $rules['concejal_id'] = 'nullable|exists:concejales,id';
        }

        $request->validate($rules, [
            'nombre.required' => 'El nombre es obligatorio.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.unique' => 'Esta cédula ya ha sido registrada.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'concejal_id.exists' => 'El concejal seleccionado no es válido.',
        ]);

        $votante = new Votante();
        $votante->nombre = $request->nombre;
        $votante->cedula = $request->cedula;
        $votante->telefono = $request->telefono;
        $votante->user_id = Auth::id();
        $votante->lider_id = $lider->user_id;

        // 🔹 Jerarquía: líder creado por un concejal
        if (!is_null($lider->concejal_id)) {

            // ✅ Validar que ese concejal exista antes de asignarlo
            $concejal = Concejal::find($lider->concejal_id);
            if (!$concejal) {
                return redirect()->back()->with('error', 'El concejal vinculado al líder no existe.');
            }

            $votante->concejal_id = $concejal->id;

            if ($request->tambien_vota_alcalde == '1' && !is_null($lider->alcalde_id)) {
                $votante->alcalde_id = $lider->alcalde_id;
            }
        }

        // 🔹 Jerarquía: líder creado por un alcalde
        elseif (!is_null($lider->alcalde_id)) {
            $votante->alcalde_id = $lider->alcalde_id;

            if ($request->filled('concejal_id')) {
                // ✅ Validar que concejal_id enviado desde formulario también existe
                $concejal = Concejal::find($request->concejal_id);
                if (!$concejal) {
                    return redirect()->back()->with('error', 'El concejal seleccionado no existe.');
                }

                $votante->concejal_id = $concejal->id;
            }
        }

        $votante->save();

        return redirect()->route('ingresarVotantes')->with('success', 'Votante registrado correctamente.');
    }

    public function index()
    {
        $lider = Lider::where('user_id', Auth::id())->first();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $votantes = Votante::where('lider_id', $lider->user_id)->get();
        $concejalOpciones = [];

        if (is_null($lider->concejal_id) && !is_null($lider->alcalde_id)) {
            $concejalOpciones = Concejal::where('alcalde_id', $lider->alcalde_id)->get();
        }

        return view('permisos.ingresarVotantes', compact('votantes', 'lider', 'concejalOpciones'));
    }
}
