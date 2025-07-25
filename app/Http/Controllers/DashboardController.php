<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Podrías pasar datos dinámicos según el rol
        $rol = auth()->user()->getRoleNames()->first();

        return view('dashboard.dashboard', compact('rol'));
    }
}
