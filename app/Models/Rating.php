<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'user_id',
        'rate',
        'review',
        "rated_user_id"
    ];

    protected $casts = [
        'user_id' => 'integer',
        'rated_user_id' => 'integer',
        'rate' => 'integer',
        'review' => 'string',
    ];

    /**
     * The user who **gave** the rating.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The user who **received** the rating.
     */
    public function ratedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }
}