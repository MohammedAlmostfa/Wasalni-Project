<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'user_id' => $this->profile->user_id,
            'user_name' => $this->profile->first_name.' '.$this->profile->last_name,
            'birthday' => $this->profile->birthday,

            'roles' => $this->roles->map(function ($role) {
                return [
                    'about_User' => $role->pivot->about_User,
                    'car_Type' => $role->pivot->car_Type,
                ];
            }),
            'ratings' => $this->tripRatings->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'rate' => $rating->rate,
                    'review' => $rating->review,
                    'created_at' => $rating->created_at,
                    'user_name' => $rating->user->profile->first_name.' '.$rating->user->profile->last_name
                ];
            })
        ];
    }
}
