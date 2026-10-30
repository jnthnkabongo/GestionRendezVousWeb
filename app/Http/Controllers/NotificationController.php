<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function send(
        Request $request,
        FirebaseService $firebaseService
    ) {
        $firebaseService->sendNotification(
            $request->device_token,
            'Nouvelle notification',
            'Vous avez reçu une nouvelle notification.'
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification envoyée avec succès.'
        ]);
    }

    public function sendOneSignal(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_external_user_ids' => [(string)$request->user_id],
            'headings' => [
                'en' => $request->title,
            ],
            'contents' => [
                'en' => $request->message,
            ],
        ]);

        return response()->json([
            'success' => $response->successful(),
            'message' => $response->successful() ? 'Notification OneSignal envoyée avec succès.' : 'Erreur lors de l\'envoi de la notification.',
            'data' => [
                'user_id' => $request->user_id,
            ],
        ]);
    }

    public function sendOneSignalToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'included_segments' => ['All'],
            'headings' => [
                'en' => $request->title,
            ],
            'contents' => [
                'en' => $request->message,
            ],
        ]);

        return response()->json([
            'success' => $response->successful(),
            'message' => $response->successful() ? 'Notification OneSignal envoyée à tous les utilisateurs.' : 'Erreur lors de l\'envoi de la notification.',
            'data' => $response->json(),
        ]);
    }

    public function sendByOnesignalId(Request $request)
    {
        $request->validate([
            'onesignal_id' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_aliases' => [
                'onesignal_id' => [$request->onesignal_id],
            ],
            'target_channel' => 'push',
            'headings' => [
                'en' => $request->title,
            ],
            'contents' => [
                'en' => $request->message,
            ],
        ]);

        return response()->json([
            'success' => $response->successful(),
            'message' => $response->successful() ? 'Notification OneSignal envoyée avec succès.' : 'Erreur lors de l\'envoi de la notification.',
            'data' => $response->json(),
        ]);
    }
} 