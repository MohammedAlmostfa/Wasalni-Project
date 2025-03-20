<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'city_name',
        //'country_id',
    ];

    /**
     * Define an inverse one-to-many relationship with the Country model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    //  public function country()
    // {
    //     return $this->belongsTo(Country::class);
    // }
    public function tripsFrom()
    {
        return $this->hasMany(Trip::class, 'from');
    }

    public function tripsTo()
    {
        return $this->hasMany(Trip::class, 'to');
    }
}
