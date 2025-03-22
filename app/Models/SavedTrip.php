<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedTrip extends Model
{
    use HasFactory;


    protected $fillable=['trip_id',
    'user_id'
];



}
