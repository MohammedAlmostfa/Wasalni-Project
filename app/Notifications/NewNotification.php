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
    protected $title;
    protected $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function via($notifiable)
    {
        return [ 'firebase'];
    }


    public function toFirebase($notifiable)
    {
        // تحقق من وجود الأجهزة للمستخدم
        $devices = $notifiable->devices;  // جلب جميع الأجهزة المرتبطة بالمستخدم

        if ($devices->isEmpty()) {
            Log::error('No devices found for user: ' . $notifiable->id);
            return;
        }

        try {
            // إعداد Firebase Messaging
            $firebase = (new Factory)
                ->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')))
                ->createMessaging();

            foreach ($devices as $device) {
                if (empty($device->fcm_token)) {
                    Log::warning('FCM token is missing for device of user: ' . $notifiable->id);
                    continue;
                }

                $message = CloudMessage::new()
                    ->withNotification([
                        'title' => $this->title,
                        'body' => $this->body,
                    ]);


                $firebase->send($message, RegistrationToken::fromValue($device->fcm_token));

                Log::info('Firebase notification sent successfully to device of user: ' . $notifiable->id);
            }

        } catch (\Exception $e) {
            Log::error('Error sending Firebase notification to user ' . $notifiable->id . ': ' . $e->getMessage());
        }
    }

}
