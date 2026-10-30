<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $liste_roles = Role::withCount('users')->orderBy('created_at', 'desc')->paginate(10);
        return view('pages.responsable.roles', compact('liste_roles'));
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
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
        ]);

        $role = Role::create($validated);

        return redirect()->route('liste-roles')->with('success', 'Rôle créé avec succès');
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
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:500',
        ]);

        $role->update($validated);

        return redirect()->route('liste-roles')->with('success', 'Rôle modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        // Vérifier si le rôle a des utilisateurs associés
        if ($role->users()->count() > 0) {
            return redirect()->route('liste-roles')->with('error', 'Impossible de supprimer ce rôle car il est associé à des utilisateurs');
        }

        $role->delete();

        return redirect()->route('liste-roles')->with('success', 'Rôle supprimé avec succès');
    }
}
