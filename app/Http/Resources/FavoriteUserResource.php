<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteUserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
                 'id' => $this->id,

                     'favorite_user_id' => $this->favoriteUser->id,
                         'name' => $this->favoriteUser->profile->first_name." ".  $this->favoriteUser->profile->last_name,

                      'add on' => $this->created_at->format('Y-m-d H:i:s'),
             ];
    }
}
