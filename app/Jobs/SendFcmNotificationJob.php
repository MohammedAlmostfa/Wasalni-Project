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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $user;
    protected $title;
    protected $body;

    /**
     * إنشاء المهمة الجديدة.
     *
     * @param $user
     * @param string $title
     * @param string $body
     */
    public function __construct($user, string $title, string $body)
    {
        $this->user = $user;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * تنفيذ المهمة.
     *
     * @return void
     */
    public function handle()
    {

        $this->user->notify(new NewNotification($this->title, $this->body));
    }
}
