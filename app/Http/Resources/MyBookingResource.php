<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyBookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'booking_id' => $this->id,
            'trip_id' => $this->trip_id,
            'status' => $this->status,
            'seats_number' => $this->seats_number,
            'available_seats' => $this->trip->available_seats,
            'seat_price' => $this->trip->seat_price,
            'nots' => $this->nots,
            'name' => $this->trip->user->profile->first_name . " " . $this->trip->user->profile->last_name,
            'driver_id' => $this->trip->user->id,
            'trip_start' => $this->trip->trip_start->format('h:i A'),
            'avg_driver_rating' => $this->trip->user->avg_driver_rating ,
            "number_of_rating" => $this->trip->user->number_of_rating ,
            'from_city' => optional($this->trip->cityFrom)->city_name,
            'to_city' => optional($this->trip->cityTo)->city_name,

            'image'=> $this->user->roles->first()->pivot->image_path.'/'.$this->user->roles->first()->pivot->image_name.'.'.$this->user->roles->first()->pivot->mime_type ??null ,
        ];
    }
}
