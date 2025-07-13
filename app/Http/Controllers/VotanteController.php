<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Votante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VotanteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ingresar votantes');
    }

    /**
     * Muestra el formulario para registrar votante
     */
    public function create()
    {
        $user = Auth::user();
        
        // Determinar el contexto según el rol del usuario
        if ($user->esLider()) {
            return $this->createForLider($user);
        }
        
        abort(403, 'No tienes permisos para registrar votantes');
    }

    /**
     * Formulario para líder (casos A y B)
     */
    private function createForLider(User $lider)
    {
        $data = [
            'lider' => $lider,
            'concejalesDisponibles' => collect(),
            'tipoLider' => null,
            'alcaldeAsociado' => null,
            'concejalAsociado' => null
        ];

        // CASO A: Líder de concejal
        if ($lider->concejal_id) {
            $data['tipoLider'] = 'concejal';
            $data['concejalAsociado'] = $lider->concejal;
            $data['alcaldeAsociado'] = $lider->concejal->alcalde;
        }
        // CASO B: Líder de alcalde
        elseif ($lider->alcalde_id) {
            $data['tipoLider'] = 'alcalde';
            $data['alcaldeAsociado'] = $lider->alcalde;
            $data['concejalesDisponibles'] = $lider->alcalde->concejales()
                ->whereHas('roles', function($q) {
                    $q->where('name', 'concejal');
                })
                ->get();
        }

        return view('votantes.create', $data);
    }

    /**
     * Almacena un nuevo votante
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->esLider()) {
            abort(403, 'Solo los líderes pueden registrar votantes');
        }

        // Validaciones básicas
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'vincular_alcalde' => 'nullable|boolean',
            'concejal_seleccionado' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // CASO A: Líder de concejal
            if ($user->concejal_id) {
                $result = $this->procesarCasoLiderConcejal($request, $user);
            }
            // CASO B: Líder de alcalde
            elseif ($user->alcalde_id) {
                $result = $this->procesarCasoLiderAlcalde($request, $user);
            }
            else {
                throw new \Exception('Líder no está asociado correctamente');
            }

            DB::commit();

            return redirect()->route('votantes.index')
                ->with('success', $result['mensaje']);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * CASO A: Procesa registro por líder de concejal
     */
    private function procesarCasoLiderConcejal(Request $request, User $lider): array
    {
        $concejal = $lider->concejal;
        $alcalde = $concejal->alcalde;
        
        // Validar reglas de negocio
        $validacion = Votante::validarRegistroVotante(
            $request->cedula,
            $alcalde->id,
            $concejal->id
        );

        if (!$validacion['valido']) {
            throw new \Exception($validacion['mensaje']);
        }

        // Crear registro base
        $votanteData = [
            'nombre' => $request->nombre,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'concejal_id' => $concejal->id,
            'registrado_por' => $lider->id,
        ];

        // Si desea vincular al alcalde también
        if ($request->vincular_alcalde) {
            // Verificar si ya está con este alcalde
            $existeConAlcalde = Votante::where('cedula', $request->cedula)
                ->where('alcalde_id', $alcalde->id)
                ->exists();

            if (!$existeConAlcalde) {
                $votanteData['alcalde_id'] = $alcalde->id;
            }
        }

        $votante = Votante::create($votanteData);

        $mensaje = "Votante registrado exitosamente para el concejal {$concejal->name}";
        if ($request->vincular_alcalde) {
            $mensaje .= " y alcalde {$alcalde->name}";
        }

        return [
            'votante' => $votante,
            'mensaje' => $mensaje
        ];
    }

    /**
     * CASO B: Procesa registro por líder de alcalde
     */
    private function procesarCasoLiderAlcalde(Request $request, User $lider): array
    {
        $alcalde = $lider->alcalde;
        
        // Validar reglas de negocio
        $validacion = Votante::validarRegistroVotante(
            $request->cedula,
            $alcalde->id
        );

        if (!$validacion['valido']) {
            // Si permite modificación, recuperar el registro existente
            if (isset($validacion['permitir_modificacion']) && $validacion['permitir_modificacion']) {
                return $this->modificarVotanteExistente($request, $validacion['votante_existente'], $lider);
            }
            throw new \Exception($validacion['mensaje']);
        }

        // Crear registro base
        $votanteData = [
            'nombre' => $request->nombre,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'alcalde_id' => $alcalde->id,
            'registrado_por' => $lider->id,
        ];

        // Si desea vincular a un concejal también
        if ($request->concejal_seleccionado) {
            $concejalId = $request->concejal_seleccionado;
            
            // Verificar que el concejal pertenece al alcalde
            $concejal = User::where('id', $concejalId)
                ->where('alcalde_id', $alcalde->id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'concejal');
                })
                ->first();

            if (!$concejal) {
                throw new \Exception('Concejal seleccionado no válido');
            }

            // Validar que no esté con otro concejal del mismo alcalde
            $validacionConcejal = Votante::validarAsignacionConcejal(
                $request->cedula,
                $concejalId
            );

            if (!$validacionConcejal['valido']) {
                throw new \Exception($validacionConcejal['mensaje']);
            }

            $votanteData['concejal_id'] = $concejalId;
        }

        $votante = Votante::create($votanteData);

        $mensaje = "Votante registrado exitosamente para el alcalde {$alcalde->name}";
        if ($request->concejal_seleccionado) {
            $concejal = User::find($request->concejal_seleccionado);
            $mensaje .= " y concejal {$concejal->name}";
        }

        return [
            'votante' => $votante,
            'mensaje' => $mensaje
        ];
    }

    /**
     * Modifica un votante existente
     */
    private function modificarVotanteExistente(Request $request, Votante $votante, User $lider): array
    {
        // Actualizar datos
        $votante->update([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
        ]);

        // Si desea vincular a un concejal también (solo para caso B)
        if ($lider->alcalde_id && $request->concejal_seleccionado) {
            $concejalId = $request->concejal_seleccionado;
            
            // Validar concejal
            $concejal = User::where('id', $concejalId)
                ->where('alcalde_id', $lider->alcalde_id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'concejal');
                })
                ->first();

            if ($concejal) {
                $validacionConcejal = Votante::validarAsignacionConcejal(
                    $request->cedula,
                    $concejalId,
                    $votante->id
                );

                if ($validacionConcejal['valido']) {
                    $votante->concejal_id = $concejalId;
                    $votante->save();
                }
            }
        }

        return [
            'votante' => $votante,
            'mensaje' => "Votante actualizado exitosamente"
        ];
    }

    /**
     * Muestra la lista de votantes
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Votante::with(['alcalde', 'concejal', 'registradoPor']);

        // Filtrar según rol y permisos
        if ($user->esLider()) {
            if ($user->concejal_id) {
                // Líder de concejal: solo sus votantes
                $query->where('registrado_por', $user->id);
            } elseif ($user->alcalde_id) {
                // Líder de alcalde: votantes del alcalde
                $query->byAlcaldeCompleto($user->alcalde_id);
            }
        } elseif ($user->esConcejal()) {
            // Concejal: sus votantes directos
            $query->byConcejal($user->id);
        } elseif ($user->esAlcalde()) {
            // Alcalde: todos sus votantes
            $query->byAlcaldeCompleto($user->id);
        }

        // Búsqueda
        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->where(function($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                  ->orWhere('cedula', 'like', "%{$termino}%")
                  ->orWhere('telefono', 'like', "%{$termino}%")
                  ->orWhere('email', 'like', "%{$termino}%");
            });
        }

        $votantes = $query->orderBy('nombre')->paginate(20);

        return view('votantes.index', compact('votantes'));
    }

    /**
     * Muestra los detalles de un votante
     */
    public function show(Votante $votante)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$this->puedeVerVotante($user, $votante)) {
            abort(403, 'No tienes permisos para ver este votante');
        }

        $votante->load(['alcalde', 'concejal', 'registradoPor']);

        return view('votantes.show', compact('votante'));
    }

    /**
     * Muestra el formulario para editar un votante
     */
    public function edit(Votante $votante)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$this->puedeEditarVotante($user, $votante)) {
            abort(403, 'No tienes permisos para editar este votante');
        }

        $data = [
            'votante' => $votante,
            'lider' => $user,
            'concejalesDisponibles' => collect(),
            'tipoLider' => null,
            'alcaldeAsociado' => null,
            'concejalAsociado' => null
        ];

        // Determinar contexto
        if ($user->concejal_id) {
            $data['tipoLider'] = 'concejal';
            $data['concejalAsociado'] = $user->concejal;
            $data['alcaldeAsociado'] = $user->concejal->alcalde;
        } elseif ($user->alcalde_id) {
            $data['tipoLider'] = 'alcalde';
            $data['alcaldeAsociado'] = $user->alcalde;
            $data['concejalesDisponibles'] = $user->alcalde->concejales()
                ->whereHas('roles', function($q) {
                    $q->where('name', 'concejal');
                })
                ->get();
        }

        return view('votantes.edit', $data);
    }

    /**
     * Actualiza un votante
     */
    public function update(Request $request, Votante $votante)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$this->puedeEditarVotante($user, $votante)) {
            abort(403, 'No tienes permisos para editar este votante');
        }

        // Validaciones básicas
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'vincular_alcalde' => 'nullable|boolean',
            'concejal_seleccionado' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Actualizar datos básicos
            $votante->update([
                'nombre' => $request->nombre,
                'cedula' => $request->cedula,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'direccion' => $request->direccion,
            ]);

            // Lógica específica según tipo de líder
            if ($user->concejal_id) {
                $this->actualizarCasoLiderConcejal($request, $votante, $user);
            } elseif ($user->alcalde_id) {
                $this->actualizarCasoLiderAlcalde($request, $votante, $user);
            }

            DB::commit();

            return redirect()->route('votantes.show', $votante)
                ->with('success', 'Votante actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Actualiza votante para líder de concejal
     */
    private function actualizarCasoLiderConcejal(Request $request, Votante $votante, User $lider)
    {
        $concejal = $lider->concejal;
        $alcalde = $concejal->alcalde;

        // Validar cédula si cambió
        if ($votante->cedula !== $request->cedula) {
            $validacion = Votante::validarRegistroVotante(
                $request->cedula,
                $alcalde->id,
                $concejal->id,
                $votante->id
            );

            if (!$validacion['valido']) {
                throw new \Exception($validacion['mensaje']);
            }
        }

        // Manejar vinculación con alcalde
        if ($request->vincular_alcalde) {
            $existeConAlcalde = Votante::where('cedula', $request->cedula)
                ->where('alcalde_id', $alcalde->id)
                ->where('id', '!=', $votante->id)
                ->exists();

            if (!$existeConAlcalde) {
                $votante->alcalde_id = $alcalde->id;
            }
        } else {
            $votante->alcalde_id = null;
        }

        $votante->save();
    }

    /**
     * Actualiza votante para líder de alcalde
     */
    private function actualizarCasoLiderAlcalde(Request $request, Votante $votante, User $lider)
    {
        $alcalde = $lider->alcalde;

        // Validar cédula si cambió
        if ($votante->cedula !== $request->cedula) {
            $validacion = Votante::validarRegistroVotante(
                $request->cedula,
                $alcalde->id,
                null,
                $votante->id
            );

            if (!$validacion['valido']) {
                throw new \Exception($validacion['mensaje']);
            }
        }

        // Manejar vinculación con concejal
        if ($request->concejal_seleccionado) {
            $concejalId = $request->concejal_seleccionado;
            
            // Verificar que el concejal pertenece al alcalde
            $concejal = User::where('id', $concejalId)
                ->where('alcalde_id', $alcalde->id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'concejal');
                })
                ->first();

            if (!$concejal) {
                throw new \Exception('Concejal seleccionado no válido');
            }

            // Validar asignación
            $validacionConcejal = Votante::validarAsignacionConcejal(
                $request->cedula,
                $concejalId,
                $votante->id
            );

            if (!$validacionConcejal['valido']) {
                throw new \Exception($validacionConcejal['mensaje']);
            }

            $votante->concejal_id = $concejalId;
        } else {
            $votante->concejal_id = null;
        }

        $votante->save();
    }

    /**
     * Elimina un votante
     */
    public function destroy(Votante $votante)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$this->puedeEliminarVotante($user, $votante)) {
            abort(403, 'No tienes permisos para eliminar este votante');
        }

        $votante->delete();

        return redirect()->route('votantes.index')
            ->with('success', 'Votante eliminado exitosamente');
    }

    /**
     * API: Buscar votantes
     */
    public function buscar(Request $request)
    {
        $user = Auth::user();
        $termino = $request->get('q', '');

        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $query = Votante::where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('cedula', 'like', "%{$termino}%");
        });

        // Filtrar según permisos
        if ($user->esLider()) {
            if ($user->concejal_id) {
                $query->where('registrado_por', $user->id);
            } elseif ($user->alcalde_id) {
                $query->byAlcaldeCompleto($user->alcalde_id);
            }
        } elseif ($user->esConcejal()) {
            $query->byConcejal($user->id);
        } elseif ($user->esAlcalde()) {
            $query->byAlcaldeCompleto($user->id);
        }

        $votantes = $query->with(['alcalde', 'concejal'])
            ->limit(10)
            ->get()
            ->map(function ($votante) {
                return [
                    'id' => $votante->id,
                    'nombre' => $votante->nombre,
                    'cedula' => $votante->cedula,
                    'alcalde' => $votante->alcalde?->name,
                    'concejal' => $votante->concejal?->name,
                ];
            });

        return response()->json($votantes);
    }

    /**
     * Estadísticas de votantes
     */
    public function estadisticas()
    {
        $user = Auth::user();

        if ($user->esAlcalde()) {
            $estadisticas = Votante::estadisticasPorAlcalde($user->id);
        } elseif ($user->esConcejal()) {
            $estadisticas = [
                'total' => $user->votantesConcejal()->count(),
                'detalle' => 'Votantes registrados para tu concejalía'
            ];
        } else {
            abort(403, 'No tienes permisos para ver estadísticas');
        }

        return view('votantes.estadisticas', compact('estadisticas'));
    }

    // ===============================
    // MÉTODOS PRIVADOS DE VALIDACIÓN
    // ===============================

    private function puedeVerVotante(User $user, Votante $votante): bool
    {
        if ($user->esAdministrador()) {
            return true;
        }

        if ($user->esLider()) {
            return $votante->registrado_por === $user->id ||
                   $votante->perteneceAlcalde($user->alcalde_id ?? $user->concejal?->alcalde_id);
        }

        if ($user->esConcejal()) {
            return $votante->concejal_id === $user->id ||
                   $votante->perteneceAlcalde($user->alcalde_id);
        }

        if ($user->esAlcalde()) {
            return $votante->perteneceAlcalde($user->id);
        }

        return false;
    }

    private function puedeEditarVotante(User $user, Votante $votante): bool
    {
        if ($user->esLider()) {
            return $votante->registrado_por === $user->id;
        }

        return false;
    }

    private function puedeEliminarVotante(User $user, Votante $votante): bool
    {
        if ($user->esLider()) {
            return $votante->registrado_por === $user->id;
        }

        return false;
    }
}