<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;
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
     * Define a relationship with the City model for the "from" city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cityFrom()
    {
        return $this->belongsTo(City::class, 'from');
    }

    /**
     * Define a relationship with the City model for the "to" city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cityTo()
    {
        return $this->belongsTo(City::class, 'to');
    }

    /**
     * Define a relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define a relationship with the Booking model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }


    /**
     * Define a scope to filter trips.
     *
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param array $filteringData
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterby($model, $filteringData)
    {
        if (isset($filteringData['trip_start'])) {
            $model->where('trip_start', '>', $filteringData['trip_start']);
        }
        if (isset($filteringData['from'])) {
            $model->where('from', $filteringData['from']);
        }
        if (isset($filteringData['to'])) {
            $model->where('to', $filteringData['to']);
        }
        if (isset($filteringData['status'])) {
            $model->where('status', $filteringData['status']);
        }
        if (isset($filteringData['seat_price'])) {
            $model->where('seat_price', '<=', $filteringData['seat_price']);
        }
        if (isset($filteringData['available_seats'])) {
            $model->where('available_seats', '>=', $filteringData['available_seats']);
        }

        return $model;
    }
}
