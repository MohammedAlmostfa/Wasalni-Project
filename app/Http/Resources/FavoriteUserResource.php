<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteUserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "user_id" => $this->profile->user_id,
            'name' => $this->profile->first_name . " " .  $this->profile->last_name,
             'image' => $this->roles->first()->pivot->image_path . '/' . $this->roles->first()->pivot->image_name . '.' . $this->roles->first()->pivot->mime_type ?? null,
        ];
    }
}
