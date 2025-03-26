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
    protected $fillable = ['city_name'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'city_name' => 'array' // Automatic JSON to array conversion
    ];

    /**
     * Accessor to get the city name in current application locale
     *
     * Returns the city name in the current application language,
     * or null if not available for the current locale.
     *
     * @return string|null The localized city name or null
     */
    public function getNameAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $this->city_name[$locale] ?? null;
    }

    /**
     * Relationship: Trips departing from this city
     *
     * Defines a one-to-many relationship with Trip model where
     * this city is the departure point (from).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tripsFrom(): HasMany
    {
        return $this->hasMany(Trip::class, 'from');
    }

    /**
     * Relationship: Trips arriving to this city
     *
     * Defines a one-to-many relationship with Trip model where
     * this city is the destination point (to).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tripsTo(): HasMany
    {
        return $this->hasMany(Trip::class, 'to');
    }

    /**
     * Relationship: Profile associated with this city
     *
     * Defines a one-to-one relationship with Profile model where
     * this city is associated with a user profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the city name in a specific locale with fallback
     *
     * @param string $locale The desired locale (default: 'en')
     * @return string|null The city name in specified locale or fallback
     */
    public function getNameByLocale(string $locale = 'en'): ?string
    {
        return $this->city_name[$locale] ?? $this->city_name['en'] ?? null;
    }
}
