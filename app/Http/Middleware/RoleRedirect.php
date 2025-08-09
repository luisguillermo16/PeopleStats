<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            if ($user->hasRole('super-admin')) {
                return redirect()->route('admin');
            } elseif ($user->hasRole('aspirante-alcaldia')) {
                return redirect()->route('dashboard');
            } elseif ($user->hasRole('aspirante-concejo')) {
                return redirect()->route('dashboard');
            } elseif ($user->hasRole('lider')) {
                return redirect()->route('dashboard');
            }
        }

        // Si no tiene ningún rol válido
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'acceso' => 'No tienes roles asignados. Contacta al administrador.',
        ]);
    }
}
