<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FavoritePerson
 *
 * Represents a favorite person relationship between two users.
 * This model stores the relationship where a user (user_id) adds another user (favorite_user_id) as a favorite.
 */
class FavoritePerson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',          // The ID of the user who added the favorite
        'favorite_user_id', // The ID of the user who was added as a favorite
    ];

    protected $casts=[
        "user_id"=> "integer",
        "favorite_user_id"=> "integer",
    ];
    /**
     * Get the user who added the favorite person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the favorite user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function favoriteUser()
    {
        return $this->belongsTo(User::class, 'favorite_user_id');
    }
}
