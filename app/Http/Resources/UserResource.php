<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
                   'created_at' =>$this->created_at->format('Y-m-d  h:i A'),
        'updated_at' =>$this->updated_at->format('Y-m-d  h:i A'),
            'profile' => [
                'first_name' => $this->profile->first_name,
                'last_name' => $this->profile->last_name,
                'gender' => $this->profile->gender,
                'birthday' => $this->profile->birthday,
                'phone' => $this->profile->phone,
                'address' => $this->profile->address,
                'country_id'=>$this->profile->country_id,
            ],
        ];
    }
}
