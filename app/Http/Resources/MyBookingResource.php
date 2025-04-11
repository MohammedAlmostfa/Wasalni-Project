<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyBookingResource extends JsonResource
{
    public function toArray($request)
    {

        $trip = optional($this->trip);
        $user = optional($trip->user);
        $profile = optional($user->profile);
        $imagePivot = optional($user->roles->first())->pivot;

        return [
            'booking_id' => $this->id,
            'trip_id' => $this->trip_id,
            'status' => $this->status,
            'seats_number' => $this->seats_number,
            'available_seats' => $trip->available_seats,
            'seat_price' => $trip->seat_price,
            'nots' => $this->nots,
            'name' => "{$profile->first_name} {$profile->last_name}",
            'driver_id' => $user->id,
            'trip_start' => optional($trip->trip_start)->format('h:i A'),
            'avg_driver_rating' => $user->avg_driver_rating,
            "number_of_rating" => $user->number_of_rating,
            'from_city' => optional($trip->cityFrom)->city_name,
            'to_city' => optional($trip->cityTo)->city_name,


            'image' => $imagePivot && $imagePivot->image_path && $imagePivot->image_name && $imagePivot->mime_type
                ? "{$imagePivot->image_path}/{$imagePivot->image_name}.{$imagePivot->mime_type}"
                : null,
        ];
    }
}
