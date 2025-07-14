<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alcalde;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AlcaldeController extends Controller
{
    public function vistaGestion(Request $request)
    {
        $query = User::with(['roles', 'alcaldeInfo'])
                     ->role('alcalde');

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhereHas('alcaldeInfo', fn ($a) =>
                        $a->where('partido_politico', 'like', "%$s%"));
            });
        }

        $alcaldes = $query->paginate(10);

        return view('permisos.crearAlcalde', compact('alcaldes'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'partido_politico' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($r) {
                $user = User::create([
                    'name'     => $r->name,
                    'email'    => $r->email,
                    'password' => Hash::make($r->password),
                ])->assignRole('alcalde');

                Alcalde::create([
                    'user_id'          => $user->id,
                    'partido_politico' => $r->partido_politico,
                    'activo'           => true,
                ]);
            });

            return redirect()->back()->with('success', 'Alcalde creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el alcalde: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $alcalde)
    {
        if (!$alcalde->hasRole('alcalde')) {
            return redirect()->back()->with('error', 'Usuario no válido como alcalde');
        }

        $validatedData = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($alcalde->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'partido_politico' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $alcalde, $validatedData) {
                $userData = [
                    'name'  => $validatedData['name'],
                    'email' => $validatedData['email'],
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($validatedData['password']);
                }

                $alcalde->update($userData);

                $alcaldeData = [
                    'partido_politico' => $validatedData['partido_politico'],
                ];

                if ($alcalde->alcaldeInfo) {
                    $alcalde->alcaldeInfo->update($alcaldeData);
                } else {
                    Alcalde::create($alcaldeData + [
                        'user_id' => $alcalde->id,
                        'activo'  => true
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Alcalde actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el alcalde: ' . $e->getMessage());
        }
    }

    public function destroy(User $alcalde)
    {
        if (!$alcalde->hasRole('alcalde')) {
            return redirect()->back()->with('error', 'Usuario no válido como alcalde');
        }

        try {
            DB::transaction(function () use ($alcalde) {
                if ($alcalde->alcaldeInfo) {
                    $alcalde->alcaldeInfo->delete();
                }

                $alcalde->removeRole('alcalde');

                $alcalde->delete();
            });

            return redirect()->back()->with('success', 'Alcalde eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el alcalde: ' . $e->getMessage());
        }
    }

    public function show(User $alcalde)
    {
        if (!$alcalde->hasRole('alcalde')) {
            return response()->json(['error' => 'Usuario no válido como alcalde'], 404);
        }

        return response()->json($alcalde->load(['roles', 'alcaldeInfo']));
    }

    public function edit(User $alcalde)
    {
        if (!$alcalde->hasRole('alcalde')) {
            return response()->json(['error' => 'Usuario no válido como alcalde'], 404);
        }

        return response()->json([
            'id' => $alcalde->id,
            'name' => $alcalde->name,
            'email' => $alcalde->email,
            'partido_politico' => $alcalde->alcaldeInfo->partido_politico ?? '',
            'activo' => $alcalde->alcaldeInfo->activo ?? true,
            'created_at' => $alcalde->created_at->format('d/m/Y H:i'),
        ]);
    }

    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'alcaldes' => 'required|array',
            'alcaldes.*' => 'exists:users,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $alcaldes = User::whereIn('id', $request->alcaldes)
                                ->role('alcalde')
                                ->get();

                if ($alcaldes->isEmpty()) {
                    throw new \Exception('No se encontraron alcaldes válidos para eliminar');
                }

                foreach ($alcaldes as $alcalde) {
                    if ($alcalde->alcaldeInfo) {
                        $alcalde->alcaldeInfo->delete();
                    }

                    $alcalde->removeRole('alcalde');

                    $alcalde->delete();
                }
            });

            return redirect()->back()->with('success', 'Alcaldes eliminados exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar los alcaldes: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $alcalde)
    {
        if (!$alcalde->hasRole('alcalde')) {
            return redirect()->back()->with('error', 'Usuario no válido como alcalde');
        }

        try {
            DB::transaction(function () use ($alcalde) {
                if ($alcalde->alcaldeInfo) {
                    $alcalde->alcaldeInfo->update([
                        'activo' => !$alcalde->alcaldeInfo->activo
                    ]);
                } else {
                    Alcalde::create([
                        'user_id' => $alcalde->id,
                        'activo'  => true
                    ]);
                }
            });

            $status = $alcalde->fresh()->alcaldeInfo->activo ? 'activado' : 'desactivado';
            return redirect()->back()->with('success', "Alcalde {$status} exitosamente");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    public function index()
    {
        return view('userAlcalde.dashboardAlcalde');
    }

    public function home()
    {
        return view('userAlcalde.homeAlcalde');
    }
}
