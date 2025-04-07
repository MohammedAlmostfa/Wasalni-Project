<?php

namespace App\Models;

use App\Models\Rating;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    // Including necessary traits for the User model
    use HasFactory, Notifiable, HasRoles, HasRelationships;

    // Guard name for JWT Authentication
    protected $guard_name = 'api';

    // Mass assignable attributes for the User model
    protected $fillable = [
        'email',
        'password',
    ];

    // Attributes hidden from array and JSON output (for security)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts attributes to specific types (e.g., casting email_verified_at to a datetime object)
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Ensuring password is hashed
    ];

    /**
     * Get the identifier for the JWT (used for authentication).
     *
     * @return mixed The identifier for the JWT (user's primary key).
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the custom claims for the JWT (empty in this case).
     *
     * @return array An empty array or any additional claims you need in the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Define a one-to-one relationship with the Profile model.
     *
     * This means each user has one profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Define a has-many-through relationship to access cities through the profile and country.
     *
     * This allows accessing cities that are related to a user's profile's country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function cities()
    {
        return $this->hasManyThrough(
            City::class,
            Profile::class,
            'user_id',    // Foreign key on profiles table
            'country_id', // Foreign key on cities table
            'id',         // Local key on users table
        );
    }

    /**
     * Define a has-many relationship with the Trip model.
     *
     * This establishes that a user can have many trips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Get the favorite people added by this user.
     *
     * This defines a one-to-many relationship where a user can favorite many people.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favoritePeople()
    {
        return $this->hasMany(FavoritePerson::class, 'user_id');
    }

    /**
     * Get the users who added this user as a favorite.
     *
     * This defines a one-to-many relationship where this user can be favorited by many others.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favoritedBy()
    {
        return $this->hasMany(FavoritePerson::class, 'favorite_user_id');
    }

    /**
     * Define a has-many relationship with the Booking model.
     *
     * This indicates that a user can have many bookings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Define a has-many relationship with the Rating model.
     *
     * This defines that a user can have many ratings associated with their trips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Define a many-to-many relationship with the Trip model.
     *
     * This allows accessing trips that are saved by the user through the trip_user pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function savedTrips()
    {
        return $this->belongsToMany(Trip::class, 'trip_user', 'user_id', 'trip_id');
    }

    /**
     * Define a has-many relationship with the UserDevice model.
     *
     * This indicates that a user can have many devices associated with them.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }
    public function tripproperies()
    {
        return $this->belongsToMany(TripPropertie::class, 'tip_properties_users', 'user_id', 'tipProperty_id');
    }


    /**
     * Get the ratings for trips through bookings (deep relationship).
     *
     * This establishes a complex many-to-many-to-many relationship between:
     * Users -> Trips -> Bookings -> Ratings
     *
     * This allows retrieving ratings for trips booked by this user.
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function tripRatings()
    {
        return $this->hasManyDeep(
            Rating::class,
            [Trip::class, Booking::class], // Intermediate models
            [
                'user_id',    // Foreign key on trips table (references users)
                'trip_id',    // Foreign key on bookings table (references trips)
                'booking_id'  // Foreign key on ratings table (references bookings)
            ],
            [
                'id',        // Local key on users table
                'id',        // Local key on trips table
                'id'         // Local key on bookings table
            ]
        );
    }
}
