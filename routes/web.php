<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// RUTAS DE USUARIOS

Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
Route::post('users/roles', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.roles.update');


// -----------------------------------------  RUTAS DE DEPENDENCIAS -----------------------------------------------
//Gestion global de dependencias
Route::get('dependencies', [\App\Http\Controllers\DependencyController::class, 'index'])->name('dependencies.index');
Route::post('dependencies', [\App\Http\Controllers\DependencyController::class, 'store'])->name('dependencies.store');
Route::delete('dependencies/{dependency}', [\App\Http\Controllers\DependencyController::class, 'destroy'])->name('dependencies.destroy');

//Gestion de usuario dentro de la dependencia
Route::get('/dependencies/{dependency}/users', [\App\Http\Controllers\DependencyController::class, 'usersIndex'])
    ->name('dependencies.users.index');
Route::post('/dependencies/{dependency}/users', [\App\Http\Controllers\DependencyController::class, 'usersStore'])
    ->name('dependencies.users.store');
Route::delete('/dependencies/{dependency}/users/{user}', [\App\Http\Controllers\DependencyController::class, 'usersDelete'])
    ->name('dependencies.users.destroy');

// -----------------------------------------  ACABA RUTAS DE DEPENDENCIAS -------------------------------------------

// -----------------------------------------  RUTAS DE SCHEDULES ----------------------------------------------------
Route::get('users/{user}/schedules', [\App\Http\Controllers\ScheduleController::class, 'userSchedules'])->name('users.schedules.show');
Route::post('users/{user}/schedules', [\App\Http\Controllers\ScheduleController::class, 'StoreUserSchedule'])->name('users.schedules.store');


// -----------------------------------------  ACABA RUTAS DE SCHEDULES -----------------------------------------------

// RUTAS DE MONITORES

Route::get('monitors', [\App\Http\Controllers\MonitorController::class, 'index'])->name('monitors.index');

// RUTAS DE MONITORES

Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');


require __DIR__ . '/auth.php';
