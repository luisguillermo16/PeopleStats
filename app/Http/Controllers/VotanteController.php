<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Votante;
use App\Models\LugarVotacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VotantesImport;

class VotanteController extends Controller
{
    // =============================
    // MÉTODOS PRIVADOS DE APOYO
    // =============================

    private function getLider()
    {
        return User::role('lider')->where('id', Auth::id())->first();
    }

    private function getConcejales($alcaldeId)
    {
        return User::role('aspirante-concejo')->where('alcalde_id', $alcaldeId)->get();
    }

    /**
     * Filtra lugares de votación según el líder actual
     * El líder solo puede ver lugares creados por sus superiores directos
     */
    private function getLugaresFiltrados($lider)
    {
        $query = LugarVotacion::with('mesas');
        
        // CASO 1: Líder ligado a un concejal específico
        // Debe ver lugares creados por ESE concejal
        if (!is_null($lider->concejal_id)) {
            $query->where('concejal_id', $lider->concejal_id);
        }
        // CASO 2: Líder ligado solo a un alcalde (sin concejal específico)  
        // Debe ver lugares creados por ESE alcalde
        elseif (!is_null($lider->alcalde_id) && is_null($lider->concejal_id)) {
            $query->where('alcalde_id', $lider->alcalde_id);
        }
        // CASO 3: Líder ligado tanto a alcalde como a concejal
        // Debe ver lugares creados por AMBOS (su concejal Y su alcalde)
        elseif (!is_null($lider->alcalde_id) && !is_null($lider->concejal_id)) {
            $query->where(function($q) use ($lider) {
                $q->where('concejal_id', $lider->concejal_id)
                  ->orWhere('alcalde_id', $lider->alcalde_id);
            });
        }
        // CASO 4: Líder sin ligaciones - no puede ver ningún lugar
        else {
            $query->whereRaw('1 = 0'); // No devuelve resultados
        }
        
        return $query->orderBy('nombre')
            ->get()
            ->map(function ($lugar) {
                return [
                    'id' => $lugar->id,
                    'nombre' => $lugar->nombre,
                    'alcalde_creador' => $lugar->alcalde_id,
                    'concejal_creador' => $lugar->concejal_id,
                    'mesas' => $lugar->mesas->map(function ($mesa) {
                        return [
                            'id' => $mesa->id,
                            'numero' => $mesa->numero
                        ];
                    })->toArray(),
                ];
            });
    }

    /**
     * MÉTODO ALTERNATIVO: Filtrado más explícito y con logs para debug
     */
    private function getLugaresFiltradosConDebug($lider)
    {
        \Log::info('=== FILTRADO DE LUGARES PARA LÍDER ===', [
            'lider_id' => $lider->id,
            'alcalde_id' => $lider->alcalde_id,
            'concejal_id' => $lider->concejal_id
        ]);

        $query = LugarVotacion::with('mesas');
        
        if (!is_null($lider->concejal_id)) {
            // Líder ligado a concejal específico - solo ve lugares de ESE concejal
            $query->where('concejal_id', $lider->concejal_id);
            \Log::info('🎯 Filtrado: Solo lugares creados por concejal_id = ' . $lider->concejal_id);
            
        } elseif (!is_null($lider->alcalde_id) && is_null($lider->concejal_id)) {
            // Líder ligado solo a alcalde - solo ve lugares de ESE alcalde
            $query->where('alcalde_id', $lider->alcalde_id);
            \Log::info('🎯 Filtrado: Solo lugares creados por alcalde_id = ' . $lider->alcalde_id);
            
        } elseif (!is_null($lider->alcalde_id) && !is_null($lider->concejal_id)) {
            // Líder ligado a AMBOS - ve lugares de su concejal Y su alcalde
            $query->where(function($q) use ($lider) {
                $q->where('concejal_id', $lider->concejal_id)
                  ->orWhere('alcalde_id', $lider->alcalde_id);
            });
            \Log::info('🎯 Filtrado: Lugares de concejal_id = ' . $lider->concejal_id . ' O alcalde_id = ' . $lider->alcalde_id);
            
        } else {
            // Líder sin ligaciones - no puede ver ningún lugar
            $query->whereRaw('1 = 0');
            \Log::warning('⚠️ Líder sin alcalde_id ni concejal_id - sin lugares disponibles', ['lider_id' => $lider->id]);
        }
        
        $lugares = $query->orderBy('nombre')->get();
        
        \Log::info('📊 RESULTADO: ' . $lugares->count() . ' lugares encontrados');
        
        foreach ($lugares as $lugar) {
            \Log::info('📍 Lugar encontrado:', [
                'id' => $lugar->id,
                'nombre' => $lugar->nombre,
                'creado_por_alcalde' => $lugar->alcalde_id,
                'creado_por_concejal' => $lugar->concejal_id,
                'mesas_count' => $lugar->mesas->count()
            ]);
        }
        
        return $lugares->map(function ($lugar) {
            return [
                'id' => $lugar->id,
                'nombre' => $lugar->nombre,
                'creado_por_alcalde' => $lugar->alcalde_id,
                'creado_por_concejal' => $lugar->concejal_id,
                'mesas' => $lugar->mesas->map(function ($mesa) {
                    return [
                        'id' => $mesa->id,
                        'numero' => $mesa->numero
                    ];
                })->toArray(),
            ];
        });
    }

    // =============================
    // FORMULARIO DE CREACIÓN
    // =============================
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

        $lugares = $this->getLugaresFiltrados($lider);

        return view('votantes.create', compact('lider', 'concejalOpciones', 'lugares'));
    }

    // =============================
    // GUARDAR NUEVO VOTANTE
    // =============================
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
            'lugares_votacion_id' => 'required|exists:lugar_votacions,id', // Corregido el nombre de la tabla
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

        $votante = new Votante($request->only('nombre', 'cedula', 'telefono', 'mesa', 'lugares_votacion_id'));
        $votante->lider_id = $lider->id;

        // Lógica para asignar alcalde/concejal
        if ($lider->concejal_id) {
            $votante->concejal_id = $lider->concejal_id;
            $votante->alcalde_id = ($request->tambien_vota_alcalde == '1' && $lider->alcalde_id) ? $lider->alcalde_id : null;
        } elseif ($lider->alcalde_id) {
            $votante->alcalde_id = $lider->alcalde_id;
            if ($request->filled('concejal_id')) {
                $votante->concejal_id = $request->concejal_id;
            }
        }

        $votante->save();

        return redirect()->route('ingresarVotantes')->with('success', 'Votante registrado correctamente.');
    }

    // =============================
    // LISTAR VOTANTES
    // =============================
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

        $lugares = $this->getLugaresFiltrados($lider);

        return view('permisos.ingresarVotantes', compact('votantes', 'lider', 'concejalOpciones', 'lugares'));
    }

    // =============================
    // EDITAR VOTANTE
    // =============================
    public function edit(Votante $votante)
    {
        $lider = $this->getLider();

        if (!$lider || $votante->lider_id !== $lider->id) {
            return redirect()->route('votantes.index')->with('error', 'No autorizado para editar este votante.');
        }

        $votantes = Votante::where('lider_id', $lider->id)->paginate(10);
        $concejalOpciones = $this->getConcejales($lider->alcalde_id ?? null);
        $lugares = $this->getLugaresFiltrados($lider);

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

    // =============================
    // ACTUALIZAR VOTANTE
    // =============================
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
            'lugares_votacion_id' => 'required|exists:lugar_votacions,id', // Corregido el nombre de la tabla
        ];

        if ($lider->concejal_id) {
            $rules['tambien_vota_alcalde'] = 'required|in:1,0';
        } elseif ($lider->alcalde_id) {
            $rules['concejal_id'] = 'nullable|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'cedula.unique' => 'Esta cédula ya ha sido registrada.',
            'concejal_id.exists' => 'El concejal seleccionado no es válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('editModalId', $votante->id);
        }

        $votante->fill($request->only('nombre', 'cedula', 'telefono', 'mesa', 'lugares_votacion_id'));

        // Lógica para actualizar alcalde/concejal
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

    // =============================
    // ELIMINAR VOTANTE
    // =============================
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

    // =============================
    // IMPORTAR EXCEL
    // =============================
    public function import(Request $request)
    {
        $lider = $this->getLider();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $import = new VotantesImport($lider);
            Excel::import($import, $request->file('excel_file'));

            $mensaje = "{$import->importados} votantes importados correctamente.";
            if ($import->saltados > 0) {
                $mensaje .= " {$import->saltados} registros fueron ignorados por cédulas duplicadas o lugares inválidos.";
            }

            return redirect()->route('ingresarVotantes')->with('success', $mensaje);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    // =============================
    // MÉTODO DE DEBUG
    // =============================
    public function debug()
    {
        $lider = $this->getLider();
        
        if (!$lider) {
            return response()->json(['error' => 'Líder no encontrado']);
        }

        $lugares = $this->getLugaresFiltradosConDebug($lider);
        
        return response()->json([
            'lider' => [
                'id' => $lider->id,
                'alcalde_id' => $lider->alcalde_id,
                'concejal_id' => $lider->concejal_id,
            ],
            'lugares_count' => count($lugares),
            'lugares' => $lugares
        ]);
    }
}