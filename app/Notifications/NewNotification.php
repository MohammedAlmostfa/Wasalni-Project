<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\RegistrationToken;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * A notification class for sending notifications via Firebase Cloud Messaging (FCM).
 */
class NewNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $body;

    /**
     * Constructor for creating a new notification instance.
     *
     * @param string $title Notification title.
     * @param string $body Notification body.
     */
    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body  = $body;
    }

    /**
     * Specifies the delivery channels for the notification.
     *
     * @param mixed $notifiable The notifiable entity (e.g., user or model).
     * @return array
     */
    public function via($notifiable)
    {
        // Use the custom FCM channel
        return ['fcm'];
    }

    /**
     * Handles sending the notification through Firebase Cloud Messaging (FCM).
     *
     * @param mixed $notifiable The notifiable entity (e.g., user or model).
     * @return void
     */
    public function toFcm($notifiable)
    {
        // Retrieve the devices associated with the notifiable entity (e.g., user devices)
        $devices = $notifiable->devices;

        if (!$devices) {
            Log::error('No devices found for the user: ' . $notifiable->id);
            return;
        }

        // Collect valid FCM tokens from the devices
        $tokens = [];
        foreach ($devices as $device) {
            if (empty($device->fcm_token)) {
                Log::warning('FCM token is missing for the user: ' . $notifiable->id);
                continue;
            }
            $tokens[] = RegistrationToken::fromValue($device->fcm_token);
        }

        if (empty($tokens)) {
            Log::warning('No valid FCM tokens found for the user: ' . $notifiable->id);
            return;
        }

        // Initialize Firebase Messaging with credentials from the JSON configuration file
        $firebaseFactory = (new Factory)
            ->withServiceAccount(storage_path(config('services.fcm.credentialsPath')));

        $messaging = $firebaseFactory->createMessaging();

        // Construct the notification message
        $message = CloudMessage::new()
            ->withNotification([
                'title' => $this->title, // The notification title
                'body'  => $this->body,  // The notification body
            ]);

        try {
            // Send the notification to all valid FCM tokens
            $response = $messaging->sendMulticast($message, $tokens);
            Log::info('FCM notification sent successfully to user: ' . $notifiable->id);
        } catch (Throwable $e) {
            // Log errors during notification sending
            Log::error('Error sending FCM notification: ' . $e->getMessage());
        }
    }
}
