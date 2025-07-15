<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Concejal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ConcejalController extends Controller
{
    /**
     * Mostrar listado de concejales del alcalde autenticado con filtros.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'concejal'])
            ->where('alcalde_id', auth()->id())
            ->role('aspirante-concejo');

        // Filtro por búsqueda general (nombre, email, partido político)
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name',  'like', "%$s%")
                  ->orWhere('email','like', "%$s%")
                  ->orWhereHas('concejal', fn ($c) =>
                        $c->where('partido_politico','like', "%$s%"));
            });
        }

        // Filtro por número de lista exacto
        if ($request->filled('numero_lista')) {
            $numeroLista = $request->input('numero_lista');
            $query->whereHas('concejal', fn ($c) =>
                $c->where('numero_lista', $numeroLista));
        }

        $concejales = $query->paginate(10)->withQueryString();

        return view('permisos.crearConcejal', compact('concejales'));
    }

    /**
     * Vista principal para concejales/líderes.
     * Ruta: /homeConcejal
     */
    public function home()
    {
        return view('userConcejal.homeConcejal');
    }

    /**
     * Crear un nuevo concejal vinculado al alcalde.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users',
            'password'         => 'required|string|min:8|confirmed',
            'partido_politico' => 'nullable|string|max:255',
            'numero_lista'     => 'nullable|integer|min:1',
        ]);

        try {
            $user = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'alcalde_id' => auth()->id(),
            ]);

            $user->assignRole('aspirante-concejo');

            Concejal::create([
                'user_id'          => $user->id,
                'alcalde_id'       => auth()->id(),
                'partido_politico' => $request->partido_politico,
                'numero_lista'     => $request->numero_lista,
                'activo'           => true,
            ]);

            return back()->with('success', 'Concejal creado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear concejal: '.$e->getMessage());
        }
    }

    /**
     * Actualizar concejal.
     */
    public function update(Request $request, User $concejal)
    {
        if (!$concejal->hasRole('aspirante-concejo')) {
            return back()->with('error', 'Usuario no válido como concejal');
        }

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => [
                'required','email','max:255',
                Rule::unique('users')->ignore($concejal->id),
            ],
            'password'         => 'nullable|string|min:8|confirmed',
            'partido_politico' => 'nullable|string|max:255',
            'numero_lista'     => 'nullable|integer|min:1',
        ]);

        try {
            $concejal->update([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->filled('password')
                                ? Hash::make($request->password)
                                : $concejal->password,
            ]);

            $dataExtra = [
                'partido_politico' => $request->partido_politico,
                'numero_lista'     => $request->numero_lista,
            ];

            $concejal->concejal
                ? $concejal->concejal->update($dataExtra)
                : Concejal::create($dataExtra + [
                    'user_id'    => $concejal->id,
                    'alcalde_id' => auth()->id(),
                    'activo'     => true,
                ]);

            return back()->with('success', 'Concejal actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar concejal: '.$e->getMessage());
        }
    }

    /**
     * Eliminar concejal.
     */
    public function destroy(User $concejal)
    {
        if (!$concejal->hasRole('aspirante-concejo')) {
            return back()->with('error', 'Usuario no válido como concejal');
        }

        try {
            $concejal->concejal?->delete();
            $concejal->removeRole('aspirante-concejo');
            $concejal->delete();

            return back()->with('success', 'Concejal eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar concejal: '.$e->getMessage());
        }
    }
}
