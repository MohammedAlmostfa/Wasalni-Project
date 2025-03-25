<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'trip_start',
        'from',
        'to',
        'status',
        'seat_price',
        'available_seats',
        'user_id',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trip_start' => 'datetime',
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
            0 => 'Pending',
            1 => 'on_the_way',
            2 => 'Complete',
            3 => 'Ending',
            4 => 'cancel',
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
            'Pending' => 0,
            'on_the_way' => 1,
            'Complete' => 2,
            'Ending' => 3,
            'cancel' => 4,
        ];

        $this->attributes['status'] = $statuses[$value];
    }

    /**
     * Define a relationship with the City model for the "from" city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cityFrom(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from');
    }

    /**
     * Define a relationship with the City model for the "to" city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cityTo(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to');
    }

    /**
     * Define a relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define a relationship with the Booking model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }


    /**
     * Define a relationship with the User model for users who saved the trip.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trip_user', 'trip_id', 'user_id');
    }
    /**
     * Define a scope to filter trips based on provided criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $model The query builder instance.
     * @param array $filteringData An associative array of filtering criteria.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBy($model, $filteringData)
    {
        $filteringData['status'] = $filteringData['status'] ?? "0";

        if (isset($filteringData['startDate'])) {
            $model->whereDate('trip_start', '>=', $filteringData['startDate']);
        }
        if (isset($filteringData['startTime'])) {
            $model->whereTime('trip_start', '>=', $filteringData['startTime']);
        }

        if (isset($filteringData['from'])) {
            $model->where('from', $filteringData['from']);
        }
        if (isset($filteringData['to'])) {
            $model->where('to', $filteringData['to']);
        }
        if (isset($filteringData['status'])) {
            $model->where('trips.status', $filteringData['status']);
        }
        if (isset($filteringData['seat_price'])) {
            $model->where('seat_price', '>=', $filteringData['seat_price']);
            $model->orderBy('seat_price', 'asc');
        }
        if (isset($filteringData['available_seats'])) {
            $model->where('available_seats', '>=', $filteringData['available_seats']);
        }
        return $model;
    }


}
