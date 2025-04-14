<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

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
       'description' => 'string',
       'from' => 'integer',
       'to' => 'integer',
       'status' => 'integer',
       'seat_price' => 'integer',
       'available_seats' => 'integer',
       'user_id' => 'integer',
];


    /**
     * Status mapping for human-readable conversion
     */
    const STATUS_MAP = [
        0 => 'Pending',
        1 => 'On The Way',
        2 => 'Complete',
        3 => 'Ending',
        4 => 'Cancelled',
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
     * Relationship: City where the trip starts
     *
     * @return BelongsTo
     */
    public function cityFrom(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from');
    }

    /**
     * Relationship: City where the trip ends
     *
     * @return BelongsTo
     */
    public function cityTo(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to');
    }

    /**
     * Relationship: User who created the trip
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: All bookings for this trip
     *
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relationship: Users who saved this trip
     *
     * @return BelongsToMany
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trip_user', 'trip_id', 'user_id');
    }


    /**
     * Scope to filter trips based on various criteria
     *
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param array $filteringData {
     *     @type string $startDate   Filter trips starting after this date (Y-m-d)
     *     @type string $startTime   Filter trips starting after this time (H:i:s)
     *     @type int    $from        Filter by departure city ID
     *     @type int    $to          Filter by destination city ID
     *     @type int    $status      Filter by status (0-4)
     *     @type float  $seat_price  Filter by maximum seat price
     *     @type int    $available_seats Filter by minimum available seats
     * }
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBy($model, array $filteringData)
    {
        // Set default values
        $filteringData['status'] = $filteringData['status'] ?? "0";
        $filteringData['from'] = $filteringData['from'] ?? Auth::user()->city_id;

        // Apply filters based on provided criteria
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
            $model->where('seat_price', '<=', $filteringData['seat_price']);

        }

        if (isset($filteringData['available_seats'])) {
            $model->where('available_seats', '=', $filteringData['available_seats']);

        }

        return $model;
    }
}
