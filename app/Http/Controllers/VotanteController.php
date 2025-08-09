<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Votante;
use App\Models\LugarVotacion;
use App\Models\Barrio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VotantesImport;
use Illuminate\Support\Facades\Log;

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
     */
    private function getLugaresFiltrados($lider)
    {
        $query = LugarVotacion::with('mesas');

        if (!is_null($lider->alcalde_id) && !is_null($lider->concejal_id)) {
            $query->where(function($q) use ($lider) {
                $q->where('concejal_id', $lider->concejal_id)
                  ->orWhere('alcalde_id', $lider->alcalde_id);
            });
        } elseif (!is_null($lider->concejal_id)) {
            $query->where('concejal_id', $lider->concejal_id);
        } elseif (!is_null($lider->alcalde_id)) {
            $query->where('alcalde_id', $lider->alcalde_id);
        } else {
            $query->whereRaw('1 = 0');
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
     * Filtra barrios según el alcalde del líder
     */
    private function getBarriosFiltrados($lider)
    {
        $alcaldeId = null;

        if ($lider->concejal_id) {
            $concejal = User::find($lider->concejal_id);
            $alcaldeId = $concejal ? $concejal->alcalde_id : null;
        }

        if (!$alcaldeId && $lider->alcalde_id) {
            $alcaldeId = $lider->alcalde_id;
        }

        if (!$alcaldeId) {
            return collect();
        }

        return Barrio::where('alcalde_id', $alcaldeId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
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
        $barrios = $this->getBarriosFiltrados($lider);

        return view('votantes.create', compact('lider', 'concejalOpciones', 'lugares', 'barrios'));
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
            'cedula' => [
                'required',
                'string',
                'max:20',
                Rule::unique('votantes')->where(function ($query) use ($lider) {
                    $query->where('lider_id', $lider->id);
                }),
            ],
            'telefono' => 'required|string|max:20',
            'mesa' => 'required|string|max:255',
            'lugar_votacion_id' => 'required|exists:lugares_votacion,id',
            'barrio_id' => 'required|exists:barrios,id',
        ];

        if ($lider->concejal_id) {
            $rules['tambien_vota_alcalde'] = 'required|in:1,0';
        } elseif ($lider->alcalde_id) {
            $rules['concejal_id'] = 'nullable|exists:users,id';
        }

        $request->validate($rules, [
            'cedula.unique' => 'Esta cédula ya ha sido registrada por este líder.',
            'concejal_id.exists' => 'El concejal seleccionado no es válido.',
        ]);

        $votante = new Votante($request->only('nombre', 'cedula', 'telefono', 'mesa', 'lugar_votacion_id', 'barrio_id'));
        $votante->lider_id = $lider->id;

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
    public function index(Request $request)
    {
        $lider = $this->getLider();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        // Configuración de paginación configurable
        $perPage = $request->get('per_page', 10); // Por defecto 25, máximo 100
        $perPage = min(max($perPage, 10), 100); // Limitar entre 10 y 100
        
        // Búsqueda con filtros
        $query = Votante::where('lider_id', $lider->id);
        
        // Filtro por nombre
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }
        
        // Filtro por cédula
        if ($request->filled('cedula')) {
            $query->where('cedula', 'like', '%' . $request->cedula . '%');
        }
        
        // Filtro por barrio
        if ($request->filled('barrio_id')) {
            $query->where('barrio_id', $request->barrio_id);
        }
        
        // Filtro por lugar de votación
        if ($request->filled('lugar_votacion_id')) {
            $query->where('lugar_votacion_id', $request->lugar_votacion_id);
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['nombre', 'cedula', 'created_at', 'barrio_id', 'lugar_votacion_id'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Paginación con eager loading para evitar N+1
        $votantes = $query->with(['barrio:id,nombre', 'lugarVotacion:id,nombre'])
                          ->paginate($perPage);
        
        // Agregar parámetros de búsqueda a la paginación
        $votantes->appends($request->except('page'));
        
        $concejalOpciones = [];

        if (is_null($lider->concejal_id) && $lider->alcalde_id) {
            $concejalOpciones = $this->getConcejales($lider->alcalde_id);
        }

        $lugares = $this->getLugaresFiltrados($lider);
        $barrios = $this->getBarriosFiltrados($lider);

        return view('permisos.ingresarVotantes', compact(
            'votantes', 
            'lider', 
            'concejalOpciones', 
            'lugares', 
            'barrios',
            'perPage'
        ));
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
        $barrios = $this->getBarriosFiltrados($lider);

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
            'barrios',
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
            'cedula' => [
                'required',
                'string',
                'max:20',
                Rule::unique('votantes')->where(function ($query) use ($lider) {
                    $query->where('lider_id', $lider->id);
                })->ignore($votante->id),
            ],
            'telefono' => 'required|string|max:20',
            'mesa' => 'required|string|max:255',
            'lugar_votacion_id' => 'required|exists:lugares_votacion,id',
            'barrio_id' => 'required|exists:barrios,id',
        ];

        if ($lider->concejal_id) {
            $rules['tambien_vota_alcalde'] = 'required|in:1,0';
        } elseif ($lider->alcalde_id) {
            $rules['concejal_id'] = 'nullable|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'cedula.unique' => 'Esta cédula ya ha sido registrada por este líder.',
            'concejal_id.exists' => 'El concejal seleccionado no es válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('editModalId', $votante->id);
        }

        $votante->fill($request->only('nombre', 'cedula', 'telefono', 'mesa', 'lugar_votacion_id', 'barrio_id'));

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
    // BUSCAR POR CÉDULA
    // =============================
    public function buscarPorCedula(Request $request)
    {
        $cedula = $request->query('cedula');

        if (!$cedula) {
            return response()->json(['exists' => false]);
        }

        $lider = $this->getLider();

        $existe = false;
        if ($lider) {
            $existe = Votante::where('cedula', $cedula)
                ->where('lider_id', $lider->id)
                ->exists();
        }

        return response()->json(['exists' => $existe]);
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
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $import = new VotantesImport($lider);
            Excel::import($import, $request->file('excel_file'));

            $mensaje = "{$import->importados} votantes importados correctamente.";
            if ($import->saltados > 0) {
                $mensaje .= " {$import->saltados} registros fueron ignorados por las siguientes razones:";
                foreach ($import->errores as $error) {
                    $mensaje .= " - {$error}";
                }
            }

            return redirect()->route('ingresarVotantes')->with('success', $mensaje);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    /**
     * Verificar estado de importación
     */
    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_votantes.csv"',
        ];

        $columns = ['nombre', 'cedula', 'telefono', 'mesa', 'lugar_votacion', 'barrio', 'concejal', 'alcalde_id'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Juan Pérez', '123456789', '3001234567', '1', 'Colegio Central', 'Centro', 'Nombre Concejal', '1']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

        $lugares = $this->getLugaresFiltrados($lider);
        $barrios = $this->getBarriosFiltrados($lider);

        return response()->json([
            'lider' => [
                'id' => $lider->id,
                'alcalde_id' => $lider->alcalde_id,
                'concejal_id' => $lider->concejal_id,
            ],
            'lugares_count' => count($lugares),
            'barrios_count' => count($barrios),
            'lugares' => $lugares,
            'barrios' => $barrios
        ]);
    }
    public function estadisticas()
    {
        $lider = $this->getLider();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        // Clave única para el caché de este líder
        $cacheKey = "estadisticas_lider_{$lider->id}";
        $cacheDuration = 300; // 5 minutos

        // Intentar obtener del caché primero
        $estadisticas = \Cache::remember($cacheKey, $cacheDuration, function () use ($lider) {
            // Totales generales con consultas optimizadas
            $totalVotantes = Votante::where('lider_id', $lider->id)->count();
            $totalMesas = Votante::where('lider_id', $lider->id)->distinct('mesa')->count('mesa');
            
            // Cachear conteos de roles globales (no cambian por líder)
            $totalConcejales = \Cache::remember('total_concejales', 600, function () {
                return User::role('aspirante-concejo')->count();
            });
            
            $totalLideres = \Cache::remember('total_lideres', 600, function () {
                return User::role('lider')->count();
            });

            // Votantes por lugar de votación con eager loading optimizado
            $votantesPorLugar = Votante::select('lugar_votacion_id', \DB::raw('count(*) as total'))
                ->where('lider_id', $lider->id)
                ->groupBy('lugar_votacion_id')
                ->with('lugarVotacion:id,nombre')
                ->get()
                ->map(function ($item) {
                    return [
                        'nombre' => $item->lugarVotacion->nombre ?? 'Sin lugar',
                        'total' => $item->total
                    ];
                });

            // Votantes por barrio con eager loading optimizado
            $votantesPorBarrio = Votante::select('barrio_id', \DB::raw('count(*) as total'))
                ->where('lider_id', $lider->id)
                ->groupBy('barrio_id')
                ->with('barrio:id,nombre')
                ->get()
                ->map(function ($item) {
                    return [
                        'nombre' => $item->barrio->nombre ?? 'Sin barrio',
                        'total' => $item->total
                    ];
                });

            // Votantes que también votan alcalde
            $votantesAlcalde = Votante::where('lider_id', $lider->id)
                ->whereNotNull('alcalde_id')
                ->count();

            // Votantes por mes (si tienes created_at)
            $votantesPorMes = Votante::selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                ->where('lider_id', $lider->id)
                ->groupBy('mes')
                ->orderBy('mes')
                ->get()
                ->map(function ($item) {
                    return [
                        'mes' => date("F", mktime(0, 0, 0, $item->mes, 1)),
                        'total' => $item->total
                    ];
                });

            return [
                'totalVotantes' => $totalVotantes,
                'totalMesas' => $totalMesas,
                'totalConcejales' => $totalConcejales,
                'totalLideres' => $totalLideres,
                'votantesPorLugar' => $votantesPorLugar,
                'votantesPorBarrio' => $votantesPorBarrio,
                'votantesAlcalde' => $votantesAlcalde,
                'votantesPorMes' => $votantesPorMes,
                'cache_timestamp' => now()->toISOString()
            ];
        });

        // Extraer variables del array para la vista
        extract($estadisticas);

        return view('votantes.dashboard', compact(
            'totalVotantes',
            'totalMesas',
            'totalConcejales',
            'totalLideres',
            'votantesPorLugar',
            'votantesPorBarrio',
            'votantesAlcalde',
            'votantesPorMes',
            'cache_timestamp'
        ));
    }

}
