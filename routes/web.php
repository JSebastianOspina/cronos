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
})->middleware(['auth']);

Route::get('/dashboard', function () {

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard')->middleware(['auth']);


// RUTAS DE USUARIOS

Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index')->middleware(['auth']);
Route::post('users/roles', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.roles.update')->middleware(['auth']);


// -----------------------------------------  RUTAS DE DEPENDENCIAS -----------------------------------------------
//Gestion global de dependencias
Route::get('dependencies', [\App\Http\Controllers\DependencyController::class, 'index'])->name('dependencies.index')->middleware(['auth']);
Route::post('dependencies', [\App\Http\Controllers\DependencyController::class, 'store'])->name('dependencies.store')->middleware(['auth']);
Route::delete('dependencies/{dependency}', [\App\Http\Controllers\DependencyController::class, 'destroy'])->name('dependencies.destroy')->middleware(['auth']);

//Gestion de usuario dentro de la dependencia
Route::get('/dependencies/{dependency}/users', [\App\Http\Controllers\DependencyController::class, 'usersIndex'])
    ->name('dependencies.users.index')->middleware(['auth']);
Route::post('/dependencies/{dependency}/users', [\App\Http\Controllers\DependencyController::class, 'usersStore'])
    ->name('dependencies.users.store')->middleware(['auth']);
Route::delete('/dependencies/{dependency}/users/{user}', [\App\Http\Controllers\DependencyController::class, 'usersDelete'])
    ->name('dependencies.users.destroy')->middleware(['auth']);

// -----------------------------------------  ACABA RUTAS DE DEPENDENCIAS -------------------------------------------

// -----------------------------------------  RUTAS DE SCHEDULES ----------------------------------------------------
Route::get('users/{user}/schedules', [\App\Http\Controllers\ScheduleController::class, 'userSchedules'])->name('users.schedules.show')->middleware(['auth']);
Route::get('createEvent', [\App\Http\Controllers\ScheduleController::class, 'handleEventCreation'])->middleware(['auth']);
Route::post('users/{user}/schedules', [\App\Http\Controllers\ScheduleController::class, 'StoreUserSchedule'])->name('users.schedules.store')->middleware(['auth']);

//Borrar un Schedule
Route::delete('schedules/{schedule}', [\App\Http\Controllers\ScheduleController::class, 'destroy'])->name('schedules.destroy')->middleware(['auth']);

// -----------------------------------------  ACABA RUTAS DE SCHEDULES -----------------------------------------------


// -----------------------------------------   RUTAS DE CHECK IN - OUT -----------------------------------------------

Route::get('check', [\App\Http\Controllers\RecordController::class, 'getActiveRecords'])->name('check.getActiveRecords')->middleware(['auth']);
// -----------------------------------------  ACABA CHECK IN - OUT -----------------------------------------------

// RUTAS DE MONITORES

Route::get('monitors', [\App\Http\Controllers\MonitorController::class, 'index'])->name('monitors.index')->middleware(['auth']);

// RUTAS DE REPORTES


Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index')->middleware(['auth']);


require __DIR__ . '/auth.php';
