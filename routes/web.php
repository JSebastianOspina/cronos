<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

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

Route::get('/', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'handleRoleRedirect'])->middleware(['auth'])->name('index');


//Ruta general para capturar errores
Route::inertia('error', 'Error')->name('error');


/*---------------------------------------  RUTAS PARA ROL ADMINISTRADOR -------------------------------------*/

/* GESTION DE USUARIOS Y ROLES */

// Mostrar usuarios y sus roles
Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index')->middleware(['auth', 'isAdmin']);
// Actualizar el rol de un usuario
Route::post('users/roles', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.roles.update')->middleware(['auth', 'isAdmin']);


/* GESTION GLOBAL DE DEPENDENCIAS */

//Crear dependencia
Route::post('dependencies', [\App\Http\Controllers\DependencyController::class, 'store'])->name('dependencies.store')->middleware(['auth']);
//Borrar dependencia
Route::delete('dependencies/{dependency}', [\App\Http\Controllers\DependencyController::class, 'destroy'])->name('dependencies.destroy')->middleware(['auth']);

/*-----------------------------------  ACABA RUTAS PARA ROL ADMINISTRADOR -------------------------------------*/


/*---------------------------------------  RUTAS ROL SUPERVISORES  --------------------------------------*/

/*-----------> Rutas dependencias <-------------*/

//Gestion global de dependencias
Route::get('dependencies', [\App\Http\Controllers\DependencyController::class, 'index'])->name('dependencies.index')->middleware(['auth', 'isSupervisor']);
//Ver usuarios pertenencientes a la dependencia
Route::get('/dependencies/{dependency}/users', [\App\Http\Controllers\DependencyController::class, 'usersIndex'])
    ->name('dependencies.users.index')->middleware(['auth', 'isSupervisor']);
//Crear usuarios dentro de la dependencia (incluidos otros supervisores)
Route::post('/dependencies/{dependency}/users', [\App\Http\Controllers\DependencyController::class, 'usersStore'])
    ->name('dependencies.users.store')->middleware(['auth', 'isSupervisor']);
//Eliminar un usuario de la dependencia (y su calendario asociado)
Route::delete('/dependencies/{dependency}/users/{user}', [\App\Http\Controllers\DependencyController::class, 'usersDelete'])
    ->name('dependencies.users.destroy')->middleware(['auth', 'isSupervisor']);

/*-----------> Rutas de Schedules <-------------*/

//Ver agendas de un usuario
Route::get('users/{user}/dependency/{dependency}/schedules', [\App\Http\Controllers\ScheduleController::class, 'userSchedules'])->name('users.schedules.show')->middleware(['auth', 'isSupervisor']);
// Crear un evento (agenda) a un usuario
Route::post('users/{user}/schedules', [\App\Http\Controllers\ScheduleController::class, 'StoreUserSchedule'])->name('users.schedules.store')->middleware(['auth', 'isSupervisor']);
//Borrar un evento
Route::delete('schedules/{schedule}', [\App\Http\Controllers\ScheduleController::class, 'destroy'])->name('schedules.destroy')->middleware(['auth', 'isSupervisor']);

/*-----------> Rutas de records <-------------*/

//Show filter dependency records view
Route::get('records/dependencies/{dependency}', [\App\Http\Controllers\RecordController::class, 'filterDependencyRecordsView'])->name('records.filter')->middleware(['auth', 'isSupervisor']);
//Json con records segun fechas
Route::get('api/records/dependencies/{dependency}', [\App\Http\Controllers\RecordController::class, 'filterDependencyRecords'])->name('api.records.filter')->middleware(['auth', 'isSupervisor']);
//Descargar records de usuario según dependencia
Route::get('records/dependencies/{dependencyId}/user/{userId}/download', [\App\Http\Controllers\RecordController::class, 'downloadUserDependencyRecords'])->name('downloadUserDependencyRecords')->middleware(['auth']);
//Actualizar horas de administrador
Route::patch('records/{record}', [\App\Http\Controllers\RecordController::class, 'updateSupervisorHour'])->name('records.updateSupervisorHour')->middleware(['auth', 'isSupervisor']);
//Cancelar monitoria
Route::post('records/{record}/cancelMonitorHours', [\App\Http\Controllers\RecordController::class, 'cancelMonitorHours'])->name('records.cancelMonitorHours')->middleware(['auth', 'isSupervisor']);
//Añadir observación
Route::post('records/{record}/observations', [\App\Http\Controllers\RecordController::class, 'makeObservation'])->name('records.observations.store')->middleware(['auth', 'isSupervisor']);


/*---------------------------------------  ACABA ROL SUPERVISORES  -------------------------------------*/


/*---------------------------------------  RUTAS ROL USUARIO -----------------------------------------*/

/*-----------> Rutas de Check In - Out <-------------*/

//Api, traer monitorias activas
Route::get('api/getActiveRecords', [\App\Http\Controllers\RecordController::class, 'getActiveRecords'])->name('check.getActiveRecords')->middleware(['auth']);
//Hacer check in o checkout en una monitoria activa
Route::post('api/makeCheckInOrCheckout', [\App\Http\Controllers\RecordController::class, 'makeCheckInOrCheckout'])->name('check.makeCheckInOrCheckout')->middleware(['auth']);
// Vista para desplegar info del api (monitorias activas)
Route::get('check', [\App\Http\Controllers\RecordController::class, 'showCheckInOutView'])->name('check.showCheckInOutView')->middleware(['auth']);

/*-----------> Rutas de monitores <-------------*/

// API, Obtener calendarios monitor (JSON)
Route::get('api/monitors/{monitor}/calendars', [\App\Http\Controllers\MonitorController::class, 'getUserCalendars'])->name('api.monitors.getUserCalendars')->middleware(['auth']);
//Vista calendarios monitor (enlace de calendar)
Route::get('monitors/{monitor}/calendars', [\App\Http\Controllers\MonitorController::class, 'showUserCalendars'])->name('monitors.calendars')->middleware(['auth']);

/*-----------> Rutas de monitores <-------------*/


/*---------------------------------  ACABA RUTAS ROL USUARIO ----------------------------*/


/*-----------> Rutas de records <-------------*/

//Crea records apartir de schedules que sean periódicos
Route::get('createPeriodicRecords', [\App\Http\Controllers\RecordController::class, 'createPeriodicRecords'])->name('createPeriodicRecords');


// RUTAS DE REPORTES
Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index')->middleware(['auth']);

//Generate authorization URL for connection Google APP
Route::get('authorize', [\App\Http\Controllers\GoogleCalendarController::class, 'generateAuthenticateUrl'])->name('generateAuthenticateUrl');
//Handling response from google cloud (Oauth2)
Route::get('authorize/callback', [\App\Http\Controllers\GoogleCalendarController::class, 'handleGoogleCallback'])->name('generateAuthenticateUrl');

Route::get('/google/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('googleLogin');

Route::get('/google/callback', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'handleGoogleCallback']);

require __DIR__ . '/auth.php';
