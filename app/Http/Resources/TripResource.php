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
            'from_city' => $this->from_city,
            'to_city' => $this->to_city,
            'created_at' =>$this->created_at->format('d/m/Y h:i A'),
            'trip_start' => $this->trip_start->format('d/m/Y h:00 A'),
            'seat_price' => $this->seat_price,
             'available_seats' => $this->available_seats,
             'is_saved'=>$this->is_saved,

        ];
    }
}
