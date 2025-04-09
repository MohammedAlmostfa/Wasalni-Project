<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupedBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // Use the collection directly
        return $this->groupBy(function ($item) {
            return $item->trip->trip_start->format('Y-m-d');  // Group by trip start date
        })->map(function ($group, $date) {
            return [
                'date' => $date,
                'trips' => MyBookingResource::collection($group),
            ];
        })->values();  // Return the grouped values as an array
    }
}
