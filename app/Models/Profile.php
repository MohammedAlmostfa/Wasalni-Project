<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'gender',
        'birthday',
        'phone',
        'address',
        //  'country_id'
        "city_id"
    ];

    /**
     * Define an inverse one-to-one relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define a one-to-one relationship with the Country model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */


    //  public function country()
    // {
    //    return $this->hasOne(Country::class);
    //}


    public function city()
    {
        return $this->hasOne(City::class);
    }
}
