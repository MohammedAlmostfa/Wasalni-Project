<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray($request)
    {
        $image = optional($this->image);

        return [
            'id' => $this->id,
            'email' => $this->email,
            'user_id' => $this->profile->user_id ?? null,
            'user_name' => optional($this->profile)->first_name . ' ' . optional($this->profile)->last_name,
            'birthday' => optional($this->profile)->birthday,

            // Roles mapping
            'about_user' => optional($this->roles->first())->pivot->about_User,
            'car_type' => optional($this->roles->first())->pivot->car_Type,

            // Image path
            'image' => $image && $image->image_path && $image->image_name && $image->mime_type
                ? "{$image->image_path}/{$image->image_name}.{$image->mime_type}"
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
