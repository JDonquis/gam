<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CensusController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\IncidenceController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\SanctionController;

Route::middleware('guest')->group(function () {

    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/forgot-password', [LoginController::class, 'forgotPassword'])->name('password.recover');
});

Route::middleware('auth')->prefix('admin')->group(function () {


    // ----------------------------               ---------------------------------------
    // ---------------------------- NOTIFICATIONS ---------------------------------------
    // ----------------------------               ---------------------------------------

    Route::post('/notificaciones/{notification}/marcar-leida', function ($notification) {
        $notification = auth()->user()->notifications()->find($notification);

        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    })->name('notifications.markAsRead');

    Route::post('/notificaciones/marcar-todas-leidas-hoy', function () {
        auth()->user()->notifications()
            ->whereDate('created_at', today())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    })->name('notifications.markAllAsReadToday');

    // ----------------------------         ---------------------------------------
    // ---------------------------- PROFILE ---------------------------------------
    // ----------------------------         ---------------------------------------

    Route::get('/', [AppController::class, 'index'])->name('dashboard');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('mi-perfil', [UserController::class, 'profile'])->name('profile');
    Route::get('mi-perfil/actualizar', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('mi-perfil/actualizar', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::delete('mi-perfil/eliminar', [UserController::class, 'destroyProfile'])->name('profile.destroy');

    // ----------------------------         ---------------------------------------
    // ---------------------------- USERS ---------------------------------------
    // ----------------------------         ---------------------------------------

    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('/usuarios/perfil/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/usuarios/crear', [UserController::class, 'store'])->name('users.store');
    Route::get('/usuarios/crear', [UserController::class, 'create'])->name('users.create');
    Route::get('/usuarios/perfil/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/usuarios/perfil/{user}/editar', [UserController::class, 'update'])->name('users.update');
    Route::delete('/usuarios/pefil/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // ----------------------------         ---------------------------------------
    // ---------------------------- DOCTORS ---------------------------------------
    // ----------------------------         ---------------------------------------

    Route::get('/medicos', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/medicos/ver/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');
    Route::get('/medicos/crear', [DoctorController::class, 'create'])->name('doctors.create');
    Route::get('/medicos/ver/{doctor}/editar', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('/medicos/ver/{doctor}/editar', [DoctorController::class, 'update'])->name('doctors.update');
    Route::delete('/medicos/ver/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    Route::post('/medicos/crear', [DoctorController::class, 'store'])->name('doctors.store');


    // ----------------------------                     ---------------------------------------
    // ----------------------------   CONFIGURACION   ---------------------------------------
    // ----------------------------                     ---------------------------------------

    Route::get('/configuracion', [ConfigurationController::class, 'index'])->name('configuration.index');
    Route::get('/configuracion/crear', [ConfigurationController::class, 'create'])->name('configuration.create');
    Route::post('/configuracion/crear', [ConfigurationController::class, 'store'])->name('configuration.store');
    Route::get('/configuracion/ver/{config}/editar', [ConfigurationController::class, 'edit'])->name('configuration.edit');
    Route::put('/configuracion/ver/{config}/editar', [ConfigurationController::class, 'update'])->name('configuration.update');
    Route::delete('/configuracion/ver/{config}', [ConfigurationController::class, 'destroy'])->name('configuration.destroy');




    // ----------------------------                     ---------------------------------------
    // ---------------------------- REGISTROS DE CENSOS ---------------------------------------
    // ----------------------------                     ---------------------------------------

    Route::get('/censos', [CensusController::class, 'index'])->name('census.index');
    Route::get('/censos/crear', [CensusController::class, 'create'])->name('census.create');
    Route::get('/censos/ver/{census}', [CensusController::class, 'show'])->name('census.show');
    Route::post('/censos/crear', [CensusController::class, 'store'])->name('census.store');
    Route::post('/censos/crear/preview', [CensusController::class, 'preview'])->name('census.preview');
    Route::delete('/censos/ver/{census}', [CensusController::class, 'destroy'])->name('census.destroy');
    Route::get('/censos/descargar/{census}', [CensusController::class, 'download'])->name('census.download');
    Route::post('/census/progress', [CensusController::class, 'progress'])->name('census.progress');

    // ----------------------------            ---------------------------------------
    // ---------------------------- INCIDENCES ---------------------------------------
    // ----------------------------            ---------------------------------------

    Route::get('/incidencias', [IncidenceController::class, 'index'])->name('incidences.index');
    Route::get('/incidencias/ver/{incidence}', [IncidenceController::class, 'show'])->name('incidences.show');
    Route::get('/incidencias/crear', [IncidenceController::class, 'create'])->name('incidences.create');
    Route::put('/incidencias/ver/{incidence}/editar', [IncidenceController::class, 'update'])->name('incidences.update');
    Route::delete('/incidencias/ver/{incidence}', [IncidenceController::class, 'destroy'])->name('incidences.destroy');
    Route::get('/incidencias/todos', [IncidenceController::class, 'destroyAll'])->name('incidences.destroy.all');
    Route::post('/incidencias/crear', [IncidenceController::class, 'store'])->name('incidences.store');

    // ----------------------------            ---------------------------------------
    // ---------------------------- SANCTIONS ---------------------------------------
    // ----------------------------            ---------------------------------------
    Route::get('/sanciones', [SanctionController::class, 'index'])->name('sanctions.index');
    Route::post('/sanciones', [SanctionController::class, 'store'])->name('sanctions.store');
    Route::get('/sanciones/ver/{sanction}/editar', [SanctionController::class, 'edit'])->name('sanctions.edit');
    Route::put('/sanciones/ver/{sanction}/editar', [SanctionController::class, 'update'])->name('sanctions.update');
    Route::delete('/sanciones/ver/{incidence}', [SanctionController::class, 'destroy'])->name('sanctions.destroy');
});
