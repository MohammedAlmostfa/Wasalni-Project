<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;
    protected $fillable = [
       'user_id',      // Foreign key for the associated user
       'about_user',   // A description or additional details about the user
       'status',       // The status of the request (e.g., pending, approved, etc.)
       'car_type',     // Type of car associated with the request (e.g., SUV, sedan, etc.)
    ];

    /**
     * Status mapping for human-readable conversion
     */
    const STATUS_MAP = [
   0 => 'pending',
            1 => 'accepted',
            2 => 'rejected',
    ];

    /**
     * Get human-readable status from stored integer value
     *
     * @param int $value The stored status value
     * @return string Human-readable status
     */
    public function getStatusAttribute($value): string
    {
        return self::STATUS_MAP[$value] ?? 'Unknown';
    }

    /**
     * Set status by converting human-readable string to stored integer value
     *
     * @param string $value Human-readable status
     */
    public function setStatusAttribute($value): void
    {
        $flippedMap = array_flip(self::STATUS_MAP);
        $this->attributes['status'] = $flippedMap[$value] ?? 0;
    }

    /**
     * Define the relationship between PrivateUser and User.
     * A PrivateUser belongs to one User (one-to-many relationship).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
