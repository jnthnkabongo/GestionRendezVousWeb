<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Client;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RendezVousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $liste_rendez_vous = RendezVous::with('client')->orderBy('created_at', 'desc')->paginate(4);
        $clients = Client::all();
        $users = User::all();
        return view('pages.responsable.rendez-vous', compact('liste_rendez_vous', 'clients', 'users'));
    }

    /**
     * API: Get all rendez-vous
     */
    public function apiIndex()
    {
        $rendezVous = RendezVous::with(['client', 'chef'])
            ->orderBy('date_rendez_vous', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rendezVous,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'objet' => 'required|string|max:255',
            'date_rendez_vous' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required',
            'client_id' => 'required|exists:clients,id',
            'statut' => 'nullable|in:planifie,confirme,termine,annule',
            'type_rendez_vous' => 'nullable|in:interieur,exterieur',
            'description' => 'nullable|string',
            'chef_id' => 'nullable|exists:users,id',
            'rappel_active' => 'nullable|boolean',
            'rappel_minutes' => 'nullable|integer|min:1',
        ]);

        if (Auth::check()) {
            $validated['cree_par'] = Auth::id();
        }
        $validated['chef_id'] = $validated['chef_id'] ?? (Auth::check() ? Auth::id() : null);
        $validated['statut'] = $validated['statut'] ?? 'planifie';
        $validated['rappel_active'] = $request->has('rappel_active');
        $validated['rappel_minutes'] = $validated['rappel_minutes'] ?? 30;

        $rendezVous = RendezVous::create($validated);

        // Créer une notification de rappel programmée si activé
        if ($rendezVous->rappel_active) {
            // Charger le client pour éviter l'erreur de relation
            $rendezVous->load('client');

            // Calculer l'heure du rappel
            $rappelMinutes = $rendezVous->rappel_minutes ?? 30;
            $rdvDateTime = Carbon::parse($rendezVous->date_rendez_vous)->setTimeFromTimeString($rendezVous->heure_debut);
            $rappelTime = $rdvDateTime->copy()->subMinutes($rappelMinutes);

            try {
                Notification::create([
                    'user_id' => $rendezVous->chef_id,
                    'rendez_vous_id' => $rendezVous->id,
                    'titre' => 'Rappel: ' . $rendezVous->objet,
                    'message' => 'Rendez-vous avec ' . ($rendezVous->client ? $rendezVous->client->nom : 'un client') . ' prévu le ' . $rendezVous->date_rendez_vous . ' à ' . $rendezVous->heure_debut,
                    'type' => 'rappel_rendez_vous',
                    'lue' => false,
                    'envoye' => false,
                    'date_envoi' => $rappelTime,
                ]);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la création de la notification: ' . $e->getMessage());
            }
        }

        return redirect()->route('liste-rendez-vous')->with('success', 'Rendez-vous créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rendezVous = RendezVous::findOrFail($id);

        $validated = $request->validate([
            'objet' => 'required|string|max:255',
            'date_rendez_vous' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required',
            'statut' => 'nullable|in:planifie,confirme,termine,annule',
            'type_rendez_vous' => 'nullable|in:interieur,exterieur',
            'chef_id' => 'nullable|exists:users,id',
            'rappel_active' => 'nullable|boolean',
            'rappel_minutes' => 'nullable|integer|min:1',
        ]);

        $validated['chef_id'] = $validated['chef_id'] ?? $rendezVous->chef_id;
        $validated['rappel_active'] = $request->has('rappel_active');
        $validated['rappel_minutes'] = $validated['rappel_minutes'] ?? 30;

        $rendezVous->update($validated);

        // Mettre à jour ou créer la notification de rappel si activé
        if ($rendezVous->rappel_active) {
            // Charger le client pour éviter l'erreur de relation
            $rendezVous->load('client');

            // Calculer l'heure du rappel
            $rappelMinutes = $rendezVous->rappel_minutes ?? 30;
            $rdvDateTime = Carbon::parse($rendezVous->date_rendez_vous)->setTimeFromTimeString($rendezVous->heure_debut);
            $rappelTime = $rdvDateTime->copy()->subMinutes($rappelMinutes);

            // Vérifier si une notification existe déjà
            $notificationExistante = Notification::where('rendez_vous_id', $rendezVous->id)
                ->where('type', 'rappel_rendez_vous')
                ->first();

            if ($notificationExistante) {
                // Mettre à jour la notification existante
                $notificationExistante->update([
                    'titre' => 'Rappel: ' . $rendezVous->objet,
                    'message' => 'Rendez-vous avec ' . ($rendezVous->client ? $rendezVous->client->nom : 'un client') . ' prévu le ' . $rendezVous->date_rendez_vous . ' à ' . $rendezVous->heure_debut,
                    'date_envoi' => $rappelTime,
                    'envoye' => false,
                ]);
            } else {
                // Créer une nouvelle notification
                try {
                    Notification::create([
                        'user_id' => $rendezVous->chef_id,
                        'rendez_vous_id' => $rendezVous->id,
                        'titre' => 'Rappel: ' . $rendezVous->objet,
                        'message' => 'Rendez-vous avec ' . ($rendezVous->client ? $rendezVous->client->nom : 'un client') . ' prévu le ' . $rendezVous->date_rendez_vous . ' à ' . $rendezVous->heure_debut,
                        'type' => 'rappel_rendez_vous',
                        'lue' => false,
                        'envoye' => false,
                        'date_envoi' => $rappelTime,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de la création de la notification: ' . $e->getMessage());
                }
            }
        } else {
            // Si le rappel est désactivé, supprimer la notification existante
            Notification::where('rendez_vous_id', $rendezVous->id)
                ->where('type', 'rappel_rendez_vous')
                ->delete();
        }

        return redirect()->route('liste-rendez-vous')->with('success', 'Rendez-vous modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rendezVous = RendezVous::findOrFail($id);
        $rendezVous->delete();

        return redirect()->route('liste-rendez-vous')->with('success', 'Rendez-vous supprimé avec succès');
    }

    /**
     * Remove all resources from storage.
     */
    public function destroyAll()
    {
        RendezVous::truncate();

        return redirect()->route('liste-rendez-vous')->with('success', 'Tous les rendez-vous ont été supprimés');
    }


    //////////////////////////////////////////////////////
    public function listeRendezVousApi() {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 401);
        }
        
        $rendezVous = RendezVous::with('chef','client','createur','rappels','notifications')->where('statut', 'confirme')->orderBy('id', 'desc')->get();
        return response()->json([
            'message' => 'Liste des rendez-vous',
            'rendez_vous' => $rendezVous
        ], 200);
    }

    public function listeRendezVousPlanifierApi() {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 401);
        }
        
        $rendezVous = RendezVous::with('chef','client','createur','rappels','notifications')->where('statut', 'planifie')->orderBy('id', 'desc')->get();
        return response()->json([
            'message' => 'Liste des rendez-vous planifiés',
            'rendez_vous_planifies' => $rendezVous
        ], 200);
    }

     public function listeRendezVousTerminerApi() {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 401);
        }
        
        $rendezVous = RendezVous::with('chef','client','createur','rappels','notifications')->where('statut', 'termine')->orderBy('id', 'desc')->get();
        return response()->json([
            'message' => 'Liste des rendez-vous terminés',
            'rendez_vous_termines' => $rendezVous
        ], 200);
    }

    public function creationRendezVousApi(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 401);
        }

        $validated = $request->validate([
            'objet' => 'required|string|max:255',
            'date_rendez_vous' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required',
            'client_id' => 'nullable|exists:clients,id',
            'statut' => 'nullable|in:planifie,confirme,termine,annule',
            'type_rendez_vous' => 'nullable|in:interieur,exterieur',
            'description' => 'nullable|string',
            'chef_id' => 'nullable|exists:users,id',
            'rappel_active' => 'nullable|boolean',
            'rappel_minutes' => 'nullable|integer|min:1',
        ]);

        $validated['cree_par'] = $user->id;
        $validated['chef_id'] = $validated['chef_id'] ?? $user->id;
        $validated['statut'] = $validated['statut'] ?? 'planifie';
        $validated['rappel_active'] = $request->has('rappel_active');
        $validated['rappel_minutes'] = $validated['rappel_minutes'] ?? 30;

        $rendezVous = RendezVous::create($validated);

        // Créer une notification de rappel si activé
        if ($rendezVous->rappel_active) {
            // Charger le client pour éviter l'erreur de relation
            $rendezVous->load('client');

            // Calculer l'heure du rappel
            $rappelMinutes = $rendezVous->rappel_minutes ?? 30;
            $rdvDateTime = Carbon::parse($rendezVous->date_rendez_vous)->setTimeFromTimeString($rendezVous->heure_debut);
            $rappelTime = $rdvDateTime->copy()->subMinutes($rappelMinutes);

            try {
                Notification::create([
                    'user_id' => $rendezVous->chef_id,
                    'rendez_vous_id' => $rendezVous->id,
                    'titre' => 'Rappel: ' . $rendezVous->objet,
                    'message' => 'Rendez-vous avec ' . ($rendezVous->client ? $rendezVous->client->nom : 'un client') . ' prévu le ' . $rendezVous->date_rendez_vous . ' à ' . $rendezVous->heure_debut,
                    'type' => 'rappel_rendez_vous',
                    'lue' => false,
                    'envoye' => false,
                    'date_envoi' => $rappelTime,
                ]);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la création de la notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Rendez-vous créé avec succès',
            'data' => $rendezVous,
        ], 201);
    }

    public function modificationRendezVousApi(Request $request, $id) {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 401);
        }

        $rendezVous = RendezVous::findOrFail($id);

        $validated = $request->validate([
            'objet' => 'required|string|max:255',
            'date_rendez_vous' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required',
            'client_id' => 'nullable|exists:clients,id',
            'statut' => 'nullable|in:planifie,confirme,termine,annule',
            'type_rendez_vous' => 'nullable|in:interieur,exterieur',
            'description' => 'nullable|string',
            'chef_id' => 'nullable|exists:users,id',
            'rappel_active' => 'nullable|boolean',
            'rappel_minutes' => 'nullable|integer|min:1',
        ]);

        $validated['cree_par'] = $user->id;
        $validated['chef_id'] = $validated['chef_id'] ?? $user->id;
        $validated['statut'] = $validated['statut'] ?? 'planifie';
        $validated['rappel_active'] = $request->has('rappel_active');
        $validated['rappel_minutes'] = $validated['rappel_minutes'] ?? 30;

        $rendezVous->update($validated);

        // Mettre à jour ou créer la notification de rappel si activé
        if ($rendezVous->rappel_active) {
            // Charger le client pour éviter l'erreur de relation
            $rendezVous->load('client');

            // Calculer l'heure du rappel
            $rappelMinutes = $rendezVous->rappel_minutes ?? 30;
            $rdvDateTime = Carbon::parse($rendezVous->date_rendez_vous)->setTimeFromTimeString($rendezVous->heure_debut);
            $rappelTime = $rdvDateTime->copy()->subMinutes($rappelMinutes);

            // Vérifier si une notification existe déjà
            $notificationExistante = Notification::where('rendez_vous_id', $rendezVous->id)
                ->where('type', 'rappel_rendez_vous')
                ->first();

            if ($notificationExistante) {
                // Mettre à jour la notification existante
                $notificationExistante->update([
                    'titre' => 'Rappel: ' . $rendezVous->objet,
                    'message' => 'Rendez-vous avec ' . ($rendezVous->client ? $rendezVous->client->nom : 'un client') . ' prévu le ' . $rendezVous->date_rendez_vous . ' à ' . $rendezVous->heure_debut,
                    'date_envoi' => $rappelTime,
                    'envoye' => false,
                ]);
            } else {
                // Créer une nouvelle notification
                try {
                    Notification::create([
                        'user_id' => $rendezVous->chef_id,
                        'rendez_vous_id' => $rendezVous->id,
                        'titre' => 'Rappel: ' . $rendezVous->objet,
                        'message' => 'Rendez-vous avec ' . ($rendezVous->client ? $rendezVous->client->nom : 'un client') . ' prévu le ' . $rendezVous->date_rendez_vous . ' à ' . $rendezVous->heure_debut,
                        'type' => 'rappel_rendez_vous',
                        'lue' => false,
                        'envoye' => false,
                        'date_envoi' => $rappelTime,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de la création de la notification: ' . $e->getMessage());
                }
            }
        } else {
            // Si le rappel est désactivé, supprimer la notification existante
            Notification::where('rendez_vous_id', $rendezVous->id)
                ->where('type', 'rappel_rendez_vous')
                ->delete();
        }

        return response()->json([
            'message' => 'Rendez-vous modifié avec succès',
            'data' => $rendezVous,
        ], 200);
    }

    public function suppressionRendezVousApi($id) {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 401);
        }

        $rendezVous = RendezVous::findOrFail($id);
        $rendezVous->delete();

        return response()->json([
            'message' => 'Rendez-vous supprimé avec succès',
        ], 200);
    }
}
