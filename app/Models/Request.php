<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Request
 *
 * This model represents a user's request to join as a private driver,
 * including status management and relationship with the user.
 *
 * @package App\Models
 */
class Request extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',      // Foreign key linking to the user
        'about_user',   // Description or additional info about the user
        'status',       // Status of the request (mapped using statusMap)
        'car_type',     // Car type provided in the request
    ];

    /**
     * Mapping of status codes to human-readable strings.
     *
     * @var array<int, string>
     */
    protected static array $statusMap = [
        0 => 'pending',
        1 => 'accepted',
        2 => 'rejected',
    ];

    /**
     * Accessor for the `status` attribute.
     * Converts integer value from DB into readable string.
     *
     * @param mixed $value
     * @return string
     */
    public function getStatusAttribute($value): string
    {
        return self::$statusMap[$value] ?? 'Unknown';
    }

    /**
     * Mutator for the `status` attribute.
     * Converts string input (like "accepted") to numeric value before saving.
     *
     * @param mixed $value
     * @return void
     */
    public function setStatusAttribute($value): void
    {
        $flippedMap = array_flip(self::$statusMap);
        Log::info("Setting status: {$value} => " . ($flippedMap[$value] ?? 'invalid'));

        // Default to 'pending' if input is invalid
        $this->attributes['status'] = $flippedMap[$value] ?? 0;
    }

    /**
     * Defines the inverse one-to-many relationship with the User model.
     * A request belongs to a single user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
