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
use App\Jobs\ImportarVotantesJob;
use App\Models\Mesa;

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
     * Obtiene el alcalde_id de la rama del líder actual
     */
    private function getAlcaldeIdDeRama($lider)
    {
        // Si el líder tiene alcalde_id directamente
        if ($lider->alcalde_id) {
            return $lider->alcalde_id;
        }

        // Si el líder pertenece a un concejal, obtener el alcalde del concejal
        if ($lider->concejal_id) {
            $concejal = User::find($lider->concejal_id);
            return $concejal ? $concejal->alcalde_id : null;
        }

        return null;
    }

    /**
     * Valida que una cédula no exista en la rama del alcalde
     */
    private function validarCedulaUnicaEnRama($cedula, $lider, $votanteId = null)
    {
        $alcaldeId = $this->getAlcaldeIdDeRama($lider);
        
        if (!$alcaldeId) {
            return false; // Sin alcalde no puede validar
        }

        $query = Votante::where('cedula', $cedula)
                        ->where('alcalde_id', $alcaldeId);

        // Si estamos editando, excluir el votante actual
        if ($votanteId) {
            $query->where('id', '!=', $votanteId);
        }

        return !$query->exists();
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
        $alcaldeId = $this->getAlcaldeIdDeRama($lider);

        if (!$alcaldeId) {
            return collect();
        }

        return Barrio::where('alcalde_id', $alcaldeId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    }

    /**
     * Obtiene todas las mesas disponibles para el líder actual
     */
    private function getMesasFiltradas($lider)
    {
        $lugares = $this->getLugaresFiltrados($lider);
        $mesas = collect();

        foreach ($lugares as $lugar) {
            foreach ($lugar['mesas'] as $mesa) {
                $mesas->push((object)[
                    'id' => $mesa['id'],
                    'numero' => $mesa['numero'],
                    'lugar_id' => $lugar['id'],
                    'lugar_nombre' => $lugar['nombre']
                ]);
            }
        }

        return $mesas->sortBy('numero');
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
        $mesas = $this->getMesasFiltradas($lider);

        return view('votantes.create', compact('lider', 'concejalOpciones', 'lugares', 'barrios', 'mesas'));
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

        // Validación personalizada de cédula única en la rama
        $request->validate([
            'cedula' => 'required|string|max:20',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'mesa_id' => 'required|exists:mesas,id',
            'lugar_votacion_id' => 'required|exists:lugares_votacion,id',
            'barrio_id' => 'required|exists:barrios,id',
        ]);

        // Validación específica: cédula única por rama de alcalde
        if (!$this->validarCedulaUnicaEnRama($request->cedula, $lider)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['cedula' => 'Esta cédula ya ha sido registrada en esta campaña por otro líder.']);
        }

        // Validar que la mesa pertenece al lugar seleccionado
        $mesa = Mesa::where('id', $request->mesa_id)
                   ->where('lugar_votacion_id', $request->lugar_votacion_id)
                   ->first();

        if (!$mesa) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['mesa_id' => 'La mesa seleccionada no pertenece al lugar de votación elegido.']);
        }

        // Validaciones adicionales según el tipo de líder
        if ($lider->concejal_id) {
            $request->validate([
                'tambien_vota_alcalde' => 'required|in:1,0'
            ]);
        } elseif ($lider->alcalde_id) {
            $request->validate([
                'concejal_id' => 'nullable|exists:users,id'
            ]);
        }

        // Crear votante
        $votante = new Votante($request->only('nombre', 'cedula', 'telefono', 'mesa_id', 'lugar_votacion_id', 'barrio_id'));
        $votante->lider_id = $lider->id;

        // Asignar alcalde_id siempre (clave para la validación)
        $votante->alcalde_id = $this->getAlcaldeIdDeRama($lider);

        if ($lider->concejal_id) {
            $votante->concejal_id = $lider->concejal_id;
            // Solo quitar el alcalde si NO vota por alcalde
            if ($request->tambien_vota_alcalde != '1') {
                $votante->alcalde_id = null;
            }
        } elseif ($lider->alcalde_id) {
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
        $perPage = $request->get('per_page', 10);
        $perPage = min(max($perPage, 10), 100);
        
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

        // Filtro por mesa
        if ($request->filled('mesa_id')) {
            $query->where('mesa_id', $request->mesa_id);
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['nombre', 'cedula', 'created_at', 'barrio_id', 'lugar_votacion_id', 'mesa_id'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Paginación con eager loading
        $votantes = $query->with([
            'barrio:id,nombre',
            'lugarVotacion:id,nombre',
            'mesa:id,numero'
        ])->paginate($perPage);
        
        $votantes->appends($request->except('page'));
        
        $concejalOpciones = [];

        if (is_null($lider->concejal_id) && $lider->alcalde_id) {
            $concejalOpciones = $this->getConcejales($lider->alcalde_id);
        }

        $lugares = $this->getLugaresFiltrados($lider);
        $barrios = $this->getBarriosFiltrados($lider);
        $mesas = $this->getMesasFiltradas($lider);
       
        return view('permisos.ingresarVotantes', compact(
            'votantes', 
            'lider', 
            'concejalOpciones', 
            'lugares', 
            'barrios',
            'mesas',
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
        $mesas = $this->getMesasFiltradas($lider);

        $totalVotantes = Votante::where('lider_id', $lider->id)->count();
        $totalMesas = Votante::where('lider_id', $lider->id)->distinct('mesa_id')->count('mesa_id');
        $totalConcejales = User::role('aspirante-concejo')->count();
        $totalLideres = User::role('lider')->count();

        return view('permisos.ingresarVotantes', compact(
            'votante',
            'lider',
            'votantes',
            'concejalOpciones',
            'lugares',
            'barrios',
            'mesas',
            'totalVotantes',
            'totalMesas',
            'totalConcejales',
            'totalLideres'
        ));
    }

    // =============================
    // ACTUALIZAR VOTANTE - CORREGIDO
    // =============================
    public function update(Request $request, $id)
    {
        $votante = Votante::findOrFail($id);
        $lider = $this->getLider();

        if (!$lider || $votante->lider_id !== $lider->id) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar este votante.');
        }

        // Validación básica
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20',
            'telefono' => 'required|string|max:20',
            'mesa_id' => 'required|exists:mesas,id',
            'lugar_votacion_id' => 'required|exists:lugares_votacion,id',
            'barrio_id' => 'required|exists:barrios,id',
        ]);

        // Validación específica: cédula única por rama (excluyendo el actual)
        if (!$this->validarCedulaUnicaEnRama($request->cedula, $lider, $votante->id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['cedula' => 'Esta cédula ya ha sido registrada en esta campaña por otro líder.'])
                ->with('editModalId', $votante->id);
        }

        // Validar que la mesa pertenece al lugar seleccionado
        $mesa = Mesa::where('id', $request->mesa_id)
                   ->where('lugar_votacion_id', $request->lugar_votacion_id)
                   ->first();

        if (!$mesa) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['mesa_id' => 'La mesa seleccionada no pertenece al lugar de votación elegido.'])
                ->with('editModalId', $votante->id);
        }

        // Validaciones adicionales según el tipo de líder
        if ($lider->concejal_id) {
            $request->validate(['tambien_vota_alcalde' => 'required|in:1,0']);
        } elseif ($lider->alcalde_id) {
            $request->validate(['concejal_id' => 'nullable|exists:users,id']);
        }

        // ✅ ACTUALIZAR DATOS BÁSICOS - LÍNEA CORREGIDA
        $votante->fill($request->only('nombre', 'cedula', 'telefono', 'mesa_id', 'lugar_votacion_id', 'barrio_id'));

        // Asignar alcalde_id siempre
        $votante->alcalde_id = $this->getAlcaldeIdDeRama($lider);

        if ($lider->concejal_id) {
            $votante->concejal_id = $lider->concejal_id;
            // Solo quitar el alcalde si NO vota por alcalde
            if ($request->tambien_vota_alcalde != '1') {
                $votante->alcalde_id = null;
            }
        } elseif ($lider->alcalde_id) {
            $votante->concejal_id = $request->filled('concejal_id') ? $request->concejal_id : null;
        }

        $votante->save();

        return redirect()->route('ingresarVotantes')->with('success', 'Votante actualizado correctamente.');
    }

    // =============================
    // BUSCAR POR CÉDULA - MEJORADO
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
            // Verificar si existe en la rama del alcalde
            $existe = !$this->validarCedulaUnicaEnRama($cedula, $lider);
        }

        return response()->json([
            'exists' => $existe,
            'message' => $existe ? 'Esta cédula ya está registrada en esta campaña.' : 'Cédula disponible.'
        ]);
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
    // IMPORTAR EXCEL - MEJORADO
    // =============================
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls'
        ]);

        $lider = $this->getLider();

        $import = new VotantesImport($lider);
        Excel::import($import, $request->file('excel_file'));

        // Guardamos en sesión para mostrar en la vista
        session()->flash('import_result', [
            'importados' => $import->importadosDetalle,
            'errores'    => $import->errores
        ]);

        return redirect()->route('ingresarVotantes')->with('success', 'Votantes importados correctamente.');
    }

    /**
     * Template de descarga
     */
    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_votantes.csv"',
        ];

        $columns = ['nombre', 'cedula', 'telefono', 'mesa_id', 'lugar_votacion_id', 'barrio_id'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Juan Pérez', '123456789', '3001234567', '1', '1', '1']);
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
        $mesas = $this->getMesasFiltradas($lider);
        $alcaldeId = $this->getAlcaldeIdDeRama($lider);

        return response()->json([
            'lider' => [
                'id' => $lider->id,
                'alcalde_id' => $lider->alcalde_id,
                'concejal_id' => $lider->concejal_id,
                'alcalde_de_rama' => $alcaldeId,
            ],
            'lugares_count' => count($lugares),
            'barrios_count' => count($barrios),
            'mesas_count' => $mesas->count(),
            'lugares' => $lugares,
            'barrios' => $barrios,
            'mesas' => $mesas
        ]);
    }

    public function estadisticas()
    {
        $lider = $this->getLider();

        if (!$lider) {
            return redirect()->back()->with('error', 'No se encontró el líder asociado al usuario.');
        }

        $cacheKey = "estadisticas_lider_{$lider->id}";
        $cacheDuration = 300;

        $estadisticas = \Cache::remember($cacheKey, $cacheDuration, function () use ($lider) {
            $totalVotantes = Votante::where('lider_id', $lider->id)->count();
            // Corregido: usar mesa_id en lugar de mesa
            $totalMesas = Votante::where('lider_id', $lider->id)->distinct('mesa_id')->count('mesa_id');
            
            $totalConcejales = \Cache::remember('total_concejales', 600, function () {
                return User::role('aspirante-concejo')->count();
            });
            
            $totalLideres = \Cache::remember('total_lideres', 600, function () {
                return User::role('lider')->count();
            });

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

            $votantesAlcalde = Votante::where('lider_id', $lider->id)
                ->whereNotNull('alcalde_id')
                ->count();

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