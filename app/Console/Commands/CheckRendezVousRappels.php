<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\RendezVous;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('app:check-rendez-vous-rappels')]
#[Description('Vérifie et envoie les rappels de rendez-vous')]
class CheckRendezVousRappels extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Heure actuelle: " . $now->toDateTimeString());

        // Récupérer les rendez-vous qui ont un rappel actif et qui n'ont pas encore de notification de rappel envoyée
        $rendezVous = RendezVous::where('rappel_active', true)
            ->where('statut', 'planifie')
            ->where('date_rendez_vous', '>=', $now->toDateString())
            ->get();

        $this->info("Rendez-vous avec rappel actif trouvés: " . $rendezVous->count());

        $rappelsEnvoyes = 0;

        foreach ($rendezVous as $rdv) {
            // Récupérer la notification de rappel associée
            $notification = Notification::where('rendez_vous_id', $rdv->id)
                ->where('type', 'rappel_rendez_vous')
                ->first();

            if (!$notification) {
                $this->warn("Aucune notification trouvée pour le RDV #{$rdv->id}");
                continue;
            }

            // Si déjà envoyé, passer
            if ($notification->envoye) {
                $this->info("Rappel déjà envoyé pour le RDV #{$rdv->id}");
                continue;
            }

            $rappelTime = Carbon::parse($notification->date_envoi);
            $rdvDateTime = Carbon::parse($rdv->date_rendez_vous)->setTimeFromTimeString($rdv->heure_debut);
            $this->info("RDV #{$rdv->id}: {$rdv->objet} - Heure rappel: " . $rappelTime->toDateTimeString() . " - Diff: " . $now->diffInMinutes($rappelTime, false) . " min");

            // Vérifier si on est dans la fenêtre de rappel (±1 minute pour éviter les doubles)
            if ($now->diffInMinutes($rappelTime, false) >= -1 && $now->diffInMinutes($rappelTime, false) <= 1) {
                // Récupérer le chef du rendez-vous ou l'administrateur
                $user = $rdv->chef ?? User::whereHas('role', function($query) {
                    $query->where('name', 'administrateur');
                })->first();

                if ($user) {
                    $this->info("Utilisateur trouvé: {$user->name} - OneSignal ID: " . ($user->onesignal_id ?? 'N/A'));

                    // Envoyer la notification OneSignal si l'utilisateur a un onesignal_id
                    if ($user->onesignal_id) {
                        $this->sendOneSignalNotification(
                            $user->onesignal_id,
                            'MY2K',
                            "Excellence Kally vous avez un Rendez-vous \"{$rdv->objet}\" avec {$rdv->client->nom} {$rdv->client->prenom} commence dans " . $rappelTime->diffInMinutes($rdvDateTime, false) . " minutes."
                        );
                    } else {
                        $this->warn("Aucun onesignal_id pour l'utilisateur {$user->name}");
                    }

                    // Marquer comme envoyé
                    $notification->update(['envoye' => true]);

                    $rappelsEnvoyes++;
                } else {
                    $this->warn("Aucun utilisateur trouvé pour le rendez-vous #{$rdv->id}");
                }
            }
        }

        if ($rappelsEnvoyes > 0) {
            $this->info("{$rappelsEnvoyes} rappel(s) envoyé(s) avec succès.");
        } else {
            $this->info('Aucun rappel à envoyer.');
        }

        return Command::SUCCESS;
    }

    /**
     * Envoyer une notification OneSignal via l'endpoint interne
     */
    private function sendOneSignalNotification($onesignalId, $title, $message)
    {
        try {
            $apiUrl = 'https://my2kmining.alwaysdata.net/api/notifications/onesignal/send-by-onesignal-id';

            $this->info("Tentative d'envoi à: " . $apiUrl);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, [
                'onesignal_id' => $onesignalId,
                'title' => $title,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                $this->error('Erreur lors de l\'envoi de la notification OneSignal: ' . $response->body());
            } else {
                $this->info('Notification OneSignal envoyée avec succès pour ' . $onesignalId);
            }
        } catch (\Exception $e) {
            $this->error('Exception lors de l\'envoi de la notification OneSignal: ' . $e->getMessage());
        }
    }
}
