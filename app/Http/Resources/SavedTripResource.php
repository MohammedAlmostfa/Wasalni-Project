<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedTripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
             'trip_id' => $this->trip_id,
            'description' => $this->description,
            'status' => $this->status,
            'name' => $this->first_name ." ". $this->last_name ??null,
            'from_city' => $this->from_city,
            'to_city' => $this->to_city,
            'created_at' => date('Y-m-d H:i', strtotime($this->created_at)),
            'trip_start' => date('Y-m-d H:i', strtotime($this->trip_start)),
            'seat_price' => $this->seat_price,
            'available_seats' => $this->available_seats,
            'saved_at' => date('Y-m-d H:i', strtotime($this->saved_at)),
            ];
    }
}
