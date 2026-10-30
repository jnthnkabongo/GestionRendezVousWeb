<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Partager les notifications globalement dans toutes les vues
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $now = Carbon::now();

                // Rappels de rendez-vous uniquement (non terminés ni annulés)
                $notifications = Notification::where('user_id', $user->id)
                    ->where('type', 'rappel_rendez_vous')
                    ->where(function ($query) use ($now) {
                        $query->whereNull('date_envoi')
                            ->orWhere('date_envoi', '<=', $now);
                    })
                    ->whereHas('rendezVous', function ($query) {
                        $query->where('rappel_active', true)
                            ->where('statut', '!=', 'termine')
                            ->where('statut', '!=', 'annule');
                    })
                    ->with('rendezVous')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $unreadCount = Notification::where('user_id', $user->id)
                    ->where('lue', false)
                    ->where('type', 'rappel_rendez_vous')
                    ->where(function ($query) use ($now) {
                        $query->whereNull('date_envoi')
                            ->orWhere('date_envoi', '<=', $now);
                    })
                    ->whereHas('rendezVous', function ($query) {
                        $query->where('rappel_active', true)
                            ->where('statut', '!=', 'termine')
                            ->where('statut', '!=', 'annule');
                    })
                    ->count();

                $view->with(compact('notifications', 'unreadCount'));
            }
        });
    }
}
