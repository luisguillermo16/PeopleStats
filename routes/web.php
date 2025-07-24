<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlcaldeController;
use App\Http\Controllers\ConcejalController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\VotanteController;
use App\Http\Controllers\DashboardController;  // <-- Aquí importamos el controlador nuevo
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Redirect raíz
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));


/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/
Route::get ('/login',            [LoginController::class,'index' ])->name('login');
Route::post('/inicia-sesion',    [LoginController::class,'login' ])->name('inicia-sesion');
Route::post('/validar-registro', [LoginController::class,'registro'])->name('validar-registro');
Route::post('/logout',           [LoginController::class,'logout' ])->name('logout');
Route::post('/check-email',      [LoginController::class,'checkEmail'])->name('check-email');


/*
|--------------------------------------------------------------------------
| SUPER-ADMIN
|--------------------------------------------------------------------------
| Permiso: acceder admin
| Gestiona todos los usuarios del sistema.
*/
Route::middleware(['auth', 'can:acceder admin'])->group(function () {
    Route::get('/admin', [UserController::class, 'index'])->name('admin');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
});


/*
|--------------------------------------------------------------------------
| ALCALDE (gestiona Concejales)
|--------------------------------------------------------------------------
| Permiso: crear concejales
| Desde aquí el alcalde crea y administra sus concejales.
*/
Route::middleware(['auth','can:crear concejales'])->group(function () {

    // Dashboard del alcalde
    Route::get('/dashboardAlcalde', [AlcaldeController::class,'index'])->name('dashboardAlcalde');
    
    // Vista tabla de concejales
    Route::get('/crearConcejal',    [ConcejalController::class,'index'])->name('crearConcejal');

    // Model binding seguro: {concejal} siempre será un User con rol aspirante-concejo
    Route::bind('concejal', function ($value) {
        $user = User::with('concejal')->find($value);
        if (!$user || !$user->hasRole('aspirante-concejo')) abort(404, 'Usuario no válido como concejal');
        return $user;
    });

    // CRUD concejales (sin index/create porque ya usamos /crearConcejal)
    Route::post  ('/admin/concejales',              [ConcejalController::class,'store'  ])->name('admin.concejales.store');
    Route::put   ('/admin/concejales/{concejal}',   [ConcejalController::class,'update' ])->name('admin.concejales.update');
    Route::delete('/admin/concejales/{concejal}',   [ConcejalController::class,'destroy'])->name('admin.concejales.destroy');

    // Extras
    Route::patch ('/admin/concejales/{concejal}/toggle-status', [ConcejalController::class,'toggleStatus'])->name('admin.concejales.toggle-status');
    Route::delete('/admin/concejales/destroy-multiple',         [ConcejalController::class,'destroyMultiple'])->name('admin.concejales.destroy-multiple');
    Route::get   ('/admin/concejales/{concejal}/edit-data',     [ConcejalController::class,'edit'])->name('admin.concejales.edit-data');

    // ===== RUTAS DEL DASHBOARD =====
    // Card votantes alcalde (vista completa)
    Route::get('/dashboard/card-votantes-alcalde', [DashboardController::class, 'cardVotantesAlcalde'])->name('dashboard.cardVotantesAlcalde');
    
    // Datos AJAX para actualización en tiempo real
    Route::get('/dashboard/votantes-data-ajax', [DashboardController::class, 'votantesDataAjax'])->name('dashboard.votantesDataAjax');
});


/*
|--------------------------------------------------------------------------
| CONCEJAL (gestiona Líderes)
|--------------------------------------------------------------------------
| Permiso: crear lideres
| Concejales gestionan líderes; también usamos este grupo para la vista homeConcejal.
*/
Route::middleware(['auth','can:crear lideres'])->group(function () {

    // Home concejal
    Route::get('/homeConcejal',   [ConcejalController::class,'home'])->name('homeConcejal');

    // Vista tabla de líderes
    Route::get('/crearLider',     [LiderController::class, 'index'])->name('crearLider');

    // Model binding seguro: {lider} = User con rol lider
    Route::bind('lider', function ($value) {
        $user = User::with('votantesRegistrados')->find($value); // cargamos algo útil; ajusta si tienes relación lider
        if (!$user || !$user->hasRole('lider')) abort(404, 'Usuario no válido como líder');
        return $user;
    });

    // CRUD líderes
    Route::post  ('/lideres',         [LiderController::class, 'store'])->name('admin.lideres.store');
    Route::get   ('/lideres/{lider}', [LiderController::class, 'show'])->name('admin.lideres.show');
    Route::put   ('/lideres/{lider}', [LiderController::class, 'update'])->name('admin.lideres.update');
    Route::delete('/lideres/{lider}', [LiderController::class, 'destroy'])->name('admin.lideres.destroy');

    // Extras líderes
    Route::patch ('/lideres/{lider}/toggle-status', [LiderController::class,'toggleStatus'])->name('admin.lideres.toggle-status');
    Route::delete('/lideres/destroy-multiple',       [LiderController::class,'destroyMultiple'])->name('admin.lideres.destroy-multiple');
});


/*
|--------------------------------------------------------------------------
| LÍDER (gestiona Votantes)
|--------------------------------------------------------------------------
| Permiso: ingresar votantes
| Los líderes registran votantes a su concejal; opcionalmente marcan apoyo al alcalde.
*/
Route::middleware(['auth','can:ingresar votantes'])->group(function () {

    // Home líder
    Route::get('/homeLider', [UserController::class,'homeLider'])->name('homeLider');

    // Listado de votantes del líder autenticado
    Route::get('/ingresarVotantes', [VotanteController::class, 'index'])->name('ingresarVotantes');

    // Formulario para ingresar votantes (esta es la ruta que usas en el sidebar)
    Route::get('/votantes/ingresar', [VotanteController::class, 'create'])->name('votantes.ingresar');

    // Guardar nuevo votante
    Route::post('/votantes', [VotanteController::class, 'store'])->name('votantes.store');

    // Editar / actualizar votante (solo dueño)
    Route::get('/votantes/{votante}/editar', [VotanteController::class, 'edit'])->name('votantes.edit');
    Route::put('/votantes/{votante}',        [VotanteController::class, 'update'])->name('votantes.update');

    // Eliminar votante
    Route::delete('/votantes/{votante}', [VotanteController::class, 'destroy'])->name('votantes.destroy');

    // Buscar votante por cédula (AJAX para validar duplicados o consultar si ya existe en otro concejal)
    Route::get('/buscar-votante', [VotanteController::class, 'buscarPorCedula'])->name('votantes.buscar');
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')
            ->with('error', 'Debes iniciar sesión para acceder a esta página.');
    }
    abort(404);
});