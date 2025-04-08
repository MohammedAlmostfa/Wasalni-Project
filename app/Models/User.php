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
    protected $guard_name = 'api'; // Defines guard used for permissions and JWT auth

    // Attributes that are mass assignable
    protected $fillable = [
        'email',    // Email address of the user
        'password', // Password for authentication
    ];

    // Attributes hidden from array and JSON output
    protected $hidden = [
        'password',         // To prevent the password from being exposed
        'remember_token',   // Security token used for "remember me" functionality
    ];

    // Attribute type casting
    protected $casts = [
        'email_verified_at' => 'datetime', // Converts to a DateTime object
        'password' => 'hashed',           // Ensures the password is hashed before saving
    ];

    /**
     * Get the identifier for the JWT.
     *
     * This provides the unique identifier for the JWT token.
     *
     * @return mixed The user's primary key.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get custom claims for the JWT.
     *
     * Returns additional claims that should be included in the JWT token.
     *
     * @return array An empty array (custom claims can be added here).
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relationship: One-to-One with Profile.
     *
     * Each user has a single profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Relationship: Has-Many-Through with City.
     *
     * This allows users to access cities through their profile and country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function cities()
    {
        return $this->hasManyThrough(
            City::class,         // The final model (cities)
            Profile::class,      // Intermediate model (profiles)
            'user_id',           // Foreign key on profiles table
            'country_id',        // Foreign key on cities table
            'id',                // Local key on users table
        );
    }

    /**
     * Relationship: Has-Many with Trip.
     *
     * A user can create multiple trips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Relationship: Many-to-Many with User (Favorites).
     *
     * Represents the users this user has marked as "favorite."
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorite_people', 'user_id', 'favorite_user_id')
                    ->withTimestamps(); // Tracks when a favorite was added
    }

    /**
     * Relationship: Many-to-Many (Favorited By).
     *
     * Retrieves users who have added this user to their favorites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_people', 'favorite_user_id', 'user_id')
                    ->withTimestamps(); // Tracks when this user was favorited
    }

    /**
     * Relationship: Has-Many with Booking.
     *
     * A user can create multiple bookings for their trips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relationship: Has-Many with Rating.
     *
     * A user can have many ratings associated with their trips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Relationship: Many-to-Many with Trip (Saved Trips).
     *
     * Access trips saved by this user via the pivot table `trip_user`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function savedTrips()
    {
        return $this->belongsToMany(Trip::class, 'trip_user', 'user_id', 'trip_id');
    }

    /**
     * Relationship: Has-Many with UserDevice.
     *
     * A user can have multiple associated devices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * Relationship: Many-to-Many with TripPropertie.
     *
     * Links users to trip properties through the pivot table `trip_properties_users`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tripProperties()
    {
        return $this->belongsToMany(TripPropertie::class, 'trip_properties_users', 'user_id', 'tripProperty_id');
    }

    /**
     * Deep Relationship: Trip Ratings through Bookings.
     *
     * Defines a complex many-to-many-to-many relationship to access
     * trip ratings through trips and bookings.
     *
     * Users -> Trips -> Bookings -> Ratings
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function tripRatings()
    {
        return $this->hasManyDeep(
            Rating::class,           // Final model
            [Trip::class, Booking::class], // Intermediate models
            [
                'user_id',           // Foreign key on trips table
                'trip_id',           // Foreign key on bookings table
                'booking_id'         // Foreign key on ratings table
            ],
            [
                'id',                // Local key on users table
                'id',                // Local key on trips table
                'id'                 // Local key on bookings table
            ]
        );
    }
}
