<?php

namespace App\Http\Controllers;

use App\Http\Requests\Credentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard-responsable');
        }
        // User::create([
        //     'name' => 'Jonathan kabongo',
        //     'email' => 'jnthnkabongo@gmail.com',
        //     'role_id' => 1,
        //     'password' => Hash::make('1234567'),

        // ]);
        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard-responsable'))->with('success', 'Connexion réussie');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès');
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Traiter l'inscription
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'password' => Hash::make($validated['password']),
            'actif' => true,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard-responsable')->with('success', 'Compte créé avec succès');
    }

    public function loginApi(Credentials $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $credentials['email'] || ! $credentials['password']) {
            return response()->json([
                'success' => false,
                'message' => 'Email et mot de passe sont requis.',
            ], 422);
        }

        if (Auth::attempt($credentials)) {
            // Récupérer l'utilisateur connecté avec son rôle
            $user = Auth::user()->load('role');
            $token = $user->createToken('auth-token')->plainTextToken;
            
            //$request->session()->regenerate();

            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ], 401);
    }

    public function logoutApi(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    public function updateOnesignalId(Request $request)
    {
        $request->validate([
            'onesignal_id' => 'required|string',
        ]);

        $user = Auth::user();
        $user->onesignal_id = $request->onesignal_id;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'OneSignal ID mis à jour avec succès',
        ]);
    }
}
