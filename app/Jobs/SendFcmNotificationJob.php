<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\NewNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Bus\Dispatchable;

class SendFcmNotificationJob implements ShouldQueue
{
    // Using traits for managing jobs and serializing data
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Properties to store the user, title, and body of the notification
    protected $user;
    protected $title;
    protected $body;

    /**
     * Create a new job instance.
     *
     * @param $user The user who will receive the notification
     * @param string $title The title of the notification
     * @param string $body The content of the notification
     */
    public function __construct($user, string $title, string $body)
    {
        // Assign values to the class properties
        $this->user = $user;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Execute the job.
     *
     * This method sends a notification to the user using a custom notification class.
     *
     * @return void
     */
    public function handle()
    {
        // Send the notification to the user using FCM and the NewNotification class
        $this->user->notify(new NewNotification($this->title, $this->body));
    }
}
