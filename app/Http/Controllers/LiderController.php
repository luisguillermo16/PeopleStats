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
 public function index(Request $request)
{
    $userAuth = Auth::user();

    $query = User::role('lider')
        ->withCount('votantesRegistrados')
        ->orderByDesc('votantes_registrados_count');

    // Aplicar filtro según rol del usuario
    if ($userAuth->hasRole('aspirante-alcaldia')) {
        $query->where('alcalde_id', $userAuth->id);
    } elseif ($userAuth->hasRole('aspirante-concejo')) {
        $query->where('concejal_id', $userAuth->id);
    } else {
        return redirect()->back()->with('error', 'No tienes permiso para ver líderes.');
    }

    // 🔹 Aplicar búsqueda si hay texto
    if ($request->has('search') && $request->search != '') {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $lideres = $query->latest()->paginate(10);

    // Mantener query string en la paginación
    $lideres->appends($request->all());

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
