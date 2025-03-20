<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        "trip_id",
        "status",
        "seats_number",
        "user_id",
        'nots'
    ];
    public function getStatusAttribute($value)
    {
        $statuses = [


            0=>'pending',
            1=>'accepted',
            2=>'rejected' ,

        ];

        return $statuses[$value];
    }
    public function setStatusAttribute($value)
    {

        $statuses = [
            'pending' => 0,
            'accepted'=>1,
            'rejected' => 2,
        ];

        $this->attributes['status'] = $statuses[$value];
    }
    /**
     * Define the relationship with the Trip model.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function scopeFilterby($model, $filteringData)
    {
        if (isset($filteringData['status'])) {
            $model->where('status', $filteringData['status']);
        }
        if (isset($filteringData['seats_number'])) {
            $model->where('seats_number', $filteringData['seats_number']);
        }


        return $model;

    }
    /**
     * Define the relationship with the Rating model.
     */
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Define the relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
