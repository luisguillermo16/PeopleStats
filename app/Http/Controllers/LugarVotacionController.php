<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LugarVotacion;
use App\Models\Mesa;
use App\Models\User;

class LugarVotacionController extends Controller
{
    /**
     * Lista todos los lugares de votación filtrados por el usuario actual
     */
    public function index()
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuario no autenticado.');
        }

        // NUEVO ENFOQUE: El usuario ES el concejal/alcalde, no TIENE un concejal/alcalde
        // Filtrar lugares donde el usuario actual sea el concejal o alcalde asignado
        $lugares = LugarVotacion::with('mesas')
            ->where(function ($query) use ($usuario) {
                // Lugares donde el usuario actual es el concejal asignado
                $query->where('concejal_id', $usuario->id);
                
                // O lugares donde el usuario actual es el alcalde asignado
                $query->orWhere('alcalde_id', $usuario->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug info
        \Log::info('Filtro de lugares:', [
            'usuario_id' => $usuario->id,
            'usuario_email' => $usuario->email,
            'total_lugares_filtrados' => $lugares->count(),
            'lugares_encontrados' => $lugares->pluck('nombre')->toArray()
        ]);

        return view('permisos.crearPuntosVotacion', compact('lugares'));
    }

    /**
     * Método para testing - eliminar después del diagnóstico
     */
    public function debug()
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado']);
        }

        // Información del usuario
        $userInfo = [
            'ID Usuario' => $usuario->id,
            'Nombre' => $usuario->name ?? 'N/A',
            'Email' => $usuario->email ?? 'N/A',
            'Alcalde ID (campo)' => $usuario->alcalde_id ?? 'NULL',
            'Concejal ID (campo)' => $usuario->concejal_id ?? 'NULL',
            'Es concejal/alcalde por ID' => 'Se busca en lugares donde concejal_id = ' . $usuario->id . ' o alcalde_id = ' . $usuario->id
        ];

        // Todos los lugares
        $lugares = LugarVotacion::all()->map(function($lugar) {
            return [
                'ID' => $lugar->id,
                'Nombre' => $lugar->nombre,
                'Alcalde ID' => $lugar->alcalde_id ?? 'NULL',
                'Concejal ID' => $lugar->concejal_id ?? 'NULL',
                'Direccion' => $lugar->direccion ?? 'N/A'
            ];
        });

        // NUEVO FILTRO: Lugares donde el usuario ES el concejal/alcalde
        $lugaresFiltradosNuevo = LugarVotacion::where(function ($query) use ($usuario) {
            $query->where('concejal_id', $usuario->id)
                  ->orWhere('alcalde_id', $usuario->id);
        })->get()->map(function($lugar) {
            return [
                'ID' => $lugar->id,
                'Nombre' => $lugar->nombre,
                'Alcalde ID' => $lugar->alcalde_id ?? 'NULL',
                'Concejal ID' => $lugar->concejal_id ?? 'NULL'
            ];
        });

        // FILTRO ANTERIOR: Lugares donde el usuario TIENE concejal_id/alcalde_id
        $lugaresFiltradosAnterior = [];
        if ($usuario->alcalde_id || $usuario->concejal_id) {
            $lugaresFiltradosAnterior = LugarVotacion::where(function ($query) use ($usuario) {
                if ($usuario->concejal_id) {
                    $query->where('concejal_id', $usuario->concejal_id);
                }
                if ($usuario->alcalde_id) {
                    if ($usuario->concejal_id) {
                        $query->orWhere('alcalde_id', $usuario->alcalde_id);
                    } else {
                        $query->where('alcalde_id', $usuario->alcalde_id);
                    }
                }
            })->get()->map(function($lugar) {
                return [
                    'ID' => $lugar->id,
                    'Nombre' => $lugar->nombre,
                    'Alcalde ID' => $lugar->alcalde_id ?? 'NULL',
                    'Concejal ID' => $lugar->concejal_id ?? 'NULL'
                ];
            });
        }

        return response()->json([
            'usuario_info' => $userInfo,
            'total_lugares_en_bd' => $lugares->count(),
            'todos_los_lugares' => $lugares,
            'NUEVO_FILTRO' => [
                'descripcion' => 'Lugares donde usuario.id = lugar.concejal_id O usuario.id = lugar.alcalde_id',
                'total' => count($lugaresFiltradosNuevo),
                'lugares' => $lugaresFiltradosNuevo,
                'sql' => 'WHERE concejal_id = ' . $usuario->id . ' OR alcalde_id = ' . $usuario->id
            ],
            'FILTRO_ANTERIOR' => [
                'descripcion' => 'Lugares donde lugar.concejal_id = usuario.concejal_id O lugar.alcalde_id = usuario.alcalde_id',
                'total' => count($lugaresFiltradosAnterior),
                'lugares' => $lugaresFiltradosAnterior,
                'problema' => 'Usuario no tiene concejal_id ni alcalde_id definidos'
            ]
        ]);
    }

    /**
     * Muestra formulario para crear un nuevo lugar (modal lo maneja)
     */
    public function create()
    {
        $usuario = Auth::user();
        
        $lugares = LugarVotacion::with('mesas')
            ->where(function ($query) use ($usuario) {
                $query->where('concejal_id', $usuario->id)
                      ->orWhere('alcalde_id', $usuario->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
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
        $usuario = Auth::user();
        
        // Verificar permisos: el lugar debe pertenecer al usuario
        if ($lugar->concejal_id != $usuario->id && $lugar->alcalde_id != $usuario->id) {
            return redirect()->back()->with('error', 'No tiene permisos para editar este lugar de votación.');
        }
        
        $lugares = LugarVotacion::with('mesas')
            ->where(function ($query) use ($usuario) {
                $query->where('concejal_id', $usuario->id)
                      ->orWhere('alcalde_id', $usuario->id);
            })
            ->get();
            
        return view('permisos.crearPuntosVotacion', compact('lugares', 'lugar'));
    }

    /**
     * Actualizar datos del lugar y manejar mesas
     */
    public function update(Request $request, LugarVotacion $lugar)
    {
        $usuario = Auth::user();
        
        // Verificar permisos
        if ($lugar->concejal_id != $usuario->id && $lugar->alcalde_id != $usuario->id) {
            return redirect()->back()->with('error', 'No tiene permisos para actualizar este lugar de votación.');
        }

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
        $usuario = Auth::user();
        
        // Verificar permisos
        if ($lugar->concejal_id != $usuario->id && $lugar->alcalde_id != $usuario->id) {
            return redirect()->back()->with('error', 'No tiene permisos para eliminar este lugar de votación.');
        }

        $lugar->delete();

        return redirect()->route('lugares')->with('success', 'Punto de votación eliminado correctamente.');
    }
}