<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'booking_id' => $this->id,
            'trip_id'=>$this->trip_id,
            'trip_start' => date('Y-m-d h:i A', strtotime($this->trip_start)),
            'total_price' => $this->seat_price * $this->seats_number,
            'from_city' => json_decode($this->from_city, true),
            'to_city' => json_decode($this->to_city, true),
            'nots'=>$this->nots,
             'seats_number'=>$this->seats_number,
            'driver_name' => $this->first_name . ' ' . $this->last_name,
            'status'=>$this->status,
            'driver_id'=>$this->driver_id,
        ];
    }

}
