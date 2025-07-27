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

    if ($userAuth->hasRole('aspirante-alcaldia')) {
        $lideres = User::role('lider')
            ->where('alcalde_id', $userAuth->id)
            
            ->latest()
            ->paginate(10);
    } elseif ($userAuth->hasRole('aspirante-concejo')) {
        $lideres = User::role('lider')
            ->where('concejal_id', $userAuth->id)
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

    // ✅ Asignar rol 'lider'
    $user->assignRole('lider');

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
       $lider->votantesRegistrados()->update(['lider_id' => null]);
    // Eliminar votantes asociados a este líder (si existen)
    $lider->votantesRegistrados()->delete();

    // Luego eliminar el líder
    $lider->delete();

    return redirect()->route('crearLider')->with('success', 'Líder eliminado exitosamente.');
}
}
