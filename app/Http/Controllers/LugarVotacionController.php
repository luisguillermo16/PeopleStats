<?php

namespace App\Http\Controllers;

use App\Models\LugarVotacion;
use App\Models\Mesa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LugarVotacionController extends Controller
{
    /**
     * Mostrar listado de lugares de votación según rol del usuario.
     * Incluye validación cruzada entre alcaldes y concejales ligados.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $lugares = collect();

            if ($user->hasRole('aspirante-alcaldia') || $user->hasRole('alcalde')) {
                // IDs usuarios ligados: el mismo usuario + alcalde o aspirantes ligados
                $usuariosIds = [$user->id];

                if ($user->hasRole('aspirante-alcaldia')) {
                    // Si es aspirante, agrega el alcalde_id
                    if ($user->alcalde_id) {
                        $usuariosIds[] = $user->alcalde_id;
                    }
                } else {
                    // Si es alcalde, buscar aspirantes ligados a este alcalde
                    $aspirantesLigadosIds = User::role('aspirante-alcaldia')
                        ->where('alcalde_id', $user->id)
                        ->pluck('id')
                        ->toArray();
                    $usuariosIds = array_merge($usuariosIds, $aspirantesLigadosIds);
                }

                // NUEVA FUNCIONALIDAD: También obtener concejales ligados a este alcalde
                $concejalesTotalesIds = [];
                
                foreach ($usuariosIds as $alcaldeId) {
                    // Buscar concejales que tengan alcalde_id igual a este alcalde
                    $concejalesLigados = User::whereIn('rol_id', function($query) {
                            $query->select('id')
                                  ->from('roles')
                                  ->whereIn('name', ['aspirante-concejo', 'concejal']);
                        })
                        ->where('alcalde_id', $alcaldeId)
                        ->pluck('id')
                        ->toArray();
                    
                    $concejalesTotalesIds = array_merge($concejalesTotalesIds, $concejalesLigados);
                }

                // Obtener lugares de votación de alcaldes Y concejales ligados
                $lugares = LugarVotacion::with('mesas')
                    ->where(function($query) use ($usuariosIds, $concejalesTotalesIds) {
                        $query->whereIn('alcalde_id', $usuariosIds);
                        if (!empty($concejalesTotalesIds)) {
                            $query->orWhereIn('concejal_id', $concejalesTotalesIds);
                        }
                    })
                    ->get();

            } elseif ($user->hasRole('aspirante-concejo') || $user->hasRole('concejal')) {
                // Mismo comportamiento para concejal
                $usuariosIds = [$user->id];

                if ($user->hasRole('aspirante-concejo')) {
                    if ($user->concejal_id) {
                        $usuariosIds[] = $user->concejal_id;
                    }
                } else {
                    $aspirantesConcejoIds = User::role('aspirante-concejo')
                        ->where('concejal_id', $user->id)
                        ->pluck('id')
                        ->toArray();
                    $usuariosIds = array_merge($usuariosIds, $aspirantesConcejoIds);
                }

                // NUEVA FUNCIONALIDAD: También obtener lugares del alcalde al que está ligado
                $alcaldeIds = [];
                
                foreach ($usuariosIds as $concejalId) {
                    $concejal = User::find($concejalId);
                    
                    if ($concejal && $concejal->alcalde_id) {
                        $alcaldeIds[] = $concejal->alcalde_id;
                        
                        // También agregar aspirantes ligados a ese alcalde
                        $aspirantesAlcaldiaIds = User::role('aspirante-alcaldia')
                            ->where('alcalde_id', $concejal->alcalde_id)
                            ->pluck('id')
                            ->toArray();
                        $alcaldeIds = array_merge($alcaldeIds, $aspirantesAlcaldiaIds);
                    }
                }

                // Eliminar duplicados
                $alcaldeIds = array_unique($alcaldeIds);

                // Obtener lugares de votación de concejales Y alcaldes ligados
                $lugares = LugarVotacion::with('mesas')
                    ->where(function($query) use ($usuariosIds, $alcaldeIds) {
                        $query->whereIn('concejal_id', $usuariosIds);
                        if (!empty($alcaldeIds)) {
                            $query->orWhereIn('alcalde_id', $alcaldeIds);
                        }
                    })
                    ->get();

            } else {
                // Otros roles ven todos los lugares
                $lugares = LugarVotacion::with('mesas')->get();
            }

            return view('permisos.crearPuntosVotacion', compact('lugares'));
        } catch (\Exception $e) {
            $lugares = collect();
            return view('permisos.crearPuntosVotacion', compact('lugares'))
                ->with('error', 'Error al cargar los lugares de votación: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si un usuario puede editar/eliminar un lugar específico
     */
    private function canUserModifyLugar($user, $lugar)
    {
        // Si es super admin u otros roles con permisos completos
        if ($user->hasRole(['super-admin', 'admin'])) {
            return true;
        }

        // Verificación para roles de alcaldía
        if ($user->hasRole('aspirante-alcaldia') || $user->hasRole('alcalde')) {
            // Puede editar si el lugar fue creado por él
            if ($lugar->alcalde_id === $user->id) {
                return true;
            }

            // Puede editar si fue creado por su alcalde/aspirante ligado
            if ($user->hasRole('aspirante-alcaldia') && $user->alcalde_id && $lugar->alcalde_id === $user->alcalde_id) {
                return true;
            }

            // Puede editar si fue creado por un aspirante ligado a él
            if ($user->hasRole('alcalde')) {
                $aspirantesLigados = User::role('aspirante-alcaldia')
                    ->where('alcalde_id', $user->id)
                    ->pluck('id')
                    ->toArray();
                
                if (in_array($lugar->alcalde_id, $aspirantesLigados)) {
                    return true;
                }
            }
        }

        // Verificación para roles de concejo
        if ($user->hasRole('aspirante-concejo') || $user->hasRole('concejal')) {
            // Puede editar si el lugar fue creado por él
            if ($lugar->concejal_id === $user->id) {
                return true;
            }

            // Puede editar si fue creado por su concejal/aspirante ligado
            if ($user->hasRole('aspirante-concejo') && $user->concejal_id && $lugar->concejal_id === $user->concejal_id) {
                return true;
            }

            // Puede editar si fue creado por un aspirante ligado a él
            if ($user->hasRole('concejal')) {
                $aspirantesLigados = User::role('aspirante-concejo')
                    ->where('concejal_id', $user->id)
                    ->pluck('id')
                    ->toArray();
                
                if (in_array($lugar->concejal_id, $aspirantesLigados)) {
                    return true;
                }
            }

            // NUEVA VALIDACIÓN: Puede editar si está ligado al alcalde que creó el lugar
            if ($user->alcalde_id && $lugar->alcalde_id === $user->alcalde_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mostrar formulario para crear un nuevo lugar de votación.
     */
    public function create()
    {
        return $this->index(); // Reutiliza la misma lógica
    }

    /**
     * Guardar un nuevo lugar de votación junto con sus mesas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'direccion' => 'required|string',
            'mesas' => 'required|array',
            'mesas.*' => 'required|numeric'
        ]);

        $user = Auth::user();

        $lugar = LugarVotacion::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'alcalde_id' => ($user->hasRole('aspirante-alcaldia') || $user->hasRole('alcalde')) ? $user->id : null,
            'concejal_id' => ($user->hasRole('aspirante-concejo') || $user->hasRole('concejal')) ? $user->id : null,
        ]);

        foreach ($request->mesas as $numero) {
            Mesa::create([
                'numero' => $numero,
                'lugar_votacion_id' => $lugar->id,
            ]);
        }

        return redirect()->back()->with('success', 'Lugar de votación creado correctamente.');
    }

    /**
     * Mostrar formulario para editar un lugar de votación y sus mesas.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $lugar = LugarVotacion::with('mesas')->findOrFail($id);

        if (!$this->canUserModifyLugar($user, $lugar)) {
            abort(403, 'No tienes permisos para editar este lugar de votación.');
        }

        $mesasArray = $lugar->mesas->pluck('numero')->toArray();
        return view('permisos.crearPuntosVotacion', compact('lugar', 'mesasArray'));
    }

    /**
     * Actualizar un lugar de votación y sus mesas.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'mesas' => 'required|array|min:1',
            'mesas.*' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $lugar = LugarVotacion::findOrFail($id);

            if (!$this->canUserModifyLugar($user, $lugar)) {
                abort(403, 'No tienes permisos para actualizar este lugar de votación.');
            }

            $lugar->update([
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
            ]);

            Mesa::where('lugar_votacion_id', $lugar->id)->delete();

            foreach ($request->mesas as $numero) {
                Mesa::create([
                    'numero' => $numero,
                    'lugar_votacion_id' => $lugar->id,
                ]);
            }

            DB::commit();
            return redirect()->route('lugares')->with('success', 'Lugar actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el lugar: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un lugar de votación y sus mesas.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $lugar = LugarVotacion::findOrFail($id);

       

        Mesa::where('lugar_votacion_id', $lugar->id)->delete();
        $lugar->delete();

        return redirect()->back()->with('success', 'Lugar de votación eliminado.');
    }

    /**
     * Método para depuración y diagnóstico rápido.
     */
    public function debug()
    {
        try {
            $user = Auth::user();

            $debug_info = [
                'user_authenticated' => (bool) $user,
                'user_id' => $user?->id,
                'user_roles' => $user ? $user->getRoleNames() : [],
                'user_alcalde_id' => $user?->alcalde_id,
                'user_concejal_id' => $user?->concejal_id,
                'can_crear_puntos' => $user ? $user->can('crear puntos de votacion') : false,
                'lugar_votacion_table_exists' => \Schema::hasTable('lugar_votacions'),
                'mesa_table_exists' => \Schema::hasTable('mesas'),
                'total_lugares' => LugarVotacion::count(),
                'total_mesas' => Mesa::count(),
            ];

            return response()->json($debug_info);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}