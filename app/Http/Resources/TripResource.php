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
            'name' => $this->first_name ." ". $this->last_name ??null,
            'driver_id'=> $this->driver_id,
            'from_city' => json_decode($this->from_city, true),
            'to_city' => json_decode($this->to_city, true),
            'created_at' =>$this->created_at->format('Y-m-d  h:i A'),
            'trip_start' => $this->trip_start->format('h:i A'),
            'seat_price' => $this->seat_price,
             'available_seats' => $this->available_seats,
             'is_saved'=>$this->is_saved,

        ];
    }
}
