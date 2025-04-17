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

    protected $notify_title;
    protected $notify_from;
    protected $notify_to;
    protected $notify_type;

    /**
     * Constructor
     */
    public function __construct(array $notify_from, array $notify_to, array $notify_title, string $notify_type)
    {
        $this->notify_title = $notify_title;
        $this->notify_from = $notify_from;
        $this->notify_to = $notify_to;
        $this->notify_type = $notify_type;
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
           ->withData([
                'notify_title' => json_encode($this->notify_title, JSON_UNESCAPED_UNICODE),
                'notify_from' => json_encode($this->notify_from, JSON_UNESCAPED_UNICODE),
                'notify_to' => json_encode($this->notify_to, JSON_UNESCAPED_UNICODE),
                'notify_type' => (string)$this->notify_type,
            ]);

        try {
            $response = $messaging->sendMulticast($message, $tokens);
            Log::info('FCM sent to user: ' . $notifiable->id);
        } catch (Throwable $e) {
            Log::error('FCM error: ' . $e->getMessage());
        }
    }
}
