<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteUserResource extends JsonResource
{
    public function toArray($request)
    {

        $profile = optional($this->profile);
        $image = optional($this->image);


        return [
            "user_id" => $profile->user_id,
            'name' => "{$profile->first_name} {$profile->last_name}",
            'image' => $image && $image->image_path && $image->image_name && $image->mime_type
                ? "{$image->image_path}/{$image->image_name}.{$image->mime_type}"
                : null,
        ];
    }
}
