<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable for security protection.
     *
     * @var array<int, string> List of fields that can be mass assigned
     */
    protected $fillable = [
        "trip_id",       // ID of the associated trip
        "status",        // Booking status (0=pending, 1=accepted, 2=rejected, 3=cancel)
        "seats_number",  // Number of seats booked
        "user_id",       // ID of the user who made the booking
        'nots'           // Notes or additional information about the booking
    ];
    protected $casts = [
        "trip_id" => "integer",
        "status" => "integer",
        "seats_number" => "integer",
        "user_id" => "integer",
        "nots" => "string"
    ];

    /**
     * Convert numeric status to human-readable string.
     *
     * @param int $value Numeric status value from database
     * @return string Human-readable status text
     */
    public function getStatusAttribute($value): string
    {
        $statuses = [
            0 => 'pending',   // Booking is awaiting confirmation
            1 => 'accepted',  // Booking has been approved
            2 => 'rejected',  // Booking has been declined
            3 => 'cancel',    // Booking was cancelled
        ];

        return $statuses[$value] ?? 'unknown'; // Fallback for undefined statuses
    }

    /**
     * Convert human-readable status to database numeric value.
     *
     * @param string $value Human-readable status text
     * @return void
     */
    public function setStatusAttribute($value): void
    {
        $statuses = [
            'pending' => 0,   // Map 'pending' to 0
            'accepted' => 1,   // Map 'accepted' to 1
            'rejected' => 2,   // Map 'rejected' to 2
            'cancel' => 3,     // Map 'cancel' to 3
        ];

        // Set the numeric value based on the string input
        $this->attributes['status'] = $statuses[strtolower($value)] ?? 0;
    }


    /**
     * Relationship: Booking belongs to a Trip.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trip(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Relationship: Booking has one Rating.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rating(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Relationship: Booking belongs to a User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter bookings by various criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $model Query builder instance
     * @param array $filteringData Filter criteria:
     *   - status: Filter by booking status
     *   - seats_number: Filter by number of seats
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBy(
        \Illuminate\Database\Eloquent\Builder $model,
        array $filteringData
    ): \Illuminate\Database\Eloquent\Builder {
        // Filter by status if provided
        if (isset($filteringData['status'])) {
            $model->where('bookings.status', $filteringData['status']);
        }

        // Filter by seats number if provided
        if (isset($filteringData['seats_number'])) {
            $model->where('seats_number', $filteringData['seats_number']);
        }

        return $model;
    }
}
