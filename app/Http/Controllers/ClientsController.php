<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $liste_clients = Client::orderBy('id', 'desc')->with('rendezVous')->paginate(10);
        return view('pages.responsable.invites', compact('liste_clients'));
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
        ]);

        $client = Client::create($validated);

        return redirect()->route('liste-invites')->with('success', 'Client créé avec succès');
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
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
        ]);

        $client->update($validated);

        return redirect()->route('liste-invites')->with('success', 'Client modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);

        // Vérifier si le client a des rendez-vous associés
        if ($client->rendezVous()->count() > 0) {
            return redirect()->route('liste-invites')->with('error', 'Impossible de supprimer ce client car il a des rendez-vous associés');
        }

        $client->delete();

        return redirect()->route('liste-invites')->with('success', 'Client supprimé avec succès');
    }

    /**
     * API: Get all clients
     */
    public function apiIndex()
    {
        $clients = Client::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $clients,
        ]);
    }

    /**
     * API: Create a new client
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Client créé avec succès',
            'data' => $client,
        ], 201);
    }

    /**
     * API: Update a client
     */
    public function apiUpdate(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
        ]);

        $client->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Client modifié avec succès',
            'data' => $client,
        ]);
    }

    /**
     * API: Delete a client
     */
    public function apiDestroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client supprimé avec succès',
        ]);
    }
}
