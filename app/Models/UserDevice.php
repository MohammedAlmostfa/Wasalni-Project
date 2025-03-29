<?php

// app/Models/UserDevice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = ['user_id'
    ,'uidd',
    'fcm_token'
];

    protected $casts = [
        'user_id' => 'integer',
        'fcm_token' => 'string',
        'uidd' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
