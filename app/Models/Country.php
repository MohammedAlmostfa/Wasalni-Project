<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'country_name', // Name of the country
    ];

    /**
     * Define a one-to-one relationship with the Profile model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Define an inverse one-to-one relationship with the Profile model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Define a one-to-many relationship with the City model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function cities(): HasMany
    // {
    //     return $this->hasMany(City::class);
    // }
}
