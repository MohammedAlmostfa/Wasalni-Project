<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'country_name'
    ];

    /**
     * Define a one-to-one relationship with the Profile model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Define an inverse one-to-one relationship with the Profile model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Define a one-to-many relationship with the City model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function cities()
    //  {
    //     return $this->hasMany(City::class);
    // }
}
