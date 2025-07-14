<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlcaldeController;
use App\Http\Controllers\ConcejalController;
use App\Models\User;

/* ───────── REDIRECCIÓN INICIAL ───────── */
Route::get('/', fn () => redirect()->route('login'));

/* ╔════════════════════════════╗
   ║     AUTENTICACIÓN          ║
   ╚════════════════════════════╝ */
Route::get ('/login',             [LoginController::class,'index' ])->name('login');
Route::post('/inicia-sesion',     [LoginController::class,'login' ])->name('inicia-sesion');
Route::post('/validar-registro',  [LoginController::class,'registro'])->name('validar-registro');
Route::post('/logout',            [LoginController::class,'logout' ])->name('logout');
Route::post('/check-email',       [LoginController::class,'checkEmail'])->name('check-email');

/* ╔════════════════════════════╗
   ║         SUPER‑ADMIN        ║
   ╚════════════════════════════╝ */
Route::middleware(['auth','can:acceder admin'])->group(function () {
    Route::get   ('/admin',               [UserController::class,'index' ])->name('admin');
    Route::post  ('/admin/users',         [UserController::class,'store' ])->name('admin.users.store');
    Route::put   ('/admin/users/{user}',  [UserController::class,'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}',  [UserController::class,'destroy'])->name('admin.users.destroy');
    Route::get   ('/admin/users/{user}',  [UserController::class,'show' ])->name('admin.users.show');
});

/* ╔════════════════════════════╗
   ║   ALCALDE (gestiona CJs)   ║
   ╚════════════════════════════╝ */
Route::middleware(['auth','can:crear concejales'])->group(function () {

    /*  Dashboard del alcalde  */
    Route::get('/dashboardAlcalde', [AlcaldeController::class,'index'])->name('dashboardAlcalde');

    /*  Vista tabla de concejales  */
    Route::get('/crearConcejal',    [ConcejalController::class,'index'])->name('crearConcejal');

    /*  Model binding seguro: sólo usuarios con rol aspirante‑concejo  */
    Route::bind('concejal', function ($value) {
        $user = User::with('concejal')->find($value);
        if (!$user || !$user->hasRole('aspirante-concejo')) abort(404);
        return $user;
    });

    /*  CRUD RESTful de concejales  */
    Route::resource('/admin/concejales', ConcejalController::class)
        ->except(['index', 'create'])
        ->names('admin.concejales')
        ->parameters(['concejales' => 'concejal']);

    /*  Extra: activar/desactivar y borrado masivo  */
    Route::patch ('/admin/concejales/{concejal}/toggle-status',   [ConcejalController::class,'toggleStatus'])   ->name('admin.concejales.toggle-status');
    Route::delete('/admin/concejales/destroy-multiple',           [ConcejalController::class,'destroyMultiple'])->name('admin.concejales.destroy-multiple');
    Route::get   ('/admin/concejales/{concejal}/edit-data',       [ConcejalController::class,'edit'])           ->name('admin.concejales.edit-data');
});

/* ╔════════════════════════════╗
   ║   CONCEJAL / LÍDER HOME    ║
   ╚════════════════════════════╝ */
Route::middleware(['auth','can:crear lideres'])->get('/homeConcejal', [ConcejalController::class,'home'])->name('homeConcejal');
Route::middleware(['auth','can:ingresar votantes'])->get('/homeLider', [UserController::class,'homeLider'])->name('homeLider');

/* ╔════════════════════════════╗
   ║          FALLBACK          ║
   ╚════════════════════════════╝ */
Route::fallback(function () {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
    }
    abort(404);
});
