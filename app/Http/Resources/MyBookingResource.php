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
            'nots' => $this->nots,
            'driver_name' => $this->trip->user->profile->first_name . " " . $this->trip->user->profile->last_name,
      'trip_start' => $this->trip->trip_start->format('h:i A'),
               'from_city' => optional($this->city_from)->city_name,
            'to_city' => optional($this->city_to)->city_name,
            'image_name' => $this->user->roles->first()->pivot->image_name ?? null,
            'mime_type' => $this->user->roles->first()->pivot->mime_type ?? null,
            'image_path' => $this->user->roles->first()->pivot->image_path ?? null,
        ];
    }
}
