<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(
                config('firebase.credentials.file')
            );

        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification(
        string $deviceToken,
        string $title,
        string $body
    ) {
        $message = CloudMessage::new()
            ->withTarget('token', $deviceToken)
            ->withNotification(
                Notification::create($title, $body)
            );

        return $this->messaging->send($message);
    }
}