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
        return [
            'trip_id' => $this->trip_id,
            'description' => $this->description,
            'status' => $this->status,
            'name' => optional($this->user->profile)->first_name . " " . optional($this->user->profile)->last_name,
            'driver_id' => $this->user_id,
            'from_city' => optional($this->cityFrom)->city_name,
            'to_city' => optional($this->cityTo)->city_name,
            'created_at' => $this->created_at->format('Y-m-d h:i A'),
            'trip_start' => $this->trip_start->format('h:i A'),
            'seat_price' => $this->seat_price,
            'available_seats' => $this->available_seats,
            'is_saved' => $this->is_saved,
            'avg_driver_rating' => $this->user->avg_driver_rating ,
               'image_name' => $this->user->roles->first()->pivot->image_name,
                'mime_type' => $this->user->roles->first()->pivot->mime_type,
                'image_path' => $this->user->roles->first()->pivot->image_path,
        ];
    }

}
