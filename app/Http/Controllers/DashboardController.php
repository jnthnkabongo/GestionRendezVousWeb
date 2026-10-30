<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if (!Auth::check()) {
        //     return redirect()->route('index');
        // }

        $user = Auth::user();

        // Statistiques globales
        $totalRendezVous = RendezVous::count();
        $totalClients = Client::count();
        $liste_rendez_vous_interieur = RendezVous::where('type_rendez_vous', 'interieur')
            ->orderBy('id', 'desc')
            ->whereDate('date_rendez_vous', today())
            ->paginate(10, ['*'], 'page_interieur');
        $liste_rendez_vous_exterieur = RendezVous::where('type_rendez_vous', 'exterieur')
            ->orderBy('id', 'desc')
            ->whereDate('date_rendez_vous', today())
            ->paginate(10, ['*'], 'page_exterieur');
        $totalSecretaires = User::where('role_id', 2)->count();

        // Rendez-vous du jour
        $rendezVousAujourdhui = RendezVous::whereDate('date_rendez_vous', today())->count();
        $rendezVousAujourdHuiList = RendezVous::whereDate('date_rendez_vous', today())
            ->with(['client', 'chef'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Rendez-vous à venir
        $rendezVousAVenir = RendezVous::whereDate('date_rendez_vous', '>', today())
            ->where('statut', 'planifie')
            ->count();

        // Rendez-vous par statut
        $rendezVousPlanifies = RendezVous::where('statut', 'planifie')->count();
        $rendezVousConfirmes = RendezVous::where('statut', 'confirme')->count();
        $rendezVousTermines = RendezVous::where('statut', 'termine')->count();
        $rendezVousAnnules = RendezVous::where('statut', 'annule')->count();

        // Rendez-vous récents
        $rendezVousRecents = RendezVous::with(['client', 'chef'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Rappels de rendez-vous uniquement (non terminés ni annulés)
        $now = Carbon::now();
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

        Log::info('Dashboard Controller - Notifications count: ' . $notifications->count() . ', Unread: ' . $unreadCount . ', Now: ' . $now);

        return view('pages.responsable.index', compact(
            'totalRendezVous',
            'totalClients',
            'totalSecretaires',
            'rendezVousAujourdhui',
            'rendezVousAujourdHuiList',
            'rendezVousAVenir',
            'rendezVousPlanifies',
            'rendezVousConfirmes',
            'rendezVousTermines',
            'rendezVousAnnules',
            'rendezVousRecents',
            'liste_rendez_vous_exterieur',
            'liste_rendez_vous_interieur',
            'user',
            'notifications',
            'unreadCount'
        ));
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * API: Get notifications for header
     */
    public function getNotificationsApi()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => collect(), 'unreadCount' => 0]);
        }

        $now = Carbon::now();
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

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    /////////////////////////////////////////////////////////

    public function getDashboardApi(){
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 401);
        }
        
        $totalRendezVous = RendezVous::count();
        $totalClients = Client::count();
        // $totalSecretaires = User::where('role', 'secretaire')->count();
        
        $rendezVousAujourdhui = RendezVous::whereDate('date_rendez_vous', today())->count();
        $rendezVousAujourdHuiList = RendezVous::whereDate('date_rendez_vous', today())->get();
        
        $rendezVousAVenir = RendezVous::where('date_rendez_vous', '>', today())->count();
        
        $rendezVousPlanifies = RendezVous::where('statut', 'planifie')->count();
        $rendezVousConfirmes = RendezVous::where('statut', 'confirme')->count();
        $rendezVousTermines = RendezVous::where('statut', 'termine')->count();
        $rendezVousAnnules = RendezVous::where('statut', 'annule')->count();
        
        $rendezVousRecents = RendezVous::with(['client', 'chef'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'total_rendez_vous' => $totalRendezVous,
            'total_clients' => $totalClients,
            // 'total_secretaire' => $totalSecretaires,
            'rendez_vous_aujourdhui' => $rendezVousAujourdhui,
            'rendez_vous_aujourdhui_list' => $rendezVousAujourdHuiList,
            'rendez_vous_a_venir' => $rendezVousAVenir,
            'rendez_vous_planifies' => $rendezVousPlanifies,
            'rendez_vous_confirmes' => $rendezVousConfirmes,
            'rendez_vous_termines' => $rendezVousTermines,
            'rendez_vous_annules' => $rendezVousAnnules,
            'rendez_vous_recents' => $rendezVousRecents,
            'user' => $user,
        ], 200);
    }
}
