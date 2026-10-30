<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UtilisateursController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('soumission');
Route::get('/register', [AuthController::class, 'create'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard-responsable');

    // Mecaniques de gestion du personnel
    Route::get('/personnels', [UserController::class, 'index'])->name('liste-personnels');
    Route::get('/personnels/create', [UserController::class, 'create'])->name('create-personnel');
    Route::post('/personnels', [UserController::class, 'store'])->name('store-personnel');
    Route::get('personnels/{id}', [UserController::class, 'show'])->name('show-personnel');
    Route::put('/personnels/{id}', [UserController::class, 'update'])->name('update-personnel');
    Route::delete('/personnels/{id}', [UserController::class, 'destroy'])->name('destroy-personnel');

    // Mecanique de gestion des rôles
    Route::get('/roles', [RolesController::class, 'index'])->name('liste-roles');
    Route::post('/roles', [RolesController::class, 'store'])->name('create-role');
    Route::get('/roles/{id}', [RolesController::class, 'show'])->name('show-role');
    Route::put('/roles/{id}', [RolesController::class, 'update'])->name('update-role');
    Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->name('destroy-role');

    // Mecanisuqes de gestion des invités
    Route::get('/invites', [ClientsController::class, 'index'])->name('liste-invites');
    // Route::get('/dashboard/invites/create', [ClientsController::class, 'create'])->name('create-invite');
    Route::post('/invites', [ClientsController::class, 'store'])->name('store-invite');
    Route::get('/invites/{id}', [ClientsController::class, 'show'])->name('show-invite');
    Route::put('/invites/{id}', [ClientsController::class, 'update'])->name('update-invite');
    Route::delete('/invites/{id}', [ClientsController::class, 'destroy'])->name('destroy-invite');

    // Mecaniques de gestion des rendez-vous
    Route::get('/rendez-vous', [RendezVousController::class, 'index'])->name('liste-rendez-vous');
    Route::post('/rendez-vous', [RendezVousController::class, 'store'])->name('rendez-vous.store');
    Route::put('/rendez-vous/{id}', [RendezVousController::class, 'update'])->name('rendez-vous.update');
    Route::delete('/rendez-vous/{id}', [RendezVousController::class, 'destroy'])->name('rendez-vous.destroy');
    Route::delete('/rendez-vous', [RendezVousController::class, 'destroyAll'])->name('rendez-vous.destroyAll');


    // Mecaniques de gestion des notifications
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('liste-notifications');
    Route::get('/api/notifications', [DashboardController::class, 'getNotificationsApi'])->name('api.notifications');

});

