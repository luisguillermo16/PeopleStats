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
        // Cargar todos los líderes con su usuario
        $lideres = User::role('lider')->with('lider')->latest()->paginate(10);

        return view('permisos.crearLider', compact('lideres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $concejal = Auth::user()->concejal;

        if (!$concejal) {
            return redirect()->back()->with('error', 'El usuario autenticado no es un concejal.');
        }

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'concejal_id' => $concejal->id,
            'alcalde_id'  => $concejal->alcalde_id,
        ]);

        $user->assignRole('lider');

        Lider::create([
            'user_id'     => $user->id,
            'concejal_id' => $concejal->id,
            'zona_influencia'     => $request->zona_influencia,
            'telefono'            => $request->telefono,
            'afiliacion_politica' => $request->afiliacion_politica,
            'nivel_liderazgo'     => $request->nivel_liderazgo,
            'descripcion'         => $request->descripcion,
        ]);

        return redirect()->route('crearLider')->with('success', 'Líder creado exitosamente.');
    }
}
