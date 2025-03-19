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
    public function toArray(Request $request)
    {
        return [

             'id' => $this->id,
             'user_id' => $this->user_id,
             'status' => $this->status,
             'seats_number' => $this->seats_number,
             'id' => $this->user->id,
             'created_at' => $this->created_at->format('Y-m-d H:i'),
             'nots'=>$this->nots

            ];

    }
}
