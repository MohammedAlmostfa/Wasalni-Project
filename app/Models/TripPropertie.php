<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPropertie extends Model
{
    use HasFactory;

    protected $fillable=[
        'attributes'
    ];
    protected $casts = [
      'attributes' => 'array' // Automatic JSON to array conversion
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'trip_properties_users', 'tripProperty_id', 'user_id');
    }


}
