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
    protected $title;
    protected $cityfrom;
    protected $cityto;
    protected $type;

    /**
     * Create a new job instance.
     */
    public function __construct($user, array $cityfrom, array $cityto, array $title, string $type)
    {
        $this->user = $user;
        $this->title = $title;
        $this->cityfrom = $cityfrom;
        $this->cityto = $cityto;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

        $this->user->notify(new NewNotification(
            $this->title,
            $this->cityfrom,
            $this->cityto,
            $this->type
        ));




    }

}
