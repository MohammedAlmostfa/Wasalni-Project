<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Rating;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    // Using necessary traits for the User model like HasFactory, Notifiable, HasRoles
    use HasFactory, Notifiable, HasRoles, HasRelationships;

    // Guard name for JWT Authentication
    protected $guard_name = 'api';

    // Mass assignable attributes for the User model
    protected $fillable = [
        'email',
        'password',
    ];

    // Attributes hidden from array and JSON output for security purposes
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Type casting attributes (e.g., casting email_verified_at to a DateTime object)
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Ensuring password is hashed
    ];

    /**
     * Get the JWT Identifier (used for authentication).
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Returning the primary key (ID)
    }

    /**
     * Get the custom claims for the JWT (empty in this case).
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Define a One-to-One relationship with the Profile model.
     *
     * This means each user has one profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    /**
     * Define a Has-Many relationship with the Trip model.
     *
     * This indicates that a user can have many trips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Many-to-Many relationship for the users who favor this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorite_people', 'user_id', 'favorite_user_id')
            ->withTimestamps();
    }

    /**
     * Many-to-Many relationship for users who have favorited this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_people', 'favorite_user_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Define a Has-Many relationship with the Booking model.
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
     * Many-to-Many relationship with the Trip model via the pivot table (trip_user).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function savedTrips()
    {
        return $this->belongsToMany(Trip::class, 'trip_user', 'user_id', 'trip_id');
    }

    /**
     * Define a Has-Many relationship with the UserDevice model.
     *
     * This indicates that a user can have many devices associated with them.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * Many-to-Many relationship with the TripPropertie model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tripproperies()
    {
        return $this->belongsToMany(TripPropertie::class, 'trip_properties_users', 'user_id', 'tripProperty_id');
    }

    /**
     * Define a MorphOne relationship with the Image model for the user's profile image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('tage', 'profile');
    }

    /**
     * Define a MorphMany relationship with the Image model for the user's car images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function carImage()
    {
        return $this->morphMany(Image::class, 'imageable')->where('tage', 'car');
    }

    /**
     * Get all ratings **given** by this user.
     *
     * This represents the ratings that the user has submitted for others.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    /**
     * Get all ratings **received** by this user.
     *
     * These are the ratings others have given to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'rated_user_id');
    }

    /**
     * Calculate and return the **average rating** received by the user.
     *
     * If the user has no ratings, the default is 0.
     *
     * @return float
     */
    public function averageRatings()
    {
        return round($this->receivedRatings()->avg('rate') ?? 0, 2);
    }

    /**
     * Count the total **number of ratings** received by the user.
     *
     * This helps track how many people have rated the user.
     *
     * @return int
     */
    public function countRatings()
    {
        return $this->receivedRatings()->count();
    }

}
