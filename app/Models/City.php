<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'city_name', // Name of the city
        // 'country_id', // Foreign key for the Country model (commented out)
    ];

    /**
     * Define a one-to-many relationship with the Trip model for trips starting from this city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tripsFrom(): HasMany
    {
        return $this->hasMany(Trip::class, 'from');
    }

    /**
     * Define a one-to-many relationship with the Trip model for trips ending in this city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tripsTo(): HasMany
    {
        return $this->hasMany(Trip::class, 'to');
    }

    /**
     * Define a one-to-one relationship with the Profile model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
}
