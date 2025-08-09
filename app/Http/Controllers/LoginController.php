<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de login.
     */
    public function index()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Registrar un nuevo usuario.
     */
    public function registro(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string|exists:roles,name',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            Auth::login($user);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user)->with('success', '¡Registro exitoso! Bienvenido ' . $user->name);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
                         ->withInput($request->except(['password', 'password_confirmation']));
        } catch (\Exception $e) {
            Log::error('Error en registro: ' . $e->getMessage());
            return back()->with('error', 'Error al registrar usuario. Intenta nuevamente.')
                         ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Autenticar usuario.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($credentials)) {
                return back()->withErrors([
                    'email' => 'Credenciales inválidas.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();

            $user = Auth::user();
            return $this->redirectBasedOnRole($user);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())
                         ->withInput($request->only('email'));
        } catch (\Exception $e) {
            Log::error('Error en login: ' . $e->getMessage());
            return back()->with('error', 'Error durante el login. Intenta de nuevo.')
                         ->withInput($request->only('email'));
        }
    }

    /**
     * Redirigir según el rol del usuario.
     */
    private function redirectBasedOnRole(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin');
        } elseif ($user->hasRole('aspirante-alcaldia')) {
            return redirect()->route('dashboard');
        } elseif ($user->hasRole('aspirante-concejo')) {
            return redirect()->route('dashboard');
        } elseif ($user->hasRole('lider')) {
            return redirect()->route('dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->with('error', 'Rol no válido. Contacta al administrador.');
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Log::info('Usuario cerró sesión', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email ?? null,
            'ip' => $request->ip(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Verificar si el email existe en el sistema.
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $emailExists = User::where('email', $request->email)->exists();
        
        return response()->json([
            'exists' => $emailExists,
            'message' => $emailExists ? 'Email encontrado' : 'Email no registrado'
        ]);
    }
}
