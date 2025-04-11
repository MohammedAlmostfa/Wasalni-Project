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
            'user_id' => $this->profile->user_id ?? null,
            'user_name' => optional($this->profile)->first_name . ' ' . optional($this->profile)->last_name,
            'birthday' => optional($this->profile)->birthday,

            // Roles mapping
            'about_user' => optional($this->roles->first())->pivot->about_User,
            'car_type' => optional($this->roles->first())->pivot->car_Type,

'image' => optional($this->roles->first()->pivot)->image_path && optional($this->roles->first()->pivot)->image_name && optional($this->roles->first()->pivot)->mime_type
    ? $this->roles->first()->pivot->image_path . '/' . $this->roles->first()->pivot->image_name . '.' . $this->roles->first()->pivot->mime_type
    : null,
            'Joining_date' => $this->created_at->format('Y-m-d'),
     'is_favorite' => $this->is_favorite,
            'User_trips_count' => $this->User_trips_count,
            'avg_rating' => $this->avg_rating,
            // Trip properties mapping
            'tripproperies' => $this->tripproperies->map(function ($property) {
                return [
                    'attributes' => $property->attributes,
                ];
            }),

            // Ratings mapping
            'ratings' => $this->tripRatings->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'rate' => $rating->rate,
                    'review' => $rating->review,
                    'created_at' => $rating->created_at->format('Y-m-d H:i:s'),
                    'user_name' => optional($rating->user->profile)->first_name . ' ' . optional($rating->user->profile)->last_name,
                ];
            }),


        ];
    }
}
