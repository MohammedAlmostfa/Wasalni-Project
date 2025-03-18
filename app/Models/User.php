<?php
namespace App\Models;

use App\Models\Rating;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;
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
            'country_id'  // Local key on profiles table
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
}
