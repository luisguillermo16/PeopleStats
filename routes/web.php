<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/inicia-sesion', [LoginController::class, 'login'])->name('inicia-sesion');
Route::post('/validar-registro', [LoginController::class, 'registro'])->name('validar-registro');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/check-email', [LoginController::class, 'checkEmail'])->name('check-email');

// ============================================
// RUTAS PROTEGIDAS POR PERMISOS ESPECÍFICOS
// ============================================

// 🟩 SUPER ADMIN - Solo él puede acceder al panel admin
Route::middleware(['auth', 'can:acceder admin'])->group(function () {
    Route::get('/admin', [UserController::class, 'index'])->name('admin');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
  ;
});

// 🟨 ASPIRANTE A ALCALDÍA - Solo él puede crear concejales
Route::middleware(['auth', 'can:crear concejales'])->group(function () {
    Route::get('/home', [UserController::class, 'home'])->name('home');
    // Otras rutas específicas para alcaldes
});

// 🟧 ASPIRANTE AL CONCEJO - Solo él puede crear líderes
Route::middleware(['auth', 'can:crear lideres'])->group(function () {
    Route::get('/homeConcejal', [UserController::class, 'homeConcejal'])->name('homeConcejal');
    // Otras rutas específicas para concejales
});

// 🟦 LÍDER - Solo él puede ingresar votantes
Route::middleware(['auth', 'can:ingresar votantes'])->group(function () {
    Route::get('/homeLider', [UserController::class, 'homeLider'])->name('homeLider');
    // Otras rutas específicas para líderes
});

// Fallback
Route::fallback(function () {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
    }
    abort(404);
});