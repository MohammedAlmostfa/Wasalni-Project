<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\RegistrationToken;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $cityfrom;
    protected $cityto;
    protected $type;

    /**
     * Constructor
     */
    public function __construct(array $cityfrom, array $cityto, array $title, string $type)
    {
        $this->title = $title;
        $this->cityfrom = $cityfrom;
        $this->cityto = $cityto;
        $this->type = $type;
    }

    /**
     * Channels to send notification on.
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    /**
     * Send FCM Notification
     */
    public function toFcm($notifiable)
    {
        $devices = $notifiable->devices;

        if (!$devices) {
            Log::error('No devices found for the user: ' . $notifiable->id);
            return;
        }

        $tokens = [];
        foreach ($devices as $device) {
            if (empty($device->fcm_token)) {
                Log::warning('FCM token missing for user: ' . $notifiable->id);
                continue;
            }
            $tokens[] = RegistrationToken::fromValue($device->fcm_token);
        }

        if (empty($tokens)) {
            Log::warning('No valid FCM tokens found for user: ' . $notifiable->id);
            return;
        }

        $firebaseFactory = (new Factory)
            ->withServiceAccount(storage_path(config('services.fcm.credentialsPath')));
        $messaging = $firebaseFactory->createMessaging();

        $message = CloudMessage::new()
            ->withNotification([
                'title' => $this->title, // The notification title

            ])->withData([
                 'title' => $this->title,
                'from' => $this->cityfrom,
                'to' => $this->cityto,
                'type' => $this->type,
            ]);

        try {
            $response = $messaging->sendMulticast($message, $tokens);
            Log::info('FCM sent to user: ' . $notifiable->id);
        } catch (Throwable $e) {
            Log::error('FCM error: ' . $e->getMessage());
        }
    }
}
