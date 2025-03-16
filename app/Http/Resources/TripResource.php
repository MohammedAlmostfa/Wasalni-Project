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
            'id' => $this->id,
            'description' => $this->description,
            'status' => $this->status,
            'name' => $this->first_name ." ". $this->last_name ??null,
            'from_city' => $this->from_city,
            'to_city' => $this->to_city,
            'created_at' => $this->created_at,
            'trip_start' => $this->created_at,
             'seat_price' => $this->created_at,
              'available_seats' => $this->created_at,
        ];
    }
}
