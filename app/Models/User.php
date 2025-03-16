<?php

namespace App\Models;

use App\Models\Profile;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Define a one-to-one relationship with the Profile model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Define a has-many-through relationship to access cities through the user's profile and country.
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
            'country_id'  // Local key on profiles table
        );
    }
    // Other methods and properties...

    public function trips()
    {
        return $this->hasMany(Trip::class) ;
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

    public function booking()
    {

        return $this->hasOne(User::class);
    }
}
