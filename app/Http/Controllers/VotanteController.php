<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Votante;
use App\Models\LugarVotacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VotanteController extends Controller
{
    private function getLider()
    {
        return User::role('lider')->where('id', Auth::id())->first();
    }

    private function getConcejales($alcaldeId)
    {
        return User::role('aspirante-concejo')->where('alcalde_id', $alcaldeId)->get();
    }

    private function getLugares()
    {
        return LugarVotacion::with('mesas')->orderBy('nombre')->get()->map(function ($lugar) {
            return [
                'id' => $lugar->id,
                'nombre' => $lugar->nombre,
                'mesas' => $lugar->mesas->pluck('numero')->unique()->values()->toArray(),
            ];
        });
    }

    public function create()
    {
        $lider = $this->getLider();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $concejalOpciones = [];

        if (is_null($lider->concejal_id) && $lider->alcalde_id) {
            $concejalOpciones = $this->getConcejales($lider->alcalde_id);
        }

        $lugares = $this->getLugares();

        return view('votantes.create', compact('lider', 'concejalOpciones', 'lugares'));
    }

    public function store(Request $request)
    {
        $lider = $this->getLider();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $rules = [
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:votantes,cedula',
            'telefono' => 'required|string|max:20',
            'mesa' => 'required|string|max:255',
            'lugar_votacion_id' => 'required|exists:lugares_votacion,id',
        ];

        if ($lider->concejal_id) {
            $rules['tambien_vota_alcalde'] = 'required|in:1,0';
        } elseif ($lider->alcalde_id) {
            $rules['concejal_id'] = 'nullable|exists:users,id';
        }

        $request->validate($rules, [
            'cedula.unique' => 'Esta cédula ya ha sido registrada.',
            'concejal_id.exists' => 'El concejal seleccionado no es válido.',
        ]);

        $votante = new Votante($request->only('nombre', 'cedula', 'telefono', 'mesa', 'lugar_votacion_id'));
        $votante->lider_id = $lider->id;

        if ($lider->concejal_id) {
            $votante->concejal_id = $lider->concejal_id;
            if ($request->tambien_vota_alcalde && $lider->alcalde_id) {
                $votante->alcalde_id = $lider->alcalde_id;
            }
        } elseif ($lider->alcalde_id) {
            $votante->alcalde_id = $lider->alcalde_id;
            if ($request->filled('concejal_id')) {
                $votante->concejal_id = $request->concejal_id;
            }
        }

        $votante->save();

        return redirect()->route('ingresarVotantes')->with('success', 'Votante registrado correctamente.');
    }

    public function index()
    {
        $lider = $this->getLider();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $votantes = Votante::where('lider_id', $lider->id)->paginate(10);
        $concejalOpciones = [];

        if (is_null($lider->concejal_id) && $lider->alcalde_id) {
            $concejalOpciones = $this->getConcejales($lider->alcalde_id);
        }

        $lugares = LugarVotacion::with('mesas')->orderBy('nombre')->get();

        return view('permisos.ingresarVotantes', compact('votantes', 'lider', 'concejalOpciones', 'lugares'));
    }

    public function edit(Votante $votante)
    {
        $lider = $this->getLider();

        if (!$lider || $votante->lider_id !== $lider->id) {
            return redirect()->route('votantes.index')->with('error', 'No autorizado para editar este votante.');
        }

        $votantes = Votante::where('lider_id', $lider->id)->paginate(10);
        $concejalOpciones = $this->getConcejales($lider->alcalde_id ?? null);
        $lugares = $this->getLugares();

        $totalVotantes = Votante::where('lider_id', $lider->id)->count();
        $totalMesas = Votante::where('lider_id', $lider->id)->distinct('mesa')->count('mesa');
        $totalConcejales = User::role('aspirante-concejo')->count();
        $totalLideres = User::role('lider')->count();

        return view('permisos.ingresarVotantes', compact(
            'votante',
            'lider',
            'votantes',
            'concejalOpciones',
            'lugares',
            'totalVotantes',
            'totalMesas',
            'totalConcejales',
            'totalLideres'
        ));
    }

    public function update(Request $request, $id)
{
    $votante = Votante::findOrFail($id);
    $lider = $this->getLider();

    if (!$lider || $votante->lider_id !== $lider->id) {
        return redirect()->back()->with('error', 'No tienes permiso para actualizar este votante.');
    }

    $rules = [
        'nombre' => 'required|string|max:255',
        'cedula' => 'required|string|max:20|unique:votantes,cedula,' . $votante->id,
        'telefono' => 'required|string|max:20',
        'mesa' => 'required|string|max:255',
        'lugar_votacion_id' => 'required|exists:lugares_votacion,id',
    ];

    if ($lider->concejal_id) {
        $rules['tambien_vota_alcalde'] = 'required|in:1,0';
    } elseif ($lider->alcalde_id) {
        $rules['concejal_id'] = 'nullable|exists:users,id';
    }

    // Validación manual
    $validator = Validator::make($request->all(), $rules, [
        'cedula.unique' => 'Esta cédula ya ha sido registrada.',
        'concejal_id.exists' => 'El concejal seleccionado no es válido.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('editModalId', $votante->id); // 👈 Forzamos reabrir el modal de edición
    }

    $votante->fill($request->only('nombre', 'cedula', 'telefono', 'mesa', 'lugar_votacion_id'));

    if ($lider->concejal_id) {
        $votante->concejal_id = $lider->concejal_id;
        $votante->alcalde_id = ($request->tambien_vota_alcalde == '1' && $lider->alcalde_id) ? $lider->alcalde_id : null;
    } elseif ($lider->alcalde_id) {
        $votante->alcalde_id = $lider->alcalde_id;
        $votante->concejal_id = $request->filled('concejal_id') ? $request->concejal_id : null;
    }

    $votante->save();

    return redirect()->route('ingresarVotantes')->with('success', 'Votante actualizado correctamente.');
}

    public function destroy($id)
    {
        $votante = Votante::findOrFail($id);
        $lider = $this->getLider();

        if (!$lider || $votante->lider_id !== $lider->id) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este votante.');
        }

        $votante->delete();

        return redirect()->route('ingresarVotantes')->with('success', 'Votante eliminado correctamente.');
    }
}
