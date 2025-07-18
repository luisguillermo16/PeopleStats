<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class LiderController extends Controller
{
    public function index()
    {
        $userAuth = Auth::user();

        // Si es alcalde: ver sus líderes directos + líderes de concejales que él creó
     if ($userAuth->hasRole('aspirante-alcaldia')) {
    // Mostrar solo los líderes que fueron creados directamente por el alcalde
    $lideres = User::role('lider')
        ->where('alcalde_id', $userAuth->id)
        ->whereNull('concejal_id') // 🔒 Muy importante: excluye líderes de concejales
        ->with('lider')
        ->latest()
        ->paginate(10);
}

        // Si es concejal: ver solo líderes que él creó
        elseif ($userAuth->hasRole('aspirante-concejo')) {
            $lideres = User::role('lider')
                ->where('concejal_id', $userAuth->id)
                ->with('lider')
                ->latest()
                ->paginate(10);
        } else {
            return redirect()->back()->with('error', 'No tienes permiso para ver líderes.');
        }

        return view('permisos.crearLider', compact('lideres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $userAuth = Auth::user();

        if (!$userAuth->hasAnyRole(['aspirante-concejo', 'aspirante-alcaldia', 'super-admin'])) {
            return redirect()->back()->with('error', 'No tienes permisos para crear líderes.');
        }

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'concejal_id' => $userAuth->hasRole('aspirante-concejo') ? $userAuth->id : null,
            'alcalde_id'  => $userAuth->hasRole('aspirante-alcaldia') ? $userAuth->id : ($userAuth->alcalde_id ?? null),
        ]);

        $user->assignRole('lider');

        Lider::create([
            'user_id'     => $user->id,
            'concejal_id' => $user->concejal_id,
            'alcalde_id'  => $user->alcalde_id,
        ]);

        return redirect()->route('crearLider')->with('success', 'Líder creado exitosamente.');
    }

    public function show(User $lider)
    {
        return view('permisos.verLider', compact('lider'));
    }

    public function update(Request $request, User $lider)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $lider->id,
        ]);

        $lider->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('crearLider')->with('success', 'Líder actualizado exitosamente.');
    }

    public function destroy(User $lider)
    {
        $lider->delete();

        return redirect()->route('crearLider')->with('success', 'Líder eliminado exitosamente.');
    }
}
