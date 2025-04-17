<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\NewNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendFcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $notify_title;
    protected $notify_from;
    protected $notify_to;
    protected $notify_type;

    /**
     * Create a new job instance.
     */
    public function __construct($user, array $notify_from, array $notify_to, array $notify_title, string $notify_type)
    {
        $this->user = $user;
        $this->notify_title = $notify_title;
        $this->notify_from = $notify_from;
        $this->notify_to = $notify_to;
        $this->notify_type = $notify_type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

        $this->user->notify(new NewNotification(
            $this->notify_title,
            $this->notify_from,
            $this->notify_to,
            $this->notify_type
        ));




    }

}
