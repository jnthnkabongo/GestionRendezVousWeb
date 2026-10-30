<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.responsable.notifications');
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

    /////////////////////////////////////////////////////

    public function listeNotifications() {
        $user = Auth::user();
       
        if (!$user) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 401);
        }
        
        // TODO: Implémenter la logique pour récupérer les notifications de l'utilisateur
        // Exemple: $notifications = Notification::where('user_id', $user->id)->get();
        $listeNotifications = Notification::with('user','rendezVous')->where('user_id', $user->id)->get();
        
        return response()->json([
            'message' => 'Liste des notifications', 
            'notifications' => $listeNotifications
        ], 200);
    }
}
