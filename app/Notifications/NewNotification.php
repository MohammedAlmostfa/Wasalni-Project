<?php

namespace App\Notifications;

use Kreait\Firebase\Factory;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\RegistrationToken;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class NewNotification extends Notification
{
    // Properties to hold the notification title and body
    protected $title;
    protected $body;

    /**
     * Constructor to initialize the notification with a title and body.
     *
     * @param string $title The title of the notification.
     * @param string $body The body content of the notification.
     */
    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Define the channels through which the notification will be sent.
     *
     * In this case, we're sending the notification via Firebase.
     *
     * @param mixed $notifiable The entity that is receiving the notification (e.g., a User).
     * @return array The list of channels to use for sending the notification.
     */
    public function via($notifiable)
    {
        return ['firebase'];  // Define the 'firebase' channel for notification delivery.
    }

    /**
     * Send the notification through Firebase Cloud Messaging (FCM).
     *
     * This method sends the notification to all devices associated with the user,
     * using Firebase Cloud Messaging.
     *
     * @param mixed $notifiable The entity that is receiving the notification (e.g., a User).
     * @return void
     */
    public function toFirebase($notifiable)
    {
        // Check if the user has any associated devices
        $devices = $notifiable->devices;

        // If no devices are found, log an error and stop
        if ($devices->isEmpty()) {
            Log::error('No devices found for user: ' . $notifiable->id);
            return;
        }

        try {
            // Initialize Firebase Messaging
            $firebase = (new Factory)
                ->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS'))) // Load Firebase credentials from the specified path
                ->createMessaging(); // Create a messaging instance

            // Initialize an empty array to hold valid FCM tokens
            $tokens = [];

            // Loop through each device to collect valid FCM tokens
            foreach ($devices as $device) {
                // Check if the device has an FCM token; if not, log a warning and skip it
                if (empty($device->fcm_token)) {
                    Log::warning('FCM token is missing for device of user: ' . $notifiable->id);
                    continue;
                }

                // Add the valid FCM token to the tokens array
                $tokens[] = RegistrationToken::fromValue($device->fcm_token);
            }

            // If we have valid FCM tokens, send the notification
            if (!empty($tokens)) {
                // Create the message to be sent via Firebase
                $message = CloudMessage::new()
                    ->withNotification([
                        'title' => $this->title,  // The title of the notification
                        'body' => $this->body,    // The body of the notification
                    ]);

                // Send the message to all devices with valid tokens using multicast
                $firebase->sendMulticast($message, $tokens);

                // Log the successful delivery of the notification
                Log::info('Firebase notification sent successfully to devices of user: ' . $notifiable->id);
            } else {
                // If no valid FCM tokens are found, log a warning
                Log::warning('No valid FCM tokens found for user: ' . $notifiable->id);
            }

        } catch (\Exception $e) {
            // In case of an exception (e.g., network issue or Firebase failure), log the error
            Log::error('Error sending Firebase notification to user ' . $notifiable->id . ': ' . $e->getMessage());
        }
    }
}
