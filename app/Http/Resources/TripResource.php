<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {

        $user = optional($this->user);
        $profile = optional($user->profile);
        $imagePivot = optional($user->roles->first())->pivot;

        return [
            'trip_id' => $this->trip_id,
            'description' => $this->description,
            'status' => $this->status,
            'name' => "{$profile->first_name} {$profile->last_name}",
            'driver_id' => $this->user_id,
            'from_city' => optional($this->cityFrom)->city_name,
            'to_city' => optional($this->cityTo)->city_name,
            'created_at' => optional($this->created_at)->format('Y-m-d h:i A'),
            'trip_start' => optional($this->trip_start)->format('h:i A'),
            'seat_price' => $this->seat_price,
            'available_seats' => $this->available_seats,
            'is_saved' => $this->is_saved,
            'avg_driver_rating' => $user->avg_driver_rating,
            'number_of_rating' => $user->number_of_rating,

            'image' => $imagePivot && $imagePivot->image_path && $imagePivot->image_name && $imagePivot->mime_type
                ? "{$imagePivot->image_path}/{$imagePivot->image_name}.{$imagePivot->mime_type}"
                : null,
        ];
    }
}
