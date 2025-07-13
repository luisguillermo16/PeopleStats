<?php

use App\Http\Controllers\AlcaldeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VotanteController;

// Redirección al login
Route::get('/', function () {
    return redirect()->route('login');
});


// ==========================================
// RUTAS DE AUTENTICACIÓN
// ==========================================
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/inicia-sesion', [LoginController::class, 'login'])->name('inicia-sesion');
Route::post('/validar-registro', [LoginController::class, 'registro'])->name('validar-registro');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/check-email', [LoginController::class, 'checkEmail'])->name('check-email');


// ==========================================
// RUTAS PROTEGIDAS POR PERMISOS DE SPATIE
// ==========================================

// 🟩 SUPER ADMIN - Panel de administración
Route::middleware(['auth', 'can:acceder admin'])->group(function () {
    Route::get('/admin', [UserController::class, 'index'])->name('admin');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
});


// 🟨 ASPIRANTE A ALCALDÍA - Panel de alcalde
Route::middleware(['auth', 'can:crear concejales'])->group(function () {
    Route::get('/home', [AlcaldeController::class, 'index'])->name('home');
    Route::post('/home/users', [AlcaldeController::class, 'store'])->name('home.users.store');
    Route::put('/home/users/{user}', [AlcaldeController::class, 'update'])->name('home.users.update');
    Route::delete('/home/users/{user}', [AlcaldeController::class, 'destroy'])->name('home.users.destroy');
    Route::get('/home/users/{user}', [AlcaldeController::class, 'show'])->name('home.users.show');
});
    // Aquí puedes añadir más rutas específicas para alcaldes si las necesitas


// 🟧 ASPIRANTE AL CONCEJO - Panel de concejal
Route::middleware(['auth', 'can:crear lideres'])->group(function () {
    Route::get('/homeConcejal', [UserController::class, 'homeConcejal'])->name('homeConcejal');
    // Aquí puedes añadir más rutas específicas para concejales si las necesitas
});


// 🟦 LÍDER - Gestión de votantes
Route::middleware(['auth', 'can:ingresar votantes'])->group(function () {
    Route::get('/homeLider', [UserController::class, 'homeLider'])->name('homeLider');

  
});


// ==========================================
// RUTA DE FALLBACK
// ==========================================
Route::fallback(function () {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
    }
    abort(404);
});
