<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "trip_id",
        "status",
        "seats_number",
        "user_id",
        'nots'
    ];

    /**
     * Get the status attribute as a human-readable string.
     *
     * @param int $value The status value stored in the database.
     * @return string The human-readable status.
     */
    public function getStatusAttribute($value)
    {
        $statuses = [
            0 => 'pending',
            1 => 'accepted',
            2 => 'rejected',
             3=>'cancel' ,
        ];

        return $statuses[$value];
    }

    /**
     * Set the status attribute from a human-readable string to a database value.
     *
     * @param string $value The human-readable status.
     * @return void
     */
    public function setStatusAttribute($value)
    {
        $statuses = [
            'pending' => 0,
            'accepted' => 1,
            'rejected' => 2,
            'cancel' => 3,
        ];

        $this->attributes['status'] = $statuses[$value];
    }

    /**
     * Define the relationship with the Trip model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Define the relationship with the Rating model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Define the relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Apply filtering conditions to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $model The query builder instance.
     * @param array $filteringData An associative array of filtering criteria (e.g., ['status' => 1, 'seats_number' => 2]).
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterby($model, $filteringData)
    {
        if (isset($filteringData['status'])) {
            $model->where('bookings.status', $filteringData['status']);
        }
        if (isset($filteringData['seats_number'])) {
            $model->where('seats_number', $filteringData['seats_number']);
        }

        return $model;
    }

}
