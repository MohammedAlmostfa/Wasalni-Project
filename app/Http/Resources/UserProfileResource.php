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
            'roles' => $this->roles->map(function ($role) {
                return [
                    'about_User' => $role->pivot->about_User,
                    'car_Type' => $role->pivot->car_Type,
                    'image_name' => $role->pivot->image_name,
                    'mime_type' => $role->pivot->mime_type,
                    'image_path' => $role->pivot->image_path,
                ];
            }),

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

            'is_favorite' => $this->is_favorite,
            'User_trips_count' => $this->User_trips_count,
            'avg_rating' => $this->avg_rating,
        ];
    }
}
