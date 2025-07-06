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
        $query = User::with('roles'); // Cargar roles

        // Búsqueda por nombre o email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filtro por rol usando rol_id
        if ($request->filled('role')) {
            $query->where('rol_id', $request->get('role'));
        }

        // Ordenar por fecha de creación
        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.admin', compact('users'));
    }

    /**
     * Guarda un nuevo usuario.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|integer|exists:roles,id', // Validar que el rol existe
        
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol_id' => $request->role, // Usar rol_id
                
            ]);

            // Asignar rol usando Spatie
            $rol = Role::find($request->role);
            if ($rol) {
                $user->assignRole($rol->name);
            }

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
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'role' => 'required|integer|exists:roles,id',
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'rol_id' => $request->role,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Actualizar rol
            $user->syncRoles([]); // Quitar roles actuales
            $rol = Role::find($request->role);
            if ($rol) {
                $user->assignRole($rol->name);
            }

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
     * Muestra los detalles de un usuario (en JSON).
     */
    public function show(User $user)
    {
        return response()->json($user->load('roles'));
    }

    /**
     * Muestra la página de inicio para el usuario alcalde.
     */
    public function home()
    {
        return view('userAlcalde.home');
    }

    public function homeConcejal()
    {
        return view('userConcejal.homeConcejal');
    }

    public function homeLider()
    {
        return view('userLider.homeLider');
    }
}