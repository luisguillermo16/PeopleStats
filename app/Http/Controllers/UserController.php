<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Muestra el listado de usuarios con filtros.
     */
    public function index(Request $request)
    {
        $query = User::with('roles'); // Cargar relaciones de roles

        // Filtro de búsqueda por nombre o email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por estado (si tienes columna 'status')
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtro por rol usando Spatie
        if ($request->filled('role')) {
            $query->role($request->input('role')); // método role() de Spatie
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $roles = Role::all();

        return view('admin.admin', compact('users', 'roles'));
    }

    /**
     * Guarda un nuevo usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|exists:roles,name',
        ]);

        try {
            $user = User::create([
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            // Asignar rol con Spatie
            $user->assignRole($request->input('role'));

            return redirect()->route('admin')->with('success', 'Usuario creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role'     => 'required|string|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $data = [
                'name'  => $request->input('name'),
                'email' => $request->input('email'),
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }

            $user->update($data);

            // Sincronizar rol usando Spatie
            $user->syncRoles([$request->input('role')]);

            return redirect()->route('admin')->with('success', 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return redirect()->route('admin')->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un usuario en JSON.
     */
    public function show(User $user)
    {
        return response()->json($user->load('roles'));
    }

    /**
     * Página de inicio para el usuario tipo Líder.
     */
    public function homeLider()
    {
        return view('userLider.homeLider');
    }

    /**
     * Página de inicio para el usuario tipo Alcalde.
     */
    public function homeAlcalde()
    {
        return view('userAlcalde.homeAlcalde');
    }

    /**
     * Página de inicio para el administrador.
     */
    public function homeAdmin()
    {
        return view('admin.homeAdmin');
    }
}
