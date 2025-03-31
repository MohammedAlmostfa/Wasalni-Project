<?php

namespace App\Providers;

use Illuminate\Notifications\ChannelManager;
use App\Channels\FcmChannel;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)->extend('fcm', function () {
            return new FcmChannel;
        });

    }
}
