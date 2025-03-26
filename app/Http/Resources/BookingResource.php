<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'seats_number' => $this->seats_number,
            'id' => $this->user->id,
            'name' => $this->user->profile->first_name . ' ' . $this->user->profile->last_name,
            'nots'=>$this->nots,
           'created_at' =>$this->created_at->format('Y-m-d h:i A'),

        ];
    }
}
