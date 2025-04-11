<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteUserResource extends JsonResource
{
    public function toArray($request)
    {
        // ✅ التأكد من أن جميع البيانات موجودة قبل الوصول إليها
        $profile = optional($this->profile);
        $imagePivot = optional($this->roles->first())->pivot;

        return [
            "user_id" => $profile->user_id,
            'name' => "{$profile->first_name} {$profile->last_name}",


            'image' => $imagePivot && $imagePivot->image_path && $imagePivot->image_name && $imagePivot->mime_type
                ? "{$imagePivot->image_path}/{$imagePivot->image_name}.{$imagePivot->mime_type}"
                : null,
        ];
    }
}
