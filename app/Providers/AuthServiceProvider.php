<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Trip;
use App\Models\Rating;
use App\Models\Booking;
use App\Policies\TripPolicy;
use App\Models\FavoritePerson;
use App\Models\Request;
use App\Policies\RatingPolicy;
use App\Policies\BookingPolicy;
use App\Policies\RequestPolicy;
use App\Policies\SavedTripPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Trip::class => TripPolicy::class,
        Booking::class=>BookingPolicy::class,
        Rating::class=>RatingPolicy::class,
        Request::class=>RequestPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('createRating', [RatingPolicy::class, 'createRating']);
        Gate::define('showbookingsbytrip', [BookingPolicy::class, 'showbookingsbytrip']);
        Gate::define('removeFromSavedTrip', [SavedTripPolicy::class, 'removeTripFromSaved']);
        Gate::define('cancel', [BookingPolicy::class, 'cancel']);

    }
}
