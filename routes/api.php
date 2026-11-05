<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/notifications/firebase/send', [NotificationController::class, 'send']);
Route::post('/notifications/onesignal/send', [NotificationController::class, 'sendOneSignal']);
Route::post('/notifications/onesignal/send-all', [NotificationController::class, 'sendOneSignalToAll']);
Route::post('/notifications/onesignal/send-by-onesignal-id', [NotificationController::class, 'sendByOnesignalId']);


Route::post('login', [AuthController::class, 'loginApi']);
Route::post('logout', [AuthController::class, 'logoutApi']);

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('dashboard-api', [DashboardController::class, 'getDashboardApi']);

    // Notifications
    Route::get('notifications', [NotificationsController::class, 'listeNotifications']);

    // OneSignal
    Route::post('update-onesignal-id', [AuthController::class, 'updateOnesignalId']);

    // Rendez-vous
    Route::get('rendez-vous', [RendezVousController::class, 'listeRendezVousApi']);
    Route::get('rendez-vous/planifier', [RendezVousController::class, 'listeRendezVousPlanifierApi']);
    Route::get('rendez-vous/terminer', [RendezVousController::class, 'listeRendezVousTerminerApi']);
    Route::get('rendez-vous/{id}', [RendezVousController::class, 'detailRendezVousApi']);
    Route::post('rendez-vous', [RendezVousController::class, 'creationRendezVousApi']);
    Route::put('rendez-vous/{id}', [RendezVousController::class, 'modificationRendezVousApi']);
    Route::delete('rendez-vous/{id}', [RendezVousController::class, 'suppressionRendezVousApi']);

    // Clients
    Route::get('clients', [ClientsController::class, 'apiIndex']);
    Route::post('clients', [ClientsController::class, 'apiStore']);
    Route::put('clients/{id}', [ClientsController::class, 'apiUpdate']);
    Route::delete('clients/{id}', [ClientsController::class, 'apiDestroy']);

    // Utilisateurs
    Route::get('utilisateurs', [UserController::class, 'apiIndex']);
    Route::get('utilisateurs/roles', [UserController::class, 'apiRoles']);
    Route::post('utilisateurs', [UserController::class, 'apiStore']);
    Route::put('utilisateurs/{id}', [UserController::class, 'apiUpdate']);
    Route::delete('utilisateurs/{id}', [UserController::class, 'apiDestroy']);

});
