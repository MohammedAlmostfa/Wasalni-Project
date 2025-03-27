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
    use HasFactory, Notifiable, HasRoles , HasRelationships;
    protected $guard_name = 'api';
    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Define a one-to-one relationship with the Profile model.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Define a has-many-through relationship to access cities through the profile and country.
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
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }


    /**
     * Get the favorite people added by this user.
     */
    public function favoritePeople()
    {
        return $this->hasMany(FavoritePerson::class, 'user_id');
    }

    /**
     * Get the users who added this user as a favorite.
     */
    public function favoritedBy()
    {
        return $this->hasMany(FavoritePerson::class, 'favorite_user_id');
    }

    /**
     * Define a has-many relationship with the Booking model.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    public function savedTrips()
    {
        return $this->belongsToMany(Trip::class, 'trip_user', 'user_id', 'trip_id');
    }

    /**
     * Get the ratings for trips through bookings (deep relationship).
     *
     * This establishes a many-to-many-to-many relationship between:
     * Users -> Trips -> Bookings -> Ratings
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
