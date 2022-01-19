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


// RUTAS DE DEPENDENCIAS

Route::get('dependencies', [\App\Http\Controllers\DependencyController::class, 'index'])->name('dependencies.index');


// RUTAS DE MONITORES

Route::get('monitors', [\App\Http\Controllers\MonitorController::class, 'index'])->name('monitors.index');

// RUTAS DE MONITORES

Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');


require __DIR__ . '/auth.php';
